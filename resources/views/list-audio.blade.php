@extends('layouts.app')

@section('content')
    <div class="container">
        <ul style="list-style: none;">
            <?php $isempty = true; ?>
            @foreach(Storage::disk('local')->allFiles() as $file)
                @if (strpos($file, 'public/uploads/') !== false)
                    <?php $isempty = false; ?>
                    <li>
                        {{-- {!! Form::open(['url' => 'list-audio']) !!}
                            {!! Form::text('audio-name', $file) !!}
                        {!! Form::close() !!} --}}
                        <?php $path = str_replace('public/uploads/', 'storage/uploads/', $file); ?>
                        <audio controls type="audio">
                            <source src={{ $path }}>
                        </audio>
                    </li>
                @endif
            @endforeach
            @if ($isempty == true)
                It's empty.
            @endif
        </ul>
    </div>
@endsection