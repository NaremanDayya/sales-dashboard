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
        <h2 id="title" class="table-title">عمولات سفير العلامة التجارية: {{ $salesRep->name }}</h2>
        <div class="table-actions d-flex align-items-center gap-2">
            <select id="exportType" class="form-select" style="width: 150px;">
                <option value="excel" selected>Excel (تصدير إكسل)</option>
                <option value="pdf">PDF (تصدير PDF)</option>
            </select>
            <button class="btn btn-outline" onclick="handleExport()">
                <i class="fas fa-download"></i> تصدير
            </button>
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
                
                {{-- Year Filter --}}
                <select id="yearSelect" class="form-select w-auto" onchange="applyYearFilter()">
                    @for ($year = now()->year - 4; $year <= now()->year + 2; $year++)
                        <option value="{{ $year }}" {{ $selectedYear==$year ? 'selected' : '' }}>
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
    $currentMonth = date('n'); // Current month (1-12)
    $currentMonthCommission = $commission["month_commission_$currentMonth"] ?? 0;
    $currentMonthPaid = $commission["payment_status_month_$currentMonth"] ?? false;
    $hasCurrentMonthCommission = is_numeric($currentMonthCommission) && $currentMonthCommission > 0;
    $achievedTarget = $commission['achieved_percentage'] >= 90;
    $commissionId = $commission['id'];
   
    // Get the specific commission ID for current month
    $currentMonthCommissionId = $commission['id'] . '-' . $currentMonth; // Combine ID with month
@endphp

<tr>
    <td>{{ $commission['service_type'] }}</td>
    <td>{{ number_format($commission['total_achieved_amount'], 0) }}</td>
        <td>{{ number_format($commission['month_achieved_amount'], 0) }}</td>
<td class="px-4 py-3 text-sm">
    <div class="flex items-center justify-center space-x-1">
        @if($commission['calculation_type'] === 'item')
            <!-- Item Fee Display -->
<span class="font-medium text-blue-600" style="font-size: 14px; font-weight: 700;">
    {{ number_format($commission['item_fee'], 0) }}
</span>
<span class="text-gray-500" style="font-size: 14px; font-weight: 700;">&nbsp;&nbsp;ر.س/وحدة</span>

            <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        @else
            <!-- Rate Display -->
            <span class="font-medium text-purple-600" style="font-size: 14px; font-weight: 700;">
{{ rtrim(rtrim(number_format($commission['commission_rate'], 2, '.', ''), '0'), '.') }}%

            </span>
        @endif
    </div>
    
</td>

    @for ($month = 1; $month <= 12; $month++)
<td class="{{ $month == $currentMonth ? 'border-2 border-blue-500' : '' }} {{ $commission["payment_status_month_$month"] ? 'bg-success text-white' : '' }}">
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
        @if($currentMonthPaid)
            <span class="badge bg-success py-1 px-2">تم الصرف</span>
        @elseif($hasCurrentMonthCommission)
            <span class="badge bg-warning py-1 px-2">متاح للصرف</span>
        @else
            <span class="badge bg-secondary py-1 px-2">لا يوجد عمولة</span>
        @endif
    </td>

    @if(Auth::user()->role === 'admin')
 <td class="text-center">
        @if($hasCurrentMonthCommission && !$currentMonthPaid && $achievedTarget)
            <button onclick="payCommission('{{ $commissionId }}')"
                class="btn btn-xs btn-primary py-0 px-2" style="font-size: 0.75rem;"
                data-month="{{ $currentMonth }}">
                صرف عمولة الشهر الحالي
            </button>
        @elseif($currentMonthPaid)
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

    // Pay commission function
function payCommission(commissionId, monthDescription) {
    // Show commission ID and month description in alert
    
    if (confirm(`هل أنت متأكد من صرف هذه العمولة؟`)) {
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
    // Initial render
    //document.addEventListener('DOMContentLoaded', function() {
      //  renderTable();
    //});
</script>
@endpush
@endsection
