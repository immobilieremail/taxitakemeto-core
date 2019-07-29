@extends('template')

@section('title')
    @lang('shell_message.title')
@endsection

@section('content')
    <br>
    <div>
        <?php $create_list_button = __('shell_message.create_list_button'); ?>
        {!! Form::open(['url' => "/$lang/shell/$shell_id"]) !!}
            {!! Form::submit($create_list_button) !!}
        {!! Form::close() !!}
    </div>
    <br>
    <div>
        <?php $isempty = 0; ?>
        <ul style="list-style:none">
            @for ($i = 0; isset($edits[$i]); $i++)
                <li>
                    {{ $i + 1 }} :
                    <a href=<?php echo "/$lang/upload-audio/" . $edits[$i]->id; ?>>{{ $edits[$i]->id }}</a>
                    <a href=<?php echo "/$lang/list-audio/" . $views[$i]->id; ?>>{{ $views[$i]->id }}</a>
                </li>
            @endfor
        </ul>
    </div>
@endsection