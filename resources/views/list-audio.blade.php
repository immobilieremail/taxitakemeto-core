@extends('template')

@section('title')
    List Audio
@endsection

@section('content')
    <br>
    <div>
        @include('includes.audio-player', ['lists' => $lists])
    </div>
@endsection