module Main exposing (..)

import Browser
import Browser.Navigation as Nav
import Html exposing (Html, h1, h2, h3, h4, h5, h6, div, text, img, p, hr, label, span, a)
import Html.Attributes exposing (class, style, href, src, placeholder, id)
import Html.Events exposing (onClick, onSubmit, onMouseOut, onMouseOver)
import Http
import Task exposing (attempt)
import Url
import Url.Parser as P exposing ((</>))
import Bootstrap.Accordion as Accordion
import Bootstrap.Navbar as Navbar
import Bootstrap.Grid as Grid
import Bootstrap.Grid.Col as Col
import Bootstrap.Grid.Row as Row
import Bootstrap.Card as Card
import Bootstrap.Card.Block as Block
import Bootstrap.Button as Button
import Bootstrap.Modal as Modal
import Bootstrap.Text as Text
import Bootstrap.Carousel as Carousel
import Bootstrap.Form as Form
import Bootstrap.Form.Input as Input
import Bootstrap.Form.Textarea as Textarea
import Bootstrap.Form.Checkbox as Checkbox
import Bootstrap.Form.InputGroup as InputGroup
import Color
import Process
import Request as R
import PI exposing (PI)
import Media exposing (Media)
import User exposing (User, Contact)
import Shell exposing (Shell, ShellDropbox)
import Travel exposing (Travel)
import SwissNumber exposing (SwissNumber)
import OverButton as OB exposing (OverButton, carouselPrevButton, carouselNextButton)
import Message as Message exposing (Message, userDashboardType, view, viewWarningMessage)
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
  | SimpleViewPI
  | ViewSearchPI
  | ViewNewTravel
  | ViewNewPI
  | ViewLogin
  | ViewNewAccount
  | ViewInvit
  | ViewProfile


type alias Model =
  { key : Nav.Key
  , currentView : CurrentView
  , navbarState : Navbar.State
  , currentTravel : Travel
  , currentPI : PI
  , proposals : List PI
  , checked : List PI
  , carouselState : Carousel.State
  , accordionState : Accordion.State
  , modalVisibility : Modal.Visibility
  , mouseOver : List OverButton
  , formTitle : String
  , formDescription : String
  , formAddress : String
  , tmpPassword : String
  , tmpConfirmPassword : String
  , user : User
  , message : Maybe Message.Message
  , loading : Bool
  , shell : Shell
  , senderDropbox : Maybe ShellDropbox
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
  , carouselState = Carousel.initialState
  , accordionState = Accordion.initialState
  , modalVisibility = Modal.hidden
  , mouseOver = []
  , formTitle = ""
  , formDescription = ""
  , formAddress = ""
  , tmpPassword = ""
  , tmpConfirmPassword = ""
  , user = User "John Doe" [] Nothing
  , message = Nothing
  , loading = False
  , shell = Shell "" [] [] (User "John Doe" [] Nothing) Nothing
  , senderDropbox = Nothing
  }


init : () -> Url.Url -> Nav.Key -> ( Model, Cmd Msg )
init flags url key =
  let
    (state, cmd) = Navbar.initialState UpdateNavbar
    model = model0 key state
  in
    updateFromUrl model url cmd



-- UPDATE


type Msg
  = LinkClicked Browser.UrlRequest
  | UrlChanged Url.Url
  | ViewChanged String
  | GotPI (Result Http.Error PI)
  | GotTravel (Result Http.Error Travel)
  | GotShell (Result Http.Error (Shell, Maybe ShellDropbox))
  | UpdateNavbar Navbar.State
  | CarouselMsg Carousel.Msg
  | AccordionMsg Accordion.State
  | TravelAccordionMsg Accordion.State
  | CloseInvitationModal
  | CloseModal
  | ShowModal
  | CarouselPrev
  | CarouselNext
  | MouseOver OverButton
  | MouseOut OverButton
  | AddToCheck PI Bool
  | AddCheckedToTravel SwissNumber
  | CreateNewPI
  | CreateNewTravel
  | GotNewPI (Result Http.Error PI)
  | GotNewTravel (Result Http.Error Travel)
  | SetTitle String
  | SetDescription String
  | SetAddress String
  | SetUserName String
  | AddUserContact User.Contact String
  | SetTmpPassword String
  | SetTmpConfirmPassword String
  | SetUserPassword
  | GotDashboard (Result Http.Error User)
  | EmptyResponse (Result Http.Error ())


