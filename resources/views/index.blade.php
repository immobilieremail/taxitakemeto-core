@extends('template')

@section('title')
    MyApp
@endsection

@section('content')
    {!! Form::open(['url' => 'user-new']) !!}
        {!! Form::submit('Create new user') !!}
    {!! Form::close() !!}
    <br>
    {!! Form::open(['url' => 'user-page']) !!}
        Search user
        {!! Form::text('user') !!}
        {!! Form::submit('Search') !!}
    {!! Form::close() !!}
@endsection