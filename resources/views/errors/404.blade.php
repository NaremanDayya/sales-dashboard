<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>الصفحة غير موجودة - 404</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap');

        * {
            font-family: 'Tajawal', sans-serif;
        }

        .floating {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        .bg-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .btn-hover {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .btn-hover:after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(-100%);
            transition: transform 0.3s ease;
        }

        .btn-hover:hover:after {
            transform: translateX(0);
        }

        .text-shadow {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
        }

        .particle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            animation: floatParticle 15s infinite linear;
        }

        @keyframes floatParticle {
            0% {
                transform: translateY(0) translateX(0) rotate(0deg);
                opacity: 1;
            }
            100% {
                transform: translateY(-500px) translateX(100px) rotate(360deg);
                opacity: 0;
            }
        }
    </style>
</head>
<body class="bg-gradient min-h-screen flex items-center justify-center px-4 sm:px-6 lg:px-8 overflow-hidden">
<!-- Background particles -->
<div id="particles"></div>

<div class="text-center max-w-3xl mx-auto py-12 relative z-10">
    <!-- Icon -->
    <div class="floating mb-8">
        <svg xmlns="http://www.w3.org/2000/svg"
             class="h-40 w-40 mx-auto text-white opacity-90"
             fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                  d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </div>

    <!-- Headings -->
    <h1 class="text-8xl md:text-9xl font-extrabold text-white mb-4 text-shadow">404</h1>
    <h2 class="text-3xl md:text-4xl font-semibold text-white mb-6 text-shadow">الصفحة غير موجودة</h2>

    <!-- Description -->
    <p class="text-lg md:text-xl text-white mb-10 max-w-2xl mx-auto leading-relaxed">
        عذراً، الصفحة التي تحاول الوصول إليها غير موجودة. قد تكون تم نقلها أو حذفها أو ربما قمت بكتابة العنوان بشكل خاطئ.
    </p>

    <!-- Buttons -->
    <div class="flex flex-col sm:flex-row justify-center gap-4">
        <a href="{{ route('dashboard') }}"
           class="px-8 py-4 bg-white hover:bg-gray-100 text-indigo-700 font-bold rounded-lg shadow-lg btn-hover transition-all duration-300 flex items-center justify-center">
            <i class="fas fa-home ml-2"></i>
            العودة إلى لوحة التحكم
        </a>
        <a href="{{ url()->previous() }}"
           class="px-8 py-4 bg-transparent border-2 border-white hover:bg-white hover:text-indigo-700 text-white font-bold rounded-lg shadow-sm btn-hover transition-all duration-300 flex items-center justify-center">
            <i class="fas fa-arrow-right ml-2"></i>
            الرجوع للخلف
        </a>
    </div>



    <!-- Footer -->
    <div class="mt-12 text-white opacity-70 pt-60">
        <p>© 2025 آفاق الخليج - جميع الحقوق محفوظة</p>
    </div>
</div>

<script>
    // Create background particles
    document.addEventListener('DOMContentLoaded', function() {
        const particlesContainer = document.getElementById('particles');
        const particleCount = 30;

        for (let i = 0; i < particleCount; i++) {
            const particle = document.createElement('div');
            particle.classList.add('particle');

            // Random properties
            const size = Math.random() * 20 + 5;
            const posX = Math.random() * 100;
            const posY = Math.random() * 100;
            const delay = Math.random() * 15;
            const duration = Math.random() * 10 + 15;

            particle.style.width = `${size}px`;
            particle.style.height = `${size}px`;
            particle.style.left = `${posX}vw`;
            particle.style.top = `${posY}vh`;
            particle.style.animationDelay = `${delay}s`;
            particle.style.animationDuration = `${duration}s`;

            particlesContainer.appendChild(particle);
        }
    });
</script>
</body>
</html>
