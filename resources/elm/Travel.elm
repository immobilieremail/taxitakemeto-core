module Travel exposing (..)

import Html exposing (..)
import Html.Events exposing (..)
import Html.Attributes exposing (..)
import Bootstrap.Accordion as Accordion
import Bootstrap.Grid as Grid
import Bootstrap.Grid.Col as Col
import Bootstrap.Grid.Row as Row
import Bootstrap.Text as Text
import SwissNumber exposing (SwissNumber)
import PI exposing (PI)



-- TYPES


type alias Travel =
  { swissNumber : SwissNumber
  , title : String
  , listPI : List PI
  , accordionState : Accordion.State
  }



swissNumberIsNotEqual : SwissNumber -> Travel -> Bool
swissNumberIsNotEqual swissNumber travel =
  travel.swissNumber /= swissNumber


updateListPI : SwissNumber -> List PI -> Travel -> Travel
updateListPI swissNumber list travel =
  case travel.swissNumber == swissNumber of
    True ->
      { travel | listPI = travel.listPI ++ list }

    False ->
      travel


updateAccordionState : Accordion.State -> Travel -> Travel
updateAccordionState state travel =
  { swissNumber = travel.swissNumber
  , title = travel.title
  , listPI = travel.listPI
  , accordionState = state
  }



-- VIEWS


view : Travel -> Html msg
view travel =
  a
    [ href ("/elm/travel#" ++ travel.swissNumber) ]
    [ div
      [ class "travel-header" ]
      [ Grid.row
        [ Row.middleXs ]
        [ Grid.col
          [ Col.xs12, Col.textAlign Text.alignXsLeft ]
          [ h5
            []
            [ text travel.title ]
          ]
        ]
      ]
    ]

viewList : List Travel -> Html msg
viewList listTravel =
  Grid.container
    []
    (List.map view listTravel)
