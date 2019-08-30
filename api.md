# Entry Points

POST /audiolist
Create an empty audiolist and return the audiolist edition facet (AudioListEdit or ALEdit)

```
{
    "type":"ocap",
    "ocapType":"ALEdit",
    "url":"http://.../api/audiolist_edit/{audiolist_edit_id}"
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

GET /audiolist_view/{audiolist_view_id}

```
{
   "type":"ALView",
    "contents":[Audio, ...]
}
```

## AudioListEdit

An AudioListEdit (or ALEdit) is a facet of an audiolist.
It is the Read Write access of the audiolist : it lists all audios of the audiolist and gives the urls to add, update and delete audios.

GET /audiolist_edit/{audiolist_edit_id}

```
{
    "type":"ALEdit",
    "delete":"http://...", (DELETE)
    "new_audio":"http://...", (POST)
    "view_facet":"http://...", (GET)
    "contents":[{
        "audio":Audio,
        "update_audio":"http://...", (POST)
        "delete_audio":"http://..." (DELETE)
    }]
}
```

"new_audio" request must contain the file (in an 'audio' variable) which will be added to the audiolist.
It returns the created Audio.

"update_audio" request must contain the file (in an 'audio' variable) which will replace the previous audio.
It returns the created Audio.

"delete" and "delete_audio" returns 200 on success.
