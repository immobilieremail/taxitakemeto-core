module Message exposing (..)

import Html exposing (..)
import Html.Attributes exposing (..)
import Html.Events exposing (..)
import Bootstrap.Alert as Alert



-- TYPES


type MessageType
    = UserDashboard


type alias Message =
    { message : String
    , type_ : MessageType
    }



userDashboardType : MessageType
userDashboardType =
    UserDashboard



-- VIEWS


view : Message -> MessageType -> Html msg
view message desiredMessageType =
    case message.type_ of
        UserDashboard ->
            case UserDashboard == desiredMessageType of
                True ->
                    Alert.simpleWarning
                        []
                        [ span
                            [ style "font-size" "12px" ]
                            [ text message.message ]
                        ]

                False ->
                    div [] []
