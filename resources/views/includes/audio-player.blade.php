<ul style="list-style: none;">
    <?php $isempty = true; ?>
    @foreach ($lists as $list)
        @if ($isempty == true)
            <?php $isempty = false; ?>
        @endif
        <li>
            <div>
                <audio controls type="audio">
                    <source src={{ $list->path }}>
                </audio>
                {{-- If it is not in view --}}
                @if ($delete == true)
                    {!! Form::open(['url' => "/$lang/upload-audio/$edit_nbr/$list->swiss_number", 'files' => true]) !!}
                        {{ method_field('PATCH') }}
                        {!! Form::file('audio') !!}
                        {!! Form::submit(__('uploadaudio_message.update_button')) !!}
                    {!! Form::close() !!}
                    {!! Form::open(['url' => "/$lang/upload-audio/$edit_nbr/$list->swiss_number"]) !!}
                        {{ method_field('DELETE') }}
                        {!! Form::hidden('audio_path', $list->path) !!}
                        {!! Form::submit(__('uploadaudio_message.update_button')) !!}
                    {!! Form::close() !!}
                    <br>
                @endif
            </div>
        </li>
        <br>
    @endforeach
    @if ($isempty == true)
        @lang('uploadaudio_message.empty')
    @endif
</ul>
