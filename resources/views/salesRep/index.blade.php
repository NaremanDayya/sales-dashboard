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
                <button class="btn btn-primary no-print" onclick="addNewSalesRep()">
                    <i class="fas fa-plus"></i> إضافة مندوب
                </button>
                <div class="export-btn-group no-print">
                    <button id="columnsBtn" class="export-btn columns-btn" onclick="openColumnsModal()">
                        <span class="btn-icon"><i class="fas fa-columns"></i></span>
                        <span class="btn-text">اختيار الأعمدة</span>
                        <span id="columnsBadge" class="columns-badge">9</span>
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
                                        <input type="checkbox" value="name" checked>
                                        <span class="checkmark"></span>
                                        <span class="column-name">اسم المندوب</span>
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
                                        <span class="column-name">عدد العملاء المستهدفين</span>
                                    </label>
                                </div>
                                <div class="column-item">
                                    <label class="column-checkbox">
                                        <input type="checkbox" value="late_customers" checked>
                                        <span class="checkmark"></span>
                                        <span class="column-name">عدد العملاء المتأخرين</span>
                                    </label>
                                </div>
                                <div class="column-item">
                                    <label class="column-checkbox">
                                        <input type="checkbox" value="total_orders" checked>
                                        <span class="checkmark"></span>
                                        <span class="column-name">عدد الطلبات الإجمالية</span>
                                    </label>
                                </div>
                                <div class="column-item">
                                    <label class="column-checkbox">
                                        <input type="checkbox" value="pending_orders" checked>
                                        <span class="checkmark"></span>
                                        <span class="column-name">عدد الطلبات المعلقة</span>
                                    </label>
                                </div>
                                <div class="column-item">
                                    <label class="column-checkbox">
                                        <input type="checkbox" value="interested_customers" checked>
                                        <span class="checkmark"></span>
                                        <span class="column-name">عدد العملاء المهتمين والمحتملين</span>
                                    </label>
                                </div>
                                <div class="column-item">
                                    <label class="column-checkbox">
                                        <input type="checkbox" value="achieved_target_percentage" checked>
                                        <span class="checkmark"></span>
                                        <span class="column-name">نسبة التارجت المتحقق</span>
                                    </label>
                                </div>
                                <div class="column-item">
                                    <label class="column-checkbox">
                                        <input type="checkbox" value="achieved_target_amount" checked>
                                        <span class="checkmark"></span>
                                        <span class="column-name">كمية التارجت المتحقق</span>
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
                            <th>كمية التارجت المتحقق</th>
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
        let salesRepsData = [];
        let currentFilteredReps = [];
        // Initialize column selector
        document.addEventListener('DOMContentLoaded', function() {

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
            });
            renderTable(currentFilteredReps);
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
                        exportToPDF(selectedColumns);
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
        <td class="col-name px-6 py-4 whitespace-nowrap text-sm font-bold text-blue-700">
            <span class="inline-block bg-blue-100 text-blue-800 px-3 py-1 rounded-full">
                ${rep.name}
            </span>
        </td>

        <td class="col-start-date font-bold px-6 py-4 whitespace-nowrap text-sm text-gray-800">
            ${formatDateForDisplay(rep.start_work_date)}
        </td>

        <td class="col-work-duration px-6 py-4 whitespace-nowrap text-sm text-gray-800">
            ${rep.work_duration}
        </td>

        <td class="col-target-customers px-6 py-4 whitespace-nowrap text-sm text-center">
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                ${rep.target_customers}
            </span>
        </td>

        <td class="col-late-customers px-4 py-3 whitespace-nowrap text-sm text-center">
            <span class="${
                rep.late_customers > 0
                    ? 'inline-flex items-center px-2 py-0.5 rounded-md bg-red-300 text-red-800 font-semibold'
                    : 'text-gray-500'
            }">
                ${rep.late_customers}
            </span>
        </td>

        <td class="col-total-orders px-6 py-4 whitespace-nowrap text-sm text-center">
            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-green-100 text-green-800">
                ${rep.total_orders}
            </span>
        </td>

        <td class="col-pending-orders px-4 py-3 whitespace-nowrap text-sm text-center">
            <span class="${
                rep.pending_orders > 0
                    ? 'inline-flex items-center px-2 py-0.5 rounded-md bg-orange-300 text-orange-800 font-semibold'
                    : 'text-gray-500'
            }">
                ${rep.pending_orders}
            </span>
        </td>

        <td class="col-interested-customers px-6 py-4 whitespace-nowrap text-sm text-center">
            <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-purple-100 text-purple-800">
                ${rep.interested_customers}
            </span>
        </td>

       <td class="col-achieved-target-percentage px-6 py-4 whitespace-nowrap text-sm text-center">
    <span class="${getAchievementClass(rep.achieved_target_percentage)}">
        ${rep.achieved_target_percentage !== undefined ? rep.achieved_target_percentage + '%' : 'N/A'}
    </span>
</td>

<td class="col-achieved-target-amount px-6 py-4 whitespace-nowrap text-sm text-center">
    <span class="${getAchievementClass(rep.achieved_target_percentage)}">
        ${rep.achieved_target_amount !== undefined ? rep.achieved_target_amount : 'N/A'}
    </span>
