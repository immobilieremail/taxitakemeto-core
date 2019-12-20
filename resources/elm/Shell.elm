module Shell exposing (..)

import Json.Decode as D exposing (Decoder, field, string)
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
  , sender : Maybe SwissNumber
  }


type alias DropboxFacet =
  { name : String
  }

type alias ShellDropbox =
  { swissNumber : SwissNumber
  , name : String
  }


type alias Shell =
  { swissNumber : SwissNumber
  , travelList : List Travel
  , contactList : List ShellDropbox
  , user : User
  , sender : Maybe SwissNumber
  }



shellFromShellFacet : ShellFacet -> Shell
shellFromShellFacet shellFacet =
  Shell shellFacet.swissNumber [] [] (User "John Doe" [] Nothing) shellFacet.sender


-- JSON Decoders


shellFacetDecoder : Decoder ShellFacet
shellFacetDecoder =
  D.map6 ShellFacet
  (field "url" string)
  (field "type" string)
  (D.maybe (field "data" (field "user" string)))
  (D.maybe (field "data" (field "travels" string)))
  (D.maybe (field "data" (field "contacts" string)))
  (D.maybe (field "data" (field "sender" string)))


dropboxFacetDecoder : Decoder DropboxFacet
dropboxFacetDecoder =
  D.map DropboxFacet (field "name" string)