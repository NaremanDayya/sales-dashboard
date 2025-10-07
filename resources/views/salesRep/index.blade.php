@extends('layouts.master')
@section('title','جدول سفراء العلامة التجارية')
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
    [x-cloak] { display: none !important; }

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
    .pdf-export-container {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        position: fixed !important;
        left: -10000px !important;
        top: -10000px !important;
        z-index: -9999 !important;
        pointer-events: none !important;
    }

    /* Ensure the original PDF content in view is hidden */
    .pdf-content .pdf-header,
    .pdf-content .pdf-footer {
        display: none !important;
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
#bulkActionsBtn::after {
  display: none;
}
.dropdown-menu {
    display: none;
    position: absolute;
    right: 0;
    background-color: white;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    z-index: 10;
    min-width: 180px;
    margin-top: 4px;
}

.dropdown.active .dropdown-menu {
    display: block;
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
        btn-apply display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .:hover {
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
	font-size:14px;
        font-weight:800;

}
.status-dropdown-item {
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
        font-family: 'Tajawal', sans-serif;
    }

    .data-table thead th {
        background: linear-gradient(135deg, #4154f1 0%, #6a7ef9 100%);
        color: white;
        font-weight: bold;
        padding: 12px 8px;
        text-align: center;
        border: 1px solid #2d3db8;
        font-size: 14px;
    }

    .data-table tbody td {
        padding: 10px 8px;
        border: 1px solid #dee2e6;
        text-align: center;
        vertical-align: middle;
        font-size: 13px;
        font-weight: 500;
    }

    .data-table tbody tr:nth-child(even) {
        background-color: #f8f9fa;
    }

    .data-table tbody tr:hover {
        background-color: #e9ecef;
    }

    /* Status badges for PDF */
    .status-badge-pdf {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-active-pdf {
        background-color: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    .status-inactive-pdf {
        background-color: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    /* Count badges for PDF */
    .count-badge-pdf {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 4px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        min-width: 30px;
    }

    .badge-blue-pdf {
        background-color: #dbeafe;
        color: #1e40af;
        border: 1px solid #bfdbfe;
    }

    .badge-green-pdf {
        background-color: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    .badge-red-pdf {
        background-color: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .badge-orange-pdf {
        background-color: #ffedd5;
        color: #9a3412;
        border: 1px solid #fed7aa;
    }

    .badge-purple-pdf {
        background-color: #f3e8ff;
        color: #7e22ce;
        border: 1px solid #e9d5ff;
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
        gap: 5px;
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

  .bulk-actions {
        margin-right: 10px;
    }

    .rep-checkbox {
        margin: 0 auto;
        display: block;
        width: 16px;
        height: 16px;
    }

    #selectAllCheckbox {
        width: 16px;
        height: 16px;
    }
  .status-dropdown-item {
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

    .column-selector {
        position: relative;
        display: inline-block;
    }

    .column-selector-btn {
        background-color: var(--gray-100);
        border: 1px solid var(--gray-300);
        border-radius: 6px;
        padding: 8px 12px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .column-selector-btn:hover {
        background-color: var(--gray-200);
    }

    .column-selector-dropdown {
        position: absolute;
        right: 0;
        background-color: white;
        border: 1px solid var(--gray-300);
        border-radius: 6px;
        padding: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        z-index: 1000;
        min-width: 200px;
        max-height: 300px;
        overflow-y: auto;
    }

    .column-selector-item {
        display: flex;
        align-items: center;
        padding: 8px;
        cursor: pointer;
    }

    .column-selector-item:hover {
        background-color: var(--gray-100);
    }

    .column-selector-checkbox {
        margin-left: 8px;
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
    .pdf-header {
        display: none;
        width: 100%;
        text-align: center;
        padding: 20px 0;
        background-color: #ffffff !important; /* إزالة الخلفية الزرقاء */
        color: #333; /* تغيير اللون إلى داكن */
        margin-bottom: 20px;
        border-bottom: 2px solid #4154f1; /* إضافة حدود زرقاء بدلاً من الخلفية */
    }

    .pdf-header .header-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 15px;
    }

    .pdf-header .header-logo {
        max-height: 80px;
        width: auto;
        /* إزالة filter الذي يحول اللون إلى أبيض */
        filter: none !important;
    }

    .pdf-header .header-text {
        font-size: 24px;
        font-weight: bold;
        color: #4154f1 !important; /* تغيير اللون إلى أزرق */
        margin: 0;
    }

    .pdf-header .report-info {
        display: flex;
        justify-content: center;
        gap: 30px;
        font-size: 14px;
        margin-top: 10px;
    }

    .pdf-header .report-date {
        background: #f8f9fa;
        padding: 5px 15px;
        border-radius: 20px;
        color: #495057;
        border: 1px solid #dee2e6;
    }

    .pdf-header .report-time {
        background: #f8f9fa;
        padding: 5px 15px;
        border-radius: 20px;
        color: #495057;
        border: 1px solid #dee2e6;
    }

    .pdf-footer {
        display: none;
        width: 100%;
        text-align: center;
        padding: 15px 0;
        background: #f8f9fa;
        color: #6c757d;
        margin-top: 30px;
        border-top: 2px solid #dee2e6;
        font-size: 12px;
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
        #print-area .fas.fa-print,
        #print-area .column-selector {
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

        #print-area
table.data-table thead {
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
@section('favicon')
    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.jpg') }}">
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.jpg') }}">
@endsection
<body>
    @section('content')
    <div id="print-area" class="table-container">
        <div class="table-header">
            <h2 id="title" class="table-title">مندوبي المبيعات</h2>
            <div class="table-actions d-flex align-items-center gap-2">
<button class="btn btn-primary" onclick="window.location.href='/salesreps/credentials'">
    <i class="fas fa-user-lock"></i> بيانات دخول سفراء العلامة التجارية
</button>
                <button class="btn btn-primary no-print" onclick="addNewSalesRep()">
                    <i class="fas fa-plus"></i> إضافة مندوب
                </button>
                <div class="export-btn-group no-print">
                    <button id="columnsBtn" class="export-btn columns-btn" onclick="openColumnsModal()">
                        <span class="btn-icon"><i class="fas fa-columns"></i></span>
                        <span class="btn-text">اختيار الأعمدة</span>
                        <span id="columnsBadge" class="columns-badge">11</span>
                    </button>
                </div>
                <div class="export-options no-print">
                    <div class="dropdown">
                        <button class="btn btn-dropdown" id="exportBtn" type="button">
                            تصدير البيانات
                            <svg width="12" height="8" viewBox="0 0 12 8" fill="none"
                                xmlns="http://www.w3.org/2000/svg">
                                <path d="M1 1L6 6L11 1" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            </svg>
                        </button>
                        <div class="dropdown-menu" id="exportDropdown">
                            <button class="dropdown-item" data-type="xlsx">
                                <svg class="dropdown-icon" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path d="M4 6H20M4 12H20M4 18H11" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" />
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
                <!-- Columns Selection Modal -->
                <div id="columnsModal" class="columns-modal">
                    <div class="columns-modal-content">
                        <div class="columns-modal-header">
                            <h3 class="columns-modal-title">
                                <i class="fas fa-columns"></i>
                                اختيار الأعمدة للعرض
                            </h3>
                            <button class="columns-modal-close" onclick="closeColumnsModal()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="columns-modal-body">
                            <div class="columns-search">
                                <input type="text" id="columnsSearch" placeholder="بحث عن عمود..."
                                    onkeyup="filterColumns()">
                                <i class="fas fa-search"></i>
                            </div>

                            <div class="columns-list" id="columnsList">
                                <!-- Column items will be generated here -->
                                <div class="column-item">
                                    <label class="column-checkbox">
                                        <input type="checkbox" value="personal_image" checked>
                                        <span class="checkmark"></span>
                                        <span class="column-name">الصورة الشخصية</span>
                                    </label>
                                </div>
                                <div class="column-item">
                                    <label class="column-checkbox">
                                        <input type="checkbox" value="name" checked>
                                        <span class="checkmark"></span>
                                        <span class="column-name"> سفير العلامة التجارية</span>
                                    </label>
                                </div>
                                <div class="column-item">
                                    <label class="column-checkbox">
                                        <input type="checkbox" value="start_work_date" checked>
                                        <span class="checkmark"></span>
                                        <span class="column-name">تاريخ الالتحاق بالعمل</span>
                                    </label>
                                </div>
                                <div class="column-item">
                                    <label class="column-checkbox">
                                        <input type="checkbox" value="work_duration" checked>
                                        <span class="checkmark"></span>
                                        <span class="column-name"> مدة العمل</span>
                                    </label>
                                </div>
                                <div class="column-item">
                                    <label class="column-checkbox">
                                        <input type="checkbox" value="target_customers" checked>
                                        <span class="checkmark"></span>
                                        <span class="column-name"> العملاء المستهدفين</span>
                                    </label>
                                </div>
                                <div class="column-item">
                                    <label class="column-checkbox">
                                        <input type="checkbox" value="late_customers" checked>
                                        <span class="checkmark"></span>
                                        <span class="column-name"> العملاء المتأخرين</span>
                                    </label>
                                </div>
                                <div class="column-item">
                                    <label class="column-checkbox">
                                        <input type="checkbox" value="total_orders" checked>
                                        <span class="checkmark"></span>
                                        <span class="column-name"> الطلبات الإجمالية</span>
                                    </label>
                                </div>
                                <div class="column-item">
                                    <label class="column-checkbox">
                                        <input type="checkbox" value="pending_orders" checked>
                                        <span class="checkmark"></span>
                                        <span class="column-name"> الطلبات المعلقة</span>
                                    </label>
                                </div>
                                <div class="column-item">
                                    <label class="column-checkbox">
                                        <input type="checkbox" value="interested_customers" checked>
                                        <span class="checkmark"></span>
                                        <span class="column-name"> العملاء المهتمين والمحتملين</span>
                                    </label>
                                </div>
                         	<div class="column-item">
                                        <label class="column-checkbox">
                                        <input type="checkbox" value="active_agreements_count" checked>
                                        <span class="checkmark"></span>
                                        <span class="column-name">  الاتفاقيات النشطة</span>
                                        </label>
                                </div>
  				<div class="column-item">
                                        <label class="column-checkbox">
                                        <input type="checkbox" value="inactive_agreements_count" checked>
                                        <span class="checkmark"></span>
                                        <span class="column-name">  الاتفاقيات غير النشطة</span>
                                        </label>
                                </div>
                            </div>
                        </div>
                        <div class="columns-modal-footer">
                            <div class="columns-actions">
                                <button class="btn-select-all" onclick="toggleSelectAll()">تحديد الكل</button>
                                <button class="btn-select-all" onclick="resetSelection()">إعادة تعيين</button>
                            </div>
                            <div>
                                <button class="btn-cancel" onclick="closeColumnsModal()">إلغاء</button>
                                <button class="btn-apply" onclick="applyColumnSelection()">
                                    <i class="fas fa-check"></i>
                                    تطبيق
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Print button -->
                <button class="btn btn-outline no-print" onclick="window.print()" title="طباعة التقرير">
                    <i class="fas fa-print"></i>
                </button>
            </div>
        </div>
        <div class="table-filters">
            <div class="search-box">
                <input type="text" class="search-input" placeholder="بحث..." id="searchInput">
                <i class="fas fa-search search-icon"></i>
            </div>



    <div class="d-flex align-items-center gap-2">
        <!-- Filter Dropdown -->
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-filter text-secondary"></i>
            <select id="filterSelect" onchange="applyFilter()" class="form-select w-auto">
                <option value="">الكل</option>
                <option value="late_customers">عملاء متأخرون</option>
                <option value="pending_orders">طلبات معلقة</option>
                <option value="interested_customers">عملاء مهتمون</option>
            </select>
        </div>

<div class="bulk-actions no-print ms-2">
  <div class="dropdown">

    <button
      class="btn btn-outline-secondary dropdown-toggle"
      id="bulkActionsBtn"
      type="button"
      data-bs-toggle="dropdown"
      aria-expanded="false"
      disabled>
      تنفيذ الإجراء
      <svg width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
        <path d="M1 1L6 6L11 1" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
      </svg>
    </button>


    <ul class="dropdown-menu" aria-labelledby="bulkActionsBtn" id="bulkActionsDropdown">
      <li>
        <button class="status-dropdown-item" data-action="activate">تفعيل الحساب</button>
      </li>
      <li>
        <button class="status-dropdown-item" data-action="deactivate">تعطيل الحساب</button>
      </li>
    </ul>
  </div>
</div>
    </div>
</div>
            </div>
        <div id="passwordModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-800">تغيير كلمة المرور</h3>
                        <button onclick="closePasswordModal()" class="text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <form id="passwordForm" action="{{ route('salesrep.password.change', ['salesrep' => '__ID__']) }}"
                        method="POST">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="salesRepId" name="salesRepId" value="">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">كلمة المرور الجديدة</label>
                                <input type="password" id="salesrepPassword" name="salesrepPassword" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">تأكيد كلمة المرور</label>
                                <input type="password" id="confirmPassword" name="confirmPassword" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            </div>
 <div class="flex justify-end space-x-3 pt-2">
                                <button type="button" onclick="closePasswordModal()"
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                    إلغاء
                                </button> &nbsp;&nbsp;&nbsp;
                                <button type="submit"
                                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                                    حفظ كلمة المرور
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>

<!-- Delete Modal -->
 <div id="deleteModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
            <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4 p-4">

<form id="deleteForm" action="{{ route('sales-reps.destroy', ['sales_rep' => '__ID__']) }}" method="POST">
    @csrf
            @csrf
            @method('DELETE')
            <input type="hidden" id="deleteSalesRepId" name="salesRepId">

            <h3 class="text-lg font-bold text-gray-900 mb-3">تأكيد الحذف</h3>
            <p id="deleteMessage" class="text-gray-600 mb-5">
                هل أنت متأكد أنك تريد حذف هذا المستخدم؟ هذا الإجراء لا يمكن التراجع عنه.
            </p>

<div class="flex justify-end pt-2">
    <button type="button" onclick="closeDeleteModal()"
        class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 mr-4">
        إلغاء
    </button>&nbsp;&nbsp;&nbsp;&nbsp;
    <button type="submit"
        class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
        حذف
    </button>
</div>

        </form>
    </div>
</div>

        <div class="table-responsive">
            <div class="pdf-content" id="pdf-content">
                <div class="pdf-header" style="display: none;">
                    <div class="header-content">
                        <div class="d-flex flex-column align-items-center text-center">
                            <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="header-logo mb-3" />
                            <h2 class="header-text">تقرير سفراء العلامة التجارية</h2>
                        </div>
                        <div class="report-info">
            <span class="report-date">
                تاريخ التقرير: {{ date('Y-m-d') }}
            </span>
                            <span class="report-time">
                الوقت: {{ date('H:i') }}
            </span>
                        </div>
                    </div>
                </div>

                </div>
                <table class="data-table">
                    <thead>
                        <tr>
				<th class="no-print">
                                <input type="checkbox" id="selectAllCheckbox" onclick="toggleSelectAllReps()">
                            </th>
<th data-column="image">الصورة الشخصية</th>
                        <th data-column="name"> سفير العلامة التجارية</th>
                        <th data-column="start_work_date">تاريخ الالتحاق بالعمل</th>
                        <th data-column="work_duration">مدة العمل</th>
                        <th data-column="target_customers"> العملاء المستهدفين</th>
                        <th data-column="late_customers"> العملاء المتأخرين</th>
                        <th data-column="total_orders"> الطلبات الإجمالية</th>
                        <th data-column="pending_orders"> الطلبات المعلقة</th>
                        <th data-column="interested_customers"> العملاء المهتمين</th>
			<th data-column="active_agreements_count"> الاتفاقيات النشطة</th>
			<th data-column="inactive_agreements_count"> الاتفاقيات غير النشطة</th>
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
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script>
        let salesRepsData = [];
        let currentFilteredReps = [];
        // Initialize column selector
        document.addEventListener('DOMContentLoaded', function() {
    setupBulkActions();

        salesRepsData = @json($salesReps);
        currentFilteredReps = [...salesRepsData];

            console.log("SalesRepss Data:", salesRepsData);

            // Render initial table
            renderTable();
             document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            currentFilteredReps = salesRepsData.filter(rep =>
                rep.name.toLowerCase().includes(searchTerm) ||
                rep.start_work_date.toLowerCase().includes(searchTerm)
            );
            renderTable(currentFilteredReps);
            });
        });
// Export dropdown functionality
            const exportBtn = document.getElementById('exportBtn');
            const dropdown = document.getElementById('exportDropdown');

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
                    const selectedColumns = getSelectedColumns();

                    if (exportType === 'csv') {
                        exportSalesReps('csv', selectedColumns);
                    } else if (exportType === 'pdf') {
                        exportPDF(selectedColumns);
                    }else{
                        exportSalesReps();
                    }

                    // Close dropdown after selection
                    this.closest('.dropdown').classList.remove('active');
                });
            });

            // Prevent dropdown from closing when clicking inside it
            dropdown.addEventListener('click', function(e) {
                e.stopPropagation();
            });
function setupBulkActions() {
    document.getElementById('bulkActionsBtn').addEventListener('click', function(e) {
        e.stopPropagation();
        this.closest('.dropdown').classList.toggle('active');
    });

    document.querySelectorAll('#bulkActionsDropdown .status-dropdown-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const action = this.getAttribute('data-action');
            const selectedIds = Array.from(document.querySelectorAll('.rep-checkbox:checked'))
                .map(checkbox => checkbox.value);

            if (selectedIds.length === 0) {
                alert('يرجى تحديد مندوب واحد على الأقل');
                return;
            }

            if (confirm(`هل أنت متأكد من أنك تريد ${action === 'activate' ? 'تفعيل' : 'تعطيل'} ${selectedIds.length} مندوب؟`)) {
                performBulkAction(selectedIds, action);
            }
        });
    });
}
        // Toggle table columns based on selection
        function toggleTableColumns() {
            Object.keys(selectedColumns).forEach(colId => {
                const colClass = colId.replace('col-', '');
                const elements = document.querySelectorAll(`.${colClass}`);
                elements.forEach(el => {
                    el.style.display = selectedColumns[colId] ? '' : 'none';
                });
            });
        }

        // Render table function
        function renderTable(data = currentFilteredReps) {
            const tbody = document.getElementById('tableBody');
            if (!tbody) {
                console.error("Table body element not found!");
                return;
            }
            tbody.innerHTML = '';

            if (data.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="10" class="empty-state">
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
    <td class="no-print">
            <input type="checkbox" class="rep-checkbox" value="${rep.id}"
                   data-status="${rep.account_status}"
                   onchange="updateBulkActionsButton()">
        </td>

<td class="px-4 py-2 text-center">
  ${rep.personal_image
    ? `<img src="${rep.personal_image}" alt="شعار" class="h-16 w-16 mx-auto rounded-full border object-cover" />`
    : '—'}
</td>
<td class="col-name px-6 py-4 whitespace-nowrap text-lg font-bold text-blue-700" x-data="{ deleteModalOpen: false }">
    <div class="flex flex-col items-start space-y-1">
        <!-- Name -->
        <a href="/sales-reps/${rep.id}" style="text-decoration: none;">
            <span class="text-lg font-bold text-blue-800 bg-blue-100 px-3 py-1 rounded-full">
                ${rep.name}
            </span>
        </a>

        <!-- Status under the name -->
        <span class="text-xs font-medium px-2 py-0.5 rounded-full
            ${rep.account_status === 'inactive' ? 'bg-red-100 text-red-600' : 'bg-green-100 text-green-600'}">
            ${rep.account_status === 'inactive' ? 'معطل' : 'نشط'}
        </span>
    </div>
</td>


     <td class="col-start-date px-6 py-4 whitespace-nowrap text-lg font-bold text-gray-800">
    ${formatDateForDisplay(rep.start_work_date)}
</td>

<td class="col-work-duration px-6 py-4 whitespace-nowrap text-lg font-bold text-gray-800">
    ${rep.work_duration}
</td>



<td class="col-target-customers px-6 py-4 whitespace-nowrap text-sm text-center">
    <a href="/sales-reps/${rep.id}/clients" style="text-decoration: none;">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800" style="font-size:14px;font-weight:800;">
            ${rep.target_customers}
        </span>
    </a>
</td>

<td class="col-late-customers px-4 py-3 whitespace-nowrap text-lg text-center">
    <a href="/sales-reps/${rep.id}/clients" style="text-decoration: none;">
        <!-- العدد الكلي -->
        <div class="mb-1">
            <span class="${
        rep.late_customers > 0
            ? 'inline-flex items-center px-2 py-0.5 rounded-md bg-red-300 text-red-800 font-bold text-lg'
            : 'text-gray-400 font-bold text-lg'
    }">
             ${rep.late_customers}
            </span>
        </div>

        <!-- التفاصيل: مهتم، غير مهتم، حيادي -->
        <div class="flex justify-center space-x-4 rtl:space-x-reverse text-sm text-gray-600">
            <!-- المهتم -->
            <div class="flex items-center space-x-1 rtl:space-x-reverse">
                <i class="fas fa-thumbs-up text-green-500"></i>
                <span class="font-semibold">${rep.interested_late_customers}</span>
            </div>

            <!-- غير المهتم -->
            <div class="flex items-center space-x-1 rtl:space-x-reverse">
                <i class="fas fa-thumbs-down text-red-700"></i>
                <span class="font-semibold">${rep.not_interested_late_customers}</span>
            </div>

            <!-- الحيادي -->
            <div class="flex items-center space-x-1 rtl:space-x-reverse">
                <i class="fas fa-clock text-gray-400"></i>
                <span class="font-semibold">${rep.neutral_late_customers}</span>
            </div>
        </div>
    </a>
</td>

<td class="col-total-orders px-6 py-4 whitespace-nowrap text-sm text-center">
    <a href="/salesrep/${rep.id}/MyRequests" style="text-decoration: none;">
        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-green-100 text-green-800 font-semibold">
            ${rep.total_orders}
        </span>
    </a>
</td>

<td class="col-pending-orders px-4 py-3 whitespace-nowrap text-sm text-center">
    <a href="/salesrep/${rep.id}/MyRequests" style="text-decoration: none;">
        <span class="${
        rep.pending_orders > 0
            ? 'inline-flex items-center px-2 py-0.5 rounded-md bg-orange-300 text-orange-800 font-semibold'
            : 'inline-flex items-center px-2 py-0.5 rounded-md bg-gray-200 text-gray-500 font-semibold'
    }">
            ${rep.pending_orders}
        </span>
    </a>
</td>

<td class="col-interested-customers px-6 py-4 whitespace-nowrap text-sm text-center">
    <a href="/sales-reps/${rep.id}/clients" style="text-decoration: none;">
        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-purple-100 text-purple-800 font-semibold">
            ${rep.interested_customers}
        </span>
    </a>
</td>
<td data-column="active_agreements_count" class="col-interested-customers px-6 py-4 whitespace-nowrap text-sm text-center">
    <a href="/salesrep/${rep.id}/agreements" style="text-decoration: none;">
        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-green-100 text-green-800 font-semibold">
            ${rep.active_agreements_count}
        </span>
    </a>
</td>

<td data-column="inactive_agreements_count" class="col-interested-customers px-6 py-4 whitespace-nowrap text-sm text-center">
    <a href="/salesrep/${rep.id}/agreements" style="text-decoration: none;">
        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-orange-100 text-orange-800 font-semibold">
            ${rep.inactive_agreements_count}
        </span>
    </a>
</td>

        <td class="col-actions px-6 py-4 whitespace-nowrap text-sm text-gray-500 no-print">
            <div class="action-btns space-x-1 rtl:space-x-reverse no-print">
                <a href="/sales-reps/${rep.id}" class="action-btn view" title="عرض">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="/sales-reps/${rep.id}/edit" class="action-btn edit" title="تعديل">
                    <i class="fas fa-edit"></i>
                </a>
                <a href="/sales-rep/${rep.id}/targets" class="action-btn delete" title="التارجت">
                    <i class="fas fa-bullseye"></i>
                </a>
              <button onclick="openPasswordModal('${rep.id}')" class="action-btn edit-password">
    <i class="fas fa-key mr-1"></i>
</button>
<button onclick="openDeleteModal('${rep.id}')" class="action-btn delete">
  <i class="fas fa-trash-alt mr-1"></i>
</button>

            </div>
        </td>
    `;

    tbody.appendChild(row);
});
}
        // Placeholder functions for buttons
        function addNewSalesRep() {
            window.location.href = "{{ route('sales-reps.create') }}";
        }

        function viewRep(id) {
            window.location.href = `/sales-reps/${id}`;
        }

        function editRep(id) {
            window.location.href = `/sales-reps/${id}/edit`;
        }

        function deleteRep(id) {
            if (confirm('هل أنت متأكد من حذف هذا سفير العلامة التجارية؟')) {
                window.location.href = `/sales-reps/${id}/delete`;
            }
        }

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

        function openColumnsModal() {
            document.getElementById('columnsModal').style.display = 'flex';
            updateColumnsBadge();
        }

        function closeColumnsModal() {
            document.getElementById('columnsModal').style.display = 'none';
        }

        function filterColumns() {
            const searchTerm = document.getElementById('columnsSearch').value.toLowerCase();
            const columnItems = document.querySelectorAll('.column-item');

            columnItems.forEach(item => {
                const columnName = item.querySelector('.column-name').textContent.toLowerCase();
                if (columnName.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

function toggleSelectAllReps() {
    const selectAll = document.getElementById('selectAllCheckbox').checked;
    document.querySelectorAll('.rep-checkbox').forEach(checkbox => {
        checkbox.checked = selectAll;
    });
    updateBulkActionsButton();
}
function updateBulkActionsButton() {
    const checkedBoxes = document.querySelectorAll('.rep-checkbox:checked');
    const bulkActionsBtn = document.getElementById('bulkActionsBtn');

    if (checkedBoxes.length > 0) {
        bulkActionsBtn.disabled = false;
    } else {
        bulkActionsBtn.disabled = true;
    }
}
function setupBulkActions() {
    document.getElementById('bulkActionsBtn').addEventListener('click', function(e) {
        esetupBulkActions.stopPropagation();
        this.closest('.dropdown').classList.toggle('active');
    });

   document.querySelectorAll('#bulkActionsDropdown .status-dropdown-item').forEach(item => {
    item.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const action = this.getAttribute('data-action');
        const selectedIds = Array.from(document.querySelectorAll('.rep-checkbox:checked'))
            .map(checkbox => checkbox.value);

        if (selectedIds.length === 0) {
            alert('يرجى تحديد مندوب واحد على الأقل');
            return;
        }

        if (confirm(`هل أنت متأكد من أنك تريد ${action === 'activate' ? 'تفعيل' : 'تعطيل'} ${selectedIds.length} مندوب؟`)) {
            performBulkAction(selectedIds, action);
        }
    });
});
}
function openPasswordModal(salesRepId) {
    document.getElementById('salesRepId').value = salesRepId;
    const form = document.getElementById('passwordForm');
    form.action = form.action.replace('__ID__', salesRepId);
    document.getElementById('passwordModal').classList.remove('hidden');
}

function closePasswordModal() {
    document.getElementById('passwordModal').classList.add('hidden');
    document.getElementById('passwordForm').reset();
}

function openDeleteModal(salesRepId) {
    document.getElementById('deleteSalesRepId').value = salesRepId;
    const form = document.getElementById('deleteForm');
    form.action = form.action.replace('__ID__', salesRepId);
    document.getElementById('deleteModal').classList.remove('hidden');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteForm').reset();
}


document.getElementById('passwordForm').addEventListener('submit', async function (e) {
    e.preventDefault();

    const form = this;
    const submitBtn = form.querySelector('button[type="submit"]');
    const newPassword = document.getElementById('salesrepPassword').value;
    const confirmPassword = document.getElementById('confirmPassword').value;

    if (newPassword !== confirmPassword) {
        alert('كلمة المرور وتأكيدها غير متطابقين');
        return;
    }

    submitBtn.disabled = true;
    submitBtn.innerText = 'جاري الحفظ...';

    try {
        const response = await fetch(form.action, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                _method: 'PUT',
                salesrepPassword: newPassword,
                salesrepPassword_confirmation: confirmPassword
            })
        });

        const data = await response.json();

        if (!response.ok) {
            // Handle validation errors
            if (data.errors) {
                let errorMessages = [];
                for (const field in data.errors) {
                    errorMessages.push(...data.errors[field]);
                }
                alert(errorMessages.join('\n'));
            } else {
                throw new Error(data.message || 'حدث خطأ أثناء تحديث كلمة المرور');
            }
            return;
        }

        alert(data.message || 'تم تحديث كلمة المرور بنجاح');
        closePasswordModal();
        window.location.reload();
    } catch (error) {
        console.error('Error:', error);
        alert(error.message || 'حدث خطأ غير متوقع');
    } finally {
        submitBtn.disabled = false;
        submitBtn.innerText = 'حفظ كلمة المرور';
    }
});
function performBulkAction(ids, action) {
    const url = `admin/sales-reps/bulk-${action}`; // Updated endpoint
    const token = document.querySelector('meta[name="csrf-token"]').content;

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ ids: ids })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`تم ${action === 'activate' ? 'تفعيل' : 'تعطيل'} الحسابات المحددة بنجاح`);
            window.location.reload();
        } else {
            alert('حدث خطأ أثناء تنفيذ العملية: ' + (data.message || ''));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ أثناء تنفيذ العملية');
    });
}
        function toggleSelectAll() {
            const checkboxes = document.querySelectorAll('.column-checkbox input');
            const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);

            checkboxes.forEach(checkbox => {
                checkbox.checked = !allChecked;
                // Trigger change event to update UI
                const event = new Event('change');
                checkbox.dispatchEvent(event);
            });

            updateColumnsBadge();
        }

        function resetSelection() {
            const checkboxes = document.querySelectorAll('.column-checkbox input');
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
                // Trigger change event to update UI
                const event = new Event('change');
                checkbox.dispatchEvent(event);
            });

            updateColumnsBadge();
        }

        function applyColumnSelection() {
            const selectedColumns = [];
            const checkboxes = document.querySelectorAll('.column-checkbox input:checked');

            checkboxes.forEach(checkbox => {
                selectedColumns.push(checkbox.value);
            });

            // Update table columns visibility based on selection
            updateTableColumns(selectedColumns);
            updateColumnsBadge();
            closeColumnsModal();
        }

        function updateTableColumns(selectedColumns) {
            // Map between checkbox values and column classes/names
            const columnMapping = {
                'personal_image': 'الصورة الشخصية',
                'name': ' سفير العلامة التجارية',
                'start_work_date': 'تاريخ الالتحاق بالعمل',
                'work_duration': 'مدة العمل',
                'target_customers': ' العملاء المستهدفين',
                'late_customers': ' العملاء المتأخرين',
                'total_orders': ' الطلبات الإجمالية',
                'pending_orders': ' الطلبات المعلقة',
                'interested_customers': ' العملاء المهتمين والمحتملين',
                'active_agreements_count': '  الاتفاقيات النشطة',
                'inactive_agreements_count': '  الاتفاقيات غير النشطة',
            };

            // Get all table headers
            const headers = document.querySelectorAll('.data-table thead th');

            headers.forEach((header, index) => {
                const headerText = header.textContent.trim();
                const isActionColumn = headerText === 'الإجراءات';

                // Always show action column
                if (isActionColumn) {
                    header.style.display = '';
                    document.querySelectorAll('.data-table tbody tr').forEach(row => {
                        if (row.cells[index]) row.cells[index].style.display = '';
                    });
                    return;
                }

                // Check if this column should be visible
                const columnKey = Object.keys(columnMapping).find(key => columnMapping[key] === headerText);
                const shouldShow = selectedColumns.includes(columnKey);

                // Set visibility
                header.style.display = shouldShow ? '' : 'none';
                document.querySelectorAll('.data-table tbody tr').forEach(row => {
                    if (row.cells[index]) row.cells[index].style.display = shouldShow ? '' : 'none';
                });
            });
        }
function updateColumnsBadge() {
            const checkedCount = document.querySelectorAll('.column-checkbox input:checked').length;
            document.getElementById('columnsBadge').textContent = checkedCount;
        }

        function getSelectedColumns() {
            const checkboxes = document.querySelectorAll('.column-checkbox input:checked');
            return Array.from(checkboxes).map(checkbox => checkbox.value);
        }


function exportSalesReps(type = 'csv', selectedColumns = null) {
    if (!selectedColumns || selectedColumns.length === 0) {
        selectedColumns = Array.from(document.querySelectorAll('.column-checkbox input[type="checkbox"]'))
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);
    }

    const columnMap = {
        'name': ' سفير العلامة التجارية',
        'start_work_date': 'تاريخ الالتحاق بالعمل',
        'work_duration': 'مدة العمل',
        'target_customers': ' العملاء المستهدفين',
        'late_customers': ' العملاء المتأخرين',
        'total_orders': ' الطلبات الإجمالية',
        'pending_orders': ' الطلبات المعلقة',
        'interested_customers': ' العملاء المهتمين والمحتملين',
        'active_agreements_count': '  الاتفاقيات النشطة',
        'inactive_agreements_count': '  الاتفاقيات غير النشطة',
    };

    const headers = selectedColumns.map(key => columnMap[key] || key);

    const data = currentFilteredReps.map(rep => {
        const row = {};
        selectedColumns.forEach(key => {
            let value = rep[key] || '';
            if (key === 'achieved_target_percentage') {
                value = rep.achieved_target_percentage + '%';
            } else if (key === 'start_work_date') {
                value = formatDateForDisplay(rep.start_work_date);
            }
            row[key] = value;
        });
        return row;
    });

 if (type === 'csv') {
        let csvContent = '\uFEFF';
        csvContent += headers.map(h => `"${h.replace(/"/g, '""')}"`).join(',') + '\r\n';

        data.forEach(row => {
            const rowValues = selectedColumns.map(key => {
                let value = row[key] || '';
                value = String(value).replace(/"/g, '""');
                return `"${value}"`;
            });
            csvContent += rowValues.join(',') + '\r\n';
        });

        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `مندوبو_المبيعات_${new Date().toISOString().slice(0, 10)}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
    } else if (type === 'pdf') {
exportPDF(selectedColumns);
    }
}
function fixArabicText(text) {
    return text
        .replace(/\(/g, '⁽')
        .replace(/\)/g, '⁾')
        .replace(/\+/g, '＋');
}

        function exportPDF(selectedColumns) {
            try {
                // Create a clean container for PDF generation
                const pdfContainer = document.createElement('div');
                pdfContainer.className = 'pdf-export-container';
                pdfContainer.style.width = '100%';
                pdfContainer.style.padding = '20px';
                pdfContainer.style.fontFamily = 'Tajawal, sans-serif';
                pdfContainer.style.direction = 'rtl';
                pdfContainer.style.backgroundColor = '#ffffff';

                // Create enhanced header with white background and proper image
                const header = `
            <div class="pdf-header" style="display: block; background-color: #ffffff; padding: 20px; margin-bottom: 20px; border-bottom: 2px solid #4154f1;">
                <div class="header-content" style="display: flex; flex-direction: column; align-items: center; gap: 15px;">
                    <img src="${window.location.origin}/assets/img/logo.png" alt="Logo"
                         style="max-height: 80px; width: auto;">
                    <h2 style="font-size: 24px; font-weight: bold; color: #4154f1; margin: 0;">
                        تقرير سفراء العلامة التجارية
                    </h2>
                </div>
                <div style="display: flex; justify-content: center; gap: 30px; font-size: 14px; margin-top: 10px;">
                    <span style="background: #f8f9fa; padding: 5px 15px; border-radius: 20px; color: #495057; border: 1px solid #dee2e6;">
                        تاريخ التقرير: ${new Date().toLocaleDateString('ar-EG')}
                    </span>
                    <span style="background: #f8f9fa; padding: 5px 15px; border-radius: 20px; color: #495057; border: 1px solid #dee2e6;">
                        الوقت: ${new Date().toLocaleTimeString('ar-EG')}
                    </span>
                </div>
            </div>
        `;

                // Create enhanced footer
                const footer = `
            <div class="pdf-footer" style="display: block; margin-top: 30px; padding: 15px 0; border-top: 2px solid #dee2e6; background: #f8f9fa;">
                <p style="margin: 0; color: #6c757d; font-size: 12px; text-align: center;">
                    جميع الحقوق محفوظة &copy; شركة آفاق الخليج ${new Date().getFullYear()}
                    | تم إنشاء التقرير في: ${new Date().toLocaleString('ar-EG')}
                </p>
            </div>
        `;

                // Clone and prepare the table
                const originalTable = document.querySelector('.data-table');
                const table = originalTable.cloneNode(true);

                // Remove ONLY no-print columns (checkbox and actions), NOT the image column
                const headers = table.querySelectorAll('thead th');
                headers.forEach((header, index) => {
                    const headerText = header.textContent.trim();
                    // Remove only actions column and checkbox column
                    if (headerText === 'الإجراءات' || header.classList.contains('no-print')) {
                        header.remove();
                        // Remove corresponding cells from all rows
                        table.querySelectorAll('tbody tr').forEach(row => {
                            if (row.cells[index]) row.cells[index].remove();
                        });
                    }
                });

                // DO NOT remove the image column - let it be handled by column selection

                // Apply column selection
                const columnMap = {
                    'personal_image': 'الصورة الشخصية',
                    'name': 'سفير العلامة التجارية',
                    'start_work_date': 'تاريخ الالتحاق بالعمل',
                    'work_duration': 'مدة العمل',
                    'target_customers': 'العملاء المستهدفين',
                    'late_customers': 'العملاء المتأخرين',
                    'total_orders': 'الطلبات الإجمالية',
                    'pending_orders': 'الطلبات المعلقة',
                    'interested_customers': 'العملاء المهتمين والمحتملين',
                    'active_agreements_count': 'الاتفاقيات النشطة',
                    'inactive_agreements_count': 'الاتفاقيات غير النشطة'
                };

                // Re-get headers after initial cleanup
                const finalHeaders = table.querySelectorAll('thead th');
                finalHeaders.forEach((header, index) => {
                    const headerText = header.textContent.trim();
                    const columnKey = Object.keys(columnMap).find(key => columnMap[key] === headerText);

                    if (columnKey && !selectedColumns.includes(columnKey)) {
                        header.style.display = 'none';
                        table.querySelectorAll('tbody tr').forEach(row => {
                            if (row.cells[index]) row.cells[index].style.display = 'none';
                        });
                    }
                });

                // Enhance table styling for PDF with image support
                table.style.width = '100%';
                table.style.borderCollapse = 'collapse';
                table.style.fontFamily = 'Tajawal, sans-serif';
                table.style.marginBottom = '20px';

                table.querySelectorAll('thead th').forEach(th => {
                    th.style.background = 'linear-gradient(135deg, #4154f1 0%, #6a7ef9 100%)';
                    th.style.color = 'white';
                    th.style.padding = '12px 8px';
                    th.style.border = '1px solid #2d3db8';
                    th.style.fontWeight = 'bold';
                    th.style.textAlign = 'center';
                });

                table.querySelectorAll('tbody td').forEach(td => {
                    td.style.padding = '10px 8px';
                    td.style.border = '1px solid #dee2e6';
                    td.style.textAlign = 'center';
                    td.style.verticalAlign = 'middle';

                    // Special styling for image cells
                    if (td.querySelector('img')) {
                        td.style.padding = '8px 4px';
                    }
                });

                // Style images for PDF
                table.querySelectorAll('tbody td img').forEach(img => {
                    img.style.maxWidth = '50px';
                    img.style.maxHeight = '50px';
                    img.style.borderRadius = '50%';
                    img.style.border = '2px solid #e2e8f0';
                    img.style.objectFit = 'cover';
                    img.style.display = 'block';
                    img.style.margin = '0 auto';
                });

                table.querySelectorAll('tbody tr:nth-child(even)').forEach(tr => {
                    tr.style.backgroundColor = '#f8f9fa';
                });

                // Add page break handling
                const rowsPerPage = 12; // Reduced to accommodate images
                const tableRows = table.querySelectorAll('tbody tr');

                for (let i = rowsPerPage; i < tableRows.length; i += rowsPerPage) {
                    if (tableRows[i]) {
                        tableRows[i].style.pageBreakBefore = 'always';
                    }
                }

                // Build the PDF content
                pdfContainer.innerHTML = header + table.outerHTML + footer;

                // Add to document temporarily
                document.body.appendChild(pdfContainer);

                // PDF options with enhanced image handling
                const options = {
                    margin: [10, 10, 10, 10], // Reduced margins for more space
                    filename: `تقرير_سفراء_العلامة_التجارية_${new Date().toISOString().slice(0,10)}.pdf`,
                    image: {
                        type: 'jpeg',
                        quality: 0.95
                    },
                    html2canvas: {
                        scale: 2,
                        useCORS: true,
                        logging: false,
                        backgroundColor: '#ffffff',
                        allowTaint: true, // Allow tainted images
                        onclone: function(clonedDoc) {
                            // Remove any remaining no-print elements
                            const clonedNoPrint = clonedDoc.querySelectorAll('.no-print');
                            clonedNoPrint.forEach(el => el.remove());

                            // Ensure table styling
                            const clonedTable = clonedDoc.querySelector('.data-table');
                            if (clonedTable) {
                                clonedTable.style.width = '100%';
                                clonedTable.style.borderCollapse = 'collapse';
                            }

                            // Ensure header has white background
                            const clonedHeader = clonedDoc.querySelector('.pdf-header');
                            if (clonedHeader) {
                                clonedHeader.style.backgroundColor = '#ffffff';
                                clonedHeader.style.color = '#333';
                            }

                            // Ensure images are properly sized
                            const clonedImages = clonedDoc.querySelectorAll('img');
                            clonedImages.forEach(img => {
                                if (img.classList.contains('header-logo')) {
                                    img.style.maxHeight = '80px';
                                } else if (img.parentElement.closest('td')) {
                                    // Table images
                                    img.style.maxWidth = '50px';
                                    img.style.maxHeight = '50px';
                                    img.style.borderRadius = '50%';
                                }
                            });
                        }
                    },
                    jsPDF: {
                        unit: 'mm',
                        format: 'a3',
                        orientation: 'landscape',
                        compress: true
                    },
                    pagebreak: {
                        mode: ['avoid-all', 'css', 'legacy'],
                        avoid: ['tr', 'td', 'th']
                    }
                };

                // Generate PDF
                html2pdf()
                    .set(options)
                    .from(pdfContainer)
                    .toPdf()
                    .get('pdf')
                    .then(function(pdf) {
                        // Add page numbers
                        const totalPages = pdf.internal.getNumberOfPages();
                        for (let i = 1; i <= totalPages; i++) {
                            pdf.setPage(i);
                            pdf.setFontSize(10);
                            pdf.setTextColor(100, 100, 100);
                            pdf.text(
                                `صفحة ${i} من ${totalPages}`,
                                pdf.internal.pageSize.getWidth() - 20,
                                pdf.internal.pageSize.getHeight() - 10
                            );
                        }
                    })
                    .save()
                    .finally(() => {
                        // Clean up
                        document.body.removeChild(pdfContainer);
                    });

            } catch (error) {
                console.error('PDF export error:', error);
                alert('حدث خطأ أثناء إنشاء ملف PDF. الرجاء المحاولة مرة أخرى.');

                // Clean up even on error
                const container = document.querySelector('.pdf-export-container');
                if (container) {
                    document.body.removeChild(container);
                }
            }
        }



function getAchievementClass(percentage) {
    if (percentage === undefined) return 'inline-flex items-center px-3 py-1 rounded-full bg-gray-100 text-gray-800 font-semibold';

    return percentage < 70
        ? 'inline-flex items-center px-3 py-1 rounded-full bg-red-100 text-red-800 font-semibold'
        : percentage < 100
            ? 'inline-flex items-center px-3 py-1 rounded-full bg-orange-100 text-orange-800 font-semibold'
            : 'inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-800 font-semibold';
}
function exportSalesReps(selectedColumns = null) {
    if (!selectedColumns || selectedColumns.length === 0) {
        selectedColumns = Array.from(document.querySelectorAll('.column-checkbox input[type="checkbox"]'))
            .filter(checkbox => checkbox.checked)
            .map(checkbox => checkbox.value);
    }

    const columnMap = {
        'name': ' سفير العلامة التجارية',
        'start_work_date': 'تاريخ الالتحاق بالعمل',
        'work_duration': 'مدة العمل',
        'target_customers': ' العملاء المستهدفين',
        'late_customers': ' العملاء المتأخرين',
        'total_orders': ' الطلبات الإجمالية',
        'pending_orders': ' الطلبات المعلقة',
        'interested_customers': ' العملاء المهتمين والمحتملين',
        'active_agreements_count': '  الاتفاقيات النشطة',
        'inactive_agreements_count': '  الاتفاقيات غير النشطة',    };

    const headers = selectedColumns.map(key => columnMap[key] || key);

const data = currentFilteredReps.map(rep => {
    const row = {};
    selectedColumns.forEach(key => {
        let value = '';

        if (key === 'start_work_date' && rep.start_work_date) {
            value = formatDateForDisplay(rep.start_work_date);
        } else if (rep[key] !== undefined && rep[key] !== null) {
            value = rep[key];
        }

        row[key] = value;
    });
    return row;
});

    // Prepare worksheet data: headers + rows
    const wsData = [headers, ...data.map(row => selectedColumns.map(key => row[key]))];
    const worksheet = XLSX.utils.aoa_to_sheet(wsData);

    // Auto-size columns based on content length
    const colWidths = wsData[0].map((_, colIndex) => {
        const maxLen = wsData.reduce((max, row) => {
            const cell = row[colIndex] ? String(row[colIndex]) : '';
            return Math.max(max, cell.length);
        }, 10);
        return { wch: maxLen + 2 };
    });
    worksheet['!cols'] = colWidths;

    // Create workbook and append sheet
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, "مندوبو المبيعات");

    // Trigger file download
    XLSX.writeFile(workbook, `مندوبو_المبيعات_${new Date().toISOString().slice(0, 10)}.xlsx`);
}

function getColumnKey(columnName) {
const columnMap = {
    'personal_image': 'الصورة الشخصية',
    'سفير العلامة التجارية': 'name',
    'تاريخ الالتحاق بالعمل': 'start_work_date',
    'مدة العمل': 'work_duration',
    'العملاء المستهدفين': 'target_customers',
    'العملاء المتأخرين': 'late_customers',
    'الطلبات الإجمالية': 'total_orders',
    'الطلبات المعلقة': 'pending_orders',
    'العملاء المهتمين والمحتملين': 'interested_customers',
    'الاتفاقيات النشطة': 'active_agreements_count',
    'الاتفاقيات غير النشطة': 'inactive_agreements_count',
};

    return columnMap[columnName] || columnName;
}



 function formatDateForDisplay(date) {
            if (!date) return "";
            try {
                const d = new Date(date);
                return isNaN(d.getTime()) ? String(date) : d.toLocaleDateString('ar-EG');
            } catch (e) {
                console.warn("Date formatting failed for:", date);
                return String(date);
            }
        }

    </script>
    @endpush
</body>
