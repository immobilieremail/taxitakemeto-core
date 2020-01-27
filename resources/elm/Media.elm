module Media exposing (..)

import Bootstrap.Carousel.Slide as Slide
import Bootstrap.Grid as Grid
import Bootstrap.Grid.Col as Col
import Bootstrap.Grid.Row as Row
import Bootstrap.Text as Text
import Html exposing (Html, div, h4, img, source, text, video)
import Html.Attributes exposing (class, controls, src, style, type_)
import Json.Decode as D exposing (Decoder, field, map4, string)
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
    Media mediaFacet.mediaType mediaFacet.path


audioFromMediaFacet : MediaFacet -> Audio
audioFromMediaFacet mediaFacet =
    Audio "Language" mediaFacet.path



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
                        , style "width" "100%"
                        ]
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
        [ case List.head medias of
            Just media ->
                case media.mediaType of
                    ImageType ->
                        img
                            (src media.url :: attributes)
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
        |> D.andThen
            (\str ->
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


mediaFacetDecoder : Decoder MediaFacet
mediaFacetDecoder =
    D.map4 MediaFacet
        (field "type" string)
        (field "url" string)
        (field "media_type" mediaTypeDecoder)
        (field "path" string)
