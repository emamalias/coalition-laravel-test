<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>{{ $title ?? config('app.name') }}</title>

        @vite('resources/css/app.css')
    </head>
    <body class="antialiased font-body bg-gray-200">

        <div class="min-h-screen flex w-full items-center justify-center">
            <div class="flex flex-col gap-5 justify-center w-full max-w-5xl m-auto bg-white rounded-2xl shadow-sm min-h-[500px] p-6">
                <div class="flex-1">
                    {{ $slot }}
                </div>
            </div>
        </div>
        @vite('resources/js/app.js')
    </body>
</html>
