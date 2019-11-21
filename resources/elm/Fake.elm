module Fake exposing (..)

import Bootstrap.Accordion as Accordion
import SwissNumber exposing (SwissNumber)
import Travel exposing (Travel)
import Media exposing (..)
import PI exposing (PI)

pi : SwissNumber -> PI
pi ocapUrl =
  case ocapUrl of
  "http://localhost:8000/api/obj/1" ->
    { swissNumber = "http://localhost:8000/api/obj/1"
    , title = "Wat Phra Kaew Temple - Thaïland"
    , description = "This is a description of Meenakshi Amman Temple."
    , address = "9 Boulevard de la Canopée"
    , medias = [ Media Media.imageType "https://upload.wikimedia.org/wikipedia/commons/b/b2/Wat_Phra_Sri_Rattana_Satsadaram_11.jpg"
      , Media Media.imageType "https://bangkokmonamour.files.wordpress.com/2015/09/vue-generale-temple.jpg"
      , Media Media.imageType "https://upload.wikimedia.org/wikipedia/commons/c/c1/Wat_Phra_Kaew_by_Ninara_TSP_edit_crop.jpg" ]
    , audios = [ Audio "" "Thaï" "" "http://localhost:8000/storage/converts/DX9ytBq8luIwmUcu6fiN2g==.mp3" ""
      , Audio "" "English" "" "http://localhost:8000/storage/converts/DX9ytBq8luIwmUcu6fiN2g==.mp3" ""
      , Audio "" "French" "" "http://localhost:8000/storage/converts/DX9ytBq8luIwmUcu6fiN2g==.mp3" "" ]
    , tags = [ PI.free, PI.reserved ]
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
    , audios = [ Audio "" "French" "" "http://localhost:8000/storage/converts/DX9ytBq8luIwmUcu6fiN2g==.mp3" ""
      , Audio "" "English" "" "http://localhost:8000/storage/converts/DX9ytBq8luIwmUcu6fiN2g==.mp3" "" ]
    , tags = [ PI.paying, PI.notReserved, PI.onGoing, PI.free ]
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
    , tags = [ PI.onGoing, PI.free, PI.notReserved ]
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


travel : SwissNumber -> Travel
travel ocapUrl =
  case ocapUrl of
  "http://localhost:8000/api/obj/parisdakar" ->
    { swissNumber = "http://localhost:8000/api/obj/parisdakar"
    , title = "Paris - Dakar"
    , listPI = [
        PI "http://localhost:8000/api/obj/1" "Wat Phra Kaew Temple - Thaïland" "" ""
          []
          []
          [ PI.free, PI.reserved ]
          [ PI.touristicPlace ]
        , PI "http://localhost:8000/api/obj/2" "Food Festival - Singapour" "" ""
          []
          []
          [ PI.paying ]
          [ PI.restaurant, PI.touristicPlace ]
        , PI "http://localhost:8000/api/obj/3" "Hôtel F1 - Bordeaux" "" ""
          []
          []
          [ PI.paying, PI.notReserved, PI.onGoing, PI.free ]
          [ PI.hotel, PI.shop, PI.touristicPlace, PI.restaurant ]
        , PI "http://localhost:8000/api/obj/4" "Souk Rabais Bazar - Marrakech" "" ""
          []
          []
          [ PI.onGoing, PI.free, PI.notReserved ]
          [ PI.shop, PI.touristicPlace, PI.restaurant ]
        ]
    , accordionState = Accordion.initialState
    }

  "http://localhost:8000/api/obj/voyagebirmanie" ->
    { swissNumber = "http://localhost:8000/api/obj/voyagebirmanie"
    , title = "Petit voyage en Birmanie"
    , listPI = [
      PI "http://localhost:8000/api/obj/3" "Hôtel F1 - Bordeaux" "" ""
        []
        []
        [ PI.paying, PI.notReserved, PI.onGoing, PI.free ]
        [ PI.hotel, PI.shop, PI.touristicPlace, PI.restaurant ]
      , PI "http://localhost:8000/api/obj/1" "Wat Phra Kaew Temple - Thaïland" "" ""
        []
        []
        [ PI.free, PI.reserved ]
        [ PI.touristicPlace ]
      ]
    , accordionState = Accordion.initialState
    }

  "http://localhost:8000/api/obj/sejourtadjikistan" ->
    { swissNumber = "http://localhost:8000/api/obj/sejourtadjikistan"
    , title = "Séjour au Tadjikistan"
    , listPI = [
      PI "http://localhost:8000/api/obj/4" "Souk Rabais Bazar - Marrakech" "" ""
        []
        []
        [ PI.onGoing, PI.free, PI.notReserved ]
        [ PI.shop, PI.touristicPlace, PI.restaurant ]
      , PI "http://localhost:8000/api/obj/2" "Food Festival - Singapour" "" ""
        []
        []
        [ PI.paying ]
        [ PI.restaurant, PI.touristicPlace ]
      ]
    , accordionState = Accordion.initialState
    }

  "http://localhost:8000/api/obj/vacancesmontagne" ->
    { swissNumber = "http://localhost:8000/api/obj/vacancesmontagne"
    , title = "Vacances à la montagne"
    , listPI = [
      PI "http://localhost:8000/api/obj/1" "Wat Phra Kaew Temple - Thaïland" "" ""
        []
        []
        [ PI.free, PI.reserved ]
        [ PI.touristicPlace ]
      , PI "http://localhost:8000/api/obj/2" "Food Festival - Singapour" "" ""
        []
        []
        [ PI.paying ]
        [ PI.restaurant, PI.touristicPlace ]
      , PI "http://localhost:8000/api/obj/3" "Hôtel F1 - Bordeaux" "" ""
        []
        []
        [ PI.paying, PI.notReserved, PI.onGoing, PI.free ]
        [ PI.hotel, PI.shop, PI.touristicPlace, PI.restaurant ]
      ]
    , accordionState = Accordion.initialState
    }

  _ ->
    { swissNumber = ""
    , title = ""
    , listPI = []
    , accordionState = Accordion.initialState
    }
