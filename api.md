# Entry Points

## Audio

POST http://.../api/audio
Create an audio and return the audio edition facet (AudioEdit).

```
{
    "type":"ocap",
    "ocapType":"AudioEdit",
    "url":"/api/audio/{audio_edit_id}/edit" (GET)
}
```

The request must be a Form Request and must contain an "audio" field with the file.

## AudioList

POST /api/audiolist
Create an empty audiolist and return the audiolist edition facet (AudioListEdit).

```
{
    "type":"ocap",
    "ocapType":"AudioListEdit",
    "url":"http://.../api/audiolist/{audiolist_edit_id}/edit" (GET)
}
```

## Shell

POST /api/shell
Create an empty shell and return it.

```
{
    "type":"ocap",
    "ocapType":"Shell",
    "url":"/api/shell/{shell_id}" (GET)
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

## Shell

A Shell is an audiolists container.

GET /api/shell/{shell_id}

```
{
    "type":"Shell",
    "dropbox":"/api/...",
    "update":"/api/...", (PUT)
    "contents":[
        "audiolists_view":[
            {
                "type":"ocap",
                "ocapType":"AudioListView",
                "url":"/api/..." (GET)
            }
        ],
        "audiolists_edit":[
            {
                "type":"ocap",
                "ocapType":"AudioListEdit",
                "url":"/api/..." (GET)
            }
        ]
    ]
}
```

"dropbox" field is the Shell public url : it gives others Shells the capacity to send ocap to this Shell.

"update" request header "Content-Type" must be set to "application/json".
"update" request body must contain a json with a "data" field containing an "audiolists" array containing the "ocapType" and the "ocap" url of the objects which will be added to the shell.
"update" request returns the updated Shell when successful.

### Example

```
{
    "data":{
        "audiolists":[
            {
                "ocapType":"AudioListView",
                "ocap":"/api/audiolist/89adaA74@a_0zaxQM"
            },
            {
                "ocapType":"AudioListEdit",
                "ocap":"/api/audiolist/@apNdzaw463n63bcR"
            },
            {
                "ocapType":"AudioListView",
                "ocap":"/api/audiolist/opea4587Oa3Z_uya"
            }
        ]
    }
}
```
