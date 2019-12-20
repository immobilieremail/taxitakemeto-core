module Request exposing (
  getDropboxRequest, getSingleShellRequest, getSingleTravelRequest, getSinglePIRequest,
  createNewTravelRequest, createNewPIRequest, addPItoTravelRequest, addTraveltoShellRequest)

import Http
import Task exposing (Task)
import Json.Encode as E exposing (string, list)
import Json.Decode as D exposing (Decoder)
import SwissNumber exposing (SwissNumber)
import Travel exposing (Travel, TravelFacet)
import Shell exposing (Shell, ShellFacet, ShellDropbox, DropboxFacet)
import Media exposing (MediaFacet)
import Ocap exposing (Ocap, OcapListFacet)
import PI exposing (PI, PIFacet)

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


getDropboxFromUrl : SwissNumber -> Task Http.Error DropboxFacet
getDropboxFromUrl ocapUrl =
  Http.task
    { method = "GET"
    , headers = []
    , url = ocapUrl
    , body = Http.emptyBody
    , resolver = Http.stringResolver <| handleJsonResponse <| Shell.dropboxFacetDecoder
    , timeout = Nothing
    }


getShellfromUrl : SwissNumber -> Task Http.Error ShellFacet
getShellfromUrl ocapUrl =
  Http.task
    { method = "GET"
    , headers = []
    , url = ocapUrl
    , body = Http.emptyBody
    , resolver = Http.stringResolver <| handleJsonResponse <| Shell.shellFacetDecoder
    , timeout = Nothing
    }


putToOcapList : List { a | swissNumber : SwissNumber } -> SwissNumber -> Task Http.Error ()
putToOcapList ocapList ocapUrl =
  Http.task
    { method = "PUT"
    , headers = []
    , url = ocapUrl
    , body = Http.jsonBody (E.object [ ("ocaps", E.list E.string (List.map (\obj -> obj.swissNumber) ocapList)) ])
    , resolver = Http.stringResolver (\_ -> Ok ())
    , timeout = Nothing
    }


createNewOcapList : List { a | swissNumber : SwissNumber } -> Task Http.Error Ocap
createNewOcapList ocapList =
  Http.task
    { method = "POST"
    , headers = []
    , url = "http://localhost:8000/api/list"
    , body = Http.jsonBody (E.object [ ("ocaps", E.list E.string (List.map (\ocap -> ocap.swissNumber) ocapList)) ])
    , resolver = Http.stringResolver <| handleJsonResponse <| Ocap.ocapDecoder
    , timeout = Nothing
    }


createNewPI : String -> String -> String -> Task Http.Error Ocap
createNewPI title description address =
  Http.task
    { method = "POST"
    , headers = []
    , url = "http://localhost:8000/api/pi"
    , body = Http.jsonBody (E.object [ ("title", E.string title), ("description", E.string description), ("address", E.string address) ])
    , resolver = Http.stringResolver <| handleJsonResponse <| Ocap.ocapDecoder
    , timeout = Nothing
    }


createNewTravel : String -> SwissNumber -> Task Http.Error Ocap
createNewTravel title listUrl =
  Http.task
    { method = "POST"
    , headers = []
    , url = "http://localhost:8000/api/travel"
    , body = Http.jsonBody (E.object [ ("title", E.string title), ("pis", E.string listUrl) ])
    , resolver = Http.stringResolver <| handleJsonResponse <| Ocap.ocapDecoder
    , timeout = Nothing
    }


