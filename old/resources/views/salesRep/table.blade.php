@extends('layouts.table')
@section('title','جدول المندوبين')
@push('styles')
<style>
    :root {
        --primary: #4154f1;
        --primary-light: #6a7ef9;
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

    .dropdown-menu {
        max-height: 300px;
        overflow-y: auto;
        padding: 10px;
    }

    .dropdown-item {
        padding: 5px 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .dropdown-item input[type="checkbox"] {
        margin: 0;
    }

    .dropdown-toggle::after {
        margin-right: 0.5em;
        margin-left: 0;
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
        align-items: center;
        justify-content: center;

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
        padding-top: 60px;
        padding-bottom: 60px;

    }

    .data-table thead th {
        background-color: var(--gray-100);
        color: var(--gray-600);
        font-weight: 600;
        padding: 12px 15px;
        text-align: right;
        border-bottom: 2px solid var(--gray-200);
        position: sticky;
        top: 0;
    }

    .data-table tbody tr {
        transition: background-color 0.2s ease;
    }

    .data-table tbody tr:hover {
        background-color: var(--gray-100);
    }

    .data-table tbody td {
        padding: 12px 15px;
        border-bottom: 1px solid var(--gray-200);
        text-align: right;
        vertical-align: middle;
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

    .title @media (max-width: 768px) {
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
        font-family: "Arial", sans-serif;
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
            width: 100%;
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
            <h2 id="title" class="table-title">مندوبي المبيعات</h2>
            <div class="table-actions d-flex align-items-center gap-2">
<button class="btn btn-primary" onclick="window.location.href='/salesreps/credentials'">
    <i class="fas fa-plus"></i> بيانات دخول المندوبين
</button>
                <button class="btn btn-primary" onclick="addNewSalesRep()">
                    <i class="fas fa-plus"></i> إضافة مندوب
                </button>

                <select id="exportType" class="form-select" style="width: 150px;">
                    <option value="excel" selected>Excel (تصدير إكسل)</option>
                    <option value="pdf">PDF (تصدير PDF)</option>
                </select>
                <button class="btn btn-outline" onclick="handleExport()">
                    <i class="fas fa-download"></i> تصدير
                </button>
                <div class="table-filters">
                    <div class="search-box">
                        <input type="text" class="search-input" placeholder="بحث..." id="searchInput">
                        <i class="fas fa-search search-icon"></i>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <div class="d-flex align-items-center mb-3 gap-2">
                            <i class="fas fa-filter text-secondary"></i>
                            <select id="filterSelect" onchange="applyFilter()" class="form-select w-auto">
                                <option value="">الكل</option>
                                <option value="late_customers">عملاء متأخرون</option>
                                <option value="pending_orders">طلبات معلقة</option>
                                <option value="interested_customers">عملاء مهتمون</option>
                            </select>
                        </div>

                        <!-- Add this dropdown for column selection -->
                        <div class="dropdown">
                            <button class="btn btn-outline dropdown-toggle" type="button" id="columnDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-columns"></i> الأعمدة
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="columnDropdown">
                                <li>
                                    <div class="dropdown-item">
                                        <input type="checkbox" id="col-name" class="column-toggle" checked
                                            data-column="0">
                                        <label for="col-name">اسم المندوب</label>
                                    </div>
                                </li>
                                <li>
                                    <div class="dropdown-item">
                                        <input type="checkbox" id="col-start-date" class="column-toggle" checked
                                            data-column="1">
                                        <label for="col-start-date">تاريخ الالتحاق</label>
                                    </div>
                                </li>
                                <li>
                                    <div class="dropdown-item">
                                        <input type="checkbox" id="col-duration" class="column-toggle" checked
                                            data-column="2">
                                        <label for="col-duration">مدة العمل</label>
                                    </div>
                                </li>
                                <li>
                                    <div class="dropdown-item">
                                        <input type="checkbox" id="col-target" class="column-toggle" checked
                                            data-column="3">
                                        <label for="col-target">العملاء المستهدفين</label>
                                    </div>
                                </li>
                                <li>
                                    <div class="dropdown-item">
                                        <input type="checkbox" id="col-late" class="column-toggle" checked
                                            data-column="4">
                                        <label for="col-late">العملاء المتأخرين</label>
                                    </div>
                                </li>
                                <li>
                                    <div class="dropdown-item">
                                        <input type="checkbox" id="col-orders" class="column-toggle" checked
                                            data-column="5">
                                        <label for="col-orders">الطلبات الإجمالية</label>
                                    </div>
                                </li>
                                <li>
                                    <div class="dropdown-item">
                                        <input type="checkbox" id="col-pending" class="column-toggle" checked
                                            data-column="6">
                                        <label for="col-pending">الطلبات المعلقة</label>
                                    </div>
                                </li>
                                <li>
                                    <div class="dropdown-item">
                                        <input type="checkbox" id="col-interested" class="column-toggle" checked
                                            data-column="7">
                                        <label for="col-interested">العملاء المهتمين</label>
                                    </div>
                                </li>
                                <li>
                                    <div class="dropdown-item">
                                        <input type="checkbox" id="col-achieved" class="column-toggle" checked
                                            data-column="8">
                                        <label for="col-achieved">نسبة التارجت</label>
                                    </div>
                                </li>
                                <li>
                                    <div class="dropdown-item">
                                        <input type="checkbox" id="col-actions" class="column-toggle" checked
                                            data-column="9">
                                        <label for="col-actions">الإجراءات</label>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- Print button -->
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
                    <select id="filterSelect" onchange="applyFilter()" class="form-select w-auto">
                        <option value="">الكل</option>
                        <option value="late_customers">عملاء متأخرون</option>
                        <option value="pending_orders">طلبات معلقة</option>
                        <option value="interested_customers">عملاء مهتمون</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="table-responsive">
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
                        <tr>
                            <th>اسم المندوب</th>
                            <th>تاريخ الالتحاق بالعمل</th>
                            <th>مدة العمل</th>
                            <th>عدد العملاء المستهدفين</th>
                            <th>عدد العملاء المتأخرين</th>
                            <th>عدد الطلبات الإجمالية</th>
                            <th>عدد الطلبات المعلقة</th>
                            <th>عدد العملاء المهتمين والمحتملين</th>
                            <th>نسبة التارجت المتحقق</th>
                            <th class="no-print">الإجراءات</th>
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

        <div class="pagination" id="pagination">
            <!-- Pagination will be inserted here -->
        </div>

    </div>

    @endsection
    @push('scripts')
    <script>
        // Sample data for sales reps
        const salesRepsData = @json($salesReps);
        // Render table function
function renderTable(data = salesRepsData) {
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = '';

            if (data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="8" class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-users-slash"></i>
                            </div>
                            <div class="empty-text">لا توجد بيانات متاحة</div>
                        </td>
                    </tr>
                `;
                return;
            }

            data.forEach(rep => {
                const row = document.createElement('tr');
                row.className = 'hover:bg-gray-50';
                row.innerHTML = `

  <td class="px-4 py-2 text-sm font-semibold text-gray-800 hover:text-blue-600 hover:underline cursor-pointer">
            <a href="/sales-reps/${rep.id}" class="block w-full h-full">
                ${rep.name || '—'}
            </a>
        </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                        ${rep.start_work_date}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                        ${rep.work_duration}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            ${rep.target_customers}
                        </span>
                    </td>
                   <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                        <span class="${
                            rep.late_customers > 0
                                ? 'inline-flex items-center px-2 py-0.5 rounded-md bg-red-300 text-red-800 font-semibold'
                                : 'text-gray-500'
                        }">
                            ${rep.late_customers}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-green-100 text-green-800">
                            ${rep.total_orders}
                        </span>
                    </td>
                   <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                    <span class="${
                        rep.pending_orders > 0
                            ? 'inline-flex items-center px-2 py-0.5 rounded-md bg-orange-300 text-orange-800 font-semibold'
                            : 'text-gray-500'
                    }">
                        ${rep.pending_orders}
                    </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-purple-100 text-purple-800">
                            ${rep.interested_customers}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                        <span class="${
                            rep.achieved_target < 70
                                ? 'inline-flex items-center px-3 py-1 rounded-full bg-red-100 text-red-800 font-semibold'
                                : rep.achieved_target < 100
                                    ? 'inline-flex items-center px-3 py-1 rounded-full bg-orange-100 text-orange-800 font-semibold'
                                    : 'inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-800 font-semibold'
                        }">
                            ${rep.achieved_target}%
                        </span>
                    </td>


                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 no-print">
                        <div class="action-btns">
                            <button class="action-btn view" title="عرض" onclick="viewRep(${rep.id})">
                            <a href="/sales-reps/${rep.id}" title="عرض" class="action-btn view" style="text-decoration: none;">
                                        <i class="fas fa-eye"></i>
                            </a>
                            </button>
                            <button class="action-btn edit" title="تعديل" onclick="editRep(${rep.id})">
                                <a href="/sales-reps/${rep.id}/edit" title="تعديل" class="action-btn edit" style="text-decoration: none;">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </button>
                            <button class="action-btn delete" title="حذف" onclick="deleteRep(${rep.id})">
                                <a href="/sales-reps/${rep.id}/delete" title="حذف" class="action-btn delete" style="text-decoration: none;">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        // Search function
        document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const filteredData = salesRepsData.filter(rep =>
            rep.name.toLowerCase().includes(searchTerm) ||
            rep.start_work_date.toLowerCase().includes(searchTerm)
        );
        renderTable(filteredData);
    });
        // Initialize table on page load
        document.addEventListener('DOMContentLoaded', function() {
            renderTable();
        });

        // Placeholder functions for buttons
       function addNewSalesRep() {
        window.location.href = "{{ route('sales-reps.create') }}";
        }

      function escapeCsvField(field) {
    if (field == null) return ''; // handle null or undefined
    const fieldStr = field.toString();
    if (fieldStr.includes('"')) {
        // escape quotes by doubling them
        return `"${fieldStr.replace(/"/g, '""')}"`;
    }
    if (fieldStr.includes(',')) {
        // wrap in quotes if comma exists
        return `"${fieldStr}"`;
    }
    return fieldStr;
}

function exportSalesReps() {
    const headers = [
        "الاسم",
        "تاريخ بدء العمل",
        "مدة العمل",
        "العملاء المستهدفين",
        "العملاء المتأخرين",
        "عدد الطلبات",
        "الطلبات المعلقة",
        "العملاء المهتمين",
        "الهدف المُحقق"
    ];

    // Map filtered data, convert date strings to Date objects
    const data = currentFilteredReps.map(rep => [
        rep.name,
        rep.start_work_date ? new Date(rep.start_work_date) : null,
        rep.work_duration,
        rep.target_customers,
        rep.late_customers,
        rep.total_orders,
        rep.pending_orders,
        rep.interested_customers,
        rep.achieved_target + "%"
    ]);

    const worksheetData = [headers, ...data];
    const ws = XLSX.utils.aoa_to_sheet(worksheetData);

    // Set column widths
    ws['!cols'] = [
        { wch: 20 }, { wch: 15 }, { wch: 18 }, { wch: 15 },
        { wch: 15 }, { wch: 15 }, { wch: 15 }, { wch: 15 }, { wch: 12 }
    ];

    // Convert JS Date to Excel serial
    function datenum(v) {
        return (v - new Date(Date.UTC(1899, 11, 30))) / (24 * 60 * 60 * 1000);
    }

    for(let i = 1; i <= currentFilteredReps.length; i++) {
        const cellAddress = XLSX.utils.encode_cell({r: i, c: 1}); // column B
        const cell = ws[cellAddress];
        if(cell && cell.v) {
            if (!(cell.v instanceof Date)) {
                cell.v = new Date(cell.v);
            }
            cell.t = 'n';
            cell.z = XLSX.SSF.get_table()[14];  // m/d/yy
            cell.v = datenum(cell.v);
        }
    }

    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "تقرير مندوبين المبيعات");
    XLSX.writeFile(wb, "تقرير_مندوبين_المبيعات.xlsx");
}

