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
import Loading exposing ( LoaderType(..), defaultConfig, render )
import Color
import Process
import Task
import PI exposing (..)
import Media exposing (..)
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


type alias Travel =
  { swissNumber : SwissNumber
  , title : String
  , listPI : List PI
  }


type alias Model =
  { key : Nav.Key
  , currentView : CurrentView
  , navbarState : Navbar.State
  , currentTravel : Travel
  , currentPI : PI
  , listTravel : List Travel
  , modalVisibility : Modal.Visibility
  , carouselState : Carousel.State
  , accordionState : Accordion.State
  , mouseOver : List OverButton
  }


model0 key state =
  { key = key
  , currentView = ViewListTravelDashboard
  , navbarState = state
  , currentTravel = Travel "" "" []
  , currentPI = PI "" "" "" "" [] [] [] []
  , listTravel = []
  , modalVisibility = Modal.hidden
  , carouselState = Carousel.initialState
  , accordionState = Accordion.initialState
  , mouseOver = []
  }

fakeModel0 key state =
  let model = model0 key state
  in { model | listTravel =
    [ Travel
        "http://localhost:8000/api/obj/unvoyage"
        "Paris - Dakar - Namek"
        [ PI "http://localhost:8000/api/obj/1" "Meenakshi Amman Temple - India" "This is a description of Meenakshi Amman Temple." "9 Boulevard de la Canopée"
          [ Media Media.videoType "http://localhost:8000/storage/converts/GpCcKj6Rb9@V_eoeFO4oIQ==.mp4"
          , Media Media.imageType "https://static.nationalgeographic.fr/files/meenakshi-amman-temple-india.jpg"
          , Media Media.imageType "https://upload.wikimedia.org/wikipedia/commons/7/7c/Temple_de_M%C3%AEn%C3%A2ksh%C3%AE01.jpg"
          , Media Media.imageType "https://www.ancient-origins.net/sites/default/files/field/image/Meenakshi-Amman-Temple.jpg" ]
          [ Audio "" "Hindi" "" "http://localhost:8000/storage/converts/yrXFohm5kSzWqgE2d14LCg==.mp3" "" ]
          [ PI.free, PI.reserved ]
          [ PI.touristicPlace ]
        , PI "http://localhost:8000/api/obj/2" "Food Festival - Singapour" "It’s no secret that Singaporeans are united in their love for great food. And nowhere is this more evident than at the annual Singapore Food Festival (SFF), which celebrated its 26th anniversary in 2019. Every year, foodies have savoured wonderful delicacies, created by the city-state’s brightest culinary talents in a true feast for the senses." "666 rue de l'Enfer"
          [ Media Media.imageType "https://www.je-papote.com/wp-content/uploads/2016/08/food-festival-singapour.jpg"
          , Media Media.imageType "https://www.holidify.com/images/cmsuploads/compressed/Festival-Village-at-the-Singapore-Night-Festival.-Photo-courtesy-of-Singapore-Night-Festival-2016-2_20180730124945.jpg"
          , Media Media.imageType "https://d3ba08y2c5j5cf.cloudfront.net/wp-content/uploads/2017/07/11161819/iStock-545286388-copy-smaller-1920x1317.jpg" ]
          [ Audio "" "Chinese" "" "http://localhost:8000/storage/converts/e2HMlOMqsJzfzNSVSkGiJQ==.mp3" "" ]
          [ PI.paying ]
          [ PI.restaurant, PI.touristicPlace ]
        ]
    ], currentTravel =
      Travel
        "http://localhost:8000/api/obj/unvoyage"
        "Paris - Dakar - Namek"
        [ PI "http://localhost:8000/api/obj/2" "Food Festival - Singapour" "It’s no secret that Singaporeans are united in their love for great food. And nowhere is this more evident than at the annual Singapore Food Festival (SFF), which celebrated its 26th anniversary in 2019. Every year, foodies have savoured wonderful delicacies, created by the city-state’s brightest culinary talents in a true feast for the senses." "666 rue de l'Enfer"
          [ Media Media.imageType "https://www.je-papote.com/wp-content/uploads/2016/08/food-festival-singapour.jpg"
          , Media Media.imageType "https://www.holidify.com/images/cmsuploads/compressed/Festival-Village-at-the-Singapore-Night-Festival.-Photo-courtesy-of-Singapore-Night-Festival-2016-2_20180730124945.jpg"
          , Media Media.imageType "https://d3ba08y2c5j5cf.cloudfront.net/wp-content/uploads/2017/07/11161819/iStock-545286388-copy-smaller-1920x1317.jpg" ]
          [ Audio "" "Chinese" "" "http://localhost:8000/storage/converts/e2HMlOMqsJzfzNSVSkGiJQ==.mp3" "" ]
          [ PI.paying ]
          [ PI.restaurant, PI.touristicPlace ]
        , PI "http://localhost:8000/api/obj/3" "Hôtel F1 - Bordeaux" "HotelF1 est une marque hôtelière 1 étoile filiale du groupe Accor. Souvent proche des axes de transport, hotelF1 propose une offre hôtelière super-économique et diversifiée, et axe son expérience autour du concept. Fin décembre 2018, hotelF1 compte 172 hôtels en France. The best hotel i have ever seen in my whole life." "Le Paradis (lieu-dit)"
            [ Media Media.imageType "https://www.ahstatic.com/photos/2472_ho_00_p_1024x768.jpg"
            , Media Media.imageType "https://www.ahstatic.com/photos/2551_ho_00_p_1024x768.jpg"
            , Media Media.imageType "https://q-cf.bstatic.com/images/hotel/max1024x768/161/161139975.jpg" ]
            [ Audio "" "English" "" "http://localhost:8000/storage/converts/@r4pNRIQkBKk4Jn7H_nvlg==.mp3" "" ]
            [ PI.paying, PI.notReserved, PI.onGoing, PI.free ]
            [ PI.hotel, PI.shop, PI.touristicPlace, PI.restaurant ]
          , PI "http://localhost:8000/api/obj/4" "Souk Rabais Bazar - Marrakech" " السوق التقليدي أو السوقة،[1] منطقة بيع وشراء في المدن العربية التقليدية. إن كافة المدن في أسواق والمدن الكبيرة منها فيها أكثر من سوق. معظم الأسواق دائمة ومفتوحة يوميا إلا أن بعض الأسواق موسمية" "Rue du Marchand"
            [ Media Media.imageType "https://cdn.pixabay.com/photo/2016/08/28/22/22/souk-1627045_960_720.jpg"
            , Media Media.imageType "https://visitmarrakech.ma/wp-content/uploads/2018/02/Souks_Marrakech_Maroc.jpg"
            , Media Media.imageType "https://decorationorientale.com/wp-content/uploads/2018/05/Marrakech-Souk.jpg" ]
            [ Audio "" "Arabian" "" "http://localhost:8000/storage/converts/m03@H3yVB@tuuJyt7FZKyg==.mp3" "" ]
            [ PI.onGoing, PI.free, PI.notReserved ]
            [ PI.shop, PI.touristicPlace, PI.restaurant ]
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
  | CloseModal
  | ShowModal
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
        ( { model | currentTravel = travel, accordionState = Accordion.initialStateCardOpen travel.swissNumber }, Cmd.none )

      False ->
        ( model, Cmd.none )

    Err _ ->
      ( model, Cmd.none )

  ViewChanged newView ->
    ( { model | currentView = newView }, Cmd.none )

  UpdateNavbar state ->
    ( { model | navbarState = state }, Cmd.none)

  CloseModal ->
    ( { model | modalVisibility = Modal.hidden } , Cmd.none )

  ShowModal ->
    ( { model | modalVisibility = Modal.shown } , Cmd.none )

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
          , viewPI model.currentPI model.modalVisibility model.carouselState model.accordionState model.mouseOver
          ]

      SimpleViewPI ->
        simpleViewPI model.currentPI model.carouselState model.mouseOver

      LoadingPage ->
        Loading.view
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
        , style "width" "60px" ]
        []
      ]
    |> Navbar.items
      [ navbarItem "#" "Item 1"
      , navbarItem "#" "Item 2"
      , navbarItem "#" "Item 3"
      , navbarItem "#" "Item 4"
      ]
    |> Navbar.view model.navbarState


