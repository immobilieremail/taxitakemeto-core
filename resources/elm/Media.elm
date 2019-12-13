module Media exposing (..)

import Html exposing (..)
import Html.Attributes exposing (..)
import Html.Events exposing (..)
import Json.Decode as D exposing (Decoder, map4, map3, field, string, int, list)
import Bootstrap.Grid as Grid
import Bootstrap.Grid.Col as Col
import Bootstrap.Grid.Row as Row
import Bootstrap.Text as Text
import Bootstrap.Carousel.Slide as Slide
import SwissNumber as SN



-- TYPES


type MediaType
  = ImageType
  | AudioType
  | VideoType

type alias Media =
  { mediaType : MediaType
  , url : String
  }


type alias Audio =
  { language : String
  , path : String
  }


type alias MediaFacet =
  { jsonType : String
  , url : SN.SwissNumber
  , mediaType : MediaType
  , path : String
  }





mediaFromMediaFacet : MediaFacet -> Media
mediaFromMediaFacet mediaFacet =
  Media
    mediaFacet.mediaType
    mediaFacet.path


-- CONSTRUCTORS (used to fake Media)


imageType : MediaType
imageType =
  ImageType


audioType : MediaType
audioType =
  AudioType


videoType : MediaType
videoType =
  VideoType



-- VIEW


carouselSlide : Media -> Slide.Config msg
carouselSlide media =
  case media.mediaType of
  ImageType ->
    Slide.config [] (Slide.image [] media.url)

  VideoType ->
    Slide.config []
      (Slide.customContent
        (video
          [ controls True
          , style "width" "100%" ]
          [ source
            [ src media.url
            , type_ "video/mp4"
            ]
            []
          ]
        )
      )

  _ ->
    Slide.config [] (Slide.image [] "https://www.labaleine.fr/sites/baleine/files/image-not-found.jpg")


viewFirstMedia : List (Html.Attribute msg) -> List Media -> Html msg
viewFirstMedia attributes medias =
  div
    [ class "d-flex" ]
    [ case (List.head medias) of
      Just media ->
        case media.mediaType of
        ImageType ->
          img
            ([ src media.url ] ++ attributes)
            []

        VideoType ->
          video
            [ controls True ]
            [ source
              [ src media.url
              , type_ "video/mp4"
              ]
              []
            ]

        _ ->
          img
          [ src "https://www.labaleine.fr/sites/baleine/files/image-not-found.jpg" ]
          []

      Nothing ->
        img
          [ src "https://www.labaleine.fr/sites/baleine/files/image-not-found.jpg" ]
          []
    ]


viewAudioLanguage : Audio -> Html msg
viewAudioLanguage audio =
  Grid.row
    [ Row.middleXs ]
    [ Grid.col
      [ Col.sm6 ]
      [ h4
        [ class "text-center" ]
        [ text audio.language ]
      ]
    , Grid.col
      [ Col.sm6, Col.textAlign Text.alignXsCenter ]
      [ Html.audio
        [ controls True
        , class "audio-size"
        ]
        [ Html.source
          [ src audio.path
          , type_ "audio/mpeg"
          ]
          []
        ]
      ]
    ]



-- JSON API


mediaTypeDecoder : Decoder MediaType
mediaTypeDecoder =
  D.string
    |> D.andThen (\str ->
      case str of
        "image" ->
          D.succeed ImageType

        "video" ->
          D.succeed VideoType

        "audio" ->
          D.succeed AudioType

        somethingElse ->
          D.fail <| "Unknown mediaType: " ++ somethingElse
    )

mediaDecoder : Decoder Media
mediaDecoder =
  D.map2 Media
  (field "mediaType" mediaTypeDecoder)
  (field "url" string)


mediaFacetDecoder : Decoder MediaFacet
mediaFacetDecoder =
  D.map4 MediaFacet
  (field "type" string)
  (field "url" string)
  (field "media_type" mediaTypeDecoder)
  (field "path" string)
