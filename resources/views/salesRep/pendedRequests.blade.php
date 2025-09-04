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
			<th class="text-center">تاريخ الاستجابة</th>
			<th class="text-center">مدة الاستجابة</th>
                        @if(auth()->user()->role == 'admin')
                        <th class="no-print">الإجراءات</th>
                        @endif

                    </tr>
                </thead>
                <tbody id="tableBody" class="text-center">
                    @foreach($clientRequests as $request)
                    <tr>

<td class="text-center">{{ $request->id }}</td>

<td style="font-size: 14px; font-weight: 700; white-space: nowrap;">
    <div class="flex items-center space-x-2">
{{--
<form action="{{ route('admin.client-request.delete', ['client_request' => $request->id]) }}"
      method="POST"
      onsubmit="return confirm('هل أنت متأكد من حذف هذا الطلب؟')">
    @csrf
    @method('DELETE')
    <button type="submit" class="text-red-600 hover:text-red-900">
        <i class="fas fa-trash-alt"></i>
    </button>
</form>
--}}

	  <a href="{{ route('sales-reps.client-requests.show', ['client' => $request->client_id, 'client_request' => $request->id]) }}"
           class="text-gray-800 hover:text-primary hover:underline">
            طلب تعديل عميل
        </a>

  </div>
</td>

                     <td class="text-center">
                            <a href="{{ route('sales-reps.clients.show', ['sales_rep' => $request->client->sales_rep_id, 'client' => $request->client_id]) }}"
                                class="text-blue-600 hover:underline">
                                {{ $request->client->company_name ?? 'عميل غير معروف' }}
                            </a>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('sales-reps.show', ['sales_rep' => $request->client->sales_rep_id]) }}"
                                class="hover:text-indigo-600 hover:underline" style="text-align:center;">
                                {{ $request->salesRep->name ?? 'مندوب غير معروف' }}
                            </a>
                        </td>
 <td class="text-center">
 @php
        $editedFieldsTranslation = [
            'company_name' => 'اسم الشركة',
    'logo' => 'الشعار',
    'address' => 'العنوان',
    'contact_person' => 'الشخص المسؤول',
    'interest_status' => 'حالة الاهتمام',
    'phone' => 'رقم الهاتف',
    'contact_position' => 'منصب المسؤول'
        ];
        echo $editedFieldsTranslation[$request->edited_field] ?? $request->edited_field;
    @endphp
</td>                        <td class="text-center">{{ $request->description }}</td>
<td class="text-center">
    <div class="text-gray-600 dark:text-gray-300">
        {{ $request->created_at->format('Y-m-d H:i') }}
    </div>

    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
        {{ $request->created_at->diffForHumans() }}
    </div>
</td>

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
                        <td class="no-print">
        <div class="flex space-x-2">
   @if ($request->status === 'pending')
                                            @php
                                                $updateRoute = '#';
                                                if ($request->request_type === 'client_data_change') {
                                                    $updateRoute = route('admin.client-request.update', [
                                                        'client' => $request->client_id,
                                                        'client_request' => $request->id,
                                                    ]);
                                                } elseif ($request->request_type === 'agreement_data_change') {
                                                    $updateRoute = route('admin.agreement-request.update', [
                                                        'agreement' => $request->agreement_id,
                                                        'agreement_request' => $request->id,
                                                    ]);
                                                }
                                            @endphp

                                            @if ($updateRoute !== '#')
                                                <form action="{{ $updateRoute }}" method="POST"
                                                    class="inline-flex gap-2">
                                                    @csrf
                                                    @method('PUT')

                                                    <button type="submit" name="status" value="approved"
                                                        class="px-3 py-1 bg-green-100 text-green-800 rounded-md text-sm hover:bg-green-200 transition-colors">
                                                         قبول
                                                    </button>

                                                    <button type="submit" name="status" value="rejected"
                                                        class="px-3 py-1 bg-red-100 text-red-800 rounded-md text-sm hover:bg-red-200 transition-colors">
                                                         رفض
                                                    </button>

                                      </form>
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
@foreach($chatClientRequests as $request)
<tr>
    <td class="text-center">{{ $request->id }}</td>

 <td style="font-size: 14px; font-weight: 700; white-space: nowrap;">
    <a href="{{ route('sales-reps.clientRequests.show', ['client' => $request->client_id, 'client_request' => $request->id]) }}"
       class="text-gray-800 hover:text-primary hover:underline">
 {{-- <form action="{{ route('admin.chat-client-request.destroy' , [ 'client' => $request->client_id,   'client_request' => $request->id  ]) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('هل أنت متأكد من رغبتك في حذف هذا الطلب؟')">
                    <i class="fas fa-trash-alt"></i> <!-- Font Awesome delete icon -->
                </button>
            </form>
--}}

