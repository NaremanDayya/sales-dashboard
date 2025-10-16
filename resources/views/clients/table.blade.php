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

        .clickable-cell {
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .clickable-cell:hover {
            background-color: #f1f5f9;
            color: #4154f1;
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

        /* Modal and dropdown styles */
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
        .modal-body::-webkit-scrollbar {
            width: 6px;
        }

        .modal-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .modal-body::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .modal-body::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Ensure no word wrapping in table cells */
        .data-table td,
        .data-table th {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="mb-4 sm:mb-0">
                        <h1 class="text-2xl font-bold text-gray-900">العملاء</h1>
                        <p class="text-gray-600 mt-1">إدارة وعرض جميع عملاء الشركة</p>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-wrap gap-3">
                        @if(Auth::user()->role == 'salesRep')
                            <a class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200"
                               href="{{ route('sales-reps.clients.create', Auth::user()->salesRep->id) }}">
                                <i class="fas fa-plus ml-2"></i>
                                إضافة عميل
                            </a>
                        @endif

                        @if(Auth::user()->role == 'admin')
                            <a class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200"
                               href="{{ route('admin.shared-companies') }}">
                                <i class="fas fa-users ml-2"></i>
                                العملاء المشتركين
                            </a>

                            <!-- Late Days Settings Button -->
                            <div class="late-days-btn-group" x-data="{ open: false }">
                                <button @click="open = true" class="inline-flex items-center px-4 py-2 bg-yellow-500 border border-transparent rounded-md font-semibold text-white hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 transition-colors duration-200">
                                    <i class="fas fa-cog ml-2"></i>
                                    أيام التأخير
                                </button>

                                <!-- Modal -->
                                <div x-show="open" x-cloak class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50 p-4">
                                    <div @click.away="open = false" class="bg-white rounded-lg shadow-xl w-full max-w-md">
                                        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                                            <h3 class="text-lg font-semibold text-gray-900">تعديل عدد أيام التأخير</h3>
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

                        <!-- Export and Columns -->
                        <div class="flex gap-3">
                            <!-- Columns Selection -->
                            <div class="export-btn-group">
                                <button id="columnsBtn" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md font-semibold text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 transition-colors duration-200" onclick="openColumnsModal()">
                                    <i class="fas fa-columns ml-2"></i>
                                    اختيار الأعمدة
                                    <span id="columnsBadge" class="bg-white text-purple-600 rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-2">13</span>
                                </button>
                            </div>

                            <!-- Export Dropdown -->
                            <div class="export-options">
                                <div class="dropdown relative">
                                    <button class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-white hover:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200" id="exportBtn" type="button">
                                        تصدير البيانات
                                        <i class="fas fa-chevron-down mr-2 text-sm"></i>
                                    </button>
                                    <div class="dropdown-menu absolute top-full left-0 mt-1 w-48 bg-white rounded-md shadow-lg py-1 z-10 border border-gray-200">
                                        <button class="dropdown-item flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-right" data-type="xlsx">
                                            <i class="fas fa-file-excel ml-2 text-green-600"></i>
                                            تصدير كملف Excel
                                        </button>
                                        <button class="dropdown-item flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 w-full text-right" data-type="pdf">
                                            <i class="fas fa-file-pdf ml-2 text-red-600"></i>
                                            تصدير كملف PDF
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Print Button -->
                            <button class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-white hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200 no-print" onclick="window.print()">
                                <i class="fas fa-print ml-2"></i>
                                طباعة
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ request()->url() }}" id="filterForm">
                        <!-- First Row: Search and Main Filters -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                            <!-- Search Box -->
                            <div class="relative">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text"
                                       class="block w-full pr-10 pl-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                       placeholder="بحث في العملاء..."
                                       id="searchInput"
                                       name="search"
                                       value="{{ request('search') }}">
                            </div>

                            <!-- Main Filters -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                                <!-- Interest Status -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">حالة الاهتمام</label>
                                    <select id="filterSelect" name="interest_status" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
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

                                <!-- Service Type -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">نوع الخدمة</label>
                                    <select id="serviceTypeFilter" name="service" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                                        <option value="">الكل</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}" {{ request('service') == $service->id ? 'selected' : '' }}>{{ $service->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Sales Rep -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">مندوب المبيعات</label>
                                    <select id="salesRepFilter" name="sales_rep" class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200">
                                        <option value="">كل المندوبين</option>
                                        @foreach($sales_rep_names as $name)
                                            <option value="{{ $name }}" {{ request('sales_rep') == $name ? 'selected' : '' }}>{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Late Days -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">أيام التأخير</label>
                                    <input type="number"
                                           name="late_days"
                                           value="{{ request('late_days', \App\Models\Setting::where('key', 'late_customer_days')->value('value') ?? 3) }}"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                           placeholder="أيام التأخر"
                                           min="1">
                                </div>
                            </div>
                        </div>

                        <!-- Second Row: Date Filters -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Created Date Filter -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">تاريخ الإنشاء</label>
                                <div class="flex gap-2">
                                    <input type="text"
                                           id="createdAtFilter"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                           placeholder="تاريخ الإنشاء"
                                           name="created_date"
                                           value="{{ request('created_date') }}">
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200">
                                        <i class="fas fa-search"></i>
                                    </button>
                                    <button type="button" onclick="resetDate('created_date')" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors duration-200">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Contact Date Range -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">نطاق تاريخ التواصل</label>
                                <div class="flex gap-2">
                                    <input type="text"
                                           id="fromDate"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                           placeholder="من تاريخ"
                                           name="from_date"
                                           value="{{ request('from_date') }}">
                                    <input type="text"
                                           id="toDate"
                                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                           placeholder="إلى تاريخ"
                                           name="to_date"
                                           value="{{ request('to_date') }}">
                                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200">
                                        <i class="fas fa-filter"></i>
                                    </button>
                                    <button type="button" onclick="resetDateRange()" class="px-4 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 transition-colors duration-200">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-gray-200">
                            <button type="submit" class="px-6 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200 font-medium">
                                <i class="fas fa-filter ml-2"></i>
                                تطبيق الفلتر
                            </button>
                            <button type="button" onclick="resetFilters()" class="px-6 py-2 bg-gray-500 text-white rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors duration-200 font-medium">
                                <i class="fas fa-redo ml-2"></i>
                                إعادة تعيين
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table Section -->
            <div id="print-area" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                <!-- PDF Header (Hidden by default) -->
                <div class="pdf-header hidden">
                    <div class="text-center py-4 border-b-2 border-gray-800 bg-gray-100">
                        <div class="flex items-center justify-center gap-4 mb-2">
                            <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="h-16" />
                            <h2 class="text-2xl font-bold text-blue-600">تقرير العملاء</h2>
                        </div>
                    </div>
                </div>

                <!-- Table Container -->
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 data-table">
                        <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">شعار شركة العميل</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الشركة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">مقر الشركة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الشخص المسؤول</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المنصب الوظيفي</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم الجوال</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider no-print">واتس اب مباشر</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">حالة الاهتمام</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الخدمة المهتم بها</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">عدد الخدمة</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">آخر تواصل</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">آخر تواصل منذ</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">عدد مرات التواصل</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">طلبات العميل</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider no-print">الدردشة</th>
                        </tr>
                        </thead>
                        <tbody id="tableBody" class="bg-white divide-y divide-gray-200">
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

    <!-- Columns Selection Modal -->
    <div id="columnsModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-columns ml-2 text-purple-600"></i>
                    اختيار الأعمدة للعرض
                </h3>
                <button class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closeColumnsModal()">
                    <i class="fas fa-times text-lg"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto">
                <div class="mb-4">
                    <div class="relative">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text"
                               id="columnsSearch"
                               placeholder="بحث عن عمود..."
                               class="block w-full pr-10 pl-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                               onkeyup="filterColumns()">
                    </div>
                </div>
                <div class="columns-list grid grid-cols-1 gap-2" id="columnsList">
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
                                            <span class="column-name">شعار شركة العميل</span>
                                        </label>
                                    </div>
                                    <div class="column-item">
                                        <label class="column-checkbox">
                                            <input type="checkbox" value="company_name" checked>
                                            <span class="checkmark"></span>
                                            <span class="column-name"> الشركة </span>
                                        </label>
                                    </div>
                                    <div class="column-item">
                                        <label class="column-checkbox">
                                            <input type="checkbox" value="address" checked>
                                            <span class="checkmark"></span>
                                            <span class="column-name"> مقر الشركة</span>
                                        </label>
                                    </div>
                                    <div class="column-item">
                                        <label class="column-checkbox">
                                            <input type="checkbox" value="contact_person" checked>
                                            <span class="checkmark"></span>
                                            <span class="column-name"> الشخص المسؤول</span>
                                        </label>
                                    </div>
                                    <div class="column-item">
                                        <label class="column-checkbox">
                                            <input type="checkbox" value="contact_position" checked>
                                            <span class="checkmark"></span>
                                            <span class="column-name">المنصب الوظيفي </span>
                                        </label>
                                    </div>
                                    <div class="column-item">
                                        <label class="column-checkbox">
                                            <input type="checkbox" value="phone" checked>
                                            <span class="checkmark"></span>
                                            <span class="column-name">رقم الجوال</span>
                                        </label>
                                    </div>
                                    <div class="column-item">
                                        <label class="column-checkbox">
                                            <input type="checkbox" value="whatsapp_link" checked>
                                            <span class="checkmark"></span>
                                            <span class="column-name">رابط واتس اب مباشر</span>
                                        </label>
                                    </div>
                                    <div class="column-item">
                                        <label class="column-checkbox">
                                            <input type="checkbox" value="interest_status" checked>
                                            <span class="checkmark"></span>
                                            <span class="column-name">حالة الاهتمام </span>
                                        </label>
                                    </div>
                                    <div class="column-item">
                                        <label class="column-checkbox">
                                            <input type="checkbox" value="interested_service" checked>
                                            <span class="checkmark"></span>
                                            <span class="column-name">الخدمة المهتم بها</span>
                                        </label>
                                    </div>
                                    <div class="column-item">
                                        <label class="column-checkbox">
                                            <input type="checkbox" value="last_contact_date" checked>
                                            <span class="checkmark"></span>
                                            <span class="column-name">تاریخ آخر تواصل </span>
                                        </label>
                                    </div>
                                    <div class="column-item">
                                        <label class="column-checkbox">
                                            <input type="checkbox" value="contact_days_left" checked>
                                            <span class="checkmark"></span>
                                            <span class="column-name"> آخر تواصل منذ</span>
                                        </label>
                                    </div>
                                    <div class="column-item">
                                        <label class="column-checkbox">
                                            <input type="checkbox" value="contact_count" checked>
                                            <span class="checkmark"></span>
                                            <span class="column-name"> عدد مرات التواصل </span>
                                        </label>
                                    </div>
                                    <div class="column-item">
                                        <label class="column-checkbox">
                                            <input type="checkbox" value="requests_count" checked>
                                            <span class="checkmark"></span>
                                            <span class="column-name">طلبات العميل </span>
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
                    </div
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-between items-center">
                <div class="flex gap-4">
                    <button class="text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200" onclick="toggleSelectAll()">تحديد الكل</button>
                    <button class="text-gray-600 hover:text-gray-800 font-medium transition-colors duration-200" onclick="resetSelection()">إعادة تعيين</button>
                </div>
                <div class="flex gap-3">
                    <button class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors duration-200" onclick="closeColumnsModal()">إلغاء</button>
                    <button class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200 flex items-center" onclick="applyColumnSelection()">
                        <i class="fas fa-check ml-2"></i>
                        تطبيق
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md mx-4">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-900" id="modalTitle">تعديل البيانات</h3>
                <button class="text-gray-400 hover:text-gray-600 transition-colors" onclick="closeEditModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="px-6 py-4">
                <form id="editForm">
                    <input type="hidden" id="editClientId" name="client_id">
                    <input type="hidden" id="editField" name="field">
                    <div class="mb-4">
                        <label id="fieldLabel" class="block text-sm font-medium text-gray-700 mb-2"></label>
                        <div id="inputContainer">
                            <!-- Input field will be inserted here dynamically -->
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeEditModal()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                            إلغاء
                        </button>
                        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors duration-200">
                            حفظ
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

        @push('scripts')
            <script>
                function resetFilters() {
                    window.location.href = "{{ request()->url() }}";
                }

                function resetDate(fieldName) {
                    document.querySelector(`[name="${fieldName}"]`).value = '';
                    document.getElementById('filterForm').submit();
                }

                function resetDateRange() {
                    document.querySelector('[name="from_date"]').value = '';
                    document.querySelector('[name="to_date"]').value = '';
                    document.getElementById('filterForm').submit();
                }

                // Initialize flatpickr
                flatpickr("#fromDate", {
                    locale: "ar",
                    dateFormat: "Y-m-d",
                    allowInput: true,
                    defaultDate: "{{ request('from_date') }}",
                });

                flatpickr("#toDate", {
                    locale: "ar",
                    dateFormat: "Y-m-d",
                    allowInput: true,
                    defaultDate: "{{ request('to_date') }}",
                });

                flatpickr("#createdAtFilter", {
                    locale: "ar",
                    dateFormat: "Y-m-d",
                    allowInput: true,
                    defaultDate: "{{ request('created_date') }}",
                });
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

                    // Handle export option clicks
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
                function toggleFakePlaceholder(input) {
                    const placeholder = document.getElementById('fakePlaceholder');
                    placeholder.style.display = input.value ? 'none' : 'block';
                }

                function resetDateFilter() {
                    const input = document.getElementById('dateFilter');
                    input.value = '';
                    toggleFakePlaceholder(input);
                    // add your reset logic here...
                }

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
<td class="px-4 py-2 text-sm font-semibold text-gray-800">
    <div class="flex flex-col items-center">
        <!-- Clickable company name -->
        <span class="cell-value clickable-cell text-center mb-1"
              onclick="redirectToClient(${client.sales_rep_id}, ${client.client_id})">
            ${client.company_name || '—'}
        </span>

        <!-- Sales rep name -->
        <div class="text-xs text-gray-500">
            ${client.sales_rep_name || '—'}
        </div>


    </div>
</td>

<!-- Address Cell -->
<td class="px-4 py-2 text-sm text-gray-600">
    <div class="flex items-center justify-between">
        <span class="cell-value clickable-cell"
              onclick="redirectToClient(${client.sales_rep_id}, ${client.client_id})">
            ${client.address || '—'}
        </span>

    </div>
</td>
            <!-- Contact Person -->
<td class="px-4 py-2 text-sm text-gray-700">
    <div class="flex items-center justify-between">
        <span class="cell-value clickable-cell"
              onclick="redirectToClient(${client.sales_rep_id}, ${client.client_id})">
            ${client.contact_person || '—'}
        </span>

    </div>
</td>

<!-- Contact Position Cell -->
<td class="px-4 py-2 text-sm text-gray-700">
    <div class="flex items-center justify-between">
        <span class="cell-value clickable-cell"
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
           <td class="px-4 py-2 text-sm font-medium text-blue-700" dir="rtl">
    ${client.interested_service ? client.interested_service : '—'}
</td>

<td class="px-4 py-2 text-sm font-medium text-gray-600" dir="rtl">
    ${client.interested_service_count > 0 ? `
        <span class="inline-flex items-center px-2 py-0.5 rounded bg-green-100 text-blue-800">
            ${client.interested_service_count}
        </span>
    ` : '-'}
</td>




            <!-- Last Contact Date -->
            <td class="px-4 py-2 text-sm text-center ${ client.is_late_customer ? 'text-red-600 font-bold' : 'text-green-600 font-bold' }">
                ${formatDateForDisplay(client.last_contact_date) || '—'}
            </td>

            <!-- Days Left -->
            <td class="px-4 py-2 text-sm text-center ${client.is_late_customer ? 'text-red-600 font-bold' : 'text-green-600 font-bold'}">
                ${client.contact_days_left ?
                            `${client.contact_days_left} ${getArabicDaysWord(client.contact_days_left)}` :
                            '—'
                        }
            </td>

            <!-- Contact Count -->
            <td class="px-4 py-2 text-sm text-center">
                <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-100 text-blue-800">
                    ${client.contact_count || 0}
                </span>
            </td>

            <!-- Requests Count -->
            <td class="px-4 py-2 text-sm text-center text-gray-400 cursor-pointer"
                onclick="window.location.href='/salesrep/' + ${client.sales_rep_id} + '/MyRequests'">
                <span class="inline-flex items-center px-2 py-0.5 rounded bg-purple-100 text-blue-800">
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
</td>
        `;
                        tbody.appendChild(row);
                    });
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

                function startEditing(cell) {
                    // If already in edit mode, do nothing
                    if (cell.querySelector('.edit-form')) return;

                    const value = cell.querySelector('.cell-value').textContent;
                    const clientId = cell.getAttribute('data-client-id');
                    const field = cell.getAttribute('data-field');

                    // Special handling for whatsapp link
                    let displayValue = value;
                    if (field === 'whatsapp_link' && value !== '—') {
                        const linkElement = cell.querySelector('a');
                        displayValue = linkElement ? linkElement.getAttribute('href') : value;
                    }

                    cell.innerHTML = `
        <div class="edit-form">
            <input type="text" class="edit-input" value="${displayValue}" data-original-value="${displayValue}">
            <div class="edit-actions">
                <button class="edit-btn edit-save" onclick="saveEdit(this, ${clientId}, '${field}')">
                    <i class="fas fa-check"></i>
                </button>
                <button class="edit-btn edit-cancel" onclick="cancelEdit(this)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `;

                    // Focus the input
                    cell.querySelector('.edit-input').focus();
                }

                // Special function for interest status (dropdown instead of text input)
                function startEditingInterestStatus(cell) {
                    // If already in edit mode, do nothing
                    if (cell.querySelector('.edit-form')) return;

                    const clientId = cell.getAttribute('data-client-id');
                    const field = cell.getAttribute('data-field');
                    const currentStatus = cell.querySelector('.cell-value span').textContent.trim();

                    // Map display text to values
                    const statusMap = {
                        'مهتم': 'interested',
                        'غير مهتم': 'not interested',
                        'مؤجل': 'neutral'
                    };

                    const currentValue = statusMap[currentStatus] || 'neutral';

                    cell.innerHTML = `
        <div class="edit-form">
            <select class="edit-input" data-original-value="${currentValue}">
                <option value="interested" ${currentValue === 'interested' ? 'selected' : ''}>مهتم</option>
                <option value="not interested" ${currentValue === 'not interested' ? 'selected' : ''}>غير مهتم</option>
                <option value="neutral" ${currentValue === 'neutral' ? 'selected' : ''}>مؤجل</option>
            </select>
            <div class="edit-actions">
                <button class="edit-btn edit-save" onclick="saveEdit(this, ${clientId}, '${field}')">
                    <i class="fas fa-check"></i>
                </button>
                <button class="edit-btn edit-cancel" onclick="cancelEdit(this)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `;
                }

                function saveEdit(button, clientId, field) {
                    const form = button.closest('.edit-form');
                    const input = form.querySelector('.edit-input');
                    let newValue;

                    // Handle different input types
                    if (input.tagName === 'SELECT') {
                        newValue = input.value;
                    } else {
                        newValue = input.value.trim();
                    }

                    const originalValue = input.getAttribute('data-original-value');

                    if (newValue === originalValue) {
                        cancelEdit(button);
                        return;
                    }

                    // Show loading state
                    button.disabled = true;
                    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                    // Send AJAX request to update the value
                    fetch(`/api/clients/${clientId}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            [field]: newValue
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Update the cell with the new value
                                const cell = button.closest('.editable-cell');
                                updateCellDisplay(cell, field, newValue);

                                // If phone was updated, also update the WhatsApp link
                                if (field === 'phone') {
                                    updateWhatsAppLink(clientId, newValue);
                                }

                                // Update the data in our local array
                                const clientIndex = ClientsData.findIndex(c => c.client_id === clientId);
                                if (clientIndex !== -1) {
                                    ClientsData[clientIndex][field] = newValue;

                                    // If phone was updated, update WhatsApp link in local data too
                                    if (field === 'phone') {
                                        const cleanPhone = newValue.replace(/\D/g, ''); // Remove non-digit characters
                                        ClientsData[clientIndex].whatsapp_link = `https://wa.me/${cleanPhone}`;
                                    }
                                }

                                // Show success message
                                showNotification('تم تحديث البيانات بنجاح يا قمر', 'success');
                            } else {
                                throw new Error(data.message || 'فشل في التحديث');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showNotification('حدث خطأ أثناء التحديث', 'error');
                            cancelEdit(button);
                        });
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


                function cancelEdit(button) {
                    const cell = button.closest('.editable-cell');
                    const field = cell.getAttribute('data-field');
                    const clientId = cell.getAttribute('data-client-id');

                    // Find the original value from our data
                    const client = ClientsData.find(c => c.client_id == clientId);
                    if (client) {
                        updateCellDisplay(cell, field, client[field]);
                    }
                }

                // Helper function to update cell display based on field type
                function updateCellDisplay(cell, field, value) {
                    switch (field) {
                        case 'company_name':
                            cell.innerHTML = `
                <span class="cell-value">${value || '—'}</span>
                <i class="fas fa-edit edit-icon"></i>
            `;
                            break;

                        case 'phone':
                            const formattedPhone = value ? (value.startsWith('+') ? value : '+' + value) : '—';
                            cell.innerHTML = `
                <span class="cell-value">
                    <span dir="ltr" class="ltr-number">${formattedPhone}</span>
                </span>
                <i class="fas fa-edit edit-icon"></i>
            `;
                            break;



                        case 'interest_status':
                            const statusText = {
                                'interested': 'مهتم',
                                'not interested': 'غير مهتم',
                                'neutral': 'مؤجل'
                            }[value] || 'مؤجل';

                            const statusClass = {
                                'interested': 'bg-green-100 text-green-800',
                                'not interested': 'bg-red-100 text-red-800',
                                'neutral': 'bg-gray-100 text-gray-700'
                            }[value] || 'bg-gray-100 text-gray-700';

                            cell.innerHTML = `
                <span class="cell-value">
                    <span class="inline-block px-2 py-0.5 rounded-full ${statusClass}">
                        ${statusText}
                    </span>
                </span>
                <i class="fas fa-edit edit-icon"></i>
            `;
                            break;

                        default:
                            cell.innerHTML = `
                <span class="cell-value">${value || '—'}</span>
                <i class="fas fa-edit edit-icon"></i>
            `;
                    }
                }

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

                // Handle form submission
                document.getElementById('editForm').addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    const clientId = formData.get('client_id');
                    const field = formData.get('field');
                    const value = formData.get('value');

                    // Show loading state
                    const submitBtn = this.querySelector('button[type="submit"]');
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = 'جاري الحفظ...';

                    // Send AJAX request
                    fetch(`/api/clients/${clientId}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ [field]: value })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Update local data
                                const clientIndex = ClientsData.findIndex(c => c.client_id == clientId);
                                if (clientIndex !== -1) {
                                    ClientsData[clientIndex][field] = value;
                                }

                                // Update current filtered data if needed
                                const filteredIndex = currentFilteredClients.findIndex(c => c.client_id == clientId);
                                if (filteredIndex !== -1) {
                                    currentFilteredClients[filteredIndex][field] = value;
                                }

                                // Re-render table
                                renderTable();

                                // Show success message
                                showNotification('تم تحديث البيانات بنجاح', 'success');
                                closeEditModal();
                            } else {
                                throw new Error(data.message || 'فشل في التحديث');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showNotification('حدث خطأ أثناء التحديث', 'error');
                        })
                        .finally(() => {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = 'حفظ';
                        });
                });

                // Close modal when clicking outside
                document.getElementById('editModal').addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeEditModal();
                    }
                });
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
                function saveAgreementEdits() {
                    const formData = new FormData(document.getElementById('agreementEditForm'));
                    const agreementId = formData.get('agreement_id');

                    const data = {};
                    for (let [key, value] of formData.entries()) {
                        if (key !== 'agreement_id') {
                            data[key] = value;
                        }
                    }

                    // Show loading state
                    const saveBtn = document.querySelector('#agreementEditModal .modal-footer button:last-child');
                    const originalText = saveBtn.textContent;
                    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...';
                    saveBtn.disabled = true;

                    // Send AJAX request - CORRECTED URL
                    fetch(`/api/agreements/${agreementId}`, {
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
                                const agreementIndex = AgreementsData.findIndex(a => a.agreement_id == agreementId);
                                if (agreementIndex !== -1) {
                                    Object.assign(AgreementsData[agreementIndex], data);
                                }

                                // Update current filtered data
                                const filteredIndex = currentFilteredAgreements.findIndex(a => a.agreement_id == agreementId);
                                if (filteredIndex !== -1) {
                                    Object.assign(currentFilteredAgreements[filteredIndex], data);
                                }

                                // Re-render table
                                renderTable();

                                showNotification('تم تحديث بيانات الاتفاقية بنجاح', 'success');
                                closeAgreementEditModal();
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
            </script>
@endpush
