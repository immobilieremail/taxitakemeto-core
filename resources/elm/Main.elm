import Browser
import Browser.Navigation as Nav
import Html exposing (..)
import Html.Attributes exposing (..)
import Html.Events exposing (..)
import Http
import Json.Decode as D exposing (Decoder, field, string, int)
import Url



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


type alias Model =
  { key : Nav.Key
  , url : Url.Url
  , ocaps : List OcapData -- kept for debugging
  , audiolistEdits :  List AudiolistEdit
  }


init : () -> Url.Url -> Nav.Key -> ( Model, Cmd Msg )
init flags url key =
  ( Model key url [] [], Cmd.none )



-- UPDATE


type Msg
  = LinkClicked Browser.UrlRequest
  | UrlChanged Url.Url
  | GetNewAudiolistEdit
  | GotNewAudiolistEdit (Result Http.Error OcapData)


update : Msg -> Model -> ( Model, Cmd Msg )
update msg model =
  case msg of
    LinkClicked urlRequest ->
      case urlRequest of
        Browser.Internal url ->
          ( model, Nav.pushUrl model.key (Url.toString url) )

        Browser.External href ->
          ( model, Nav.load href )

    UrlChanged url ->
      ( { model | url = url }
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



-- SUBSCRIPTIONS


subscriptions : Model -> Sub Msg
subscriptions _ =
  Sub.none



-- VIEW


view : Model -> Browser.Document Msg
view model =
  { title = "TaxiTakeMeTo"
  , body =
    [ h1 [] [ text "AudiolistEdits" ]
    , button [ onClick GetNewAudiolistEdit ] [ text "New Audiolist" ]
    ] ++ (List.map viewAudiolistEdit model.audiolistEdits)
  }


viewAudiolistEdit : AudiolistEdit -> Html Msg
viewAudiolistEdit aledit =
  li []
    [ a [ href aledit.url ] [ text aledit.url ]
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
  if ocap.jsonType == "ocap" && ocap.ocapType == "ALEdit" then
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
