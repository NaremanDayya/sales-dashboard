@extends('layouts.table')
@section('title', 'الشركات المشتركة بين سفراء العلامة التجارية')
@push('styles')
<style>
    :root {
        --primary: #4f46e5;
        --primary-light: #6366f1;
        --secondary: #10b981;
        --danger: #ef4444;
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
	.date-filter {
    display: flex;
    align-items: center;
    gap: 8px;
}

.date-filter input[type="date"] {
    height: 38px;
    border: 1px solid var(--gray-300);
    border-radius: 6px;
    padding: 0 12px;
    font-family: 'Tajawal', sans-serif;
}

.date-filter input[type="date"]:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
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
        font-weight: 800;
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

    .rep-group {
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding: 8px 0;
    }

    .rep-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 6px 10px;
        background-color: var(--gray-100);
        border-radius: 6px;
    }

    .rep-name {
        font-weight: 500;
        color: var(--gray-700);
    }

    .rep-status {
        font-size: 12px;
        padding: 2px 8px;
        border-radius: 12px;
    }

    .status-interested {
        background-color: #ecfdf5;
        color: #059669;
    }

    .status-not-interested {
        background-color: #fee2e2;
        color: #dc2626;
    }

    .status-pending {
        background-color: #fffbeb;
        color: #d97706;
    }

    .rep-link {
        color: var(--primary);
        text-decoration: none;
        transition: color 0.2s;
    }

    .rep-link:hover {
        color: var(--primary-light);
        text-decoration: underline;
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

    .table-html {
        width: 100%;
    }

    .table-html.no-sidebar #main-table {
        margin-right: 0 !important;
        width: 100% !important;
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

    @media print {
        body * {
            visibility: hidden;
        }

        #print-area,
        #print-area * {
            visibility: visible;
        }

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

        #print-area .table-actions,
        #print-area .table-filters,
        #print-area .pagination,
        #print-area .btn,
        #print-area select,
        #print-area input,
        #print-area .search-box,
        #print-area .search-icon {
            display: none !important;
        }

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

        #print-area tr {
            page-break-inside: avoid;
        }

        #print-area {
            direction: rtl;
            width: 100% !important
        }

        .no-print {
            display: none !important;
        }

        .body {
            margin: 0;
            padding: 0;
        }
    }
</style>
@endpush

