# Entry Points

POST /audiolist
Create an empty audiolist and return the audiolist edition facet (AudioListEdit or ALEdit)

```
{
    "type":"ocap",
    "ocapType":"ALEdit",
    "url":"http://.../api/audiolist/{audiolist_id}/edit"
}
```

# Resources

## Audio

An Audio contains information about an audio file.

```
{
   "type":"Audio",
    "audio_id":"{audio_id}",
    "path_to_file":"{path_to_file}"
}
```

## AudioListView

An AudioListView (or ALView) is a facet of an audiolist.
It is the Read Only access of the audiolist : it lists all the audios of the audiolist.

GET /audiolist/{audiolist_view_id}

```
{
   "type":"ALView",
    "contents":[Audio, ...]
}
```

## AudioListEdit

An AudioListEdit (or ALEdit) is a facet of an audiolist.
It is the Read Write access of the audiolist : it lists all audios of the audiolist and gives the urls to add, update and delete audios.

GET /audiolist/{audiolist_edit_id}

```
{
    "type":"ALEdit",
    "new_audio":"http://...", (POST)
    "view_facet":"http://...", (GET)
    "contents":[{
        "audio":Audio,
        "update_audio":"http://...", (PUT)
        "delete_audio":"http://..." (DELETE)
    }]
}
```

"new_audio" request must contain the file (in an 'audio' parameter) which will be added to the audiolist.
It returns the created Audio.

"update_audio" request must contain the file (in an 'audio' parameter) which will replace the previous audio.
It returns the created Audio.

"delete_audio" returns "status":200 on success.
