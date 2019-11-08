module PI exposing (
  Tag, TypePI,
  viewTagPI, viewTypePI,
  tagDecoder, typePIDecoder,
  free, paying, reserved, notReserved, onGoing, restaurant, hotel, shop, touristicPlace)

import Html exposing (..)
import Html.Attributes exposing (..)
import Html.Events exposing (..)
import Json.Decode as D exposing (Decoder, map4, map3, field, string, int, list)
import Bootstrap.Grid as Grid
import Bootstrap.Grid.Col as Col
import Bootstrap.Button as Button



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



-- CONSTRUCTORS


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
        , Button.attrs [ style "width" "120px", style "height" "30px" ]
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
        , Button.attrs [ style "width" "120px", style "height" "30px" ]
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
        , Button.attrs [ style "width" "120px", style "height" "30px" ]
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
        , Button.attrs [ style "width" "120px", style "height" "30px" ]
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
        , Button.attrs [ style "width" "120px", style "height" "30px" ]
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
      [ Col.xs3, Col.attrs [ style "min-width" "50px", class "mt-2" ] ]
      [ img
        [ class "d-block rounded"
        , style "width" "30px"
        , src "https://cdn.pixabay.com/photo/2019/09/08/17/24/eat-4461470_960_720.png"
        ]
        []
      ]

  Shop ->
    Grid.col
      [ Col.xs3, Col.attrs [ style "min-width" "50px", class "mt-2" ] ]
      [ img
        [ class "d-block rounded"
        , style "width" "30px"
        , src "https://cdn.pixabay.com/photo/2015/12/23/01/14/edit-1105049_960_720.png"
        ]
        []
      ]

  Hotel ->
    Grid.col
      [ Col.xs3, Col.attrs [ style "min-width" "50px", class "mt-2" ] ]
      [ img
        [ class "d-block rounded"
        , style "width" "30px"
        , src "https://cdn.pixabay.com/photo/2015/12/28/02/58/home-1110868_960_720.png"
        ]
        []
      ]

  TouristicPlace ->
    Grid.col
      [ Col.xs3, Col.attrs [ style "min-width" "50px", class "mt-2" ] ]
      [ img
        [ class "d-block rounded"
        , style "width" "30px"
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
