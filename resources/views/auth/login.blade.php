<!doctype html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تسجيل الدخول - نظام أفاق الخليج</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.jpg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen flex items-center justify-center bg-slate-50 px-4 py-10">
    <div class="w-full max-w-md">
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8 sm:p-10">
            <div class="text-center mb-8">
                <img src="{{ asset('assets/img/logo.png') }}" alt="شعار أفاق الخليج" class="h-16 mx-auto mb-4 object-contain">
                <h1 class="text-xl font-bold text-gray-900">نظام إدارة المبيعات</h1>
                <p class="text-sm text-gray-500 mt-1">أفاق الخليج للاستثمار والتجارة</p>
            </div>

            <x-validation-errors class="mb-4" />

            @if (session('status'))
                <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" autocomplete="off" class="space-y-5">
                @csrf

                <!-- Hidden fake fields to trick browsers -->
                <input type="text" name="fake_username" style="display:none">
                <input type="password" name="fake_password" style="display:none">

                <!-- Email Input -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">البريد الإلكتروني</label>
                    <input type="email"
                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           autofocus
                           autocomplete="username">
                </div>

                <!-- Password Input -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">كلمة المرور</label>
                    <div class="relative">
                        <input type="password"
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm pe-10"
                               id="password"
                               name="password"
                               required
                               autocomplete="current-password"
                               readonly
                               onfocus="this.removeAttribute('readonly')">
                        <span class="absolute inset-y-0 start-3 flex items-center cursor-pointer text-gray-400 hover:text-gray-600" onclick="togglePassword()">👁️</span>
                    </div>
                </div>

                <!-- Remember Me Checkbox -->
                <div class="flex items-center gap-2">
                    <input type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" id="remember" name="remember">
                    <label class="text-sm text-gray-600" for="remember">تذكرني</label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors duration-150">
                    تسجيل الدخول
                </button>
            </form>
        </div>

        <p class="text-center mt-6 text-xs text-gray-400">&copy; {{ date('Y') }} أفاق الخليج. جميع الحقوق محفوظة.</p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const passwordField = document.getElementById('password');
                if (passwordField) {
                    passwordField.setAttribute('autocomplete', 'new-password');
                    passwordField.value = '';
                }
            }, 100);
        });
        function togglePassword() {
            const passwordField = document.getElementById('password');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
            } else {
                passwordField.type = 'password';
            }
        }
    </script>
</body>

</html>
