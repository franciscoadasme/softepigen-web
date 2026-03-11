<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        {{ $meta ?? '' }}

        <title>{{ $title ?? config('app.name', 'No Title') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link
            href="https://fonts.bunny.net/css?family=inter:100,200,300,400,500,600,700,800,900"
            rel="stylesheet"
        />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @if (config('services.turnstile.key'))
            <script
                src="https://challenges.cloudflare.com/turnstile/v0/api.js"
                async
                defer
            ></script>
        @endif
    </head>
    <body
        class="flex min-h-screen flex-col bg-[oklch(from_var(--color-primary)_calc(l+0.6)_calc(c-0.11)_h)] text-slate-900"
    >
        <div class="mx-auto mt-20 mb-8 w-[52rem] text-center">
            <h1 class="mb-2 text-4xl font-bold text-primary md:text-5xl">
                🧬Softepigen v2
            </h1>
            <p class="text-lg text-slate-600">
                Primers Design Web-Based Tool for MS-HRM Technique
            </p>
        </div>

        <main class="flex-grow">
            {{ $slot }}
        </main>

        <footer
            class="mt-20 bg-[oklch(from_var(--color-primary)_calc(l+0.55)_calc(c-0.11)_h)] py-10"
        >
            <div class="mx-auto flex w-[52rem] gap-10">
                <div class="flex-1">
                    <p class="text-sm text-slate-700">
                        Laboratorio de Bioinformática y Química Computacional
                        (LBQC)
                        <br />
                        Sala C101, Sector C
                        <br />
                        Universidad Católica del Maule
                        <br />
                        Avenida San Miguel 3605
                        <br />
                        Talca 3480112
                        <br />
                        Chile
                    </p>
                </div>
                <div
                    class="grid grid-cols-2 items-center justify-items-center gap-5"
                >
                    <img
                        src="{{ asset('images/logo_ucm.png') }}"
                        alt="Universidad Católica de Maule"
                        class="h-12"
                    />
                    <img
                        src="{{ asset('images/logo_udes.svg') }}"
                        alt="Universidad de Santander"
                        class="h-12"
                    />
                    <img
                        src="{{ asset('images/logo_cieam.png') }}"
                        alt="Centro de Investigación de Estudios Avanzados del Maule"
                        class="h-10"
                    />
                    <img
                        src="{{ asset('images/logo_lbqc.png') }}"
                        alt="Laboratorio de Bioinformática y Química Computacional"
                        class="h-10"
                    />
                </div>
            </div>
            <p class="mt-5 text-center text-sm text-slate-400">
                &copy; {{ date('Y') }} Softepigen v2. All rights reserved.
            </p>
        </footer>
    </body>
</html>
