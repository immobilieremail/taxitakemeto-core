@extends('template')

@section('title')
    MyApp
@endsection

@section('content')
    {!! Form::open(['url' => '/']) !!}
        {!! Form::submit('Create new list') !!}
    {!! Form::close() !!}
@endsection