# Entry Points

## AudioList

POST /api/audiolist
Create an empty audiolist and return the audiolist edition facet (AudioListEdit).

```
{
    "type":"ocap",
    "ocapType":"AudioListEdit",
    "url":"/api/audiolist/{audiolist_edit_id}/edit" (GET)
}
```

## Audio

POST /api/audio
Create an audio and return the audio edition facet (AudioEdit).

```
{
    "type":"ocap",
    "ocapType":"AudioEdit",
    "url":"/api/audio/{audio_edit_id}/edit" (GET)
}
```

The request must be a Form Request and must contain an "audio" field with the file.

# Resources

## AudioView

An AudioView is a facet of an audio.
It is the Read Only access of the audio.

GET /api/audio/{audio_view_id}

```
{
    "type":"Audio",
    "id":"{audio_view_id}",
    "contents":""
}
```

## AudioEdit

An AudioEdit is a facet of an audio.
It is the Read Write access of the audio.

GET /api/audio/{audio_edit_id}/edit

```
{
    "type":"Audio",
    "id":"{audio_view_id}",
    "view_facet":"/api/...", (GET)
    "contents":"",
    "delete_audio":"/api/..." (DELETE)
}
```

## AudioListView

An AudioListView is a facet of an audiolist.
It is the Read Only access of the audiolist : it lists all the audios of the audiolist.

GET /audiolist/{audiolist_view_id}

```
{
    "type":"ALView",
    "id":"{audiolist_view_id},
    "contents":[
        {
            "type":"ocap",
            "ocapType":"AudioView",
            "url":"/api/..." (GET)
        }
    ]
}
```

## AudioListEdit

An AudioListEdit is a facet of an audiolist.
It is the Read Write access of the audiolist : it lists all audios of the audiolist and gives the url to update the list.

GET /audiolist/{audiolist_edit_id}/edit

```
{
    "type":"AudioListEdit",
    "id":"{audiolist_edit_id}",
    "update":"/api/...", (PUT)
    "view_facet":"/api/...", (GET)
    "contents":[
        {
            "type":"ocap",
            "ocapType":"AudioView",
            "url":"/api/..." (GET)
        }
    ]
}
```

"update" request header "Content-Type" must be set to "application/json".
"update" request body must contain a json with a "data" field containing an "audios" array containing all the "id" of the AudioViewFacet from the audios you want to link to the list.
"update" request returns the updated AudioListEdit when successful.

### Example

```
{
    "data":{
        "audios":[
            {
                "id":"{AudioViewFacet_id}
            },
            {
                "id":"{AudioViewFacet_id}
            }
        ]
    }
}
```
