module Shell exposing (..)

import Html exposing (..)
import Json.Decode as D exposing (Decoder, map4, map3, field, string, int, list)
import SwissNumber exposing (SwissNumber)
import Travel exposing (Travel)
import User exposing (User)



-- TYPES


type alias ShellFacet =
  { swissNumber : SwissNumber
  , facetType : String
  , user : Maybe SwissNumber
  , travelList : Maybe SwissNumber
  , contactList : Maybe SwissNumber
  }


type alias ShellDropbox =
  { swissNumber : SwissNumber
  }


type alias Shell =
  { swissNumber : SwissNumber
  , travelList : List Travel
  , contactList : List ShellDropbox
  , user : User
  }



shellFromShellFacet : ShellFacet -> Shell
shellFromShellFacet shellFacet =
  Shell shellFacet.swissNumber [] [] (User "John Doe" [] Nothing)


-- JSON Decoders


shellFacetDecoder : Decoder ShellFacet
shellFacetDecoder =
  D.map5 ShellFacet
  (field "url" string)
  (field "type" string)
  (D.maybe (field "data" (field "user" string)))
  (D.maybe (field "data" (field "travels" string)))
  (D.maybe (field "data" (field "contacts" string)))
