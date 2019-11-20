module Travel exposing (..)

import Bootstrap.Accordion as Accordion
import SwissNumber exposing (SwissNumber)
import PI exposing (PI)

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