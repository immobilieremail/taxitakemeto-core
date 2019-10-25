module Main exposing (..)

import Browser
import File exposing (File)
import Browser.Navigation as Nav
import Html exposing (..)
import Html.Attributes exposing (..)
import Html.Events exposing (..)
import Http
import Json.Decode as D exposing (Decoder,map4, map3, field, string, int, list)
import Url
import Url.Parser as P
import Url.Parser exposing ((</>))
import Bootstrap.Navbar as Navbar
import Bootstrap.Grid as Grid
import Bootstrap.Grid.Col as Col
import Bootstrap.Grid.Row as Row
import Bootstrap.Card as Card
import Bootstrap.Card.Block as Block
import Bootstrap.Button as Button
import Bootstrap.ListGroup as Listgroup
import Bootstrap.Modal as Modal
import Bootstrap.Text as Text



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
  | SimpleViewDashboard
  | ViewAudiolistEdit AudiolistEdit


type alias Model =
  { key : Nav.Key
  , ocaps : List OcapData -- kept for debugging
  , audiolistEdits :  List AudiolistEdit
  , currentView : CurrentView
  , audioContent : List Audio
  , files : List File
  , navbarState : Navbar.State
  }

model0 key state = { key = key
             , ocaps = []
             , audiolistEdits = []
             , currentView = ViewDashboard
             , audioContent = []
             , files = []
             , navbarState = state
             }

fakeModel0 key state =
  let model = model0 key state
  in { model | audioContent =
    [
      Audio "" "English" "" "http://localhost:8000/storage/converts/0Fma9oR2sTgEINpUgaa7iA==.mp4" "",
      Audio "" "ไทย" "" "http://localhost:8000/storage/converts/0Fma9oR2sTgEINpUgaa7iA==.mp4" "",
      Audio "" "Français" "" "http://localhost:8000/storage/converts/0Fma9oR2sTgEINpUgaa7iA==.mp4" ""
    ]
  }

init : () -> Url.Url -> Nav.Key -> ( Model, Cmd Msg )
init flags url key =
  let (state, cmd) = Navbar.initialState UpdateNavbar
      model = fakeModel0 key state
  in ( updateFromUrl model url, cmd )



-- UPDATE


type Msg
  = LinkClicked Browser.UrlRequest
  | UrlChanged Url.Url
  | ViewChanged CurrentView
  | GotFiles (List File)
  | GetNewAudiolistEdit
  | GotNewAudioEdit (Result Http.Error OcapData)
  | GotNewAudiolistEdit (Result Http.Error OcapData)
  | GotNewAudioContent (Result Http.Error Audio)
  | UpdateNavbar Navbar.State

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

  ViewChanged newView ->
    ( { model | currentView = newView }, Cmd.none )

  GetNewAudiolistEdit ->
    ( model, getNewAudiolistEdit )

  GotFiles inputFiles ->
    ( model, Http.request
        { method = "POST"
          , url = "/api/audio"
          , headers = []
          , body = Http.multipartBody (List.map (Http.filePart "audio") inputFiles)
          , expect = Http.expectJson GotNewAudioEdit decodeOcap
          , timeout = Nothing
          , tracker = Just "upload"
          }
    )

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

  GotNewAudioEdit data ->
    case data of
    Ok ocap ->
      case extractAudioEdit ocap of
      Nothing ->
        ( model, Cmd.none )

      Just audioedit ->
        ( model, Http.request
          { method = "GET"
            , url = audioedit.url
            , headers = []
            , body = Http.emptyBody
            , expect = Http.expectJson GotNewAudioContent decodeAudioContent
            , timeout = Nothing
            , tracker = Just "play"
          }
        )

    Err _ ->
      ( model, Cmd.none )

  GotNewAudioContent data ->
    case data of
    Ok ocap ->
      ( { model
        | audioContent = model.audioContent ++ [ocap] }, Cmd.none )

    Err _ ->
      ( model, Cmd.none )

  UpdateNavbar state ->
    ( { model | navbarState = state }, Cmd.none)




-- SUBSCRIPTIONS


subscriptions : Model -> Sub Msg
subscriptions model =
  Navbar.subscriptions model.navbarState UpdateNavbar



