module Ocap exposing (Ocap, OcapListFacet, ocapDecoder, ocapListFacetDecoder)

import Json.Decode as D exposing (Decoder, map3, field, string, list)
import SwissNumber as SN



-- TYPES


type alias Ocap =
  { ocapType : String
  , url : SN.SwissNumber
  }


type alias OcapListFacet =
  { swissNumber : SN.SwissNumber
  , jsonType : String
  , contents : List Ocap
  }



-- JSON Decoders


ocapDecoder : Decoder Ocap
ocapDecoder =
  D.map2 Ocap
  (field "ocapType" string)
  (field "url" string)


ocapListFacetDecoder : Decoder OcapListFacet
ocapListFacetDecoder =
  D.map3 OcapListFacet
  (field "url" string)
  (field "type" string)
  (field "contents" (D.list ocapDecoder))
