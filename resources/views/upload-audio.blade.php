@extends('template')

@section('title')
    Upload Audio
@endsection

@section('content')
    {!! Form::open(['url' => 'upload-audio', 'files' => true]) !!}
        {!! Form::file('audio') !!}
        {!! Form::submit('Envoyer') !!}
    {!! Form::close() !!}
@endsection