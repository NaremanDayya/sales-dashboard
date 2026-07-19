<!DOCTYPE html>
<html lang="ar" dir="rtl">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">

        <style>
            * { box-sizing: border-box; }
            html, body {
                margin: 0;
                min-height: 100vh;
                background: #f8fafc;
                color: #0f172a;
                font-family: 'Tajawal', ui-sans-serif, system-ui, sans-serif;
            }
            .wrap {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 1.5rem;
            }
            .card {
                width: 100%;
                max-width: 28rem;
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 1rem;
                box-shadow: 0 1px 3px 0 rgba(0,0,0,.06);
                padding: 2.5rem 2rem;
                text-align: center;
            }
            .code {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                min-width: 3.5rem;
                height: 3.5rem;
                padding: 0 1rem;
                border-radius: 9999px;
                background: #eef2ff;
                color: #4f46e5;
                font-weight: 800;
                font-size: 1.5rem;
                margin-bottom: 1.25rem;
            }
            .message {
                font-size: 1.125rem;
                font-weight: 700;
                color: #111827;
                margin: 0 0 .5rem;
            }
            .hint {
                font-size: .875rem;
                color: #6b7280;
                margin: 0 0 1.75rem;
            }
            .actions { display: flex; gap: .75rem; justify-content: center; flex-wrap: wrap; }
            .btn {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: .625rem 1.25rem;
                border-radius: .625rem;
                font-size: .875rem;
                font-weight: 600;
                text-decoration: none;
                transition: background-color .15s ease;
            }
            .btn-primary { background: #4f46e5; color: #fff; }
            .btn-primary:hover { background: #4338ca; }
            .btn-secondary { background: #f3f4f6; color: #374151; }
            .btn-secondary:hover { background: #e5e7eb; }
        </style>
    </head>
    <body>
        <div class="wrap">
            <div class="card">
                <span class="code">@yield('code')</span>
                <p class="message">@yield('message')</p>
                <p class="hint">عذراً، حدث خطأ أثناء معالجة طلبك. حاول مرة أخرى أو عد إلى الرئيسية.</p>
                <div class="actions">
                    <a href="{{ url('/') }}" class="btn btn-primary">الصفحة الرئيسية</a>
                    <a href="{{ url()->previous() }}" class="btn btn-secondary">الرجوع للخلف</a>
                </div>
            </div>
        </div>
    </body>
</html>
