@extends('layouts.app')

@section('content')
    <div class="container">
        @if ($validation_msg !== '')
            {{ $validation_msg }}<br><br>
        @endif
        {!! Form::open(['url' => 'upload-audio', 'files' => true]) !!}
            {!! Form::file('audio') !!}
            {!! Form::submit('Send') !!}
        {!! Form::close() !!}
    </div>
@endsection