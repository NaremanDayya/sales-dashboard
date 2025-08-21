@extends('layouts.table')
@section('title','جدول التارجت')
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
[x-cloak] { display: none !important; }
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

    .data-table tbody tr {
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
	font-size:14px;
	font-weight:800;    
}
.form-select {
	font-size:14px;
	    font-weight: 800;

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
      .animate-fadeIn {
        animation: fadeIn 0.3s ease-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .action-btn:hover {
        background-color: var(--gray-200);
        color: var(--gray-700);
    }

    .action-btn.edit:hover {
        color: var(--primary);
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


    }
</style>
@endpush

<body>
    @section('content')
    <div id="print-area" class="table-container">
	<div class="table-header">
            <h2 id="title" class="table-title">التارجت</h2>
            <div class="table-actions d-flex align-items-center gap-2">
	 @isset($salesRep)
            <div class="bg-indigo-100 text-indigo-800 px-4 py-2 rounded-full" style="font-size:14px; font-weight:800;">
                سفير العلامة التجارية: {{ $salesRep->name }}
            </div>
            @endisset
 
   <a href="{{ route('sales-reps.commissions.index',$salesRep->id) }}"
       class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-semibold rounded hover:bg-blue-700 transition">
        <i class="fas fa-chart-line mr-1"></i> عرض العمولات
    </a>

<select id="exportType" class="form-select" style="width: 150px; direction: rtl; text-align: right;">
    <option value="excel" selected>Excel (تصدير إكسل)</option>
    <option value="pdf">PDF (تصدير PDF)</option>
</select>                <button class="btn btn-outline" onclick="handleExport()">
<span style="font-size:14px; font-weight:800;">
    <i class="fas fa-download"></i> تصدير
</span>                </button>

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

                    {{-- Commission Status Filter --}}
                    <select id="filterSelect" onchange="applyFilter()" class="form-select w-auto">
                        <option value="">الكل</option>
                        <option value="commission">يستحق العمولة</option>
                        <option value="no commission">لا يستحق العمولة</option>
                    </select>

                    {{-- Year Filter --}}
                    <select id="yearSelect" class="form-select w-auto" onchange="applyYearFilter()">
                        @for ($year = now()->year - 4; $year <= now()->year + 2; $year++)
                            <option value="{{ $year }}" {{ $selectedYear==$year ? 'selected' : '' }}>
                                السنة: {{ $year }}
                            </option>
                            @endfor
                    </select>
  @if(Auth::user()->role == 'admin')
<div x-data="{ open: false }">
   <!-- Trigger Button -->
    <button @click="open = true"
        class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded shadow">
        تعديل نسبة تحقيق التارجت
    </button>

    <!-- Modal -->
    <div x-show="open" x-cloak class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
        <div @click.away="open = false" class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">

            <h3 class="text-lg font-bold mb-4">   تعديل نسبة تحقيق التارجت </h3>

            <form action="{{ route('settings.updateCommissionThreshold') }}" method="POST">
                @csrf
                <div class="mb-4">
                        <label for="commission_threshold" class="block text-sm font-medium text-gray-700 mb-1">
        النسبة المئوية المطلوبة لاحتساب العمولة
    </label>
 <input type="number" min="0" max="100" step="0.1" name="commission_threshold" id="commission_threshold"
value="{{ old('commission_threshold', \App\Models\Setting::where('key', 'commission_threshold')->value('value') ?? 90) }}"
           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
           style="font-size: 14px; font-weight: 700;">

                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" @click="open = false"
                        class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">إلغاء</button>
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

                </div>

            </div>
        </div>

        <div class="table-responsive" dir="rtl">
            <div class="pdf-content">
                <div class="pdf-header" style="display: none;">
                    <div
                        class="header-content d-flex align-items-center justify-content-between flex-wrap mb-4 p-3 shadow rounded bg-white">
                        <div class="d-flex flex-column align-items-center text-center mx-auto">
                            <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="header-logo mb-2" />
                        </div>
                    </div>
                </div>
                <table class="data-table">
                    <thead>
                        <tr class="text-center">
                            <th>نوع الخدمة</th>
                            <th>تارجت الخدمة</th>
                            <th>التارجت المرحل للشهر الحالي</th> 
                            <th> التارجت المطلوب للشهر الحالي</th>
                            <th colspan="12">نسبة تحقيق التارجت الشهري</th>
                            <th>المجموع الكلي السنوي</th>
                            <th>حالة العمولة للشهر الحالي</th>
                        </tr>
                        <tr class="text-center">
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th>1</th>
                            <th>2</th>
                            <th>3</th>
                            <th>4</th>
                            <th>5</th>
                            <th>6</th>
                            <th>7</th>
                            <th>8</th>
                            <th>9</th>
                            <th>10</th>
                            <th>11</th>
                            <th>12</th>
                            <th></th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody id="tableBody">
                        <!-- Data will be inserted here -->
                    </tbody>
                </table>
<div class="pdf-footer" style="display: none;">
                    <p>جميع الحقوق محفوظة &copy; شركة آفاق الخليج {{ date('Y') }}</p>
                </div>
            </div>
        </div>

        <div class="pagination" id="pagination"></div>

    </div>


<!-- Modern Commission Modal -->
<div id="commissionModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-900 bg-opacity-75 backdrop-blur-sm"></div>
        </div>

        <!-- Modal box -->
        <div class="inline-block align-bottom bg-white rounded-2xl text-right overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <!-- Header -->
<div class="bg-gray-900 px-6 py-4">
    <h3 class="text-lg font-semibold text-gray-50 tracking-tight">إدارة العمولة</h3>
</div>
            <!-- Body -->
            <div class="px-6 py-5">

                <form method="POST" id="commissionForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="type" id="commission_type_input">
                    <input type="hidden" name="item_fee" id="item_fee_input">

                    <label style="font-size: 14px; font-weight: 700;" class="block text-gray-700 mb-3">اختر نوع العمولة:</label>

                    <div class="grid grid-cols-1 gap-3 mb-4">
                        <!-- Percentage option -->
                        <button type="button" onclick="setCommissionType('rate')"
                            class="flex items-center justify-between px-4 py-3 bg-green-50 hover:bg-green-100 border border-green-200 rounded-lg transition-colors">
                            <span style="font-size: 14px; font-weight: 700;" class="text-green-800">نسبة مئوية</span>
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>

                        <!-- Unit option -->
                        <button type="button" onclick="showFeeInput()"
                            class="flex items-center justify-between px-4 py-3 bg-yellow-50 hover:bg-yellow-100 border border-yellow-200 rounded-lg transition-colors">
                            <span style="font-size: 14px; font-weight: 700;" class="text-yellow-800">حسب عدد الوحدات</span>
                            <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </button>
                    </div>

                    <!-- Fee input -->
                    <div id="feeInputSection" class="mt-4 hidden animate-fadeIn">
                        <label for="fee" style="font-size: 14px; font-weight: 700;" class="block mb-1 text-gray-700">أدخل سعر الوحدة:</label>
                        <div class="flex">
                            <input type="number" step="0.01" min="0" id="fee" name="fee"
                                class="flex-1 border border-gray-300 rounded-l-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="مثال: 400.00">
                            <button type="button" onclick="submitItemCommission()"
                                style="font-size: 14px; font-weight: 700;"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-r-lg transition-colors">
                                تأكيد
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Footer -->
            <div class="bg-gray-50 px-6 py-4 flex justify-end">
                <button onclick="closeCommissionModal()"
                    style="font-size: 14px; font-weight: 700;"
                    class="px-4 py-2 text-gray-700 hover:text-gray-900 bg-white hover:bg-gray-100 border border-gray-300 rounded-lg transition-colors">
                    إغلاق
                </button>
            </div>
        </div>
    </div>
</div>

    @endsection
    @push('scripts')
    <script>
        // Targets data from Laravel
    const targetsData = @json($Targets);

    // Render table function
function renderTable(data = targetsData) {
    console.log("Rendering table with data:", data);
    const tbody = document.getElementById('tableBody');
    if (!tbody) {
        console.error("Table body element not found!");
        return;
    }
    tbody.innerHTML = '';

    if (!data || data.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="18" class="empty-state text-center">
                    <div class="empty-icon">
                        <i class="fas fa-users-slash"></i>
                    </div>
                    <div class="empty-text">لا توجد بيانات متاحة</div>
                </td>
            </tr>
        `;
        return;
    }

    const isAdmin = @json(Auth::user()->role === 'admin');
    const currentMonth = new Date().getMonth() + 1; // JavaScript months are 0-11, so +1 for 1-12

    data.forEach(target => {
        try {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50 text-center';

            // Get current month's achievement
            const currentMonthRawValue = target[`month_achieved_${currentMonth}`] || '-';
            const isDash = currentMonthRawValue === '-';
            const currentMonthValue = isDash ? 0 : parseFloat(String(currentMonthRawValue).replace(/,/g, '')) || 0;

            // Determine commission status for current month
            let commissionStatusText = 'لا تصرف';
            let commissionStatusColor = 'bg-gray-100 text-gray-800';

if (!isDash) {
    const threshold = target.needed_achieved_percentage || 90;
    
    if (currentMonthValue >= threshold) {
        commissionStatusText = 'تصرف';
        commissionStatusColor = 'bg-green-100 text-green-800';
    } else if (currentMonthValue >= (threshold - 20)) {
        commissionStatusText = 'تحت المراجعة';
        commissionStatusColor = 'bg-orange-100 text-orange-800';
    }
}
            const monthlyCellsHTML = Array.from({ length: 12 }, (_, i) => {
                const monthIndex = i + 1;
                const rawMonthValue = target[`month_achieved_${monthIndex}`];
                const isDash = rawMonthValue === '-';
                const monthValue = isDash ? 0 : parseFloat(String(rawMonthValue).replace(/,/g, '')) || 0;

let monthClass = 'text-gray-800';
if (!isDash) {
    const threshold = target.needed_achieved_percentage || 90; // Fallback to 90 if null
    
    if (monthValue >= threshold) {
        monthClass = 'text-green-600 font-semibold';
    } else if (monthValue >= (threshold - 20)) {
        monthClass = 'text-orange-600 font-semibold';
    } else {
        monthClass = 'text-red-600 font-semibold';
    }
}
                return `
                    <td class="px-2 py-3 text-sm ${monthClass}">
                        <div>${isDash ? '-' : monthValue + '%'}</div>
                    </td>
                `;
            }).join('');

            row.innerHTML = `
                <td class="px-6 py-4 text-sm font-bold text-blue-700">
                    <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full">
                        ${target.service_type || 'N/A'}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-800">
                    ${target.target_amount || 0}
                </td>
                <td class="px-6 py-4 text-sm text-gray-800">
                    ${target.carried_over_amount || 0}
                </td>
 <td class="px-6 py-4 text-sm text-gray-800">
                    ${target.actual_target_amount || 0}
                </td>

                ${monthlyCellsHTML}
<td class="px-4 py-3 text-sm font-semibold text-center ${
    (target.year_achieved_target || 0) >= (target.needed_achieved_percentage || 90)
        ? 'text-green-600'
        : (target.year_achieved_target || 0) >= ((target.needed_achieved_percentage || 90) - 20)  // 20% below threshold
        ? 'text-orange-600'
        : 'text-red-600'
}">
    ${target.year_achieved_target || 0}%
</td>

		<td class="px-4 py-3 text-sm text-center">
    <div class="flex flex-col items-center">
        <!-- Status Badge -->
        <span style="font-size:14px; font-weight:800;"
            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium ${commissionStatusColor}">
            ${commissionStatusText}
        </span>
        
        ${!isDash && currentMonthValue >= 90 && window.isAdmin ? `
            <button
                onclick="openCommissionModal(${target.commission_id}, ${target.month_achieved_amount})"
                class="bg-blue-500 hover:bg-blue-600 text-white text-xs py-1 px-3 rounded-full mt-1">
                تحديد طريقة العمولة
            </button>
        ` : ''}
    </div>
</td>
            `;

            tbody.appendChild(row);
        } catch (error) {
            console.error("Error rendering row:", error, target);
        }
    });
}
//Filter function
    let currentFilteredReps = [...targetsData]; // Initially show all
function applyFilter() {
    const criteria = document.getElementById('filterSelect').value;

    if (!criteria) {
        currentFilteredTargets = [...targetsData];  // reset filter
        renderTable(currentFilteredTargets);
        return;
    }

    switch (criteria) {
        case 'commission':
            currentFilteredTargets = targetsData.filter(target => target.commission_status == "تصرف");
            break;
        case 'no commission':
            currentFilteredTargets = targetsData.filter(target => target.pending_orders == "لا تصرف");
            break;

        default:
            currentFilteredTargets = [...targetsData];
    }

    if (currentFilteredTargets.length === 0) {
        alert('لا يوجد مندوبين يطابقون معايير التصفية.');
        return;
    }

    renderTable(currentFilteredTargets);
}
function applyYearFilter() {
  const year = document.getElementById('yearSelect').value;
  const url = new URL(window.location.href);
  url.searchParams.set('year', year);


  const commissionFilter = document.getElementById('filterSelect').value;
  if (commissionFilter) {
    url.searchParams.set('commission', commissionFilter);
  } else {
    url.searchParams.delete('commission');
  }

  window.location.href = url.toString();
}
function handleExport() {
        const exportType = document.getElementById('exportType').value;
    if (exportType === 'excel') {
        exportTargets();
    } else if (exportType === 'pdf') {
        generateTargetsPDF();
    } else {
        alert('اختر نوع التصدير أولاً');
    }
    }

    // Initialize table when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
            renderTable();
    });

 // Search function
        document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const filteredData = targetsData.filter(target =>
            target.commission_status.toLowerCase().includes(searchTerm)
        );
        renderTable(filteredData);
    });
    function addNewService() {
        window.location.href = "{{ route('services.create') }}";
    }

    function escapeCsvField(field) {
        if (field == null) return '';
        const fieldStr = field.toString();
        if (fieldStr.includes('"')) {
            return `"${fieldStr.replace(/"/g, '""')}"`;
        }
        if (fieldStr.includes(',')) {
            return `"${fieldStr}"`;
        }
        return fieldStr;
    }