type Route
  = RouteHome
  | RouteLogin
  | RouteProfile
  | RouteNewAccount
  | RouteInvit
  | RouteSearch
  | RouteNewPI
  | RouteNewTravel
  | RoutePI (Maybe String)
  | RouteSimplePI (Maybe String)
  | RouteTravel (Maybe String)
  | RouteShell (Maybe String)


replaceInList : List { a | swissNumber : SwissNumber } -> { a | swissNumber : SwissNumber } -> List { a | swissNumber : SwissNumber }
replaceInList list object =
  List.map (\obj -> if obj.swissNumber == object.swissNumber then object else obj) list


getRootUrl : String
getRootUrl =
  "http://localhost:8000"


router : P.Parser (Route -> a) a
router =
  P.oneOf
  [ P.map RouteHome <| P.s "elm"
  , P.map RouteLogin <| P.s "elm" </> P.s "login"
  , P.map RouteProfile <| P.s "elm" </> P.s "profile"
  , P.map RouteNewAccount <| P.s "elm" </> P.s "newaccount"
  , P.map RouteInvit <| P.s "elm" </> P.s "invit"
  , P.map RouteSearch <| P.s "elm" </> P.s "search"
  , P.map RouteNewPI <| P.s "elm" </> P.s "newpi"
  , P.map RouteNewTravel <| P.s "elm" </> P.s "newtravel"
  , P.map RoutePI <| P.s "elm" </> P.s "pi" </> P.fragment identity
  , P.map RouteSimplePI <| P.s "elm" </> P.s "pi" </> P.s "simpleview" </> P.fragment identity
  , P.map RouteTravel <| P.s "elm" </> P.s "travel" </> P.fragment identity
  , P.map RouteShell <| P.s "elm" </> P.s "shell" </> P.fragment identity
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

        RouteProfile ->
          ( { model | currentView = ViewProfile }, commonCmd )

        RouteNewAccount ->
          ( { model | currentView = ViewNewAccount }, commonCmd )

        RouteInvit ->
          ( { model | currentView = ViewInvit }, commonCmd )

        RouteSearch ->
          ( { model | currentView = ViewSearchPI }, commonCmd )

        RouteNewPI ->
          ( { model | currentView = ViewNewPI }, commonCmd )

        RouteNewTravel ->
          ( { model | currentView = ViewNewTravel }, commonCmd )

        RoutePI data ->
          case data of
            Nothing ->
              ( model, commonCmd )

            Just ocapUrl ->
              let
                getPIinTravel = \travel -> List.filter (\pi -> pi.swissNumber == ocapUrl) travel.listPI
                piList = List.concatMap getPIinTravel model.shell.travelList
                newCurrentPI =
                  case List.head piList of
                    Nothing -> model.currentPI
                    Just newPI -> newPI
              in
                ( { model | loading = List.isEmpty piList
                  , currentView = ViewUserDashboard
                  , currentPI = newCurrentPI
                  }
                , Cmd.batch
                  [ commonCmd
                  , getSinglePI ocapUrl
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
                  , getSinglePI ocapUrl
                  ]
                )

        RouteTravel data ->
          case data of
            Nothing ->
              ( model, commonCmd )

            Just ocapUrl ->
              ( { model | currentTravel =
                case List.head (List.filter (\travel -> travel.swissNumber == ocapUrl) model.shell.travelList) of
                  Just travel ->
                    travel

                  Nothing ->
                    model.currentTravel

                , message = Maybe.Just (Message.Message "This could be an old version of this travel." Message.userDashboardType)
                , loading = True
                }
              , Cmd.batch
                [ commonCmd
                , getSingleTravel ocapUrl
                ]
              )

        RouteShell data ->
          case data of
            Nothing ->
              ( model, commonCmd )

            Just ocapUrl ->
              ( { model | currentView = ViewUserDashboard }
              , Cmd.batch
                [ commonCmd
                , getSingleShell ocapUrl
                ]
              )

update : Msg -> Model -> ( Model, Cmd Msg )
update msg model =
  case msg of
    LinkClicked urlRequest ->
      case urlRequest of
        Browser.Internal url ->
          updateFromUrl { model | loading = False } url (Nav.pushUrl model.key (Url.toString url))

        Browser.External href ->
          ( model, Nav.load href )

    UrlChanged url ->
      updateFromUrl { model | loading = False } url Cmd.none

    GotPI result ->
      case result of
        Ok pi ->
          let
            oldCurrentTravel = model.currentTravel
            newCurrentTravel = { oldCurrentTravel | listPI = replaceInList oldCurrentTravel.listPI pi }

            oldShell = model.shell
            newShell = { oldShell | travelList = replaceInList oldShell.travelList newCurrentTravel }
          in
            ( { model | currentPI = pi
              , currentTravel = newCurrentTravel
              , shell = newShell
              }, Cmd.none )

        Err _ ->
          ( model, Cmd.none )

    GotTravel result ->
      case result of
        Ok travel ->
          if travel.swissNumber /= "" then
            if travel /= model.currentTravel then
              let
                updatedTravel = { travel | accordionState = model.currentTravel.accordionState }
                mappedList = replaceInList model.shell.travelList travel
                oldShell = model.shell
                newShell = { oldShell | travelList = mappedList }
              in
                ( { model | currentTravel = updatedTravel
                  , shell = newShell
                  , message = Nothing
                  , loading = False
                  }, Cmd.none )
            else
              ( { model | message = Nothing
                , loading = False
                }, Cmd.none )
          else
            ( model, Cmd.none )

        Err _ ->
          ( model, Cmd.none )

    GotShell result ->
      case result of
        Ok (shell, senderDropbox) ->
          if model.currentTravel.swissNumber /= "" then
            ( { model | shell = shell }, Cmd.none )
          else
            let
              newCurrentTravel =
                case List.head shell.travelList of
                  Just travel ->
                    travel

                  Nothing ->
                    model.currentTravel

            in
              ( { model | shell = shell
                , currentTravel = newCurrentTravel
                , senderDropbox = senderDropbox
                }, Cmd.none )

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
      ( { model | currentTravel = Travel.updateAccordionState state model.currentTravel }, Cmd.none )

    ShowModal ->
      ( { model | modalVisibility = Modal.shown } , Cmd.none )

    CloseModal ->
      ( { model | modalVisibility = Modal.hidden } , Cmd.none )

    CloseInvitationModal ->
      let
        oldShell = model.shell
        newShell = { oldShell | sender = Nothing }
      in
        ( { model | shell = newShell, senderDropbox = Nothing }, Cmd.none )

    CarouselPrev ->
      ( { model | carouselState = Carousel.prev model.carouselState }, Cmd.none )

    CarouselNext ->
      ( { model | carouselState = Carousel.next model.carouselState }, Cmd.none )

    MouseOver overButton ->
      if List.member overButton model.mouseOver then
        ( model, Cmd.none )
      else
        ( { model | mouseOver = overButton :: model.mouseOver }, Cmd.none )

    MouseOut overButton ->
      ( { model | mouseOver = List.filter (\n -> n /= overButton) model.mouseOver }, Cmd.none )

    AddToCheck pi bool ->
      if bool then
        ( { model
          | checked = pi :: List.filter (PI.swissNumberIsNotEqual pi.swissNumber) model.checked
          }, Cmd.none )
      else
        ( { model
          | checked = List.filter (PI.swissNumberIsNotEqual pi.swissNumber) model.checked
          }, Cmd.none )

    AddCheckedToTravel swissNumber ->
      let
        oldShell = model.shell
        oldTravelList = oldShell.travelList
        newShell = { oldShell | travelList = List.map (Travel.updateListPI swissNumber model.checked) oldTravelList }
      in
        if model.currentTravel.swissNumber == swissNumber then
          let
            oldCurrentTravel = model.currentTravel
            newCurrentTravelPIs = oldCurrentTravel.listPI ++ model.checked
            newCurrentTravel = { oldCurrentTravel | listPI = newCurrentTravelPIs }
          in
            ( { model | currentTravel = newCurrentTravel, shell = newShell, checked = [] }, addPItoTravel swissNumber newCurrentTravelPIs )
        else
          ( { model | shell = newShell, checked = [] }, Cmd.none )

    CreateNewPI ->
      ( { model | loading = True }, createSinglePI model.formTitle model.formTitle model.formTitle )

    GotNewPI result ->
      case result of
        Ok pi ->
          ( { model | proposals = pi :: model.proposals }, Cmd.none )

        Err _ ->
          ( model, Cmd.none )

    CreateNewTravel ->
      ( { model | loading = True }, createSingleTravel model.formTitle model.checked )

    GotNewTravel result ->
      case result of
        Ok travel ->
          let
            oldShell = model.shell
            newTravelList = model.shell.travelList ++ [ travel ]
            newShell = { oldShell | travelList = newTravelList }
          in
            case Url.fromString (getRootUrl ++ "/elm/shell#" ++ model.shell.swissNumber) of
              Just url ->
                updateFromUrl
                  { model | currentTravel = travel
                  , shell = newShell
                  , checked = []
                  , accordionState = Accordion.initialState
                  , loading = False
                  } url
                  (Cmd.batch
                    [ Nav.pushUrl model.key (Url.toString url)
                    , addTraveltoShell model.shell.swissNumber newTravelList
                    ]
                  )

              Nothing ->
                ( { model | currentTravel = travel
                  , shell = newShell
                  , checked = []
                  , accordionState = Accordion.initialState
                  , loading = False
                  }, Cmd.none )

        Err _ ->
          ( model, Cmd.none )

    SetTitle title ->
      ( { model | formTitle = title }, Cmd.none )

    SetDescription description ->
      ( { model | formDescription = description }, Cmd.none )

    SetAddress address ->
      ( { model | formAddress = address }, Cmd.none )

    SetTmpPassword pswd ->
      ( { model | tmpPassword = pswd }, Cmd.none )

    SetTmpConfirmPassword pswd ->
      ( { model | tmpConfirmPassword = pswd }, Cmd.none )

    SetUserName name ->
      let
        oldUser = model.user
        newUser = { oldUser | name = name }
      in
        ( { model | user = newUser }, Cmd.none )

    AddUserContact contactType contact ->
      let
        newContact = User.getContactType contactType contact
        filteredContactList = List.filter (User.filterContactType contactType) model.user.contact

        oldUser = model.user
        newUser = { oldUser | contact = filteredContactList ++ if contact /= "" then [ newContact ] else [] }
      in
        ( { model | user = newUser }, Cmd.none )

    SetUserPassword ->
      ( model, checkUserLogin model.user.name model.tmpPassword )

    GotDashboard user ->
      case user of
        Ok result ->
          ( { model | user = result, currentView = ViewUserDashboard }, Cmd.none )

        Err _ ->
          ( model, Cmd.none )

    EmptyResponse _ ->
      ( model, Cmd.none )



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
        div []
          [ viewNavbar model
          , viewUserDashboard model
          ]

      SimpleViewPI ->
        simpleViewPI model.carouselState model.mouseOver model.currentPI

      ViewSearchPI ->
        div []
          [ viewNavbar model
          , viewSearchPI model
          ]

      ViewNewTravel ->
        div []
          [ viewNavbar model
          , viewCreateNewTravel model
          ]

      ViewNewPI ->
        div []
          [ viewNavbar model
          , viewCreateNewPI model
          ]

      ViewLogin ->
        div [] [ viewLogin model.user model.tmpPassword ]

      ViewNewAccount ->
        div [] [ viewNewAccount model.user model.tmpPassword model.tmpConfirmPassword ]

      ViewInvit ->
        div [] [ viewInvit model.user ]

      ViewProfile ->
        div []
          [ viewNavbar model
          , viewProfile model.user model.tmpPassword model.tmpConfirmPassword model.modalVisibility
          ]
    ]
  }


