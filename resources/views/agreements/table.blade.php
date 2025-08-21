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
    width: 100% !important;
    max-width: 100% !important;
    margin: 0 !important;
    padding: 1rem;
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    flex: 1 1 auto;
    display: flex;
    flex-direction: column;
}

.main {
    max-width: 100%;
}
.w-full {
    padding-left: 0;
    padding-right: 0;
}

    .dropdown-container { position: relative; display: inline-block; font-family: Arial, sans-serif;
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
        font-weight: 800;
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
    width: 100% !important;
    max-width: 100% !important;
    flex: 1 1 auto;
}
    .data-table {
width: 100% !important;
    min-width: 100% !important;
    max-width: 100% !important;
    border-collapse: collapse;
    }

    .data-table thead th {
        background-color: var(--gray-100);
        color: var(--gray-600);
        font-weight: 600;
        padding: 5px 8px;
        text-align: center;
        border-bottom: 2px solid var(--gray-200);
        position: sticky;
        top: 0;
	white-space: nowrap;
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
        padding: 5px 8px !important;
        border-bottom: 1px solid var(--gray-200);
        text-align: center;
	font-size: 14px;
        font-weight: 800;

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
        font-weight: 800;
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
            @if(Auth::user()->role == 'salesRep')
            <a class="btn btn-primary" href="{{ route('salesrep.agreements.create', Auth::user()->salesRep->id) }}">
                <i class="fas fa-plus"></i> إضافة إتفاقية
            </a>
            @endif
            <div class="export-btn-group">
                <button id="columnsBtn" class="export-btn columns-btn" onclick="openColumnsModal()">
                    <span class="btn-icon"><i class="fas fa-columns"></i></span>
                    <span class="btn-text">اختيار الأعمدة</span>
                    <span id="columnsBadge" class="columns-badge">13</span>
                </button>
            </div>
            <div class="export-options">
                <div class="dropdown">
                    <button class="btn btn-dropdown" id="exportBtn" type="button">
                                تصدير البيانات
                        <svg width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
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
تصدير كملف Excel                        </button>
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
                                    <input type="checkbox" value="client_logo" checked>
                                    <span class="checkmark"></span>
                                    <span class="column-name">شعار العميل</span>
                                </label>
                            </div>
                            <div class="column-item">
                                <label class="column-checkbox">
                                    <input type="checkbox" value="client_name" checked>
                                    <span class="checkmark"></span>
                                    <span class="column-name"> العميل</span>
                                </label>
                            </div>
			    <div class="column-item">
                                <label class="column-checkbox">
                                    <input type="checkbox" value="sales_Rep_name" checked> 
                                    <span class="checkmark"></span>
                                    <span class="column-name"> سفير العلامة التجارية</span> 
                                </label>
                            </div>

                            <div class="column-item">
                                <label class="column-checkbox">
                                    <input type="checkbox" value="signing_date" checked>
                                    <span class="checkmark"></span>
                                    <span class="column-name"> توقيع الاتفاقية</span>
                                </label>
                            </div>
                            <div class="column-item">
                                <label class="column-checkbox">
                                    <input type="checkbox" value="duration_years" checked>
                                    <span class="checkmark"></span>
                                    <span class="column-name">مدة الاتفاقية </span>
                                </label>
                            </div>
                            <div class="column-item">
                                <label class="column-checkbox">
                                    <input type="checkbox" value="termination_type" checked>
                                    <span class="checkmark"></span>
                                    <span class="column-name"> إنهاء الاتفاقية</span>
                                </label>
                            </div>
                            <div class="column-item">
                                <label class="column-checkbox">
                                    <input type="checkbox" value="implementation_date" checked>
                                    <span class="checkmark"></span>
                                    <span class="column-name"> تنفيذ الاتفاقية</span>
                                </label>
                            </div>
                            <div class="column-item">
                                <label class="column-checkbox">
                                    <input type="checkbox" value="end_date" checked>
                                    <span class="checkmark"></span>
                                    <span class="column-name"> انتهاء الاتفاقية</span>
                                </label>
                            </div>
                            <div class="column-item">
                                <label class="column-checkbox">
                                    <input type="checkbox" value="notice_months" checked>
                                    <span class="checkmark"></span>
                                    <span class="column-name">أشهر الإخطار </span>
                                </label>
                            </div>
                            <div class="column-item">
                                <label class="column-checkbox">
                                    <input type="checkbox" value="required_notice_date" checked>
                                    <span class="checkmark"></span>
                                    <span class="column-name"> الإخطار المتوقع</span>
                                </label>
                            </div>
                            <div class="column-item">
                                <label class="column-checkbox">
                                    <input type="checkbox" value="notice_status" checked>
                                    <span class="checkmark"></span>
                                    <span class="column-name">حالة الإخطار</span>
                                </label>
                            </div>
                            <div class="column-item">
                                <label class="column-checkbox">
                                    <input type="checkbox" value="service_type" checked>
                                    <span class="checkmark"></span>
                                    <span class="column-name"> الخدمة</span>
                                </label>
                            </div>
                            <div class="column-item">
                                <label class="column-checkbox">
                                    <input type="checkbox" value="product_quantity" checked>
                                    <span class="checkmark"></span>
                                    <span class="column-name">عدد المنتج</span>
                                </label>
                            </div>
                            <div class="column-item">
                                <label class="column-checkbox">
                                    <input type="checkbox" value="price" checked>
                                    <span class="checkmark"></span>
                                    <span class="column-name">التسعيرة</span>
                                </label>
                            </div>
                            <div class="column-item">
                                <label class="column-checkbox">
                                    <input type="checkbox" value="total_amount" checked>
                                    <span class="checkmark"></span>
                                    <span class="column-name">المجموع</span>
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


            <button class="btn btn-outline" onclick="window.print()" title="طباعة التقرير">
                <i class="fas fa-print"></i>
            </button>

        </div>
    </div>

<div class="table-filters">
    <div class="search-box mb-3">
        <input type="text" class="search-input" placeholder="بحث..." id="searchInput">
        <i class="fas fa-search search-icon"></i>
    </div>

    <div class="d-flex align-items-center gap-3 flex-wrap">
        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-filter text-secondary"></i>
            <select id="filterSelect" onchange="applyFilter()" class="form-select w-auto" style="font-size: 14px; font-weight: 800;">
                <option value="">الكل</option>
                <option value="sent">تم الإخطار</option>
                <option value="not sent">لم يتم الإخطار</option>
                <option value="returnable">مشروطة بمقابل</option>
                <option value="non returnable">غير مشروطة بمقابل</option>
            </select>
        </div>

        <div class="d-flex align-items-center gap-2">
            <i class="fas fa-cog text-secondary"></i>
<select id="serviceTypeFilter" onchange="applyFilter()" class="form-select w-auto" style="font-size: 14px; font-weight: 800;">
    <option value=""> الخدمة</option>
    @foreach($services as $service)
        <option value="{{ $service->id }}">{{ $service->name }}</option>
    @endforeach
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
<div class="w-full">
            <table class="data-table min-w-full">
                <thead>
                    <tr>
                        <th>شعار العميل</th>
                        <th> العميل</th>
                        <th> سفير العلامة التجارية</th>                 
        		<th> توقيع الاتفاقية</th>
                        <th>مدة الاتفاقية </th>
                        <th> إنهاء الاتفاقية</th>
                        <th> تنفيذ الاتفاقية</th>
                        <th> انتهاء الاتفاقية</th>
                        <th>أشهر الإخطار </th>
                        <th> الإخطار المتوقع</th>
                        <th>حالة الإخطار</th>
                        <th> الخدمة</th>
                        <th>عدد المنتج</th>
                        <th>التسعيرة</th>
                        <th>المجموع</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <!-- Data will be inserted here -->
                </tbody>
            </table>
</div>
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
<script>
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
                                        (agreement.sales_Rep_name && agreement.sales_Rep_name.toLowerCase().includes(searchTerm)) ||
		    (agreement.termination_type && agreement.termination_type.toLowerCase().includes(searchTerm)) ||
                    (agreement.service_type && agreement.service_type.toLowerCase().includes(searchTerm))
                );
            });
            renderTable(filteredData);
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

        if (exportType === 'xlsx') {
            exportAgreements();
        } else if (exportType === 'pdf') {
            exportToPDF(selectedColumns);
        }
    });
});

        // Prevent dropdown from closing when clicking inside it
        dropdown.addEventListener('click', function(e) {
            e.stopPropagation();
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
                <td class="px-4 py-2 text-sm font-semibold text-gray-800 hover:text-blue-600 hover:underline cursor-pointer whitespace-nowrap overflow-x-auto max-w-xs text-ellipsis">
                    <a href="/salesrep/${agreement.sales_rep_id}/agreements/${agreement.agreement_id}" class="block w-full h-full">
                        ${agreement.client_name || '—'}
                    </a>
                </td>
 <td class="px-4 py-2 text-sm font-semibold text-gray-800 hover:text-blue-600 hover:underline cursor-pointer whitespace-nowrap overflow-x-auto max-w-xs text-ellipsis">
                    <a href="/sales-reps/${agreement.sales_rep_id}" class="block w-full h-full">
                        ${agreement.sales_Rep_name || '—'}
                    </a>
                </td>

<td class="px-4 py-2 text-sm text-gray-700">
    <a href="/salesrep/${agreement.sales_rep_id}/agreements/${agreement.agreement_id}" class="no-underline text-inherit">
        ${formatDateForDisplay(agreement.signing_date) || '—'}
    </a>
</td>

<td class="whitespace-nowrap overflow-x-auto max-w-xs text-ellipsis px-4 py-2 text-sm text-gray-700">
    <a href="/salesrep/${agreement.sales_rep_id}/agreements/${agreement.agreement_id}" class="no-underline text-inherit">
        ${agreement.duration_years || '—'} سنوات
    </a>
</td>

<td class="px-4 py-2 text-sm text-gray-700 whitespace-nowrap overflow-x-auto max-w-xs text-ellipsis">
    <a href="/salesrep/${agreement.sales_rep_id}/agreements/${agreement.agreement_id}" class="no-underline text-inherit">
        ${
            agreement.termination_type === 'returnable'
                ? `مشروطة بمقابل `
                : agreement.termination_type === 'non_returnable'
                    ? 'غير مشروطة بمقابل'
                    : '—'
        }
    </a>
</td>

<td class="px-4 py-2 text-sm text-gray-700">
    <a href="/salesrep/${agreement.sales_rep_id}/agreements/${agreement.agreement_id}" class="no-underline text-inherit">
        ${formatDateForDisplay(agreement.implementation_date) || '—'}
    </a>
</td>

<td class="px-4 py-2 text-sm text-gray-700">
    <a href="/salesrep/${agreement.sales_rep_id}/agreements/${agreement.agreement_id}" class="no-underline text-inherit">
        ${formatDateForDisplay(agreement.end_date) || '—'}
    </a>
</td>

<td class="px-4 py-2 text-sm text-gray-700">
    <a href="/salesrep/${agreement.sales_rep_id}/agreements/${agreement.agreement_id}" class="no-underline text-inherit">
        ${agreement.notice_months || '—'}
    </a>
 <td class="px-4 py-2 text-sm text-center ${ agreement.is_notice_at_time ? 'text-red-600 font-bold' : 'text-green-600 font-bold' }">
                ${formatDateForDisplay(agreement.required_notice_date) || '—'}</td>

<td class="px-4 py-2 text-center whitespace-nowrap overflow-x-auto max-w-xs text-ellipsis">
    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-xs font-semibold ${
        agreement.notice_status === 'sent'
            ? 'bg-green-100 text-green-600'
            : (agreement.notice_status === 'not_sent' || agreement.notice_status === 'not sent')
                ? 'bg-red-100 text-red-600'
                : 'bg-gray-100 text-gray-600'
    }">
        ${
            agreement.notice_status === 'sent'
                ? 'تم الإخطار'
                : (agreement.notice_status === 'not_sent' || agreement.notice_status === 'not sent')
                    ? 'لم يتم الإخطار'
                    : '—'
        }
    </span>
</td>                <td dir="rtl" class="px-4 py-2 text-sm text-gray-700 whitespace-nowrap overflow-x-auto max-w-xs text-ellipsis">${agreement.service_type || '—'}</td>
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
        const headers = document.querySelectorAll('.data-table thead th');
        const rows = document.querySelectorAll('.data-table tbody tr');

        headers.forEach((header, index) => {
            const columnName = header.textContent.trim();
            const columnKey = getColumnKey(columnName);

            if (selectedColumns.includes(columnKey)) {
                header.style.display = '';
                rows.forEach(row => {
                    if (row.cells[index]) {
                        row.cells[index].style.display = '';
                    }
                });
            } else {
                header.style.display = 'none';
                rows.forEach(row => {
                    if (row.cells[index]) {
                        row.cells[index].style.display = 'none';
                    }
                });
            }
        });
    }

    function getColumnKey(columnName) {
        const columnMap =
{
    'شعار العميل': 'client_logo',
    'العميل': 'client_name',
    'سفير العلامة التجارية': 'sales_Rep_name',
    'توقيع الاتفاقية': 'signing_date',
    'مدة الاتفاقية': 'duration_years',
    'إنهاء الاتفاقية': 'termination_type',
    'تنفيذ الاتفاقية': 'implementation_date',
    'انتهاء الاتفاقية': 'end_date',
    'أشهر الإخطار': 'notice_months',
    'الإخطار المتوقع': 'required_notice_date',
    'حالة الإخطار': 'notice_status',
    'الخدمة': 'service_type',
    'عدد المنتج': 'product_quantity',
    'التسعيرة': 'price',
    'المجموع': 'total_amount'
}
        return columnMap[columnName] || columnName.toLowerCase().replace(/\s+/g, '_');
    }

    function updateColumnsBadge() {
        const checkedCount = document.querySelectorAll('.column-checkbox input:checked').length;
        document.getElementById('columnsBadge').textContent = checkedCount;
    }

    // Initialize column checkboxes
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.column-checkbox input');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const checkmark = this.nextElementSibling;
                if (this.checked) {
                    checkmark.style.backgroundColor = 'var(--primary)';
                    checkmark.style.borderColor = 'var(--primary)';
                } else {
                    checkmark.style.backgroundColor = '';
                    checkmark.style.borderColor = 'var(--gray-300)';
                }
                updateColumnsBadge();
            });
        });

        // Initialize with all columns selected
        updateColumnsBadge();
    });

