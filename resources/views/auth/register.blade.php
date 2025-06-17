    <!doctype html>
    <html lang="ar" dir="rtl">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>إنشاء حساب - نظام أفاق الخليج</title>

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

            .register-container {
                max-width: 500px;
                width: 100%;
                padding: 40px;
                background-color: white;
                border-radius: 12px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
                margin: 20px;
            }

            .register-header {
                text-align: center;
                margin-bottom: 30px;
            }

            .company-logo {
                height: 80px;
                margin: 0 auto 20px;
                display: block;
                object-fit: contain;
            }

            .register-title {
                font-size: 1.8rem;
                font-weight: 700;
                color: #1a4b8c;
                margin-bottom: 5px;
            }

            .company-name {
                color: #2a6fd6;
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

            .footer-links {
                display: flex;
                justify-content: center;
                margin-top: 20px;
                font-size: 0.9rem;
            }

            .footer-link {
                color: #1a4b8c;
                text-decoration: none;
            }

            .footer-link:hover {
                text-decoration: underline;
            }

            .form-check-label {
                font-size: 0.9rem;
                color: #4a5568;
            }

            .copyright {
                text-align: center;
                margin-top: 30px;
                font-size: 0.8rem;
                color: #718096;
            }
        </style>
    </head>

    <body>
        <div class="register-container">
            <div class="register-header">
                <img src="{{ asset('assets/img/logo.png') }}" alt="شعار أفاق الخليج" class="company-logo">
                <h1 class="register-title">إنشاء حساب جديد</h1>
                <p class="company-name">أفاق الخليج للاستثمار والتجارة</p>
            </div>

            <x-validation-errors class="mb-4" />

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">الاسم الكامل</label>
                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>
                </div>

                <div class="mb-3">
                    <label for="username" class="form-label">اسم المستخدم</label>
                    <input id="username" type="text" class="form-control" name="username" value="{{ old('username') }}" required>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">البريد الإلكتروني</label>
                    <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">كلمة المرور</label>
                    <input id="password" type="password" class="form-control" name="password" required>
                </div>

                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">تأكيد كلمة المرور</label>
                    <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" required>
                </div>

                @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="terms" name="terms" required>
                        <label class="form-check-label" for="terms">
                            {!! __('أوافق على :terms_of_service و :privacy_policy', [
                                'terms_of_service' => '<a href="'.route('terms.show').'" target="_blank" class="footer-link">شروط الخدمة</a>',
                                'privacy_policy' => '<a href="'.route('policy.show').'" target="_blank" class="footer-link">سياسة الخصوصية</a>',
                            ]) !!}
                        </label>
                    </div>
                @endif

                <button type="submit" class="btn btn-primary">تسجيل الحساب</button>

                <div class="footer-links mt-3">
                    <a href="{{ route('login') }}" class="footer-link">هل لديك حساب؟ تسجيل الدخول</a>
                </div>
            </form>

            <div class="copyright">
                &copy; {{ date('Y') }} أفاق الخليج. جميع الحقوق محفوظة.
            </div>
        </div>

        <!-- Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>

    </html>
