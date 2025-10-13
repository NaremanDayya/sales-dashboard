<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>نظام إدارة المبيعات - أفاق الخليج</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Tajawal Arabic Font -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    <style>
        body {
            font-family: 'Tajawal', sans-serif;
        }

        .beige-bg {
            background-color: #f8f4f0;
        }

        .hero-pattern {
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%231a4b8c' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .feature-card {
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        .blue-gradient {
            background: linear-gradient(135deg, #1a4b8c 0%, #2a6fd6 100%);
        }

        .red-accent {
            color: #BB2128;
        }

        .red-bg {
            background-color: #BB2128;
        }

        .blue-red-gradient {
            background: linear-gradient(135deg, #1a4b8c 0%, #BB2128 100%);
        }

        .border-red-accent {
            border-color: #BB2128;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col beige-bg text-gray-800 hero-pattern">
<!-- Header -->
<header class="sticky top-0 z-50 bg-white bg-opacity-90 shadow-sm">
    <nav class="container mx-auto px-4 py-3">
        <div class="flex justify-between items-center">
            <!-- Logo -->
            <div class="flex items-center space-x-2 space-x-reverse">
                <div>
                    <img src="{{asset('assets/img/logo.png')}}" class="h-20 w-50">
                </div>
            </div>

            <!-- Navigation -->
            <div class="flex items-center">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="blue-gradient text-white px-5 py-2.5 rounded-lg font-medium hover:opacity-90 transition duration-300 shadow-md">
                            لوحة التحكم
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="blue-gradient text-white px-5 py-2.5 rounded-lg font-medium hover:opacity-90 transition duration-300 shadow-md">
                            تسجيل الدخول
                        </a>
                    @endauth
                @endif
            </div>
        </div>
    </nav>
</header>

<!-- Hero Section -->
<main class="flex-grow flex items-center py-12">
    <div class="container mx-auto px-4">
        <div class="flex flex-col md:flex-row items-center justify-between">
            <!-- Text Content -->
            <div class="md:w-1/2 mb-10 md:mb-0 text-center md:text-right">
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                    <span class="text-[#1a4b8c]">نظام إدارة المبيعات المتكامل</span>
                </h1>
                <p class="text-xl md:text-2xl mb-8 text-gray-600 leading-relaxed">
                    منصة احترافية لتيسير عمليات المبيعات بين الإدارة ومندوبيها، من متابعة العملاء وإدارة الطلبات إلى تحليل الأداء وتحقيق الأهداف.
                </p>
                <div class="flex justify-center md:justify-start">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="blue-gradient text-white px-8 py-4 rounded-lg font-bold text-lg hover:opacity-90 transition duration-300 shadow-lg">
                                الانتقال إلى لوحة التحكم
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="blue-gradient text-white px-8 py-4 rounded-lg font-bold text-lg hover:opacity-90 transition duration-300 shadow-lg">
                                تسجيل الدخول
                            </a>
                        @endauth
                    @endif
                </div>
            </div>

            <!-- Visual Element -->
            <div class="md:w-1/2 flex justify-center">
                <div class="relative w-full max-w-md">
                    <div class="bg-white rounded-2xl p-8 shadow-xl animate-float border border-gray-100">
                        <div class="flex justify-center mb-6">
                            <div class="w-20 h-20 rounded-full bg-[#f8f4f0] flex items-center justify-center border-4 blue-gradient relative">
                                <i class="fas fa-chart-line text-3xl text-white"></i>
                                <div class="absolute -bottom-1 -right-1 w-6 h-6 rounded-full red-bg flex items-center justify-center">
                                    <i class="fas fa-bolt text-xs text-white"></i>
                                </div>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-center mb-4 text-[#1a4b8c]">تحليلات متقدمة</h3>
                        <p class="text-center text-gray-600">تتبع أداء فريق المبيعات وتحليل البيانات لتحسين النتائج</p>
                    </div>

                </div>
            </div>
        </div>
    </div>
</main>

<!-- Features Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl md:text-4xl font-bold text-center mb-12">
            <span class="text-[#1a4b8c]">مميزات النظام</span>
        </h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="bg-white rounded-2xl p-6 feature-card shadow-md border border-gray-100 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-2 h-full red-bg"></div>
                <div class="w-14 h-14 rounded-full bg-[#f8f4f0] flex items-center justify-center mb-4 blue-gradient">
                    <i class="fas fa-user-tie text-xl text-white"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-[#1a4b8c]">إدارة العملاء</h3>
                <p class="text-gray-600">إدارة قاعدة عملاء شاملة مع تتبع تفاعلاتهم وتاريخ مشترياتهم</p>
            </div>

            <!-- Feature 2 -->
            <div class="bg-white rounded-2xl p-6 feature-card shadow-md border border-gray-100 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-2 h-full red-bg"></div>
                <div class="w-14 h-14 rounded-full bg-[#f8f4f0] flex items-center justify-center mb-4 blue-gradient">
                    <i class="fas fa-chart-pie text-xl text-white"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-[#1a4b8c]">تقارير وأدوات تحليلية</h3>
                <p class="text-gray-600">تقارير تفصيلية وأدوات تحليلية متقدمة لاتخاذ القرارات المدروسة</p>
            </div>

            <!-- Feature 3 -->
            <div class="bg-white rounded-2xl p-6 feature-card shadow-md border border-gray-100 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-2 h-full red-bg"></div>
                <div class="w-14 h-14 rounded-full bg-[#f8f4f0] flex items-center justify-center mb-4 blue-gradient">
                    <i class="fas fa-mobile-alt text-xl text-white"></i>
                </div>
                <h3 class="text-xl font-bold mb-3 text-[#1a4b8c]">منصة متعددة الأجهزة</h3>
                <p class="text-gray-600">واجهة مستخدم متجاوبة تعمل بكفاءة على جميع الأجهزة والأنظمة</p>
            </div>
        </div>
    </div>
</section>

<script>
    // Simple animation on scroll
    document.addEventListener('DOMContentLoaded', function() {
        const featureCards = document.querySelectorAll('.feature-card');

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = 1;
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, { threshold: 0.1 });

        featureCards.forEach(card => {
            card.style.opacity = 0;
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            observer.observe(card);
        });
    });
</script>
</body>
</html>
