@extends('layouts.app')

@section('content')
    <div class="container">
        {!! Form::open(['url' => 'share-audio?id=' . $id]) !!}
            Search by email
            {!! Form::text('share-to-email') !!}
            {!! Form::submit('Share') !!}
        {!! Form::close() !!}
    </div>
@endsection
