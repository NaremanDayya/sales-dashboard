@extends('layouts.table')
@section('title','جدول العمولات')
@push('styles')
<style>
    :root {
        --primary: #4f46e5;
        --primary-light: #6366f1;
        --secondary: #10b981;
        --danger: #ef4444;
        --warning: #f59e0b;
        --gray-100: #f8fafc;
        --gray-200: #e2e8f0;
        --gray-300: #cbd5e1;
        --gray-400: #94a3b8;
        --gray-500: #64748b;
        --gray-600: #475569;
        --gray-700: #334155;
        --gray-800: #1e293b;
        --gray-900: #0f172a;
    }
    .dropdown {
        position: relative;
        display: inline-block;
    }

    .btn-dropdown {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        background: #ffffff;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        color: #374151;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-dropdown:hover {
        background: #f9fafb;
        border-color: #9ca3af;
    }

    .btn-dropdown svg {
        transition: transform 0.2s;
    }

    .dropdown-menu {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        min-width: 180px;
        margin-top: 4px;
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        z-index: 1000;
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 8px;
        width: 100%;
        padding: 8px 12px;
        text-align: left;
        color: #374151;
        font-size: 14px;
        background: none;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }

    .dropdown-item:hover {
        background: #f9fafb;
    }

    .dropdown-icon {
        color: #6b7280;
    }

    /* Show dropdown when active */
    .dropdown.active .btn-dropdown svg {
        transform: rotate(180deg);
    }

    .dropdown.active .dropdown-menu {
        display: block;
    }
    .pdf-export-table {
        border: 2px solid #000 !important;
        border-collapse: collapse !important;
    }

    .pdf-export-table th,
    .pdf-export-table td {
        border: 2px solid #000 !important;
        padding: 8px !important;
        text-align: center !important;
    }

    .pdf-export-table thead th {
        background-color: #f0f0f0 !important;
        border-bottom: 3px solid #000 !important;
        font-weight: bold !important;
    }
    /* Ensure dropdown stays above other elements */
    .export-options {
        position: relative;
        z-index: 1000;
    }
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
        font-family: 'Tajawal', sans-serif;
    }

    body {
        background-color: var(--gray-100);
        color: var(--gray-800);
        direction: rtl;
        padding: 20px;
    }

    .table-container {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin: 1rem;
    }

    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.25rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .table-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1e293b;
  }

    .table-actions {
        display: flex;
        gap: 10px;
    }

    .btn {
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 500;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
    }

    .btn-primary {
        background-color: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background-color: var(--primary-light);
        transform: translateY(-1px);
    }

    .btn-outline {
        background-color: white;
        color: var(--primary);
        border: 1px solid var(--gray-300);
    }

    .btn-outline:hover {
        background-color: var(--gray-100);
    }

    .table-filters {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        background-color: var(--gray-100);
        border-bottom: 1px solid var(--gray-200);
    }
 .search-box {
        position: relative;
        width: 300px;
    }

    .search-input {
        width: 100%;
        padding: 8px 40px 8px 15px;
        border-radius: 6px;
        border: 1px solid var(--gray-300);
        font-size: 14px;
        transition: all 0.2s ease;
    }

    .search-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
    }

    .search-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray-400);
    }

    .table-responsive {
        width: 100%;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 800px;
        border: 1px solid var(--gray-300);
    }

    .data-table thead th {
        background-color: var(--gray-100);
        color: var(--gray-600);
        font-weight: 600;
        padding: 12px 15px;
        text-align: center;
        border-bottom: 2px solid var(--gray-300);
        border-right: 1px solid var(--gray-300);
        position: sticky;
        top: 0;
    }

    .data-table W
