<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />

        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>

        <link href="{{ asset('css/app.css') }}" rel="stylesheet">

        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon-16x16.png') }}">
        <link rel="manifest" href="{{ asset('site.webmanifest') }}">
        <link rel="mask-icon" href="{{ asset('safari-pinned-tab.svg') }}" color="#524EF2">
        <meta name="msapplication-TileColor" content="#252f3f">
        <meta name="theme-color" content="#ffffff">
    </head>
    <body class="antialiased bg-theme-light-blue h-screen">
        <div id="app" class="h-full flex flex-col">
            <div class="flex-grow-0">
                <navigation-bar></navigation-bar>
            </div>
            <main class="flex-grow">
                <div class="max-w-screen-2xl h-full mx-auto py-4 sm:py-10 px-4 sm:px-6 lg:px-8">
                    {{ $slot }}
                </div>
            </main>
        </div>

        <script src="{{ asset('js/app.js') }}" defer></script>
    </body>
</html>
