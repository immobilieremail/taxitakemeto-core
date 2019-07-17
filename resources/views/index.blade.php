@extends('template')

@section('title')
    MyApp
@endsection

@section('content')
    <br>
    <div>
        {!! Form::open(['url' => '/']) !!}
            {!! Form::submit('Create new list') !!}
        {!! Form::close() !!}
    </div>
    <br><br>
    <div>
        <?php $isempty = 0 ?>
        <ul style="list-style:none">
            @foreach ($lists as $list)
                @if ($isempty == 0)
                    Edit paths
                @endif
                <?php $isempty += 1 ?>
                <li>{{ $isempty }} : <a href=<?php echo 'upload-audio/' . $list->id_edit ?>>{{ $list->id_edit }}</a></li>
            @endforeach
        </ul>
    </div>
    <div>
        <?php $isempty = 0 ?>
        <ul style="list-style:none">
            @foreach ($lists as $list)
                @if ($isempty == 0)
                    View paths
                @endif
                <?php $isempty += 1 ?>
                <li>{{ $isempty }} : <a href=<?php echo 'list-audio/' . $list->id_view ?>>{{ $list->id_view }}</a></li>
            @endforeach
        </ul>
    </div>
@endsection