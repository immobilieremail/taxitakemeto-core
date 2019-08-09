@extends('template')

@section('title')
    @lang('uploadaudio_message.title')
@endsection

@section('content')
    <br><br><br>
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