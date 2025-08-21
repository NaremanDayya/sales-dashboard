@extends('layouts.table')
@section('title','جدول الاتفاقيات')
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

    .dropdown-container {
        position: relative;
        display: inline-block;
        font-family: Arial, sans-serif;
    }

    .nice-button {
        background-color: #595be0;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s;
    }

    .nice-button:hover {
        background-color: #334ae2;
    }

    .dropdown-select {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        min-width: 300px;
        box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
        z-index: 1;
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 5px;
        margin-top: 5px;
    }

    .dropdown-select option {
        padding: 8px 12px;
        cursor: pointer;
    }

    .dropdown-select option:hover {
        background-color: #e9e9e9;
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
        page-break-inside: avoid;
        break-inside: avoid;
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

@section('content')
<div id="print-area" class="table-container">
    <div class="table-header">
        <h2 id="title" class="table-title">الاتفاقيات</h2>
        <div class="table-actions d-flex align-items-center gap-2">
            <button class="btn btn-primary" onclick="addNewAgreement()">
                <i class="fas fa-plus"></i> إضافة إتفاقية
            </button>
            <div class="dropdown-container">
                <button id="columnsToggle" class="nice-button">▼ عرض الأعمدة</button>

                <select id="columnsSelect" multiple class="dropdown-select">
                    <option value="client_logo" selected>شعار العميل</option>
                    <option value="client_name" selected>اسم العميل</option>
                    <option value="signing_date" selected>تاريخ توقيع الاتفاقية</option>
                    <option value="duration_years" selected>مدة الاتفاقية بالسنوات</option>
                    <option value="termination_type" selected>نوع إنهاء الاتفاقية</option>
                    <option value="implementation_date" selected>تاريخ تنفيذ الاتفاقية</option>
                    <option value="end_date" selected>تاريخ انتهاء الاتفاقية</option>
                    <option value="notice_months" selected>أشهر الإخطار قبل الإنهاء</option>
                    <option value="notice_status" selected>حالة الإخطار</option>
                    <option value="service_type" selected>نوع الخدمة</option>
                    <option value="product_quantity" selected>عدد المنتج</option>
                    <option value="price" selected>التسعيرة</option>
                    <option value="total_amount" selected>المجموع</option>
                </select>
            </div>
                <button class="btn btn-outline" onclick="exportAgreements()">
                    <i class="fas fa-download"></i> تصدير
                </button>

                <button class="btn btn-outline" onclick="window.print()" title="طباعة التقرير">
                    <i class="fas fa-print"></i>
                </button>

                <button onclick="exportAgreements()">تصدير</button> --}}
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
                        <option value="sent">تم الاخطار</option>
                        <option value="not sent"> لم يتم الاخطار</option>
                        <option value="returnable">مشروطة بمقابل</option>
                        <option value="non returnable">غير مشروطة بمقابل </option>
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
                            <th>شعار العميل</th>
                            <th>اسم العميل</th>
                            <th>تاريخ توقيع الاتفاقية</th>
                            <th>مدة الاتفاقية بالسنوات</th>
                            <th>نوع إنهاء الاتفاقية</th>
                            <th>تاريخ تنفيذ الاتفاقية</th>
                            <th>تاريخ انتهاء الاتفاقية</th>
                            <th>أشهر الإخطار قبل الإنهاء</th>
                            <th>حالة الإخطار</th>
                            <th>نوع الخدمة</th>
                            <th>عدد المنتج</th>
                            <th>التسعيرة</th>
                            <th>المجموع</th>
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
        <input type="hidden" id="current_sales_rep_id" value="{{ $Agreements[0]['sales_rep_id'] ?? '' }}">

        <div class="pagination" id="pagination"></div>
    </div>
    @endsection

    @push('scripts')
    <script src="https://cdn.sheetjs.com/xlsx-0.19.3/package/dist/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <script>
        // Global variables
    let AgreementsData = [];
    let currentFilteredAgreements = [];

    document.addEventListener('DOMContentLoaded', function() {
        // Initialize data
        AgreementsData = @json($Agreements);
        currentFilteredAgreements = [...AgreementsData];

        console.log("Agreements Data:", AgreementsData);

        // Render initial table
        renderTable();

        // Setup event listeners
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const filteredData = currentFilteredAgreements.filter(agreement => {
                return (
                    (agreement.client_name && agreement.client_name.toLowerCase().includes(searchTerm)) ||
                    (agreement.termination_type && agreement.termination_type.toLowerCase().includes(searchTerm)) ||
                    (agreement.service_type && agreement.service_type.toLowerCase().includes(searchTerm))
                );
            });
            renderTable(filteredData);
        });
    });

    function renderTable(data = currentFilteredAgreements) {
        const tbody = document.getElementById('tableBody');
        if (!tbody) {
            console.error("Table body element not found!");
            return;
        }
        tbody.innerHTML = '';

        if (!data || data.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="13" class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-user-times"></i>
                        </div>
                        <div class="empty-text">لا توجد بيانات متاحة</div>
                    </td>
                </tr>
            `;
            return;
        }

        data.forEach(agreement => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td class="px-4 py-2 text-center">
                    ${agreement.client_logo ? `<img src="${agreement.client_logo}" alt="شعار" class="h-10 mx-auto rounded-full border" />` : '—'}
                </td>
                <td class="px-4 py-2 text-sm font-semibold text-gray-800">${agreement.client_name || '—'}</td>
                <td class="px-4 py-2 text-sm text-gray-700">${agreement.signing_date || '—'}</td>
                <td class="px-4 py-2 text-sm text-gray-700">${agreement.duration_years || '—'} سنوات</td>
                <td class="px-4 py-2 text-sm text-gray-700">
                    ${
                        agreement.termination_type === 'returnable'
                        ? `مشروطة بمقابل (${agreement.return_value ?? '—'})`
                        : agreement.termination_type === 'non_returnable'
                            ? 'غير مشروطة بمقابل'
                            : '—'
                    }
                </td>
                <td class="px-4 py-2 text-sm text-gray-700">${agreement.implementation_date || '—'}</td>
                <td class="px-4 py-2 text-sm text-gray-700">${agreement.end_date || '—'}</td>
                <td class="px-4 py-2 text-sm text-gray-700">${agreement.notice_months || '—'}</td>
                <td class="px-4 py-2 text-sm text-center">
                    <span class="inline-block px-2 py-0.5 rounded-full ${
                        agreement.notice_status === 'sent'
                            ? 'bg-green-100 text-green-800'
                            : (agreement.notice_status === 'not_sent' || agreement.notice_status === 'not sent')
                                ? 'bg-red-100 text-red-800'
                                : 'bg-gray-100 text-gray-700'
                    }">
                        ${
                            agreement.notice_status === 'sent'
                                ? 'تم الإخطار'
                                : (agreement.notice_status === 'not_sent' || agreement.notice_status === 'not sent')
                                    ? 'لم يتم الإخطار'
                                    : '—'
                        }
                    </span>
                </td>
                <td class="px-4 py-2 text-sm text-gray-700">${agreement.service_type || '—'}</td>
                <td class="px-4 py-2 text-sm text-gray-700">${agreement.product_quantity || '—'}</td>
                <td class="px-4 py-2 text-sm text-gray-700">${agreement.price || '—'}</td>
                <td class="px-4 py-2 text-sm text-gray-700">${agreement.total_amount || '—'}</td>
            `;
            tbody.appendChild(row);
        });
    }

    function addNewAgreement() {
        const salesRepId = document.getElementById('current_sales_rep_id').value;
        if (!salesRepId) {
            alert("الرجاء تحديد مندوب مبيعات أولاً");
            return;
        }
        window.location.href = "{{ route('salesrep.agreements.create', ['salesrep' => ':id']) }}".replace(':id', salesRepId);
    }

    function applyFilter() {
        const criteria = document.getElementById('filterSelect').value;

        if (!criteria || criteria === "") {
            currentFilteredAgreements = [...AgreementsData];
            renderTable();
            return;
        }

        switch (criteria) {
            case 'sent':
                currentFilteredAgreements = AgreementsData.filter(agreement =>
                    agreement.notice_status && agreement.notice_status.toLowerCase() === 'sent'
                );
                break;

            case 'not sent':
                currentFilteredAgreements = AgreementsData.filter(agreement =>
                    agreement.notice_status &&
                    (agreement.notice_status.toLowerCase() === 'not sent' ||
                     agreement.notice_status.toLowerCase() === 'not_sent')
                );
                break;

            case 'returnable':
                currentFilteredAgreements = AgreementsData.filter(agreement =>
                    agreement.termination_type &&
                    agreement.termination_type.toLowerCase() === 'returnable'
                );
                break;

            case 'non returnable':
                currentFilteredAgreements = AgreementsData.filter(agreement =>
                    agreement.termination_type &&
                    agreement.termination_type.toLowerCase() === 'non returnable'
                );
                break;

            default:
                currentFilteredAgreements = [...AgreementsData];
        }

        renderTable();
    }

    function handleExport() {
        if (typeof XLSX === 'undefined') {
            alert('Excel export library not loaded');
            return;
        }
        if (typeof html2pdf === 'undefined') {
            alert('PDF export library not loaded');
            return;
        }

        const exportType = document.getElementById('exportType').value;
        if (exportType === 'excel') {
            exportAgreements();
        } else if (exportType === 'pdf') {
            generateAgreementsPDF();
        } else {
            alert('اختر نوع التصدير أولاً');
        }
    }

   function exportAgreements() {
    // Map of headers to keys and how to format them
    const columnsMap = {
        client_logo: "شعار العميل",
        client_name: "اسم العميل",
        signing_date: "تاريخ توقيع الاتفاقية",
        duration_years: "مدة الاتفاقية بالسنوات",
        termination_type: "نوع إنهاء الاتفاقية",
        implementation_date: "تاريخ تنفيذ الاتفاقية",
        end_date: "تاريخ انتهاء الاتفاقية",
        notice_months: "أشهر الإخطار قبل الإنهاء",
        notice_status: "حالة الإخطار",
        service_type: "نوع الخدمة",
        product_quantity: "عدد المنتج",
        price: "التسعيرة",
        total_amount: "المجموع"
    };

    // Get selected columns from the multi-select
    const select = document.getElementById('columnsSelect');
    const selectedKeys = Array.from(select.selectedOptions).map(opt => opt.value);

    // Build headers array based on selection
    const headers = selectedKeys.map(key => columnsMap[key]);

    // Map each agreement to only the selected columns with proper formatting
    const data = currentFilteredAgreements.map(agreement => {
        return selectedKeys.map(key => {
            switch (key) {
                case 'client_logo':
                    return agreement.client_logo;
                case 'client_name':
                    return agreement.client_name;
                case 'signing_date':
                    return formatDate(agreement.signing_date);
                case 'duration_years':
                    return agreement.duration_years;
                case 'termination_type':
                    return agreement.termination_type === 'returnable'
                        ? `مشروطة بمقابل (${agreement.return_value ?? '—'})`
                        : agreement.termination_type === 'non_returnable'
                            ? 'غير مشروطة بمقابل'
                            : '—';
                case 'implementation_date':
                    return formatDate(agreement.implementation_date);
                case 'end_date':
                    return formatDate(agreement.end_date);
                case 'notice_months':
                    return agreement.notice_months;
                case 'notice_status':
                    return getNoticeStatus(agreement.notice_status);
                case 'service_type':
                    return agreement.service_type;
                case 'product_quantity':
                    return agreement.product_quantity;
                case 'price':
                    return agreement.price;
                case 'total_amount':
                    return agreement.total_amount;
                default:
                    return '';
            }
        });
    });

    const worksheetData = [headers, ...data];
    const ws = XLSX.utils.aoa_to_sheet(worksheetData);

    // Optional: Set column widths based on selected columns
    // Default width or you can customize widths per column if needed
    ws['!cols'] = selectedKeys.map(() => ({ wch: 20 }));

    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "تقرير اتفاقيات الشركة");
    XLSX.writeFile(wb, "تقرير_اتفاقيات_الشركة.xlsx");
}

    function generateAgreementsPDF() {
        const header = document.querySelector('.pdf-header');
        const footer = document.querySelector('.pdf-footer');
        const element = document.querySelector('.pdf-content');

        if (header) header.style.display = 'block';
        if (footer) footer.style.display = 'block';

        if (!element) {
            console.error('PDF content element not found');
            return;
        }

        const options = {
            margin: 0.5,
            filename: 'agreements-report.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true },
            jsPDF: { unit: 'in', format: 'a3', orientation: 'landscape' }
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

    // Helper functions
    function getNoticeStatus(status) {
        if (status === 'sent') return 'تم الإخطار';
        if (status === 'not sent' || status === 'not_sent') return 'لم يتم الإخطار';
        return '—';
    }

    function formatDate(date) {
        if (!date) return "";
        try {
            const d = new Date(date);
            return isNaN(d.getTime()) ? String(date) : d.toISOString().split('T')[0];
        } catch (e) {
            console.warn("Date formatting failed for:", date);
            return String(date);
        }
    }
    function printSection(id) {
        const content = document.getElementById(id).innerHTML;
        const original = document.body.innerHTML;

        document.body.innerHTML = content;
        window.print();
        document.body.innerHTML = original;
        location.reload();
    }

     document.getElementById('columnsToggle').addEventListener('click', function() {
    const select = document.getElementById('columnsSelect');
    select.style.display = select.style.display === 'block' ? 'none' : 'block';
  });
    </script>
    @endpush
