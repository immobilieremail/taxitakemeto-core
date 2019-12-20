module Message exposing (Message, MessageType, userDashboardType, view, viewWarningMessage)

import Html exposing (Html, div, text, span)
import Html.Attributes exposing (style)
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
      if UserDashboard == desiredMessageType then
        viewWarningMessage message.message
      else
        div [] []

viewWarningMessage : String -> Html msg
viewWarningMessage message =
  Alert.simpleWarning []
    [ span
      [ style "font-size" "12px" ]
      [ text message ]
    ]
