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
import Bootstrap.Form.Input as Input
import Bootstrap.Form.Checkbox as Checkbox
import Bootstrap.Form.InputGroup as InputGroup
import Color
import Process
import Task
import PI exposing (..)
import Fake exposing (..)
import Media exposing (..)
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
  = ViewUserDashboard
  | ViewListTravelDashboard
  | ViewListPIDashboard
  | ViewPI
  | SimpleViewPI
  | LoadingPage
  | ViewSearchPI


type alias Model =
  { key : Nav.Key
  , currentView : CurrentView
  , navbarState : Navbar.State
  , currentTravel : Travel
  , currentPI : PI
  , proposals : List PI
  , listTravel : List Travel
  , carouselState : Carousel.State
  , accordionState : Accordion.State
  , mouseOver : List OverButton
  }


model0 key state =
  { key = key
  , currentView = ViewUserDashboard
  , navbarState = state
  , currentTravel = Travel "" "" [] Accordion.initialState
  , currentPI = PI "" "" "" "" [] [] [] []
  , proposals = []
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
      Accordion.initialState
    , Travel
      "http://localhost:8000/api/obj/voyagebirmanie"
      "Petit voyage en Birmanie"
      []
      Accordion.initialState
    , Travel
      "http://localhost:8000/api/obj/sejourtadjikistan"
      "Séjour au Tadjikistan"
      []
      Accordion.initialState
    ], proposals =
    [ PI
      "http://localhost:8000/api/obj/1"
      "Wat Phra Kaew Temple - Thaïland"
      "This is a description of Meenakshi Amman Temple."
      "9 Boulevard de la Canopée"
      [ Media Media.imageType "https://upload.wikimedia.org/wikipedia/commons/b/b2/Wat_Phra_Sri_Rattana_Satsadaram_11.jpg" ] [] [] []
    , PI
      "http://localhost:8000/api/obj/2"
      "Food Festival - Singapour"
      "It’s no secret that Singaporeans are united in their love for great food."
      "666 Rue de l'Enfer"
      [ Media Media.imageType "https://www.je-papote.com/wp-content/uploads/2016/08/food-festival-singapour.jpg" ] [] [] []
    , PI
      "http://localhost:8000/api/obj/3"
      "Hôtel F1 - Bordeaux"
      "HotelF1 est une marque hôtelière 1 étoile filiale du groupe Accor."
      "Le Paradis (lieu-dit)"
      [ Media Media.imageType "https://www.ahstatic.com/photos/2472_ho_00_p_1024x768.jpg" ] [] [] []
    ], currentTravel =
      Travel
        "http://localhost:8000/api/obj/parisdakar"
        "Paris - Dakar"
        [ PI
          "http://localhost:8000/api/obj/1"
          "Wat Phra Kaew Temple - Thaïland"
          "This is a description of Meenakshi Amman Temple."
          "9 Boulevard de la Canopée"
          [ Media Media.imageType "https://upload.wikimedia.org/wikipedia/commons/b/b2/Wat_Phra_Sri_Rattana_Satsadaram_11.jpg"
          , Media Media.imageType "https://bangkokmonamour.files.wordpress.com/2015/09/vue-generale-temple.jpg"
          , Media Media.imageType "https://upload.wikimedia.org/wikipedia/commons/c/c1/Wat_Phra_Kaew_by_Ninara_TSP_edit_crop.jpg"
          ]
          [ Audio "" "Thaï" "" "http://localhost:8000/storage/converts/DX9ytBq8luIwmUcu6fiN2g==.mp3" ""
          , Audio "" "English" "" "http://localhost:8000/storage/converts/DX9ytBq8luIwmUcu6fiN2g==.mp3" ""
          , Audio "" "French" "" "http://localhost:8000/storage/converts/DX9ytBq8luIwmUcu6fiN2g==.mp3" ""
          ]
          [ PI.free, PI.reserved ]
          [ PI.touristicPlace ]
        ]
        Accordion.initialState
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
  | TravelAccordionMsg Accordion.State
  | CarouselPrev
  | CarouselNext
  | MouseOver OverButton
  | MouseOut OverButton


type Route
  = RouteHome
  | RouteSearch
  | RoutePI (Maybe String)
  | RouteTravel (Maybe String)

