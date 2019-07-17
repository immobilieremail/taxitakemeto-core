@extends('template')

@section('title')
    List Audio
@endsection

@section('content')
    <div>
        <ul style="list-style: none;">
            <?php $isempty = true; ?>
            @foreach ($lists as $list)
                @if ($isempty == true)
                    <?php $isempty = false; ?>
                @endif
                @each('includes.audio-player', $list, 'audio')
            @endforeach
            @if ($isempty == true)
                It's empty.
            @endif
        </ul>
    </div>
@endsection