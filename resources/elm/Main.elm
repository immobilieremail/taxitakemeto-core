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
import Bootstrap.Form as Form
import Bootstrap.Form.Input as Input
import Bootstrap.Form.Checkbox as Checkbox
import Bootstrap.Form.InputGroup as InputGroup
import Color
import Process
import Task
import PI exposing (..)
import Fake exposing (..)
import Media exposing (..)
import User exposing (User)
import Travel exposing (Travel)
import SwissNumber exposing (SwissNumber)
import OverButton as OB exposing (..)
import Message as Message exposing (..)
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
  | SimpleViewPI
  | LoadingPage
  | ViewSearchPI
  | ViewNewTravel
  | ViewLogin
  | ViewNewAccount
  | ViewInvit


type alias Model =
  { key : Nav.Key
  , currentView : CurrentView
  , navbarState : Navbar.State
  , currentTravel : Travel
  , currentPI : PI
  , proposals : List PI
  , checked : List PI
  , listTravel : List Travel
  , carouselState : Carousel.State
  , accordionState : Accordion.State
  , mouseOver : List OverButton
  , formTitle : String
  , user : User
  , message : Maybe Message.Message
  , loading : Bool
  }


model0 : Nav.Key -> Navbar.State -> Model
model0 key state =
  { key = key
  , currentView = ViewUserDashboard
  , navbarState = state
  , currentTravel = Travel "" "" [] [] Accordion.initialState
  , currentPI = PI "" "" "" "" [] [] [] []
  , proposals = []
  , checked = []
  , listTravel = []
  , carouselState = Carousel.initialState
  , accordionState = Accordion.initialState
  , mouseOver = []
  , formTitle = ""
  , user = User "John Doe" Nothing Nothing Nothing
  , message = Nothing
  , loading = False
  }

