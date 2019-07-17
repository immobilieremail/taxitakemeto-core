@extends('template')

@section('title')
    Upload Audio
@endsection

@section('content')
    @if ($validation_msg !== '')
        {{ $validation_msg }}<br><br>
    @endif
    {!! Form::open(['url' => "upload-audio/$nbr", 'files' => true]) !!}
        {!! Form::file('audio') !!}
        {!! Form::submit('Envoyer') !!}
    {!! Form::close() !!}
@endsection