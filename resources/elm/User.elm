module User exposing (..)

import Bootstrap.Form.Input as Input



-- TYPES


type Contact
  = Phone String
  | Email String


type alias User =
  { name : String
  , contact : List Contact
  , password : Maybe String
  , confirmPassword : Maybe String
  }


getContactType : Contact -> ( String -> Contact )
getContactType contact =
  case contact of
    Email _ ->
      Email

    Phone _ ->
      Phone


getContactValue : Contact -> String
getContactValue contact =
  case contact of
    Phone value ->
      value

    Email value ->
      value



filterContactType : Contact -> Contact -> Bool
filterContactType comparable contact =
  let
    value = getContactValue contact
  in
    if (getContactType comparable) value == (getContactType contact) value then
      False
    else
      True

