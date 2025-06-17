<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap');

        body {
            font-family: 'Tajawal', sans-serif;
            direction: rtl;
            color: #2d3748;
            background-color: #f8fafc;
            margin: 0;
            padding: 20px;
        }

        .card {
            border: none;
            border-radius: 12px;
            padding: 0;
            margin: 0 auto;
            background: white;
            max-width: 800px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
            color: white;
            padding: 25px 30px;
            font-size: 22px;
            font-weight: 700;
            position: relative;
        }

        .header:after {
            content: "";
            position: absolute;
            bottom: -10px;
            right: 0;
            width: 100%;
            height: 20px;
            background: linear-gradient(135deg, transparent 25%, white 25%, white 75%, transparent 75%),
                        linear-gradient(135deg, transparent 25%, white 25%, white 75%, transparent 75%);
            background-size: 20px 20px;
        }

        .content {
            padding: 30px;
        }

        .section {
            display: flex;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #edf2f7;
            align-items: center;
        }

        .section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .label {
            font-weight: 600;
            color: #4a5568;
            background: #f7fafc;
            padding: 8px 15px;
            border-radius: 6px;
            min-width: 150px;
            text-align: center;
            margin-left: 20px;
            font-size: 15px;
        }

        .value {
            font-weight: 500;
            font-size: 16px;
            flex-grow: 1;
            color: #2d3748;
        }

        .total-section {
            background: #f0fdf4;
            padding: 20px;
            border-radius: 10px;
            margin-top: 30px;
            border: 1px solid #dcfce7;
        }

        .total-section .label {
            background: #22c55e;
            color: white;
            margin-left: 0;
        }

        .total-section .value {
            font-weight: 700;
            color: #166534;
            font-size: 18px;
        }

        .dates {
            display: flex;
            gap: 20px;
            margin-top: 30px;
        }

        .date-box {
            flex: 1;
            background: #f8fafc;
            border-radius: 8px;
            padding: 15px;
            text-align: center;
            border: 1px solid #e2e8f0;
        }

        .date-label {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 5px;
        }

        .date-value {
            font-weight: 600;
            font-size: 16px;
            color: #1e293b;
        }

        .logo {
            position: absolute;
            left: 30px;
            top: 50%;
            transform: translateY(-50%);
            height: 40px;
        }
    </style>
</head>
<body>

<div class="card">
    <div class="header">
        تفاصيل العقد
        <img src="https://via.placeholder.com/150x40?text=Company+Logo" alt="Logo" class="logo">
    </div>

    <div class="content">
        <div class="section">
            <span class="label">اسم العميل</span>
            <span class="value">{{ $agreement->client->company_name }}</span>
        </div>

        <div class="section">
            <span class="label">مندوب المبيعات</span>
            <span class="value">{{ $agreement->salesRep->name }}</span>
        </div>

        <div class="section">
            <span class="label">الخدمة</span>
            <span class="value">{{ $agreement->service->name }}</span>
        </div>

        <div class="dates">
            <div class="date-box">
                <div class="date-label">تاريخ التوقيع</div>
                <div class="date-value">{{ $agreement->signing_date->format('Y-m-d') }}</div>
            </div>

            <div class="date-box">
                <div class="date-label">مدة العقد</div>
                <div class="date-value">{{ $agreement->duration_years }} سنوات</div>
            </div>

            <div class="date-box">
                <div class="date-label">تاريخ الانتهاء</div>
                <div class="date-value">{{ optional($agreement->end_date)->format('Y-m-d') ?? '---' }}</div>
            </div>
        </div>

        <div class="section total-section">
            <span class="label">المبلغ الإجمالي</span>
            <span class="value">{{ number_format($agreement->total_amount, 2) }} درهم إماراتي</span>
        </div>
    </div>
</div>

</body>
</html>
