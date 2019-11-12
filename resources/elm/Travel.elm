module Travel exposing (..)

import SwissNumber exposing (SwissNumber)
import Date exposing (Date)
import PI exposing (PI)

type alias Travel =
  { swissNumber : SwissNumber
  , title : String
  , listPI : List PI
  , startDate : Date
  , endDate : Date
  }