طلب خاص بالعميل
    </a>
</td>
    <td class="text-center">
        <a href="{{ route('sales-reps.clients.show', ['sales_rep' => $request->client->sales_rep_id, 'client' => $request->client_id]) }}"
           class="text-blue-600 hover:underline">
            {{ $request->client->company_name ?? 'عميل غير معروف' }}
        </a>
    </td>
    <td class="text-center">
        <a href="{{ route('sales-reps.show', ['sales_rep' => $request->client->sales_rep_id]) }}"
           class="hover:text-indigo-600 hover:underline">
            {{ $request->salesRep->name ?? 'مندوب غير معروف' }}
        </a>
    </td>
    <td class="text-center">—</td>
    <td class="text-center">{{ $request->message }}</td>
<td class="text-center">
    <div class="text-gray-600 dark:text-gray-300">
        {{ $request->created_at->format('Y-m-d H:i') }}
    </div>

    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
        {{ $request->created_at->diffForHumans() }}
    </div>
</td>

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
@if(auth()->user()->role === 'admin')
    <td class="px-3 py-2 text-sm text-center no-print">
        <div class="flex space-x-2">
            @if ($request->status === 'pending')
                <form action="{{ route('admin.chat-client-request.update', [$request->client_id, $request->id]) }}"
                      method="POST" class="inline-flex gap-2">
                    @csrf
                    @method('PUT')

                    <button type="submit" name="status" value="approved"
                        class="px-3 py-1 bg-green-100 text-green-800 rounded-md text-sm hover:bg-green-200 transition-colors">
                         قبول
                    </button>

                    <button type="submit" name="status" value="rejected"
                        class="px-3 py-1 bg-red-100 text-red-800 rounded-md text-sm hover:bg-red-200 transition-colors">
                         رفض
                    </button>
                </form>
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

                    @foreach($agreementRequests as $request)
                    <tr>
    <td class="text-center">{{ $request->id }}</td>

<td style="font-size: 14px; font-weight: 700; display: flex; align-items: center; gap: 8px;">
    {{--
    <form action="{{ route('admin.agreement-request.destroy', ['agreement' => $request->agreement_id, 'agreement_request' => $request->id]) }}"
          method="POST"
          onsubmit="return confirm('هل أنت متأكد من حذف هذا الطلب؟');">
        @csrf
        @method('DELETE')
        <button type="submit" style="color: red; background: none; border: none; cursor: pointer;" title="حذف">
            <i class="fa fa-trash"></i>
        </button>
    </form>
--}}



    <a href="{{ route('admin.agreement-request.review', ['agreement' => $request->agreement_id, 'agreement_request' => $request->id]) }}"
       class="text-gray-800 hover:text-primary hover:underline">
        طلب تعديل اتفاقية
    </a>
</td>

                        <td class="text-center">
                            <a href="{{ route('salesrep.agreements.show', ['salesrep' => $request->sales_rep_id, 'agreement' => $request->agreement_id]) }}"
                                class="text-blue-600 hover:underline">
                                اتفاقية #{{ $request->agreement_id }}
                            </a>
                            <div class="text-sm text-gray-500">
                                <a href="{{ route('sales-reps.clients.show', ['sales_rep' => $request->sales_rep_id, 'client' => $request->client_id]) }}"
                                    class="hover:text-indigo-600 hover:underline">
                                    {{ $request->client->company_name ?? 'عميل غير معروف' }}
                                </a>
                            </div>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('sales-reps.show', ['sales_rep' => $request->sales_rep_id]) }}"
                                class="hover:text-indigo-600 hover:underline">
                                {{ $request->salesRep->name ?? 'مندوب غير معروف' }}
                            </a>
                        </td>
 <td class="text-center">
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
</td>                        <td class="text-center">{{ $request->description }}</td>
<td class="text-center">
    <div class="text-gray-600 dark:text-gray-300">
        {{ $request->created_at->format('Y-m-d H:i') }}
    </div>

    <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
        {{ $request->created_at->diffForHumans() }}
    </div>
