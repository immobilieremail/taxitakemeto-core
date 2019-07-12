<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf_8">
        <title>App Name - @yield('title')</title>
    </head>
    <body>
        <div>
            <a href="/">Go to Home</a><br>
            <a href="/upload-audio">Upload audio</a><br>
            <a href="/list-audio">List all audio</a><br>
        </div>
        <br>
        @yield('content')
    </body>
</html>