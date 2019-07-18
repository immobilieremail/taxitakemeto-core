<ul style="list-style: none;">
    <?php $isempty = true; ?>
    @foreach ($lists as $list)
        @if ($isempty == true)
            <?php $isempty = false; ?>
        @endif
        @foreach ($list as $audio)
            <li>
                <audio controls type="audio">
                    <source src={{ $audio->path }}>
                </audio>
                @if ($delete == true)
                    {!! Form::open(['url' => "upload-audio/$edit_nbr"]) !!}
                        {{ method_field('DELETE') }}
                        {!! Form::hidden('audio', $audio->id) !!}
                        {!! Form::submit('Delete') !!}
                    {!! Form::close() !!}
                    <br>
                @endif
            </li>
        @endforeach
    @endforeach
    @if ($isempty == true)
        It's empty.
    @endif
</ul>
