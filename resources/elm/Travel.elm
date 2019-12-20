module Travel exposing (Travel, TravelFacet, travelFacetDecoder, updateAccordionState, updateListPI, swissNumberIsNotEqual, travelFromTravelFacet, viewList, view)

import Html exposing (Html, text, h5, div, a)
import Html.Attributes exposing (class, href)
import Json.Decode as D exposing (Decoder, map4, field, string)
import Bootstrap.Accordion as Accordion
import Bootstrap.Grid as Grid
import Bootstrap.Grid.Col as Col
import Bootstrap.Grid.Row as Row
import Bootstrap.Text as Text
import SwissNumber exposing (SwissNumber)
import PI exposing (PI)



-- TYPES


type alias TravelFacet =
  { swissNumber : SwissNumber
  , facetType : String
  , title : String
  , piList : Maybe SwissNumber
  }


type alias Travel =
  { swissNumber : SwissNumber
  , title : String
  , listPI : List PI
  , listContact : List SwissNumber
  , accordionState : Accordion.State
  }



swissNumberIsNotEqual : SwissNumber -> Travel -> Bool
swissNumberIsNotEqual swissNumber travel =
  travel.swissNumber /= swissNumber


updateListPI : SwissNumber -> List PI -> Travel -> Travel
updateListPI swissNumber list travel =
  if travel.swissNumber == swissNumber then
    { travel | listPI = travel.listPI ++ list }
  else
    travel


updateAccordionState : Accordion.State -> Travel -> Travel
updateAccordionState state travel =
  { swissNumber = travel.swissNumber
  , title = travel.title
  , listPI = travel.listPI
  , listContact = travel.listContact
  , accordionState = state
  }


travelFromTravelFacet : TravelFacet -> Travel
travelFromTravelFacet travelFacet =
  Travel
    travelFacet.swissNumber
    travelFacet.title
    [] [] Accordion.initialState



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
  Grid.container [] (List.map view listTravel)



-- JSON Decoders


travelFacetDecoder : Decoder TravelFacet
travelFacetDecoder =
  D.map4 TravelFacet
  (field "url" string)
  (field "type" string)
  (field "data" (field "title" string))
  (D.maybe (field "data" (field "pis" string)))
