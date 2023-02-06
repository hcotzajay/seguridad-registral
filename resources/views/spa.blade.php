<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, minimal-ui">
    <link rel="icon" type="image/png" href="{{ logo_icon() }}">

    <title>{{ env('APP_NAME') }}</title>
</head>
<body>
<div id="app">
    @php (session_start())

    @if (!isset($_SESSION['id_usuario']))
        <login
            app-name="{{ env('APP_NAME') }}"
            name-server-aplicaciones="{{ env('NAME_SERVER_APLICACIONES') }}"
            app-env-color="{{ env('APP_ENV_COLOR') }}"
            logo="{{ logo_login() }}"
        ></login>
    @else
        <work-area
            app-name="{{ env('APP_NAME')}}"
            name-server-aplicaciones="{{ env('NAME_SERVER_APLICACIONES') }}"
            app-env-color="{{ env('APP_ENV_COLOR') }}"
            logo="{{ logo_work_area() }}"
        ></work-area>
    @endif
    <vue-progress-bar></vue-progress-bar>
</div>
<script src="{{ mix('js/app.js') }}"></script>
</body>
</html>
