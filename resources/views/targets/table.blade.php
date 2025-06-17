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
                        <option value="commission">يستحق العمولة</option>
                        <option value="no commission">لا يستحق العمولة</option>
                    </select>
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
                            <th>نسبة التارجت</th>
                            <th colspan="12">نسبة تحقيق التارجت الشهري</th>
                            <th>المجموع الكلي السنوي</th>
                            <th>حالة العمولة</th>
                        </tr>
                        <tr class="text-center">
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

    @endsection
    @push('scripts')
    <script>
        // Targets data from Laravel
    const targetsData = @json($Targets);

    // Render table function
    function renderTable(data = targetsData) {
        console.log("Rendering table with data:", data); // Debug log
        const tbody = document.getElementById('tableBody');
        tbody.innerHTML = '';

        if (!data || data.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="16" class="empty-state text-center">
                        <div class="empty-icon">
                            <i class="fas fa-users-slash"></i>
                        </div>
                        <div class="empty-text">لا توجد بيانات متاحة</div>
                    </td>
                </tr>
            `;
            return;
        }

        data.forEach(target => {
            const row = document.createElement('tr');
            row.className = 'hover:bg-gray-50 text-center';
            row.innerHTML = `
                <td class="px-6 py-4 text-sm font-bold text-blue-700">
                    <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full">
                        ${target.service_type || 'N/A'}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm text-gray-800">
                    ${target.target_amount || 0}
                </td>
                ${Array.from({length: 12}, (_, i) => {
                    const monthValue = target[`month_achieved_${i+1}`] || 0;
                    let monthClass = 'text-gray-800';
                    if (monthValue < 70) {
                        monthClass = 'text-red-600 font-semibold';
                    } else if (monthValue < 100) {
                        monthClass = 'text-orange-600 font-semibold';
                    } else if (monthValue >= 100) {
                        monthClass = 'text-green-600 font-semibold';
                    }
                    return `<td class="px-2 py-3 text-sm ${monthClass}">${monthValue}%</td>`;
                }).join('')}
                <td class="px-4 py-3 text-sm font-semibold text-center ${
                    (target.year_achieved_target || 0) < 70 ? 'text-red-600' :
                    (target.year_achieved_target || 0) < 100 ? 'text-orange-600' : 'text-green-600'
                }">
                    ${target.year_achieved_target || 0}%
                </td>
                <td class="px-4 py-3 text-sm text-center">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium">
                        ${target.commission_status || 'N/A'}
                    </span>
                </td>
            `;
            tbody.appendChild(row);
        });
    }


    // Filter functionality
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
            target.commission_status || 'N/A'
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