navbarItem : String -> String -> Navbar.Item Msg
navbarItem url content =
  Navbar.itemLink [ href url ] [ text content ]

viewNavbar : Model -> Html Msg
viewNavbar model =
  Navbar.config UpdateNavbar
    |> Navbar.lightCustom Color.lightGrey
    |> Navbar.withAnimation
    |> Navbar.collapseMedium
    |> Navbar.brand
      [ href ("/elm/shell#" ++ model.shell.swissNumber) ]
      [ img
        [ src "https://cdn2.iconfinder.com/data/icons/ios-7-icons/50/user_male2-512.png"
        , class "navbar-icon"
        ]
        []
      ]
    |> Navbar.items
      [ navbarItem "/elm/search" "Search PI"
      , navbarItem "/elm/newpi" "Create new PI"
      , navbarItem "/elm/login" "Login"
      , navbarItem "/elm/profile" "My profile"
      ]
    |> Navbar.view model.navbarState


viewModal : Modal.Visibility -> Msg -> String -> Html Msg -> Html Msg -> Html Msg
viewModal modalVisibility msg title body footer =
  Modal.config msg
    |> Modal.small
    |> Modal.hideOnBackdropClick True
    |> Modal.h3 [] [ text title ]
    |> Modal.body [] [ body ]
    |> Modal.footer [] [ footer ]
    |> Modal.view modalVisibility


