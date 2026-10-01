<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تقرير الاتفاقيات</title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'dejavu sans', sans-serif;
            direction: rtl;
            text-align: right;
            color: #1f2937;
            font-size: 11px;
        }

        .header {
            text-align: center;
            margin-bottom: 14px;
        }

        .header h1 {
            font-size: 16px;
            margin: 0 0 4px 0;
        }

        .header p {
            font-size: 10px;
            color: #6b7280;
            margin: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background-color: #f3f4f6;
            color: #374151;
            font-weight: bold;
            padding: 6px 4px;
            border: 1px solid #d1d5db;
            text-align: center;
        }

        tbody td {
            padding: 5px 4px;
            border: 1px solid #e5e7eb;
            text-align: center;
            vertical-align: middle;
        }

        tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        .logo-img {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            object-fit: contain;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 8px;
            font-size: 9px;
        }

        .badge-active { background-color: #ecfdf5; color: #059669; }
        .badge-finished { background-color: #fee2e2; color: #dc2626; }
        .badge-sent { background-color: #ecfdf5; color: #059669; }
        .badge-not-sent { background-color: #fee2e2; color: #dc2626; }

        .footer {
            margin-top: 16px;
            text-align: center;
            font-size: 9px;
            color: #9ca3af;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>تقرير الاتفاقيات</h1>
        <p>مجموعة آفاق الخليج - {{ now()->format('Y-m-d') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                @if(in_array('client_logo', $selectedColumns)) <th>شعار العميل</th> @endif
                @if(in_array('client_name', $selectedColumns)) <th>العميل</th> @endif
                @if(in_array('sales_Rep_name', $selectedColumns)) <th>سفير العلامة التجارية</th> @endif
                @if(in_array('signing_date', $selectedColumns)) <th>توقيع الاتفاقية</th> @endif
                @if(in_array('duration_years', $selectedColumns)) <th>مدة الاتفاقية</th> @endif
                @if(in_array('termination_type', $selectedColumns)) <th>إنهاء الاتفاقية</th> @endif
                @if(in_array('implementation_date', $selectedColumns)) <th>تنفيذ الاتفاقية</th> @endif
                @if(in_array('end_date', $selectedColumns)) <th>انتهاء الاتفاقية</th> @endif
                @if(in_array('status', $selectedColumns)) <th>حالة الاتفاقية</th> @endif
                @if(in_array('notice_months', $selectedColumns)) <th>أشهر الإخطار</th> @endif
                @if(in_array('notice_info', $selectedColumns)) <th>الإخطار</th> @endif
                @if(in_array('service_type', $selectedColumns)) <th>الخدمة</th> @endif
                @if(in_array('product_quantity', $selectedColumns)) <th>عدد المنتج</th> @endif
                @if(in_array('price', $selectedColumns)) <th>التسعيرة</th> @endif
                @if(in_array('total_amount', $selectedColumns)) <th>المجموع</th> @endif
            </tr>
        </thead>
        <tbody>
            @forelse($agreements as $agreement)
                @php
                    $duration = $agreement->statusDuration();
                    $requiredNoticeDate = $agreement->getRequiredNoticeDate();
                    $noticeIsLate = $agreement->isNoticedAtTime() === false;
                @endphp
                <tr>
                    @if(in_array('client_logo', $selectedColumns))
                        <td>
                            @if($agreement->client?->company_logo)
                                <img src="{{ $agreement->client->company_logo }}" class="logo-img">
                            @else
                                —
                            @endif
                        </td>
                    @endif
                    @if(in_array('client_name', $selectedColumns))
                        <td>{{ $agreement->client?->company_name ?? '—' }}</td>
                    @endif
                    @if(in_array('sales_Rep_name', $selectedColumns))
                        <td>{{ $agreement->salesRep?->name ?? '—' }}</td>
                    @endif
                    @if(in_array('signing_date', $selectedColumns))
                        <td>{{ optional($agreement->signing_date)->format('Y-m-d') ?? '—' }}</td>
                    @endif
                    @if(in_array('duration_years', $selectedColumns))
                        <td>{{ $agreement->duration_years ?? '—' }} سنوات</td>
                    @endif
                    @if(in_array('termination_type', $selectedColumns))
                        <td>
                            @if($agreement->termination_type === 'returnable')
                                مشروطة بمقابل
                            @elseif($agreement->termination_type === 'non_returnable')
                                غير مشروطة بمقابل
                            @else
                                —
                            @endif
                        </td>
                    @endif
                    @if(in_array('implementation_date', $selectedColumns))
                        <td>{{ optional($agreement->implementation_date)->format('Y-m-d') ?? '—' }}</td>
                    @endif
                    @if(in_array('end_date', $selectedColumns))
                        <td>{{ optional($agreement->end_date)->format('Y-m-d') ?? '—' }}</td>
                    @endif
                    @if(in_array('status', $selectedColumns))
                        <td>
                            <span class="badge {{ $duration['finished'] ? 'badge-finished' : 'badge-active' }}">
                                {{ $duration['label'] }}: {{ $duration['years'] }} سنة، {{ $duration['months'] }} شهر، {{ $duration['days'] }} يوم
                            </span>
                        </td>
                    @endif
                    @if(in_array('notice_months', $selectedColumns))
                        <td>{{ $agreement->notice_months ?? '—' }}</td>
                    @endif
                    @if(in_array('notice_info', $selectedColumns))
                        <td>
                            {{ $requiredNoticeDate->format('Y-m-d') }}<br>
                            <span class="badge {{ $agreement->notice_status === 'sent' ? 'badge-sent' : 'badge-not-sent' }}">
                                {{ $agreement->notice_status === 'sent' ? 'تم الإخطار' : 'لم يتم الإخطار' }}
                            </span>
                        </td>
                    @endif
                    @if(in_array('service_type', $selectedColumns))
                        <td>{{ $agreement->service?->name ?? '—' }}</td>
                    @endif
                    @if(in_array('product_quantity', $selectedColumns))
                        <td>{{ $agreement->product_quantity ?? '—' }}</td>
                    @endif
                    @if(in_array('price', $selectedColumns))
                        <td>{{ number_format($agreement->price ?? 0) }}</td>
                    @endif
                    @if(in_array('total_amount', $selectedColumns))
                        <td>{{ number_format($agreement->total_amount ?? 0) }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="15">لا توجد اتفاقيات</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        جميع الحقوق محفوظة &copy; شركة آفاق الخليج {{ date('Y') }}
    </div>

</body>
</html>