viewModal : Modal.Visibility -> Carousel.State -> List Media -> Html Msg
viewModal modalVisibility carouselState medias =
  div []
    [ Modal.config CloseModal
      |> Modal.large
      |> Modal.hideOnBackdropClick True
      |> Modal.body
        []
        [ Carousel.config CarouselMsg []
          |> Carousel.withControls
          |> Carousel.withIndicators
          |> Carousel.slides
            (List.map Media.carouselSlide medias)
          |> Carousel.view carouselState
        ]
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


viewSimpleTravelLink : Travel -> Html Msg
viewSimpleTravelLink travel =
  a
    [ href ("/elm/travel#" ++ travel.swissNumber)
    , style "text-decoration" "none"
    , style "outline" "none"
    ]
    [ Grid.row
      [ Row.middleXs ]
      [ Grid.col
        [ Col.xs12, Col.textAlign Text.alignXsCenter ]
        [ h5
            [ style "overflow-wrap" "break-word" ]
            [ text travel.title ]
        ]
      ]
    ]

travelAccordionCard : Model -> Travel -> Accordion.Card Msg
travelAccordionCard model travel =
  Accordion.card
    { id = travel.swissNumber
    , options = [ Card.attrs [ style "border" "none", style "max-width" "100%" ] ]
    , header =
      Accordion.header [ class "mb-4", style "border-bottom" "none" ] <|
      Accordion.toggle
        [ class "btn-block"
        , style "text-decoration" "none"
        , style "white-space" "normal"
        ]
        [ viewSimpleTravelLink travel ]
    , blocks =
      [ Accordion.block []
        [ Block.text
          []
          [ case travel.swissNumber == model.currentTravel.swissNumber of
            True ->
              viewListPIDashboard model model.currentTravel

            False ->
              Loading.view
          ]
        ]
      ]
    }

viewListTravelDashboard : Model -> List Travel -> Html Msg
viewListTravelDashboard model listTravel =
  div
    []
    [ h2
      [ class "text-center pt-4" ]
      [ text "My Travels" ]
    , Button.button
      [ Button.primary
      , Button.onClick (ViewChanged ViewListPIDashboard)
      ]
      [ text "Go to ListPIDashboard" ]
    , Grid.container
      [ class "p-4 mb-4 rounded"
      , style "box-shadow" "0px 0px 50px 1px lightgray" ]
      [ Accordion.config AccordionMsg
        |> Accordion.onlyOneOpen
        |> Accordion.withAnimation
        |> Accordion.cards
          (List.map (travelAccordionCard model) listTravel)
        |> Accordion.view model.accordionState
      ]
    ]


viewSimplePILink : PI -> Html Msg
viewSimplePILink pi =
  a
    [ href ("/elm/pi#" ++ pi.swissNumber)
    , style "text-decoration" "none"
    , style "outline" "none"
    ]
    [ Grid.row
      [ Row.middleXs ]
      [ Grid.col
        [ Col.xs12, Col.textAlign Text.alignXsCenter ]
        [ h5
            [ style "overflow-wrap" "break-word" ]
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

piAccordionCard : Model -> PI -> Accordion.Card Msg
piAccordionCard model pi =
  Accordion.card
    { id = pi.swissNumber
    , options = [ Card.attrs [ style "border" "none", style "max-width" "100%" ] ]
    , header =
      Accordion.header [ class "mb-4", style "border-bottom" "none" ] <|
      Accordion.toggle
        [ class "btn-block"
        , style "text-decoration" "none"
        , style "white-space" "normal"
        ]
        [ viewSimplePILink pi ]
    , blocks =
      [ Accordion.block []
        [ Block.text
          []
          [ case pi.swissNumber == model.currentPI.swissNumber of
            True ->
              viewPI model.currentPI model.modalVisibility model.carouselState model.accordionState model.mouseOver

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
      [ class "text-center pt-4" ]
      [ text travel.title ]
    , Grid.container
      [ class "p-4 mb-4 rounded"
      , style "box-shadow" "0px 0px 50px 1px lightgray" ]
      [ Accordion.config AccordionMsg
        |> Accordion.onlyOneOpen
        |> Accordion.withAnimation
        |> Accordion.cards
          (List.map (piAccordionCard model) travel.listPI)
        |> Accordion.view model.accordionState
      ]
    , h2
      [ class "text-center pt-4" ]
      [ text "Contact" ]
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
              [ Button.roleLink ]
              [ img
                [ src "https://www.trzcacak.rs/myfile/full/15-159661_message-icon-png.png"
                , style "max-width" "70px"
                , class "img-fluid"
                ]
                []
              ]
            , Button.button
              [ Button.roleLink ]
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

viewCarouselButtonPrev : List OverButton -> Html Msg
viewCarouselButtonPrev mouseOver =
  Button.button
    [ Button.roleLink
    , Button.onClick CarouselPrev
    , Button.attrs
      [ style "position" "absolute"
      , style "top" "30%"
      , style "bottom" "30%"
      , style "left" "0"
      , style "height" "40%"
      ]
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
      [ style "position" "absolute"
      , style "top" "30%"
      , style "bottom" "30%"
      , style "right" "0"
      , style "height" "40%"
      ]
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


viewPI : PI -> Modal.Visibility -> Carousel.State -> Accordion.State -> List OverButton -> Html Msg
viewPI pi modalVisibility carouselState accordionState mouseOver =
  div
    [] <|
    [ Grid.container
      [ class "p-4 mb-4 rounded"
      , style "box-shadow" "0px 0px 50px 1px lightgray"
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
        [ class "text-center" ]
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
            [ class "mx-auto img-fluid pl-3 rounded"
            , style "width" "60px"
            , src "https://upload.wikimedia.org/wikipedia/commons/thumb/e/eb/PICOL_icon_View.svg/1024px-PICOL_icon_View.svg.png"
            ]
            []
          ]
        ]
      ]
    ]
    ++
    if Accordion.isOpen pi.swissNumber accordionState then
      [ viewModal modalVisibility carouselState pi.medias ]
    else
      []

simpleViewPI : PI -> Carousel.State -> List OverButton -> Html Msg
simpleViewPI pi carouselState mouseOver =
  div
    []
    [ h1
      [ class "text-center pt-4" ]
      [ text pi.title ]
    , Grid.container
      [ class "p-4 mb-4 rounded"
      , style "box-shadow" "0px 0px 50px 1px lightgray" ]
      [ Grid.row
        [ Row.middleXs ]
        [ Grid.col
          [ Col.sm6 ]
          [ viewCarousel pi.medias carouselState mouseOver
          ]
        , Grid.col
          [ Col.sm6 ]
          [ h4
            [ class "text-center " ]
            [ text pi.address ]
          ]
        ]
      , hr
        [ class "pt-4" ]
        []
      , h2
        [ class "text-center" ]
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
            [ class "mx-auto img-fluid pl-3 rounded"
            , style "width" "60px"
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
      GotPI (Ok (fakePI ocapUrl))
    )

getTravelfromUrl : SwissNumber -> Cmd Msg
getTravelfromUrl ocapUrl =
  Process.sleep 2000
    |> Task.perform (\_ ->
      GotTravel (Ok (fakeTravel ocapUrl))
    )

fakePI : SwissNumber -> PI
fakePI ocapUrl =
  case ocapUrl of
  "http://localhost:8000/api/obj/1" ->
    { swissNumber = "http://localhost:8000/api/obj/1"
    , title = "Wat Phra Kaew Temple - Thaïland"
    , description = ". I love it."
    , address = "9 Boulevard de la Canopée"
    , medias = [ Media Media.imageType "https://upload.wikimedia.org/wikipedia/commons/b/b2/Wat_Phra_Sri_Rattana_Satsadaram_11.jpg"
      , Media Media.imageType "https://bangkokmonamour.files.wordpress.com/2015/09/vue-generale-temple.jpg"
      , Media Media.imageType "https://upload.wikimedia.org/wikipedia/commons/c/c1/Wat_Phra_Kaew_by_Ninara_TSP_edit_crop.jpg" ]
    , audios = [ Audio "" "Thaï" "" "http://localhost:8000/storage/converts/DX9ytBq8luIwmUcu6fiN2g==.mp3" ""
      , Audio "" "English" "" "http://localhost:8000/storage/converts/DX9ytBq8luIwmUcu6fiN2g==.mp3" ""
      , Audio "" "French" "" "http://localhost:8000/storage/converts/DX9ytBq8luIwmUcu6fiN2g==.mp3" "" ]
    , tags = [ PI.free ]
    , typespi = [ PI.touristicPlace ]
    }

  "http://localhost:8000/api/obj/2" ->
    { swissNumber = "http://localhost:8000/api/obj/2"
    , title = "Food Festival - Singapour"
    , description = "It’s no secret that Singaporeans are united in their love for great food. And nowhere is this more evident than at the annual Singapore Food Festival (SFF), which celebrated its 26th anniversary in 2019. Every year, foodies have savoured wonderful delicacies, created by the city-state’s brightest culinary talents in a true feast for the senses."
    , address = "666 Rue de l'Enfer"
    , medias = [ Media Media.imageType "https://www.je-papote.com/wp-content/uploads/2016/08/food-festival-singapour.jpg"
      , Media Media.imageType "https://www.holidify.com/images/cmsuploads/compressed/Festival-Village-at-the-Singapore-Night-Festival.-Photo-courtesy-of-Singapore-Night-Festival-2016-2_20180730124945.jpg"
      , Media Media.imageType "https://d3ba08y2c5j5cf.cloudfront.net/wp-content/uploads/2017/07/11161819/iStock-545286388-copy-smaller-1920x1317.jpg" ]
    , audios = [ Audio "" "Chinese" "" "http://localhost:8000/storage/converts/DX9ytBq8luIwmUcu6fiN2g==.mp3" ""
      , Audio "" "English" "" "http://localhost:8000/storage/converts/DX9ytBq8luIwmUcu6fiN2g==.mp3" ""
      , Audio "" "French" "" "http://localhost:8000/storage/converts/DX9ytBq8luIwmUcu6fiN2g==.mp3" "" ]
    , tags = [ PI.paying ]
    , typespi = [ PI.restaurant, PI.touristicPlace ]
    }

  "http://localhost:8000/api/obj/3" ->
    { swissNumber = "http://localhost:8000/api/obj/3"
    , title = "Hôtel F1 - Bordeaux"
    , description = "HotelF1 est une marque hôtelière 1 étoile filiale du groupe Accor. Souvent proche des axes de transport, hotelF1 propose une offre hôtelière super-économique et diversifiée, et axe son expérience autour du concept. Fin décembre 2018, hotelF1 compte 172 hôtels en France. The best hotel i have ever seen in my whole life."
    , address = "Le Paradis (lieu-dit)"
    , medias = [ Media Media.imageType "https://www.ahstatic.com/photos/2472_ho_00_p_1024x768.jpg"
      , Media Media.imageType "https://www.ahstatic.com/photos/2551_ho_00_p_1024x768.jpg"
      , Media Media.imageType "https://q-cf.bstatic.com/images/hotel/max1024x768/161/161139975.jpg" ]
    , audios = [ Audio "" "French" "" "http://localhost:8000/storage/converts/@r4pNRIQkBKk4Jn7H_nvlg==.mp3" ""
      , Audio "" "English" "" "http://localhost:8000/storage/converts/DX9ytBq8luIwmUcu6fiN2g==.mp3" "" ]
    , tags = [ PI.paying, PI.notReserved ]
    , typespi = [ PI.hotel ]
    }

  "http://localhost:8000/api/obj/4" ->
    { swissNumber = "http://localhost:8000/api/obj/4"
    , title = "Souk Rabais Bazar - Marrakech"
    , description = " لسوق التقليدي أو السوقة،[1] منطقة بيع وشراء في المدن العربية التقليدية. إن كافة المدن في أسواق والمدن الكبيرة منها فيها أكثر من سوق. معظم الأسواق دائمة ومفتوحة يوميا إلا أن بعض الأسواق موسمية"
    , address = "Rue du Marchand"
    , medias = [ Media Media.imageType "https://cdn.pixabay.com/photo/2016/08/28/22/22/souk-1627045_960_720.jpg"
      , Media Media.imageType "https://visitmarrakech.ma/wp-content/uploads/2018/02/Souks_Marrakech_Maroc.jpg"
      , Media Media.imageType "https://decorationorientale.com/wp-content/uploads/2018/05/Marrakech-Souk.jpg" ]
    , audios = [ Audio "" "Arabian" "" "http://localhost:8000/storage/converts/DX9ytBq8luIwmUcu6fiN2g==.mp3" ""
      , Audio "" "French" "" "http://localhost:8000/storage/converts/DX9ytBq8luIwmUcu6fiN2g==.mp3" ""
      , Audio "" "English" "" "http://localhost:8000/storage/converts/DX9ytBq8luIwmUcu6fiN2g==.mp3" "" ]
    , tags = [ PI.onGoing ]
    , typespi = [ PI.shop, PI.touristicPlace, PI.restaurant ]
    }

  _ ->
    { swissNumber = ""
    , title = ""
    , description = ""
    , address = ""
    , medias = []
    , audios = []
    , tags = []
    , typespi = []
    }


fakeTravel : SwissNumber -> Travel
fakeTravel ocapUrl =
  case ocapUrl of
  "http://localhost:8000/api/obj/unvoyage" ->
    { swissNumber = "http://localhost:8000/api/obj/unvoyage"
    , title = "Paris - Dakar - Namek"
    , listPI = [
        PI "http://localhost:8000/api/obj/1" "Meenakshi Amman Temple - India" "This is a description of Meenakshi Amman Temple." "9 Boulevard de la Canopée"
          [ Media Media.videoType "http://localhost:8000/storage/converts/GpCcKj6Rb9@V_eoeFO4oIQ==.mp4"
          , Media Media.imageType "https://static.nationalgeographic.fr/files/meenakshi-amman-temple-india.jpg"
          , Media Media.imageType "https://upload.wikimedia.org/wikipedia/commons/7/7c/Temple_de_M%C3%AEn%C3%A2ksh%C3%AE01.jpg"
          , Media Media.imageType "https://www.ancient-origins.net/sites/default/files/field/image/Meenakshi-Amman-Temple.jpg" ]
          [ Audio "" "Hindi" "" "http://localhost:8000/storage/converts/yrXFohm5kSzWqgE2d14LCg==.mp3" "" ]
          [ PI.free, PI.reserved ]
          [ PI.touristicPlace ]
        , PI "http://localhost:8000/api/obj/2" "Food Festival - Singapour" "It’s no secret that Singaporeans are united in their love for great food. And nowhere is this more evident than at the annual Singapore Food Festival (SFF), which celebrated its 26th anniversary in 2019. Every year, foodies have savoured wonderful delicacies, created by the city-state’s brightest culinary talents in a true feast for the senses." "666 rue de l'Enfer"
          [ Media Media.imageType "https://www.je-papote.com/wp-content/uploads/2016/08/food-festival-singapour.jpg"
          , Media Media.imageType "https://www.holidify.com/images/cmsuploads/compressed/Festival-Village-at-the-Singapore-Night-Festival.-Photo-courtesy-of-Singapore-Night-Festival-2016-2_20180730124945.jpg"
          , Media Media.imageType "https://d3ba08y2c5j5cf.cloudfront.net/wp-content/uploads/2017/07/11161819/iStock-545286388-copy-smaller-1920x1317.jpg" ]
          [ Audio "" "Chinese" "" "http://localhost:8000/storage/converts/e2HMlOMqsJzfzNSVSkGiJQ==.mp3" "" ]
          [ PI.paying ]
          [ PI.restaurant, PI.touristicPlace ]
        , PI "http://localhost:8000/api/obj/3" "Hôtel F1 - Bordeaux" "HotelF1 est une marque hôtelière 1 étoile filiale du groupe Accor. Souvent proche des axes de transport, hotelF1 propose une offre hôtelière super-économique et diversifiée, et axe son expérience autour du concept. Fin décembre 2018, hotelF1 compte 172 hôtels en France. The best hotel i have ever seen in my whole life." "Le Paradis (lieu-dit)"
          [ Media Media.imageType "https://www.ahstatic.com/photos/2472_ho_00_p_1024x768.jpg"
          , Media Media.imageType "https://www.ahstatic.com/photos/2551_ho_00_p_1024x768.jpg"
          , Media Media.imageType "https://q-cf.bstatic.com/images/hotel/max1024x768/161/161139975.jpg" ]
          [ Audio "" "English" "" "http://localhost:8000/storage/converts/@r4pNRIQkBKk4Jn7H_nvlg==.mp3" "" ]
          [ PI.paying, PI.notReserved, PI.onGoing, PI.free ]
          [ PI.hotel, PI.shop, PI.touristicPlace, PI.restaurant ]
        , PI "http://localhost:8000/api/obj/4" "Souk Rabais Bazar - Marrakech" " السوق التقليدي أو السوقة،[1] منطقة بيع وشراء في المدن العربية التقليدية. إن كافة المدن في أسواق والمدن الكبيرة منها فيها أكثر من سوق. معظم الأسواق دائمة ومفتوحة يوميا إلا أن بعض الأسواق موسمية" "Rue du Marchand"
          [ Media Media.imageType "https://cdn.pixabay.com/photo/2016/08/28/22/22/souk-1627045_960_720.jpg"
          , Media Media.imageType "https://visitmarrakech.ma/wp-content/uploads/2018/02/Souks_Marrakech_Maroc.jpg"
          , Media Media.imageType "https://decorationorientale.com/wp-content/uploads/2018/05/Marrakech-Souk.jpg" ]
          [ Audio "" "Arabian" "" "http://localhost:8000/storage/converts/m03@H3yVB@tuuJyt7FZKyg==.mp3" "" ]
          [ PI.onGoing, PI.free, PI.notReserved ]
          [ PI.shop, PI.touristicPlace, PI.restaurant ]
        ]
    }

  _ ->
    { swissNumber = ""
    , title = ""
    , listPI = []
    }