fakeModel0 : Nav.Key -> Navbar.State -> Model
fakeModel0 key state =
  let model = model0 key state
  in { model | listTravel =
    [ Travel
      "http://localhost:8000/api/obj/parisdakar"
      "Paris - Dakar"
      []
      [ "+33 6 12 34 56 78", "foo@bar.com" ]
      Accordion.initialState
    , Travel
      "http://localhost:8000/api/obj/voyagebirmanie"
      "Petit voyage en Birmanie"
      []
      []
      Accordion.initialState
    , Travel
      "http://localhost:8000/api/obj/sejourtadjikistan"
      "Séjour au Tadjikistan"
      []
      []
      Accordion.initialState
    ], proposals =
    [ PI
      "http://localhost:8000/api/obj/1"
      "Wat Phra Kaew Temple - Thaïland"
      "This is a description of Meenakshi Amman Temple."
      "9 Boulevard de la Canopée"
      [ Media Media.imageType "https://upload.wikimedia.org/wikipedia/commons/b/b2/Wat_Phra_Sri_Rattana_Satsadaram_11.jpg" ]
      []
      [ PI.free, PI.reserved ]
      [ PI.touristicPlace ]
    , PI
      "http://localhost:8000/api/obj/2"
      "Food Festival - Singapour"
      "It’s no secret that Singaporeans are united in their love for great food."
      "666 Rue de l'Enfer"
      [ Media Media.imageType "https://www.je-papote.com/wp-content/uploads/2016/08/food-festival-singapour.jpg" ]
      []
      [ PI.paying ]
      [ PI.restaurant, PI.touristicPlace ]
    , PI
      "http://localhost:8000/api/obj/3"
      "Hôtel F1 - Bordeaux"
      "HotelF1 est une marque hôtelière 1 étoile filiale du groupe Accor."
      "Le Paradis (lieu-dit)"
      [ Media Media.imageType "https://www.ahstatic.com/photos/2472_ho_00_p_1024x768.jpg" ]
      []
      [ PI.paying, PI.notReserved, PI.onGoing, PI.free ]
      [ PI.hotel ]
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
        [ "+33 6 12 34 56 78", "foo@bar.com" ]
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
  | ViewChanged String
  | GetPI SwissNumber
  | GotPI (Result Http.Error PI)
  | GotTravel (Result Http.Error Travel)
  | UpdateNavbar Navbar.State
  | CarouselMsg Carousel.Msg
  | AccordionMsg Accordion.State
  | TravelAccordionMsg Accordion.State
  | CarouselPrev
  | CarouselNext
  | MouseOver OverButton
  | MouseOut OverButton
  | AddToCheck PI Bool
  | AddCheckedToTravel SwissNumber
  | CreateNewTravel
  | GotNewTravel (Result Http.Error Travel)
  | SetTitle String
  | SetUserName String
  | SetUserContact User.Contact String
  | SetUserPassword String
  | SetUserConfirmPassword String


type Route
  = RouteHome
  | RouteLogin
  | RouteNewAccount
  | RouteInvit
  | RouteSearch
  | RouteNewTravel
  | RoutePI (Maybe String)
  | RouteSimplePI (Maybe String)
  | RouteTravel (Maybe String)


getRootUrl : String
getRootUrl =
  "http://localhost:8000"


router : P.Parser (Route -> a) a
router =
  P.oneOf
  [ P.map RouteHome <| P.s "elm"
  , P.map RouteLogin <| P.s "elm" </> P.s "login"
  , P.map RouteNewAccount <| P.s "elm" </> P.s "newaccount"
  , P.map RouteInvit <| P.s "elm" </> P.s "invit"
  , P.map RouteSearch <| P.s "elm" </> P.s "search"
  , P.map RouteNewTravel <| P.s "elm" </> P.s "newtravel"
  , P.map RoutePI <| P.s "elm" </> P.s "pi" </> P.fragment identity
  , P.map RouteSimplePI <| P.s "elm" </> P.s "pi" </> P.s "simpleview" </> P.fragment identity
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

        RouteLogin ->
          ( { model | currentView = ViewLogin }, commonCmd )

        RouteNewAccount ->
          ( { model | currentView = ViewNewAccount }, commonCmd )

        RouteInvit ->
          ( { model | currentView = ViewInvit }, commonCmd )

        RouteSearch ->
          ( { model | currentView = ViewSearchPI }, commonCmd )

        RouteNewTravel ->
          ( { model | currentView = ViewNewTravel }, commonCmd )

        RoutePI data ->
          case data of
            Nothing ->
              ( model, commonCmd )

            Just ocapUrl ->
              ( { model | loading = True, currentView = ViewUserDashboard }
              , Cmd.batch
                [ commonCmd
                , getPIfromUrl ocapUrl
                ]
              )

        RouteSimplePI data ->
          case data of
            Nothing ->
              ( model, commonCmd )

            Just ocapUrl ->
              if ocapUrl == model.currentPI.swissNumber then
                ( { model | currentView = SimpleViewPI }, commonCmd )
              else
                ( { model | currentView = SimpleViewPI }
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
              ( { model | currentTravel =
                case (List.head (List.filter (\travel -> travel.swissNumber == ocapUrl) model.listTravel)) of
                  Just travel ->
                    travel

                  Nothing ->
                    model.currentTravel

                , message = Maybe.Just (Message.Message "This could be an old version of this travel." Message.userDashboardType)
                , loading = True
                }
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
              let
                indexList = List.range 0 (List.length model.currentTravel.listPI)
                indexPI =
                  List.sum (
                    List.filter (\index ->
                      Accordion.isOpen (pi.swissNumber ++ "#" ++ String.fromInt index) model.currentTravel.accordionState) indexList
                  )
                accordionId = pi.swissNumber ++ "#" ++ String.fromInt indexPI
              in
                ( { model | currentPI = pi
                  , currentTravel = Travel.updateAccordionState (Accordion.initialStateCardOpen accordionId) model.currentTravel
                  , loading = False
                  }
                , Cmd.none )

            False ->
              ( { model | loading = False }, Cmd.none )

        Err _ ->
          ( model, Cmd.none )

    GotTravel result ->
      case result of
        Ok travel ->
          case travel.swissNumber /= "" of
            True ->
              case travel /= model.currentTravel of
                True ->
                  let
                    updatedTravel = { travel | accordionState = model.currentTravel.accordionState }
                    mappedList = (
                      List.map (\t -> if t.swissNumber == travel.swissNumber then travel else t) model.listTravel)
                  in
                    ( { model | currentTravel = updatedTravel
                      , listTravel = mappedList
                      , message = Nothing
                      , loading = False
                      }, Cmd.none )

                False ->
                  ( { model | message = Nothing
                    , loading = False
                    }, Cmd.none )

            False ->
              ( model, Cmd.none )

        Err _ ->
          ( model, Cmd.none )

    ViewChanged maybeUrl ->
      case Url.fromString maybeUrl of
        Just url ->
          updateFromUrl model url (Nav.pushUrl model.key maybeUrl)

        Nothing ->
          ( model, Cmd.none )

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

    AddToCheck pi bool ->
      case bool of
        True ->
          ( { model
            | checked = (List.filter (PI.swissNumberIsNotEqual pi.swissNumber) model.checked) ++ [ pi ]
            }, Cmd.none )

        False ->
          ( { model
            | checked = (List.filter (PI.swissNumberIsNotEqual pi.swissNumber) model.checked)
            }, Cmd.none )

    AddCheckedToTravel swissNumber ->
      let
        oldListTravel = model.listTravel
        newListTravel = (List.map (Travel.updateListPI swissNumber model.checked) oldListTravel)
      in
        case model.currentTravel.swissNumber == swissNumber of
          True ->
            let
              oldCurrentTravel = model.currentTravel
              newCurrentTravel = { oldCurrentTravel | listPI = oldCurrentTravel.listPI ++ model.checked }
            in
              ( { model | currentTravel = newCurrentTravel, listTravel = newListTravel, checked = [] }, Cmd.none )

          False ->
            ( { model | listTravel = newListTravel, checked = [] }, Cmd.none )

    CreateNewTravel ->
      ( { model | loading = True }, createNewTravel model.formTitle model.checked )

    GotNewTravel result ->
      case result of
        Ok travel ->
          case Url.fromString (getRootUrl ++ "/elm") of
            Just url ->
              updateFromUrl
                { model | currentTravel = travel
                , listTravel = model.listTravel ++ [ travel ]
                , checked = []
                , accordionState = Accordion.initialState
                , loading = False
                } url (Nav.pushUrl model.key (Url.toString url))

            Nothing ->
              ( { model | currentTravel = travel
                , listTravel = model.listTravel ++ [ travel ]
                , checked = []
                , accordionState = Accordion.initialState
                , loading = False
                }, Cmd.none )

        Err _ ->
          ( model, Cmd.none )

    SetTitle title ->
      ( { model | formTitle = title }, Cmd.none )

    SetUserPassword pswd ->
      ( { model | user = User model.user.name model.user.contact (Just pswd) model.user.confirmPassword }, Cmd.none )

    SetUserConfirmPassword pswd ->
      ( { model | user = User model.user.name model.user.contact model.user.password (Just pswd) }, Cmd.none )

    SetUserName name ->
      ( { model | user = User name model.user.contact model.user.password model.user.confirmPassword }, Cmd.none )

    SetUserContact contactType contact ->
      case contactType of
        User.Email _ ->
          ( { model
            | user = User
              model.user.name
              (Just (User.Email contact))
              model.user.password
              model.user.confirmPassword
            }, Cmd.none )

        User.Phone _ ->
          ( { model
            | user = User
              model.user.name
              (Just (User.Phone contact))
              model.user.password
              model.user.confirmPassword
            }, Cmd.none )



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
          , Travel.viewList model.listTravel
          ]

      ViewListPIDashboard ->
        div
          []
          [ viewNavbar model
          , viewListPIDashboard model model.currentTravel
          ]

      SimpleViewPI ->
        simpleViewPI model.carouselState model.mouseOver model.currentPI

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
          , viewSearchPI model
          ]

      ViewNewTravel ->
        div
          []
          [ viewNavbar model
          , viewCreateNewTravel model
          ]

      ViewLogin ->
        div
          []
          [ viewLogin model.user ]

      ViewNewAccount ->
        div
          []
          [ viewNewAccount model.user ]

      ViewInvit ->
        div
          []
          [ viewInvit model.user ]
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
      , navbarItem "/elm/login" "Login page"
      , navbarItem "/elm/invit" "Invitation"
      , navbarItem "#" "Item 4"
      ]
    |> Navbar.view model.navbarState


viewNewAccount : User -> Html Msg
viewNewAccount user =
  let
    nameOptions = if user.name /= "" then [ Input.value user.name ] else []
    userPassword = case user.password of
      Nothing -> ""
      Just pswd -> pswd
    userConfirmPassword = case user.confirmPassword of
      Nothing -> ""
      Just pswd -> pswd
    passwordOptions = if user.name /= "" then [ Input.value userPassword ] else []
    confirmPasswordOptions = if user.name /= "" then [ Input.value userConfirmPassword ] else []
    signupConditions = user.name /= "" && userPassword /= "" && userPassword == userConfirmPassword
  in
    Grid.container
      [ style "max-width" "100%" ]
      [ Grid.row
        [ Row.middleXs
        , Row.attrs [ class "lightgrey-background p-3 mb-3" ]
        ]
        [ Grid.col
          [ Col.xs12, Col.textAlign Text.alignXsCenter ]
          [ h3
            [ class "title" ]
            [ text "Create an account" ]
          ]
        ]
      , Grid.row
        [ Row.middleXs
        , Row.attrs [ class "my-container" ]
        ]
        [ Grid.col
          [ Col.xs12, Col.textAlign Text.alignXsRight ]
          [ Form.form
            [ onSubmit (ViewChanged (getRootUrl ++ "/elm")) ]
            [ myInput Input.text "myusername" "My name" nameOptions
            , myInput Input.password "mypwd" "My password" ([ Input.onInput SetUserPassword ] ++ passwordOptions)
            , myInput Input.password "mypwdconfirm" "Confirm my password" ([ Input.onInput SetUserConfirmPassword ] ++ confirmPasswordOptions)
            , loginButton
              "Sign Up"
              (ViewChanged (getRootUrl ++ "/elm"))
              [ Button.primary
              , Button.attrs [ class "mb-2 mt-4" ]
              , Button.disabled (signupConditions == False)
              ]
            , loginButton
              "Already have an account ?"
              (ViewChanged (getRootUrl ++ "/elm/login"))
              [ Button.outlineSecondary
              , Button.attrs [ class "mb-2" ]
              ]
            ]
          ]
        ]
      ]


myInput : (List (Input.Option msg) -> Html Msg) -> String -> String -> List (Input.Option msg) -> Html Msg
myInput input id txt options =
  div
    []
    [ input
      ([ Input.id id
      , Input.placeholder txt
      , Input.attrs [ class "my-2" ]
      ] ++ options)
    ]

loginButton : String -> Msg -> List (Button.Option Msg) -> Html Msg
loginButton txt click options =
  Button.button
    ([ Button.attrs [ style "width" "100%" ]
    , Button.onClick click
    ] ++ options)
    [ text txt ]

viewLogin : User -> Html Msg
viewLogin user =
  let
    nameOptions = if user.name /= "" then [ Input.value user.name ] else []
    userPassword = case user.password of
      Nothing -> ""
      Just pswd -> pswd
    passwordOptions = if userPassword /= "" then [ Input.value userPassword ] else []
    signinConditions = user.name /= "" && userPassword /= ""
  in
    Grid.container
      [ style "max-width" "100%" ]
      [ Grid.row
        [ Row.middleXs
        , Row.attrs [ class "lightgrey-background p-3 mb-3" ]
        ]
        [ Grid.col
          [ Col.xs12 ]
          [ h3
            [ class "title" ]
            [ text "Sign In" ]
          ]
        ]
      , Grid.row
        [ Row.middleXs
        , Row.attrs [ class "my-container" ]
        ]
        [ Grid.col
          [ Col.xs12, Col.textAlign Text.alignXsRight ]
          [ Form.form
            [ onSubmit (ViewChanged (getRootUrl ++ "/elm")) ]
            [ myInput Input.text "myusername" "My name" nameOptions
            , myInput Input.password "mypwd" "My password" ([ Input.onInput SetUserPassword ] ++ passwordOptions)
            , loginButton
              "Sign In"
              (ViewChanged (getRootUrl ++ "/elm"))
              [ Button.primary
              , Button.attrs [ class "mb-2 mt-4" ]
              , Button.disabled (signinConditions == False)
              ]
            , loginButton
              "Need an account ?"
              (ViewChanged (getRootUrl ++ "/elm/newaccount"))
              [ Button.outlineSecondary
              , Button.attrs [ class "mb-2" ]
              ]
            ]
          ]
        ]
      ]


viewJanistorCard : Html Msg
viewJanistorCard =
  Grid.row
    [ Row.middleXs
    , Row.attrs
      [ class "my-container" ]
    ]
    [ Grid.col
      [ Col.xs12
      , Col.textAlign Text.alignXsCenter
      ]
      [ h5
        []
        [ text "You have been invited by" ]
      , div
        [ class "lightgrey-background py-3 px-4" ]
        [ h3
          [ class "mb-3" ]
          [ text "Bob Butler" ]
        , Grid.row
          []
          [ Grid.col
            [ Col.xs4 ]
            [ img
              [ src "https://miro.medium.com/fit/c/160/160/0*7K7xu3sVR5U-fn9s.jpg"
              , class "img-fluid rounded"
              ]
              []
            ]
          , Grid.col
            [ Col.xs8
            , Col.textAlign Text.alignXsLeft
            ]
            [ p
              []
              [ text "This text is a description of Bob Butler." ]
            ]
          ]
        ]
      ]
    ]


viewInvitForm : User -> Html Msg
viewInvitForm user =
  let
    nameOptions =
      [ Input.id "myname"
      , Input.onInput SetUserName
      , Input.placeholder "My name"
      , Input.attrs [ class "mb-4" ]
      ] ++ if user.name /= "" then [ Input.value user.name ] else []
  in
    Form.form
      []
      [ Input.text nameOptions
      , myInput
        Input.text
        "myemail"
        "My email address"
        ((User.emailInputOption user) ++ [ Input.onInput (SetUserContact (User.Email "")) ])
      , h6 [ class "text-center my-1" ] [ text "OR" ]
      , myInput
        Input.text
        "myphone"
        "My phone number"
        ((User.phoneInputOption user) ++ [ Input.onInput (SetUserContact (User.Phone "")) ])
      , Button.button
        [ Button.primary
        , Button.attrs [ class "ml-sm-2 my-2" ]
        , Button.disabled (String.length user.name == 0 || user.contact == Nothing)
        , Button.onClick (ViewChanged (getRootUrl ++ "/elm"))
        ]
        [ text "Submit" ]
      ]

viewInvit : User -> Html Msg
viewInvit user =
  Grid.container
    [ style "max-width" "100%" ]
    [ Grid.row
      [ Row.middleXs
      , Row.attrs [ class "lightgrey-background p-3 mb-3" ]
      ]
      [ Grid.col
        [ Col.xs12 ]
        [ h3
          [ class "title" ]
          [ text "Hi traveller !" ]
        ]
      ]
    , Grid.row
      [ Row.middleXs ]
      [ Grid.col
        [ Col.xs12, Col.textAlign Text.alignXsCenter ]
        [ h5
          []
          [ text "Who are you ?" ]
        ]
      ]
    , Grid.row
      [ Row.middleXs
      , Row.attrs [ class "my-container" ]
      ]
      [ Grid.col
        [ Col.xs12, Col.textAlign Text.alignXsRight ]
        [ viewInvitForm user ]
      ]
    , hr [] []
    , viewJanistorCard
    ]


viewBlockTravel : SwissNumber -> Travel -> Block.Item Msg
viewBlockTravel swissNumber travel =
  Block.text
    [ if swissNumber == travel.swissNumber then class "lightgrey-background" else class "" ]
    [ Travel.view travel ]

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
      [ Accordion.block
        [ Block.attrs [ class "user-accordion-body-padding" ] ]
        (List.map (viewBlockTravel model.currentTravel.swissNumber) model.listTravel)
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
        [ h3
          [ class "title" ]
          [ text model.currentTravel.title ]
        , case (List.length model.currentTravel.listPI) > 0 of
          True ->
            viewListPIDashboard model model.currentTravel

          False ->
            case model.loading of
              True ->
                Loading.view

              False ->
                div
                  [ class "text-center my-3" ]
                  [ Button.linkButton
                    [ Button.primary
                    , Button.attrs [ href "/elm/search" ]
                    ]
                    [ text "Add Points of Interest" ]
                  ]
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


iconRemove : Html Msg
iconRemove =
  img
    [ class "icon-image"
    , src "https://image.flaticon.com/icons/png/512/53/53891.png"
    ]
    []

viewCheckedPI : PI -> Html Msg
viewCheckedPI pi =
  Grid.container
    []
    [ Grid.row
      [ Row.attrs [ class "checked-pi mb-3" ] ]
      [ Grid.col
        [ Col.md2
        , Col.xs4
        , Col.textAlign Text.alignXsCenter
        ]
        [ Media.viewFirstMedia [ style "max-width" "150px", class "rounded" ] pi.medias ]
      , Grid.col
        [ Col.md8
        , Col.xs6
        , Col.textAlign Text.alignXsLeft
        ]
        [ h4
          [ class "resize-text" ]
          [ text pi.title ]
        , text pi.address
        ]
      , Grid.col
        [ Col.xs2
        , Col.textAlign Text.alignXsCenter
        ]
        [ Button.button
          [ Button.small
          , Button.onClick (AddToCheck pi False)
          ]
          [ iconRemove ]
        ]
      ]
    ]

viewCreateNewTravel : Model -> Html Msg
viewCreateNewTravel model =
  Grid.container
    []
    [ Grid.row
      []
      [ Grid.col
        [ Col.xs12 ]
        [ h2
          [ class "title" ]
          [ text "Create new Travel" ]
        , Form.form
          [ onSubmit CreateNewTravel
          , class "text-right"
          ]
          [ Input.text
            [ Input.attrs [ placeholder "My Travel title" ]
            , Input.onInput SetTitle
            ]
          , Button.button
            [ Button.primary
            , Button.attrs [ class "ml-sm-2 my-2" ]
            , Button.disabled (model.loading || String.length model.formTitle == 0)
            ]
            [ text "Create" ]
          ]
        ]
      ]
    , case model.loading of
      True ->
        Loading.view

      False ->
        Grid.row
        []
        [ Grid.col
          [ Col.xs12 ]
          [ hr [] [] ]
        , Grid.col
          [ Col.xs12 ]
          (List.map viewCheckedPI model.checked)
        ]
    ]


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

viewProposal : List PI -> PI -> Html Msg
viewProposal checked proposal =
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
      [ Checkbox.checkbox
        [ Checkbox.id "checkout"
        , Checkbox.onCheck (AddToCheck proposal)
        , Checkbox.checked (List.member proposal checked)
        ]
        ""
      ]
    ]

viewSearchAddToList : String -> Msg -> Html Msg
viewSearchAddToList str msg =
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
        [ Button.success
        , Button.onClick msg
        ]
        [ text "+" ]
      ]
    ]

viewOneLinePI : PI -> Html Msg
viewOneLinePI pi =
  Grid.row
    [ Row.attrs [ class "lightgrey-background mb-2 p-2" ]
    , Row.middleXs
    ]
    [ Grid.col
      [ Col.xs9
      , Col.textAlign Text.alignXsLeft
      ]
      [ span
        [ style "font-size" "12px" ]
        [ text pi.title ]
      ]
    , Grid.col
      [ Col.xs3 ]
      [ Button.button
        [ Button.small
        , Button.onClick (AddToCheck pi False)
        ]
        [ iconRemove ]
      ]
    ]

viewSearchPI : Model -> Html Msg
viewSearchPI model =
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
        (List.map (viewProposal model.checked) model.proposals)
      , case List.length model.checked > 0 of
        True ->
          div
            []
            [ h4
              []
              [ text "Selected PIs" ]
            ]

        False ->
          div [] []

      , div
        [ class "mb-3" ]
        (List.map viewOneLinePI model.checked)
      , viewSearchAddToList ("Add to '" ++ model.currentTravel.title ++ "' travel") (AddCheckedToTravel model.currentTravel.swissNumber)
      , viewSearchAddToList "Create a new travel" (ViewChanged (getRootUrl ++ "/elm/newtravel"))
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

piAccordionCard : PI -> Carousel.State -> Accordion.State -> List OverButton -> Int -> PI -> Accordion.Card Msg
piAccordionCard currentPI carouselState accordionState mouseOver index pi =
  Accordion.card
    { id = pi.swissNumber ++ "#" ++ String.fromInt index
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
              viewPI carouselState accordionState mouseOver index currentPI

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

viewContactButton : String -> Html Msg
viewContactButton contact =
  Button.button
    [ Button.roleLink ]
    [ text contact ]

viewContact : Travel -> Html Msg
viewContact travel =
  Grid.container
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
          (List.map viewContactButton travel.listContact)
        ]
      ]
    ]

