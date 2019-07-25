@extends('template')

@section('title')
    @lang('uploadaudio_message.title')
@endsection

@section('content')
    <div>
        <a href=<?php echo "/$lang/list-audio/$view_nbr" ?>>@lang('uploadaudio_message.go_to_list')</a><br><br>
        @if ($validation_msg !== '')
            {{ $validation_msg }}<br><br>
        @endif
        <?php $add_button = __('uploadaudio_message.add_button'); ?>
        {!! Form::open(['url' => "/$lang/upload-audio/$edit_nbr", 'files' => true]) !!}
            {!! Form::file('audio') !!}
            {!! Form::submit($add_button) !!}
        {!! Form::close() !!}
    </div>
    <div>
        @include('includes.audio-player', ['lists' => $lists, 'delete' => true, 'edit_nbr' => $edit_nbr, 'lang' => $lang])
    </div>
    <div>
        <a href=<?php echo '/list-audio/' . $view_nbr ?>>Go to list</a><br><br>
        @if ($validation_msg !== '')
            {{ $validation_msg }}<br><br>
        @endif
        {!! Form::open(['url' => "upload-audio/$edit_nbr", 'files' => true]) !!}
            {!! Form::file('audio') !!}
            {!! Form::submit('Add') !!}
        {!! Form::close() !!}
    </div>
    <div>
        @include('includes.audio-player', ['lists' => $lists, 'delete' => true, 'edit_nbr' => $edit_nbr])
    </div>
@endsection