</td>

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
                        <td class="no-print">
   <div class="flex space-x-2">
            @if ($request->status === 'pending')

                                            @php
                                                $updateRoute = '#';
                                                if ($request->request_type === 'client_data_change') {
                                                    $updateRoute = route('admin.client-request.update', [
                                                        'client' => $request->client_id,
                                                        'client_request' => $request->id,
                                                    ]);
                                                } elseif ($request->request_type === 'agreement_data_change') {
                                                    $updateRoute = route('admin.agreement-request.update', [
                                                        'agreement' => $request->agreement_id,
                                                        'agreement_request' => $request->id,
                                                    ]);
                                                }
                                            @endphp

                                            @if ($updateRoute !== '#')
                                                <form action="{{ $updateRoute }}" method="POST"
                                                    class="inline-flex gap-2">
                                                    @csrf
                                                    @method('PUT')

                                                    <button type="submit" name="status" value="approved"
                                                        class="px-3 py-1 bg-green-100 text-green-800 rounded-md text-sm hover:bg-green-200 transition-colors">
                                                         قبول
                                                    </button>

                                                    <button type="submit" name="status" value="rejected"
                                                        class="px-3 py-1 bg-red-100 text-red-800 rounded-md text-sm hover:bg-red-200 transition-colors">
                                                         رفض
                                                    </button>
                                                </form>
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
@if($clientRequests->isEmpty() && $agreementRequests->isEmpty() && $chatClientRequests->isEmpty())
            <tr>
                <td colspan="@if(auth()->user()->role == 'admin') 9 @else 8 @endif" class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <div class="empty-text">لا توجد طلبات معلقة في الوقت الحالي</div>
                </td>
            </tr>
            @endif                </tbody>
            </table>
            <div class="pdf-footer" style="display: none;">
                <p>جميع الحقوق محفوظة &copy; شركة آفاق الخليج {{ date('Y') }}</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Setup event listeners
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
                if (exportType === 'csv') {
                    exportRequests('csv');
                } else if (exportType === 'pdf') {
                    exportToPDF();
                }
                this.closest('.dropdown').classList.remove('active');
            });
        });

        // Prevent dropdown from closing when clicking inside it
        dropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });

function applyFilter() {
    const filterValue = document.getElementById('filterSelect').value;
    const rows = document.querySelectorAll('#tableBody tr');

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

        // Check request type filters
if (filterValue === 'client' || filterValue === 'agreement' || filterValue === 'client_chat') {
    const cellText = row.cells[0].textContent.trim();
    const isClientRequest = cellText.includes('طلب تعديل عميل');
    const isAgreementRequest = cellText.includes('طلب تعديل اتفاقية');
    const isClientChatRequest = cellText.includes('طلب خاص بالعميل');

    if (filterValue === 'client') {
        shouldShow = isClientRequest;
    } else if (filterValue === 'agreement') {
        shouldShow = isAgreementRequest;
    } else if (filterValue === 'client_chat') {
        shouldShow = isClientChatRequest;
    }
}        // Check status filters
        else {
            const statusSpan = row.cells[5].querySelector('span'); // Get the span inside the cell
            if (statusSpan) {
                const statusText = statusSpan.textContent.trim().toLowerCase();

                if (filterValue === 'approved' && statusText.includes('مقبول')) {
                    shouldShow = true;
                } else if (filterValue === 'rejected' && statusText.includes('مرفوض')) {
                    shouldShow = true;
                } else if (filterValue === 'pending' && statusText.includes('قيد الانتظار')) {
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
}    function approveRequest(type, id) {
        if (confirm('هل أنت متأكد من قبول هذا الطلب؟')) {
            fetch(`/api/sales-reps/${type}-edit-requests/${id}/approve`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'حدث خطأ أثناء معالجة الطلب');
                }
            })
            .catch(error => {
                alert('حدث خطأ في الشبكة');
            });
        }
    }

    function rejectRequest(type, id) {
        const notes = prompt('الرجاء إدخال ملاحظات حول سبب الرفض:');
        if (notes !== null) {
            fetch(`/api/sales-reps/${type}-edit-requests/${id}/reject`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ notes })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'حدث خطأ أثناء معالجة الطلب');
                }
            })
            .catch(error => {
                alert('حدث خطأ في الشبكة');
            });
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
