@extends('layouts.master')
@section('title', 'الطلبات ')
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
            z-index: 1000;
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
            transition: all 0.2s;
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

        /* Ensure dropdown stays above other elements */
        .export-options {
            position: relative;
            z-index: 1000;
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

        .table-container
        data-table th,
        data-table td {
            text-align: center;
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
            font-size: 14px;
            font-weight: 800;
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

        .title @media (max-width: 768px) {
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

@section('content')
    <div id="print-area" class="table-container">
        <div class="table-header">
            <h2 id="title" class="table-title">الطلبات</h2>
            <div class="table-actions d-flex align-items-center gap-2">
                @isset($salesRep)
                    <div class="bg-indigo-100 text-indigo-800 px-4 py-2 rounded-full" style="font-size:14px; font-weight:800;">
                        سفير العلامة التجارية: {{ $salesRep->name }}
                    </div>
                @endisset
                <div class="export-options">
                    <div class="dropdown">
                        <button class="btn btn-dropdown" id="exportBtn" type="button">
                            تصدير البيانات
                            <svg width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                    <select id="filterSelect" onchange="applyFilter()" class="form-select w-auto" style="font-size: 14px;font-weight: 800;">
                        <option value="">الكل</option>
                        <option value="client">طلبات تعديل  العملاء</option>
                        <option value="agreement">طلبات تعديل  الاتفاقيات</option>
                        <option value="client_chat">طلبات العملاء </option>
                        <option value="approved">مقبولة</option>
                        <option value="rejected">مرفوضة</option>
                        <option value="pending">معلقة</option>
                    </select>
                </div>
            </div>    </div>

        <div class="table-responsive">
            <div class="pdf-content">
                <div class="pdf-header" style="display: none;">
                    <div
                        class="header-content d-flex align-items-center justify-content-between flex-wrap mb-4 p-3 shadow rounded bg-white">
                        <div class="d-flex umn align-items-center text-center mx-auto">
                            <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="header-logo mb-2" />
                            <h2 class="header-text">تقرير الطلبات المعلقة</h2>
                        </div>
                    </div>
                </div>
                <table class="data-table text-center">
                    <thead>
                    <tr>
                        <th class="text-center">ID</th>

                        <th class="text-center">نوع الطلب</th>
                        <th class="text-center">الجهة</th>
                        <th class="text-center">سفير العلامة التجارية</th>
                        <th class="text-center">الحقل المعدل</th>
                        <th class="text-center">الوصف</th>
                        <th class="text-center">تاريخ الطلب</th>
                        @if(auth()->user()->role == 'salesRep')
                            <th class="no-print">حالة الطلب</th>
                        @endif
                        <th class="text-center">تاريخ الاستجابة</th>
                        <th class="text-center">مدة الاستجابة</th>
                        @if(auth()->user()->role == 'admin')
                            <th class="no-print">الإجراءات</th>
                        @endif

                    </tr>
                    </thead>
                    <tbody id="tableBody" class="text-center">
                    @php
                        // Combine all requests into one collection and sort by creation date
                        $allRequests = $clientRequests->concat($agreementRequests)->concat($chatClientRequests)
                            ->sortByDesc('created_at');
                    @endphp

                    @foreach($allRequests as $request)
                        <tr>
                            <td class="text-center">{{ $request->id }}</td>

                            <td style="font-size: 14px; font-weight: 700; white-space: nowrap;">
                                <div class="flex items-center space-x-2">
                                    @if(get_class($request) === 'App\Models\ClientEditRequest')
                                        <a href="{{ route('sales-reps.client-requests.show', ['client' => $request->client_id, 'client_request' => $request->id]) }}"
                                           class="text-gray-800 hover:text-primary hover:underline">
                                            طلب تعديل عميل
                                        </a>
                                    @elseif(get_class($request) === 'App\Models\AgreementEditRequest')
                                        <a href="{{ route('admin.agreement-request.review', ['agreement' => $request->agreement_id, 'agreement_request' => $request->id]) }}"
                                           class="text-gray-800 hover:text-primary hover:underline">
                                            طلب تعديل اتفاقية
                                        </a>
                                    @else
                                        <a href="{{ route('sales-reps.clientRequests.show', ['client' => $request->client_id, 'client_request' => $request->id]) }}"
                                           class="text-gray-800 hover:text-primary hover:underline">
                                            طلب خاص بالعميل
                                        </a>
                                    @endif
                                </div>
                            </td>

                            <td class="text-center">
                                @if(isset($request->client))
                                    @if(isset($request->client))
                                        <a href="{{ route('sales-reps.clients.show', ['sales_rep' => $request->client->sales_rep_id, 'client' => $request->client_id]) }}"
                                           class="text-blue-600 hover:underline flex items-center gap-2">
                                            {{ $request->client->company_name ?? 'عميل غير معروف' }}

                                            @if($request->client->clientEditRequests->count() > 0)
                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                {{ $request->client->clientEditRequests->count() }}
            </span>
                                            @endif
                                        </a>
                                    @elseif(isset($request->agreement) && $request->agreement->client)
                                        <a href="{{ route('sales-reps.clients.show', ['sales_rep' => $request->sales_rep_id, 'client' => $request->agreement->client_id]) }}"
                                           class="text-blue-600 hover:underline flex items-center gap-2">
                                            {{ $request->agreement->client->company_name ?? 'عميل غير معروف' }}

                                            @if($request->agreement->editRequests->count() > 0)
                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                {{ $request->agreement->editRequests->count() }}
            </span>
                                            @endif
                                        </a>
                                    @endif

                                @else
                                    —
                                @endif
                            </td>

                            <td class="text-center">
                                @if(isset($request->salesRep))
                                    <a href="{{ route('sales-reps.show', ['sales_rep' => $request->salesRep->id]) }}"
                                       class="hover:text-indigo-600 hover:underline">
                                        {{ $request->salesRep->name ?? 'مندوب غير معروف' }}
                                    </a>
                                @else
                                    مندوب غير معروف
                                @endif
                            </td>

                            <td class="text-center">
                                @if(get_class($request) === 'App\Models\ClientEditRequest')
                                    @php
                                        $editedFieldsTranslation = [
                                            'company_name' => 'اسم الشركة',
                                            'logo' => 'الشعار',
                                            'address' => 'العنوان',
                                            'last_contact_date' => 'تاريخ اخر تواصل',
                                            'contact_person' => 'الشخص المسؤول',
                                            'interest_status' => 'حالة الاهتمام',
                                            'phone' => 'رقم الهاتف',
                                            'contact_position' => 'منصب المسؤول',
                                               'interested_service' => 'الخدمة المهتم بها',
                                            'interested_service_count' => 'عدد الخدمة المهتم بها',
                                        ];
                                        echo $editedFieldsTranslation[$request->edited_field] ?? $request->edited_field;
                                    @endphp
                                @elseif(get_class($request) === 'App\Models\AgreementEditRequest')
                                    @php
                                        $editedFieldsTranslation = [
                                            'service_id' => 'الخدمة',
                                            'signing_date' => 'تاريخ التوقيع',
                                            'duration_years' => 'مدة العقد (بالسنوات)',
                                            'end_date' => 'تاريخ الانتهاء',
                                            'termination_type' => 'نوع الإنهاء',
                                            'notice_months' => 'مدة الإشعار (بالأشهر)',
                                            'notice_status' => 'حالة الإشعار',
                                            'product_quantity' => 'كمية المنتج',
                                            'price' => 'السعر',
                                            'agreement_status' => 'حالة الاتفاقية',
                                            'implementation_date' => 'تاريخ التنفيذ',
                                        ];
                                        echo $editedFieldsTranslation[$request->edited_field] ?? $request->edited_field;
                                    @endphp
                                @else
                                    —
                                @endif
                            </td>

                            <td class="text-center">
                                {{ $request->description ?? $request->message }}
                            </td>

                            <td class="text-center">
                                <div class="text-gray-600 dark:text-gray-300">
                                    {{ $request->created_at->format('Y-m-d H:i') }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    {{ $request->created_at->diffForHumans() }}
                                </div>
                            </td>
                            @if(auth()->user()->role == 'salesRep')
                                <td class="text-center">
                                    @if ($request->status === 'approved')
                                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-md text-sm">
            مقبول
        </span>
                                    @elseif ($request->status === 'rejected')
                                        <span class="px-3 py-1 bg-red-100 text-red-800 rounded-md text-sm">
            مرفوض
        </span>
                                    @elseif ($request->status === 'pended')
                                        <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-md text-sm">
            معلق
        </span>
                                    @endif
                                </td>
                            @endif


                            <td class="text-center">
                                @if($request->status === 'pending')
                                    <div class="text-yellow-600 dark:text-yellow-400">
                                        لم تتم استجابة الطلب بعد
                                    </div>
                                @else
                                    <div class="text-gray-600 dark:text-gray-300">
                                        {{ $request->updated_at->format('Y-m-d H:i') }}
                                    </div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $request->updated_at->diffForHumans() }}
                                    </div>
                                @endif
                            </td>

                            <td class="text-center">
                                @if($request->status === 'pending')
                                    لم تتم استجابة الطلب بعد
                                @else
                                    @php
                                        $diff = $request->created_at->diff($request->updated_at);
                                        $hours = $diff->h;
                                        $minutes = $diff->i;
                                        $days = $diff->d;
                                    @endphp

                                    @if($days > 0)
                                        {{ $days }} @if($days > 10) يومًا @elseif($days > 2) أيام @elseif($days == 2) يومان @else يوم @endif
                                    @endif

                                    @if($hours > 0)
                                        {{ $hours }} @if($hours > 10) ساعة @elseif($hours > 2) ساعات @elseif($hours == 2) ساعتان @else ساعة @endif
                                    @endif

                                    @if($minutes > 0)
                                        {{ $minutes }} @if($minutes > 10) دقيقة @elseif($minutes > 2) دقائق @elseif($minutes == 2) دقيقتان @else دقيقة @endif
                                    @endif
                                @endif
                            </td>

                            @if(auth()->user()->role == 'admin')
                                <td class="no-print" id="request-actions-{{ $request->id }}">
                                    <div class="flex space-x-2">
                                        @if ($request->status === 'pending')
                                            @php
                                                $updateRoute = '#';
                                                if (get_class($request) === 'App\Models\ClientEditRequest') {
                                                    $updateRoute = route('admin.client-request.update', [
                                                        'client' => $request->client_id,
                                                        'client_request' => $request->id,
                                                    ]);
                                                } elseif (get_class($request) === 'App\Models\AgreementEditRequest') {
                                                    $updateRoute = route('admin.agreement-request.update', [
                                                        'agreement' => $request->agreement_id,
                                                        'agreement_request' => $request->id,
                                                    ]);
                                                } elseif (get_class($request) === 'App\Models\ClientRequest') {
                                                    $updateRoute = route('admin.chat-client-request.update', [
                                                        'client' => $request->client_id,
                                                        'client_request' => $request->id
                                                    ]);
                                                }
                                            @endphp

                                            @if ($updateRoute !== '#')
                                                <div class="request-actions">
                                                    <form action="{{ $updateRoute }}" method="POST" class="inline-flex gap-2 ajax-update-form" data-request-id="{{ $request->id }}">
                                                        @csrf
                                                        @method('PUT')

                                                        <button type="button" data-status="approved"
                                                                class="update-status px-3 py-1 bg-green-100 text-green-800 rounded-md text-sm hover:bg-green-200 transition-colors">
                                                            قبول
                                                        </button>

                                                        <button type="button" data-status="rejected"
                                                                class="update-status px-3 py-1 bg-red-100 text-red-800 rounded-md text-sm hover:bg-red-200 transition-colors">
                                                            رفض
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        @else
                                            @if ($request->status === 'approved')
                                                <span class="px-3 py-1 bg-green-100 text-green-800 rounded-md text-sm">
                        مقبول
                    </span>
                                            @elseif ($request->status === 'rejected')
                                                <span class="px-3 py-1 bg-red-100 text-red-800 rounded-md text-sm">
                        مرفوض
                    </span>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach

                    @if($allRequests->isEmpty())
                        <tr>
                            <td colspan="@if(auth()->user()->role == 'admin') 10 @else 9 @endif" class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-inbox"></i>
                                </div>
                                <div class="empty-text">لا توجد طلبات في الوقت الحالي</div>
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
                <div class="pdf-footer" style="display: none;">
                    <p>جميع الحقوق محفوظة &copy; شركة آفاق الخليج {{ date('Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Setup event listeners for status update buttons
                document.querySelectorAll('.update-status').forEach(button => {
                    button.addEventListener('click', function() {
                        const form = this.closest('form');
                        const requestId = form.getAttribute('data-request-id');
                        const url = form.getAttribute('action');
                        const status = this.getAttribute('data-status');
                        const container = document.getElementById(`request-actions-${requestId}`);

                        console.log('=== DEBUG AJAX REQUEST ===');
                        console.log('Request ID:', requestId);
                        console.log('URL:', url);
                        console.log('Status:', status);
                        console.log('CSRF Token:', form.querySelector('input[name="_token"]').value);

                        // Disable buttons during request
                        const buttons = form.querySelectorAll('button');
                        buttons.forEach(btn => {
                            btn.disabled = true;
                            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
                        });

                        fetch(url, {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": form.querySelector('input[name="_token"]').value,
                                "Content-Type": "application/json",
                                "X-Requested-With": "XMLHttpRequest",
                                "Accept": "application/json"
                            },
                            body: JSON.stringify({
                                _method: "PUT",
                                status: status
                            })
                        })
                            .then(response => {
                                console.log('Response Status:', response.status);
                                console.log('Response OK:', response.ok);

                                // Check if response is JSON
                                const contentType = response.headers.get('content-type');
                                console.log('Content-Type:', contentType);

                                if (!response.ok) {
                                    throw new Error(`HTTP error! status: ${response.status}`);
                                }

                                if (contentType && contentType.includes('application/json')) {
                                    return response.json();
                                } else {
                                    // If it's not JSON, it might be a redirect HTML
                                    return response.text().then(text => {
                                        console.log('Non-JSON Response:', text.substring(0, 200));
                                        throw new Error('Server returned HTML instead of JSON. Check your controller.');
                                    });
                                }
                            })
                            .then(data => {
                                console.log('Parsed Data:', data);

                                if (data.success) {
                                    // Update the UI with the new status
                                    let statusText = '';
                                    let statusClass = '';

                                    if (status === "approved") {
                                        statusText = 'مقبول';
                                        statusClass = 'bg-green-100 text-green-800';
                                    } else {
                                        statusText = 'مرفوض';
                                        statusClass = 'bg-red-100 text-red-800';
                                    }

                                    container.innerHTML = `
                            <span class="px-3 py-1 ${statusClass} rounded-md text-sm">
                                ${statusText}
                            </span>
                        `;

                                    // Show success notification
                                    showNotification(`تم ${status === 'approved' ? 'قبول' : 'رفض'} الطلب بنجاح`, 'success');
                                } else {
                                    throw new Error(data.message || 'Failed to update status');
                                }
                            })
                            .catch(error => {
                                console.error('Full Error Details:', error);
                                console.error('Error Name:', error.name);
                                console.error('Error Message:', error.message);

                                // Re-enable buttons
                                buttons.forEach(btn => {
                                    btn.disabled = false;
                                    btn.innerHTML = btn.getAttribute('data-status') === 'approved' ? 'قبول' : 'رفض';
                                });

                                // Show detailed error notification
                                showNotification(`حدث خطأ أثناء تحديث حالة الطلب: ${error.message}`, 'error');
                            });
                    });
                });

                // Add notification function
                function showNotification(message, type) {
                    // Remove existing notifications
                    const existingNotifications = document.querySelectorAll('.custom-notification');
                    existingNotifications.forEach(notification => notification.remove());

                    // Create notification element
                    const notification = document.createElement('div');
                    notification.className = `custom-notification fixed top-4 right-4 z-50 px-6 py-3 rounded-lg shadow-lg transition-all duration-300 ${
                        type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
                    }`;
                    notification.textContent = message;
                    notification.style.fontWeight = '600';
                    notification.style.transform = 'translateX(100%)';
                    notification.style.opacity = '0';

                    // Add to page
                    document.body.appendChild(notification);

                    // Animate in
                    setTimeout(() => {
                        notification.style.transform = 'translateX(0)';
                        notification.style.opacity = '1';
                    }, 100);

                    // Remove after 5 seconds (longer for debugging)
                    setTimeout(() => {
                        notification.style.transform = 'translateX(100%)';
                        notification.style.opacity = '0';
                        setTimeout(() => {
                            if (notification.parentNode) {
                                notification.parentNode.removeChild(notification);
                            }
                        }, 300);
                    }, 5000);
                }

                // Your existing export and filter code...
                document.getElementById('searchInput').addEventListener('input', function(e) {
                    const searchTerm = e.target.value.toLowerCase();
                    const rows = document.querySelectorAll('#tableBody tr');

                    rows.forEach(row => {
                        const rowText = row.textContent.toLowerCase();
                        if (rowText.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                });

                // Export dropdown functionality
                const exportBtn = document.getElementById('exportBtn');
                const dropdown = document.getElementById('exportDropdown');

                if (exportBtn && dropdown) {
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
                                exportRequests('csv');
                            } else if (exportType === 'pdf') {
                                exportToPDF();
                            }
                            this.closest('.dropdown').classList.remove('active');
                        });
                    });

                    dropdown.addEventListener('click', function(e) {
                        e.stopPropagation();
                    });
                }
            });

            function applyFilter() {
                const filterValue = document.getElementById('filterSelect').value;
                const rows = document.querySelectorAll('#tableBody tr');

                // Determine user role and set correct column indices
                const isAdmin = {{ auth()->user()->role == 'admin' ? 'true' : 'false' }};
                const statusColumnIndex = isAdmin ? 9 : 7; // Status column index

                rows.forEach(row => {
                    if (row.classList.contains('empty-state')) {
                        row.style.display = 'none';
                        return;
                    }

                    if (!filterValue) {
                        row.style.display = '';
                        return;
                    }

                    let shouldShow = false;
                    const requestTypeCell = row.cells[1]; // Type cell (index 1)

                    // Check request type filters
                    if (['client', 'agreement', 'client_chat'].includes(filterValue)) {
                        const cellText = requestTypeCell.textContent.trim();

                        if (filterValue === 'client' && cellText.includes('تعديل عميل')) {
                            shouldShow = true;
                        } else if (filterValue === 'agreement' && cellText.includes('تعديل اتفاقية')) {
                            shouldShow = true;
                        } else if (filterValue === 'client_chat' && cellText.includes('خاص بالعميل')) {
                            shouldShow = true;
                        }
                    }
                    // Check status filters
                    else if (['approved', 'rejected', 'pending'].includes(filterValue)) {
                        // For admin users, check the action buttons cell
                        if (isAdmin) {
                            const actionCell = row.cells[statusColumnIndex];
                            const actionButtons = actionCell.querySelectorAll('button, span');

                            if (filterValue === 'pending') {
                                // If there are action buttons, it's pending
                                shouldShow = actionButtons.length > 0;

                            } else if (filterValue === 'approved') {
                                // Look for approved status text
                                shouldShow = Array.from(actionButtons).some(btn =>
                                    btn.textContent.includes('مقبول')
                                );
                            } else if (filterValue === 'rejected') {
                                // Look for rejected status text
                                shouldShow = Array.from(actionButtons).some(btn =>
                                    btn.textContent.includes('مرفوض')
                                );
                            }
                        }
                        // For salesRep users, check the status text directly
                        else {
                            const statusCell = row.cells[statusColumnIndex];
                            const statusText = statusCell.textContent.trim().toLowerCase();

                            if (filterValue === 'approved' && statusText.includes('مقبول')) {
                                shouldShow = true;
                            } else if (filterValue === 'rejected' && statusText.includes('مرفوض')) {
                                shouldShow = true;
                            } else if (filterValue === 'pending' && statusText.includes('معلق')) {
                                shouldShow = true;
                            }
                        }
                    }

                    row.style.display = shouldShow ? '' : 'none';
                });

                // Handle empty state visibility
                const visibleRows = Array.from(rows).filter(row =>
                    !row.classList.contains('empty-state') && row.style.display !== 'none'
                );
                const emptyState = document.querySelector('.empty-state');
                if (emptyState) {
                    emptyState.style.display = visibleRows.length === 0 ? '' : 'none';
                }
            }

            function exportRequests(type = 'csv') {
                // Get all visible rows (after filtering)
                const rows = Array.from(document.querySelectorAll('#tableBody tr'))
                    .filter(row => row.style.display !== 'none');

                if (rows.length === 0) {
                    alert('لا توجد بيانات للتصدير');
                    return;
                }

                // Prepare headers
                const headers = [
                    'نوع الطلب',
                    'الجهة',
                    'سفير العلامة التجارية',
                    'الحقل المعدل',
                    'الوصف',
                    'تاريخ الطلب'
                ];

                // Prepare data
                const data = rows.map(row => {
                    return {
                        type: row.cells[0].textContent.trim(),
                        entity: row.cells[1].textContent.trim(),
                        rep: row.cells[2].textContent.trim(),
                        field: row.cells[3].textContent.trim(),
                        description: row.cells[4].textContent.trim(),
                        date: row.cells[6].textContent.trim()
                    };
                });

                if (type === 'csv') {
                    // Convert to CSV
                    let csvContent = '\uFEFF'; // BOM for UTF-8
                    csvContent += headers.join(',') + '\r\n';

                    data.forEach(item => {
                        const row = [
                            `"${item.type}"`,
                            `"${item.entity}"`,
                            `"${item.rep}"`,
                            `"${item.field}"`,
                            `"${item.description}"`,
                            `"${item.date}"`
                        ];
                        csvContent += row.join(',') + '\r\n';
                    });

                    // Create download link
                    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', `الطلبات_المعلقة_${new Date().toISOString().slice(0,10)}.csv`);
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    URL.revokeObjectURL(url);
                }
            }

            function exportToPDF() {
                // Clone the table to modify for PDF export
                const originalTable = document.querySelector('.data-table');
                const table = originalTable.cloneNode(true);
                const pdfHeader = document.querySelector('.pdf-header').cloneNode(true);
                const pdfFooter = document.querySelector('.pdf-footer').cloneNode(true);

                // Show header and footer
                pdfHeader.style.display = 'block';
                pdfFooter.style.display = 'block';

                // Create a container for the PDF content
                const pdfContainer = document.createElement('div');
                pdfContainer.style.padding = '20px';
                pdfContainer.appendChild(pdfHeader);
                pdfContainer.appendChild(table);
                pdfContainer.appendChild(pdfFooter);

                // Hide action column
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

                // Options for html2pdf
                const options = {
                    margin: 10,
                    filename: `تقرير_الطلبات_المعلقة_${new Date().toISOString().slice(0,10)}.pdf`,
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
                html2pdf().set(options).from(pdfContainer).save();
            }
        </script>
    @endpush
@endsection
