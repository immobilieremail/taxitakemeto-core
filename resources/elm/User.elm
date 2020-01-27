module User exposing (..)

-- TYPES


type Contact
    = Phone String
    | Email String


type alias User =
    { name : String
    , contact : List Contact
    , password : Maybe String
    }


getPasswordValue : Maybe String -> String
getPasswordValue password =
    case password of
        Just pswd ->
            pswd

        Nothing ->
            ""


getContactType : Contact -> (String -> Contact)
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
        value =
            getContactValue contact
    in
    if getContactType comparable value == getContactType contact value then
        False

    else
        True


getEmailFromContactList : List Contact -> String
getEmailFromContactList contactList =
    let
        filteredContact =
            List.filter (filterContactType (Phone "")) contactList
    in
    case List.head filteredContact of
        Just email ->
            getContactValue email

        Nothing ->
            ""


getPhoneFromContactList : List Contact -> String
getPhoneFromContactList contact =
    let
        filteredContact =
            List.filter (filterContactType (Email "")) contact
    in
    case List.head filteredContact of
        Just phone ->
            getContactValue phone

        Nothing ->
            ""
