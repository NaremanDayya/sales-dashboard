@extends('layouts.master')

@section('title', 'مراجعة طلب تعديل الاتفاقية')

@section('content')
    <div class="container py-5">
        <div class="card border-0 shadow-lg">
            <div class="card-header bg-gradient-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">
                        <i class="bi bi-file-earmark-diff me-2"></i> مراجعة طلب تعديل الاتفاقية
                    </h3>
                    <div class="action-buttons">
                        <a href="{{ route('admin.client-request.edit', ['client' => $agreement_request->client_id, 'client_request' => $agreement_request->id]) }}"
                           class="btn btn-sm btn-light-blue"    style="--bs-btn-hover-bg: #0d6efd; --bs-btn-hover-color: #fff;"

                           data-bs-toggle="tooltip" title="عرض تفاصيل العميل">
                            <i class="bi bi-person-lines-fill me-1"></i> العميل
                        </a>
                        @if(auth()->user()->role === 'admin' && $agreement_request->status === 'pending')
                            <a href="{{ route('admin.agreement-request.edit', ['agreement' => $agreement_request->agreement->id, 'agreement_request' => $agreement_request->id]) }}"
                               class="btn btn-primary">
                                <i class="bi bi-pencil-square me-2"></i> تعديل الطلب
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="info-card bg-light p-4 rounded-3 mb-4">
                            <h5 class="text-primary mb-4"><i class="bi bi-building me-2"></i>معلومات العميل</h5>
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge bg-primary-soft text-primary me-3">العميل</span>
                                <span class="fw-medium">{{ $agreement_request->client->company_name }}</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="badge bg-primary-soft text-primary me-3">مندوب المبيعات</span>
                                <span class="fw-medium">{{ $agreement_request->salesRep->name }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="info-card bg-light p-4 rounded-3 mb-4">
                            <h5 class="text-primary mb-4"><i class="bi bi-file-text me-2"></i>تفاصيل الاتفاقية</h5>
                            <div class="d-flex align-items-center mb-3">
                                <span class="badge bg-primary-soft text-primary me-3">رقم الاتفاقية</span>
                                <span class="fw-medium">#{{ $agreement_request->agreement->id }}</span>
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

                                // Translate agreement_status and notice_status values if they exist
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

                                // Fallback if new value is not yet updated
                                $newValue = $newValue ?: 'لم يتم التعديل بعد';
                            @endphp


                            <div class="d-flex align-items-center mb-3">
                                <span class="badge bg-primary-soft text-primary me-3">الحقل المعدل</span>
                                <span class="fw-medium">{{ $translatedLabel }}</span>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Values Comparison -->
                <div class="values-comparison bg-light p-4 rounded-3 mb-4 shadow-sm">
                    <h5 class="text-primary mb-4">
                        <i class="bi bi-arrow-left-right me-2"></i>مقارنة القيم
                    </h5>

                    <div class="row">
                        <!-- Old Value -->
                        <div class="col-md-6 mb-3">
                            <div class="value-card bg-white p-3 rounded-2 border border-danger border-2 h-100">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="value-badge bg-danger text-white rounded-circle me-2"
                                         style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-arrow-left"></i>
                                    </div>
                                    <h6 class="mb-0 text-danger fw-bold">القيمة القديمة</h6>
                                </div>
                                <div class="value-content p-2 bg-light rounded">
                                    <p class="mb-0 text-dark fw-medium">{{ $oldValue ?? 'غير متوفرة' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- New Value -->
                        <div class="col-md-6 mb-3">
                            <div class="value-card bg-white p-3 rounded-2 border {{ $newValue === 'لم يتم التعديل بعد' ? 'border-secondary' : 'border-success' }} border-2 h-100">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="value-badge {{ $newValue === 'لم يتم التعديل بعد' ? 'bg-secondary' : 'bg-success' }} text-white rounded-circle me-2"
                                         style="width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-arrow-right"></i>
                                    </div>
                                    <h6 class="mb-0 {{ $newValue === 'لم يتم التعديل بعد' ? 'text-secondary' : 'text-success' }} fw-bold">
                                        القيمة الجديدة
                                    </h6>
                                </div>
                                <div class="value-content p-2 bg-light rounded">
                                    <p class="mb-0 text-dark fw-medium">{{ $newValue }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Change Indicator -->
                    @if($oldValue && $newValue !== 'لم يتم التعديل بعد')
                        <div class="change-indicator text-center mt-3">
                            <div class="d-inline-flex align-items-center bg-primary text-white px-4 py-2 rounded-pill shadow-sm">
                                <i class="bi bi-arrow-repeat me-2"></i>
                                <span class="fw-medium">تم طلب تغيير القيمة</span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Request Description -->
                <div class="request-details bg-light p-4 rounded-3 mb-4">
                    <h5 class="text-primary mb-3"><i class="bi bi-chat-square-text me-2"></i>وصف التعديل</h5>
                    <div class="description-box p-3 bg-white rounded-2 border">
                        {{ $agreement_request->description ?? 'لم يتم تقديم وصف' }}
                    </div>
                </div>

                <!-- Admin Notes -->
                <div class="request-details bg-light p-4 rounded-3 mb-4">
                    <h5 class="text-primary mb-3"><i class="bi bi-chat-square-text me-2"></i>ملاحظات الإدارة</h5>
                    <div class="description-box p-3 bg-white rounded-2 border">
                        {{ $agreement_request->notes ?? 'لم يتم وضع ملاحظات إدارية' }}
                    </div>
                </div>

                <!-- Status and Dates -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="status-card bg-light p-4 rounded-3">
                            <h5 class="text-primary mb-3"><i class="bi bi-info-circle me-2"></i>حالة الطلب</h5>
                            @php
                                $statusClasses = [
                                    'pending' => 'bg-warning text-dark',
                                    'approved' => 'bg-success text-white',
                                    'rejected' => 'bg-danger text-white',
                                ];

                                $statusTranslations = [
                                    'pending' => 'قيد الانتظار',
                                    'approved' => 'تمت الموافقة',
                                    'rejected' => 'مرفوض',
                                ];

                                $status = $agreement_request->status;
                                $badgeClass = $statusClasses[$status] ?? 'bg-secondary text-white';
                                $translatedStatus = $statusTranslations[$status] ?? $status;
                            @endphp
                            <span class="badge {{ $badgeClass }} fs-6 px-3 py-2">
                            {{ $translatedStatus }}
                        </span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="dates-card bg-light p-4 rounded-3">
                            <h5 class="text-primary mb-3"><i class="bi bi-calendar-event me-2"></i>التواريخ</h5>
                            <div class="date-item mb-2">
                                <small class="text-muted">تاريخ الإنشاء:</small>
                                <div class="fw-medium">{{ $agreement_request->created_at->format('Y-m-d H:i') }}</div>
                            </div>
                            <div class="date-item">
                                <small class="text-muted">تاريخ الرد:</small>
                                <div class="fw-medium">
                                    {{ $agreement_request->response_date ? $agreement_request->response_date->format('Y-m-d H:i') : 'لم يتم الرد بعد' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-5">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i> رجوع
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .bg-gradient-primary {
            background: linear-gradient(135deg, #3a7bd5 0%, #00d2ff 100%);
        }

        .btn-light-blue {
            background-color: #e3f2fd;
            color: #0d6efd;
            border: none;
        }

        .btn-light-blue:hover {
            background-color: #bbdefb;
        }

        .info-card {
            transition: transform 0.2s ease;
        }

        .info-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .description-box {
            min-height: 100px;
            white-space: pre-wrap;
        }

        .badge.bg-primary-soft {
            background-color: rgba(13, 110, 253, 0.1);
            min-width: 90px;
        }

        .card {
            border-radius: 12px;
            overflow: hidden;
        }

        .card-header {
            padding: 1.5rem;
        }

        /* New styles for values comparison */
        .values-comparison {
            border-left: 4px solid #4154f1;
        }

        .value-card {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .value-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .value-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, transparent 0%, currentColor 50%, transparent 100%);
            opacity: 0.3;
        }

        .value-content {
            min-height: 60px;
            display: flex;
            align-items: center;
            word-break: break-word;
        }

        .change-indicator {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

        .status-card, .dates-card {
            transition: transform 0.2s ease;
        }

        .status-card:hover, .dates-card:hover {
            transform: translateY(-2px);
        }
    </style>
@endsection
