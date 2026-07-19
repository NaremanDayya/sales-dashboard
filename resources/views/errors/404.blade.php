<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الصفحة غير موجودة - 404</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        * { font-family: 'Tajawal', ui-sans-serif, system-ui, sans-serif; }
        .floating { animation: float 6s ease-in-out infinite; }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-16px); }
            100% { transform: translateY(0px); }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8 bg-slate-50">

<div class="text-center max-w-xl mx-auto py-12">
    <div class="floating mb-8 inline-flex h-28 w-28 items-center justify-center rounded-full bg-indigo-50">
        <svg xmlns="http://www.w3.org/2000/svg"
             class="h-14 w-14 text-indigo-500"
             fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </div>

    <h1 class="text-7xl md:text-8xl font-extrabold text-gray-900 mb-3">404</h1>
    <h2 class="text-xl md:text-2xl font-semibold text-gray-700 mb-4">الصفحة غير موجودة</h2>

    <p class="text-gray-500 mb-10 leading-relaxed">
        عذراً، الصفحة التي تحاول الوصول إليها غير موجودة. قد تكون تم نقلها أو حذفها أو ربما قمت بكتابة العنوان بشكل خاطئ.
    </p>

    <div class="flex flex-col sm:flex-row justify-center gap-3">
        <a href="{{ route('dashboard') }}"
           class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            العودة إلى لوحة التحكم
        </a>
        <a href="{{ url()->previous() }}"
           class="px-6 py-3 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold rounded-lg transition-colors duration-200 flex items-center justify-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            الرجوع للخلف
        </a>
    </div>

    <p class="mt-12 text-sm text-gray-400">© {{ now()->year }} آفاق الخليج - جميع الحقوق محفوظة</p>
</div>

</body>
</html>
