@extends('template')

@section('title')
    @lang('index_message.title')
@endsection

@section('content')
    <br>
    <div>
        <?php $create_list_button = __('index_message.create_shell_button'); ?>
        {!! Form::open(['url' => "/$lang"]) !!}
            {!! Form::submit($create_list_button) !!}
        {!! Form::close() !!}
    </div>
    <br>
    <div>
        <?php $isempty = 0; ?>
        <ul style="list-style:none">
            @foreach ($shells as $shell)
                <?php $isempty += 1; ?>
                <li>
                    {{ $isempty }} : <a href=<?php echo "/$lang/shell/$shell->id"; ?>>{{ $shell->id }}</a>
                </li>
            @endforeach
        </ul>
    </div>
@endsection