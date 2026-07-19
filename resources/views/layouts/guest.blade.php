<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased">
    <div class="min-h-screen flex flex-col justify-center items-center px-4 py-10 bg-slate-50">
        <div class="mb-6">
            <a href="/">
                <x-application-logo class="w-16 h-16 fill-current text-indigo-600" />
            </a>
        </div>

        <div class="w-full sm:max-w-md px-6 py-6 sm:px-8 sm:py-8 bg-white border border-gray-200 shadow-sm overflow-hidden rounded-2xl">
            @yield('content')
        </div>
    </div>
</body>

</html>