viewListPIDashboard : Model -> Travel -> Html Msg
viewListPIDashboard model travel =
  div
    []
    [ Grid.container
      []
      [ case model.message of
          Just message ->
            Message.view message Message.userDashboardType

          Nothing ->
            div [] []

      , accordionView
        TravelAccordionMsg
        travel.accordionState
        (List.indexedMap (piAccordionCard model.currentPI model.carouselState travel.accordionState model.mouseOver) travel.listPI)
      ]
    , if List.length travel.listContact > 0 then viewContact travel else div [] []
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


piChangeViewButton : String -> Msg -> Html Msg
piChangeViewButton txt msg =
  div
    [ class "text-center mb-4" ]
    [ Button.button
      [ Button.large
      , Button.outlineSecondary
      , Button.onClick msg
      ]
      [ text txt
      , img
        [ class "pi-view-button"
        , src "https://upload.wikimedia.org/wikipedia/commons/thumb/e/eb/PICOL_icon_View.svg/1024px-PICOL_icon_View.svg.png"
        ]
        []
      ]
    ]

viewPI : Carousel.State -> Accordion.State -> List OverButton -> Int -> PI -> Html Msg
viewPI carouselState accordionState mouseOver index pi =
  div
    []
    [ Grid.container
      [ class "pt-3" ]
      [ Grid.row
        [ Row.middleXs ]
        [ Grid.col
          [ Col.sm6 ]
          [ case Accordion.isOpen (pi.swissNumber ++ "#" ++ String.fromInt index) accordionState of
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
      , piChangeViewButton "Simple view" (ViewChanged (getRootUrl ++ "/elm/pi/simpleview#" ++ pi.swissNumber))
      ]
    ]


simpleViewPI : Carousel.State -> List OverButton -> PI -> Html Msg
simpleViewPI carouselState mouseOver pi =
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
      , piChangeViewButton "Exit view" (ViewChanged (getRootUrl ++ "/elm/pi#" ++ pi.swissNumber))
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

-- createNewTravel : String -> List PI -> Cmd Msg
-- createNewTravel title listPI =
--   Http.post
--     { url = "http://localhost:8000/api/travel"
--     , body = []
--     , except = Http.expectJson GotNewTravel travelDecoder
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

createNewTravel : String -> List PI -> Cmd Msg
createNewTravel title listPI =
  Process.sleep 2000
    |> Task.perform (\_ ->
      GotNewTravel (Ok (Travel "http://localhost:8000/api/obj/newtravel" title listPI [] Accordion.initialState))
    )