</td>

        <td class="col-actions px-6 py-4 whitespace-nowrap text-sm text-gray-500 no-print">
            <div class="action-btns space-x-1 rtl:space-x-reverse">
                <a href="/sales-reps/${rep.id}" class="action-btn view" title="عرض">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="/sales-reps/${rep.id}/edit" class="action-btn edit" title="تعديل">
                    <i class="fas fa-edit"></i>
                </a>
                <a href="/sales-rep/${rep.id}/targets" class="action-btn delete" title="التارجت">
                    <i class="fas fa-bullseye"></i>
                </a>
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
            if (confirm('هل أنت متأكد من حذف هذا المندوب؟')) {
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
        'name': 'اسم المندوب',
        'start_work_date': 'تاريخ الالتحاق بالعمل',
        'work_duration': 'مدة العمل',
        'target_customers': 'عدد العملاء المستهدفين',
        'late_customers': 'عدد العملاء المتأخرين',
        'total_orders': 'عدد الطلبات الإجمالية',
        'pending_orders': 'عدد الطلبات المعلقة',
        'interested_customers': 'عدد العملاء المهتمين والمحتملين',
        'achieved_target_percentage': 'نسبة التارجت المتحقق',
        'achieved_target_amount': 'كمية التارجت المتحقق',
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
        'name': 'اسم المندوب',
        'start_work_date': 'تاريخ الالتحاق بالعمل',
        'work_duration': 'مدة العمل',
        'target_customers': 'عدد العملاء المستهدفين',
        'late_customers': 'عدد العملاء المتأخرين',
        'total_orders': 'عدد الطلبات الإجمالية',
        'pending_orders': 'عدد الطلبات المعلقة',
        'interested_customers': 'عدد العملاء المهتمين والمحتملين',
        'achieved_target_percentage': 'نسبة التارجت المتحقق',
        'achieved_target_amount': 'كمية التارجت المتحقق',
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
        exportToPDF(selectedColumns);
    }
}

function exportToPDF(selectedColumns) {
    try {
        // Clone the print area
        const element = document.getElementById('print-area').cloneNode(true);

        // Hide elements that shouldn't be in PDF
        const elementsToHide = element.querySelectorAll('.no-print, .table-actions, .table-filters, .pagination');
        elementsToHide.forEach(el => el.style.display = 'none');

        // Show the PDF header and footer
        const pdfHeader = element.querySelector('.pdf-header');
        const pdfFooter = element.querySelector('.pdf-footer');
        if (pdfHeader) pdfHeader.style.display = 'block';
        if (pdfFooter) pdfFooter.style.display = 'block';

        // Handle column visibility
        const columnMap = {
            'name': 'اسم المندوب',
            'start_work_date': 'تاريخ الالتحاق بالعمل',
            'work_duration': 'مدة العمل',
            'target_customers': 'عدد العملاء المستهدفين',
            'late_customers': 'عدد العملاء المتأخرين',
            'total_orders': 'عدد الطلبات الإجمالية',
            'pending_orders': 'عدد الطلبات المعلقة',
            'interested_customers': 'عدد العملاء المهتمين والمحتملين',
            'achieved_target_percentage': 'نسبة التارجت المتحقق',
            'achieved_target_amount': 'كمية التارجت المتحقق'
        };

        // Get all table headers and cells
        const table = element.querySelector('.data-table');
        if (table) {
            const headers = table.querySelectorAll('thead th');
            headers.forEach((header, index) => {
                const headerText = header.textContent.trim();
                const columnKey = Object.keys(columnMap).find(key => columnMap[key] === headerText);

                // Skip action column
                if (headerText === 'الإجراءات') {
                    header.style.display = 'none';
                    table.querySelectorAll('tbody tr').forEach(row => {
                        if (row.cells[index]) row.cells[index].style.display = 'none';
                    });
                    return;
                }
  if (header.classList.contains('no-print')) {
                    header.style.display = 'none';
                    table.querySelectorAll('tbody tr').forEach(row => {
                        if (row.cells[index]) {
                            row.cells[index].style.display = 'none';
                        }
                    });
                    return;
                }
                // Hide if column not selected
                if (columnKey && !selectedColumns.includes(columnKey)) {
                    header.style.display = 'none';
                    table.querySelectorAll('tbody tr').forEach(row => {
                        if (row.cells[index]) row.cells[index].style.display = 'none';
                    });
                }
            });
        }

        // PDF options
        const options = {
            margin: 10,
            filename: `مندوبو_المبيعات_${new Date().toISOString().slice(0,10)}.pdf`,
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

        // Generate PDF
        html2pdf().set(options).from(element).save();

    } catch (error) {
        console.error('PDF generation error:', error);
        alert('حدث خطأ أثناء إنشاء ملف PDF. الرجاء المحاولة مرة أخرى.');
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
        'name': 'اسم المندوب',
        'start_work_date': 'تاريخ الالتحاق بالعمل',
        'work_duration': 'مدة العمل',
        'target_customers': 'عدد العملاء المستهدفين',
        'late_customers': 'عدد العملاء المتأخرين',
        'total_orders': 'عدد الطلبات الإجمالية',
        'pending_orders': 'عدد الطلبات المعلقة',
        'interested_customers': 'عدد العملاء المهتمين والمحتملين',
        'achieved_target_percentage': 'نسبة التارجت المتحقق',
        'achieved_target_amount': 'كمية التارجت المتحقق',
    };

    const headers = selectedColumns.map(key => columnMap[key] || key);

    const data = currentFilteredReps.map(rep => {
        const row = {};
        selectedColumns.forEach(key => {
        let value = (rep[key] !== undefined && rep[key] !== null) ? rep[key] : '';
            if (key === 'achieved_target_percentage') {
                value = rep.achieved_target_percentage + '%';
            } else if (key === 'start_work_date') {
                value = formatDateForDisplay(rep.start_work_date);
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
