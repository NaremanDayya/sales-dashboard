@extends('layouts.table')
@section('title','جدول العملاء')
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
    }

    .data-table thead th {
        background-color: var(--gray-100);
        color: var(--gray-600);
        font-weight: 600;
        padding: 12px 15px;
        text-align: center;
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
        text-align: center;
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

<body>
    @section('content')
    <div id="print-area" class="table-container">
        <div class="table-header">
            <h2 id="title" class="table-title">العملاء</h2>
            <div class="table-actions d-flex align-items-center gap-2">
                <button class="btn btn-primary" onclick="addNewClient()">
                    <i class="fas fa-plus"></i> إضافة عميل
                </button>

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
                    <select id="filterSelect" onchange="applyFilter()" class="form-select w-auto">
                        <option value="">الكل</option>
                        <option value="interested">مهتم</option>
                        <option value="not interested">غير مهتم</option>
                        <option value="pending">طلبات معلقة</option>
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
                            <th class="no-print">شعار شركة العميل</th>
                            <th>اسم الشركة </th>
                            <th>مقر الشركة </th>
                            <th>اسم الشخص المسؤول </th>
                            <th>المنصب الوظيفي </th>
                            <th>رقم الجوال </th>
                            <th>واتس اب مباشر </th>
                            <th>حالة الاهتمام </th>
                            <th>تاریخ آخر تواصل </th>
                            <th>عدد مرات التواصل </th>
                            <th>حالة وجود طلب </th>
                            <th>نوع الطلب </th>
                            <th>حالة الاستجابة </th>
                            <th class="no-print">الدردشة</th>
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
        <input type="hidden" id="current_sales_rep_id" value="{{ $Clients[0]['sales_rep_id'] ?? '' }}">

        <div class="pagination" id="pagination"></div>

    </div>

    @endsection
    @push('scripts')
    <script>
        const ClientsData = @json($Clients);

        function renderTable(data = ClientsData) {
            const tbody = document.getElementById('tableBody');
            tbody.innerHTML = '';

            if (data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-user-times"></i>
                            </div>
                            <div class="empty-text">لا توجد بيانات متاحة</div>
                        </td>
                    </tr>
                `;
                return;
            }

            data.forEach(client => {
                const row = document.createElement('tr');
                row.innerHTML = `
                 <td class="px-4 py-2 text-center no-print">
                    ${client.company_logo ? `<img src="${client.company_logo}" alt="شعار" class="h-10 mx-auto rounded-full border" />` : '—'}
                </td>
                <td class="px-4 py-2 text-sm font-semibold text-gray-800">${client.company_name || '—'}</td>
                <td class="px-4 py-2 text-sm text-gray-600">${client.address || '—'}</td>
                <td class="px-4 py-2 text-sm text-gray-700">${client.contact_person || '—'}</td>
                <td class="px-4 py-2 text-sm text-gray-700">${client.contact_position || '—'}</td>
                <td class="px-4 py-2 text-sm text-blue-700 font-bold">${client.phone || '—'}</td>
                <td class="px-4 py-2 text-sm text-center">
                    ${client.whatsapp_link ? `<a href="${client.whatsapp_link}" class="text-green-600 hover:underline" target="_blank">
                        <i class="fab fa-whatsapp"></i> تواصل
                    </a>` : '—'}
                </td>
                <td class="px-4 py-2 text-sm text-center">
                    <span class="inline-block px-2 py-0.5 rounded-full ${
                        client.interest_status === 'interested'
                            ? 'bg-green-100 text-green-800'
                            : client.interest_status === 'not_interested'
                            ? 'bg-red-100 text-red-800'
                            : 'bg-gray-100 text-gray-700'
                    }">
                        ${
                            client.interest_status === 'interested'
                                ? 'مهتم'
                                : client.interest_status === 'not interested'
                                ? 'غير مهتم'
                                : 'مؤجل'
                        }
                    </span>
                </td>
                <td class="px-4 py-2 text-sm text-center text-gray-700">${client.last_contact_date || '—'}</td>
                <td class="px-4 py-2 text-sm text-center">
                    <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-100 text-blue-800">
                        ${client.contact_count || 0}
                    </span>
                </td>
                <td class="px-4 py-2 text-sm text-center text-${client.has_request === 'يوجد' ? 'green-600 font-semibold' : 'gray-400'}">
                    ${client.has_request || 'لا يوجد'}
                </td>
              <td class="px-4 py-2 text-sm text-center text-gray-700">
                ${client.request_type === 'client_data_change' ? 'تعديل بيانات العميل' :
                client.request_type === 'agreement_data_change' ? 'تعديل بيانات الاتفاقية' :
                client.request_type === 'change_last_contact_date' ? 'تعديل تاريخ آخر تواصل' :
                client.request_type === 'delete_message' ? 'طلب حذف رسالة' : '—'}
            </td>

               <td class="px-4 py-2 text-sm text-center text-gray-700">
    ${getArabicStatus(client.response_status)}
</td>
                <td class="px-4 py-2 text-sm text-center no-print">
                    <a href="/chat/${client.sales_Rep_id}" class="text-blue-600 hover:underline">
                        <i class="fas fa-comments"></i>
                    </a>
                </td>
                `;
                tbody.appendChild(row);
            });
}

document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const filteredData = ClientsData.filter(client => {
            return (
                (client.contact_person && client.contact_person.toLowerCase().includes(searchTerm)) ||
                (client.company_name && client.company_name.toLowerCase().includes(searchTerm)) ||
                (client.interest_status && client.interest_status.toLowerCase().includes(searchTerm)) ||
                (client.response_status && client.response_status.toLowerCase().includes(searchTerm))||
                (client.phone && client.phone.toLowerCase().includes(searchTerm))

            );
        });
        renderTable(filteredData);
    });

        document.addEventListener('DOMContentLoaded', () => {
            renderTable();
        });
       function addNewClient() {
    const salesRepId = document.getElementById('current_sales_rep_id').value;

    if (!salesRepId) {
        alert("الرجاء تحديد مندوب مبيعات أولاً");
        return;
    }

    window.location.href = "{{ route('sales-reps.clients.create', ['sales_rep' => ':id']) }}".replace(':id', salesRepId);
}
function getArabicStatus(status) {
    switch (status) {
        case 'approved':
            return '<span class="px-2 py-1 rounded-full text-sm font-medium text-green-600 bg-green-100">تمت الموافقة</span>';
        case 'rejected':
            return '<span class="px-2 py-1 rounded-full text-sm font-medium text-red-600 bg-red-100">مرفوض</span>';
        case 'pending':
            return '<span class="px-2 py-1 rounded-full text-sm font-medium text-orange-600 bg-orange-100">قيد الانتظار</span>';
        default:
            return '<span class="text-gray-500">—</span>';
    }
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
function applyFilter() {
    const criteria = document.getElementById('filterSelect').value;

    if (!criteria) {
        currentFilteredClients = [...ClientsData];  // reset filter
        renderTable(currentFilteredClients);
        return;
    }

    switch (criteria) {
        case 'pending':
    currentFilteredClients = ClientsData.filter(client =>
        client.response_status === 'pending'
    );
    break;

        case 'interested':
            currentFilteredClients = ClientsData.filter(client => client.interest_status === 'interested');
            break;

        case 'not interested':
            currentFilteredClients = ClientsData.filter(client => client.interest_status === 'not interested');
            break;

        case 'neutral':
            currentFilteredClients = ClientsData.filter(client => client.interest_status === 'neutral');
            break;

        default:
            currentFilteredClients = [...ClientsData];
    }

    if (currentFilteredClients.length === 0) {
        alert('لا يوجد عملاء يطابقون معايير التصفية.');
        return;
    }

    renderTable(currentFilteredClients);
}
function exportClients() {
    const headers = [
        "شعار شركة العميل",
        "اسم الشركة",
        "مقر الشركة",
        "اسم الشخص المسؤول",
        "المنصب الوظيفي",
        "رقم الجوال",
        "حالة الاهتمام",
        "تاریخ آخر تواصل",
        "عدد مرات التواصل",
        "حالة وجود طلب",
        "نوع الطلب",
        "حالة الاستجابة",
    ];

    // Map filtered data, convert date strings to Date objects
    const data = currentFilteredClients.map(client => [
        client.company_name ,
        client.address ,
        client.contact_person ,
        client.contact_position ,
        client.phone,
        client.interest_status === 'interested' ? 'مهتم' :
        client.interest_status === 'not interested' ? 'غير مهتم' : 'مؤجل',
        client.last_contact_date ?new Date(client.last_contact_date) : null,
        client.contact_count ,
        client.has_request ? 'يوجد' : 'لا يوجد',
        client.request_type || '—',
        client.response_status === 'approved' ? 'تمت الموافقة' :
        client.response_status === 'rejected' ? 'مرفوض' :
        client.response_status === 'pending' ? 'قيد الانتظار' : '—',    ]);

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

    for(let i = 1; i <= currentFilteredClients.length; i++) {
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
    XLSX.utils.book_append_sheet(wb, ws, "تقرير عملاء الشركة");
    XLSX.writeFile(wb, "تقرير_عملاء_الشركة.xlsx");
}
let currentFilteredClients = [...ClientsData]; // Initially show all

function handleExport() {
    const exportType = document.getElementById('exportType').value;
    if (exportType === 'excel') {
        exportClients();
    } else if (exportType === 'pdf') {
        generateClientsPDF();
    } else {
        alert('اختر نوع التصدير أولاً');
    }
}
function generateClientsPDF() {
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
        filename:     'clients-report.pdf',
        image:        { type: 'jpeg', quality: 0.98 },
        html2canvas:  { scale: 2, useCORS: true },
        jsPDF:        { unit: 'in', format: 'a3', orientation: 'landscape' }
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