viewProfileLabel : (List (Input.Option Msg) -> Html Msg) -> String -> String -> List (Input.Option Msg) -> Html Msg
viewProfileLabel input txt value options =
  Form.group
    [ Form.attrs [ class "mb-3" ] ]
    [ Form.label
      [ class "profile-label" ]
      [ text txt ]
    , input
      ( Input.value value :: options)
    ]

inputContactEmail : String -> Html Msg
inputContactEmail email =
  viewProfileLabel
    Input.email
    "My email address"
    email
    [ Input.onInput (AddUserContact (User.Email ""))
    , Input.id "myemail"
    ]

inputContactPhone : String -> Html Msg
inputContactPhone phone =
  viewProfileLabel
    Input.text
    "My phone number"
    phone
    [ Input.onInput (AddUserContact (User.Phone ""))
    , Input.id "myphone"
    ]

viewProfile : User -> String -> String -> Modal.Visibility -> Html Msg
viewProfile user tmpPassword tmpConfirmPassword modalVisibility =
  Grid.container []
    [ viewModal
      modalVisibility
      CloseModal
      "Create my password"
      (viewCompleteFormPassword tmpPassword tmpConfirmPassword)
      (Button.button
        [ Button.primary ]
        [ text "Submit" ]
      )
    , Grid.row
      [ Row.middleXs ]
      [ Grid.col
        [ Col.xs12, Col.textAlign Text.alignXsCenter ]
        [ h3
          [ class "title" ]
          [ text "My profile" ]
        ]
      ]
    , Form.form []
      [ viewProfileLabel Input.text "My name" user.name [ Input.onInput SetUserName ]
      , case user.password of
        Nothing ->
          Form.group []
            [ longButton "Create a password" ShowModal [ Button.primary ]
            , Form.help []
              [ text "Connect to the application using a password" ]
            ]

        Just _ ->
          Form.group []
            [ longButton "Reset my password" ShowModal [ Button.primary ] ]
      ]
    , Form.form []
      [ h4 [ class "title" ] [ text "Contact" ]
      , if List.length user.contact == 0 then
        Message.viewWarningMessage
          "You should add a contact information to help you interact with people."
      else
        div [] []
      , inputContactEmail (User.getEmailFromContactList user.contact)
      , inputContactPhone (User.getPhoneFromContactList user.contact)
      ]
    ]