tbody tr {
    transition: background-color 0.2s ease;
        page-break-inside: avoid;
        break-inside: avoid;
        border-bottom: 1px solid var(--gray-300);
    }

    .data-table tbody tr:hover {
        background-color: var(--gray-100);
    }




    .data-table tbody td {
        padding: 12px 15px;
        border-bottom: 1px solid var(--gray-300);
        text-align: center;
        vertical-align: middle;
        border-right: 1px solid var(--gray-300);
}

    .status-badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .status-active {
        background-color: #ecfdf5;
        color: #059669;
    }

    .status-pending {
        background-color: #fffbeb;
        color: #d97706;
    }

    .status-inactive {
        background-color: #fee2e2;
        color: #dc2626;
    }

    .action-btns {
        display: flex;
        gap: 8px;
    }

    .action-btn {
        width: 30px;
        height: 30px;
        border-radius: 6px;
        display: flex;
   align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        background-color: transparent;
        border: none;
        color: var(--gray-500);
    }

    .action-btn:hover {
        background-color: var(--gray-200);
        color: var(--gray-700);
    }

    .action-btn.edit:hover {
        color: var(--primary);
    }

 .table-bordered td {
    text-align: center;
    padding: 10px;
    background-color: #fefefe;
    font-weight: 800;
    font-size: 14px;
}
   .action-btn.delete:hover {
        color: var(--danger);
    }

    .pagination {
        display: flex;
        justify-content: center;
        padding: 20px;
        border-top: 1px solid var(--gray-200);
    }

    .pagination-btn {
        width: 36px;
        height: 36px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 4px;
        cursor: pointer;
        transition: all 0.2s ease;
        background-color: transparent;
        border: 1px solid var(--gray-300);
        color: var(--gray-600);
    }

    .pagination-btn:hover:not(.disabled) {
        background-color: var(--gray-100);
    }

    .pagination-btn.active {
        background-color: var(--primary);
        color: white;
        border-color: var(--primary);
    }
  .pagination-btn.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .sidebar {
        display: none !important;
    }

    .empty-state {
        padding: 40px 20px;
        text-align: center;
        color: var(--gray-500);
    }

    .empty-icon {
        font-size: 48px;
        color: var(--gray-300);
        margin-bottom: 15px;
    }

    .empty-text {
        font-size: 16px;
        margin-bottom: 20px;
    }


    @media (max-width: 768px) {
        .table-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }

        .search-box {
            width: 100%;
        }
    }

    .print-header,
    .print-footer {
        display: none;
    }
 /* Show only in print/PDF */
    .pdf-header,
    .pdf-footer {
        display: block;
        width: 100%;
        text-align: center;
        font-weight: bold;
        font-size: 18px;
        padding: 10px 0;
        border-bottom: 2px solid #333;
        border-top: 2px solid #333;
        background-color: #f5f5f5;
        color: #222;
        position: relative;
        margin-bottom: 20px;
        margin-top: 20px;
        font-family: "Tajawal", sans-serif;
    }

    .pdf-footer {
        border-top: 2px solid #333;
        border-bottom: none;
        margin-top: 40px;
    }

    .header-content {
        direction: rtl;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        font-weight: bold;
        font-size: 20px;
        color: #222;
    }

    .header-text {
        color: #4154f1;
    }

    .header-logo {
        max-height: 60px;
        width: auto;
        object-fit: contain;
    }

    @media print {

        /* Hide everything outside print-area */
        body * {
            visibility: hidden;
        }
 #print-area,
        #print-area * {
            visibility: visible;
        }

        /* Position print-area at top left and full width */
        #print-area {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            padding: 20px;
            background: #fff;
            font-size: 14px;
            color: #000;
        }

        /* Hide UI controls */
        #print-area .table-actions,
        #print-area .table-filters,
        #print-area .pagination,
        #print-area .btn,
        #print-area select,
        #print-area input,
        #print-area .search-box,
        #print-area .search-icon,
        #print-area .fas.fa-plus,
        #print-area .fas.fa-download,
        #print-area .fas.fa-print {
            display: none !important;
        }

        /* Style the table */
        #print-area table.data-table {
            width: 100% !important;
            border-collapse: collapse;
        }

        #print-area table.data-table th,
        #print-area table.data-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        #print-area table.data-table thead {
            background-color: #f0f0f0;
        }

        /* Prevent page breaks inside rows */
        #print-area tr {
            page-break-inside: avoid;
        }
 /* Show footer in print */
        #print-area .pdf-footer {
            display: block !important;
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #444;
        }

        /* RTL support */
        #print-area {
            direction: rtl;
            width: 100% !important
        }

        .no-print {
            display: none !important;
        }

        .pdf-header {
            display: block !important;
        }

        /* Optional: remove shadows, rounded borders in print for better clarity */
        .pdf-header .header-content {
            box-shadow: none !important;
            border-radius: 0 !important;
        }


        .body {
            margin: 0;
            padding: 0;
        }

        .header-logo {
            max-height: 80px;
        }

 .commission-paid {
        background-color: #dcfce7;
        color: #166534;
    }

    .commission-unpaid {
        background-color: #ffedd5;
        color: #9a3412;
    }

    .commission-partial {
        background-color: #fef9c3;
        color: #854d0e;

    }
