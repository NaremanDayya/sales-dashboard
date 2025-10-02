@extends('layouts.master')

@section('title', 'مراجعة طلب العميل من المحادثة')

@section('content')
<div class="container-fluid px-4 py-3">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="fw-semibold text-gray-800">مراجعة طلب العميل من المحادثة</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item active" aria-current="page">مراجعة الطلب</li>
                </ol>
            </nav>
        </div>
        <div class="badge bg-primary bg-opacity-10 text-primary fs-6 p-2 px-3 rounded-3">
            طلب محادثة رقم {{ $client_request->id }}
        </div>
    </div>

    <!-- Request Summary Card -->
    <div class="card border-0 rounded-4 shadow-sm mb-4">
        <div class="card-header bg-transparent border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-gray-800">
                    <i class="bi bi-building me-2 text-primary"></i>{{ $client_request->client->company_name }}
                </h5>
                <span
                    class="badge bg-{{ $client_request->status === 'pending' ? 'warning' : ($client_request->status === 'approved' ? 'success' : 'danger') }} bg-opacity-10 text-{{ $client_request->status === 'pending' ? 'warning' : ($client_request->status === 'approved' ? 'success' : 'danger') }} px-3 py-2 rounded-pill">
                    {{ ucfirst($client_request->status) }}
                </span>
            </div>
        </div>
        <div class="card-body pt-0">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="d-flex align-items-start p-3 bg-light bg-opacity-10 rounded-3 h-100">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="bi bi-tag fs-5 text-primary"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 text-muted small">نوع الطلب</h6>
                            <p class="mb-0 fw-semibold">طلب من العميل (محادثة)</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="d-flex align-items-start p-3 bg-light bg-opacity-10 rounded-3 h-100">
                        <div class="bg-primary bg-opacity-10 p-2 rounded-3 me-3">
                            <i class="bi bi-person fs-5 text-primary"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 text-muted small">سفير العلامة التجارية</h6>
                            <p class="mb-0 fw-semibold">
                                {{ $client_request->salesRep->name }}
                                <span class="text-muted small d-block">
                                    {{ $client_request->created_at->format('M d, Y h:i A') }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-light bg-opacity-25 p-4 rounded-3 my-4 border-start border-3 border-primary">
                <h6 class="text-gray-700 mb-3 d-flex align-items-center">
                    <i class="bi bi-chat-dots fs-5 me-2 text-primary"></i>تفاصيل الطلب
                </h6>
                <p class="mb-0 text-gray-800">{{ $client_request->message }}</p>
            </div>
        </div>
    </div>

    <!-- Review Decision Card -->
    <div class="card border-0 rounded-4 shadow-sm">
        <div class="card-header bg-transparent border-0 py-3">
            <h5 class="mb-0 text-gray-800">
                <i class="bi bi-clipboard2-check me-2 text-primary"></i>تقييم الطلب
            </h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.chat-client-request.update', [$client_request->client_id, $client_request->id]) }}">
                @csrf
                @method('PUT')

                <div class="row g-4">
                    <div class="col-lg-6">
                        <label class="form-label text-gray-700 mb-3">القرار</label>
                        <div class="vstack gap-3">
                            <div class="form-check-card">
                                <input class="form-check-input" type="radio" name="status" id="status-approved"
                                    value="approved" {{ $client_request->status === 'approved' ? 'checked' : '' }}>
                                <label class="form-check-label" for="status-approved">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success bg-opacity-10 p-2 rounded-3 me-3">
                                            <i class="bi bi-check-circle-fill text-success"></i>
                                        </div>
                                        <div>
                                            <strong class="d-block text-gray-800">قبول الطلب</strong>
                                            <small class="text-muted">سيتم تنفيذ الطلب أو الرد عليه</small>
                                        </div>
                                    </div>
                                </label>
                            </div>

                            <div class="form-check-card">
                                <input class="form-check-input" type="radio" name="status" id="status-rejected"
                                    value="rejected" {{ $client_request->status === 'rejected' ? 'checked' : '' }}>
                                <label class="form-check-label" for="status-rejected">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-danger bg-opacity-10 p-2 rounded-3 me-3">
                                            <i class="bi bi-x-circle-fill text-danger"></i>
                                        </div>
                                        <div>
                                            <strong class="d-block text-gray-800">رفض الطلب</strong>
                                            <small class="text-muted">لن يتم اتخاذ إجراء</small>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-floating">
                            <textarea class="form-control border-0 bg-light bg-opacity-10" placeholder="ملاحظات الإدارة"
                                name="notes" id="notes" style="height: 150px">{{ old('notes', $client_request->notes) }}</textarea>
                            <label for="notes">ملاحظات الإدارة</label>
                        </div>
                        <div class="form-text">أضف أي ملاحظات للمندوب بخصوص هذا الطلب</div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-5 pt-3 border-top">
                    <a href="{{ route('sales-reps.clients.show', ['client' => $client_request->client_id, 'sales_rep' => $client_request->sales_rep_id]) }}"
                        class="btn btn-outline-secondary rounded-3 px-4">
                        <i class="bi bi-arrow-left me-2"></i>الرجوع لصفحة العميل
                    </a>
                    <button type="submit" class="btn btn-primary rounded-3 px-4 py-2">
                        <i class="bi bi-send-check me-2"></i>إرسال التقييم
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .form-check-card {
        border: 1px solid #e9ecef;
        border-radius: 12px;
        padding: 1rem;
        transition: all 0.2s;
    }

    .form-check-card:hover {
        border-color: #0d6efd;
        background-color: rgba(13, 110, 253, 0.03);
    }

    .form-check-input {
        margin-top: 0;
        margin-left: -1.5em;
    }

    .breadcrumb {
        background-color: transparent;
        padding: 0;
    }

    .breadcrumb-item a {
        color: #6c757d;
        text-decoration: none;
    }

    .breadcrumb-item.active {
        color: #495057;
    }
</style>
@endsection