router : P.Parser (Route -> a) a
router =
  P.oneOf
  [ P.map RouteHome <| P.s "elm"
  , P.map RouteSearch <| P.s "elm" </> P.s "search"
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
    RouteHome ->
      ( { model | currentView = ViewUserDashboard }, commonCmd )

    RouteSearch ->
      ( { model | currentView = ViewSearchPI }, commonCmd )

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
        ( model
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
        ( { model |
          currentPI = pi,
          currentTravel = Travel.updateAccordionState (Accordion.initialStateCardOpen pi.swissNumber) model.currentTravel
          }
        , Cmd.none )

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
        ( { model | currentTravel = travel }, Cmd.none )

      False ->
        ( model, Cmd.none )

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

  TravelAccordionMsg state ->
    ( { model | currentTravel = (Travel.updateAccordionState state model.currentTravel) }, Cmd.none )

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
    , Accordion.subscriptions model.currentTravel.accordionState TravelAccordionMsg
  ]



-- VIEW


view : Model -> Browser.Document Msg
view model =
  { title = "TaxiTakeMeTo"
  , body =
    [ case model.currentView of
      ViewUserDashboard ->
        div
          []
          [ viewNavbar model
          , viewUserDashboard model
          ]

      ViewListTravelDashboard ->
        div
          []
          [ viewNavbar model
          , h2
            [ class "title" ]
            [ text "My Travels" ]
          , viewListTravelDashboard model.listTravel
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

      ViewSearchPI ->
        div
          []
          [ viewNavbar model
          , viewSearchPI model.proposals
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
        , class "navbar-icon"
        ]
        []
      ]
    |> Navbar.items
      [ navbarItem "/elm/search" "Search PI"
      , navbarItem "#" "Item 2"
      , navbarItem "#" "Item 3"
      , navbarItem "#" "Item 4"
      ]
    |> Navbar.view model.navbarState


viewBlockTravel : Travel -> Block.Item Msg
viewBlockTravel travel =
  Block.text
    []
    [ viewTravel travel ]

viewUserDashboardAccordionToggle : Accordion.State -> Html Msg
viewUserDashboardAccordionToggle accordionState =
  Grid.row
    []
    [ Grid.col
      [ Col.xs8 ]
      [ h5
        [ class "text-left" ]
        [ text "Other Travels" ]
      ]
    , Grid.col
      [ Col.xs4, Col.textAlign Text.alignXsRight ]
      [ case Accordion.isOpen "card1" accordionState of
        True ->
          img
            [ src "https://image.noelshack.com/fichiers/2019/47/1/1574075725-arrow-up.png"
            , style "max-width" "20px"
            ]
            [ text "/\\" ]

        False ->
          img
            [ src "https://image.noelshack.com/fichiers/2019/47/1/1574075721-arrow-down.png"
            , style "max-width" "20px"
            ]
            [ text "\\/" ]
      ]
    ]

viewUserDashboardAccordion : Model -> Accordion.Card Msg
viewUserDashboardAccordion model =
  Accordion.card
    { id = "card1"
    , options = [ Card.attrs [ class "card-option" ] ]
    , header =
      Accordion.header [ class "accordion-header" ] <|
      Accordion.toggle
        [ style "text-decoration" "none", style "width" "100%" ]
        [ viewUserDashboardAccordionToggle model.accordionState ]
    , blocks =
      [ Accordion.block []
        (List.map viewBlockTravel model.listTravel)
      ]
    }

viewUserDashboard : Model -> Html Msg
viewUserDashboard model =
  Grid.container
    []
    [ Grid.row
      []
      [ Grid.col
        [ Col.xs12 ]
        [ h2
          [ class "title" ]
          [ text "Current Travel" ]
        , h3
          [ class "text-center" ]
          [ text model.currentTravel.title ]
        , viewListPIDashboard model model.currentTravel
        ]
      , Grid.col
        [ Col.xs12 ]
        [ hr
          []
          []
        ]
      , Grid.col
        [ Col.xs12 ]
        [ accordionView
          AccordionMsg
          model.accordionState
          [ viewUserDashboardAccordion model ]
        ]
      ]
    ]


viewTravel : Travel -> Html Msg
viewTravel travel =
  a
    [ href ("/elm/travel#" ++ travel.swissNumber) ]
    [ div
      [ class "travel-header" ]
      [ Grid.row
        [ Row.middleXs ]
        [ Grid.col
          [ Col.xs12, Col.textAlign Text.alignXsCenter ]
          [ h5
            []
            [ text travel.title ]
          ]
        ]
      ]
    ]

viewListTravelDashboard : List Travel -> Html Msg
viewListTravelDashboard listTravel =
  Grid.container
    []
    (List.map viewTravel listTravel)



viewSearchBar : Html Msg
viewSearchBar =
  Grid.row
    [ Row.attrs [ class "pt-4 pb-4" ] ]
    [ Grid.col
      [ Col.lg12 ]
      [ InputGroup.config
          ( InputGroup.text [ Input.placeholder "Search PI" ] )
          |> InputGroup.predecessors
            [ InputGroup.span
              []
              [ img
                [ src "https://upload.wikimedia.org/wikipedia/commons/thumb/5/55/Magnifying_glass_icon.svg/490px-Magnifying_glass_icon.svg.png"
                , style "max-width" "20px"
                ]
                []
              ]
            ]
          |> InputGroup.successors
              [ InputGroup.button
                [ Button.secondary ]
                [ text "Search" ]
              ]
          |> InputGroup.view
      ]
    ]

viewProposal : PI -> Html Msg
viewProposal proposal =
  label
    [ class "row pb-2"
    , id "checkout"
    ]
    [ div
      [ class "col-12 text-center" ]
      [ hr
        []
        []
      ]
    , div
      [ class "col-md-2 col-4 text-center" ]
      [ Media.viewFirstMedia [ style "max-width" "150px", class "rounded" ] proposal.medias ]
    , div
      [ class "col-md-8 col-6 text-left" ]
      [ h4
        [ class "resize-text" ]
        [ text proposal.title ]
      , text proposal.address
      ]
    , div
      [ class "col-2 text-center" ]
      [ Checkbox.checkbox [ Checkbox.id "checkout" ] "" ]
      ]

viewSearchAddToList : String -> Html Msg
viewSearchAddToList str =
  Grid.row
    [ Row.attrs [ class "pt-2 pb-2 lightgrey-background mb-2" ] ]
    [ Grid.col
      [ Col.xs8, Col.textAlign Text.alignXsLeft ]
      [ h4
        []
        [ text str ]
      ]
    , Grid.col
      [ Col.xs4, Col.textAlign Text.alignXsRight ]
      [ Button.button
        [ Button.success ]
        [ text "+" ]
      ]
    ]

viewSearchPI : List PI -> Html Msg
viewSearchPI listProposal =
  Grid.container
    [ class "mt-4" ]
    [ viewSearchBar
    , Grid.row
      []
      [ Grid.col
        [ Col.xs12, Col.textAlign Text.alignXsCenter ]
        [ img
          [ src "https://image.noelshack.com/fichiers/2019/46/4/1573745503-capture-d-ecran-du-2019-11-14-16-29-54.png"
          , class "streamer-image"
          ]
          []
        ]
      ]
    , div
      [ class "proposals mb-4" ]
      [ div
      []
      (List.map viewProposal listProposal)
    , viewSearchAddToList "Add to 'Paris - Dakar' travel"
    , viewSearchAddToList "Create a new travel"
    ]
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
          [ Row.attrs [ class "pi-tags" ] ]
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
    , options = [ Card.attrs [ class "card-option" ] ]
    , header =
      Accordion.header [ class "accordion-header" ] <|
      Accordion.toggle
        [ class "card-button" ]
        [ viewSimplePILink pi ]
    , blocks =
      [ Accordion.block [ Block.attrs [ class "test" ] ]
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

accordionView : (Accordion.State -> Msg) -> Accordion.State -> List (Accordion.Card Msg) -> Html Msg
accordionView msg state cards =
  Accordion.config msg
    |> Accordion.onlyOneOpen
    |> Accordion.withAnimation
    |> Accordion.cards
      cards
    |> Accordion.view state

viewListPIDashboard : Model -> Travel -> Html Msg
viewListPIDashboard model travel =
  div
    []
    [ Grid.container
      []
      [ accordionView
        TravelAccordionMsg
        travel.accordionState
        (List.map (piAccordionCard model.currentPI model.carouselState travel.accordionState model.mouseOver) travel.listPI)
      ]
    , Grid.container
      [ class "mb-4" ]
      [ h3
        [ class "text-center" ]
        [ text "Contact" ]
      , Grid.row
        [ Row.middleXs ]
        [ Grid.col
          [ Col.xs12 ]
          [ div
            [ class "pi-tags lightgrey-background" ]
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
    []
    [ Grid.container
      [ class "pt-3" ]
      [ Grid.row
        [ Row.middleXs ]
        [ Grid.col
          [ Col.sm6 ]
          [ case Accordion.isOpen pi.swissNumber accordionState of
            True ->
              viewCarousel pi.medias carouselState mouseOver

            False ->
              Media.viewFirstMedia [] pi.medias
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
            [ Row.attrs [ class "pi-tags" ] ]
            (List.map PI.viewTagPI pi.tags)
          ]
        , Grid.col
          [ Col.sm3 ]
          []
        ]
      , hr
        []
        []
      , h2
        [ class "title" ]
        [ text "Audio language" ]
      , Grid.container
        [ class "p-4" ]
        (List.map Media.viewAudioLanguage pi.audios)
      , hr
        [ class "pt-2" ]
        []
      , div
        [ class "text-center mb-4" ]
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
    [ Grid.container
      [ class "pt-3" ]
      [ Grid.row
        [ Row.middleXs ]
        [ Grid.col
          [ Col.sm6 ]
          [ viewCarousel pi.medias carouselState mouseOver
          ]
        , Grid.col
          [ Col.sm6 ]
          [ h1
            [ class "title" ]
            [ text pi.title ]
          , h4
            [ class "title" ]
            [ text pi.address ]
          ]
        ]
      , hr
        []
        []
      , h2
        [ class "title" ]
        [ text "Audio language" ]
      , Grid.container
        [ class "p-4" ]
        (List.map Media.viewAudioLanguage pi.audios)
      , hr
        [ class "pt-2" ]
        []
      , div
        [ class "text-center mb-4" ]
        [ Button.button
          [ Button.large
          , Button.outlineSecondary
          , Button.onClick (ViewChanged ViewUserDashboard)
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

