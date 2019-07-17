@extends('layouts.app')

@section('content')
    <div>
        @include('includes.audio-player', ['lists' => $lists])
    </div>
@endsection