@extends('layouts.master')

@section('title', 'مراجعة طلب تعديل الاتفاقية')

@section('content')
<x-page-header title="مراجعة طلب تعديل الاتفاقية" subtitle="تفاصيل الطلب ومقارنة القيم">
    <x-slot name="actions">
        <a href="{{ route('admin.client-request.edit', ['client' => $agreement_request->client_id, 'client_request' => $agreement_request->id]) }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-indigo-50 text-indigo-700 text-sm font-semibold hover:bg-indigo-100 transition-colors"
           title="عرض تفاصيل العميل">
            <i class="bi bi-person-lines-fill"></i> العميل
        </a>
        @if(auth()->user()->role === 'admin' && $agreement_request->status === 'pending')
            <a href="{{ route('admin.agreement-request.edit', ['agreement' => $agreement_request->agreement->id, 'agreement_request' => $agreement_request->id]) }}"
               class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition-colors">
                <i class="bi bi-pencil-square"></i> تعديل الطلب
            </a>
        @endif
    </x-slot>
</x-page-header>

<div class="max-w-4xl bg-white border border-gray-200 rounded-xl shadow-sm p-6 space-y-6">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-gray-50 rounded-lg p-4">
            <h5 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2"><i class="bi bi-building text-indigo-500"></i>معلومات العميل</h5>
            <div class="flex items-center gap-2 mb-2">
                <x-badge color="indigo">العميل</x-badge>
                <span class="text-sm font-medium text-gray-800">{{ $agreement_request->client->company_name }}</span>
            </div>
            <div class="flex items-center gap-2">
                <x-badge color="indigo">مندوب المبيعات</x-badge>
                <span class="text-sm font-medium text-gray-800">{{ $agreement_request->salesRep->name }}</span>
            </div>
        </div>

        <div class="bg-gray-50 rounded-lg p-4">
            <h5 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2"><i class="bi bi-file-text text-indigo-500"></i>تفاصيل الاتفاقية</h5>
            <div class="flex items-center gap-2 mb-2">
                <x-badge color="indigo">رقم الاتفاقية</x-badge>
                <span class="text-sm font-medium text-gray-800">#{{ $agreement_request->agreement->id }}</span>
            </div>
            @php
                $fields = [
                    'service_type' => 'نوع الخدمة',
                    'signing_date' => 'تاريخ التوقيع',
                    'duration_years' => 'مدة السنوات',
                    'termination_type' => 'نوع الإنهاء',
                    'notice_months' => 'شهور الإخطار',
                    'notice_status' => 'حالة الإخطار',
                    'product_quantity' => 'كمية المنتج',
                    'price' => 'السعر',
                    'agreement_status' => 'حالة الاتفاقية',
                    'implementation_date' => 'تاريخ التنفيذ',
                ];

                $translatedLabel = $fields[$editedFieldLabel ?? ''] ?? $editedFieldLabel;

                $payload = $agreement_request->payload ?? [];
                $oldValue = $payload['old_value'] ?? null;
                $newValue = $payload['new_value'] ?? null;

                if (($agreement_request->edited_field ?? null) === 'agreement_status') {
                    $statusTranslations = [
                        'active' => 'سارية',
                        'terminated' => 'منتهية',
                        'pending' => 'قيد الانتظار',
                    ];
                    $oldValue = $statusTranslations[$oldValue] ?? $oldValue;
                    $newValue = $statusTranslations[$newValue] ?? $newValue;
                }

                if (($agreement_request->edited_field ?? null) === 'notice_status') {
                    $noticeTranslations = [
                        'sent' => 'تم الإرسال',
                        'not_sent' => 'لم يتم الإرسال',
                    ];
                    $oldValue = $noticeTranslations[$oldValue] ?? $oldValue;
                    $newValue = $noticeTranslations[$newValue] ?? $newValue;
                }

                $newValue = $newValue ?: 'لم يتم التعديل بعد';
            @endphp

            <div class="flex items-center gap-2">
                <x-badge color="indigo">الحقل المعدل</x-badge>
                <span class="text-sm font-medium text-gray-800">{{ $translatedLabel }}</span>
            </div>
        </div>
    </div>

    <!-- Values Comparison -->
    <div class="bg-gray-50 rounded-lg p-4 border-s-4 border-indigo-500">
        <h5 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2"><i class="bi bi-arrow-left-right text-indigo-500"></i>مقارنة القيم</h5>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white p-3 rounded-lg border-2 border-rose-200">
                <div class="flex items-center gap-2 mb-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-rose-500 text-white text-xs"><i class="bi bi-arrow-left"></i></span>
                    <h6 class="text-sm font-bold text-rose-700">القيمة القديمة</h6>
                </div>
                <div class="min-h-[48px] flex items-center bg-gray-50 rounded-lg px-3 py-2">
                    <p class="text-sm font-medium text-gray-800">{{ $oldValue ?? 'غير متوفرة' }}</p>
                </div>
            </div>

            <div class="bg-white p-3 rounded-lg border-2 {{ $newValue === 'لم يتم التعديل بعد' ? 'border-gray-200' : 'border-emerald-200' }}">
                <div class="flex items-center gap-2 mb-2">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full {{ $newValue === 'لم يتم التعديل بعد' ? 'bg-gray-400' : 'bg-emerald-500' }} text-white text-xs"><i class="bi bi-arrow-right"></i></span>
                    <h6 class="text-sm font-bold {{ $newValue === 'لم يتم التعديل بعد' ? 'text-gray-500' : 'text-emerald-700' }}">القيمة الجديدة</h6>
                </div>
                <div class="min-h-[48px] flex items-center bg-gray-50 rounded-lg px-3 py-2">
                    <p class="text-sm font-medium text-gray-800">{{ $newValue }}</p>
                </div>
            </div>
        </div>

        @if($oldValue && $newValue !== 'لم يتم التعديل بعد')
            <div class="text-center mt-4">
                <span class="inline-flex items-center gap-1.5 bg-indigo-600 text-white px-4 py-2 rounded-full text-sm font-medium">
                    <i class="bi bi-arrow-repeat"></i> تم طلب تغيير القيمة
                </span>
            </div>
        @endif
    </div>

    <!-- Request Description -->
    <div class="bg-gray-50 rounded-lg p-4">
        <h5 class="text-sm font-semibold text-gray-900 mb-2 flex items-center gap-2"><i class="bi bi-chat-square-text text-indigo-500"></i>وصف التعديل</h5>
        <div class="bg-white rounded-lg border border-gray-200 p-3 text-sm text-gray-700" style="white-space: pre-wrap;">
            {{ $agreement_request->description ?? 'لم يتم تقديم وصف' }}
        </div>
    </div>

    <!-- Admin Notes -->
    <div class="bg-gray-50 rounded-lg p-4">
        <h5 class="text-sm font-semibold text-gray-900 mb-2 flex items-center gap-2"><i class="bi bi-chat-square-text text-indigo-500"></i>ملاحظات الإدارة</h5>
        <div class="bg-white rounded-lg border border-gray-200 p-3 text-sm text-gray-700" style="white-space: pre-wrap;">
            {{ $agreement_request->notes ?? 'لم يتم وضع ملاحظات إدارية' }}
        </div>
    </div>

    <!-- Status and Dates -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-gray-50 rounded-lg p-4">
            <h5 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2"><i class="bi bi-info-circle text-indigo-500"></i>حالة الطلب</h5>
            @php
                $statusColors = [
                    'pending' => 'amber',
                    'approved' => 'emerald',
                    'rejected' => 'rose',
                ];
                $statusTranslations = [
                    'pending' => 'قيد الانتظار',
                    'approved' => 'تمت الموافقة',
                    'rejected' => 'مرفوض',
                ];
                $status = $agreement_request->status;
                $badgeColor = $statusColors[$status] ?? 'gray';
                $translatedStatus = $statusTranslations[$status] ?? $status;
            @endphp
            <x-badge :color="$badgeColor" class="text-sm px-3 py-1.5">{{ $translatedStatus }}</x-badge>
        </div>

        <div class="bg-gray-50 rounded-lg p-4">
            <h5 class="text-sm font-semibold text-gray-900 mb-3 flex items-center gap-2"><i class="bi bi-calendar-event text-indigo-500"></i>التواريخ</h5>
            <div class="mb-2">
                <span class="text-xs text-gray-400">تاريخ الإنشاء:</span>
                <div class="text-sm font-medium text-gray-800">{{ $agreement_request->created_at->format('Y-m-d H:i') }}</div>
            </div>
            <div>
                <span class="text-xs text-gray-400">تاريخ الرد:</span>
                <div class="text-sm font-medium text-gray-800">
                    {{ $agreement_request->response_date ? $agreement_request->response_date->format('Y-m-d H:i') : 'لم يتم الرد بعد' }}
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-between pt-2">
        <a href="{{ url()->previous() }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 text-sm font-semibold hover:bg-gray-50 transition-colors">
            <i class="bi bi-arrow-left"></i> رجوع
        </a>
    </div>
</div>
@endsection
