module PI exposing (..)

import Html exposing (..)
import Html.Attributes exposing (..)
import Html.Events exposing (..)
import Json.Decode as D exposing (Decoder, map4, map3, field, string, int, list)
import Bootstrap.Grid as Grid
import Bootstrap.Grid.Col as Col
import Bootstrap.Button as Button
import Media exposing (..)
import SwissNumber as SN exposing (SwissNumber)



-- TYPES


type Tag
  = Free
  | Paying
  | Reserved
  | NotReserved
  | OnGoing


type TypePI
  = Restaurant
  | Hotel
  | Shop
  | TouristicPlace


type alias PI =
  { swissNumber : SN.SwissNumber
  , title : String
  , description : String
  , address : String
  , medias : List Media.Media
  , audios : List Media.Audio
  , tags : List Tag
  , typespi : List TypePI
  }


type alias PIFacet =
  { swissNumber : SN.SwissNumber
  , facetType : String
  , title : String
  , description : String
  , address : String
  , mediaList : Maybe SN.SwissNumber
  }



swissNumberIsNotEqual : SwissNumber -> PI -> Bool
swissNumberIsNotEqual swissNumber pi =
  pi.swissNumber /= swissNumber


piFromPIFacet : PIFacet -> PI
piFromPIFacet piFacet =
  PI
    piFacet.swissNumber
    piFacet.title
    piFacet.description
    piFacet.address
    [] [] [] []



-- CONSTRUCTORS (used to fake PI)


free : Tag
free =
  Free


paying : Tag
paying =
  Paying


reserved : Tag
reserved =
  Reserved


notReserved : Tag
notReserved =
  NotReserved


onGoing : Tag
onGoing =
  OnGoing


restaurant : TypePI
restaurant =
  Restaurant


hotel : TypePI
hotel =
  Hotel


shop : TypePI
shop =
  Shop


touristicPlace : TypePI
touristicPlace =
  TouristicPlace



-- VIEW


viewTagPI : Tag -> Grid.Column msg
viewTagPI tag =
  case tag of
  Free ->
    Grid.col
      [ Col.attrs [ class "py-1" ] ]
      [ Button.button
        [ Button.small
        , Button.attrs [ class "pi-tag" ]
        , Button.outlineSuccess
        , (Button.disabled True)
        ]
        [ text "Free" ]
      ]

  Paying ->
    Grid.col
      [ Col.attrs [ class "py-1" ] ]
      [ Button.button
        [ Button.small
        , Button.attrs [ class "pi-tag" ]
        , Button.outlineWarning
        , (Button.disabled True)
        ]
        [ text "Paying" ]
      ]

  Reserved ->
    Grid.col
      [ Col.attrs [ class "py-1" ] ]
      [ Button.button
        [ Button.small
        , Button.attrs [ class "pi-tag" ]
        , Button.outlineInfo
        , (Button.disabled True)
        ]
        [ text "Reserved" ]
      ]

  NotReserved ->
    Grid.col
      [ Col.attrs [ class "py-1" ] ]
      [ Button.button
        [ Button.small
        , Button.attrs [ class "pi-tag" ]
        , Button.outlineDanger
        , (Button.disabled True)
        ]
        [ text "Not Reserved" ]
      ]

  OnGoing ->
    Grid.col
      [ Col.attrs [ class "py-1" ] ]
      [ Button.button
        [ Button.small
        , Button.attrs [ class "pi-tag" ]
        , Button.outlinePrimary
        , (Button.disabled True)
        ]
        [ text "On Going" ]
      ]


viewTypePI : TypePI -> Grid.Column msg
viewTypePI typepi =
  case typepi of
  Restaurant ->
    Grid.col
      [ Col.xs3, Col.attrs [ class "pi-type" ] ]
      [ img
        [ class "pi-type-icon"
        , src "https://cdn.pixabay.com/photo/2019/09/08/17/24/eat-4461470_960_720.png"
        ]
        []
      ]

  Shop ->
    Grid.col
      [ Col.xs3, Col.attrs [ class "pi-type" ] ]
      [ img
        [ class "pi-type-icon"
        , src "https://cdn.pixabay.com/photo/2015/12/23/01/14/edit-1105049_960_720.png"
        ]
        []
      ]

  Hotel ->
    Grid.col
      [ Col.xs3, Col.attrs [ class "pi-type" ] ]
      [ img
        [ class "pi-type-icon"
        , src "https://cdn.pixabay.com/photo/2015/12/28/02/58/home-1110868_960_720.png"
        ]
        []
      ]

  TouristicPlace ->
    Grid.col
      [ Col.xs3, Col.attrs [ class "pi-type" ] ]
      [ img
        [ class "pi-type-icon"
        , src "https://cdn.pixabay.com/photo/2016/01/10/22/23/location-1132648_960_720.png"
        ]
        []
      ]



-- JSON Decoders


tagDecoder : Decoder Tag
tagDecoder =
  D.string
    |> D.andThen (\str ->
      case str of
        "free" ->
          D.succeed Free

        "paying" ->
          D.succeed Paying

        "on going" ->
          D.succeed OnGoing

        "not reserved" ->
          D.succeed NotReserved

        somethingElse ->
          D.fail <| "Unknown tag: " ++ somethingElse
    )


typePIDecoder : Decoder TypePI
typePIDecoder =
  D.string
    |> D.andThen (\str ->
      case str of
        "restaurant" ->
          D.succeed Restaurant

        "hotel" ->
          D.succeed Hotel

        "shop" ->
          D.succeed Shop

        "touristicPlace" ->
          D.succeed TouristicPlace

        somethingElse ->
          D.fail <| "Unknown tag: " ++ somethingElse
    )


piDecoder : Decoder PI
piDecoder =
  D.map8 PI
  (field "swiss_number" string)
  (field "title" string)
  (field "description" string)
  (field "address" string)
  (field "medias" (D.list mediaDecoder))
  (field "audios" (D.list decodeAudioContent))
  (field "tags" (D.list tagDecoder))
  (field "typespi" (D.list typePIDecoder))


piFacetDecoder : Decoder PIFacet
piFacetDecoder =
  D.map6 PIFacet
  (field "url" string)
  (field "type" string)
  (field "data" (field "title" string))
  (field "data" (field "description" string))
  (field "data" (field "address" string))
  (D.maybe (field "data" (field "medias" string)))

