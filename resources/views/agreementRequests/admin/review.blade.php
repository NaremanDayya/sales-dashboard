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
                        class="btn btn-sm btn-light-blue" data-bs-toggle="tooltip" title="عرض تفاصيل العميل">
                        <i class="bi bi-person-lines-fill me-1"></i> العميل
                    </a>
                    <a href="{{ route('admin.agreement-request.edit', ['agreement' => $agreement_request->agreement->id, 'agreement_request' => $agreement_request->id]) }}"
                        class="btn btn-primary">
                        <i class="bi bi-pencil-square me-2"></i> تعديل الطلب
                    </a>

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
                        <div class="d-flex align-items-center">
                            <span class="badge bg-primary-soft text-primary me-3">الحقل المعدل</span>
                            <span class="fw-medium">{{ $editedFieldLabel }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="request-details bg-light p-4 rounded-3 mb-4">
                <h5 class="text-primary mb-3"><i class="bi bi-chat-square-text me-2"></i>وصف التعديل</h5>
                <div class="description-box p-3 bg-white rounded-2 border">
                    {{ $agreement_request->description ?? 'لم يتم تقديم وصف' }}
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
</style>
@endsection
