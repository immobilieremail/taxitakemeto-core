<ul style="list-style: none;">
    <?php $isempty = true; ?>
    @foreach ($lists as $list)
        @if ($isempty == true)
            <?php $isempty = false; ?>
        @endif
        @foreach ($list as $audio)
            <li>
                <div>
                    <audio controls type="audio">
                        <source src={{ $audio->path }}>
                    </audio>
                    @if ($delete == true)
                        {!! Form::open(['url' => "upload-audio/$edit_nbr/$audio->id", 'files' => true]) !!}
                            {{ method_field('PATCH') }}
                            {!! Form::file('audio') !!}
                            {!! Form::submit('Update') !!}
                        {!! Form::close() !!}
                        {!! Form::open(['url' => "upload-audio/$edit_nbr/$audio->id"]) !!}
                            {{ method_field('DELETE') }}
                            {!! Form::hidden('audio_path', $audio->path) !!}
                            {!! Form::submit('Delete') !!}
                        {!! Form::close() !!}
                        <br>
                    @endif
                </div>
            </li>
            <br>
        @endforeach
    @endforeach
    @if ($isempty == true)
        It's empty.
    @endif
</ul>
