<!doctype html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>إنشاء حساب - نظام أفاق الخليج</title>
    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.jpg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css'])
</head>

<body class="min-h-screen flex items-center justify-center bg-slate-50 px-4 py-10">
    <div class="w-full max-w-lg">
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8 sm:p-10">
            <div class="text-center mb-8">
                <img src="{{ asset('assets/img/logo.png') }}" alt="شعار أفاق الخليج" class="h-16 mx-auto mb-4 object-contain">
                <h1 class="text-xl font-bold text-gray-900">إنشاء حساب جديد</h1>
                <p class="text-sm text-gray-500 mt-1">أفاق الخليج للاستثمار والتجارة</p>
            </div>

            <x-validation-errors class="mb-4" />

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">الاسم الكامل</label>
                    <input id="name" type="text" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" name="name" value="{{ old('name') }}" required autofocus>
                </div>

                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-1.5">اسم المستخدم</label>
                    <input id="username" type="text" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" name="username" value="{{ old('username') }}" required>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">البريد الإلكتروني</label>
                    <input id="email" type="email" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" name="email" value="{{ old('email') }}" required>
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">كلمة المرور</label>
                    <input id="password" type="password" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" name="password" required>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">تأكيد كلمة المرور</label>
                    <input id="password_confirmation" type="password" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" name="password_confirmation" required>
                </div>

                @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                    <div class="flex items-start gap-2">
                        <input type="checkbox" class="mt-0.5 rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" id="terms" name="terms" required>
                        <label class="text-sm text-gray-600" for="terms">
                            {!! __('أوافق على :terms_of_service و :privacy_policy', [
                                'terms_of_service' => '<a href="'.route('terms.show').'" target="_blank" class="text-indigo-600 hover:underline">شروط الخدمة</a>',
                                'privacy_policy' => '<a href="'.route('policy.show').'" target="_blank" class="text-indigo-600 hover:underline">سياسة الخصوصية</a>',
                            ]) !!}
                        </label>
                    </div>
                @endif

                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors duration-150">
                    تسجيل الحساب
                </button>

                <div class="flex justify-center pt-1">
                    <a href="{{ route('login') }}" class="text-sm text-indigo-600 hover:underline">هل لديك حساب؟ تسجيل الدخول</a>
                </div>
            </form>
        </div>

        <p class="text-center mt-6 text-xs text-gray-400">&copy; {{ date('Y') }} أفاق الخليج. جميع الحقوق محفوظة.</p>
    </div>
</body>

</html>
