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
        <ul>
            @for ($i = 0; isset($edits[$i]); $i++)
                @if ($i == 0)
                    @lang('shell_message.edits')
                @endif
                <li>
                    {{ $i + 1 }} :
                    <a href=<?php echo "/$lang/upload-audio/" . $edits[$i]->id; ?>>{{ $edits[$i]->id }}</a>
                </li>
            @endfor
        </ul>
        <br>
        <ul>
            @for ($i = 0; isset($views[$i]); $i++)
                @if ($i == 0)
                    @lang('shell_message.views')
                @endif
                <li>
                    {{ $i + 1 }} :
                    <a href=<?php echo "/$lang/list-audio/" . $views[$i]->id; ?>>{{ $views[$i]->id }}</a>
                </li>
            @endfor
        </ul>
    </div>
@endsection