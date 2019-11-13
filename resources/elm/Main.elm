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
import Bootstrap.Accordion as Accordion
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
import Process
import Task
import PI exposing (..)
import Fake exposing (..)
import Media exposing (..)
import Date exposing (Date)
import Travel exposing (Travel)
import SwissNumber exposing (SwissNumber)
import OverButton as OB exposing (..)
import ViewLoading as Loading exposing (view)



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
  = ViewListTravelDashboard
  | ViewListPIDashboard
  | ViewPI
  | SimpleViewPI
  | LoadingPage


type alias Model =
  { key : Nav.Key
  , currentView : CurrentView
  , navbarState : Navbar.State
  , currentTravel : Travel
  , currentPI : PI
  , listTravel : List Travel
  , carouselState : Carousel.State
  , accordionState : Accordion.State
  , mouseOver : List OverButton
  }


model0 key state =
  { key = key
  , currentView = ViewListTravelDashboard
  , navbarState = state
  , currentTravel = Travel "" "" [] (Date "" "" "") (Date "" "" "")
  , currentPI = PI "" "" "" "" [] [] [] []
  , listTravel = []
  , carouselState = Carousel.initialState
  , accordionState = Accordion.initialState
  , mouseOver = []
  }

fakeModel0 key state =
  let model = model0 key state
  in { model | listTravel =
    [ Travel
        "http://localhost:8000/api/obj/parisdakar"
        "Paris - Dakar"
        []
        (Date "01" "05" "2018")
        (Date "02" "01" "2021")
    , Travel
        "http://localhost:8000/api/obj/voyagebirmanie"
        "Petit voyage en Birmanie"
        []
        (Date "01" "01" "1970")
        (Date "30" "06" "2005")
    , Travel
        "http://localhost:8000/api/obj/sejourtadjikistan"
        "Séjour au Tadjikistan"
        []
        (Date "19" "07" "2019")
        (Date "06" "12" "2019")
    , Travel
        "http://localhost:8000/api/obj/vacancesmontagne"
        "Vacances à la montagne"
        []
        (Date "26" "09" "2040")
        (Date "13" "04" "2042")
    ]
  }

init : () -> Url.Url -> Nav.Key -> ( Model, Cmd Msg )
init flags url key =
  let (state, cmd) = Navbar.initialState UpdateNavbar
      model = fakeModel0 key state
  in updateFromUrl model url cmd



-- UPDATE


type Msg
  = LinkClicked Browser.UrlRequest
  | UrlChanged Url.Url
  | ViewChanged CurrentView
  | GetPI SwissNumber
  | GotPI (Result Http.Error PI)
  | GetTravel SwissNumber
  | GotTravel (Result Http.Error Travel)
  | UpdateNavbar Navbar.State
  | CarouselMsg Carousel.Msg
  | AccordionMsg Accordion.State
  | CarouselPrev
  | CarouselNext
  | MouseOver OverButton
  | MouseOut OverButton


type Route
  = RouteListPI
  | RoutePI (Maybe String)
  | RouteTravel (Maybe String)

router : P.Parser (Route -> a) a
router =
  P.oneOf
  [ P.map RouteListPI <| P.s "elm"
  , P.map RoutePI <| P.s "elm" </> P.s "pi" </> P.fragment identity
  , P.map RouteTravel <| P.s "elm" </> P.s "travel" </> P.fragment identity
  ]

updateFromUrl : Model -> Url.Url -> Cmd Msg -> ( Model, Cmd Msg )
updateFromUrl model url commonCmd =
  case P.parse router url of
  Nothing ->
    ( model, commonCmd )

  Just route ->
    case route of
    RouteListPI ->
      ( { model | currentView = ViewListTravelDashboard }, commonCmd )

    RoutePI data ->
      case data of
      Nothing ->
        ( model, commonCmd )

      Just ocapUrl ->
        ( model
        , Cmd.batch
          [ commonCmd
          , getPIfromUrl ocapUrl
          ]
        )

    RouteTravel data ->
      case data of
      Nothing ->
        ( model, commonCmd )

      Just ocapUrl ->
        ( { model | currentView = LoadingPage }
        , Cmd.batch
          [ commonCmd
          , getTravelfromUrl ocapUrl
          ]
        )

