@extends('layouts.table')
@section('title', 'بيانات الدخول الخاصة بالمناديب')
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

    .export-btn-group {
        position: relative;
        display: inline-block;
    }

    .export-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 6px;
        background-color: var(--primary);
        color: white;
        border: none;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .export-btn:hover {
        background-color: var(--primary-light);
        transform: translateY(-1px);
    }

    .columns-badge {
        background-color: white;
        color: var(--primary);
        border-radius: 50%;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
    }

    .columns-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }

    .columns-modal-content {
        background-color: white;
        border-radius: 10px;
        width: 90%;
        max-width: 600px;
        max-height: 80vh;
        display: flex;
        flex-direction: column;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
    }

    .columns-modal-header {
        padding: 15px 20px;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .columns-modal-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--gray-800);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .columns-modal-close {
        background: none;
        border: none;
        font-size: 20px;
        cursor: pointer;
        color: var(--gray-500);
        transition: color 0.2s;
    }

    .columns-modal-close:hover {
        color: var(--danger);
    }

    .columns-modal-body {
        padding: 15px;
        overflow-y: auto;
        flex-grow: 1;
    }

    .columns-search {
        position: relative;
        margin-bottom: 15px;
    }

    .columns-search input {
        width: 100%;
        padding: 10px 15px 10px 40px;
        border-radius: 6px;
        border: 1px solid var(--gray-300);
        font-size: 14px;
    }

    .columns-search i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray-400);
    }

    .columns-list {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .column-item {
        padding: 5px 0;
    }

    .column-checkbox {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        padding: 8px 10px;
        border-radius: 6px;
        transition: background-color 0.2s;
    }

    .column-checkbox:hover {
        background-color: var(--gray-100);
    }

    .column-checkbox input {
        display: none;
    }

    .checkmark {
        width: 18px;
        height: 18px;
        border: 2px solid var(--gray-300);
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }

    .column-checkbox input:checked~.checkmark {
        background-color: var(--primary);
        border-color: var(--primary);
    }

    .checkmark:after {
        content: "✓";
        color: white;
        font-size: 12px;
        display: none;
    }

    .column-checkbox input:checked~.checkmark:after {
        display: block;
    }

    .column-name {
        font-size: 14px;
        color: var(--gray-700);
    }

    .columns-modal-footer {
        padding: 15px 20px;
        border-top: 1px solid var(--gray-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .columns-actions {
        display: flex;
        gap: 10px;
    }

    .btn-select-all {
        background: none;
        border: none;
        color: var(--primary);
        font-size: 14px;
        cursor: pointer;
        padding: 5px 10px;
        border-radius: 4px;
        transition: background-color 0.2s;
    }

    .btn-select-all:hover {
        background-color: var(--gray-100);
    }

    .btn-cancel {
        padding: 8px 16px;
        border-radius: 6px;
        background-color: white;
        color: var(--gray-700);
        border: 1px solid var(--gray-300);
        cursor: pointer;
        font-size: 14px;
        margin-left: 10px;
        transition: all 0.2s;
    }

    .btn-cancel:hover {
        background-color: var(--gray-100);
    }

    .btn-apply {
        padding: 8px 16px;
        border-radius: 6px;
        background-color: var(--primary);
        color: white;
        border: none;
        cursor: pointer;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .btn-apply:hover {
        background-color: var(--primary-light);
    }

    .nice-button:hover {
        background-color: #334ae2;
    }

    .dropdown {
        position: relative;
        display: inline-block;
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
        z-index: 10;
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
        <div class="table-header pt-5">
            <h2 id="title" class="table-title">بيانات الدخول للمندوبين</h2>
            <div class="table-actions d-flex align-items-center gap-2">
		  <a href="{{ route('admin.sales-rep-ips.index') }}" class="btn btn-primary d-flex align-items-center gap-1">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
            xmlns="http://www.w3.org/2000/svg">
            <path d="M4 4H20V20H4V4Z" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <path d="M9 4V20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <path d="M15 10H20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
            <path d="M15 14H20" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
        </svg>
        أجهزة تسجيل دخول المناديب
    </a>
                <div class="export-options">
                    <div class="dropdown">
                        <button class="btn btn-dropdown" id="exportBtn" type="button">
                            تصدير البيانات
                            <svg width="12" height="8" viewBox="0 0 12 8" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 1L6 6L11 1" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                        </button>
                        <div class="dropdown-menu" id="exportDropdown">
                            <button class="dropdown-item" data-type="csv">
                                <svg class="dropdown-icon" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4 6H20M4 12H20M4 18H11" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" />
                                </svg>
                                تصدير كملف CSV
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
		<div class="search-box">
                    <input type="text" class="search-input" placeholder="بحث..." id="searchInput">
                    <i class="fas fa-search search-icon"></i>
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
                            <h2 class="header-text">تقرير بيانات الدخول للمندوبين</h2>
                        </div>
                    </div>
                </div>
<table class="data-table">
    <thead>
        <tr>
            <th>الاسم</th>
            <th>البريد الإلكتروني</th>
            <th>كلمة المرور</th>
            <th class="no-print">الإجراءات</th>
        </tr>
    </thead>
    <tbody id="tableBody">
        @forelse($credentials as $credential)
        <tr>
            <td>{{ $credential[0] ?? 'N/A' }}</td> 
            <td>{{ $credential[1] ?? 'N/A' }}</td> 
            <td>
                <span class="password-field">••••••••</span>
                <button class="show-password-btn"
                    data-password="{{ $credential[2] ?? '' }}">إظهار</button>
            </td>
            <td class="no-print">
                <button
                    class="px-3 py-1 bg-blue-100 text-blue-800 rounded-md text-sm hover:bg-blue-200 transition-colors copy-btn"
                    data-password="{{ $credential[2] ?? '' }}">
                    نسخ كلمة المرور
                </button>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <div class="empty-text">لا توجد بيانات متاحة حالياً</div>
            </td>
        </tr>
        @endforelse
    </tbody>
</table>
                @push('scripts')
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
        // Password show/hide functionality
        document.querySelectorAll('.show-password-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const passwordField = this.previousElementSibling;
                const password = this.getAttribute('data-password');

                if (passwordField.textContent === '••••••••') {
                    passwordField.textContent = password;
                    this.textContent = 'إخفاء';
                    // Hide password after 10 seconds
                    setTimeout(() => {
                        if (passwordField.textContent === password) {
                            passwordField.textContent = '••••••••';
                            this.textContent = 'إظهار';
                        }
                    }, 10000);
                } else {
                    passwordField.textContent = '••••••••';
                    this.textContent = 'إظهار';
                }
            });
        });

        // Copy password functionality
        document.querySelectorAll('.copy-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const password = this.getAttribute('data-password');
                navigator.clipboard.writeText(password).then(() => {
                    const originalText = this.textContent;
                    this.textContent = 'تم النسخ!';
                    this.classList.remove('bg-blue-100', 'text-blue-800');
                    this.classList.add('bg-green-100', 'text-green-800');

                    setTimeout(() => {
                        this.textContent = originalText;
                        this.classList.remove('bg-green-100', 'text-green-800');
                        this.classList.add('bg-blue-100', 'text-blue-800');
                    }, 2000);
                });
            });
        });

        // Export functionality remains the same
        const exportBtn = document.getElementById('exportBtn');
        const dropdown = document.getElementById('exportDropdown');

        exportBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            this.closest('.dropdown').classList.toggle('active');
        });

        document.addEventListener('click', function() {
            document.querySelectorAll('.dropdown').forEach(drop => {
                drop.classList.remove('active');
            });
        });

        document.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', function() {
                const exportType = this.getAttribute('data-type');
                if (exportType === 'csv') {
                    exportCredentials('csv');
                } else if (exportType === 'pdf') {
                    exportToPDF();
                }
                this.closest('.dropdown').classList.remove('active');
            });
        });

        dropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.getElementById('tableBody');
    const rows = tableBody.getElementsByTagName('tr');

    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            // Skip the empty state row if it exists
            if (row.classList.contains('empty-state')) continue;
            
            const cells = row.getElementsByTagName('td');
            let rowMatches = false;
            
            // Check each cell (except the last one with buttons)
            for (let j = 0; j < cells.length - 1; j++) {
                const cellText = cells[j].textContent.toLowerCase();
                if (cellText.includes(searchTerm)) {
                    rowMatches = true;
                    break;
                }
            }
            
            // Show/hide the row based on search match
            row.style.display = rowMatches ? '' : 'none';
        }
    });
});

    function exportCredentials(type = 'csv') {
        const rows = Array.from(document.querySelectorAll('#tableBody tr'))
                        .filter(row => row.style.display !== 'none');

        if (rows.length === 0) {
            alert('لا توجد بيانات للتصدير');
            return;
        }

        const headers = ['الاسم', 'البريد الإلكتروني', 'كلمة المرور'];
        const data = rows.map(row => {
            return {
                name: row.cells[0].textContent.trim(),
                email: row.cells[1].textContent.trim(),
                password: row.querySelector('.show-password-btn').getAttribute('data-password')
            };
        });

        if (type === 'csv') {
            let csvContent = '\uFEFF';
            csvContent += headers.join(',') + '\r\n';

            data.forEach(item => {
                const row = [
                    `"${item.name}"`,
                    `"${item.email}"`,
                    `"${item.password}"`
                ];
                csvContent += row.join(',') + '\r\n';
            });

            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `بيانات_الدخول_${new Date().toISOString().slice(0,10)}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }
    }

    function exportToPDF() {
        const originalTable = document.querySelector('.data-table');
        const table = originalTable.cloneNode(true);
        const pdfHeader = document.querySelector('.pdf-header').cloneNode(true);
        const pdfFooter = document.querySelector('.pdf-footer').cloneNode(true);

        pdfHeader.style.display = 'block';
        pdfFooter.style.display = 'block';

        const pdfContainer = document.createElement('div');
        pdfContainer.style.padding = '20px';
        pdfContainer.appendChild(pdfHeader);
        pdfContainer.appendChild(table);
        pdfContainer.appendChild(pdfFooter);

        // Hide passwords in PDF export
        table.querySelectorAll('.password-field').forEach(field => {
            field.textContent = '••••••••';
        });

        const headers = table.querySelectorAll('thead th');
        headers.forEach((header, index) => {
            if (header.classList.contains('no-print')) {
                header.style.display = 'none';
                table.querySelectorAll('tbody tr').forEach(row => {
                    if (row.cells[index]) {
                        row.cells[index].style.display = 'none';
                    }
                });
            }
        });

        const options = {
            margin: 10,
            filename: `بيانات_الدخول_${new Date().toISOString().slice(0,10)}.pdf`,
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: {
                scale: 2,
                scrollX: 0,
                scrollY: 0,
                windowWidth: document.documentElement.offsetWidth
            },
            jsPDF: {
                unit: 'mm',
                format: 'a3',
                orientation: 'landscape',
                compress: true
            }
        };

        html2pdf().set(options).from(pdfContainer).save();
    }
                </script>
                @endpush
                @endsection
