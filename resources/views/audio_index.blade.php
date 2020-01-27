@extends('template')

@section('title')
    @lang('index_message.title')
@endsection

@section('content')
    <br>
    <div>
        @for ($i = 0; isset($audios[$i]); $i++)
            <li>
                {{ $i + 1 }} : {{ $audios[$i]["audio_id"] }}
                <br>
                <audio controls>
                    <source src={{ $audios[$i]["path_to_file"] }} type="audio/mpeg">
                </audio>
                {!! Form::open(['url' => 'api/audiolist/' . $edit_id . '/audio/' . $audios[$i]["audio_id"], 'files' => true]) !!}
                    {!! method_field('PUT') !!}
                    {!! Form::file('audio') !!}
                    {!! Form::submit('Update audio file') !!}
                {!! Form::close() !!}
                {!! Form::open(['url' => 'api/audiolist/' . $edit_id . '/audio/' . $audios[$i]["audio_id"]]) !!}
                    {!! method_field('DELETE') !!}
                    {!! Form::submit('Delete audio file') !!}
                {!! Form::close() !!}
                <br>
            </li>
        @endfor
    </div>
@endsection