let currentFilteredReps = [...salesRepsData]; // Initially show all

function applyFilter() {
    const criteria = document.getElementById('filterSelect').value;

    if (!criteria) {
        currentFilteredReps = [...salesRepsData];  // reset filter
        renderTable(currentFilteredReps);
        return;
    }

    switch (criteria) {
        case 'late_customers':
            currentFilteredReps = salesRepsData.filter(rep => rep.late_customers > 0);
            break;
        case 'pending_orders':
            currentFilteredReps = salesRepsData.filter(rep => rep.pending_orders > 0);
            break;
        case 'interested_customers':
            currentFilteredReps = salesRepsData.filter(rep => rep.interested_customers > 0);
            break;
        default:
            currentFilteredReps = [...salesRepsData];
    }

    if (currentFilteredReps.length === 0) {
        alert('لا يوجد مندوبين يطابقون معايير التصفية.');
        return;
    }

    renderTable(currentFilteredReps);
}
function handleExport() {
    const exportType = document.getElementById('exportType').value;
    if (exportType === 'excel') {
        exportSalesReps();
    } else if (exportType === 'pdf') {
        generateSalesRepPDF();
    } else {
        alert('اختر نوع التصدير أولاً');
    }
}

function generateSalesRepPDF() {
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
        filename:     'sales-reps-report.pdf',
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
document.addEventListener('DOMContentLoaded', function() {
    // Initialize column toggles
    const columnToggles = document.querySelectorAll('.column-toggle');

    columnToggles.forEach(toggle => {
        // Load saved preferences or default to checked
        const columnIndex = toggle.dataset.column;
        const isVisible = localStorage.getItem(`col-${columnIndex}-visible`) !== 'false';
        toggle.checked = isVisible;

        // Apply initial visibility
        toggleColumn(columnIndex, isVisible);

        // Add event listener
        toggle.addEventListener('change', function() {
            const isVisible = this.checked;
            const columnIndex = this.dataset.column;
            toggleColumn(columnIndex, isVisible);

            // Save preference
            localStorage.setItem(`col-${columnIndex}-visible`, isVisible);
        });
    });
});

function toggleColumn(columnIndex, show) {
    const table = document.querySelector('.data-table');
    const headerCells = table.querySelectorAll('th');
    const rows = table.querySelectorAll('tr');

    // Toggle header
    if (headerCells[columnIndex]) {
        headerCells[columnIndex].style.display = show ? '' : 'none';
    }

    // Toggle cells in each row
    rows.forEach(row => {
        const cell = row.cells[columnIndex];
        if (cell) {
            cell.style.display = show ? '' : 'none';
        }
    });
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
    @endpush
