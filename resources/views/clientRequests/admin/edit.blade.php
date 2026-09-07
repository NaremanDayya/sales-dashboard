@extends('layouts.master')

@section('title', 'Review Client Edit Request')

@section('content')
@php
    $status = $client_request->status;
    $statusColor = match($status) { 'pending' => 'amber', 'approved' => 'emerald', 'rejected' => 'rose', default => 'gray' };
    $statusText = match($status) {
        'pending' => 'قيد المراجعة',
        'approved' => 'تمت الموافقة',
        'rejected' => 'مرفوض',
        default => 'غير معروف',
    };
@endphp
<x-page-header title="مراجعة طلب تعديل العميل" :subtitle="'طلب تعديل العميل ' . $client_request->client->company_name">
    <x-slot name="actions">
        <x-badge :color="$statusColor">{{ $statusText }}</x-badge>
    </x-slot>
</x-page-header>

<div class="max-w-4xl space-y-6">
    <!-- Request Summary Card -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h5 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                <i class="bi bi-building text-indigo-500"></i>{{ $client->company_name }}
            </h5>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600"><i class="bi bi-tag"></i></span>
                    <div>
                        <h6 class="text-xs font-medium text-gray-400 mb-0.5">نوع الطلب</h6>
                        <p class="text-sm font-semibold text-gray-900">تعديل بيانات العميل</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-3 bg-gray-50 rounded-lg">
                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-indigo-50 text-indigo-600"><i class="bi bi-person"></i></span>
                    <div>
                        <h6 class="text-xs font-medium text-gray-400 mb-0.5">تم تقديمه من قبل سفير العلامة التجارية</h6>
                        <p class="text-sm font-semibold text-gray-900">
                            {{ $client_request->salesRep->name }}
                            <span class="block text-xs font-normal text-gray-400">{{ $client_request->created_at->format('M d, Y h:i A') }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-indigo-50/60 rounded-lg p-4 my-4">
                <h6 class="text-sm font-semibold text-indigo-700 mb-2 flex items-center gap-2"><i class="bi bi-chat-square-text"></i>وصف الطلب</h6>
                <p class="text-sm text-gray-700">{{ $client_request->description }}</p>
            </div>

            <div class="bg-white border-s-4 border-indigo-500 rounded-lg shadow-sm p-4">
                <h6 class="text-sm font-semibold text-indigo-700 mb-1 flex items-center gap-2"><i class="bi bi-pencil-square"></i>الحقل المطلوب تعديله</h6>
                <p class="text-sm font-semibold text-gray-900">{{ $columns[$client_request->edited_field] ?? 'Unknown' }}</p>
            </div>
        </div>
    </div>

    <!-- Review Decision Card -->
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h5 class="text-sm font-semibold text-gray-900 flex items-center gap-2"><i class="bi bi-clipboard2-check text-indigo-500"></i>حالة الطلب</h5>
        </div>
        <div class="p-6">
            <form method="POST" action="{{ route('admin.client-request.update', [$client->id, $client_request->id]) }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-3">القرار</label>
                        <div class="space-y-2">
                            <label for="status-approved" class="flex items-center gap-3 p-3 border-2 rounded-lg cursor-pointer transition-colors has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50 border-gray-200">
                                <input class="text-emerald-600 focus:ring-emerald-500" type="radio" name="status" id="status-approved"
                                    value="approved" {{ $client_request->status === 'approved' ? 'checked' : '' }}>
                                <span class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600"><i class="bi bi-check-circle-fill"></i></span>
                                    <span>
                                        <strong class="block text-sm text-gray-900">قبول الطلب</strong>
                                        <small class="text-xs text-gray-500">سيتم قبول طلب التعديل</small>
                                    </span>
                                </span>
                            </label>

                            <label for="status-rejected" class="flex items-center gap-3 p-3 border-2 rounded-lg cursor-pointer transition-colors has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50 border-gray-200">
                                <input class="text-rose-600 focus:ring-rose-500" type="radio" name="status" id="status-rejected"
                                    value="rejected" {{ $client_request->status === 'rejected' ? 'checked' : '' }}>
                                <span class="flex items-center gap-3">
                                    <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-rose-50 text-rose-600"><i class="bi bi-x-circle-fill"></i></span>
                                    <span>
                                        <strong class="block text-sm text-gray-900">رفض الطلب</strong>
                                        <small class="text-xs text-gray-500">سيتم رفض التعديل</small>
                                    </span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">ملاحظات الإدارة</label>
                        <textarea name="notes" id="notes" rows="6" placeholder="اترك تعليقًا"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ old('notes', $client_request->notes) }}</textarea>
                        <p class="mt-1.5 text-xs text-gray-400">أضف أي معلومات أو ملاحظات للمندوب</p>
                    </div>
                </div>

                <div class="flex justify-between items-center mt-6 pt-4 border-t border-gray-100">
                    <a href="{{ route('sales-reps.clients.show', ['client' => $client->id, 'sales_rep' => $client->sales_rep_id]) }}"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 text-sm font-semibold hover:bg-gray-50 transition-colors">
                        <i class="bi bi-arrow-left"></i>الرجوع لصفحة العميل
                    </a>
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition-colors">
                        <i class="bi bi-send-check"></i>إرسال التقييم
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
