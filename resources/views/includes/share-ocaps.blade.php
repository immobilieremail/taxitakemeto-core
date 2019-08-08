<div>
    {!! Form::open(['url' => "/$lang/audiolist_share/$edit"]) !!}
        {!! Form::text('dropbox'); !!}
        {!! Form::label('result', 'RO') !!}
        {!! Form::radio('result', 'RO', true) !!}
        {!! Form::label('result', 'RW') !!}
        {!! Form::radio('result', 'RW', false) !!}
        {!! Form::submit(__('uploadaudio_message.share_button')) !!}
    {!! Form::close() !!}
</div>