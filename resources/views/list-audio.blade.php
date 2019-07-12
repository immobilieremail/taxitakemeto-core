@extends('template')

@section('title')
    List Audio
@endsection

@section('content')
    List Content
    <br>
    <div>
        <ul style="list-style: none;">
            <?php $isempty = true; ?>
            @foreach(Storage::disk('local')->allFiles() as $file)
                @if (strpos($file, 'public/uploads/') !== false)
                    <?php $isempty = false; ?>
                    <li>
                        {{ $file }}<br>
                        <?php $path = str_replace('public/uploads/', 'storage/uploads/', $file); ?>
                        <audio controls type="audio">
                            <source src={{ $path }}>
                        </audio>
                    </li>
                @endif
            @endforeach
            @if ($isempty == true)
                It's empty.
            @endif
        </ul>
    </div>
@endsection