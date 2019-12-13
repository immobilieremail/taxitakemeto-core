module Request exposing (..)

import Http
import Task exposing (Task)
import Json.Decode as D exposing (Decoder)
import SwissNumber as SN exposing (SwissNumber)
import Travel exposing (..)
import Media exposing (..)
import Ocap exposing (..)
import PI exposing (..)

-- TYPES


getOcapListfromUrl : SwissNumber -> Task Http.Error OcapListFacet
getOcapListfromUrl ocapUrl =
  Http.task
    { method = "GET"
    , headers = []
    , url = ocapUrl
    , body = Http.emptyBody
    , resolver = Http.stringResolver <| handleJsonResponse <| Ocap.ocapListFacetDecoder
    , timeout = Nothing
    }


getMediafromUrl : SwissNumber -> Task Http.Error MediaFacet
getMediafromUrl ocapUrl =
  Http.task
    { method = "GET"
    , headers = []
    , url = ocapUrl
    , body = Http.emptyBody
    , resolver = Http.stringResolver <| handleJsonResponse <| Media.mediaFacetDecoder
    , timeout = Nothing
    }


getPIfromUrl : SwissNumber -> Task Http.Error PIFacet
getPIfromUrl ocapUrl =
  Http.task
    { method = "GET"
    , headers = []
    , url = ocapUrl
    , body = Http.emptyBody
    , resolver = Http.stringResolver <| handleJsonResponse <| PI.piFacetDecoder
    , timeout = Nothing
    }


getTravelfromUrl : SwissNumber -> Task Http.Error TravelFacet
getTravelfromUrl ocapUrl =
  Http.task
    { method = "GET"
    , headers = []
    , url = ocapUrl
    , body = Http.emptyBody
    , resolver = Http.stringResolver <| handleJsonResponse <| Travel.travelFacetDecoder
    , timeout = Nothing
    }


handleJsonResponse : Decoder a -> Http.Response String -> Result Http.Error a
handleJsonResponse decoder response =
  case response of
    Http.BadUrl_ url ->
      Err (Http.BadUrl url)

    Http.Timeout_ ->
      Err Http.Timeout

    Http.BadStatus_ { statusCode } _ ->
      Err (Http.BadStatus statusCode)

    Http.NetworkError_ ->
      Err Http.NetworkError

    Http.GoodStatus_ _ body ->
      case D.decodeString decoder body of
        Err _ ->
          Err (Http.BadBody body)

        Ok result ->
          Ok result


getSinglePIRequest : SwissNumber -> Task Http.Error PI
getSinglePIRequest ocapUrl =
  getPIfromUrl ocapUrl
    |> Task.andThen
      (\piFacet ->
        case piFacet.mediaList of
          Nothing ->
            Task.succeed (PI.piFromPIFacet piFacet)

          Just mediaListUrl ->
            getOcapListfromUrl mediaListUrl
              |> Task.andThen
                (\medialist ->
                  List.map (getMediafromUrl << .url) medialist.contents
                    |> Task.sequence
                    |> Task.andThen
                      (\mediaFacets ->
                        let
                          pi = PI.piFromPIFacet piFacet
                          medias = List.map Media.mediaFromMediaFacet mediaFacets
                        in
                          Task.succeed { pi | medias = medias }
                      )
                )
      )


getSingleTravelRequest : SwissNumber -> Task Http.Error Travel
getSingleTravelRequest ocapUrl =
  getTravelfromUrl ocapUrl
    |> Task.andThen
      (\travelFacet ->
        case travelFacet.piList of
          Nothing ->
            Task.succeed (Travel.travelFromTravelFacet travelFacet)

          Just piListUrl ->
            getOcapListfromUrl piListUrl
              |> Task.andThen
                (\pilist ->
                  List.map (getSinglePIRequest << .url) pilist.contents
                    |> Task.sequence
                    |> Task.andThen
                      (\pis ->
                        let
                          travel = Travel.travelFromTravelFacet travelFacet
                        in
                          Task.succeed { travel | listPI = pis }
                      )
                )
      )