viewSingleFormPassword : String -> (String -> Msg) -> (Bool, String) -> List (Input.Option Msg) -> Html Msg
viewSingleFormPassword placehold message feedback options  =
  Form.group []
    [ Input.password
      ([ Input.placeholder placehold
      , Input.attrs [ class "my-2" ]
      , Input.onInput message
      ] ++ options)
    , if Tuple.first feedback then
      Form.invalidFeedback []
        [ text (Tuple.second feedback) ]
    else
      Form.validFeedback []
        [ text "OK" ]
    ]

viewCompleteFormPassword : String -> String -> Html Msg
viewCompleteFormPassword tmpPassword tmpConfirmPassword =
  let
    userPassword = tmpPassword
    passwordCondition = String.length userPassword < 12
    passwordOptions = if passwordCondition then [ Input.danger ] else []

    userConfirmPassword = tmpConfirmPassword
    confirmPasswordCondition = userConfirmPassword /= userPassword
    confirmPasswordOptions = if confirmPasswordCondition then [ Input.danger ] else []
  in
    div []
      [ viewSingleFormPassword
        "My password"
        SetTmpPassword
        (passwordCondition, "Password must be at least 12 characters long")
        passwordOptions
      , viewSingleFormPassword
        "Confirm my password"
        SetTmpConfirmPassword
        (confirmPasswordCondition, "Must be the same as password")
        confirmPasswordOptions
      ]

