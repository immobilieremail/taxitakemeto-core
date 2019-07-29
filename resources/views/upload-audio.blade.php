@extends('template')

@section('title')
    @lang('uploadaudio_message.title')
@endsection

@section('content')
    <a href=<?php echo "/$lang/list-audio/$view_nbr" ?>>@lang('uploadaudio_message.go_to_list')</a>
    <br><br><br>
    <div>
        <?php $share_button = __('uploadaudio_message.share_button'); ?>
        {!! Form::open(['url' => "/$lang/upload-audio/$edit_nbr/share"]) !!}
            {!! Form::text('share_to') !!}
            {!! Form::submit($share_button) !!}
        {!! Form::close() !!}
    </div>
    <br>
    <div>
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
@endsection