putToShell : SwissNumber -> Http.Body -> Task Http.Error ()
putToShell shellUrl jsonBody =
  Http.task
    { method = "PUT"
    , headers = []
    , url = shellUrl
    , body = jsonBody
    , resolver = Http.stringResolver (\_ -> Ok ())
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
                          audiotyped = List.filter (\mediaFacet -> mediaFacet.mediaType == Media.AudioType) mediaFacets
                          notaudiotyped = List.filter (\mediaFacet -> mediaFacet.mediaType /= Media.AudioType) mediaFacets
                          audios = List.map Media.audioFromMediaFacet audiotyped
                          medias = List.map Media.mediaFromMediaFacet notaudiotyped
                        in
                          Task.succeed { pi | medias = medias, audios = audios }
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


getDropboxRequest : SwissNumber -> Task Http.Error ShellDropbox
getDropboxRequest ocapUrl =
  getDropboxFromUrl ocapUrl
    |> Task.andThen
      (\dropboxFacet ->
        Task.succeed (ShellDropbox ocapUrl dropboxFacet.name)
      )


getSingleShellRequest : SwissNumber -> Task Http.Error (Shell, Maybe ShellDropbox)
getSingleShellRequest ocapUrl =
  getShellfromUrl ocapUrl
    |> Task.andThen
      (\shellFacet ->
        case shellFacet.travelList of
          Nothing ->
            case shellFacet.sender of
              Nothing ->
                Task.succeed (Shell.shellFromShellFacet shellFacet, Nothing)

              Just senderDropboxUrl ->
                getDropboxFromUrl senderDropboxUrl
                  |> Task.andThen
                    (\dropboxFacet ->
                      Task.succeed (Shell.shellFromShellFacet shellFacet, Just (ShellDropbox senderDropboxUrl dropboxFacet.name))
                    )

          Just travelListUrl ->
            getOcapListfromUrl travelListUrl
              |> Task.andThen
                (\travellist ->
                  List.map (getSingleTravelRequest << .url) travellist.contents
                    |> Task.sequence
                    |> Task.andThen
                      (\travels ->
                        let
                          shell = Shell.shellFromShellFacet shellFacet
                        in
                          case shellFacet.sender of
                            Nothing ->
                              Task.succeed ({ shell | travelList = travels }, Nothing)

                            Just senderDropboxUrl ->
                              getDropboxFromUrl senderDropboxUrl
                                |> Task.andThen
                                  (\dropboxFacet ->
                                    Task.succeed ({ shell | travelList = travels }, Just (ShellDropbox senderDropboxUrl dropboxFacet.name))
                                  )
                      )
                )
      )


createNewTravelRequest : String -> List PI -> Task Http.Error Travel
createNewTravelRequest title piList =
  createNewOcapList piList
    |> Task.andThen
      (\listOcap ->
        createNewTravel title listOcap.url
          |> Task.andThen
            (\travelOcap ->
              getSingleTravelRequest travelOcap.url
            )
      )


createNewPIRequest : String -> String -> String -> Task Http.Error PI
createNewPIRequest title description address =
  createNewPI title description address
    |> Task.andThen
      (\piOcap ->
        getSinglePIRequest piOcap.url
      )


addPItoTravelRequest : SwissNumber -> List PI -> Task Http.Error ()
addPItoTravelRequest ocapUrl piList =
  getTravelfromUrl ocapUrl
    |> Task.andThen
      (\travelFacet ->
        case travelFacet.piList of
          Just piListUrl ->
            putToOcapList piList piListUrl

          Nothing ->
            createNewOcapList piList
              |> Task.andThen
                (\piOcapList ->
                  putToOcapList piList piOcapList.url
                )
      )

addTraveltoShellRequest : SwissNumber -> List Travel -> Task Http.Error ()
addTraveltoShellRequest ocapUrl travelList =
  getShellfromUrl ocapUrl
    |> Task.andThen
      (\shellFacet ->
        case shellFacet.travelList of
          Just travelListUrl ->
            putToOcapList travelList travelListUrl
              |> Task.andThen
                (\_ ->
                  putToShell ocapUrl <| Http.jsonBody (E.object [ ("travels", E.string travelListUrl) ])
                )

          Nothing ->
            createNewOcapList travelList
              |> Task.andThen
                (\travelOcapList ->
                  putToOcapList travelList travelOcapList.url
                    |> Task.andThen
                      (\_ ->
                        putToShell ocapUrl <| Http.jsonBody (E.object [ ("travels", E.string travelOcapList.url) ])
                      )
                )
      )
