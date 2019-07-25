@extends('template')

@section('title')
    List Audio
@endsection

@section('content')
    <br>
    <div>
        @include('includes.audio-player', ['lists' => $lists, 'delete' => false, 'edit_nbr' => 0, 'lang' => $lang])
    </div>
@endsection