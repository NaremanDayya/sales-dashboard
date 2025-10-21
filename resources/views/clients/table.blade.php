@extends('layouts.master')
@section('title','جدول العملاء')
@push('styles')
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #6366f1;
            --secondary: #10b981;
            --danger: #ef4444;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Tajawal', sans-serif;
        }

        body {
            background-color: #f8fafc;
            color: #1e293b;
            direction: rtl;
        }
        .modal-body {
            overflow-y: auto;
        }
        .dropdown.active .dropdown-menu {
            display: block !important;
        }
        .cell-value {
            font-size: 14px;
            font-weight: 700;
            /*white-space: nowrap;*/
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
        #clientEditModal {
            align-items: flex-start;
            padding-top: 2rem;
            padding-bottom: 2rem;
        }

        #clientEditModal > div {
            max-height: 90vh;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        .clickable-cell {
            cursor: pointer;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .clickable-cell:hover {
            background-color: #f1f5f9;
            color: #4154f1;
            font-weight: 600;
        }

        .hidden {
            display: none !important;
        }

        [x-cloak] {
            display: none !important;
        }

        .ltr-number {
            direction: ltr;
            unicode-bidi: embed;
            display: inline-block;
            font-weight: 600;
        }

        /* Improved Table Styles */
        .table-container {
            overflow-x: auto;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .data-table {
            min-width: 1400px; /* Ensure table has minimum width */
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem; /* Slightly smaller font for better fit */
        }

        .data-table th {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            font-weight: 700;
            padding: 12px 8px;
            text-align: center;
            white-space: nowrap;
            border: none;
            font-size: 0.8rem;
        }

        .data-table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e5e7eb;
            text-align: center;
            vertical-align: middle;
            font-weight: 500;
            background: white;
        }

        .data-table tr:hover td {
            background-color: #f8fafc;
        }

        .data-table tr:nth-child(even) td {
            background-color: #f9fafb;
        }

        /* Make specific content bolder */
        .company-name {
            font-weight: 700 !important;
            color: #1f2937;
        }

        .status-badge {
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.75rem;
        }

        .count-badge {
            font-weight: 700;
            font-size: 0.8rem;
        }

        /* Notification styles */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            z-index: 10000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .notification.success {
            background-color: var(--secondary);
            border-left: 4px solid #059669;
        }

        .notification.error {
            background-color: var(--danger);
            border-left: 4px solid #dc2626;
        }

        /* Improved Dropdown styles */
        .dropdown {
            position: relative;
            display: inline-block;
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

        .dropdown.active .dropdown-menu {
            display: block;
        }

        /* Improved Filter Section */
        .filters-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        @media (min-width: 1024px) {
            .filters-grid {
                grid-template-columns: 1fr 2fr;
            }
        }

        .filter-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }

        .filter-item {
            display: flex;
            flex-direction: column;
        }

        .filter-item label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #374151;
            font-size: 0.875rem;
        }

        .date-range-group {
            display: grid;
            grid-template-columns: 1fr 1fr auto auto;
            gap: 0.5rem;
            align-items: end;
        }

        /* Print styles */
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
            #print-area .btn,
            #print-area select,
            #print-area input,
            #print-area .search-box,
            #print-area .no-print {
                display: none !important;
            }

            #print-area table.data-table {
                width: 100% !important;
                border-collapse: collapse;
                font-weight: bold;
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

            #print-area .pdf-footer {
                display: block !important;
            }

            .no-print {
                display: none !important;
            }

            .pdf-header {
                display: block !important;
            }
        }

        /* Scrollbar styling */
        .table-container::-webkit-scrollbar {
            height: 8px;
        }

        .table-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .table-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .table-container::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Column Selection Modal */
        .columns-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .columns-modal.active {
            display: flex;
        }

        .columns-modal-content {
            background: white;
            border-radius: 12px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            overflow: hidden;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .columns-modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: between;
            align-items: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .columns-modal-title {
            font-size: 1.25rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .columns-modal-close {
            background: none;
            border: none;
            color: white;
            font-size: 1.25rem;
            cursor: pointer;
            padding: 0.25rem;
        }

        .columns-modal-body {
            padding: 1.5rem;
            max-height: 400px;
            overflow-y: auto;
        }

        .columns-search {
            position: relative;
            margin-bottom: 1rem;
        }

        .columns-search input {
            width: 100%;
            padding: 0.75rem 2.5rem 0.75rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.875rem;
        }

        .columns-search i {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6b7280;
        }

        .columns-list {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }

        .column-item {
            display: flex;
            align-items: center;
            padding: 0.5rem;
            border-radius: 6px;
            transition: background-color 0.2s;
        }

        .column-item:hover {
            background-color: #f3f4f6;
        }

        .column-checkbox {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            cursor: pointer;
            width: 100%;
            font-weight: 500;
        }

        .column-checkbox input[type="checkbox"] {
            width: 1.125rem;
            height: 1.125rem;
            border-radius: 4px;
            border: 2px solid #d1d5db;
            cursor: pointer;
        }

        .column-checkbox input[type="checkbox"]:checked {
            background-color: #4f46e5;
            border-color: #4f46e5;
        }

        .columns-modal-footer {
            padding: 1.5rem;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        .columns-actions {
            display: flex;
            gap: 1rem;
        }

        .btn-select-all {
            background: none;
            border: none;
            color: #4f46e5;
            font-weight: 600;
            cursor: pointer;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            transition: background-color 0.2s;
        }

        .btn-select-all:hover {
            background-color: #eef2ff;
        }

        .btn-cancel {
            background: none;
            border: 1px solid #d1d5db;
            color: #374151;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-cancel:hover {
            background-color: #f9fafb;
        }

        .btn-apply {
            background: #4f46e5;
            border: none;
            color: white;
            padding: 0.5rem 1.5rem;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: background-color 0.2s;
        }

        .btn-apply:hover {
            background: #4338ca;
        }

        /* Action buttons styling */
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            align-items: center;
        }
        #clientCounter {
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.1);
        }

        #clientCounter:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(59, 130, 246, 0.15);
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.875rem;
            text-decoration: none;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: #4f46e5;
            color: white;
        }

        .btn-primary:hover {
            background: #4338ca;
        }

        .btn-success {
            background: #10b981;
            color: white;
        }

        .btn-success:hover {
            background: #059669;
        }

        .btn-warning {
            background: #f59e0b;
            color: white;
        }

        .btn-warning:hover {
            background: #d97706;
        }

        .btn-purple {
            background: #8b5cf6;
            color: white;
        }

        .btn-purple:hover {
            background: #7c3aed;
        }

        .btn-dark {
            background: #374151;
            color: white;
        }

        .btn-dark:hover {
            background: #1f2937;
        }

        .btn-gray {
            background: #6b7280;
            color: white;
        }

        .btn-gray:hover {
            background: #4b5563;
        }
    </style>
@endpush

@section('favicon')
    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.jpg') }}">
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.jpg') }}">
@endsection

