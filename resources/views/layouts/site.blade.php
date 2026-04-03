<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('images/favicon/LG-Favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon/LG-Favicon.png') }}">
    {!! app(\App\Services\SeoService::class)->headHtml() !!}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen">
    <x-site.navbar />
    <main>
        @yield('content')
    </main>
    <x-site.footer />
</body>
</html>

