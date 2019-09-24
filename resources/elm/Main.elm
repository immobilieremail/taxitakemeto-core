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
  , ocaps : List OcapData
  }


init : () -> Url.Url -> Nav.Key -> ( Model, Cmd Msg )
init flags url key =
  ( Model key url [], Cmd.none )



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
          ( { model | ocaps = model.ocaps ++ [ocap] }, Cmd.none )

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
    [ div [] <|
        [ button [ onClick GetNewAudiolistEdit ] [ text "New Audiolist" ]
        ] ++ (List.map viewOcap model.ocaps)
    ]
  }


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

type alias AudiolistEdit =
  { url : String
  }

decodeOcap : Decoder OcapData
decodeOcap =
  D.map3 OcapData
    (field "type" string)
    (field "ocapType" string)
    (field "url" string)

{-
decodeAudioListEdit : D.Decoder AudiolistEdit
decodeAudioListEdit =
  let
    checkOcap : String -> String -> String -> D.Decoder AudiolistEdit
    checkOcap jsonType ocapType url =
      if jsonType == "ocap" && ocapType == "ALEdit" then
        D.succeed url
      else
        D.fail "Not a AudiolistEdit ocap"
  in        
  D.succeed checkOcap
    (D.field "type" D.string)
    (D.field "ocapType" D.string)
    (D.field "url" D.string)
-}

getNewAudiolistEdit : Cmd Msg
getNewAudiolistEdit =
  Http.post
    { url = "/api/audiolist"
    , expect = Http.expectJson GotNewAudiolistEdit decodeOcap
    , body = Http.emptyBody
    }
