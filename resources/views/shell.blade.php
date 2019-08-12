@extends('template')

@section('title')
    @lang('shell_message.title')
@endsection

@section('content')
    <br>
    <div>
        {!! Form::open(['url' => route('shell.new_audio_list', [$lang, $shell_id])]) !!}
            {!! Form::submit(__('shell_message.create_list_button')) !!}
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
                    <a href=<?php echo "/$lang/audiolist_edit/" . $edits[$i]["id_facet"]; ?>>{{ $edits[$i]["id_facet"] }}</a>
                    @include('includes.share-ocaps', ['edit' => $edits[$i]["id_facet"], 'lang' => $lang, 'shell_id' => $shell_id])
                    <br>
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
                    <a href=<?php echo "/$lang/list-audio/" . $views[$i]["id_facet"]; ?>>{{ $views[$i]["id_facet"] }}</a>
                </li>
            @endfor
        </ul>
        <br>
        <ul>
            @for ($i = 0; isset($dropbox[$i]); $i++)
                <li>
                    {{ $i + 1 }} : {{ $dropbox[$i]["type"] }} | {{ $dropbox[$i]["capability"] }}
                    {!! Form::open(['url' => "/$lang/shell/$shell_id/" . $dropbox[$i]["swiss_number"] . "/accept"]) !!}
                        {!! Form::submit('Accept') !!}
                    {!! Form::close() !!}
                </li>
                <br>
            @endfor
        </ul>
    </div>
@endsection