viewNewAccountForm : User -> String -> String -> Html Msg
viewNewAccountForm user tmpPassword tmpConfirmPassword =
  let
    userPassword = tmpPassword
    userConfirmPassword = tmpConfirmPassword
    signupConditions = user.name /= "" && userPassword /= "" && userPassword == userConfirmPassword
  in
    Form.form []
      [ myInput Input.text "My name" [ Input.value user.name ]
      , viewCompleteFormPassword tmpPassword tmpConfirmPassword
      , longButton "Sign Up"
        SetUserPassword
        [ Button.primary
        , Button.attrs [ class "mb-2 mt-4" ]
        , Button.disabled (signupConditions == False)
        ]
      , longButton "Already have an account ?"
        (ViewChanged (getRootUrl ++ "/elm/login"))
        [ Button.outlineSecondary
        , Button.attrs [ class "mb-2" ]
        ]
      ]

viewNewAccount : User -> String -> String -> Html Msg
viewNewAccount user tmpPassword tmpConfirmPassword =
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
        [ viewNewAccountForm user tmpPassword tmpConfirmPassword ]
      ]
    ]


myInput : (List (Input.Option msg) -> Html Msg) -> String -> List (Input.Option msg) -> Html Msg
myInput input txt options =
  Form.group []
    [ input
      ([ Input.placeholder txt
      , Input.attrs [ class "my-2" ]
      ] ++ options)
    ]

longButton : String -> Msg -> List (Button.Option Msg) -> Html Msg
longButton txt click options =
  Button.button
    ([ Button.attrs [ style "width" "100%" ]
    , Button.onClick click
    ] ++ options)
    [ text txt ]

