module Main exposing (..)

import Browser
import File exposing (File)
import Array
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
import Bootstrap.Carousel as Carousel
import Bootstrap.Carousel.Slide as Slide
import Color



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
  = ViewListPIDashboard
  | ViewDashboard PI
  | SimpleViewDashboard PI
  | ViewAudiolistEdit AudiolistEdit


type Tag
  = Free
  | Paying
  | NotReserved
  | OnGoing


type alias Image =
  { url : String
  }


type TypePI
  = Restaurant
  | Hotel
  | Shop
  | TouristicPlace


type alias PI =
  { title : String
  , description : String
  , address : String
  , images : List Image
  , audios : List Audio
  , tags : List Tag
  , typespi : List TypePI
  }

type alias Model =
  { key : Nav.Key
  , ocaps : List OcapData -- kept for debugging
  , audiolistEdits :  List AudiolistEdit
  , currentView : CurrentView
  , audioContent : List Audio
  , files : List File
  , navbarState : Navbar.State
  , listPI : List PI
  , currentPI : PI
  , modalVisibility : Modal.Visibility
  , carouselState : Carousel.State
  }

model0 key state = { key = key
             , ocaps = []
             , audiolistEdits = []
             , currentView = ViewListPIDashboard
             , audioContent = []
             , files = []
             , navbarState = state
             , listPI = []
             , currentPI = PI "" "" "" [] [] [] []
             , modalVisibility = Modal.hidden
             , carouselState = Carousel.initialState
             }

