<div>
    {!! Form::open(['url' => "/$lang/audiolist_share/$edit"]) !!}
        {!! Form::text('dropbox'); !!}
        {!! Form::label('lbl', 'RO') !!}
        {!! Form::radio('result', 'RO', true) !!}
        {!! Form::label('lbl', 'RW') !!}
        {!! Form::radio('result', 'RW', false) !!}
        {!! Form::hidden('shell_id', $shell_id) !!}
        {!! Form::submit(__('uploadaudio_message.share_button')) !!}
    {!! Form::close() !!}
</div>