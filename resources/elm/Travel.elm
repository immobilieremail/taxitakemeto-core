module Travel exposing (..)

import SwissNumber exposing (SwissNumber)
import PI exposing (PI)

type alias Travel =
  { swissNumber : SwissNumber
  , title : String
  , listPI : List PI
  }