viewLogin : User -> String -> Html Msg
viewLogin user tmpPassword =
  let
    nameOptions = if user.name /= "" then [ Input.value user.name ] else []
    userPassword = tmpPassword
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
            [ myInput Input.text "My name" nameOptions
            , myInput Input.password "My password" ( Input.onInput SetTmpPassword :: passwordOptions)
            , longButton "Sign In"
              SetUserPassword
              [ Button.primary
              , Button.attrs [ class "mb-2 mt-4" ]
              , Button.disabled (signinConditions == False)
              ]
            , longButton "Need an account ?"
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
      [ h5 [] [ text "You have been invited by" ]
      , div
        [ class "lightgrey-background py-3 px-4" ]
        [ h3
          [ class "mb-3" ]
          [ text "Bob Butler" ]
        , Grid.row []
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
            [ p [] [ text "This text is a description of Bob Butler." ] ]
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
    Form.form []
      [ Input.text nameOptions
      , myInput Input.text "My email address" [ Input.onInput (AddUserContact (User.Email "")) ]
      , h6 [ class "text-center my-1" ] [ text "OR" ]
      , myInput Input.text "My phone number" [ Input.onInput (AddUserContact (User.Phone "")) ]
      , Button.button
        [ Button.primary
        , Button.attrs [ class "ml-sm-2 my-2" ]
        , Button.disabled (String.length user.name == 0 || user.contact == [])
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
        [ h5 [] [ text "Who are you ?" ]
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
  Grid.row []
    [ Grid.col
      [ Col.xs8 ]
      [ h5
        [ class "text-left" ]
        [ text "Other Travels" ]
      ]
    , Grid.col
      [ Col.xs4, Col.textAlign Text.alignXsRight ]
      [ if Accordion.isOpen "card1" accordionState then
          img
            [ src "https://image.noelshack.com/fichiers/2019/47/1/1574075725-arrow-up.png"
            , style "max-width" "20px"
            ]
            [ text "/\\" ]
        else
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
        (List.map (viewBlockTravel model.currentTravel.swissNumber) model.shell.travelList)
      ]
    }

viewUserDashboard : Model -> Html Msg
viewUserDashboard model =
  Grid.container []
    [ Grid.row []
      [ Grid.col
        [ Col.xs12 ]
        [ if String.length model.currentTravel.swissNumber > 0 then
            div []
              [ h3
                [ class "title" ]
                [ text model.currentTravel.title ]
              , if List.length model.currentTravel.listPI > 0 then
                  viewListPIDashboard model model.currentTravel
                else
                  if model.loading then
                    Loading.view
                  else
                    div
                      [ class "text-center my-3" ]
                      [ Button.linkButton
                        [ Button.primary
                        , Button.attrs [ href "/elm/search" ]
                        ]
                        [ text "Add Points of Interest" ]
                      ]
              ]
        else
          div
            [ class "text-center my-3" ]
            [ Button.linkButton
              [ Button.primary
              , Button.attrs [ href "/elm/newtravel" ]
              ]
              [ text "Create a new Travel" ]
            ]
        ]
      , Grid.col
        [ Col.xs12 ]
        [ hr [] [] ]
      , Grid.col
        [ Col.xs12 ]
        [ accordionView
          AccordionMsg
          model.accordionState
          [ viewUserDashboardAccordion model ]
        ]
      , Grid.col
        [ Col.xs12 ]
        <| case model.senderDropbox of
            Nothing ->
              []

            Just senderDropbox ->
              [ viewModal
                Modal.shown
                CloseInvitationModal
                ("You have been invited by " ++ senderDropbox.name)
                (div [] [ text "body" ])
                (div [] [])
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
  Grid.container []
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
  Grid.container []
    [ Grid.row []
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
    , if model.loading then
        Loading.view
      else
        Grid.row []
          [ Grid.col
            [ Col.xs12 ]
            [ hr [] [] ]
          , Grid.col
            [ Col.xs12 ]
            (List.map viewCheckedPI model.checked)
          ]
    ]


viewCreateNewPI : Model -> Html Msg
viewCreateNewPI model =
  Grid.container []
    [ Grid.row []
      [ Grid.col
        [ Col.xs12 ]
        [ h2
          [ class "title" ]
          [ text "Create new Point of Interest" ]
        , Form.form
          [ onSubmit CreateNewPI
          , class "text-right"
          ]
          [ Form.group []
            [ Input.text
              [ Input.attrs [ placeholder "My Point of Interest title" ]
              , Input.onInput SetTitle
              ]
            ]
          , Form.group []
            [ Input.text
              [ Input.attrs [ placeholder "My Point of Interest address" ]
              , Input.onInput SetTitle
              ]
            ]
          , Form.group [ Form.attrs [ class "text-left" ] ]
            [ Form.label [] [ text "My Point of Interest description" ]
            , Textarea.textarea
              [ Textarea.rows 3
              , Textarea.onInput SetTitle
              ]
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
            [ InputGroup.span []
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
      [ hr [] [] ]
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
      [ h4 [] [ text str ] ]
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
    , Grid.row []
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
      [ div []
        (List.map (viewProposal model.checked) model.proposals)
      , if List.length model.checked > 0 then
          div []
            [ h4 [] [ text "Selected PIs" ] ]
        else
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
  a [ href ("/elm/pi#" ++ pi.swissNumber) ]
    [ Grid.row
      [ Row.middleXs ]
      [ Grid.col
        [ Col.xs12, Col.textAlign Text.alignXsCenter ]
        [ h5 [] [ text pi.title ] ]
      ]
    , Grid.row
      [ Row.middleXs, Row.attrs [ style "background-color" "#eeeeec", class "rounded" ] ]
      [ Grid.col
        [ Col.sm3 ]
        [ Grid.row []
          (List.map PI.viewTypePI pi.typespi)
        ]
      , Grid.col
        [ Col.sm6 ]
        [ Grid.row
          [ Row.attrs [ class "pi-tags" ] ]
          (List.map PI.viewTagPI pi.tags)
        ]
      , Grid.col [ Col.sm3 ] []
      ]
    ]

piAccordionCard : Carousel.State -> Accordion.State -> List OverButton -> Bool -> Int -> PI -> Accordion.Card Msg
piAccordionCard carouselState accordionState mouseOver loading index pi =
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
        [ Block.text []
          [ if loading then
              Loading.view
            else
              viewPI carouselState accordionState mouseOver index pi
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
  div []
    [ Grid.container []
      [ case model.message of
          Just message ->
            Message.view message Message.userDashboardType

          Nothing ->
            div [] []

      , accordionView
        TravelAccordionMsg
        travel.accordionState
        (List.indexedMap (piAccordionCard model.carouselState travel.accordionState model.mouseOver model.loading) travel.listPI)
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
      , if List.member OB.carouselPrevButton mouseOver then
          style "opacity" ".9"
        else
          style "opacity" ".5"
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
      , if List.member OB.CarouselNextButton mouseOver then
          style "opacity" ".9"
        else
          style "opacity" ".5"
      ]
      []
    ]

viewCarousel : List Media -> Carousel.State -> List OverButton -> Html Msg
viewCarousel medias carouselState mouseOver =
  if List.isEmpty medias then
    div [ class "d-flex" ]
      [ img
        [ src "https://www.labaleine.fr/sites/baleine/files/image-not-found.jpg" ]
        []
      ]
  else
    div []
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
  div []
    [ Grid.container
      [ class "pt-3" ]
      [ Grid.row
        [ Row.middleXs ]
        [ Grid.col
          [ Col.sm6 ]
          [ if Accordion.isOpen (pi.swissNumber ++ "#" ++ String.fromInt index) accordionState then
              viewCarousel pi.medias carouselState mouseOver
            else
              Media.viewFirstMedia [] pi.medias
          ]
        , Grid.col
          [ Col.sm6 ]
          [ h5 [] [ text pi.title ]
          , div
            [ class "text-justify" ]
            [ text pi.description ]
          ]
        ]
      , Grid.row
        [ Row.middleXs ]
        [ Grid.col [ Col.sm3 ] []
        , Grid.col
          [ Col.sm6 ]
          [ Grid.row
            [ Row.attrs [ class "pi-tags" ] ]
            (List.map PI.viewTagPI pi.tags)
          ]
        , Grid.col [ Col.sm3 ] []
        ]
      , hr [] []
      , h2
        [ class "title" ]
        [ text "Audio language" ]
      , Grid.container
        [ class "p-4" ]
        (List.map Media.viewAudioLanguage pi.audios)
      , hr [ class "pt-2" ] []
      , piChangeViewButton "Simple view" (ViewChanged (getRootUrl ++ "/elm/pi/simpleview#" ++ pi.swissNumber))
      ]
    ]


simpleViewPI : Carousel.State -> List OverButton -> PI -> Html Msg
simpleViewPI carouselState mouseOver pi =
  div []
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
      , hr [] []
      , h2
        [ class "title" ]
        [ text "Audio language" ]
      , Grid.container
        [ class "p-4" ]
        (List.map Media.viewAudioLanguage pi.audios)
      , hr [ class "pt-2" ] []
      , piChangeViewButton "Exit view" (ViewChanged (getRootUrl ++ "/elm/pi#" ++ pi.swissNumber))
      ]
    ]



-- JSON API



getSingleShell : SwissNumber -> Cmd Msg
getSingleShell ocapUrl =
  Task.attempt GotShell
    (R.getSingleShellRequest ocapUrl)


getSingleTravel : SwissNumber -> Cmd Msg
getSingleTravel ocapUrl =
  Task.attempt GotTravel
    (R.getSingleTravelRequest ocapUrl)


getSinglePI : SwissNumber -> Cmd Msg
getSinglePI ocapUrl =
  Task.attempt GotPI
    (R.getSinglePIRequest ocapUrl)


createSingleTravel : String -> List PI -> Cmd Msg
createSingleTravel title piList =
  Task.attempt GotNewTravel
    (R.createNewTravelRequest title piList)


createSinglePI : String -> String -> String -> Cmd Msg
createSinglePI title description address =
  Task.attempt GotNewPI
    (R.createNewPIRequest title description address)


addPItoTravel : SwissNumber -> List PI -> Cmd Msg
addPItoTravel ocapUrl piList =
  Task.attempt EmptyResponse
    (R.addPItoTravelRequest ocapUrl piList)


addTraveltoShell : SwissNumber -> List Travel -> Cmd Msg
addTraveltoShell ocapUrl travelList =
  Task.attempt EmptyResponse
    (R.addTraveltoShellRequest ocapUrl travelList)


--- Temporary fakers


checkUserLogin : String -> String -> Cmd Msg
checkUserLogin login password =
  Process.sleep 100
    |> Task.perform (\_ ->
      GotDashboard (Ok (User "John Doe" [] (Just "password")))
    )