function applyFilter() {
    const criteria = document.getElementById('filterSelect').value;
    const serviceTypeKey = document.getElementById('serviceTypeFilter').value;
    
    // Generate the service type map from PHP (using name_ar if available, otherwise name)
    const serviceTypeMap = {
        @foreach($services as $service)
            '{{ $service->id }}': '{{ $service->name }}',
        @endforeach
    };

    currentFilteredAgreements = [...AgreementsData];

    // Apply criteria filter if selected
    if (criteria && criteria !== "") {
        switch (criteria) {
            case 'sent':
                currentFilteredAgreements = currentFilteredAgreements.filter(agreement =>
                    agreement.notice_status && agreement.notice_status.toLowerCase() === 'sent'
                );
                break;

            case 'not sent':
                currentFilteredAgreements = currentFilteredAgreements.filter(agreement =>
                    agreement.notice_status &&
                    (agreement.notice_status.toLowerCase() === 'not sent' ||
                     agreement.notice_status.toLowerCase() === 'not_sent')
                );
                break;

            case 'returnable':
                currentFilteredAgreements = currentFilteredAgreements.filter(agreement =>
                    agreement.termination_type &&
                    agreement.termination_type.toLowerCase() === 'returnable'
                );
                break;

            case 'non returnable':
                currentFilteredAgreements = currentFilteredAgreements.filter(agreement =>
                    agreement.termination_type &&
                    agreement.termination_type.toLowerCase() === 'non_returnable'
                );
                break;
        }
    }

    // Apply service type filter if selected
    if (serviceTypeKey && serviceTypeKey !== "") {
        const serviceName = serviceTypeMap[serviceTypeKey];
        
        currentFilteredAgreements = currentFilteredAgreements.filter(agreement => {
            if (!agreement.service_type) return false;
            
            // Trim and normalize both values for comparison
            const agreementService = agreement.service_type.trim();
            const targetService = serviceName.trim();
            
            // Case-insensitive comparison for Arabic text
            return agreementService.localeCompare(targetService, undefined, { sensitivity: 'base' }) === 0;
        });
    }

    if (currentFilteredAgreements.length === 0) {
        alert('⚠️ لا يوجد إتفاقيات تطابق معايير التصفية');
    }

    renderTable();
}
    function getSelectedColumns() {
        const checkboxes = document.querySelectorAll('.column-checkbox input:checked');
        const selectedColumns = Array.from(checkboxes).map(checkbox => checkbox.value);
        return selectedColumns;
    }

 function exportAgreements(selectedColumns = null) {
    // If no columns selected, use all available columns
    if (!selectedColumns || selectedColumns.length === 0) {
        selectedColumns = Array.from(document.querySelectorAll('.column-checkbox input[type="checkbox"]'))
                              .filter(checkbox => checkbox.checked)
                              .map(checkbox => checkbox.value);
    }

    // Column names in Arabic
    const columnsMap = {
        client_logo: "شعار العميل",
        client_name: " العميل",
                sales_Rep_name: " سفير العلامة التجارية", 
	signing_date: " توقيع الاتفاقية",
        duration_years: "مدة الاتفاقية ",
        termination_type: " إنهاء الاتفاقية",
        implementation_date: " تنفيذ الاتفاقية",
        end_date: " انتهاء الاتفاقية",
        notice_months: "أشهر الإخطار ",
        required_notice_date: " الإخطار المتوقع",
        notice_status: "حالة الإخطار",
        service_type: " الخدمة",
        product_quantity: "عدد المنتج",
        price: "التسعيرة",
        total_amount: "المجموع"
    };

    const headers = selectedColumns
    .filter(key => key !== 'client_logo')
    .map(key => columnsMap[key]);

    const data = currentFilteredAgreements.map(agreement => {
        const row = {};
        selectedColumns.forEach(key => {
        let value = (agreement[key] !== undefined && agreement[key] !== null) ? agreement[key] : '';
            switch (key) {
                case 'client_logo':
                    row[key] = agreement.client_logo || '';
                    break;
                case 'client_name':
                    row[key] = agreement.client_name || '';
                    break;
		 case 'sales_Rep_name':
                    row[key] = agreement.sales_Rep_name || '';
                    break;
                case 'signing_date':
                    row[key] = formatDateForDisplay(agreement.signing_date);
                    break;
                case 'duration_years':
                    row[key] = agreement.duration_years || '';
                    break;
                case 'termination_type':
                    row[key] = agreement.termination_type === 'returnable'
                        ? `مشروطة بمقابل `
                        : agreement.termination_type === 'non_returnable'
                            ? 'غير مشروطة بمقابل'
                            : '—';
                    break;
                case 'implementation_date':
                    row[key] = formatDateForDisplay(agreement.implementation_date);
                    break;
                case 'end_date':
                    row[key] = formatDateForDisplay(agreement.end_date);
                    break;
                case 'notice_months':
                    row[key] = agreement.notice_months || '';
                    break;
                case 'required_notice_date':
                    row[key] = formatDateForDisplay(agreement.required_notice_date);
                    break;
                case 'notice_status':
                    row[key] = getNoticeStatus(agreement.notice_status);
                    break;
                case 'service_type':
                    row[key] = agreement.service_type || '';
                    break;
                case 'product_quantity':
                    row[key] = agreement.product_quantity || '';
                    break;
                case 'price':
                    row[key] = agreement.price || '';
                    break;
                case 'total_amount':
                    row[key] = agreement.total_amount || '';
                    break;
                default:
                    row[key] = '';
            }
        });
        return row;
    });

    // Prepare worksheet data: headers + rows
    const wsData = [headers, ...data.map(row => selectedColumns
    .filter(key => key !== 'client_logo')
    .map(key => row[key]))];
    const worksheet = XLSX.utils.aoa_to_sheet(wsData);

    // Auto-size columns
    const colWidths = wsData[0].map((_, colIndex) => {
        const maxLen = wsData.reduce((max, row) => {
            const cell = row[colIndex] ? String(row[colIndex]) : '';
            return Math.max(max, cell.length);
        }, 10);
        return { wch: maxLen + 2 };
    });
    worksheet['!cols'] = colWidths;

    // Create and download workbook
    const workbook = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(workbook, worksheet, "اتفاقيات");

    XLSX.writeFile(workbook, `اتفاقيات_الشركة_${new Date().toISOString().slice(0, 10)}.xlsx`);
}
    // CSV Export function
    function exportToCSV(headers, data, selectedKeys) {
        let csvContent = '\uFEFF' + headers.join(',') + '\r\n';

        data.forEach(row => {
            const rowValues = selectedKeys.map(key => {
                let value = row[key] || '';
                // Escape quotes and wrap in quotes if contains comma or special characters
                if (typeof value === 'string' && (value.includes(',') || value.includes('\n') || value.includes('"'))) {
                    return `"${value.replace(/"/g, '""')}"`;
                }
                return value;
            });
            csvContent += rowValues.join(',') + '\r\n';
        });

        // Create download link
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `تقرير_اتفاقيات_الشركة_${new Date().toISOString().slice(0,10)}.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // PDF Export function using html2pdf
function exportToPDF(selectedColumns) {
    // Clone the table to modify for PDF export
    const table = document.querySelector('.data-table').cloneNode(true);
    const pdfHeader = document.querySelector('.pdf-header').cloneNode(true);
    const pdfFooter = document.querySelector('.pdf-footer').cloneNode(true);

    // Style adjustments for header and footer
    pdfHeader.style.display = 'block';
    pdfHeader.style.width = '100%';
    pdfHeader.style.padding = '10px';

    pdfFooter.style.display = 'block';
    pdfFooter.style.width = '100%';
    pdfFooter.style.padding = '15px'; // Increased padding
    pdfFooter.style.minHeight = '80px'; // Minimum height guarantee
    pdfFooter.style.boxSizing = 'border-box';

    // Create a container for the PDF content
    const pdfContainer = document.createElement('div');
    pdfContainer.style.padding = '20px';
    pdfContainer.style.width = '100%';
pdfContainer.style.direction = 'rtl';
pdfContainer.style.textAlign = 'right';
pdfContainer.style.fontFamily = `'Cairo', 'Tajawal', 'Arial', sans-serif'`;
    pdfContainer.appendChild(pdfHeader);
    pdfContainer.appendChild(table);
    pdfContainer.appendChild(pdfFooter);

    // Hide columns that are not selected
    const headers = table.querySelectorAll('thead th');
    headers.forEach((header, index) => {
        const columnName = header.textContent.trim();
        const columnKey = getColumnKey(columnName);

        if (!selectedColumns.includes(columnKey)) {
            header.style.display = 'none';
            table.querySelectorAll('tbody tr').forEach(row => {
                if (row.cells[index]) {
                    row.cells[index].style.display = 'none';
                row.cells[index].direction = 'rtl';
row.cells[index].style.textAlign = 'right';
}
            });
        }
    });
    // Options for html2pdf
    const options = {
        margin: [15, 10, 20, 10], // [top, left, bottom, right] - increased bottom margin
        filename: `تقرير_اتفاقيات_الشركة_${new Date().toISOString().slice(0,10)}.pdf`,
    image: { type: 'jpeg', quality: 0.98 },
        html2canvas: {
            scale: 2,
            ignoreElements: (element) => {
                // Ignore elements that might interfere with layout
                return element.classList.contains('ignore-pdf');
            }
        },
        jsPDF: {
            unit: 'mm',
            format: [594, 420],
            orientation: 'landscape',
            // Add more space for footer
            putOnlyUsedFonts: true,
            compress: true
        }
    };

    // Generate PDF
    html2pdf().set(options).from(pdfContainer).save();
}

    // Helper functions
    function getNoticeStatus(status) {
        if (status === 'sent') return 'تم الإخطار';
        if (status === 'not sent' || status === 'not_sent') return 'لم يتم الإخطار';
        return '—';
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

  function formatDateForCSV(dateString) {
    if (!dateString) return 'لايوجد تاريخ';

    const date = new Date(dateString);
    if (isNaN(date.getTime())) return ''; // Invalid date

    // Format as YYYY-MM-DD (Excel-friendly format)
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');

    return `${year}-${month}-${day}`;
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