update : Msg -> Model -> ( Model, Cmd Msg )
update msg model =
  case msg of
  LinkClicked urlRequest ->
    case urlRequest of
    Browser.Internal url ->
      updateFromUrl model url (Nav.pushUrl model.key (Url.toString url))

    Browser.External href ->
      ( model, Nav.load href )

  UrlChanged url ->
    updateFromUrl model url Cmd.none

  GetPI swissNumber ->
    ( model, getPIfromUrl swissNumber )

  GotPI result ->
    case result of
    Ok pi ->
      case pi /= model.currentPI of
      True ->
        ( { model | currentPI = pi, accordionState = Accordion.initialStateCardOpen pi.swissNumber }, Cmd.none )

      False ->
        ( model, Cmd.none )

    Err _ ->
      ( model, Cmd.none )

  GetTravel swissNumber ->
    ( model, getTravelfromUrl swissNumber )

  GotTravel result ->
    case result of
    Ok travel ->
      case travel /= model.currentTravel of
      True ->
        ( { model | currentTravel = travel, currentView = ViewListPIDashboard }, Cmd.none )

      False ->
        ( { model | currentView = ViewListPIDashboard }, Cmd.none )

    Err _ ->
      ( model, Cmd.none )

  ViewChanged newView ->
    ( { model | currentView = newView }, Cmd.none )

  UpdateNavbar state ->
    ( { model | navbarState = state }, Cmd.none)

  CarouselMsg subMsg ->
    ( { model | carouselState = Carousel.update subMsg model.carouselState }, Cmd.none )

  AccordionMsg state ->
    ( { model | accordionState = state }, Cmd.none )

  CarouselPrev ->
    ( { model | carouselState = Carousel.prev model.carouselState }, Cmd.none )

  CarouselNext ->
    ( { model | carouselState = Carousel.next model.carouselState }, Cmd.none )

  MouseOver overButton ->
    case (List.member overButton model.mouseOver) of
    True ->
      (model, Cmd.none)

    False ->
      ( { model | mouseOver = overButton :: model.mouseOver }, Cmd.none )

  MouseOut overButton ->
    ( { model | mouseOver = List.filter (\n -> n /= overButton) model.mouseOver }, Cmd.none )


-- SUBSCRIPTIONS


subscriptions : Model -> Sub Msg
subscriptions model =
  Sub.batch [
    Navbar.subscriptions model.navbarState UpdateNavbar
    , Carousel.subscriptions (Carousel.pause model.carouselState) CarouselMsg
    , Accordion.subscriptions model.accordionState AccordionMsg
  ]



-- VIEW


view : Model -> Browser.Document Msg
view model =
  { title = "TaxiTakeMeTo"
  , body =
    [ case model.currentView of
      ViewListTravelDashboard ->
        div
          []
          [ viewNavbar model
          , viewListTravelDashboard model model.listTravel
          ]

      ViewListPIDashboard ->
        div
          []
          [ viewNavbar model
          , viewListPIDashboard model model.currentTravel
          ]

      ViewPI ->
        div
          []
          [ viewNavbar model
          , viewPI model.currentPI model.carouselState model.accordionState model.mouseOver
          ]

      SimpleViewPI ->
        simpleViewPI model.currentPI model.carouselState model.mouseOver

      LoadingPage ->
        div
          []
          [ viewNavbar model
          , Loading.view
          ]
    ]
  }


navbarItem : String -> String -> Navbar.Item Msg
navbarItem url content =
  Navbar.itemLink
    [ href url ]
    [ text content ]

viewNavbar : Model -> Html Msg
viewNavbar model =
  Navbar.config UpdateNavbar
    |> Navbar.lightCustom Color.lightGrey
    |> Navbar.withAnimation
    |> Navbar.collapseMedium
    |> Navbar.brand
      [ href "/elm" ]
      [ img
        [ src "https://cdn2.iconfinder.com/data/icons/ios-7-icons/50/user_male2-512.png"
        , class " align-middle d-inline-block rounded align-top img-thumbnails "
        , style "width" "60px"
        ]
        []
      ]
    |> Navbar.items
      [ navbarItem "#" "Item 1"
      , navbarItem "#" "Item 2"
      , navbarItem "#" "Item 3"
      , navbarItem "#" "Item 4"
      ]
    |> Navbar.view model.navbarState


viewTravel : Travel -> Html Msg
viewTravel travel =
  a
    [ href ("/elm/travel#" ++ travel.swissNumber) ]
    [ div
      [ class "p-4 mb-4 rounded card-header"
      , style "border-bottom" "none"
      ]
      [ Grid.row
        [ Row.middleXs ]
        [ Grid.col
          [ Col.xs12, Col.textAlign Text.alignXsCenter ]
          [ h5
            []
            [ text travel.title ]
          ]
        ]
      , Grid.row
        [ Row.middleXs
        , Row.attrs
          [ class "row align-items-center rounded pt-3 pb-3"
          , style "background-color" "rgb(238, 238, 236)"
          ]
        ]
        [ Grid.col
          [ Col.xs6, Col.textAlign Text.alignXsCenter ]
          [ text (travel.startDate.day ++ "/" ++ travel.startDate.month ++ "/" ++ travel.startDate.year) ]
        , Grid.col
          [ Col.xs6, Col.textAlign Text.alignXsCenter ]
          [ text (travel.endDate.day ++ "/" ++ travel.endDate.month ++ "/" ++ travel.endDate.year) ]
        ]
      ]
    ]