fakeModel0 key state =
  let model = model0 key state
  in { model | listPI =
      [ PI "Meenakshi Amman Temple - India" "मीनाक्षी सुन्दरेश्वरर मन्दिर या मीनाक्षी अम्मां मन्दिर या केवल मीनाक्षी मन्दिर (तमिल: மீனாக்ஷி அம்மன் கோவில்) भारत के तमिल नाडु राज्य के मदुरई नगर, में स्थित एक ऐतिहासिक मन्दिर है। यह हिन्दू देवता शिव (“‘सुन्दरेश्वरर”’ या सुन्दर ईश्वर के रूप में) एवं उनकी भार्या देवी पार्वती (मीनाक्षी या मछली के आकार की आंख वाली देवी के रूप में) दोनो को समर्पित है। यह . I love it." "9 Boulevard de la Canopée" [ Image "https://static.nationalgeographic.fr/files/meenakshi-amman-temple-india.jpg"] [ Audio "" "Hindi" "india" "" "http://localhost:8000/storage/converts/yrXFohm5kSzWqgE2d14LCg==.mp3" "" ] [ Free ] [ TouristicPlace ]
      , PI "Food Festival - Singapour" "It’s no secret that Singaporeans are united in their love for great food. And nowhere is this more evident than at the annual Singapore Food Festival (SFF), which celebrated its 26th anniversary in 2019. Every year, foodies have savoured wonderful delicacies, created by the city-state’s brightest culinary talents in a true feast for the senses." "666 rue de l'Enfer" [ Image "https://www.je-papote.com/wp-content/uploads/2016/08/food-festival-singapour.jpg"] [ Audio "" "Chinese" "china" "" "http://localhost:8000/storage/converts/e2HMlOMqsJzfzNSVSkGiJQ==.mp3" "" ] [ Paying ] [ Restaurant, TouristicPlace ]
      , PI "Hôtel F1 - Bordeaux" "HotelF1 est une marque hôtelière 1 étoile filiale du groupe Accor. Souvent proche des axes de transport, hotelF1 propose une offre hôtelière super-économique et diversifiée, et axe son expérience autour du concept. Fin décembre 2018, hotelF1 compte 172 hôtels en France. The best hotel i have ever seen in my whole life." "Le Paradis (lieu-dit)" [ Image "https://www.ahstatic.com/photos/2472_ho_00_p_1024x768.jpg"] [ Audio "" "English" "united-kingdom" "" "http://localhost:8000/storage/converts/@r4pNRIQkBKk4Jn7H_nvlg==.mp3" "" ] [ Paying, NotReserved ] [ Hotel ]
      , PI "Souk Rabais Bazar - Marrakech" " السوق التقليدي أو السوقة،[1] منطقة بيع وشراء في المدن العربية التقليدية. إن كافة المدن في أسواق والمدن الكبيرة منها فيها أكثر من سوق. معظم الأسواق دائمة ومفتوحة يوميا إلا أن بعض الأسواق موسمية" "Rue du Marchand" [ Image "https://cdn.pixabay.com/photo/2016/08/28/22/22/souk-1627045_960_720.jpg" ] [ Audio "" "Langue du Zouk" "mali" "" "http://localhost:8000/storage/converts/m03@H3yVB@tuuJyt7FZKyg==.mp3" "" ] [ OnGoing ] [ Shop, TouristicPlace, Restaurant ]
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
  | CloseModal
  | ShowModal
  | CarouselMsg Carousel.Msg

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
      { model | currentView = ViewDashboard model.currentPI }

    RouteAudiolistEdit data ->
      case data of
      Nothing ->
        { model | currentView = ViewDashboard model.currentPI }

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

  CloseModal ->
    ( { model | modalVisibility = Modal.hidden } , Cmd.none )

  ShowModal ->
    ( { model | modalVisibility = Modal.shown } , Cmd.none )

  CarouselMsg subMsg ->
    ({ model | carouselState = Carousel.update subMsg model.carouselState }, Cmd.none )

-- SUBSCRIPTIONS


subscriptions : Model -> Sub Msg
subscriptions model =
  Sub.batch [
    Navbar.subscriptions model.navbarState UpdateNavbar
    , Carousel.subscriptions model.carouselState CarouselMsg
  ]



-- VIEW


view : Model -> Browser.Document Msg
view model =
  { title = "TaxiTakeMeTo"
  , body =
    [ case model.currentView of
      ViewListPIDashboard ->
        div
          []
          [ viewNavbar model
          , viewListPIDashboard model.listPI ]

      ViewDashboard pi ->
        div
          []
          [ viewNavbar model
          , viewDashboard pi model.modalVisibility model.carouselState]

      SimpleViewDashboard pi ->
        simpleViewDashboard pi

      ViewAudiolistEdit aledit ->
        text "AudioLists"
    ]
  }

viewNavbar : Model -> Html Msg
viewNavbar model =
  Navbar.config UpdateNavbar
    |> Navbar.lightCustom Color.lightGrey
    |> Navbar.withAnimation
    |> Navbar.collapseMedium
    |> Navbar.brand
      [ onClick (ViewChanged ViewListPIDashboard) ]
      [ img
        [ src "https://cdn2.iconfinder.com/data/icons/ios-7-icons/50/user_male2-512.png"
        , class " align-middle d-inline-block rounded align-top img-thumbnails "
        , style "width" "60px" ]
        []
      ]
    |> Navbar.items
      [ Navbar.itemLink
        [href "#"]
        [ text "Item 1"]
      , Navbar.itemLink
        [href "#"]
        [ text "Item 2"]
      , Navbar.itemLink
        [href "#"]
        [ text "Item 3"]
      , Navbar.itemLink
        [href "#"]
        [ text "Item 4"]
      ]
    |> Navbar.view model.navbarState


-- slideImage : Image ->
slideImage image =
  Slide.config [] (Slide.image [] image.url )

viewModal : Modal.Visibility -> Carousel.State -> List Image -> Html Msg
viewModal modalVisibility carouselState images =
  div []
    [ Modal.config CloseModal
      |> Modal.large
      |> Modal.hideOnBackdropClick True
      |> Modal.body [] [
        Carousel.config CarouselMsg []
          |> Carousel.withControls
          |> Carousel.withIndicators
          |> Carousel.slides
            (List.map slideImage images)
          |> Carousel.view carouselState ]
      |> Modal.footer []
        [ Button.button
          [ Button.outlinePrimary
          , Button.attrs
            [ onClick CloseModal ]
          ]
          [ text "Close" ]
        ]
      |> Modal.view modalVisibility
    ]


viewTagPI : Tag -> Html Msg
viewTagPI tag =
  case tag of
  Free ->
    Button.button
      [ Button.small
      , Button.attrs [ style "width" "120px", style "height" "30px"]
      , Button.outlineSuccess
      , (Button.disabled False)
      ]
      [text "Free"]

  Paying ->
    Button.button
      [ Button.small
      , Button.attrs [ style "width" "120px", style "height" "30px" ]
      , Button.outlineWarning
      , (Button.disabled False)
      ]
      [text "Paying"]

  NotReserved ->
    Button.button
      [ Button.small
      , Button.attrs [ style "width" "120px", style "height" "30px" ]
      , Button.outlineDanger
      , (Button.disabled False)
      ]
      [text "Not Reserved"]

  OnGoing ->
    Button.button
      [ Button.small
      , Button.attrs [ style "width" "120px", style "height" "30px" ]
      , Button.outlinePrimary
      , (Button.disabled False)
      ]
      [text "On Going"]

viewTypePI : TypePI -> Html Msg
viewTypePI typepi =
  case typepi of
  Restaurant ->
    img
      [ class "d-block mx-auto img-fluid m-3 rounded"
      , style "width" "40px"
      , src "https://cdn.pixabay.com/photo/2019/09/08/17/24/eat-4461470_960_720.png"
      ]
      []
  Shop ->
    img
      [ class "d-block mx-auto img-fluid m-3 rounded"
      , style "width" "40px"
      , src "https://cdn.pixabay.com/photo/2015/12/23/01/14/edit-1105049_960_720.png"
      ]
      []
  Hotel ->
    img
      [ class "d-block mx-auto img-fluid m-3 rounded"
      , style "width" "40px"
      , src "https://cdn.pixabay.com/photo/2015/12/28/02/58/home-1110868_960_720.png"
      ]
      []
  TouristicPlace ->
    img
      [ class "d-block mx-auto img-fluid m-3 rounded"
      , style "width" "40px"
      , src "https://cdn.pixabay.com/photo/2016/01/10/22/23/location-1132648_960_720.png"
      ]
      []

viewSimplePILink : PI -> Html Msg
viewSimplePILink pi =
  div
    []
    [ Grid.row
      [ Row.middleXs ]
      [ Grid.col
        [ Col.xs3 ]
        []
      , Grid.col
        [ Col.xs6, Col.textAlign Text.alignXsCenter  ]
        [ h3
            [ onClick (ViewChanged (ViewDashboard pi)) ]
            [ text pi.title ]
        ]
        , Grid.col
        [ Col.xs3, Col.textAlign Text.alignXsCenter ]
        [ Button.button
          [ Button.roleLink, Button.onClick (ViewChanged (ViewDashboard pi)) ]
          [ img
            [ src "https://upload.wikimedia.org/wikipedia/commons/thumb/f/f9/Antu_arrow-right.svg/1024px-Antu_arrow-right.svg.png"
            , style "max-width" "40px"
            , class "img-fluid"
            ]
            []
          ]
        ]
      ]
    , Grid.row
      [ Row.middleXs ]
      [ Grid.col
        [ Col.sm3, Col.textAlign Text.alignXsCenter ]
        [ div
          [ class "text-center py-3 d-flex justify-content-around"
          ]
          (List.map viewTypePI pi.typespi)
        ]
      , Grid.col
        [ Col.sm6 ]
        [ div
          [ class "text-center py-3 d-flex justify-content-around" ]
          (List.map viewTagPI pi.tags)
        ]
      , Grid.col
        [ Col.sm3 ]
        []
      ]
    , hr [] []
    ]

viewListPIDashboard : List PI -> Html Msg
viewListPIDashboard listPI =
  div
    []
    [ h1
      [ class "text-center pt-4" ]
      [ text "My list of PIs" ]
    , Grid.container
      [ class "p-4 mb-4 rounded"
      , style "box-shadow" "0px 0px 50px 1px lightgray" ]
      (List.map viewSimplePILink listPI)
    , Grid.container
      [ class "p-4 mb-4 rounded"
      , style "box-shadow" "0px 0px 50px 1px lightgray" ]
      [ Grid.row
        [ Row.middleXs ]
        [ Grid.col
          [ Col.sm3 ]
          []
        , Grid.col
          [ Col.sm6 ]
          [ div
            [ class "text-center py-3 d-flex justify-content-around" ]
            [ Button.button
              [Button.roleLink]
              [ img
                [ src "https://www.trzcacak.rs/myfile/full/15-159661_message-icon-png.png"
                , style "max-width" "70px"
                , class "img-fluid"
                ]
                []
              ]
            , Button.button
              [Button.roleLink]
              [ img
                [ src "https://png.pngtree.com/svg/20170630/phone_call_1040996.png"
                , style "max-width" "70px"
                , class "img-fluid"
                ]
                []
              ]
            ]
          ]
        , Grid.col
          [ Col.sm3 ]
          []
        ]
      ]
    ]

viewDashboard : PI -> Modal.Visibility -> Carousel.State -> Html Msg
viewDashboard pi modalVisibility carouselState =
  div
    []
    [ h1
      [ class "text-center pt-4"]
      [ text pi.title ]
    , Grid.container
      [ class "p-4 mb-4 rounded"
      , style "box-shadow" "0px 0px 50px 1px lightgray"
      ]
      [ Grid.row
        [ Row.middleXs ]
        [ Grid.col
          [ Col.sm6 ]
          [ img
            [ class "d-block mx-auto img-fluid m-3 rounded"
            , style "width" "400px"
            , onClick ShowModal
            , case (Array.get 0 (Array.fromList pi.images)) of
              Just image ->
                src image.url
              Nothing ->
                src "https://www.labaleine.fr/sites/baleine/files/image-not-found.jpg"
            ]
            []
          ]
        , Grid.col
          [ Col.sm6 ]
          [ div
            [ class "text-justify"]
            [text pi.description]
          ]
        ]
      , Grid.row
        [ Row.middleXs ]
        [ Grid.col
          [ Col.sm3 ]
          []
        , Grid.col
          [ Col.sm6 ]
          [ div
            [ class "text-center py-3 d-flex justify-content-around" ]
            (List.map viewTagPI pi.tags)
          ]
        , Grid.col
          [ Col.sm3 ]
          []
        ]
      , hr
        [class "pt-4 pb-2"]
        []
      , h2
        [ class "text-center"]
        [ text "Audio language" ]
      , Grid.container
        [ class "p-4" ]
        (List.map viewAudioLanguage pi.audios)
      , hr
        [class "pt-4 pb-3"]
        []
      , div
        [ class "text-center" ]
        [ Button.button
          [ Button.large
          , Button.outlineSecondary
          , Button.onClick (ViewChanged (SimpleViewDashboard pi))
          ]
          [ text "Simple view"
          , img
            [ class "mx-auto img-fluid pl-3 rounded"
            , style "width" "60px"
            , src "https://upload.wikimedia.org/wikipedia/commons/thumb/e/eb/PICOL_icon_View.svg/1024px-PICOL_icon_View.svg.png"
            ]
            []
          ]
        ]
      ]
    , viewModal modalVisibility carouselState pi.images
    ]

simpleViewDashboard : PI -> Html Msg
simpleViewDashboard pi =
  div
    []
    [ h1
      [ class "text-center pt-4"]
      [ text pi.title ]
    , Grid.container
      [ class "p-4 mb-4 rounded"
      , style "box-shadow" "0px 0px 50px 1px lightgray" ]
      [ Grid.row
        [ Row.middleXs ]
        [ Grid.col
          [ Col.sm6 ]
          [ img
            [ style "width" "100%"
            , style "max-width" "150px"
            , class "d-block mx-auto img-fluid m-3 rounded"
            , case (Array.get 0 (Array.fromList pi.images)) of
              Just image ->
                src image.url
              Nothing ->
                src "https://www.labaleine.fr/sites/baleine/files/image-not-found.jpg"
            ]
            []
          ]
        , Grid.col
          [ Col.sm6 ]
          [ h4
            [class "text-center "]
            [text pi.address]
          ]
        ]
      , hr
        [class "pt-4"]
        []
      , h2
        [ class "text-center"]
        [ text "Audio language" ]
      , Grid.container
        [ class "p-4" ]
        (List.map viewAudioLanguage pi.audios)
      , hr
        [class "pt-4 pb-3"]
        []
      , div
        [ class "text-center" ]
        [ Button.button
          [ Button.large
          , Button.outlineSecondary
          , Button.onClick (ViewChanged (ViewDashboard pi))
          ]
          [ text "Exit view"
          , img
            [ class "mx-auto img-fluid pl-3 rounded"
            , style "width" "60px"
            , src "https://upload.wikimedia.org/wikipedia/commons/thumb/7/7e/Ic_exit_to_app_48px.svg/1024px-Ic_exit_to_app_48px.svg.png"
            ]
            []
          ]
        ]
      ]
    ]

viewAudioLanguage : Audio -> Html.Html msg
viewAudioLanguage audio =
  Grid.row
    [ Row.middleXs ]
    [ Grid.col
      [ Col.xs5 ]
      [ h3
        [ class "text-center"]
        [text audio.language]
      ]
    , Grid.col
      [ Col.xs2 ]
      [ img
        [ class "d-block mx-auto img-fluid"
        , style "max-width" "35px"
        , src ("http://localhost:8000/storage/flags/" ++ audio.flagLang ++ ".png")
        ]
        []
      ]
    , Grid.col
      [ Col.sm5, Col.textAlign Text.alignXsCenter ]
      [ Html.audio
        [ controls True
        , style "width" "100%"
        , style "max-width" "300px"
        ]
        [ Html.source
          [ src audio.path
          , type_ "audio/mpeg"
          ]
          []
        ]
      ]
    ]


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
  D.map6 Audio
  (field "type" string)
  (field "language" string)
  (field "flagLang" string)
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
  , flagLang : String
  , viewfacet : String
  , path : String
  , deleteAudio : String
  }