-- VIEW


view : Model -> Browser.Document Msg
view model =
  case model.currentView of
  ViewDashboard ->
    viewDashboard model

  SimpleViewDashboard ->
    simpleViewDashboard model
  ViewAudiolistEdit aledit ->
    viewAudiolistEdit model

viewNavbar : Model -> Html Msg
viewNavbar model =
  Navbar.config UpdateNavbar
    |> Navbar.withAnimation
    |> Navbar.collapseMedium
    |> Navbar.brand [ href "#" ] [ img [ src "https://completeconcussions.com/drive/uploads/2017/10/detail-john-doe.jpg", class " align-middle d-inline-block rounded align-top img-thumbnails ", style "width" "60px" ] [ text "Brand" ], text "John Doe" ]
    |> Navbar.items
        [ Navbar.itemLink [href "#"] [ text "Item 1"]
        , Navbar.itemLink [href "#"] [ text "Item 2"]
        , Navbar.itemLink [href "#"] [ text "Item 3"]
        , Navbar.itemLink [href "#"] [ text "Item 4"]
        ]
    |> Navbar.view model.navbarState

viewDashboard : Model -> Browser.Document Msg
viewDashboard model =
    { title = "My point d'intérêt"
    , body =
        [ viewNavbar model
        , h1 [ class "text-center pt-4"] [ text "My Point of Interest" ]
        , Grid.container [ class "p-4 mb-4 rounded", style "box-shadow" "0px 0px 50px 1px lightgray" ]
            [ Grid.row
                [ Row.middleXs ]
                [ Grid.col
                    [ Col.sm6 ]
                    [ img [  class "d-block mx-auto img-fluid m-3 rounded", src "https://img2.10bestmedia.com/Images/Photos/189483/p-Red_54_990x660_201406020123.jpg" ] [] ]
                , Grid.col
                    [ Col.sm6 ]
                    [ h3 [] [text "Red Restaurant"]
                      , div [ class "text-justify"] [text "Your bones don't break, mine do. That's clear. Your cells react to bacteria and viruses differently than mine. You don't get sick, I do. That's also clear. But for some reason, you and I react the exact same way to water. We swallow it too fast, we choke. We get some in our lungs, we drown. However unreal it may seem, we are connected, you and I. We're on the same curve, just on opposite ends."]
                    ]
                ]
              , Grid.row
                [ Row.middleXs ]
                [ Grid.col
                    [ Col.sm2 ] []
                  , Grid.col
                    [ Col.sm4 ]
                    [ div [ class "text-center py-3" ] [
                      Button.button
                        [ Button.large, Button.outlineDanger, (Button.disabled False) ]
                        [text "Not reserved"]
                      ]
                    ]
                , Grid.col
                    [ Col.sm4 ]
                    [ div [ class "text-center py-3" ] [
                      Button.button
                        [ Button.large, Button.outlineSuccess, (Button.disabled False) ] [text "On going"]
                      ]
                    ]
                , Grid.col
                    [ Col.sm2 ] []
                ]
                , hr [class "pt-4 pb-2"] []
                , h2  [ class "text-center"] [ text "Audio language" ]
                , Grid.container [ class "p-4" ] (List.map viewAudioLanguage model.audioContent)
                , hr [class "pt-4 pb-3"] []
                , div [ class "text-center" ] [
                  Button.button
                    [ Button.large, Button.outlineSecondary, Button.onClick (ViewChanged SimpleViewDashboard) ]
                    [ text "Simple view", img [ class "mx-auto img-fluid pl-3 rounded", style "width" "60px", src "https://upload.wikimedia.org/wikipedia/commons/thumb/e/eb/PICOL_icon_View.svg/1024px-PICOL_icon_View.svg.png" ] [] ]
                ]
            ]
        ]
    }

