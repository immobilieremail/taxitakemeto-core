module User exposing (..)

import Bootstrap.Form.Input as Input



-- TYPES


type Contact
  = Phone String
  | Email String


type alias User =
  { name : String
  , contact : Maybe Contact
  , password : Maybe String
  , confirmPassword : Maybe String
  }


compare : Maybe Contact -> Contact -> Bool
compare maybeContact wantedContact =
  case maybeContact of
    Nothing ->
      False

    Just contact ->
      if contact == wantedContact then
        True
      else
        False


getValue : Maybe Contact -> String
getValue maybeContact =
  case maybeContact of
    Nothing ->
      ""

    Just contact ->
      case contact of
        Phone value ->
          value

        Email value ->
          value


emailInputOption : User -> List (Input.Option msg)
emailInputOption user =
    if compare user.contact (Email (getValue user.contact)) then
      [ Input.value (getValue user.contact) ]
    else
      []


phoneInputOption : User -> List (Input.Option msg)
phoneInputOption user =
    if compare user.contact (Phone (getValue user.contact)) then
      [ Input.value (getValue user.contact) ]
    else
      []