@section('content')
    <div class="min-h-screen bg-gray-50 py-6">
        <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">العملاء</h1>
                            <p class="text-gray-600 mt-1">إدارة وعرض جميع عملاء الشركة</p>
                        </div>

                        <!-- Client Counter Badge -->
                        <div id="clientCounter" class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-2">
                            <div class="flex items-center gap-2">
                                <div class="bg-blue-100 p-1 rounded-full">
                                    <i class="fas fa-users text-blue-600 text-sm"></i>
                                </div>
                                <div>
                                    <div class="text-sm font-medium text-blue-900">العملاء المعروضين</div>
                                    <div class="text-lg font-bold text-blue-700">
                                        <span id="displayedCount">0</span>
                                        <span class="text-sm font-normal">/</span>
                                        <span id="totalCount" class="text-sm font-normal">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        @if(Auth::user()->role == 'salesRep')
                            <a class="btn btn-primary" href="{{ route('sales-reps.clients.create', Auth::user()->salesRep->id) }}">
                                <i class="fas fa-plus"></i>
                                إضافة عميل
                            </a>
                        @endif

                        @if(Auth::user()->role == 'admin')
                            <a class="btn btn-success" href="{{ route('admin.shared-companies') }}">
                                <i class="fas fa-users"></i>
                                العملاء المشتركين
                            </a>

                            <!-- Late Days Settings Button -->
                            <div class="late-days-btn-group" x-data="{ open: false }">
                                <button @click="open = true" class="btn btn-warning">
                                    <i class="fas fa-cog"></i>
                                    أيام التأخير
                                </button>

                                <!-- Modal -->
                                <div x-show="open" x-cloak class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50 p-4">
                                    <div @click.away="open = false" class="bg-white rounded-lg shadow-xl w-full max-w-md">
                                        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                                            <h3 class="text-lg font-bold text-gray-900">تعديل عدد أيام التأخير</h3>
                                            <button @click="open = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                                                <i class="fas fa-times text-lg"></i>
                                            </button>
                                        </div>
                                        <div class="px-6 py-4">
                                            <form action="{{ route('settings.update') }}" method="POST">
                                                @csrf
                                                <div class="mb-4">
                                                    <label for="late_customer_days" class="block text-sm font-medium text-gray-700 mb-2">
                                                        عدد الأيام اللازمة لاعتبار العميل متأخراً
                                                    </label>
                                                    <input type="number"
                                                           name="late_customer_days"
                                                           id="late_customer_days"
                                                           min="1"
                                                           max="30"
                                                           required
                                                           value="{{ old('late_customer_days', \App\Models\Setting::where('key', 'late_customer_days')->value('value') ?? 3) }}"
                                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                                </div>
                                                <div class="flex justify-end gap-3">
                                                    <button type="button"
                                                            @click="open = false"
                                                            class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition-colors duration-200">
                                                        إلغاء
                                                    </button>
                                                    <button type="submit"
                                                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200">
                                                        حفظ الإعدادات
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <!-- Columns Selection -->
                        <button id="columnsBtn" class="btn btn-purple" onclick="openColumnsModal()">
                            <i class="fas fa-columns"></i>
                            اختيار الأعمدة
                            <span id="columnsBadge" class="bg-white text-purple-600 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-2">13</span>
                        </button>

                        <!-- Export Dropdown -->
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

                        <!-- Print Button -->
                        <button class="btn btn-gray no-print" onclick="window.print()">
                            <i class="fas fa-print"></i>
                            طباعة
                        </button>
                    </div>
                </div>
            </div>

            <!-- Improved Filters Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                <div class="p-6">
                    <div class="filters-grid">
                        <!-- Search Box -->
                        <div class="filter-item">
                            <label for="searchInput" class="block text-sm font-medium text-gray-700 mb-2">بحث سريع</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text"
                                       class="block w-full pr-10 pl-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                       placeholder="ابحث في العملاء..."
                                       id="searchInput"
                                       name="search"
                                       value="{{ request('search') }}"
                                       oninput="applyLiveFilters()">
                            </div>
                        </div>

                        <!-- Main Filters -->
                        <div class="filter-group">
                            <div class="filter-item">
                                <label for="filterSelect" class="block text-sm font-medium text-gray-700 mb-2">حالة الاهتمام</label>
                                <select id="filterSelect" name="interest_status"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                        onchange="applyLiveFilters()">
                                    <option value="">الكل</option>
                                    <option value="interested" {{ request('interest_status') == 'interested' ? 'selected' : '' }}>مهتم</option>
                                    <option value="not interested" {{ request('interest_status') == 'not interested' ? 'selected' : '' }}>غير مهتم</option>
                                    <option value="neutral" {{ request('interest_status') == 'neutral' ? 'selected' : '' }}>مؤجل</option>
                                    <option value="late" {{ request('interest_status') == 'late' ? 'selected' : '' }}>متأخرين</option>
                                    <option value="late_interested" {{ request('interest_status') == 'late_interested' ? 'selected' : '' }}>متأخرين مهتمين</option>
                                    <option value="late_not_interested" {{ request('interest_status') == 'late_not_interested' ? 'selected' : '' }}>متأخرين غير مهتمين</option>
                                    <option value="late_neutral" {{ request('interest_status') == 'late_neutral' ? 'selected' : '' }}>متأخرين مؤجلين</option>
                                </select>
                            </div>

                            <div class="filter-item">
                                <label for="serviceTypeFilter" class="block text-sm font-medium text-gray-700 mb-2">نوع الخدمة</label>
                                <select id="serviceTypeFilter" name="service"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                        onchange="applyLiveFilters()">
                                    <option value="">الكل</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}" {{ request('service') == $service->id ? 'selected' : '' }}>{{ $service->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="filter-item">
                                <label for="salesRepFilter" class="block text-sm font-medium text-gray-700 mb-2">مندوب المبيعات</label>
                                <select id="salesRepFilter" name="sales_rep"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                        onchange="applyLiveFilters()">
                                    <option value="">كل المندوبين</option>
                                    @foreach($sales_rep_names as $name)
                                        <option value="{{ $name }}" {{ request('sales_rep') == $name ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="filter-item">
                                <label for="lateDaysInput" class="block text-sm font-medium text-gray-700 mb-2">أيام التأخير</label>
                                <input type="number"
                                       id="lateDaysInput"
                                       name="late_days"
                                       value="{{ request('late_days', \App\Models\Setting::where('key', 'late_customer_days')->value('value') ?? 3) }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                       placeholder="أيام التأخر"
                                       min="1"
                                       oninput="applyLiveFilters()">
                            </div>
                        </div>
                    </div>

                    <!-- Date Filters -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                        <div class="filter-item">
                            <label class="block text-sm font-medium text-gray-700 mb-2">تاريخ الإنشاء</label>
                            <div class="flex gap-2">
                                <input type="text"
                                       id="createdAtFilter"
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                       placeholder="تاريخ الإنشاء"
                                       name="created_date"
                                       value="{{ request('created_date') }}"
                                       onchange="applyLiveFilters()">
                                <button type="button" onclick="resetDate('created_date')" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors duration-200">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div id="createdAtCountBadge" class="mt-2 text-sm text-blue-600 font-semibold hidden">
                                عدد العملاء: <span id="createdAtCount">0</span>
                            </div>
                        </div>

                        <div class="filter-item">
                            <label class="block text-sm font-medium text-gray-700 mb-2">نطاق تاريخ التواصل</label>
                            <div class="date-range-group">
                                <input type="text"
                                       id="fromDate"
                                       class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                       placeholder="من تاريخ"
                                       name="from_date"
                                       value="{{ request('from_date') }}"
                                       onchange="applyLiveFilters()">
                                <input type="text"
                                       id="toDate"
                                       class="px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                       placeholder="إلى تاريخ"
                                       name="to_date"
                                       value="{{ request('to_date') }}"
                                       onchange="applyLiveFilters()">
                                <button type="button" onclick="resetDateRange()" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors duration-200">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-gray-200">
                        <button type="button" onclick="resetFilters()" class="btn btn-gray">
                            <i class="fas fa-redo"></i>
                            إعادة تعيين
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table Section -->
            <div class="table-responsive">
                <div class="pdf-content">
                    <div class="pdf-header" style="display: none;">
                        <div
                            class="header-content d-flex align-items-center justify-content-between flex-wrap mb-4 p-3 shadow rounded bg-white">
                            <div class="d-flex flex-column align-items-center text-center mx-auto">
                                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="header-logo mb-2" />
                                <h2 class="header-text">تقرير العملاء</h2>
                            </div>
                        </div>
                    </div>
                    <table class="data-table">
                        <thead>
                        <tr>
                            <th>شعار شركة العميل</th>
                            <th>الشركة</th>
                            <th>مقر الشركة</th>
                            <th>الشخص المسؤول</th>
                            <th>المنصب الوظيفي</th>
                            <th>رقم الجوال</th>
                            <th class="no-print">واتس اب مباشر</th>
                            <th>حالة الاهتمام</th>
                            <th>الخدمة المهتم بها</th>
                            <th>عدد الخدمة</th>
                            <th>آخر تواصل</th>
                            <th>آخر تواصل منذ</th>
                            <th>عدد مرات التواصل</th>
                            <th>طلبات العميل</th>
                            <th class="no-print">الدردشة</th>
                        </tr>
                        </thead>
                        <tbody id="tableBody" class="bg-white divide-y divide-gray-200 font-bold">
                        <!-- Data will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- PDF Footer -->
                <div class="pdf-footer hidden border-t-2 border-gray-800 mt-8 py-4 text-center">
                    <p class="text-gray-600">جميع الحقوق محفوظة &copy; شركة آفاق الخليج {{ date('Y') }}</p>
                </div>
            </div>

            <!-- Hidden Input -->
            <input type="hidden" id="current_sales_rep_id" value="{{ $Clients[0]['sales_rep_id'] ?? '' }}">
        </div>
    </div>

    <!-- Fixed Columns Selection Modal -->
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
                    <input type="text" id="columnsSearch" placeholder="بحث عن عمود..." onkeyup="filterColumns()">
                    <i class="fas fa-search"></i>
                </div>
                <div class="columns-list" id="columnsList">
                    <!-- Column items will be generated here -->
                    <div class="column-item">
                        <label class="column-checkbox">
                            <input type="checkbox" value="client_logo" checked>
                            <span class="column-name">شعار شركة العميل</span>
                        </label>
                    </div>
                    <div class="column-item">
                        <label class="column-checkbox">
                            <input type="checkbox" value="company_name" checked>
                            <span class="column-name">الشركة</span>
                        </label>
                    </div>
                    <div class="column-item">
                        <label class="column-checkbox">
                            <input type="checkbox" value="address" checked>
                            <span class="column-name">مقر الشركة</span>
                        </label>
                    </div>
                    <div class="column-item">
                        <label class="column-checkbox">
                            <input type="checkbox" value="contact_person" checked>
                            <span class="column-name">الشخص المسؤول</span>
                        </label>
                    </div>
                    <div class="column-item">
                        <label class="column-checkbox">
                            <input type="checkbox" value="contact_position" checked>
                            <span class="column-name">المنصب الوظيفي</span>
                        </label>
                    </div>
                    <div class="column-item">
                        <label class="column-checkbox">
                            <input type="checkbox" value="phone" checked>
                            <span class="column-name">رقم الجوال</span>
                        </label>
                    </div>
                    <div class="column-item">
                        <label class="column-checkbox">
                            <input type="checkbox" value="whatsapp_link" checked>
                            <span class="column-name">رابط واتس اب مباشر</span>
                        </label>
                    </div>
                    <div class="column-item">
                        <label class="column-checkbox">
                            <input type="checkbox" value="interest_status" checked>
                            <span class="column-name">حالة الاهتمام</span>
                        </label>
                    </div>
                    <div class="column-item">
                        <label class="column-checkbox">
                            <input type="checkbox" value="interested_service" checked>
                            <span class="column-name">الخدمة المهتم بها</span>
                        </label>
                    </div>
                    <div class="column-item">
                        <label class="column-checkbox">
                            <input type="checkbox" value="last_contact_date" checked>
                            <span class="column-name">تاریخ آخر تواصل</span>
                        </label>
                    </div>
                    <div class="column-item">
                        <label class="column-checkbox">
                            <input type="checkbox" value="contact_days_left" checked>
                            <span class="column-name">آخر تواصل منذ</span>
                        </label>
                    </div>
                    <div class="column-item">
                        <label class="column-checkbox">
                            <input type="checkbox" value="contact_count" checked>
                            <span class="column-name">عدد مرات التواصل</span>
                        </label>
                    </div>
                    <div class="column-item">
                        <label class="column-checkbox">
                            <input type="checkbox" value="requests_count" checked>
                            <span class="column-name">طلبات العميل</span>
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

    <!-- Edit Modal -->
    <div id="clientEditModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-lg w-full max-w-5xl mx-4 max-h-[90vh] overflow-hidden">
            <!-- Modal Header -->
            <div class="modal-header px-6 py-4 border-b bg-gray-50 flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800" id="modalClientName">تعديل بيانات العميل</h3>
                <button class="text-gray-500 hover:text-gray-700 text-xl" onclick="closeClientEditModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="modal-body px-6 py-4 overflow-y-auto" style="max-height: calc(90vh - 200px)">
                <form id="clientEditForm" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <input type="hidden" id="editClientId" name="client_id">
                    <div class="col-span-2 flex justify-center mb-4">
                        <img id="editClientLogo" src="" alt="Client Logo" class="max-h-32 max-w-full object-contain border rounded-lg p-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">مندوب المبيعات</label>
                        <input type="text" id="editSalesRep" name="sales_rep_name" readonly
                               class="w-full px-3 py-2 border rounded-md bg-gray-100">
                    </div>
                    <!-- Company Information -->
                    <div class="col-span-2">
                        <h4 class="text-md font-medium text-gray-700 mb-3 border-b pb-2">معلومات الشركة</h4>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">اسم الشركة</label>
                        <input type="text" id="editCompanyName" name="company_name"
                               class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">عنوان الشركة</label>
                        <input type="text" id="editAddress" name="address"
                               class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500">
                    </div>


                    <!-- Contact Information -->
                    <div class="col-span-2 mt-4">
                        <h4 class="text-md font-medium text-gray-700 mb-3 border-b pb-2">معلومات الاتصال</h4>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">الشخص المسؤول</label>
                        <input type="text" id="editContactPerson" name="contact_person"
                               class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">المنصب الوظيفي</label>
                        <input type="text" id="editContactPosition" name="contact_position"
                               class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">رقم الجوال</label>
                        <input type="tel" id="editPhone" name="phone" dir="ltr"
                               class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500">
                    </div>

                    {{--                            <div>--}}
                    {{--                                <label class="block text-sm font-medium text-gray-700 mb-1">رابط الواتساب</label>--}}
                    {{--                                <input type="url" id="editWhatsappLink" name="whatsapp_link"--}}
                    {{--                                       class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500">--}}
                    {{--                            </div>--}}

                    <!-- Status Information -->
                    <div class="col-span-2 mt-4">
                        <h4 class="text-md font-medium text-gray-700 mb-3 border-b pb-2">حالة العميل</h4>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">حالة الاهتمام</label>
                        <select id="editInterestStatus" name="interest_status"
                                class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500">
                            <option value="interested">مهتم</option>
                            <option value="not interested">غير مهتم</option>
                            <option value="neutral">مؤجل</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">الخدمة المهتم بها</label>
                        <select id="editInterestedService" name="interested_service"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                            <option value="">اختر الخدمة</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}">{{ $service->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">عدد مرات التواصل</label>
                        <input type="number" id="editContactCount" name="contact_count"
                               class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">عدد الخدمة المهتم بها</label>
                        <input type="number" id="editServiceCount" name="interested_service_count"
                               class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Additional Information -->
                    <div class="col-span-2 mt-4">
                        <h4 class="text-md font-medium text-gray-700 mb-3 border-b pb-2">معلومات إضافية</h4>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">تاريخ آخر تواصل</label>
                        <input type="date" id="editLastContactDate" name="last_contact_date"
                               class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500">
                    </div>




                </form>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer px-6 py-4 border-t bg-gray-50 flex justify-end gap-3">
                <button type="button" onclick="closeClientEditModal()"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                    إلغاء
                </button>
                <button type="button" onclick="saveClientEdits()"
                        class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                    حفظ التغييرات
                </button>
            </div>
        </div>
    </div>
@endsection

        @push('scripts')
            <script>
                // Live filtering function
                function applyLiveFilters() {
                    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
                    const interestStatus = document.getElementById('filterSelect').value;
                    const serviceType = document.getElementById('serviceTypeFilter').value;
                    const salesRep = document.getElementById('salesRepFilter').value;
                    const lateDays = document.getElementById('lateDaysInput').value;
                    const createdDate = document.getElementById('createdAtFilter').value;
                    const fromDate = document.getElementById('fromDate').value;
                    const toDate = document.getElementById('toDate').value;

                    let filteredData = [...ClientsData];

                    // Apply search filter
                    if (searchTerm) {
                        filteredData = filteredData.filter(client => {
                            return (
                                (client.company_name && client.company_name.toLowerCase().includes(searchTerm)) ||
                                (client.address && client.address.toLowerCase().includes(searchTerm)) ||
                                (client.contact_person && client.contact_person.toLowerCase().includes(searchTerm)) ||
                                (client.phone && client.phone.toLowerCase().includes(searchTerm)) ||
                                (client.interest_status && client.interest_status.toLowerCase().includes(searchTerm))
                            );
                        });
                    }

                    // Apply interest status filter
                    if (interestStatus) {
                        const lateDaysThreshold = parseInt(lateDays) || 3;

                        switch (interestStatus.toLowerCase()) {
                            case 'neutral':
                                filteredData = filteredData.filter(client =>
                                    client.interest_status && client.interest_status.toLowerCase() === 'neutral'
                                );
                                break;

                            case 'interested':
                                filteredData = filteredData.filter(client =>
                                    client.interest_status &&
                                    (client.interest_status.toLowerCase() === 'interested' ||
                                        client.interest_status.toLowerCase() === 'intersted')
                                );
                                break;

                            case 'not interested':
                                filteredData = filteredData.filter(client =>
                                    client.interest_status &&
                                    (client.interest_status.toLowerCase() === 'not interested' ||
                                        client.interest_status.toLowerCase() === 'not_interested' ||
                                        client.interest_status.toLowerCase() === 'notinterested')
                                );
                                break;

                            case 'late':
                                filteredData = filteredData.filter(client =>
                                    client.is_late_customer === true
                                );
                                break;

                            case 'late_interested':
                                filteredData = filteredData.filter(client =>
                                    client.is_late_customer === true &&
                                    client.interest_status &&
                                    (client.interest_status.toLowerCase() === 'interested' ||
                                        client.interest_status.toLowerCase() === 'intersted')
                                );
                                break;

                            case 'late_not_interested':
                                filteredData = filteredData.filter(client =>
                                    client.is_late_customer === true &&
                                    client.interest_status &&
                                    (client.interest_status.toLowerCase() === 'not interested' ||
                                        client.interest_status.toLowerCase() === 'not_interested' ||
                                        client.interest_status.toLowerCase() === 'notinterested')
                                );
                                break;

                            case 'late_neutral':
                                filteredData = filteredData.filter(client =>
                                    client.is_late_customer === true &&
                                    client.interest_status &&
                                    client.interest_status.toLowerCase() === 'neutral'
                                );
                                break;
                        }
                    }

                    // Apply service type filter
                    if (serviceType) {
                        const serviceTypeMap = {
                            @foreach($services as $service)
                            '{{ $service->id }}': '{{ $service->name }}',
                            @endforeach
                        };

                        const serviceName = serviceTypeMap[serviceType];
                        if (serviceName) {
                            filteredData = filteredData.filter(client => {
                                if (!client.interested_service) return false;
                                const clientService = client.interested_service.trim();
                                const targetService = serviceName.trim();
                                return clientService.localeCompare(targetService, undefined, {
                                    sensitivity: 'base',
                                    ignorePunctuation: true
                                }) === 0;
                            });
                        }
                    }

                    // Apply sales rep filter
                    if (salesRep) {
                        filteredData = filteredData.filter(client =>
                            client.sales_rep_name === salesRep
                        );
                    }

                    // Apply created date filter
                    if (createdDate) {
                        filteredData = filteredData.filter(client => {
                            if (!client.client_created_at) return false;
                            const clientDate = new Date(client.client_created_at).toISOString().split('T')[0];
                            return clientDate === createdDate;
                        });
                    }

                    // Apply date range filter
                    if (fromDate || toDate) {
                        filteredData = filteredData.filter(client => {
                            if (!client.last_contact_date) return false;
                            const contactDate = client.last_contact_date;

                            if (fromDate && toDate) {
                                return contactDate >= fromDate && contactDate <= toDate;
                            } else if (fromDate) {
                                return contactDate >= fromDate;
                            } else if (toDate) {
                                return contactDate <= toDate;
                            }
                            return true;
                        });
                    }

                    currentFilteredClients = filteredData;
                    renderTable(currentFilteredClients);
                    updateClientCounter();

                    // Update URL without reloading page (optional)
                    updateURLParams();
                }

                function updateClientCounter() {
                    const displayedCount = currentFilteredClients.length;
                    const totalCount = ClientsData.length;

                    document.getElementById('displayedCount').textContent = displayedCount;
                    document.getElementById('totalCount').textContent = totalCount;

                    // Optional: Add color change based on filtered results
                    const counter = document.getElementById('clientCounter');
                    if (displayedCount === totalCount) {
                        counter.classList.remove('bg-blue-50', 'border-blue-200');
                        counter.classList.add('bg-green-50', 'border-green-200');
                    } else {
                        counter.classList.remove('bg-green-50', 'border-green-200');
                        counter.classList.add('bg-blue-50', 'border-blue-200');
                    }
                }
                // Update URL parameters without page reload
                function updateURLParams() {
                    const params = new URLSearchParams();

                    const search = document.getElementById('searchInput').value;
                    const interestStatus = document.getElementById('filterSelect').value;
                    const serviceType = document.getElementById('serviceTypeFilter').value;
                    const salesRep = document.getElementById('salesRepFilter').value;
                    const lateDays = document.getElementById('lateDaysInput').value;
                    const createdDate = document.getElementById('createdAtFilter').value;
                    const fromDate = document.getElementById('fromDate').value;
                    const toDate = document.getElementById('toDate').value;

                    if (search) params.set('search', search);
                    if (interestStatus) params.set('interest_status', interestStatus);
                    if (serviceType) params.set('service', serviceType);
                    if (salesRep) params.set('sales_rep', salesRep);
                    if (lateDays) params.set('late_days', lateDays);
                    if (createdDate) params.set('created_date', createdDate);
                    if (fromDate) params.set('from_date', fromDate);
                    if (toDate) params.set('to_date', toDate);

                    const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                    window.history.replaceState({}, '', newUrl);
                }

                // Reset functions
                function resetFilters() {
                    document.getElementById('searchInput').value = '';
                    document.getElementById('filterSelect').value = '';
                    document.getElementById('serviceTypeFilter').value = '';
                    document.getElementById('salesRepFilter').value = '';
                    document.getElementById('lateDaysInput').value = '{{ \App\Models\Setting::where('key', 'late_customer_days')->value('value') ?? 3 }}';
                    document.getElementById('createdAtFilter').value = '';
                    document.getElementById('fromDate').value = '';
                    document.getElementById('toDate').value = '';

                    hideClientCountBadge();
                    currentFilteredClients = [...ClientsData];
                    renderTable(currentFilteredClients);
                    updateClientCounter();
                    window.history.replaceState({}, '', window.location.pathname);
                }

                function resetDate(field) {
                    if (field === 'created_date') {
                        document.getElementById('createdAtFilter').value = '';
                        hideClientCountBadge();
                    }
                    applyLiveFilters();
                }
                function resetDateRange() {
                    document.getElementById('fromDate').value = '';
                    document.getElementById('toDate').value = '';
                    applyLiveFilters();
                }

                // Initialize date pickers with change event
                flatpickr("#createdAtFilter", {
                    locale: "ar",
                    dateFormat: "Y-m-d",
                    allowInput: true,
                    onChange: function(selectedDates, dateStr) {
                        updateClientCountBadge(dateStr);
                        applyLiveFilters();
                    },
                    onOpen: function(selectedDates, dateStr) {
                        // Show count for current selected date when opening picker
                        if (dateStr) {
                            updateClientCountBadge(dateStr);
                        }
                    },
                    onDayCreate: function(dObj, dStr, fp, dayElem) {
                        // Add hover event to each day
                        dayElem.addEventListener('mouseenter', function() {
                            const dateStr = dayElem.dateObj.toISOString().split('T')[0];
                            updateClientCountBadge(dateStr);
                        });

                        dayElem.addEventListener('mouseleave', function() {
                            // When mouse leaves, show count for currently selected date (not hovered date)
                            const selectedDate = fp.selectedDates[0];
                            if (selectedDate) {
                                const selectedDateStr = selectedDate.toISOString().split('T')[0];
                                updateClientCountBadge(selectedDateStr);
                            } else {
                                hideClientCountBadge();
                            }
                        });
                    }
                });

                    flatpickr("#fromDate", {
                        locale: "ar",
                        dateFormat: "Y-m-d",
                        allowInput: true,
                        onChange: function(selectedDates, dateStr) {
                            applyLiveFilters();
                        }
                    });

                    flatpickr("#toDate", {
                        locale: "ar",
                        dateFormat: "Y-m-d",
                        allowInput: true,
                        onChange: function(selectedDates, dateStr) {
                            applyLiveFilters();
                        }
                    });
                });
                // flatpickr("#createdAtFilter", {
                //     locale: "ar",
                //     dateFormat: "Y-m-d",
                //     allowInput: true
                // });
                //
                // flatpickr("#fromDate", {
                //     locale: "ar",
                //     dateFormat: "Y-m-d",
                //     allowInput: true
                // });
                //
                // flatpickr("#toDate", {
                //     locale: "ar",
                //     dateFormat: "Y-m-d",
                //     allowInput: true
                // });

                // Dropdown functionality
                // Fixed Export Dropdown functionality
                document.addEventListener('DOMContentLoaded', function() {
                    const exportBtn = document.getElementById('exportBtn');
                    const exportDropdown = document.getElementById('exportDropdown');

                    if (exportBtn && exportDropdown) {
                        exportBtn.addEventListener('click', function(e) {
                            e.stopPropagation();
                            exportDropdown.classList.toggle('active');
                        });

                        // Close dropdown when clicking outside
                        document.addEventListener('click', function(e) {
                            if (!exportDropdown.contains(e.target)) {
                                exportDropdown.classList.remove('active');
                            }
                        });
                    }
                });

                // Simple export function
                function exportTable(type) {
                    const selectedColumns = getSelectedColumns();

                    if (type === 'xlsx') {
                        exportClients(selectedColumns);
                    } else if (type === 'pdf') {
                        exportToPDF(selectedColumns);
                    }

                    // Close dropdown after selection
                    const exportDropdown = document.getElementById('exportDropdown');
                    if (exportDropdown) {
                        exportDropdown.classList.remove('active');
                    }
                }

                // Columns Modal Functions
                function openColumnsModal() {
                    const modal = document.getElementById('columnsModal');
                    modal.classList.add('active');
                }

                function closeColumnsModal() {
                    const modal = document.getElementById('columnsModal');
                    modal.classList.remove('active');
                }

                function filterColumns() {
                    const searchTerm = document.getElementById('columnsSearch').value.toLowerCase();
                    const columnItems = document.querySelectorAll('.column-item');

                    columnItems.forEach(item => {
                        const columnName = item.querySelector('.column-name').textContent.toLowerCase();
                        if (columnName.includes(searchTerm)) {
                            item.style.display = 'flex';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                }

                function toggleSelectAll() {
                    const checkboxes = document.querySelectorAll('#columnsList input[type="checkbox"]');
                    const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);

                    checkboxes.forEach(checkbox => {
                        checkbox.checked = !allChecked;
                    });
                }

                function resetSelection() {
                    const checkboxes = document.querySelectorAll('#columnsList input[type="checkbox"]');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = true; // Reset to all selected
                    });
                }

                function applyColumnSelection() {
                    const checkboxes = document.querySelectorAll('#columnsList input[type="checkbox"]');
                    const selectedColumns = [];

                    checkboxes.forEach(checkbox => {
                        if (checkbox.checked) {
                            selectedColumns.push(checkbox.value);
                        }
                    });

                    // Update columns badge
                    document.getElementById('columnsBadge').textContent = selectedColumns.length;

                    // Hide/show columns in table
                    const tableHeaders = document.querySelectorAll('.data-table th');
                    const tableRows = document.querySelectorAll('.data-table tbody tr');

                    tableHeaders.forEach((header, index) => {
                        const columnName = getColumnNameFromHeader(header.textContent);
                        if (selectedColumns.includes(columnName)) {
                            header.style.display = '';
                            // Show corresponding cells in all rows
                            tableRows.forEach(row => {
                                const cells = row.querySelectorAll('td');
                                if (cells[index]) {
                                    cells[index].style.display = '';
                                }
                            });
                        } else {
                            header.style.display = 'none';
                            // Hide corresponding cells in all rows
                            tableRows.forEach(row => {
                                const cells = row.querySelectorAll('td');
                                if (cells[index]) {
                                    cells[index].style.display = 'none';
                                }
                            });
                        }
                    });

                    closeColumnsModal();
                }

                function getColumnNameFromHeader(headerText) {
                    const columnMap = {
                        'شعار شركة العميل': 'client_logo',
                        'الشركة': 'company_name',
                        'مقر الشركة': 'address',
                        'الشخص المسؤول': 'contact_person',
                        'المنصب الوظيفي': 'contact_position',
                        'رقم الجوال': 'phone',
                        'واتس اب مباشر': 'whatsapp_link',
                        'حالة الاهتمام': 'interest_status',
                        'الخدمة المهتم بها': 'interested_service',
                        'عدد الخدمة': 'service_count',
                        'آخر تواصل': 'last_contact_date',
                        'آخر تواصل منذ': 'contact_days_left',
                        'عدد مرات التواصل': 'contact_count',
                        'طلبات العميل': 'requests_count',
                        'الدردشة': 'chat'
                    };

                    return columnMap[headerText.trim()] || '';
                }
            </script>
            <script>
                const isAdmin = @json($isAdmin);

                let ClientsData = [];
                let currentFilteredClients = [];

                document.addEventListener('DOMContentLoaded', function() {
                    // Initialize data
                    ClientsData = @json($Clients);
                    currentFilteredClients = [...ClientsData];

                    console.log("Clients Data:", ClientsData);

                    // Render initial table
                    renderTable();
                    updateClientCounter();
                    // Setup event listeners
                    document.getElementById('searchInput').addEventListener('input', function(e) {
                        const searchTerm = e.target.value.toLowerCase();
                        const filteredData = currentFilteredClients.filter(client => {
                            return (
                                (client.company_name && client.company_name.toLowerCase().includes(searchTerm)) ||
                                (client.address && client.address.toLowerCase().includes(searchTerm)) ||
                                (client.phone && client.phone.toLowerCase().includes(searchTerm)) ||
                                (client.interest_status && client.interest_status.toLowerCase().includes(searchTerm)) ||
                                (client.response_status && client.response_status.toLowerCase().includes(searchTerm))
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

                    document.querySelectorAll('.dropdown-item').forEach(item => {
                        item.addEventListener('click', function() {
                            const exportType = this.getAttribute('data-type');
                            const selectedColumns = getSelectedColumns();

                            if (exportType === 'csv') {
                                exportClients('csv', selectedColumns);
                            } else if (exportType === 'pdf') {
                                exportToPDF(selectedColumns);
                            }else{
                                exportClients();
                            }

                            // Close dropdown after selection
                            this.closest('.dropdown').classList.remove('active');
                        });
                    });

                    // Prevent dropdown from closing when clicking inside it
                    dropdown.addEventListener('click', function(e) {
                        e.stopPropagation();
                    });
                });


                // Initialize on page load (in case date is pre-filled)
                window.addEventListener('DOMContentLoaded', () => {
                    toggleFakePlaceholder(document.getElementById('dateFilter'));
                });

                function renderTable(data = currentFilteredClients) {
                    const tbody = document.getElementById('tableBody');
                    if (!tbody) {
                        console.error("Table body element not found!");
                        return;
                    }
                    tbody.innerHTML = '';

                    // Debug: Check the value
                    console.log("isAdmin value:", isAdmin);

                    if (!data || data.length === 0) {
                        tbody.innerHTML = `
            <tr>
                <td colspan="14" class="empty-state">
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
            <!-- Company Logo -->
            <td class="px-4 py-2 text-center no-print">
                ${client.company_logo
                            ? `<div class="h-20 w-20 mx-auto border rounded-full p-3 bg-white flex items-center justify-center">
                         <img src="${client.company_logo}" alt="شعار" class="max-h-full max-w-full object-contain" />
                       </div>`
                            : '—'}
            </td>

            <!-- Company Name -->
<td class="px-4 py-2 text-sm" style="font-weight: 600; color: black;">
    <div class="flex flex-col items-center">
        <!-- Clickable company name -->
        <span class="cell-value clickable-cell text-center mb-1" style="font-weight: 600; color: black;"
              onclick="redirectToClient(${client.sales_rep_id}, ${client.client_id})">
            ${client.company_name || '—'}
        </span>

        <!-- Sales rep name -->
        <div class="text-xs" style="color: #6b7280;">
            ${client.sales_rep_name || '—'}
        </div>
    </div>
</td>

<!-- Address Cell -->
<td class="px-4 py-2 text-sm" style="color: #4b5563;">
    <div class="flex items-center justify-between">
        <span class="cell-value clickable-cell text-center" style="font-weight: 600;"
              onclick="redirectToClient(${client.sales_rep_id}, ${client.client_id})">
            ${client.address || '—'}
        </span>
    </div>
</td>

<!-- Contact Person -->
<td class="px-4 py-2 text-sm" style="color: #374151;">
    <div class="flex items-center justify-between">
        <span class="cell-value clickable-cell text-center" style="font-weight: 600;"
              onclick="redirectToClient(${client.sales_rep_id}, ${client.client_id})">
            ${client.contact_person || '—'}
        </span>
    </div>
</td>

<!-- Contact Position Cell -->
<td class="px-4 py-2 text-sm" style="color: #374151;">
    <div class="flex items-center justify-between">
        <span class="cell-value clickable-cell text-center" style="font-weight: 600;"
              onclick="redirectToClient(${client.sales_rep_id}, ${client.client_id})">
            ${client.contact_position || '—'}
        </span>
    </div>
</td>
<!-- Phone Cell -->
<td class="px-4 py-2 text-sm text-blue-700 font-bold">
    <div class="flex items-center justify-between">
        <span class="cell-value clickable-cell"
              onclick="redirectToClient(${client.sales_rep_id}, ${client.client_id})">
            <span dir="ltr" class="ltr-number">
                ${client.phone ? (client.phone.startsWith('+') ? client.phone : '+' + client.phone) : '—'}
            </span>
        </span>

    </div>
</td>
            <!-- WhatsApp Link -->
            <td class="px-4 py-2 text-sm text-center">
                <span class="cell-value">
                    ${client.whatsapp_link
                            ? `<a href="${client.whatsapp_link}" class="text-green-600 hover:underline" target="_blank">
                              <i class="fab fa-whatsapp"></i> تواصل
                           </a>`
                            : '—'
                        }
                </span>
            </td>

            <!-- Interest Status -->
          <td class="px-4 py-2 text-sm text-center">
    <div class="flex items-center justify-between">
        <span class="cell-value clickable-cell"
              onclick="redirectToClient(${client.sales_rep_id}, ${client.client_id})">
            <span class="inline-block px-2 py-0.5 rounded-full ${getStatusClass(client.interest_status)}">
                ${getStatusText(client.interest_status)}
            </span>
        </span>

    </div>
</td>

            <!-- Interested Service -->
           <td class="px-4 py-2 text-blue-700" dir="rtl" style="font-weight: 600;">
    ${client.interested_service ? client.interested_service : '—'}
</td>

<td class="px-4 py-2 text-sm font-medium text-gray-600" dir="rtl">
    ${client.interested_service_count > 0 ? `
        <span class="inline-flex items-center px-2 py-0.5 rounded bg-green-100 text-blue-800" style="font-weight: 600;">
            ${client.interested_service_count}
        </span>
    ` : '-'}
</td>




            <!-- Last Contact Date -->
            <td class="px-4 py-2 text-sm text-center ${ client.is_late_customer ? 'text-red-600' : 'text-green-600' }" style="font-weight: 600;">
                ${formatDateForDisplay(client.last_contact_date) || '—'}
            </td>

            <!-- Days Left -->
            <td class="px-4 py-2 text-sm text-center ${client.is_late_customer ? 'text-red-600' : 'text-green-600'}" style="font-weight: 600;">
                ${client.contact_days_left ?
                            `${client.contact_days_left} ${getArabicDaysWord(client.contact_days_left)}` :
                            '—'
                        }
            </td>

            <!-- Contact Count -->
            <td class="px-4 py-2 text-sm text-center">
                <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-100 text-blue-800" style="font-weight: 600;">
                    ${client.contact_count || 0}
                </span>
            </td>

            <!-- Requests Count -->
            <td class="px-4 py-2 text-sm text-center text-gray-400 cursor-pointer"
                onclick="window.location.href='/salesrep/' + ${client.sales_rep_id} + '/MyRequests'">
                <span class="inline-flex items-center px-2 py-0.5 rounded bg-purple-100 text-blue-800" style="font-weight: 600;">
                    ${client.requests_count || 0}
                </span>
            </td>

            <!-- Message Link -->
<td class="px-4 py-2 text-sm text-center no-print">
    <a href="/client/${client.client_id}/message" class="text-blue-600 hover:underline mr-2">
        <i class="fas fa-comments"></i>
    </a>
    ${isAdmin ? `
    <button onclick="openClientEditModal(${client.client_id})" class="text-green-600 hover:text-green-800" title="تعديل بيانات العميل">
        <i class="fas fa-edit"></i>
    </button>
    ` : ''}
</td>`;
                        tbody.appendChild(row);
                    });
                    updateClientCounter();
                }

                function addNewClient() {
                    const salesRepId = document.getElementById('current_sales_rep_id').value;

                    if (!salesRepId) {
                        alert("الرجاء تحديد مندوب مبيعات أولاً");
                        return;
                    }

                    window.location.href = "{{ route('sales-reps.clients.create', ['sales_rep' => ':id']) }}".replace(':id', salesRepId);
                }
                function getArabicDaysWord(number) {
                    if (number === 1) {
                        return 'يوم';
                    } else if (number === 2) {
                        return 'يومان';
                    } else if (number > 2 && number <= 10) {
                        return 'أيام';
                    } else {
                        return 'يوماً';
                    }
                }
                function getStatusText(status) {
                    const statusMap = {
                        'interested': 'مهتم',
                        'not interested': 'غير مهتم',
                        'neutral': 'مؤجل'
                    };
                    return statusMap[status] || 'مؤجل';
                }

                function getStatusClass(status) {
                    const classMap = {
                        'interested': 'bg-green-100 text-green-800',
                        'not interested': 'bg-red-100 text-red-800',
                        'neutral': 'bg-gray-100 text-gray-700'
                    };
                    return classMap[status] || 'bg-gray-100 text-gray-700';
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
                function exportTable(type) {
                    const selectedColumns = getSelectedColumns();

                    if (type === 'xlsx') {
                        exportClients(selectedColumns);
                    } else if (type === 'pdf') {
                        exportToPDF(selectedColumns);
                    }
                }

                // Simple edit modal opener
                function openEditModal(clientId) {
                    const client = ClientsData.find(c => c.client_id == clientId);
                    if (!client) {
                        showNotification('لم يتم العثور على بيانات العميل', 'error');
                        return;
                    }

                    // Populate the form
                    document.getElementById('editClientId').value = client.client_id;
                    document.getElementById('editCompanyName').value = client.company_name || '';
                    document.getElementById('editContactPerson').value = client.contact_person || '';
                    document.getElementById('editContactPosition').value = client.contact_position || '';
                    document.getElementById('editPhone').value = client.phone || '';
                    document.getElementById('editAddress').value = client.address || '';
                    document.getElementById('editInterestStatus').value = client.interest_status || 'neutral';
                    document.getElementById('editInterestedService').value = client.interested_service || '';

                    // Show modal
                    document.getElementById('editModal').classList.remove('hidden');
                }

                function closeEditModal() {
                    document.getElementById('editModal').classList.add('hidden');
                }

                function saveEdit() {
                    const clientId = document.getElementById('editClientId').value;
                    const formData = {
                        company_name: document.getElementById('editCompanyName').value,
                        contact_person: document.getElementById('editContactPerson').value,
                        contact_position: document.getElementById('editContactPosition').value,
                        phone: document.getElementById('editPhone').value,
                        address: document.getElementById('editAddress').value,
                        interest_status: document.getElementById('editInterestStatus').value,
                        interested_service: document.getElementById('editInterestedService').value
                    };

                    // Show loading state
                    const saveBtn = document.querySelector('#editModal button[onclick="saveEdit()"]');
                    const originalText = saveBtn.textContent;
                    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...';
                    saveBtn.disabled = true;

                    // Send AJAX request
                    fetch(`/api/clients/${clientId}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(formData)
                    })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                // Update local data
                                const clientIndex = ClientsData.findIndex(c => c.client_id == clientId);
                                if (clientIndex !== -1) {
                                    Object.assign(ClientsData[clientIndex], formData);
                                }

                                // Update filtered data
                                const filteredIndex = currentFilteredClients.findIndex(c => c.client_id == clientId);
                                if (filteredIndex !== -1) {
                                    Object.assign(currentFilteredClients[filteredIndex], formData);
                                }

                                // Re-render table
                                renderTable();

                                showNotification('تم تحديث بيانات العميل بنجاح', 'success');
                                closeEditModal();
                            } else {
                                throw new Error(result.message || 'فشل في حفظ التغييرات');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showNotification('حدث خطأ أثناء حفظ التغييرات', 'error');
                        })
                        .finally(() => {
                            saveBtn.textContent = originalText;
                            saveBtn.disabled = false;
                        });
                }

                // Close modal when clicking outside
                document.getElementById('editModal').addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeEditModal();
                    }
                });

                // Close modal with Escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && !document.getElementById('editModal').classList.contains('hidden')) {
                        closeEditModal();
                    }
                });
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
                    const columnVisibility = {};
                    selectedColumns.forEach(col => columnVisibility[col] = true);

                    // Get all table headers and their corresponding indexes
                    const headers = document.querySelectorAll('.data-table thead th');
                    const columnKeys = Array.from(headers).map(header => getColumnKey(header.textContent.trim()));

                    // Show/hide columns based on selection
                    columnKeys.forEach((key, index) => {
                        const shouldShow = columnVisibility[key] || index === 0 || index === headers.length - 1;
                        headers[index].style.display = shouldShow ? '' : 'none';

                        document.querySelectorAll('.data-table tbody tr').forEach(row => {
                            if (row.cells[index]) {
                                row.cells[index].style.display = shouldShow ? '' : 'none';
                            }
                        });
                    });
                }
                function redirectToClient(salesRepId, clientId){
                    window.location.href =`/sales-reps/${salesRepId}/clients/${clientId}`;
                }

                function getColumnKey(columnName) {
                    const columnMap = {
                        'شعار شركة العميل': 'client_logo',
                        'الشركة': 'company_name',
                        'مقر الشركة': 'address',
                        'الشخص المسؤول': 'contact_person',
                        'المنصب الوظيفي': 'contact_position',
                        'رقم الجوال': 'phone',
                        'الخدمة المهتم بها': 'interested_service',
                        'واتس اب مباشر': 'whatsapp_link',
                        'حالة الاهتمام': 'interest_status',
                        'آخر تواصل': 'last_contact_date',
                        'آخر تواصل منذ': 'contact_days_left',
                        'طلبات العميل': 'requests_count',
                        'عدد مرات التواصل': 'contact_count',
                    };

                    return columnMap[columnName] || columnName.toLowerCase().replace(/\s+/g, '_');
                }

                function updateColumnsBadge() {
                    const checkedCount = document.querySelectorAll('.column-checkbox input:checked').length;
                    document.getElementById('columnsBadge').textContent = checkedCount;
                }

                function getSelectedColumns() {
                    const checkboxes = document.querySelectorAll('.column-checkbox input:checked');
                    return Array.from(checkboxes).map(checkbox => checkbox.value);
                }

                function filterByDate() {
                    const fromDate = document.getElementById('fromDate').value;
                    const toDate = document.getElementById('toDate').value;

                    if (!fromDate || !toDate) {
                        alert('الرجاء تحديد تاريخ البداية والنهاية');
                        return;
                    }

                    const filteredClients = ClientsData.filter(client => {
                        const contactDate = client.last_contact_date;

                        // تأكد أن التاريخ موجود
                        if (!contactDate) return false;

                        // المقارنة بنطاق التاريخ
                        return contactDate >= fromDate && contactDate <= toDate;
                    });

                    if (filteredClients.length === 0) {
                        alert('لا توجد نتائج في الفترة المحددة');
                        return;
                    }

                    currentFilteredClients = filteredClients;
                    renderTable(currentFilteredClients);
                }
                function resetDateFilter() {
                    document.getElementById('fromDate').value = '';
                    document.getElementById('toDate').value = '';
                    currentFilteredClients = [...ClientsData];
                    renderTable(currentFilteredClients);
                }

                function applyServiceFilter() {
                    const serviceId = document.getElementById('serviceTypeFilter').value;

                    if (!serviceId || serviceId === "") {
                        currentFilteredClients = [...ClientsData];
                        renderTable(currentFilteredClients);
                        return;
                    }

                    const serviceTypeMap = {
                        @foreach($services as $service)
                        '{{ $service->id }}': '{{ $service->name }}',
                        @endforeach
                    };

                    const serviceName = serviceTypeMap[serviceId];

                    currentFilteredClients = ClientsData.filter(client => {
                        if (!client.service_type) return false;

                        const clientService = client.service_type.trim();
                        const targetService = serviceName.trim();

                        return clientService.localeCompare(targetService, undefined, {
                            sensitivity: 'base',
                            ignorePunctuation: true
                        }) === 0;
                    });

                    if (currentFilteredClients.length === 0) {
                        alert('⚠️ لا يوجد عملاء في هذه الخدمة');
                    }

                    renderTable(currentFilteredClients);
                }
                function applyFilter() {
                    const criteria = document.getElementById('filterSelect').value;
                    const serviceId = document.getElementById('serviceTypeFilter').value;
                    const salesRepName = document.getElementById('salesRepFilter').value;
                    const createdAtDate = document.getElementById('createdAtFilter').value;

                    // Generate service map
                    const serviceTypeMap = {
                        @foreach($services as $service)
                        '{{ $service->id }}': '{{ $service->name }}',
                        @endforeach
                    };

                    currentFilteredClients = [...ClientsData];

                    // Apply sales rep filter if selected
                    if (salesRepName && salesRepName !== "") {
                        currentFilteredClients = currentFilteredClients.filter(client =>
                            client.sales_rep_name === salesRepName
                        );
                    }

                    if (createdAtDate) {
                        currentFilteredClients = currentFilteredClients.filter(client => {
                            if (!client.client_created_at) return false;
                            const clientDate = new Date(client.client_created_at).toISOString().split('T')[0];
                            return clientDate === createdAtDate;
                        });
                    }

                    // Apply status criteria filter if selected
                    if (criteria && criteria !== "") {
                        switch (criteria.toLowerCase()) {
                            case 'neutral':
                                currentFilteredClients = currentFilteredClients.filter(client =>
                                    client.response_status &&
                                    client.response_status.toLowerCase() === 'neutral'
                                );
                                break;

                            case 'interested':
                                currentFilteredClients = currentFilteredClients.filter(client =>
                                    client.interest_status &&
                                    (client.interest_status.toLowerCase() === 'interested' ||
                                        client.interest_status.toLowerCase() === 'intersted')
                                );
                                break;

                            case 'not interested':
                                currentFilteredClients = currentFilteredClients.filter(client =>
                                    client.interest_status &&
                                    (client.interest_status.toLowerCase() === 'not interested' ||
                                        client.interest_status.toLowerCase() === 'not_interested' ||
                                        client.interest_status.toLowerCase() === 'notinterested')
                                );
                                break;
                            case 'late':
                                // Filter for all late customers
                                currentFilteredClients = currentFilteredClients.filter(client =>
                                    client.is_late_customer === true
                                );
                                break;

                            case 'late_interested':
                                // Filter for late AND interested customers
                                currentFilteredClients = currentFilteredClients.filter(client =>
                                    client.is_late_customer === true &&
                                    client.interest_status &&
                                    (client.interest_status.toLowerCase() === 'interested' ||
                                        client.interest_status.toLowerCase() === 'intersted')
                                );
                                break;

                            case 'late_not_interested':
                                currentFilteredClients = currentFilteredClients.filter(client =>
                                    client.is_late_customer === true &&
                                    client.interest_status &&
                                    (client.interest_status.toLowerCase() === 'not interested' ||
                                        client.interest_status.toLowerCase() === 'not_interested' ||
                                        client.interest_status.toLowerCase() === 'notinterested')
                                );
                                break;
                            case 'late_neutral':
                                currentFilteredClients = currentFilteredClients.filter(client =>
                                    client.is_late_customer === true &&
                                    client.interest_status &&
                                    client.interest_status.toLowerCase() === 'neutral'
                                );
                                break;
                        }
                    }

                    // Apply service filter if selected
                    if (serviceId && serviceId !== "") {
                        const serviceName = serviceTypeMap[serviceId];

                        currentFilteredClients = currentFilteredClients.filter(client => {
                            if (!client.interested_service) return false;

                            const clientService = client.interested_service.trim();
                            const targetService = serviceName.trim();

                            return clientService.localeCompare(targetService, undefined, {
                                sensitivity: 'base',
                                ignorePunctuation: true
                            }) === 0;
                        });
                    }

                    if (currentFilteredClients.length === 0) {
                        alert('⚠️ لا يوجد عملاء يطابقون معايير التصفية');
                    }

                    renderTable(currentFilteredClients);
                }
                function isClientLate(client, lateDaysThreshold = 3) {
                    if (!client.last_contact_date) return false;

                    const lastContactDate = new Date(client.last_contact_date);
                    const today = new Date();

                    // Calculate difference in days
                    const diffTime = today - lastContactDate;
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                    return diffDays > lateDaysThreshold;
                }
                function exportClients(selectedColumns = null) {
                    if (!selectedColumns || selectedColumns.length === 0) {
                        selectedColumns = Array.from(document.querySelectorAll('.column-checkbox input[type="checkbox"]'))
                            .filter(checkbox => checkbox.checked)
                            .map(checkbox => checkbox.value);
                    }

                    const columnsMap = {
                        'client_logo': 'شعار شركة العميل',
                        'company_name': ' الشركة',
                        'address': 'مقر الشركة',
                        'contact_person': ' الشخص المسؤول',
                        'contact_position': 'المنصب الوظيفي',
                        'phone': 'رقم الجوال',
                        'الخدمة المهتم بها': 'interested_service',
                        'whatsapp_link': 'واتس اب مباشر',
                        'interest_status': 'حالة الاهتمام',
                        'last_contact_date': ' آخر تواصل',
                        'contact_count': 'عدد مرات التواصل',
                        'contact_days_left':' آخر تواصل منذ',
                        'requests_count':'طلبات العميل',
                    };

                    const headers = selectedColumns
                        .filter(key => key !== 'client_logo')
                        .map(key => columnsMap[key]);

                    const data = currentFilteredClients.map(client => {
                        const row = {};
                        selectedColumns.forEach(key => {
                            let value = (client[key] !== undefined && client[key] !== null) ? client[key] : '';
                            switch (key) {
                                case 'client_logo':
                                    value = client.company_logo || '';
                                    break;
                                case 'company_name':
                                    value = client.company_name || '';
                                    break;
                                case 'address':
                                    value = client.address || '';
                                    break;
                                case 'contact_person':
                                    value = client.contact_person || '';
                                    break;
                                case 'contact_position':
                                    value = client.contact_position || '';
                                    break;
                                case 'phone':
                                    value = client.phone || '';
                                    break;
                                case 'whatsapp_link':
                                    value = client.whatsapp_link || '';
                                    break;
                                case 'interest_status':
                                    value = client.interest_status === 'interested' ? 'مهتم'
                                        : client.interest_status === 'not interested' ? 'غير مهتم'
                                            : 'مؤجل';
                                    break;
                                case 'last_contact_date':
                                    value = formatDateForDisplay(client.last_contact_date);
                                    break;
                                case 'contact_days_left':
                                    value = client.contact_days_left || '';
                                    break;
                                case 'contact_count':
                                    value = client.contact_count ?? 0;
                                    break;
                                case 'requests_count':
                                    value = client.requests_count ?? 0;
                                    break;
                                default:
                                    value = '';
                            }
                            row[key] = value;
                        });
                        return row;
                    });

                    const wsData = [headers, ...data.map(row => selectedColumns
                        .filter(key => key !== 'client_logo')
                        .map(key => row[key]))];
                    const worksheet = XLSX.utils.aoa_to_sheet(wsData);

                    // Auto-fit columns
                    const colWidths = wsData[0].map((_, colIndex) => {
                        const maxLen = wsData.reduce((max, row) => {
                            const cell = row[colIndex] ? String(row[colIndex]) : '';
                            return Math.max(max, cell.length);
                        }, 10);
                        return { wch: maxLen + 2 };
                    });
                    worksheet['!cols'] = colWidths;

                    const workbook = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(workbook, worksheet, "العملاء");

                    XLSX.writeFile(workbook, `عملاء_المبيعات_${new Date().toISOString().slice(0, 10)}.xlsx`);
                }
                function exportToPDF(selectedColumns) {
                    // Clone the table to modify for PDF export
                    const originalTable = document.querySelector('.data-table');
                    const table = originalTable.cloneNode(true);
                    const pdfHeader = document.querySelector('.pdf-header').cloneNode(true);
                    const pdfFooter = document.querySelector('.pdf-footer').cloneNode(true);

                    // Show header and footer
                    pdfHeader.style.display = 'block';
                    pdfFooter.style.display = 'block';
                    pdfFooter.style.padding = '20px';

                    // Create a container for the PDF content
                    const pdfContainer = document.createElement('div');
                    pdfContainer.style.padding = '20px';
                    pdfContainer.appendChild(pdfHeader);
                    pdfContainer.appendChild(table);
                    pdfContainer.appendChild(pdfFooter);

                    // Get all table headers
                    const headers = table.querySelectorAll('thead th');

                    // Hide columns that are not selected
                    headers.forEach((header, index) => {
                        const columnName = header.textContent.trim();
                        const columnKey = getColumnKey(columnName);

                        // Skip first and last columns (logo and chat) which are marked as no-print
                        if (header.classList.contains('no-print')) {
                            header.style.display = 'none';
                            table.querySelectorAll('tbody tr').forEach(row => {
                                if (row.cells[index]) {
                                    row.cells[index].style.display = 'none';
                                }
                            });
                            return;
                        }

                        if (!selectedColumns.includes(columnKey)) {
                            header.style.display = 'none';
                            table.querySelectorAll('tbody tr').forEach(row => {
                                if (row.cells[index]) {
                                    row.cells[index].style.display = 'none';
                                }
                            });
                        }
                    });

                    // Options for html2pdf
                    const options = {
                        margin: 10,
                        filename: `تقرير_عملاء_الشركة_${new Date().toISOString().slice(0,10)}.pdf`,
                        image: { type: 'jpeg', quality: 0.98 },
                        html2canvas: {
                            scale: 2,
                            scrollX: 0,
                            scrollY: 0,
                            windowWidth: document.documentElement.offsetWidth
                        },
                        jsPDF: {
                            unit: 'mm',
                            format: [594, 420],
                            orientation: 'landscape',
                            compress: true
                        }
                    };

                    // Generate PDF
                    html2pdf().set(options).from(pdfContainer).save();
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
                function previewLogoUpload(input) {
                    const logoPreview = document.getElementById('logoPreview');


                    if (input.files && input.files[0]) {
                        const reader = new FileReader();

                        reader.onload = function(e) {
                            logoPreview.src = e.target.result;
                            logoPreview.style.display = 'block';

                        }

                        reader.readAsDataURL(input.files[0]);
                    }
                }


                // New function to update WhatsApp link display
                function updateWhatsAppLink(clientId, phoneNumber) {
                    // Find the row for this client
                    const rows = document.querySelectorAll('.data-table tbody tr');

                    for (const row of rows) {
                        // Find the phone cell in this row to check if it's the right client
                        const phoneCell = row.querySelector('[data-field="phone"]');
                        if (phoneCell && phoneCell.getAttribute('data-client-id') == clientId) {
                            // Find the WhatsApp link cell (7th cell, index 6)
                            const whatsappCell = row.cells[6];
                            if (whatsappCell) {
                                // Generate new WhatsApp link - clean phone number and add WhatsApp URL
                                const cleanPhone = phoneNumber.replace(/\D/g, ''); // Remove non-digit characters
                                const whatsappLink = `https://wa.me/${cleanPhone}`;

                                // Update the cell content
                                whatsappCell.innerHTML = `
                    <span class="cell-value">
                        <a href="${whatsappLink}" class="text-green-600 hover:underline" target="_blank">
                            <i class="fab fa-whatsapp"></i> تواصل
                        </a>
                    </span>
                `;
                            }
                            break;
                        }
                    }
                }



                // Helper function to update cell display based on field type


                function showNotification(message, type) {
                    // Create notification element
                    const notification = document.createElement('div');
                    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 6px;
        color: white;
        font-weight: 500;
        z-index: 10000;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    `;

                    notification.style.backgroundColor = type === 'success' ? '#10b981' : '#ef4444';
                    notification.textContent = message;

                    document.body.appendChild(notification);

                    // Remove notification after 3 seconds
                    setTimeout(() => {
                        notification.style.opacity = '0';
                        notification.style.transition = 'opacity 0.5s';
                        setTimeout(() => notification.remove(), 500);
                    }, 3000);
                }
                function filterByCreatedDate() {
                    const createdAtDate = document.getElementById('createdAtFilter').value;

                    if (!createdAtDate) {
                        alert('الرجاء تحديد تاريخ الإنشاء');
                        return;
                    }

                    const filteredClients = ClientsData.filter(client => {
                        if (!client.client_created_at) return false;

                        // Convert both dates to YYYY-MM-DD format for comparison
                        const clientDate = new Date(client.client_created_at).toISOString().split('T')[0];
                        return clientDate === createdAtDate;
                    });

                    if (filteredClients.length === 0) {
                        alert('لا توجد عملاء تم إنشاؤهم في هذا التاريخ');
                        return;
                    }

                    currentFilteredClients = filteredClients;
                    renderTable(currentFilteredClients);
                }

                function resetCreatedDateFilter() {
                    document.getElementById('createdAtFilter').value = '';
                    hideCreatedAtCount();
                    currentFilteredClients = [...ClientsData];
                    renderTable(currentFilteredClients);
                }

                function updateCreatedAtCount(dateStr) {
                    const count = ClientsData.filter(client => {
                        if (!client.client_created_at) return false;
                        const clientDate = new Date(client.client_created_at).toISOString().split('T')[0];
                        return clientDate === dateStr;
                    }).length;

                    const countBadge = document.getElementById('createdAtCount');
                    countBadge.textContent = count;
                    countBadge.style.display = count > 0 ? 'block' : 'none';
                }

                function hideCreatedAtCount() {
                    document.getElementById('createdAtCount').style.display = 'none';
                }
                function getClientUrl(salesRepId, clientId) {

                    return `/sales-reps/${salesRepId}/clients/${clientId}`;
                }
                function openEditModal(clientId, field, currentValue) {
                    const modal = document.getElementById('editModal');
                    const fieldLabel = document.getElementById('fieldLabel');
                    const inputContainer = document.getElementById('inputContainer');
                    const editClientId = document.getElementById('editClientId');
                    const editField = document.getElementById('editField');

                    // Set modal title and field label based on field type
                    const fieldLabels = {
                        'company_name': 'اسم الشركة',
                        'address': 'عنوان الشركة',
                        'contact_person': 'الشخص المسؤول',
                        'contact_position': 'المنصب الوظيفي',
                        'phone': 'رقم الجوال',
                        'interest_status': 'حالة الاهتمام'
                    };

                    document.getElementById('modalTitle').textContent = `تعديل ${fieldLabels[field]}`;
                    fieldLabel.textContent = fieldLabels[field];
                    editClientId.value = clientId;
                    editField.value = field;

                    // Create appropriate input based on field type
                    if (field === 'interest_status') {
                        inputContainer.innerHTML = `
            <select name="value" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="interested" ${currentValue === 'interested' ? 'selected' : ''}>مهتم</option>
                <option value="not interested" ${currentValue === 'not interested' ? 'selected' : ''}>غير مهتم</option>
                <option value="neutral" ${currentValue === 'neutral' ? 'selected' : ''}>مؤجل</option>
            </select>
        `;
                    } else if (field === 'phone') {
                        inputContainer.innerHTML = `
            <input type="tel" name="value" value="${currentValue}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                   dir="ltr">
        `;
                    } else {
                        inputContainer.innerHTML = `
            <input type="text" name="value" value="${currentValue}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        `;
                    }

                    modal.classList.remove('hidden');
                }

                // Close modal
                function closeEditModal() {
                    document.getElementById('editModal').classList.add('hidden');
                }


            </script>
            <script>
                flatpickr("#fromDate", {
                    locale: "ar",
                    dateFormat: "Y-m-d",
                    allowInput: true,
                    defaultDate: null,
                });

                flatpickr("#toDate", {
                    locale: "ar",
                    dateFormat: "Y-m-d",
                    allowInput: true,
                    defaultDate: null,
                });

                flatpickr("#createdAtFilter", {
                    locale: "ar",
                    dateFormat: "Y-m-d",
                    allowInput: true,
                    defaultDate: null,
                    onChange: function(selectedDates, dateStr) {
                        if (dateStr) {
                            updateCreatedAtCount(dateStr);
                        } else {
                            hideCreatedAtCount();
                        }
                    }
                });
            </script>


            <script>
                // Function to open the client edit modal
                function openClientEditModal(clientId) {
                    // Find the client data
                    const client = ClientsData.find(c => c.client_id == clientId);

                    if (!client) {
                        showNotification('لم يتم العثور على بيانات العميل', 'error');
                        return;
                    }

                    // Populate the form fields
                    document.getElementById('editClientId').value = client.client_id;
                    document.getElementById('editCompanyName').value = client.company_name || '';
                    document.getElementById('editClientLogo').src = client.company_logo || '/path/to/placeholder/image.jpg';
                    document.getElementById('editAddress').value = client.address || '';
                    document.getElementById('editContactPerson').value = client.contact_person || '';
                    document.getElementById('editContactPosition').value = client.contact_position || '';
                    document.getElementById('editPhone').value = client.phone || '';
                    // document.getElementById('editWhatsappLink').value = client.whatsapp_link || '';
                    document.getElementById('editInterestStatus').value = client.interest_status || 'neutral';
                    document.getElementById('editInterestedService').value = client.interested_service || '';
                    document.getElementById('editContactCount').value = client.contact_count || 0;
                    document.getElementById('editServiceCount').value = client.interested_service_count || 0;
                    document.getElementById('editLastContactDate').value = client.last_contact_date || '';
                    document.getElementById('editSalesRep').value = client.sales_rep_name || '';

                    // Update modal title with client name
                    document.getElementById('modalClientName').textContent = `تعديل بيانات ${client.company_name || 'العميل'}`;

                    // Show the modal - FIXED: Remove the 'hidden' class
                    const modal = document.getElementById('clientEditModal');
                    modal.classList.remove('hidden');
                    modal.style.display = 'flex';
                }

                // Function to close the modal - FIXED
                function closeClientEditModal() {
                    const modal = document.getElementById('clientEditModal');
                    modal.classList.add('hidden');
                    modal.style.display = 'none';
                }

                // Function to save client edits
                function saveClientEdits() {
                    const formData = new FormData(document.getElementById('clientEditForm'));
                    const clientId = formData.get('client_id');

                    // Convert form data to JSON object
                    const data = {};
                    for (let [key, value] of formData.entries()) {
                        if (key !== 'client_id') {
                            data[key] = value;
                        }
                    }

                    // Show loading state
                    const saveBtn = document.querySelector('#clientEditModal .modal-footer button:last-child');
                    const originalText = saveBtn.textContent;
                    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...';
                    saveBtn.disabled = true;

                    // Send AJAX request
                    fetch(`/api/clients/${clientId}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(data)
                    })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                // Update local data
                                const clientIndex = ClientsData.findIndex(c => c.client_id == clientId);
                                if (clientIndex !== -1) {
                                    Object.assign(ClientsData[clientIndex], data);
                                }

                                // Update current filtered data
                                const filteredIndex = currentFilteredClients.findIndex(c => c.client_id == clientId);
                                if (filteredIndex !== -1) {
                                    Object.assign(currentFilteredClients[filteredIndex], data);
                                }

                                // Re-render table
                                renderTable();

                                // Show success message
                                showNotification('تم تحديث بيانات العميل بنجاح', 'success');
                                closeClientEditModal();
                            } else {
                                throw new Error(result.message || 'فشل في حفظ التغييرات');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showNotification('حدث خطأ أثناء حفظ التغييرات', 'error');
                        })
                        .finally(() => {
                            // Restore button state
                            saveBtn.textContent = originalText;
                            saveBtn.disabled = false;
                        });
                }

                // Close modal when clicking outside
                document.getElementById('clientEditModal').addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeClientEditModal();
                    }
                });

                // Close modal with Escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && !document.getElementById('clientEditModal').classList.contains('hidden')) {
                        closeClientEditModal();
                    }
                });
                function updateClientCountBadge(dateStr) {
                    if (!dateStr) {
                        document.getElementById('createdAtCountBadge').classList.add('hidden');
                        return;
                    }

                    const count = ClientsData.filter(client => {
                        if (!client.client_created_at) return false;
                        // Convert both dates to same format for accurate comparison
                        const clientDate = new Date(client.client_created_at);
                        const selectedDate = new Date(dateStr);

                        return clientDate.toISOString().split('T')[0] === selectedDate.toISOString().split('T')[0];
                    }).length;

                    document.getElementById('createdAtCount').textContent = count;
                    document.getElementById('createdAtCountBadge').classList.remove('hidden');
                }
                // Function to hide the badge
                function hideClientCountBadge() {
                    document.getElementById('createdAtCountBadge').classList.add('hidden');
                }
            </script>
@endpush