viewListTravelDashboard : Model -> List Travel -> Html Msg
viewListTravelDashboard model listTravel =
  div
    []
    [ h2
      [ class "title" ]
      [ text "My Travels" ]
    , Grid.container
      [ class "box-shadow"
      ]
      (List.map viewTravel listTravel)
    ]


viewSimplePILink : PI -> Html Msg
viewSimplePILink pi =
  a
    [ href ("/elm/pi#" ++ pi.swissNumber) ]
    [ Grid.row
      [ Row.middleXs ]
      [ Grid.col
        [ Col.xs12, Col.textAlign Text.alignXsCenter ]
        [ h5
          []
          [ text pi.title ]
        ]
      ]
    , Grid.row
      [ Row.middleXs, Row.attrs [ style "background-color" "#eeeeec", class "rounded" ] ]
      [ Grid.col
        [ Col.sm3 ]
        [ Grid.row
          []
          (List.map PI.viewTypePI pi.typespi)
        ]
      , Grid.col
        [ Col.sm6 ]
        [ Grid.row
          [ Row.attrs [ class "text-center py-3 d-flex justify-content-around" ] ]
          (List.map PI.viewTagPI pi.tags)
        ]
      , Grid.col
        [ Col.sm3 ]
        []
      ]
    ]

piAccordionCard : PI -> Carousel.State -> Accordion.State -> List OverButton -> PI -> Accordion.Card Msg
piAccordionCard currentPI carouselState accordionState mouseOver pi =
  Accordion.card
    { id = pi.swissNumber
    , options = [ Card.attrs [ style "border" "none", style "max-width" "100%" ] ]
    , header =
      Accordion.header [ class "mb-4", style "border-bottom" "none" ] <|
      Accordion.toggle
        [ class "card-button" ]
        [ viewSimplePILink pi ]
    , blocks =
      [ Accordion.block []
        [ Block.text
          []
          [ case pi.swissNumber == currentPI.swissNumber of
            True ->
              viewPI currentPI carouselState accordionState mouseOver

            False ->
              Loading.view
          ]
        ]
      ]
    }

