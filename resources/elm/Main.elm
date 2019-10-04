module Main exposing (..)

import Browser
import Browser.Navigation as Nav
import Html exposing (..)
import Html.Attributes exposing (..)
import Html.Events exposing (..)
import Http
import Json.Decode as D exposing (Decoder,map4, map3, field, string, int, list)
import Url
import Url.Parser as P
import Url.Parser exposing ((</>))



-- MAIN


main : Program () Model Msg
main =
  Browser.application
    { init = init
    , view = view
    , update = update
    , subscriptions = subscriptions
    , onUrlChange = UrlChanged
    , onUrlRequest = LinkClicked
    }



-- MODEL

type CurrentView
  = ViewDashboard
  | ViewAudiolistEdit AudiolistEdit

type alias Model =
  { key : Nav.Key
  , ocaps : List OcapData -- kept for debugging
  , audiolistEdits :  List AudiolistEdit
  , currentView : CurrentView
  , audiolistContent : List OcapData
  }


init : () -> Url.Url -> Nav.Key -> ( Model, Cmd Msg )
init flags url key =
  ( updateFromUrl (Model key [] [] ViewDashboard []) url, Cmd.none )



-- UPDATE


type Msg
  = LinkClicked Browser.UrlRequest
  | UrlChanged Url.Url
  | GetNewAudiolistEdit
  | GotNewAudiolistEdit (Result Http.Error OcapData)
  | GotNewAudiolistContent (Result Http.Error AudioList)


type Route
  = RouteDashboard
  | RouteAudiolistEdit (Maybe String)

router : P.Parser (Route -> a) a
router =
  P.oneOf
    [ P.map RouteDashboard <| P.s "elm"
    , P.map RouteAudiolistEdit <| P.s "elm" </> P.s "aledit" </> P.fragment identity
    ]

updateFromUrl : Model -> Url.Url -> Model
updateFromUrl model url =
  case P.parse router url of
    Nothing ->
      model

    Just route ->
      case route of
        RouteDashboard ->
          { model | currentView = ViewDashboard }

        RouteAudiolistEdit data ->
          case data of
            Nothing ->
              { model | currentView = ViewDashboard}

            Just ocapUrl ->
              { model | currentView = ViewAudiolistEdit <| AudiolistEdit ocapUrl}

update : Msg -> Model -> ( Model, Cmd Msg )
update msg model =
  case msg of
    LinkClicked urlRequest ->
      case urlRequest of
        Browser.Internal url ->
          ( updateFromUrl model url, Nav.pushUrl model.key (Url.toString url) )

        Browser.External href ->
          ( model, Nav.load href )

    UrlChanged url ->
      ( updateFromUrl model url
      , Cmd.none
      )

    GetNewAudiolistEdit ->
      ( model, getNewAudiolistEdit )

    GotNewAudiolistEdit data ->
      case data of
        Ok ocap ->
          case extractAudiolistEdit ocap of
            Nothing ->
              ( { model
                | ocaps = model.ocaps ++ [ocap] }, Cmd.none )

            Just aledit ->
              ( { model
                | audiolistEdits = model.audiolistEdits ++ [aledit]
                , ocaps = model.ocaps ++ [ocap] }, Cmd.none )

        Err _ ->
          ( model, Cmd.none )

    GotNewAudiolistContent data ->
      case data of
        Ok ocap ->
            ( { model
              | audiolistContent = ocap.contents }, Cmd.none )

        Err _ ->
          ( model, Cmd.none )





-- SUBSCRIPTIONS


subscriptions : Model -> Sub Msg
subscriptions _ =
  Sub.none



-- VIEW


view : Model -> Browser.Document Msg
view model =
  case model.currentView of
    ViewDashboard ->
      viewDashboard model

    ViewAudiolistEdit aledit ->
      viewAudiolistEdit aledit

viewDashboard : Model -> Browser.Document Msg
viewDashboard model =
  { title = "TaxiTakeMeTo"
  , body =
    [ h1 [] [ text "AudiolistEdits" ]
    , button [ onClick GetNewAudiolistEdit ] [ text "New Audiolist" ]
    ] ++ (List.map linkAudiolistEdit model.audiolistEdits)
  }

viewAudiolistEdit aledit =
  { title = "TaxiTakeMeTo"
  , body =
    [ div [] [ text "Oops..." ] ]
  }

linkAudiolistEdit : AudiolistEdit -> Html Msg
linkAudiolistEdit aledit =
  li []
    [ a [ href <| "/elm/aledit#" ++ aledit.url ] [ text aledit.url ]
    ]


viewOcap : OcapData -> Html Msg
viewOcap ocap =
  dl [ style "border" "solid" ]
    [ dt [] [ text "type" ]
    , dd [] [ text ocap.jsonType ]
    , dt [] [ text "ocapType" ]
    , dd [] [ text ocap.ocapType ]
    , dt [] [ text "url" ]
    , dd [] [ text ocap.url ]
    ]


-- JSON API

type alias OcapData =
  { jsonType : String
  , ocapType : String
  , url : String
  }

decodeOcap : Decoder OcapData
decodeOcap =
  D.map3 OcapData
    (field "type" string)
    (field "ocapType" string)
    (field "url" string)

type alias AudiolistEdit =
  { url : String
  }

extractAudiolistEdit : OcapData -> Maybe AudiolistEdit
extractAudiolistEdit ocap =
  if ocap.jsonType == "ocap" && ocap.ocapType == "AudioListEdit" then
    Just <| AudiolistEdit ocap.url
  else
    Nothing


getNewAudiolistEdit : Cmd Msg
getNewAudiolistEdit =
  Http.post
    { url = "/api/audiolist"
    , expect = Http.expectJson GotNewAudiolistEdit decodeOcap
    , body = Http.emptyBody
    }


type alias AudioList =
    { jsontype : String
    , viewfacet : String
    , update : String
    , contents : List OcapData
    }

decodeAudiolistContent : Decoder AudioList
decodeAudiolistContent =
    D.map4 AudioList
    (field "type" string)
    (field "view_facet" string)
    (field "update" string)
    (field "contents" (D.list decodeOcap))

getAudiolistContent : String -> Cmd Msg
getAudiolistContent url =
  Http.get
    { url = url
    , expect = Http.expectJson GotNewAudiolistContent decodeAudiolistContent
    }

