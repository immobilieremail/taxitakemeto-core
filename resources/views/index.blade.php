@extends('template')

@section('title')
    @lang('index_message.title')
@endsection

@section('content')
    <br>
    <div>
        {!! Form::open(['url' => 'api/audiolist']) !!}
            {!! Form::submit('Create AudioList') !!}
        {!! Form::close() !!}
    </div>
    <br>
    <div>
        @for ($i = 0; isset($edits[$i]); $i++)
            @if ($i == 0)
                Edit
            @endif
            <li>
                {{ $i + 1 }} :
                <a href=<?php echo '/api/audiolist/' . $edits[$i]["swiss_number"] . '/edit'; ?>>{{ $edits[$i]["swiss_number"] }}</a>
                {!! Form::open(['url' => 'api/audiolist/' . $edits[$i]["swiss_number"] . '/audio', 'files' => true]) !!}
                    {!! Form::file('audio') !!}
                    {!! Form::submit('Add audio file') !!}
                {!! Form::close() !!}
                <br>
            </li>
        @endfor
    </div>
    <br>
    <div>
        @for ($i = 0; isset($views[$i]); $i++)
            @if ($i == 0)
                View
            @endif
            <li>
                {{ $i + 1 }} :
                <a href=<?php echo '/api/audiolist/' . $views[$i]["swiss_number"]; ?>>{{ $edits[$i]["swiss_number"] }}</a>
                <br>
            </li>
        @endfor
    </div>
@endsection