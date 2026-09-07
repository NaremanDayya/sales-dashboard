@extends('layouts.master')

@section('title', 'Review Agreement Edit agreement_request')

@section('content')
<x-page-header title="مراجعة طلب تعديل الاتفاقية" subtitle="اتخذ قرارًا بشأن طلب التعديل المقدَّم">
    <x-slot name="actions">
        <x-badge color="indigo">طلب رقم {{ $agreement_request->id }}</x-badge>
    </x-slot>
</x-page-header>

<div class="max-w-4xl space-y-6">
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h5 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                <i class="bi bi-file-earmark-text text-indigo-500"></i>الاتفاقية رقم {{ $agreement->id }}
            </h5>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-indigo-600">
                        <i class="bi bi-tag"></i>
                    </span>
                    <div>
                        <h6 class="text-xs font-medium text-gray-400">نوع تعديل الطلب</h6>
                        <p class="text-sm font-semibold text-gray-900">تعديل بيانات الإتفاقية</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-indigo-600">
                        <i class="bi bi-person"></i>
                    </span>
                    <div>
                        <h6 class="text-xs font-medium text-gray-400">تم تقديم الطلب من قبل سفير العلامة التجارية</h6>
                        <p class="text-sm font-semibold text-gray-900">
                            {{ $agreement_request->salesRep?->user?->name }}
                            <span class="block text-xs font-normal text-gray-400">
                                {{ $agreement_request->created_at?->format('M d, Y h:i A') }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <div class="bg-indigo-50/60 rounded-lg p-4 mb-4">
                <h6 class="text-sm font-semibold text-indigo-700 mb-2 flex items-center gap-2">
                    <i class="bi bi-chat-square-text"></i>وصف طلب التعديل
                </h6>
                <p class="text-sm text-gray-700">{{ $agreement_request->description }}</p>
            </div>

            <div class="bg-white border-s-4 border-indigo-500 rounded-lg shadow-sm p-4 mb-4">
                <h6 class="text-sm font-semibold text-indigo-700 mb-1 flex items-center gap-2">
                    <i class="bi bi-pencil-square"></i>الحقل المطلوب تعديله
                </h6>
                <p class="text-sm font-semibold text-gray-900">
                    {{ $columns[$agreement_request->edited_field] ?? 'Unknown Field' }}
                </p>
            </div>

            @if($agreement_request->edited_field)
                <div class="bg-gray-50 rounded-lg p-4">
                    <h6 class="text-xs font-medium text-gray-400 mb-2 flex items-center gap-2">
                        <i class="bi bi-arrow-left-right"></i>القيمة الحالية
                    </h6>
                    <p class="text-sm font-semibold text-gray-900">
                        {{ $agreement->{$agreement_request->edited_field} ?? 'N/A' }}
                    </p>
                </div>
            @endif
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h5 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                <i class="bi bi-check-circle text-indigo-500"></i>مراجعة الطلب
            </h5>
        </div>
        <div class="p-6">
            <form method="POST" action="{{ route('admin.agreement-request.update', [
    'agreement' => $agreement->id,
    'agreement_request' => $agreement_request->id,
]) }}">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">القرار</label>
                        <div class="space-y-2">
                            <label for="status-approved" class="flex items-start gap-3 p-3 border-2 rounded-lg cursor-pointer transition-colors has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50 border-gray-200">
                                <input type="radio" class="mt-1 text-emerald-600 focus:ring-emerald-500" name="status" id="status-approved" value="approved" {{ $agreement_request->status === 'approved' ? 'checked' : '' }}>
                                <span>
                                    <span class="flex items-center gap-1.5 text-sm font-semibold text-emerald-700"><i class="bi bi-check-circle-fill"></i> قبول طلب تعديل الإتفاقية</span>
                                    <span class="block text-xs text-gray-500 mt-0.5">سيتم السماح بإجراء تعديل على {{ $columns[$agreement_request->edited_field] }}</span>
                                </span>
                            </label>

                            <label for="status-rejected" class="flex items-start gap-3 p-3 border-2 rounded-lg cursor-pointer transition-colors has-[:checked]:border-rose-500 has-[:checked]:bg-rose-50 border-gray-200">
                                <input type="radio" class="mt-1 text-rose-600 focus:ring-rose-500" name="status" id="status-rejected" value="rejected" {{ $agreement_request->status === 'rejected' ? 'checked' : '' }}>
                                <span>
                                    <span class="flex items-center gap-1.5 text-sm font-semibold text-rose-700"><i class="bi bi-x-circle-fill"></i> رفض التعديل للإتفاقية</span>
                                    <span class="block text-xs text-gray-500 mt-0.5">سيتم منع إجراء تعديل على {{ $columns[$agreement_request->edited_field] }}</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">ملاحظات الإدارة</label>
                        <textarea name="notes" id="notes" rows="5"
                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="أضف أي ملاحظات للمندوب...">{{ old('notes', $agreement_request->notes) }}</textarea>
                    </div>
                </div>

                <div class="flex justify-between items-center mt-6 pt-4 border-t border-gray-100">
                    <a href="{{ route('salesrep.agreements.show', ['salesrep' => $agreement->sales_rep_id, 'agreement' => $agreement->id]) }}"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 text-sm font-semibold hover:bg-gray-50 transition-colors">
                        <i class="bi bi-arrow-left"></i>الرجوع للإتفاقية
                    </a>
                    <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition-colors">
                        <i class="bi bi-send-check"></i>إرسال القرار
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
