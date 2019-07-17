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
                <li>
                    @foreach ($list as $audio)
                        <audio controls type="audio">
                            <source src={{ $audio->path }}>
                        </audio>
                    @endforeach
                </li>
            @endforeach
            @if ($isempty == true)
                It's empty.
            @endif
        </ul>
    </div>
@endsection