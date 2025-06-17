    <!doctype html>
    <html lang="ar" dir="rtl">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>تسجيل الدخول - نظام أفاق الخليج</title>

        <!-- Bootstrap 5 RTL CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">

        <!-- Tajawal Arabic Font -->
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

        <!-- Custom Styles -->
        <style>
            body {
                background-color: #f8f9fa;
                font-family: 'Tajawal', sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
            }

            .login-container {
                max-width: 450px;
                width: 100%;
                padding: 40px;
                background-color: white;
                border-radius: 12px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                margin: 20px;
            }

            .login-header {
                text-align: center;
                margin-bottom: 30px;
            }

               .company-logo {
                height: 80px;
                margin: 0 auto 20px;
                display: block;
                object-fit: contain;
               }

            .login-title {
                font-size: 1.8rem;
                font-weight: 700;
                color: #1a4b8c; /* Afaq Alkhaleej blue */
                margin-bottom: 5px;
            }

            .company-name {
                color: #2a6fd6; /* Lighter blue */
                font-size: 1.1rem;
                margin-bottom: 30px;
            }

            .form-control {
                border-radius: 8px;
                padding: 12px 15px;
                border: 1px solid #e2e8f0;
            }

            .form-control:focus {
                border-color: #1a4b8c;
                box-shadow: 0 0 0 0.25rem rgba(26, 75, 140, 0.25);
            }

            .btn-primary {
                background-color: #1a4b8c;
                border-color: #1a4b8c;
                padding: 12px;
                border-radius: 8px;
                font-weight: 600;
                width: 100%;
            }

            .btn-primary:hover {
                background-color: #14427c;
                border-color: #14427c;
            }

            .form-check-label {
                font-size: 0.9rem;
                color: #4a5568;
            }

            .footer-links {
                display: flex;
                justify-content: space-between;
                margin-top: 25px;
                font-size: 0.9rem;
            }

            .footer-link {
                color: #1a4b8c;
                text-decoration: none;
            }

            .footer-link:hover {
                text-decoration: underline;
            }

            .copyright {
                text-align: center;
                margin-top: 30px;
                font-size: 0.8rem;
                color: #718096;
            }

            .password-toggle {
                position: absolute;
                left: 15px;
                top: 50%;
                transform: translateY(-50%);
                cursor: pointer;
                color: #4a5568;
            }
        </style>
    </head>

    <body>
        <div class="login-container">
            <div class="login-header">
                <img src="{{ asset('assets/img/logo.png') }}" alt="شعار أفاق الخليج" class="company-logo">
                <h1 class="login-title">نظام إدارة المبيعات</h1>
                <p class="company-name">أفاق الخليج للاستثمار والتجارة</p>
            </div>

            <x-validation-errors class="mb-4" />

            @if (session('status'))
                <div class="alert alert-success mb-4">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Input -->
                <div class="mb-4 position-relative">
                    <label for="email" class="form-label">البريد الإلكتروني</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required autofocus>
                </div>

                <!-- Password Input -->
                <div class="mb-4 position-relative">
                    <label for="password" class="form-label">كلمة المرور</label>
                    <div class="position-relative">
                        <input type="password" class="form-control" id="password" name="password" required>
                        <span class="password-toggle" onclick="togglePassword()">👁️</span>
                    </div>
                </div>

                <!-- Remember Me Checkbox -->
                <div class="mb-4 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">تذكرني</label>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary">تسجيل الدخول</button>

                <div class="footer-links">
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="footer-link">نسيت كلمة المرور؟</a>
                    @endif

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="footer-link">إنشاء حساب جديد</a>
                    @endif
                </div>
            </form>

            <div class="copyright">
                &copy; {{ date('Y') }} أفاق الخليج. جميع الحقوق محفوظة.
            </div>
        </div>

        <!-- Bootstrap 5 JS Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <script>
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

