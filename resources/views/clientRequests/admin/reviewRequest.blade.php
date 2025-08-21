@extends('layouts.master')

@section('title', 'تفاصيل طلب خاص بالعميل')

@section('content')
<div class="container py-4">
    <div class="max-w-6xl mx-auto">
        @if(Auth::user()->role == 'admin')
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">تفاصيل الطلب الخاص بالعميل</h1>
            <div class="flex space-x-3 rtl:space-x-reverse">
                <a href="{{ route('admin.chat-client-request.edit', ['client' => $client_request->client_id ,'client_request' => $client_request->id]) }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                    تعديل الحالة
                </a>
            </div>
        </div>
        @endif

        <!-- Main Card -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">اسم العميل</span>
                        <p class="text-lg font-semibold text-gray-800 dark:text-white">
                            {{ $client_request->client->company_name ?? '-' }}
                        </p>
                    </div>
                    <div class="space-y-1">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">المندوب</span>
                        <p class="text-lg font-semibold text-gray-800 dark:text-white">
                            {{ $client_request->salesRep->name ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                <div class="space-y-1 mb-4">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">نص الطلب</span>
                    <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <p class="text-gray-800 dark:text-gray-200">{{ $client_request->message }}</p>
                    </div>
                </div>

                <div class="space-y-1 mb-4">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">حالة الطلب</span>
                    @php
                        $statusClasses = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'approved' => 'bg-green-100 text-green-800',
                            'rejected' => 'bg-red-100 text-red-800',
                        ];
                        $badgeClass = $statusClasses[$client_request->status] ?? 'bg-gray-100 text-gray-800';
                        $statusArabic = [
                            'pending' => 'قيد الانتظار',
                            'approved' => 'مقبول',
                            'rejected' => 'مرفوض',
                        ][$client_request->status] ?? $client_request->status;
                    @endphp
                    <span class="px-3 py-1 rounded-full text-sm font-medium {{ $badgeClass }}">
                        {{ $statusArabic }}
                    </span>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">تاريخ الإنشاء</span>
                        <p class="text-gray-800 dark:text-white">
                            {{ $client_request->created_at->format('Y-m-d H:i') }}
                        </p>
                    </div>
                    <div class="space-y-1">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">تاريخ الرد</span>
                        <p class="text-gray-800 dark:text-white">
                            {{ $client_request->updated_at->format('Y-m-d H:i') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .rtl {
        direction: rtl;
    }
    .rtl .ml-2 {
        margin-left: 0;
        margin-right: 0.5rem;
    }
</style>
@endsection