viewListPIDashboard : Model -> Travel -> Html Msg
viewListPIDashboard model travel =
  div
    []
    [ h2
      [ class "title" ]
      [ text travel.title ]
    , Grid.container
      [ class "box-shadow" ]
      [ Accordion.config AccordionMsg
        |> Accordion.onlyOneOpen
        |> Accordion.withAnimation
        |> Accordion.cards
          (List.map (piAccordionCard model.currentPI model.carouselState model.accordionState model.mouseOver) travel.listPI)
        |> Accordion.view model.accordionState
      ]
    , h2
      [ class "title" ]
      [ text "Contact" ]
    , Grid.container
      [ class "box-shadow" ]
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
              [ Button.roleLink ]
              [ img
                [ src "https://www.trzcacak.rs/myfile/full/15-159661_message-icon-png.png"
                , class "little-image"
                ]
                []
              ]
            , Button.button
              [ Button.roleLink ]
              [ img
                [ src "https://png.pngtree.com/svg/20170630/phone_call_1040996.png"
                , class "little-image"
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

viewCarouselButtonPrev : List OverButton -> Html Msg
viewCarouselButtonPrev mouseOver =
  Button.button
    [ Button.roleLink
    , Button.onClick CarouselPrev
    , Button.attrs
      [ class "carousel-button-prev" ]
    ]
    [ span
      [ class "carousel-control-prev-icon"
      , onMouseOver (MouseOver OB.carouselPrevButton)
      , onMouseOut (MouseOut OB.carouselPrevButton)
      , case (List.member OB.carouselPrevButton mouseOver) of
        False ->
          style "opacity" ".5"

        True ->
          style "opacity" ".9"
      ]
      []
    ]

viewCarouselButtonNext : List OverButton -> Html Msg
viewCarouselButtonNext mouseOver =
  Button.button
    [ Button.roleLink
    , Button.onClick CarouselNext
    , Button.attrs
      [ class "carousel-button-next" ]
    ]
    [ span
      [ class "carousel-control-next-icon"
      , onMouseOver (MouseOver OB.carouselNextButton)
      , onMouseOut (MouseOut OB.carouselNextButton)
      , case (List.member CarouselNextButton mouseOver) of
        False ->
          style "opacity" ".5"

        True ->
          style "opacity" ".9"
      ]
      []
    ]

viewCarousel : List Media -> Carousel.State -> List OverButton -> Html Msg
viewCarousel medias carouselState mouseOver =
  div
    []
    [ Carousel.config CarouselMsg []
      |> Carousel.slides
        (List.map Media.carouselSlide medias)
      |> Carousel.view carouselState
    , viewCarouselButtonPrev mouseOver
    , viewCarouselButtonNext mouseOver
    ]


viewPI : PI -> Carousel.State -> Accordion.State -> List OverButton -> Html Msg
viewPI pi carouselState accordionState mouseOver =
  div
    [] <|
    [ Grid.container
      [ class "box-shadow"
      ]
      [ Grid.row
        [ Row.middleXs ]
        [ Grid.col
          [ Col.sm6 ]
          [ case Accordion.isOpen pi.swissNumber accordionState of
            True ->
              viewCarousel pi.medias carouselState mouseOver

            False ->
              Media.viewFirstMedia pi.medias
          ]
        , Grid.col
          [ Col.sm6 ]
          [ h5
            []
            [ text pi.title ]
          , div
            [ class "text-justify" ]
            [ text pi.description ]
          ]
        ]
      , Grid.row
        [ Row.middleXs ]
        [ Grid.col
          [ Col.sm3 ]
          []
        , Grid.col
          [ Col.sm6 ]
          [ Grid.row
            [ Row.attrs [ class "text-center py-3 d-flex justify-content-around" ] ]
            (List.map PI.viewTagPI pi.tags)
          ]
        , Grid.col
          [ Col.sm3 ]
          []
        ]
      , hr
        [ class "pt-4 pb-2" ]
        []
      , h2
        [ class "title" ]
        [ text "Audio language" ]
      , Grid.container
        [ class "p-4" ]
        (List.map Media.viewAudioLanguage pi.audios)
      , hr
        [ class "pt-4 pb-3" ]
        []
      , div
        [ class "text-center" ]
        [ Button.button
          [ Button.large
          , Button.outlineSecondary
          , Button.onClick (ViewChanged SimpleViewPI)
          ]
          [ text "Simple view"
          , img
            [ class "pi-view-button"
            , src "https://upload.wikimedia.org/wikipedia/commons/thumb/e/eb/PICOL_icon_View.svg/1024px-PICOL_icon_View.svg.png"
            ]
            []
          ]
        ]
      ]
    ]


simpleViewPI : PI -> Carousel.State -> List OverButton -> Html Msg
simpleViewPI pi carouselState mouseOver =
  div
    []
    [ h1
      [ class "title" ]
      [ text pi.title ]
    , Grid.container
      [ class "box-shadow" ]
      [ Grid.row
        [ Row.middleXs ]
        [ Grid.col
          [ Col.sm6 ]
          [ viewCarousel pi.medias carouselState mouseOver
          ]
        , Grid.col
          [ Col.sm6 ]
          [ h4
            [ class "title" ]
            [ text pi.address ]
          ]
        ]
      , hr
        [ class "pt-4" ]
        []
      , h2
        [ class "title" ]
        [ text "Audio language" ]
      , Grid.container
        [ class "p-4" ]
        (List.map Media.viewAudioLanguage pi.audios)
      , hr
        [ class "pt-4 pb-3" ]
        []
      , div
        [ class "text-center" ]
        [ Button.button
          [ Button.large
          , Button.outlineSecondary
          , Button.onClick (ViewChanged ViewListPIDashboard)
          ]
          [ text "Exit view"
          , img
            [ class "pi-view-button"
            , src "https://upload.wikimedia.org/wikipedia/commons/thumb/7/7e/Ic_exit_to_app_48px.svg/1024px-Ic_exit_to_app_48px.svg.png"
            ]
            []
          ]
        ]
      ]
    ]


-- JSON API

decodeAudioContent : Decoder Audio
decodeAudioContent =
  D.map5 Audio
  (field "type" string)
  (field "language" string)
  (field "view_facet" string)
  (field "path" string)
  (field "delete" string)


-- getPIfromUrl : String -> Cmd Msg
-- getPIfromUrl ocapUrl =
--   Http.get
--     { url = ocapUrl
--     , expect = Http.expectJson GotPI piDecoder
--     }

-- getTravelfromUrl : SwissNumber -> Cmd Msg
-- getTravelfromUrl ocapUrl =
--   Http.get
--     { url = ocapUrl
--     , except = Http.exceptJson GotTravel travelDecoder
--     }

--- Temporary fakers

getPIfromUrl : String -> Cmd Msg
getPIfromUrl ocapUrl =
  Process.sleep 2000
    |> Task.perform (\_ ->
      GotPI (Ok (Fake.pi ocapUrl))
    )

getTravelfromUrl : SwissNumber -> Cmd Msg
getTravelfromUrl ocapUrl =
  Process.sleep 2000
    |> Task.perform (\_ ->
      GotTravel (Ok (Fake.travel ocapUrl))
    )

