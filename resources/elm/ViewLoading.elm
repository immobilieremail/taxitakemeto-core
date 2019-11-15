module ViewLoading exposing (view)

import Html exposing (..)
import Html.Attributes exposing (..)
import Html.Events exposing (..)
import Loading exposing ( LoaderType(..), defaultConfig, render )



view : Html msg
view =
  div
    [ class "p-4" ]
    [ Loading.render
      Spinner -- LoaderType
      { defaultConfig | color = "#333", size = 75 } -- Config
      Loading.On -- LoadingState
    ]
