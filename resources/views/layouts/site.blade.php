<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#061634">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon/LG-Favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon/LG-Favicon.png') }}">
    {!! app(\App\Services\SeoService::class)->headHtml() !!}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @stack('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-[100] focus:rounded-full focus:bg-white focus:px-5 focus:py-3 focus:text-sm focus:font-semibold focus:text-ink-900 focus:shadow-xl">Skip to content</a>
    <x-site.navbar />
    <main id="main">
        @yield('content')
    </main>
    <x-site.footer />
</body>
</html>
