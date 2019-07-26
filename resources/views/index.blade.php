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
        <?php $isempty = 0; ?>
        <ul style="list-style:none">
            @for ($i = 0; isset($edits[$i]); $i++)
                <?php $isempty += 1; ?>
                <li>
                    {{ $isempty }} : <a href=<?php echo "/$lang/upload-audio/" . $edits[$i] ?>>{{ $edits[$i] }}</a>
                    <a href=<?php echo "/$lang/list-audio/" . $views[$i] ?>>{{ $views[$i] }}</a>
                </li>
            @endfor
        </ul>
    </div>
@endsection