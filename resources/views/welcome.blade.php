<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>نظام إدارة المبيعات - أفاق الخليج</title>

    <!-- Bootstrap 5 RTL CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet" />
<link rel="preload" href="https://ghg-sales.com/assets/img/background-sales3.webp" as="image">

    <!-- Tajawal Arabic Font -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet" />

    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            background-color: white;
        }

        .navbar-brand {
            font-weight: bold;
            display: flex;
            align-items: center;
            color: #BB2128;
            font-size: 1.25rem;
        }

        .navbar-brand img {
	    max-height: 60px;
            margin-left: 10px;
        }

        .welcome-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 0 20px;
	    background-image: url("{{ asset('assets/img/background-sales3.webp') }}");            ;
background-color: rgba(184, 12, 12, 0.3);            
background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-blend-mode: overlay;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .welcome-container h1 {
            font-size: 3rem;
            color: #f7f7f7;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .welcome-container p {
            font-size: 1.25rem;
            color: #f5f5f5;
            max-width: 600px;
            margin-bottom: 2rem;
            line-height: 1.5;
        }

        .btn-primary,
        .btn-outline-primary {
            min-width: 120px;
            font-weight: 600;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 0.95rem;
        }

        .btn-primary {
            background-color: #BB2128;
            border-color: #BB2128;
        }

        .btn-primary:hover {
            background-color: #14427c;
            border-color: #14427c;
        }

        .btn-outline-primary {
            color: #BB2128;
            border-color: #BB2128;
        }

        .btn-outline-primary:hover {
            background-color: #BB2128;
            color: white;
        }
    </style>
</head>

<body>
    <!-- Header -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <!-- Logo and Brand -->
            <a class="navbar-brand" href="#">
                <img src="{{ asset('assets/img/logo.png') }}" alt="شعار أفاق الخليج" />
            </a>

            <!-- Toggle for mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="تبديل التنقل">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Navbar Links -->
            <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
                <!-- Right side (empty or could be links later) -->
                <ul class="navbar-nav"></ul>

                <!-- Left side: Auth buttons -->
                <ul class="navbar-nav">
                    @if (Route::has('login'))
                    @auth
                    <li class="nav-item">
                        <a href="{{ url('/dashboard') }}" class="btn btn-primary">لوحة التحكم</a>
                    </li>
                    @else
                    <li class="nav-item me-2">
                        <a href="{{ route('login') }}" class="btn btn-primary">تسجيل الدخول</a>
                    </li>

                    @endauth
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    <!-- Welcome Section -->
    <div class="welcome-container">
        <h1>مرحباً بكم في نظام إدارة المبيعات</h1>
        <p>
            منصة احترافية لتيسير عمليات المبيعات بين الإدارة ومندوبيها، من متابعة العملاء وإدارة الطلبات إلى تحليل
            الأداء وتحقيق الأهداف.
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