simpleViewDashboard : Model -> Browser.Document Msg
simpleViewDashboard model =
    { title = "My point d'intérêt"
    , body =
        [ h1 [ class "text-center pt-4"] [ text "My Point of Interest" ]
        , Grid.container [ class "p-4 mb-4 rounded", style "box-shadow" "0px 0px 50px 1px lightgray" ]
            [ Grid.row
                [ Row.middleXs ]
                [ Grid.col
                    [ Col.sm6 ]
                    [ img [  style "width" "100%", style "max-width" "150px", class "d-block mx-auto img-fluid m-3 rounded", src "https://img2.10bestmedia.com/Images/Photos/189483/p-Red_54_990x660_201406020123.jpg" ] [] ]
                , Grid.col
                    [ Col.sm6 ]
                    [ h4 [] [text "Red Restaurant"]
                      , div [ class "text-justify"] [text "Your bones don't break, mine do. That's clear."]
                    ]
                ]
                , hr [class "pt-4"] []
                , h2  [ class "text-center"] [ text "Audio language" ]
                , Grid.container [ class "p-4" ] (List.map viewAudioLanguage model.audioContent)
                , hr [class "pt-4 pb-3"] []
                , div [ class "text-center" ] [
                  Button.button
                    [ Button.large, Button.outlineSecondary, Button.onClick (ViewChanged ViewDashboard) ]
                    [ text "Exit view", img [ class "mx-auto img-fluid pl-3 rounded", style "width" "60px", src "https://upload.wikimedia.org/wikipedia/commons/thumb/7/7e/Ic_exit_to_app_48px.svg/1024px-Ic_exit_to_app_48px.svg.png" ] [] ]
                ]
            ]
        ]
    }

viewAudioLanguage : Audio -> Html.Html msg
viewAudioLanguage audio =
  Grid.row
    [ Row.middleXs ]
    [ Grid.col
      [ Col.sm4 ]
      [ h3 [ class "text-center"] [text audio.language] ]
    , Grid.col
      [ Col.sm2 ]
      [ img [class "d-block mx-auto img-fluid", src "storage/converts/sound.png"] [] ]
    , Grid.col
      [ Col.sm6, Col.textAlign Text.alignXsCenter ]
      [ Html.audio [controls True, style "width" "100%", style "max-width" "300px"] [ Html.source [ src audio.path, type_ "audio/mpeg"] [] ]  ]
    ]

viewAudiolistEdit : Model -> Browser.Document Msg
viewAudiolistEdit model =
  { title = "TaxiTakeMeTo"
  , body =
  [ h1 [] [ text "Audio list" ]
  ,input
        [ type_ "file"
        , multiple True
        , on "change" (D.map GotFiles filesDecoder)
        ]
        []
  ] ++ (List.map linkAudioEdit model.audioContent)
  }

linkAudiolistEdit : AudiolistEdit -> Html Msg
linkAudiolistEdit aledit =
  li []
  [ a [ href <| "/elm/aledit#" ++ aledit.url ] [ text aledit.url ]
  ]

linkAudioEdit : Audio -> Html Msg
linkAudioEdit audio =
  li []
  [ -- a [ href <| "/elm/aledit#" ++ aledit.url ] [ text aledit.url ]
    Html.audio [controls True] [ Html.source [src audio.path, type_ "audio/mpeg"] [] ]
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

filesDecoder : D.Decoder (List File)
filesDecoder =
  D.at ["target","files"] (D.list File.decoder)

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

decodeAudioContent : Decoder Audio
decodeAudioContent =
  D.map5 Audio
  (field "type" string)
  (field "language" string)
  (field "view_facet" string)
  (field "path" string)
  (field "delete" string)

type alias AudiolistEdit =
  { url : String
  }

type alias AudioEdit =
  { url : String
  }

extractAudiolistEdit : OcapData -> Maybe AudiolistEdit
extractAudiolistEdit ocap =
  if ocap.jsonType == "ocap" && ocap.ocapType == "AudioListEdit" then
  Just <| AudiolistEdit ocap.url
  else
  Nothing

extractAudioEdit : OcapData -> Maybe AudioEdit
extractAudioEdit ocap =
  if ocap.jsonType == "ocap" && ocap.ocapType == "AudioEdit" then
  Just <| AudioEdit ocap.url
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

type alias Audio =
  { jsontype : String
  , language : String
  , viewfacet : String
  , path : String
  , deleteAudio : String
  }
