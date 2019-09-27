# Entry Points

## AudioList

POST http://.../api/audiolist
Create an empty audiolist and return the audiolist edition facet (AudioListEdit).

```
{
    "type":"ocap",
    "ocapType":"AudioListEdit",
    "url":"http://.../api/audiolist/{audiolist_edit_id}/edit" (GET)
}
```

## Audio

POST http://.../api/audio
Create an audio and return the audio edition facet (AudioEdit).

```
{
    "type":"ocap",
    "ocapType":"AudioEdit",
    "url":"http://.../api/audio/{audio_edit_id}/edit" (GET)
}
```

The request must be a Form Request and must contain an "audio" field with the file.

# Resources

## AudioView

An AudioView is a facet of an audio.
It is the Read Only access of the audio.

GET http://.../api/audio/{audio_view_id}

```
{
    "type":"AudioView",
    "path":"{audio_path}"
}
```

## AudioEdit

An AudioEdit is a facet of an audio.
It is the Read Write access of the audio.

GET http://.../api/audio/{audio_edit_id}/edit

```
{
    "type":"AudioEdit",
    "view_facet":"http://.../api/...", (GET)
    "path":"{audio_path}",
    "delete":"http://.../api/..." (DELETE)
}
```

## AudioListView

An AudioListView is a facet of an audiolist.
It is the Read Only access of the audiolist : it lists all the audios of the audiolist.

GET http://.../audiolist/{audiolist_view_id}

```
{
    "type":"AudioListView",
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

GET http://.../audiolist/{audiolist_edit_id}/edit

```
{
    "type":"AudioListEdit",
    "view_facet":"http://.../api/...", (GET)
    "update":"http://.../api/...", (PUT)
    "contents":[
        {
            "type":"ocap",
            "ocapType":"AudioView",
            "url":"http://.../api/..." (GET)
        }
    ]
}
```

"update" request header "Content-Type" must be set to "application/json".
"update" request body must contain a json with a "data" field containing an "audios" array containing all the "ocap" (url) of the AudioViewFacet from the audios you want to link to the list.
"update" request returns the updated AudioListEdit when successful.

### Example

```
{
    "data":{
        "audios":[
            {
                "ocap":"http://.../api/audio/{audio_view_id}"
            },
            {
                "ocap":"http://.../api/audio/{audio_view_id}"
            }
        ]
    }
}
```
