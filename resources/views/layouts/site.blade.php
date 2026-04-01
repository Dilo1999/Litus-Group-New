<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'LITUS Group' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon/LG-Favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/favicon/LG-Favicon.png') }}">
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