@section('content')
<div class="table-html no-sidebar">
    <div id="print-area" class="table-container">
        <div class="table-header">
            <h2 class="table-title">الشركات المشتركة بين سفراء العلامة التجارية</h2>
            <div class="table-actions">
                <button class="btn btn-outline" onclick="window.print()" title="طباعة التقرير">
                    <i class="fas fa-print"></i>
                </button>
            </div>
        </div>

        <div class="table-filters">
            <div class="search-box">
                <input type="text" class="search-input" placeholder="بحث باسم الشركة..." id="searchInput">
                <i class="fas fa-search search-icon"></i>
            </div>

        <div class="date-filter">
            <input type="date" id="dateFilter" class="form-input" style="padding: 8px 12px; border-radius: 6px; border: 1px solid #cbd5e1;">
            <button onclick="filterByDate()" class="btn btn-outline" style="margin-right: 8px;">
                <i class="fas fa-filter"></i> تصفية
            </button>
            <button onclick="resetDateFilter()" class="btn btn-outline">
                <i class="fas fa-times"></i> إعادة تعيين
            </button>
        </div>
        </div>

        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>الشعار</th>
                        <th>اسم الشركة</th>
                        <th>عدد سفراء العلامة التجارية</th>
                        <th>سفراء العلامة التجارية</th>
                        <th>تاريخ آخر تواصل</th>
                        <th>حالات الاهتمام</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    @forelse($sharedCompanies as $company)
                    <tr>
                        <td>
                            @foreach($company['clients'] as $client)
                            <div class="mb-2" style="width: 40px; height: 40px;">
                                @if($client['company_logo'])
                                <img src="{{ $client['company_logo'] }}" alt="شعار الشركة"
                                    class="rounded-full border object-cover w-full h-full">
                                @else
                                <div
                                    class="rounded-full border bg-gray-100 w-full h-full flex items-center justify-center">
                                    <i class="fas fa-building text-gray-400"></i>
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </td>
                        <td style="font-size: 14px; font-weight: 700;">
                            @foreach($company['clients'] as $client)
                            <div class="mb-2">
                                <a href="/sales-reps/{{ $client['sales_rep_id'] }}/clients/{{ $client['client_id'] }}"
                                    class="text-gray-800 hover:text-primary hover:underline"
                                    style="font-size: 14px; font-weight: 700;">
                                    {{ $company['company_name'] }}
                                </a>
                            </div>
                            @endforeach
                        </td>
                        <td class="text-center" style="font-size: 14px; font-weight: 700;">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 text-blue-800">
                                {{ count($company['clients']) }}
                            </span>
                        </td>
                        <td>
                            <div class="rep-group">
                                @foreach($company['clients'] as $client)
                                <div class="rep-item" style="font-size: 14px; font-weight: 700;">
                                    <a href="/sales-reps/{{ $client['sales_rep_id'] }}"
                                        class="text-gray-800 hover:text-primary hover:underline">
                                        {{ $client['sales_rep_name'] }}
                                    </a>
                                </div>
                                @endforeach
                            </div>
                        </td>
                        <td>
                            <div class="rep-group">
                                @foreach($company['clients'] as $client)
                                <div class="rep-item flex items-center gap-2"
                                    style="font-size: 14px; font-weight: 700;">

                                    <a href="/client/{{ $client['client_id'] }}/message">
                                        <span>{{ $client['last_contact_date'] }}</span>
                                    </a>
                                </div>
                                @endforeach
                            </div>
                        </td>
                        <td>
                            <div class="rep-group">
                                @foreach($company['clients'] as $client)
                                <span class="rep-status d-block my-1" style="font-size: 14px; font-weight: 700;
                    @if($client['interest_status'] == 'interested')
                        background-color: #ecfdf5; color: #059669;
                    @elseif($client['interest_status'] == 'not interested')
                        background-color: #fee2e2; color: #dc2626;
                    @else
                        background-color: #fffbeb; color: #d97706;
                    @endif">
                                    @if($client['interest_status'] == 'interested') مهتم
                                    @elseif($client['interest_status'] == 'not interested') غير مهتم
                                    @else مؤجل @endif
                                </span>
                                @endforeach
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="empty-text">لا توجد شركات مشتركة بين سفراء العلامة التجارية</div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let SharedCompaniesData = @json($sharedCompanies);
    let currentFilteredCompanies = [...SharedCompaniesData];

    function filterByDate() {
        const selectedDate = document.getElementById('dateFilter').value;
        if (!selectedDate) {
            alert('الرجاء اختيار تاريخ أولاً');
            return;
        }

        const filteredCompanies = SharedCompaniesData.filter(company => {
            return company.clients.some(client => client.last_contact_date === selectedDate);
        });

        if (filteredCompanies.length === 0) {
            alert('لا توجد نتائج للتاريخ المحدد');
            return;
        }

        currentFilteredCompanies = filteredCompanies;
        renderTable(currentFilteredCompanies);
    }

    function resetDateFilter() {
        document.getElementById('dateFilter').value = '';
        currentFilteredCompanies = [...SharedCompaniesData];
        renderTable(currentFilteredCompanies);
    }

    function renderTable(data = currentFilteredCompanies) {
        const tbody = document.getElementById('tableBody');
        tbody.innerHTML = '';

        if (!data || data.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="empty-text">لا توجد شركات مشتركة بين سفراء العلامة التجارية</div>
                    </td>
                </tr>
            `;
            return;
        }

        data.forEach(company => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    ${company.clients.map(client => `
                        <div class="mb-2" style="width: 40px; height: 40px;">
                            ${client.company_logo ?
                                `<img src="${client.company_logo}" alt="شعار الشركة" class="rounded-full border object-cover w-full h-full">` :
                                `<div class="rounded-full border bg-gray-100 w-full h-full flex items-center justify-center">
                                    <i class="fas fa-building text-gray-400"></i>
                                </div>`}
                        </div>
                    `).join('')}
                </td>
                <td style="font-size: 14px; font-weight: 700;">
                    ${company.clients.map(client => `
                        <div class="mb-2">
                            <a href="/sales-reps/${client.sales_rep_id}/clients/${client.client_id}"
                               class="text-gray-800 hover:text-primary hover:underline"
                               style="font-size: 14px; font-weight: 700;">
                                ${company.company_name}
                            </a>
                        </div>
                    `).join('')}
                </td>
                <td class="text-center" style="font-size: 14px; font-weight: 700;">
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-blue-100 text-blue-800">
                        ${company.clients.length}
                    </span>
                </td>
                <td>
                    <div class="rep-group">
                        ${company.clients.map(client => `
                            <div class="rep-item" style="font-size: 14px; font-weight: 700;">
                                <a href="/sales-reps/${client.sales_rep_id}"
                                   class="text-gray-800 hover:text-primary hover:underline">
                                    ${client.sales_rep_name}
                                </a>
                            </div>
                        `).join('')}
                    </div>
                </td>
                <td>
                    <div class="rep-group">
                        ${company.clients.map(client => `
                            <div class="rep-item flex items-center gap-2" style="font-size: 14px; font-weight: 700;">
                                <span>${client.last_contact_date}</span>
                                <a href="/client/${client.client_id}/message"
                                   class="text-gray-600 hover:text-primary"
                                   title="الدردشة">
                                    <i class="fas fa-comments"></i>
                                </a>
                            </div>
                        `).join('')}
                    </div>
                </td>
                <td>
                    <div class="rep-group">
                        ${company.clients.map(client => `
                            <span class="rep-status d-block my-1"
                                style="font-size: 14px; font-weight: 700;
                                ${client.interest_status === 'interested' ?
                                    'background-color: #ecfdf5; color: #059669;' :
                                    client.interest_status === 'not interested' ?
                                    'background-color: #fee2e2; color: #dc2626;' :
                                    'background-color: #fffbeb; color: #d97706;'}">
                                ${client.interest_status === 'interested' ? 'مهتم' :
                                  client.interest_status === 'not interested' ? 'غير مهتم' : 'مؤجل'}
                            </span>
                        `).join('')}
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    // Initialize the table on load
    document.addEventListener('DOMContentLoaded', function() {
        renderTable();

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const dateFilter = document.getElementById('dateFilter').value;

            const filteredData = SharedCompaniesData.filter(company => {
                const matchesSearch = company.company_name.toLowerCase().includes(searchTerm);
                const matchesDate = dateFilter ?
                    company.clients.some(client => client.last_contact_date === dateFilter) : true;

                return matchesSearch && matchesDate;
            });

            currentFilteredCompanies = filteredData;
            renderTable(currentFilteredCompanies);
        });
    });
</script>
@endpush
