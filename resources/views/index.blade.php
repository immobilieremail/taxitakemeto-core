@extends('template')

@section('title')
    @lang('index_message.title')
@endsection

@section('content')
    <br>
    <div>
        <?php $create_list_button = __('index_message.create_list_button'); ?>
        {!! Form::open(['url' => "/$lang"]) !!}
            {!! Form::submit($create_list_button) !!}
        {!! Form::close() !!}
    </div>
    <br><br>
    <div>
        <?php $isempty = 0 ?>
        <ul style="list-style:none">
            @foreach ($lists as $list)
                @if ($isempty == 0)
                    @lang('index_message.edits_path')
                @endif
                <?php $isempty += 1 ?>
                <li>{{ $isempty }} : <a href=<?php echo "/$lang/upload-audio/$list->id_edit" ?>>{{ $list->id_edit }}</a></li>
            @endforeach
        </ul>
    </div>
    <div>
        <?php $isempty = 0 ?>
        <ul style="list-style:none">
            @foreach ($lists as $list)
                @if ($isempty == 0)
                    @lang('index_message.views_path')
                @endif
                <?php $isempty += 1 ?>
                <li>{{ $isempty }} : <a href=<?php echo "/$lang/list-audio/$list->id_view" ?>>{{ $list->id_view }}</a></li>
            @endforeach
        </ul>
    </div>
@endsection