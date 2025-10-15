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
            font-family: 'Tajawal', sans-serif;
        }

        .ltr-number {
            direction: ltr;
            unicode-bidi: embed;
            display: inline-block;
        }

        .clickable-cell {
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .clickable-cell:hover {
            background-color: #f8fafc;
            color: var(--primary);
        }

        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-interested {
            background-color: #ecfdf5;
            color: #059669;
        }

        .status-not-interested {
            background-color: #fef2f2;
            color: #dc2626;
        }

        .status-neutral {
            background-color: #f3f4f6;
            color: #6b7280;
        }

        .late-indicator {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 11px;
            font-weight: 700;
        }

        .late-true {
            background-color: #fef2f2;
            color: #dc2626;
        }

        .late-false {
            background-color: #f0fdf4;
            color: #16a34a;
        }

        .count-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
        }

        .count-blue {
            background-color: #dbeafe;
            color: #1d4ed8;
        }

        .count-purple {
            background-color: #f3e8ff;
            color: #7c3aed;
        }

        .count-green {
            background-color: #dcfce7;
            color: #15803d;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .hover-lift {
            transition: all 0.3s ease;
        }

        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }

        /* Export Dropdown Styles */
        .export-dropdown {
            position: relative;
            display: inline-block;
        }

        .export-dropdown-menu {
            display: none;
            position: absolute;
            left: 0;
            top: 100%;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            z-index: 50;
            min-width: 180px;
            margin-top: 8px;
        }

        .export-dropdown-item {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            padding: 12px 16px;
            text-align: right;
            color: #374151;
            font-size: 14px;
            background: none;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .export-dropdown-item:hover {
            background: #f9fafb;
        }

        .export-dropdown-item i {
            width: 16px;
        }

        /* Columns Modal Styles */
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
            border-radius: 16px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
        }

        .columns-modal-header {
            padding: 20px 24px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8fafc;
            border-radius: 16px 16px 0 0;
        }

        .columns-modal-title {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .columns-modal-close {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #6b7280;
            transition: color 0.2s;
            padding: 4px;
            border-radius: 6px;
        }

        .columns-modal-close:hover {
            background: #f3f4f6;
            color: #374151;
        }

        .columns-modal-body {
            padding: 20px 24px;
            overflow-y: auto;
            flex-grow: 1;
        }

        .columns-search {
            position: relative;
            margin-bottom: 20px;
        }

        .columns-search input {
            width: 100%;
            padding: 12px 16px 12px 44px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            font-size: 14px;
            transition: all 0.2s;
        }

        .columns-search input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .columns-search i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #9ca3af;
        }

        .columns-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .column-item {
            padding: 8px 0;
        }

        .column-checkbox {
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            padding: 12px 16px;
            border-radius: 8px;
            transition: background-color 0.2s;
            border: 1px solid transparent;
        }

        .column-checkbox:hover {
            background-color: #f8fafc;
            border-color: #e5e7eb;
        }

        .column-checkbox input {
            display: none;
        }

        .checkmark {
            width: 20px;
            height: 20px;
            border: 2px solid #d1d5db;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            background: white;
        }

        .column-checkbox input:checked ~ .checkmark {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .checkmark:after {
            content: "✓";
            color: white;
            font-size: 12px;
            font-weight: bold;
            display: none;
        }

        .column-checkbox input:checked ~ .checkmark:after {
            display: block;
        }

        .column-name {
            font-size: 14px;
            color: #374151;
            font-weight: 500;
        }

        .columns-modal-footer {
            padding: 20px 24px;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8fafc;
            border-radius: 0 0 16px 16px;
        }

        .columns-actions {
            display: flex;
            gap: 12px;
        }

        .btn-select-all {
            background: none;
            border: none;
            color: var(--primary);
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            padding: 8px 12px;
            border-radius: 6px;
            transition: background-color 0.2s;
        }

        .btn-select-all:hover {
            background-color: #eef2ff;
        }

        .btn-cancel {
            padding: 10px 20px;
            border-radius: 8px;
            background-color: white;
            color: #374151;
            border: 1px solid #d1d5db;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            margin-left: 12px;
            transition: all 0.2s;
        }

        .btn-cancel:hover {
            background-color: #f9fafb;
            border-color: #9ca3af;
        }

        .btn-apply {
            padding: 10px 20px;
            border-radius: 8px;
            background-color: var(--primary);
            color: white;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn-apply:hover {
            background-color: var(--primary-light);
            transform: translateY(-1px);
        }

        /* Print Styles */
        @media print {
            .no-print {
                display: none !important;
            }

            .glass-card {
                background: white !important;
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }

            .hover-lift:hover {
                transform: none !important;
                box-shadow: none !important;
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .columns-modal-content {
                width: 95%;
                margin: 20px;
            }

            .columns-modal-header,
            .columns-modal-body,
            .columns-modal-footer {
                padding: 16px 20px;
            }

            .columns-actions {
                flex-direction: column;
                gap: 8px;
            }
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
@endpush

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-6 px-4">
        <div class="full-width">
            <!-- Header Section -->
            <div class="glass-card rounded-2xl shadow-sm mb-6 p-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center space-x-4 space-x-reverse mb-4 lg:mb-0">
                        <div class="p-3 bg-primary rounded-xl">
                            <i class="fas fa-users text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">العملاء</h1>
                            <p class="text-gray-600 mt-1">إدارة وعرض جميع عملاء الشركة</p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        @if(Auth::user()->role == 'salesRep')
                            <a href="{{ route('sales-reps.clients.create', Auth::user()->salesRep->id) }}"
                               class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-light text-white rounded-lg font-medium transition-all duration-200 hover-lift">
                                <i class="fas fa-plus ml-2"></i>
                                إضافة عميل
                            </a>
                        @endif

                        @if(Auth::user()->role == 'admin')
                            <a href="{{ route('admin.shared-companies') }}"
                               class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-all duration-200 hover-lift">
                                <i class="fas fa-users ml-2"></i>
                                العملاء المشتركين
                            </a>
                        @endif

                        <!-- Columns Selection Button -->
                        <button onclick="openColumnsModal()"
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-all duration-200 hover-lift">
                            <i class="fas fa-columns ml-2"></i>
                            اختيار الأعمدة
                            <span id="columnsBadge" class="mr-2 bg-primary text-white rounded-full px-2 py-1 text-xs">13</span>
                        </button>

                        <!-- Export Dropdown -->
                        <div class="export-dropdown">
                            <button id="exportBtn"
                                    class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-all duration-200 hover-lift">
                                <i class="fas fa-download ml-2"></i>
                                تصدير البيانات
                                <i class="fas fa-chevron-down mr-2"></i>
                            </button>
                            <div class="export-dropdown-menu" id="exportDropdown">
                                <button class="export-dropdown-item" data-type="xlsx">
                                    <i class="fas fa-file-excel text-green-600"></i>
                                    تصدير كملف Excel
                                </button>
                                <button class="export-dropdown-item" data-type="pdf">
                                    <i class="fas fa-file-pdf text-red-600"></i>
                                    تصدير كملف PDF
                                </button>
                            </div>
                        </div>

                        <button onclick="window.print()"
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 font-medium hover:bg-gray-50 transition-all duration-200 hover-lift">
                            <i class="fas fa-print ml-2"></i>
                            طباعة
                        </button>
                    </div>
                </div>
            </div>

            <!-- Filters Section -->
            <div class="glass-card rounded-2xl shadow-sm mb-6 p-6">
                <form method="GET" action="{{ request()->url() }}" class="space-y-4">
                    <!-- Search and Main Filters Row -->
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div class="relative">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   placeholder="ابحث بالاسم، المسؤول، أو الهاتف..."
                                   class="w-full pr-10 pl-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                        </div>

                        <!-- Interest Status -->
                        <select name="interest_status"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent bg-white">
                            <option value="">جميع حالات الاهتمام</option>
                            <option value="interested" {{ request('interest_status') == 'interested' ? 'selected' : '' }}>مهتم</option>
                            <option value="not interested" {{ request('interest_status') == 'not interested' ? 'selected' : '' }}>غير مهتم</option>
                            <option value="neutral" {{ request('interest_status') == 'neutral' ? 'selected' : '' }}>مؤجل</option>
                        </select>

                        <!-- Service Type -->
                        <select name="service_type"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent bg-white">
                            <option value="">جميع الخدمات</option>
                            @foreach($services as $service)
                                <option value="{{ $service->id }}" {{ request('service_type') == $service->id ? 'selected' : '' }}>
                                    {{ $service->name }}
                                </option>
                            @endforeach
                        </select>

                        <!-- Sales Rep Filter -->
                        <select name="sales_rep"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent bg-white">
                            <option value="">كل المندوبين</option>
                            @foreach($sales_rep_names as $name)
                                <option value="{{ $name }}" {{ request('sales_rep') == $name ? 'selected' : '' }}>
                                    {{ $name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Second Row: Late Customer Filter and Date Range -->
                    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                        <!-- Late Customer Filter -->
                        <select name="late_customer"
                                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent bg-white">
                            <option value="">جميع العملاء</option>
                            <option value="late" {{ request('late_customer') == 'late' ? 'selected' : '' }}>عملاء متأخرين</option>
                            <option value="not_late" {{ request('late_customer') == 'not_late' ? 'selected' : '' }}>عملاء غير متأخرين</option>
                        </select>

                        <!-- Date Range -->
                        <div class="lg:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">من تاريخ</label>
                                <input type="date"
                                       name="from_date"
                                       value="{{ request('from_date') }}"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">إلى تاريخ</label>
                                <input type="date"
                                       name="to_date"
                                       value="{{ request('to_date') }}"
                                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent">
                            </div>
                        </div>

                        <!-- Late Days Settings (Admin Only) -->
                        @if(Auth::user()->role == 'admin')
                            <div class="relative" x-data="{ open: false }">
                                <button type="button" @click="open = true"
                                        class="w-full px-4 py-2.5 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg font-medium transition-all duration-200 flex items-center justify-center h-full">
                                    <i class="fas fa-cog ml-2"></i>
                                    أيام التأخير
                                </button>

                                <!-- Modal -->
                                <div x-show="open" x-cloak class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
                                    <div @click.away="open = false" class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                                        <h3 class="text-lg font-bold mb-4">تعديل عدد الأيام لتأخير العميل</h3>
                                        <form action="{{ route('settings.update') }}" method="POST">
                                            @csrf
                                            <div class="mb-4">
                                                <label for="late_customer_days" class="block text-sm font-medium text-gray-700">عدد الأيام</label>
                                                <input type="number" name="late_customer_days" id="late_customer_days" min="1" max="30" required
                                                       value="{{ old('late_customer_days', \App\Models\Setting::where('key', 'late_customer_days')->value('value') ?? 3) }}"
                                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500">
                                            </div>
                                            <div class="flex justify-end space-x-2 space-x-reverse">
                                                <button type="button" @click="open = false"
                                                        class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">إلغاء</button>
                                                <button type="submit"
                                                        class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">حفظ</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div></div> <!-- Empty div for layout consistency -->
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex items-center justify-end space-x-3 space-x-reverse pt-4">
                        <button type="submit"
                                class="bg-primary hover:bg-primary-light text-white px-6 py-2.5 rounded-lg font-medium transition-all duration-200 hover-lift flex items-center justify-center">
                            <i class="fas fa-filter ml-2"></i>
                            تطبيق الفلتر
                        </button>
                        <a href="{{ request()->url() }}"
                           class="px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-all duration-200 flex items-center">
                            <i class="fas fa-times ml-2"></i>
                            إعادة تعيين
                        </a>
                    </div>

                    <!-- Active Filters -->
                    @if(request()->anyFilled(['search', 'interest_status', 'service_type', 'sales_rep', 'late_customer', 'from_date', 'to_date']))
                        <div class="pt-4 border-t border-gray-200">
                            <div class="flex items-center space-x-2 space-x-reverse flex-wrap gap-2">
                                <span class="text-sm text-gray-600">الفلاتر النشطة:</span>
                                @if(request('search'))
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-blue-100 text-blue-800">
                                        بحث: "{{ request('search') }}"
                                        <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}" class="mr-1 hover:text-blue-600">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </span>
                                @endif
                                @if(request('interest_status'))
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-green-100 text-green-800">
                                        حالة: {{ request('interest_status') == 'interested' ? 'مهتم' : (request('interest_status') == 'not interested' ? 'غير مهتم' : 'مؤجل') }}
                                        <a href="{{ request()->fullUrlWithQuery(['interest_status' => null]) }}" class="mr-1 hover:text-green-600">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </span>
                                @endif
                                @if(request('service_type'))
                                    @php
                                        $selectedService = $services->where('id', request('service_type'))->first();
                                    @endphp
                                    @if($selectedService)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-purple-100 text-purple-800">
                                            خدمة: {{ $selectedService->name }}
                                            <a href="{{ request()->fullUrlWithQuery(['service_type' => null]) }}" class="mr-1 hover:text-purple-600">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </span>
                                    @endif
                                @endif
                                @if(request('sales_rep'))
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-orange-100 text-orange-800">
                                        مندوب: {{ request('sales_rep') }}
                                        <a href="{{ request()->fullUrlWithQuery(['sales_rep' => null]) }}" class="mr-1 hover:text-orange-600">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </span>
                                @endif
                                @if(request('late_customer'))
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-red-100 text-red-800">
                                        {{ request('late_customer') == 'late' ? 'متأخرين' : 'غير متأخرين' }}
                                        <a href="{{ request()->fullUrlWithQuery(['late_customer' => null]) }}" class="mr-1 hover:text-red-600">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </span>
                                @endif
                                @if(request('from_date') || request('to_date'))
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs bg-indigo-100 text-indigo-800">
                                        تاريخ: {{ request('from_date') ?? 'البداية' }} - {{ request('to_date') ?? 'النهاية' }}
                                        <a href="{{ request()->fullUrlWithQuery(['from_date' => null, 'to_date' => null]) }}" class="mr-1 hover:text-indigo-600">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </span>
                                @endif
                            </div>
                        </div>
                    @endif
                </form>
            </div>

            <!-- Results Count -->
            <div class="mb-4 flex items-center justify-between">
                <div class="text-sm text-gray-600">
                    عرض <span class="font-bold text-primary">{{ $Clients->count() }}</span> عميل
                    @if(request()->anyFilled(['search', 'interest_status', 'service_type', 'sales_rep', 'late_customer', 'from_date', 'to_date']))
                        <span class="text-gray-500">(مصفى)</span>
                    @endif
                </div>

                @if($Clients->count() > 0)
                    <div class="text-xs text-gray-500">
                        آخر تحديث: {{ now()->format('Y-m-d H:i') }}
                    </div>
                @endif
            </div>

            <!-- Table Section -->
            <div class="glass-card rounded-2xl shadow-sm overflow-hidden">
                @if($Clients->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">شعار شركة العميل</th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الشركة</th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">مقر الشركة</th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الشخص المسؤول</th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">المنصب الوظيفي</th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم الجوال</th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">واتس اب مباشر</th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">حالة الاهتمام</th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الخدمة المهتم بها</th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">عدد الخدمة</th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">آخر تواصل</th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">آخر تواصل منذ</th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">عدد مرات التواصل</th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">طلبات العميل</th>
                                <th class="px-6 py-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الدردشة</th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($Clients as $client)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <!-- Company Logo -->
                                    <td class="px-6 py-4 text-center">
                                        @if($client['company_logo'])
                                            <div class="h-16 w-16 mx-auto border rounded-full p-2 bg-white flex items-center justify-center">
                                                <img src="{{ $client['company_logo'] }}" alt="شعار" class="max-h-full max-w-full object-contain" />
                                            </div>
                                        @else
                                            <div class="h-16 w-16 mx-auto border rounded-full p-2 bg-gray-100 flex items-center justify-center">
                                                <i class="fas fa-building text-gray-400"></i>
                                            </div>
                                        @endif
                                    </td>

                                    <!-- Company Name -->
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col items-center">
                                    <span class="clickable-cell text-center mb-1 font-medium text-gray-900"
                                          onclick="redirectToClient({{ $client['sales_rep_id'] }}, {{ $client['client_id'] }})">
                                        {{ $client['company_name'] ?? '—' }}
                                    </span>
                                            <div class="text-xs text-gray-500">
                                                {{ $client['sales_rep_name'] ?? '—' }}
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Address -->
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                <span class="clickable-cell"
                                      onclick="redirectToClient({{ $client['sales_rep_id'] }}, {{ $client['client_id'] }})">
                                    {{ $client['address'] ?? '—' }}
                                </span>
                                    </td>

                                    <!-- Contact Person -->
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                <span class="clickable-cell"
                                      onclick="redirectToClient({{ $client['sales_rep_id'] }}, {{ $client['client_id'] }})">
                                    {{ $client['contact_person'] ?? '—' }}
                                </span>
                                    </td>

                                    <!-- Contact Position -->
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                <span class="clickable-cell"
                                      onclick="redirectToClient({{ $client['sales_rep_id'] }}, {{ $client['client_id'] }})">
                                    {{ $client['contact_position'] ?? '—' }}
                                </span>
                                    </td>

                                    <!-- Phone -->
                                    <td class="px-6 py-4 text-sm text-blue-700 font-bold">
                                <span class="clickable-cell ltr-number"
                                      onclick="redirectToClient({{ $client['sales_rep_id'] }}, {{ $client['client_id'] }})">
{{ $client['phone'] ? (str_starts_with($client['phone'], '+') ? $client['phone'] : '+' . $client['phone']) : '—' }}                                </span>
                                    </td>

                                    <!-- WhatsApp Link -->
                                    <td class="px-6 py-4 text-sm text-center">
                                        @if($client['whatsapp_link'])
                                            <a href="{{ $client['whatsapp_link'] }}" class="text-green-600 hover:underline" target="_blank">
                                                <i class="fab fa-whatsapp"></i> تواصل
                                            </a>
                                        @else
                                            —
                                        @endif
                                    </td>

                                    <!-- Interest Status -->
                                    <td class="px-6 py-4 text-sm text-center">
                                <span class="clickable-cell"
                                      onclick="redirectToClient({{ $client['sales_rep_id'] }}, {{ $client['client_id'] }})">
                                    <span class="status-badge status-{{ str_replace(' ', '-', $client['interest_status']) }}">
                                        @if($client['interest_status'] == 'interested')
                                            مهتم
                                        @elseif($client['interest_status'] == 'not interested')
                                            غير مهتم
                                        @else
                                            مؤجل
                                        @endif
                                    </span>
                                </span>
                                    </td>

                                    <!-- Interested Service -->
                                    <td class="px-6 py-4 text-sm font-medium text-blue-700" dir="rtl">
                                        {{ $client['interested_service'] ? $client['interested_service'] : '—' }}
                                    </td>

                                    <!-- Service Count -->
                                    <td class="px-6 py-4 text-sm font-medium text-gray-600" dir="rtl">
                                        @if($client['interested_service_count'] > 0)
                                            <span class="count-badge count-green">
                                    {{ $client['interested_service_count'] }}
                                </span>
                                        @else
                                            -
                                        @endif
                                    </td>

                                    <!-- Last Contact Date -->
                                    <td class="px-6 py-4 text-sm text-center {{ $client['is_late_customer'] ? 'text-red-600 font-bold' : 'text-green-600 font-bold' }}">
                                        {{ $client['last_contact_date'] ? \Carbon\Carbon::parse($client['last_contact_date'])->format('Y-m-d') : '—' }}
                                    </td>

                                    <!-- Days Left -->
                                    <td class="px-6 py-4 text-sm text-center {{ $client['is_late_customer'] ? 'text-red-600 font-bold' : 'text-green-600 font-bold' }}">
                                        @if($client['contact_days_left'])
                                            {{ $client['contact_days_left'] }} يوم
                                        @else
                                            —
                                        @endif
                                    </td>

                                    <!-- Contact Count -->
                                    <td class="px-6 py-4 text-sm text-center">
                                <span class="count-badge count-blue">
                                    {{ $client['contact_count'] ?? 0 }}
                                </span>
                                    </td>

                                    <!-- Requests Count -->
                                    <td class="px-6 py-4 text-sm text-center text-gray-400 cursor-pointer"
                                        onclick="window.location.href='/salesrep/' + {{ $client['sales_rep_id'] }} + '/MyRequests'">
                                <span class="count-badge count-purple">
                                    {{ $client['requests_count'] ?? 0 }}
                                </span>
                                    </td>

                                    <!-- Message Link -->
                                    <td class="px-6 py-4 text-sm text-center">
                                        <a href="/client/{{ $client['client_id'] }}/message" class="text-blue-600 hover:underline mr-2">
                                            <i class="fas fa-comments"></i>
                                        </a>
                                        @if($isAdmin)
                                            <button onclick="openClientEditModal({{ $client['client_id'] }})" class="text-green-600 hover:text-green-800" title="تعديل بيانات العميل">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-12">
                        <div class="mx-auto h-24 w-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-users text-gray-400 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">لا توجد عملاء</h3>
                        <p class="text-gray-500 mb-6">لم يتم العثور على عملاء تطابق معايير البحث.</p>
                        @if(request()->anyFilled(['search', 'interest_status', 'service_type', 'sales_rep', 'late_customer', 'from_date', 'to_date']))
                            <a href="{{ request()->url() }}"
                               class="inline-flex items-center px-4 py-2 bg-primary hover:bg-primary-light text-white rounded-lg font-medium transition-all duration-200">
                                عرض جميع العملاء
                            </a>
                        @endif
                    </div>
                @endif
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
                            <input type="text" id="columnsSearch" placeholder="بحث عن عمود..." onkeyup="filterColumns()">
                            <i class="fas fa-search"></i>
                        </div>

                        <div class="columns-list" id="columnsList">
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
                                    <span class="column-name">الشركة</span>
                                </label>
                            </div>
                            <div class="column-item">
                                <label class="column-checkbox">
                                    <input type="checkbox" value="address" checked>
                                    <span class="checkmark"></span>
                                    <span class="column-name">مقر الشركة</span>
                                </label>
                            </div>
                            <div class="column-item">
                                <label class="column-checkbox">
                                    <input type="checkbox" value="contact_person" checked>
                                    <span class="checkmark"></span>
                                    <span class="column-name">الشخص المسؤول</span>
                                </label>
                            </div>
                            <div class="column-item">
                                <label class="column-checkbox">
                                    <input type="checkbox" value="contact_position" checked>
                                    <span class="checkmark"></span>
                                    <span class="column-name">المنصب الوظيفي</span>
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
                                    <span class="column-name">حالة الاهتمام</span>
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
                                    <span class="column-name">تاریخ آخر تواصل</span>
                                </label>
                            </div>
                            <div class="column-item">
                                <label class="column-checkbox">
                                    <input type="checkbox" value="contact_days_left" checked>
                                    <span class="checkmark"></span>
                                    <span class="column-name">آخر تواصل منذ</span>
                                </label>
                            </div>
                            <div class="column-item">
                                <label class="column-checkbox">
                                    <input type="checkbox" value="contact_count" checked>
                                    <span class="checkmark"></span>
                                    <span class="column-name">عدد مرات التواصل</span>
                                </label>
                            </div>
                            <div class="column-item">
                                <label class="column-checkbox">
                                    <input type="checkbox" value="requests_count" checked>
                                    <span class="checkmark"></span>
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

            <!-- Client Edit Modal -->
            <div id="clientEditModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="bg-white rounded-lg shadow-lg w-full max-w-5xl mx-4 max-h-[90vh] overflow-hidden">
                    <!-- Modal Header -->
                    <div class="modal-header px-6 py-4 border-b bg-gray-50 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-800" id="modalClientName">تعديل بيانات العميل</h3>
                        <button class="text-gray-500 hover:text-gray-700 text-xl" onclick="closeClientEditModal()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <div class="modal-body px-6 py-4 overflow-y-auto max-h-[65vh]">
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
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <script>
        const isAdmin = @json($isAdmin);
        let ClientsData = @json($Clients);
        let currentFilteredClients = [...ClientsData];

        document.addEventListener('DOMContentLoaded', function() {
            // Export dropdown functionality
            const exportBtn = document.getElementById('exportBtn');
            const dropdown = document.getElementById('exportDropdown');

            if (exportBtn && dropdown) {
                exportBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function() {
                    dropdown.style.display = 'none';
                });

                // Handle export option clicks
                document.querySelectorAll('.export-dropdown-item').forEach(item => {
                    item.addEventListener('click', function() {
                        const exportType = this.getAttribute('data-type');
                        const selectedColumns = getSelectedColumns();

                        if (exportType === 'xlsx') {
                            exportClients('xlsx', selectedColumns);
                        } else if (exportType === 'pdf') {
                            exportToPDF(selectedColumns);
                        }

                        dropdown.style.display = 'none';
                    });
                });

                // Prevent dropdown from closing when clicking inside it
                dropdown.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            }

            updateColumnsBadge();
        });

        // Columns Modal Functions
        function openColumnsModal() {
            document.getElementById('columnsModal').style.display = 'flex';
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
            });

            updateColumnsBadge();
        }

        function resetSelection() {
            const checkboxes = document.querySelectorAll('.column-checkbox input');
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
            });

            updateColumnsBadge();
        }

        function applyColumnSelection() {
            const selectedColumns = getSelectedColumns();
            updateTableColumns(selectedColumns);
            updateColumnsBadge();
            closeColumnsModal();
        }

        function updateColumnsBadge() {
            const checkedCount = document.querySelectorAll('.column-checkbox input:checked').length;
            document.getElementById('columnsBadge').textContent = checkedCount;
        }

        function getSelectedColumns() {
            const checkboxes = document.querySelectorAll('.column-checkbox input:checked');
            return Array.from(checkboxes).map(checkbox => checkbox.value);
        }

        function updateTableColumns(selectedColumns) {
            const columnVisibility = {};
            selectedColumns.forEach(col => columnVisibility[col] = true);

            const headers = document.querySelectorAll('thead th');
            const columnKeys = Array.from(headers).map(header => getColumnKey(header.textContent.trim()));

            columnKeys.forEach((key, index) => {
                const shouldShow = columnVisibility[key] || index === 0 || index === headers.length - 1;
                headers[index].style.display = shouldShow ? '' : 'none';

                document.querySelectorAll('tbody tr').forEach(row => {
                    if (row.cells[index]) {
                        row.cells[index].style.display = shouldShow ? '' : 'none';
                    }
                });
            });
        }

        function getColumnKey(columnName) {
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
                'آخر تواصل': 'last_contact_date',
                'آخر تواصل منذ': 'contact_days_left',
                'عدد مرات التواصل': 'contact_count',
                'طلبات العميل': 'requests_count',
            };

            return columnMap[columnName] || columnName.toLowerCase().replace(/\s+/g, '_');
        }

        // Export Functions
        function exportClients(selectedColumns = null) {
            if (!selectedColumns || selectedColumns.length === 0) {
                selectedColumns = getSelectedColumns();
            }

            const columnsMap = {
                'client_logo': 'شعار شركة العميل',
                'company_name': 'الشركة',
                'address': 'مقر الشركة',
                'contact_person': 'الشخص المسؤول',
                'contact_position': 'المنصب الوظيفي',
                'phone': 'رقم الجوال',
                'whatsapp_link': 'واتس اب مباشر',
                'interest_status': 'حالة الاهتمام',
                'interested_service': 'الخدمة المهتم بها',
                'last_contact_date': 'آخر تواصل',
                'contact_count': 'عدد مرات التواصل',
                'contact_days_left': 'آخر تواصل منذ',
                'requests_count': 'طلبات العميل',
            };

            const headers = selectedColumns
                .filter(key => key !== 'client_logo')
                .map(key => columnsMap[key]);

            const data = ClientsData.map(client => {
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
            const table = document.querySelector('table').cloneNode(true);
            const pdfContainer = document.createElement('div');
            pdfContainer.style.padding = '20px';
            pdfContainer.appendChild(table);

            // Hide columns that are not selected
            const headers = table.querySelectorAll('thead th');
            const columnKeys = Array.from(headers).map(header => getColumnKey(header.textContent.trim()));

            columnKeys.forEach((key, index) => {
                if (!selectedColumns.includes(key)) {
                    headers[index].style.display = 'none';
                    table.querySelectorAll('tbody tr').forEach(row => {
                        if (row.cells[index]) {
                            row.cells[index].style.display = 'none';
                        }
                    });
                }
            });

            const options = {
                margin: 10,
                filename: `تقرير_عملاء_الشركة_${new Date().toISOString().slice(0,10)}.pdf`,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { scale: 2 },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
            };

            html2pdf().set(options).from(pdfContainer).save();
        }

        function formatDateForDisplay(date) {
            if (!date) return "";
            try {
                const d = new Date(date);
                return isNaN(d.getTime()) ? String(date) : d.toLocaleDateString('ar-EG');
            } catch (e) {
                return String(date);
            }
        }

        // Client Edit Functions
        function openClientEditModal(clientId) {
            const client = ClientsData.find(c => c.client_id == clientId);
            if (!client) {
                showNotification('لم يتم العثور على بيانات العميل', 'error');
                return;
            }

            document.getElementById('editClientId').value = client.client_id;
            document.getElementById('editCompanyName').value = client.company_name || '';
            document.getElementById('editClientLogo').src = client.company_logo || '';
            document.getElementById('editAddress').value = client.address || '';
            document.getElementById('editContactPerson').value = client.contact_person || '';
            document.getElementById('editContactPosition').value = client.contact_position || '';
            document.getElementById('editPhone').value = client.phone || '';
            document.getElementById('editInterestStatus').value = client.interest_status || 'neutral';
            document.getElementById('editInterestedService').value = client.interested_service || '';
            document.getElementById('editContactCount').value = client.contact_count || 0;
            document.getElementById('editServiceCount').value = client.interested_service_count || 0;
            document.getElementById('editLastContactDate').value = client.last_contact_date || '';
            document.getElementById('editSalesRep').value = client.sales_rep_name || '';

            document.getElementById('modalClientName').textContent = `تعديل بيانات ${client.company_name || 'العميل'}`;
            document.getElementById('clientEditModal').classList.remove('hidden');
        }

        function closeClientEditModal() {
            document.getElementById('clientEditModal').classList.add('hidden');
        }

        function saveClientEdits() {
            const formData = new FormData(document.getElementById('clientEditForm'));
            const clientId = formData.get('client_id');

            const data = {};
            for (let [key, value] of formData.entries()) {
                if (key !== 'client_id') {
                    data[key] = value;
                }
            }

            const saveBtn = document.querySelector('#clientEditModal .modal-footer button:last-child');
            const originalText = saveBtn.textContent;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...';
            saveBtn.disabled = true;

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
                        showNotification('تم تحديث بيانات العميل بنجاح', 'success');
                        closeClientEditModal();
                        // Reload the page to reflect changes
                        setTimeout(() => window.location.reload(), 1000);
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

        function redirectToClient(salesRepId, clientId) {
            window.location.href = `/sales-reps/${salesRepId}/clients/${clientId}`;
        }

        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            z-index: 10000;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        `;

            notification.style.backgroundColor = type === 'success' ? '#10b981' : '#ef4444';
            notification.textContent = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transition = 'opacity 0.5s';
                setTimeout(() => notification.remove(), 500);
            }, 3000);
        }

        // Close modals when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('columns-modal')) {
                closeColumnsModal();
            }
            if (e.target.id === 'clientEditModal') {
                closeClientEditModal();
            }
        });

        // Close modals with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeColumnsModal();
                closeClientEditModal();
            }
        });
    </script>
@endpush
