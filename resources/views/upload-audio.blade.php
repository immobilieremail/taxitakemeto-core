@extends('template')

@section('title')
    @lang('uploadaudio_message.title')
@endsection

@section('content')
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
        @if ($validation_msg != null)
            {{ $validation_msg }}<br><br>
        @endif
        <?php $add_button = __('uploadaudio_message.add_button'); ?>
        {!! Form::open(['url' => "/$lang/audiolist_edit/$edit_nbr/new_audio", 'files' => true]) !!}
            {!! Form::file('audio') !!}
            {!! Form::submit($add_button) !!}
        {!! Form::close() !!}
    </div>
    <div>
        @include('includes.audio-player', ['lists' => $lists, 'delete' => true, 'edit_nbr' => $edit_nbr, 'lang' => $lang])
    </div>
@endsection