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

updateAccordionState : Accordion.State -> Travel -> Travel
updateAccordionState state travel =
  { swissNumber = travel.swissNumber
  , title = travel.title
  , listPI = travel.listPI
  , accordionState = state
  }