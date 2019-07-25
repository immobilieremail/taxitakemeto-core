<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf_8">
        <title>@lang('template_message.title') - @yield('title')</title>
    </head>
    <body>
        <div>
            <a href="/">@lang('template_message.go_to_index')</a><br>
        </div>
        @yield('content')
    </body>
</html>