.commission-table td {
    text-align: center;
    vertical-align: middle;
    padding: 12px;
    font-size: 14px;
    color: #333;
    background-color: #f9f9f9;
    border: 1px solid #dee2e6;
}
</style>
@endpush
<body>
@section('content')
<div id="print-area" class="table-container">
    <div class="table-header">
        <h2 id="title" class="table-title">
            عمولات سفير العلامة التجارية: {{ $salesRep->name }}
            @if($selectedMonth)
شهر - {{$selectedMonth}}
            @endif
        </h2>
        <div class="table-actions d-flex align-items-center gap-2">
            @isset($salesRep)
                <div class="bg-indigo-100 text-indigo-800 px-4 py-2 rounded-full" style="font-size:14px; font-weight:800;">
                    سفير العلامة التجارية: {{ $salesRep->name }}
                </div>
            @endisset
            <div class="export-options">
                <div class="dropdown">
                    <button class="btn btn-dropdown" id="exportBtn" type="button">
                        تصدير البيانات
                        <svg width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 1L6 6L11 1" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </button>
                    <div class="dropdown-menu" id="exportDropdown">
                        <button class="dropdown-item" data-type="excel">
                            <svg class="dropdown-icon" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" stroke="currentColor" stroke-width="2"/>
                                <polyline points="14,2 14,8 20,8" stroke="currentColor" stroke-width="2"/>
                                <path d="M16 13H8" stroke="currentColor" stroke-width="2"/>
                                <path d="M16 17H8" stroke="currentColor" stroke-width="2"/>
                                <path d="M10 9H8" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            تصدير كملف Excel
                        </button>
                        <button class="dropdown-item" data-type="pdf">
                            <svg class="dropdown-icon" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M4 7V17C4 18.1046 4.89543 19 6 19H18C19.1046 19 20 18.1046 20 17V7M4 7H20M4 7L6 4H18L20 7"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                            تصدير كملف PDF
                        </button>
                    </div>
                </div>
            </div>
            <button class="btn btn-outline" onclick="window.print()" title="طباعة التقرير">
                <i class="fas fa-print"></i>
            </button>
        </div>
    </div>

    <div class="table-filters">
        <div class="search-box">
            <input type="text" class="search-input" placeholder="بحث..." id="searchInput">
            <i class="fas fa-search search-icon"></i>
        </div>

        <div>
            <div class="d-flex align-items-center mb-3 gap-2">
                <i class="fas fa-filter text-secondary"></i>

                {{-- Month Filter --}}
                <select id="monthSelect" class="form-select w-auto" onchange="applyMonthFilter()">
                    <option value="">الشهر: الكل</option>
                    @for ($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                    @endfor
                </select>

                {{-- Year Filter --}}
                <select id="yearSelect" class="form-select w-auto" onchange="applyYearFilter()">
                    @for ($year = now()->year - 4; $year <= now()->year + 2; $year++)
                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                            السنة: {{ $year }}
                        </option>
                    @endfor
                </select>
            </div>
        </div>
    </div>

    <div class="table-responsive" dir="rtl">
        <div class="pdf-content">
            <div class="pdf-header" style="display: none;">
                <div class="header-content d-flex align-items-center justify-content-between flex-wrap mb-4 p-3 shadow rounded bg-white">
                    <div class="d-flex flex-column align-items-center text-center mx-auto">
                        <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="header-logo mb-2" />
                    </div>
                </div>
            </div>
 <table class="table table-bordered">
        <thead>
            <tr class="text-center">
                <th>نوع الخدمة</th>
                <th>العدد المحقق سنويا</th>
        	<th> العدد المحقق للشهر الحالي </th>
	        <th>نسبة العمولة</th>
                @for ($month = 1; $month <= 12; $month++)
                    <th>{{ $month }}</th>
                @endfor
                <th>المجموع الكلي السنوي</th>
                <th>حالة الصرف للشهر الحالي</th>
                @if(Auth::user()->role === 'admin')
                <th>إجراءات</th>
                @endif
            </tr>
        </thead>
<tbody>
@foreach($Commissions as $commission)
    @php
        // Use selected month or current month
        $displayMonth = $selectedMonth ?: date('n');
        $displayMonthCommission = $commission["month_commission_$displayMonth"] ?? 0;
        $displayMonthPaid = $commission["payment_status_month_$displayMonth"] ?? false;
        $hasDisplayMonthCommission = is_numeric($displayMonthCommission) && $displayMonthCommission > 0;
        $achievedTarget = $commission['achieved_percentage'] >= 90;
        $commissionId = $commission["commission_id_month_$displayMonth"] ?? null;
    @endphp

    <tr>
        <td>{{ $commission['service_type'] }}</td>
        <td>{{ number_format($commission['total_achieved_amount'], 0) }}</td>
        <td>{{ number_format($commission['month_achieved_amount'], 0) }}</td>
        <td class="px-4 py-3 text-sm">
            <div class="flex items-center justify-center space-x-1">
                @if($commission['calculation_type'] === 'item')
                    <span class="font-medium text-blue-600" style="font-size: 14px; font-weight: 700;">
                    {{ number_format($commission['item_fee'], 0) }}
                </span>
                    <span class="text-gray-500" style="font-size: 14px; font-weight: 700;">&nbsp;&nbsp;ر.س/وحدة</span>
                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                @else
                    <span class="font-medium text-purple-600" style="font-size: 14px; font-weight: 700;">
                    {{ rtrim(rtrim(number_format($commission['commission_rate'], 2, '.', ''), '0'), '.') }}%
                </span>
                @endif
            </div>
        </td>

        @for ($month = 1; $month <= 12; $month++)
            <td class="{{ $month == $displayMonth ? 'border-2 border-blue-500' : '' }} {{ $commission["payment_status_month_$month"] ? 'bg-success text-white' : '' }}">
                @if(is_numeric($commission["month_commission_$month"]))
                    @if($commission["month_commission_$month"] == 0)
                        0
                    @else
                        {{ number_format($commission["month_commission_$month"], 0) }} ر.س
                    @endif
                @elseif(!empty($commission["month_commission_$month"]))
                    {{ $commission["month_commission_$month"] }}
                @else
                    0
                @endif
            </td>
        @endfor

        <td>{{ number_format($commission['total_commission'], 0) }}</td>

        <td class="text-center">
            @if($displayMonthPaid)
                <span class="badge bg-success py-1 px-2">تم الصرف</span>
            @elseif($hasDisplayMonthCommission)
                <span class="badge bg-warning py-1 px-2">متاح للصرف</span>
            @else
                <span class="badge bg-secondary py-1 px-2">لا يوجد عمولة</span>
            @endif
        </td>

        @if(Auth::user()->role === 'admin')
            <td class="text-center">
                @if($hasDisplayMonthCommission && !$displayMonthPaid && $achievedTarget && $commissionId)
                    <button onclick="payCommission('{{ $commissionId }}', {{ $displayMonth }})"
                            class="btn btn-xs btn-primary py-0 px-2" style="font-size: 0.75rem;">
                        صرف عمولة الشهر
                    </button>
                @elseif($displayMonthPaid)
                    <span class="badge bg-success py-1 px-2">تم الصرف</span>
                @else
                    <span class="text-muted small">لا يستحق عمولة</span>
                @endif
            </td>
        @endif
    </tr>
@endforeach
</tbody>
    </table>
            <div class="pdf-footer" style="display: none;">
                <p>جميع الحقوق محفوظة &copy; شركة آفاق الخليج {{ date('Y') }}</p>
            </div>
        </div>
    </div>

    <div class="pagination" id="pagination"></div>
</div>

    @push('scripts')
<script>
    // Commissions data from Laravel
    const commissionsData = @json($Commissions);
    const isAdmin = @json(Auth::user()->role === 'admin');

    // Render table function
function renderTable(data = commissionsData) {
    const tbody = document.getElementById('tableBody');
    const isAdmin = @json(Auth::user()->role === 'admin');
    const currentMonth = new Date().getMonth() + 1; // Gets current month (1-12)
    const currentYear = new Date().getFullYear(); // Gets current year

    tbody.innerHTML = '';

    if (!data || data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="${isAdmin ? 18 : 17}" class="empty-state text-center">
                    <div class="empty-icon">
                        <i class="fas fa-users-slash"></i>
                    </div>
                    <div class="empty-text">لا توجد بيانات متاحة</div>
                </td>
            </tr>
        `;
        return;
    }

    data.forEach(commission => {
        // Get current month's data
        const currentMonthCommission = commission[`month_commission_${currentMonth}`] || 0;
        const currentMonthPaid = commission[`payment_status_month_${currentMonth}`] === true ||
                               commission[`payment_status_month_${currentMonth}`] === 1;

        const hasCommission = currentMonthCommission !== '-' &&
                            currentMonthCommission !== 0 &&
                            !isNaN(currentMonthCommission);

        // Status determination
        let statusText, statusClass;

        if (!hasCommission) {
            statusText = 'لا يوجد عمولة';
            statusClass = 'bg-gray-100 text-gray-800';
        } else if (currentMonthPaid) {
            statusText = 'مدفوعة';
            statusClass = 'bg-green-100 text-green-800';
        } else {
            statusText = 'متاحة للصرف';
            statusClass = 'bg-yellow-100 text-yellow-800';
        }

        // Admin action button
        let actionButton = '';
        if (isAdmin) {
            if (hasCommission && !currentMonthPaid) {
                actionButton = `
                    <button onclick="payCommission('${commission.id}', ${currentMonth}, ${currentYear})"
                        class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-400">
                        صرف العمولة
                    </button>
                `;
            } else if (hasCommission && currentMonthPaid) {
                actionButton = `
                    <span class="px-3 py-1 bg-gray-200 text-gray-700 rounded">
                        تم الصرف
                    </span>
                `;
            } else {
                actionButton = `
                    <span class="px-3 py-1 bg-gray-200 text-gray-700 rounded">
                        غير متاح
                    </span>
                `;
            }
        }

        // Create row HTML
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50 text-center';
        row.innerHTML = `
            <!-- Your other table cells here -->

            <!-- Status Cell -->
            <td class="px-4 py-3 text-sm text-center">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${statusClass}">
                    ${statusText}
                </span>
            </td>

            <!-- Action Button (for admin) -->
            ${isAdmin ? `
            <td class="px-4 py-3 text-sm text-center">
                ${actionButton}
            </td>
            ` : ''}
        `;

        tbody.appendChild(row);
    });

    // Debug output
}
    // Apply year filter
    function applyYearFilter() {
        const year = document.getElementById('yearSelect').value;
        window.location.href = window.location.pathname + '?year=' + year;
    }
    function applyMonthFilter() {
        const month = document.getElementById('monthSelect').value;
        const year = document.getElementById('yearSelect').value;

        // Create URL with query parameters
        const url = new URL(window.location.href);

        if (month) {
            url.searchParams.set('month', month);
        } else {
            url.searchParams.delete('month');
        }

        if (year) {
            url.searchParams.set('year', year);
        } else {
            url.searchParams.delete('year');
        }

        // Redirect to the new URL
        window.location.href = url.toString();
    }

    function applyYearFilter() {
        const year = document.getElementById('yearSelect').value;
        const month = document.getElementById('monthSelect').value;

        // Create URL with query parameters
        const url = new URL(window.location.href);

        if (year) {
            url.searchParams.set('year', year);
        } else {
            url.searchParams.delete('year');
        }

        if (month) {
            url.searchParams.set('month', month);
        } else {
            url.searchParams.delete('month');
        }

        // Redirect to the new URL
        window.location.href = url.toString();
    }function applyMonthFilter() {
        const month = document.getElementById('monthSelect').value;
        const year = document.getElementById('yearSelect').value;

        // Create URL with query parameters
        const url = new URL(window.location.href);

        if (month) {
            url.searchParams.set('month', month);
        } else {
            url.searchParams.delete('month');
        }

        if (year) {
            url.searchParams.set('year', year);
        } else {
            url.searchParams.delete('year');
        }

        // Redirect to the new URL
        window.location.href = url.toString();
    }

    // Apply year filter (updated to preserve month)
    function applyYearFilter() {
        const year = document.getElementById('yearSelect').value;
        const month = document.getElementById('monthSelect').value;

        // Create URL with query parameters
        const url = new URL(window.location.href);

        if (year) {
            url.searchParams.set('year', year);
        } else {
            url.searchParams.delete('year');
        }

        if (month) {
            url.searchParams.set('month', month);
        } else {
            url.searchParams.delete('month');
        }

        // Redirect to the new URL
        window.location.href = url.toString();
    }

    // Read URL parameters on page load
    function readUrlParameters() {
        const urlParams = new URLSearchParams(window.location.search);

        // Get month from URL
        const month = urlParams.get('month');
        if (month) {
            const monthSelect = document.getElementById('monthSelect');
            if (monthSelect) {
                monthSelect.value = month;
            }
        }

        // Get year from URL
        const year = urlParams.get('year');
        if (year) {
            const yearSelect = document.getElementById('yearSelect');
            if (yearSelect) {
                yearSelect.value = year;
            }
        }
    }

    // Call on page load
    document.addEventListener('DOMContentLoaded', function() {
        readUrlParameters();
    })
    // Pay commission function
    function payCommission(commissionId, month) {
        if (confirm(`هل أنت متأكد من صرف عمولة هذا الشهر؟`)) {
            fetch(`/commissions/${commissionId}/payment-done`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(`تم صرف العمولة بنجاح`);
                        window.location.reload();
                    } else {
                        alert(`حدث خطأ: ${data.message || 'فشل في عملية الصرف'}`);
                    }
                })
                .catch(error => {
                    alert('حدث خطأ في الاتصال: ' + error.message);
                    console.error('Error:', error);
                });
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        const exportBtn = document.getElementById('exportBtn');
        const dropdown = document.getElementById('exportDropdown');

        // Toggle dropdown on button click
        exportBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            this.closest('.dropdown').classList.toggle('active');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            document.querySelectorAll('.dropdown').forEach(drop => {
                drop.classList.remove('active');
            });
        });

        // Handle export option clicks
        document.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', function() {
                const exportType = this.getAttribute('data-type');
                if (exportType === 'excel') {
                    exportToExcel();
                } else if (exportType === 'pdf') {
                    exportToPDF();
                }
                this.closest('.dropdown').classList.remove('active');
            });
        });

        // Prevent dropdown from closing when clicking inside it
        dropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });

    // Export to Excel function
    function exportToExcel() {
        try {
            // Get table data
            const table = document.querySelector('.table-bordered');
            const rows = Array.from(table.querySelectorAll('tbody tr'));

            if (rows.length === 0) {
                alert('لا توجد بيانات للتصدير');
                return;
            }

            // Prepare data for Excel
            const data = [];

            // Add headers
            const headers = Array.from(table.querySelectorAll('thead th')).map(th => th.textContent.trim());
            data.push(headers);

            // Add rows
            rows.forEach(row => {
                const rowData = Array.from(row.querySelectorAll('td')).map(td => {
                    // Remove any HTML tags and get clean text
                    return td.textContent.trim().replace(/\s+/g, ' ');
                });
                data.push(rowData);
            });

            // Create worksheet
            const ws = XLSX.utils.aoa_to_sheet(data);

            // Auto-size columns
            const colWidths = headers.map((_, colIndex) => {
                const maxLen = data.reduce((max, row) => {
                    const cell = row[colIndex] ? String(row[colIndex]) : '';
                    return Math.max(max, cell.length);
                }, headers[colIndex].length);
                return { wch: Math.min(maxLen + 2, 50) };
            });
            ws['!cols'] = colWidths;

            // Create workbook and append sheet
            const wb = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(wb, ws, "العمولات");

            // Generate and download
            const salesRepName = "{{ $salesRep->name }}".replace(/\s+/g, '_');
            const fileName = `عمولات_${salesRepName}_${new Date().toISOString().slice(0, 10)}.xlsx`;
            XLSX.writeFile(wb, fileName);

        } catch (error) {
            console.error('Excel export error:', error);
            alert('حدث خطأ أثناء تصدير ملف Excel');
        }
    }

    // Export to PDF function
    function exportToPDF() {
        try {
            // Clone the main content area
            const printArea = document.getElementById('print-area').cloneNode(true);

            // Add PDF-specific border classes to tables
            const tables = printArea.querySelectorAll('table');
            tables.forEach(table => {
                table.classList.add('pdf-export-table');
            });

            // Remove action buttons and filters from cloned content
            const elementsToRemove = printArea.querySelectorAll('.no-print, .table-actions, .table-filters, .btn, .dropdown, .search-box, .pagination');
            elementsToRemove.forEach(el => el.remove());

            // Show PDF header and footer in the clone
            const pdfHeader = printArea.querySelector('.pdf-header');
            const pdfFooter = printArea.querySelector('.pdf-footer');
            if (pdfHeader) pdfHeader.style.display = 'block';
            if (pdfFooter) pdfFooter.style.display = 'block';

            // Create a clean container for PDF
            const pdfContainer = document.createElement('div');
            pdfContainer.style.width = '100%';
            pdfContainer.style.padding = '20px';
            pdfContainer.style.fontFamily = 'Tajawal, sans-serif';
            pdfContainer.style.direction = 'rtl';
            pdfContainer.style.backgroundColor = '#ffffff';

            // Add the PDF border styles inline
            const style = document.createElement('style');
            style.textContent = `
            .pdf-export-table {
                border: 2px solid #000 !important;
                border-collapse: collapse !important;
            }
            .pdf-export-table th,
            .pdf-export-table td {
                border: 2px solid #000 !important;
                padding: 8px !important;
            }
            .pdf-export-table thead th {
                background-color: #f0f0f0 !important;
                border-bottom: 3px solid #000 !important;
            }
        `;
            pdfContainer.appendChild(style);
            pdfContainer.appendChild(printArea);

            // Add to document temporarily
            document.body.appendChild(pdfContainer);

            // PDF options
            const options = {
                margin: [10, 10, 10, 10],
                filename: `عمولات_{{ $salesRep->name }}_${new Date().toISOString().slice(0,10)}.pdf`,
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2,
                    useCORS: true,
                    logging: false,
                    backgroundColor: '#ffffff',
                    scrollX: 0,
                    scrollY: 0,
                    windowWidth: printArea.scrollWidth
                },
                jsPDF: {
                    unit: 'mm',
                    format: 'a3',
                    orientation: 'landscape',
                    compress: true
                }
            };

            // Generate PDF
            html2pdf()
                .set(options)
                .from(pdfContainer)
                .save()
                .finally(() => {
                    // Clean up
                    document.body.removeChild(pdfContainer);
                });

        } catch (error) {
            console.error('PDF export error:', error);
            alert('حدث خطأ أثناء إنشاء ملف PDF');

            // Clean up on error
            const container = document.querySelector('#pdf-container-temp');
            if (container) {
                document.body.removeChild(container);
            }
        }
    }
    // Initial render
    //document.addEventListener('DOMContentLoaded', function() {
      //  renderTable();
    //});
</script>
@endpush
@endsection