function exportTargets() {
    try {
        // Check if SheetJS is available
        if (typeof XLSX === 'undefined') {
            alert('Excel export library not loaded! Please include SheetJS library.');
            return;
        }

        // Check if data exists
        if (!targetsData || targetsData.length === 0) {
            alert('No data available to export!');
            return;
        }

        // Define Arabic headers
        const headers = [
            "نوع الخدمة",
            "نسبة التارجت",
            "الشهر 1 (%)",
            "الشهر 2 (%)",
            "الشهر 3 (%)",
            "الشهر 4 (%)",
            "الشهر 5 (%)",
            "الشهر 6 (%)",
            "الشهر 7 (%)",
            "الشهر 8 (%)",
            "الشهر 9 (%)",
            "الشهر 10 (%)",
            "الشهر 11 (%)",
            "الشهر 12 (%)",
            "المجموع الكلي (%)",
            "حالة العمولة"
        ];

        // Prepare data - convert numbers to percentages (divided by 100)
        const data = targetsData.map(target => [
            target.service_type || 'N/A',
            (target.target_amount || 0) / 100,
            (target.month_achieved_1 || 0) / 100,
            (target.month_achieved_2 || 0) / 100,
            (target.month_achieved_3 || 0) / 100,
            (target.month_achieved_4 || 0) / 100,
            (target.month_achieved_5 || 0) / 100,
            (target.month_achieved_6 || 0) / 100,
            (target.month_achieved_7 || 0) / 100,
            (target.month_achieved_8 || 0) / 100,
            (target.month_achieved_9 || 0) / 100,
            (target.month_achieved_10 || 0) / 100,
            (target.month_achieved_11 || 0) / 100,
            (target.month_achieved_12 || 0) / 100,
            (target.year_achieved_target || 0) / 100,
            target.commission_value_month_1 || 0,
            target.commission_value_month_2 || 0,
            target.commission_value_month_3 || 0,
            target.commission_value_month_4 || 0,
            target.commission_value_month_5 || 0,
            target.commission_value_month_6 || 0,
            target.commission_value_month_7 || 0,
            target.commission_value_month_8 || 0,
            target.commission_value_month_9 || 0,
            target.commission_value_month_10 || 0,
            target.commission_value_month_11 || 0,
            target.commission_value_month_12 || 0,
            target.commission_status || 'N/A',
        ]);
        // Create worksheet
        const ws = XLSX.utils.aoa_to_sheet([headers, ...data]);

        // Set column widths
        ws['!cols'] = [
            { wch: 25 },  // Service type
            { wch: 15 },  // Target amount
            ...Array(12).fill({ wch: 7 }),  // 12 months
            { wch: 15 },  // Year total
            { wch: 20 }   // Commission status
        ];

        // Format percentage columns (columns B to O, 0-based index)
        const percentageCols = [1, ...Array.from({length: 13}, (_, i) => i + 2)];

        percentageCols.forEach(col => {
            for (let row = 1; row <= targetsData.length; row++) {
                const cellAddress = XLSX.utils.encode_cell({r: row, c: col});
                if (ws[cellAddress]) {
                    ws[cellAddress].t = 'n';  // Number type
                    ws[cellAddress].z = '0.00%';  // Percentage format
                }
            }
        });

        // Create workbook and export
        const wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, "تقرير التارجت");
        XLSX.writeFile(wb, "تقرير_التارجت.xlsx");

    } catch (error) {
        console.error('Export error:', error);
        alert('حدث خطأ أثناء التصدير: ' + error.message);
    }
}

    function generateTargetsPDF() {
    const header = document.querySelector('.pdf-header');
    const footer = document.querySelector('.pdf-footer');
    const element = document.querySelector('.pdf-content');

    // Safety checks in case header/footer don't exist
    if (header) header.style.display = 'block';
    if (footer) footer.style.display = 'block';

    if (!element) {
        console.error('PDF content element not found');
        return;
    }

    const options = {
        margin:       0.5,
        filename:     'targets-report.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2, useCORS: true },
        jsPDF:        { unit: 'in', format: 'a4', orientation: 'landscape' }
    };

    html2pdf().set(options).from(element).save().then(() => {
        if (header) header.style.display = 'none';
        if (footer) footer.style.display = 'none';
    }).catch(error => {
        console.error("PDF generation failed:", error);
        if (header) header.style.display = 'none';
        if (footer) footer.style.display = 'none';
    });
}
      let commissionId = null;
    let achievedAmount = 0;

   function openCommissionModal(id, achieved) {
    commissionId = id;
    achievedAmount = achieved;
    document.getElementById('commissionModal').classList.remove('hidden');
}

    function closeCommissionModal() {
        document.getElementById('commissionModal').classList.add('hidden');
        document.getElementById('feeInputSection').classList.add('hidden');
        document.getElementById('fee').value = '';
    }

    function setCommissionType(type) {
        const form = document.getElementById('commissionForm');
        document.getElementById('commission_type_input').value = type;

        form.action = `/admin/commissions/${commissionId}/update-type`;

        form.submit();
    }

    function showFeeInput() {
        document.getElementById('feeInputSection').classList.remove('hidden');
    }

    function submitItemCommission() {
        const fee = parseFloat(document.getElementById('fee').value);

        if (isNaN(fee) || fee <= 0) {
            alert('يرجى إدخال قيمة صحيحة للرسوم.');
            return;
        }

        document.getElementById('commission_type_input').value = 'item';
        document.getElementById('item_fee_input').value = fee;

        const form = document.getElementById('commissionForm');
        form.action = `/admin/commissions/${commissionId}/update-type`;

        form.submit();
    }


 function printSection(id) {
        const content = document.getElementById(id).innerHTML;
        const original = document.body.innerHTML;

        document.body.innerHTML = content;
        window.print();
        document.body.innerHTML = original;
        location.reload();
    }

    </script>
    <script>
        window.isAdmin = @json(auth()->user()->role === 'admin');
    </script>

 @endpush

