@extends('template')

@section('title')
    @lang('uploadaudio_message.title')
@endsection

@section('content')
    @if ($view_nbr != NULL)
        <a href=<?php echo "/$lang/list-audio/$view_nbr" ?>>@lang('uploadaudio_message.go_to_list')</a>
    @endif
    <br><br><br>
    <div>
        <?php $share_button = __('uploadaudio_message.share_button'); ?>
        {!! Form::open(['url' => "/$lang/upload-audio/$edit_nbr/share"]) !!}
            {!! Form::text('share_to') !!}
            {!! Form::label('view', 'View') !!}
            {!! Form::checkbox('view', 'view', true) !!}
            {!! Form::label('edit', 'Edit') !!}
            {!! Form::checkbox('edit', 'edit') !!}
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
        @if ($view_nbr != NULL)
            @include('includes.audio-player', ['lists' => $lists, 'delete' => true, 'edit_nbr' => $edit_nbr, 'lang' => $lang])
        @endif
    </div>
@endsection