@extends('template')

@section('title')
    List Audio
@endsection

@section('content')
    <div>
        @include('includes.audio-player', ['lists' => $lists])
    </div>
@endsection