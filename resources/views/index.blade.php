@extends('template')

@section('title')
    MyApp
@endsection

@section('content')
    Content
    @lang('auth.failed')

    <div class="col">
        <div class="btn-group dropup">
            <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ __('Langue') }}
            </button>
            <div class="dropdown-menu">
                @foreach(config('app.locales') as $locale)
                    <a class="dropdown-item @if($locale == session('locale')) active @endif" href="{{ route('language', $locale) }}">
                        {{ $locale }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endsection