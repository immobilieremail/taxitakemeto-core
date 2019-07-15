@extends('layouts.app')

@section('content')
    <div class="container">
        <ul style="list-style: none;">
            <?php $isempty = true; ?>
                @foreach ($audios as $audio)
                    @if (File::exists($audio->path))
                        <?php $isempty = false; ?>
                        <li>
                            <?php echo $audio->name; ?><br>
                            <audio controls type="audio">
                                <source src=<?php echo $audio->path; ?>>
                            </audio>
                            @if ($id == $audio->owner_id)
                                <a href=<?php echo '/share-audio?id=' . $audio->audio_id ?>>Share</a>
                            @endif
                        </li>
                    @endif
                @endforeach
            @if ($isempty == true)
                It's empty.
            @endif
        </ul>
    </div>
@endsection