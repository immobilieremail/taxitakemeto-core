@extends('template')

@section('title')
    @lang('index_message.title')
@endsection

@section('content')
    <br>
    <div>
        {!! Form::open(['url' => action('ShellController@store', $lang)]) !!}
            {!! Form::submit(__('index_message.create_shell_button')) !!}
        {!! Form::close() !!}
    </div>
    <br>
    <div>
        <?php $isempty = 0; ?>
        <ul style="list-style:none">
            @foreach ($shells as $shell)
                <?php $isempty += 1; ?>
                <li>
                    {{ $isempty }} : <a href=<?php echo "/$lang/shell/$shell->swiss_number"; ?>>{{ $shell->swiss_number }}</a>
                </li>
            @endforeach
        </ul>
    </div>
@endsection