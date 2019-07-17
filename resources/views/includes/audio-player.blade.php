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
            </li>
        @endforeach
    @endforeach
    @if ($isempty == true)
        It's empty.
    @endif
</ul>
