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
                <?php $delete_button = __('uploadaudio_message.delete_button');
                    $update_button = __('uploadaudio_message.update_button'); ?>
                    {!! Form::open(['url' => "/$lang/upload-audio/$edit_nbr/$list->id", 'files' => true]) !!}
                        {{ method_field('PATCH') }}
                        {!! Form::file('audio') !!}
                        {!! Form::submit($update_button) !!}
                    {!! Form::close() !!}
                    {!! Form::open(['url' => "/$lang/upload-audio/$edit_nbr/$list->id"]) !!}
                        {{ method_field('DELETE') }}
                        {!! Form::hidden('audio_path', $list->path) !!}
                        {!! Form::submit($delete_button) !!}
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
