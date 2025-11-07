<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />

        <title>{{ $title ?? config('app.name', 'No Title') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link
            href="https://fonts.bunny.net/css?family=inter:100,200,300,400,500,600,700,800,900"
            rel="stylesheet"
        />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-slate-100 text-slate-900">
        <div class="mx-auto mt-20 mb-8 w-[52rem] text-center">
            <h1 class="mb-2 text-4xl font-bold md:text-5xl">🧬Softepigen v2</h1>
            <p class="text-lg text-slate-600">
                Primers Design Web-Based Tool for MS-HRM Technique
            </p>
        </div>

        {{ $slot }}
    </body>
</html>
