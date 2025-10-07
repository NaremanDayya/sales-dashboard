@extends('layouts.master')

@section('title', 'تفاصيل طلب تعديل بيانات العميل')

@section('content')
    <div class="container py-4">
        <div class="max-w-6xl mx-auto">
            @if(auth()->user()->role === 'admin' && $client_request->status === 'pending')
                <!-- Header Section -->
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">تفاصيل طلب التعديل</h1>
                    <div class="flex space-x-3 rtl:space-x-reverse">
                        <a href="{{ route('admin.client-request.edit', ['client' => $client_request->client_id, 'client_request' => $client_request->id]) }}"
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition duration-200">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20"
                                 fill="currentColor">
                                <path
                                    d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                            </svg>
                            تعديل الحالة
                        </a>
                    </div>
                </div>
            @endif

            <!-- Main Card -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
                <!-- Client Info Section -->
                <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">اسم العميل</span>
                            <p class="text-lg font-semibold text-gray-800 dark:text-white">{{
                            $client_request->client->company_name ?? '-' }}</p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">سفير العلامة التجارية</span>
                            <p class="text-lg font-semibold text-gray-800 dark:text-white">{{
                            $client_request->salesRep->name ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Request Details Section -->
                <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        @if(isset($editedFieldLabel) && $editedFieldLabel)
                            @php
                                $fields = [
                                    'company_name' => 'اسم الشركة',
                                    'logo' => 'الشعار',
                                    'address' => 'العنوان',
                                    'contact_person' => 'الشخص المسؤول',
                                    'contact_position' => 'منصب الشخص المسؤول',
                                    'interest_status' => 'حالة الاهتمام',
                                    'phone' => 'رقم الجوال',
                                    'interested_service' => 'الخدمة المهتم بها',
                                    'interested_service_count' => 'عدد الخدمات المهتم بها',
                                ];

                                $translatedLabel = $fields[$editedFieldLabel] ?? $editedFieldLabel;
                            @endphp

                            <div class="space-y-1">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">الحقل المعدل</span>
                                <p class="text-lg font-semibold text-gray-800 dark:text-white">{{ $translatedLabel }}</p>
                            </div>
                        @endif
                        <div class="space-y-1">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">حالة الطلب</span>
                            @php
                                $statusClasses = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'approved' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                ];

                                $statusTranslations = [
                                    'pending' => 'قيد الانتظار',
                                    'approved' => 'تمت الموافقة',
                                    'rejected' => 'مرفوض',
                                ];

                                $status = $client_request->status;
                                $badgeClass = $statusClasses[$status] ?? 'bg-gray-100 text-gray-800';
                                $translatedStatus = $statusTranslations[$status] ?? $status;
                            @endphp
                            <span class="px-3 py-1 rounded-full text-sm font-medium {{ $badgeClass }}">
                            {{ $translatedStatus }}
                        </span>
                        </div>
                    </div>

                    <!-- NEW SECTION: Old Value and New Value -->
                    @php
                        $payload = json_decode($client_request->payload, true) ?? [];
                        $oldValue = $payload['old_value'] ?? null;
                        $newValue = $payload['new_value'] ?? null;

                        // Translate interest statuses if the edited field is 'interest_status'
                        if (($client_request->edited_field ?? null) === 'interest_status') {
                            $statusTranslations = [
                                'interested' => 'مهتم',
                                'not_interested' => 'غير مهتم',
                                'neutral' => 'مؤجل',
                            ];

                            if (isset($statusTranslations[$oldValue])) {
                                $oldValue = $statusTranslations[$oldValue];
                            }

                            if (isset($statusTranslations[$newValue])) {
                                $newValue = $statusTranslations[$newValue];
                            }
                        }

                        // Add fallback text if new value isn't set
                        $newValue = $newValue ?: 'لم يتم التعديل بعد';
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Old Value Card -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                            <div class="flex items-center mb-3">
                                <div class="w-3 h-3 bg-red-500 rounded-full mr-2"></div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">القيمة القديمة</span>
                            </div>
                            <div class="p-3 bg-white dark:bg-gray-800 rounded border border-gray-300 dark:border-gray-600">
                                <p class="text-gray-800 dark:text-gray-200 text-lg font-semibold break-words">
                                    {{ $oldValue ?? 'غير متوفرة' }}
                                </p>
                            </div>
                        </div>

                        <!-- New Value Card -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                            <div class="flex items-center mb-3">
                                <div class="w-3 h-3 {{ $newValue === 'لم يتم التعديل بعد' ? 'bg-gray-400' : 'bg-green-500' }} rounded-full mr-2"></div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">القيمة الجديدة</span>
                            </div>
                            <div class="p-3 bg-white dark:bg-gray-800 rounded border border-gray-300 dark:border-gray-600">
                                <p class="text-gray-800 dark:text-gray-200 text-lg font-semibold break-words">
                                    {{ $newValue }}
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($oldValue && $newValue !== 'لم يتم التعديل بعد')
                        <div class="flex items-center justify-center mb-6">
                            <div class="flex items-center bg-blue-50 dark:bg-blue-900/20 px-4 py-2 rounded-full">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600 dark:text-blue-400 mr-2" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v3.586L7.707 9.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L11 10.586V7z" clip-rule="evenodd" />
                                </svg>
                                <span class="text-sm font-medium text-blue-700 dark:text-blue-300">تم طلب تغيير القيمة</span>
                            </div>
                        </div>
                    @endif


                @if($client_request->description || $client_request->message)
                        <div class="space-y-1 mb-4">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">وصف الطلب</span>
                            <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <p class="text-gray-800 dark:text-gray-200">
                                    {{ $client_request->message ?? $client_request->description }}
                                </p>
                            </div>
                        </div>
                    @endif

                    <div class="space-y-1">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">ملاحظات الإدارة</span>
                        <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <p class="text-gray-800 dark:text-gray-200">{{ $client_request->notes ?? 'لا يوجد ملاحظات' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Dates Section -->
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">تاريخ الإنشاء</span>
                            <p class="text-gray-800 dark:text-white">{{ $client_request->created_at->format('Y-m-d H:i') }}
                            </p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">تاريخ الرد</span>
                            <p class="text-gray-800 dark:text-white">{{ $client_request->response_date ?
                            $client_request->response_date->format('Y-m-d H:i') : 'لم يتم الرد بعد' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        /* Custom styles can be added here if needed */
        .rtl {
            direction: rtl;
        }

        .rtl .ml-2 {
            margin-left: 0;
            margin-right: 0.5rem;
        }

        .break-words {
            word-break: break-word;
            overflow-wrap: break-word;
        }
    </style>
@endsection
