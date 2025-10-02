@extends('layouts.master')

@section('title', 'Review Agreement Edit agreement_request')

@section('content')

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-light">
            <i class="bi bi-file-earmark-text me-2"></i>مراجعة طلب تعديل الإتفاقية
        </h1>
        <div class="badge bg-primary rounded-pill fs-6 p-2">
            طلب تعديل إتفاقية رقم{{ $agreement_request->id }}
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-lg overflow-hidden mb-4">
        <div class="card-header bg-light-blue-gradient">
            <h5 class="mb-0 text-white">
                <i class="bi bi-file-earmark-text me-2"></i>الإتفاقية رقم{{ $agreement->id }}
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-blue-soft me-3">
                            <i class="bi bi-tag text-blue"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted">نوع تعديل الطلب</h6>
                            <p class="mb-0 fw-bold">
تعديل بيانات الإتفاقية                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-blue-soft me-3">
                            <i class="bi bi-person text-blue"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 text-muted">تم تقديم الطلب من قبل سفير العلامة التجارية:</h6>
                            <p class="mb-0 fw-bold">
                                {{ $agreement_request->salesRep?->user?->name }}
                                <span class="text-muted small d-block">
                                    {{ $agreement_request->created_at?->format('M d, Y h:i A') }}
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="agreement_request-description bg-light-blue-soft p-4 rounded mb-4">
                <h6 class="text-blue mb-3">
                    <i class="bi bi-chat-square-text me-2"></i>وصف طلب التعديل
                </h6>
                <p class="mb-0">{{ $agreement_request->description }}</p>
            </div>

            <div class="edited-field-info bg-white border-start border-4 border-primary p-3 rounded shadow-sm mb-4">
                <h6 class="text-primary mb-2">
                    <i class="bi bi-pencil-square me-2"></i>الحقل المطلوب تعديله
                <p class="mb-0 text-dark fw-semibold">
                    {{ $columns[$agreement_request->edited_field] ?? 'Unknown Field' }}
                </p>
            </div>

            @if($agreement_request->edited_field)
            <div class="current-value bg-light p-4 rounded mb-4">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted mb-3">
                            <i class="bi bi-arrow-left-right me-2"></i>القيمة الحالية
                        </h6>
                        <p class="mb-0 fw-semibold">
                            {{ $agreement->{$agreement_request->edited_field} ?? 'N/A' }}
                        </p>
                    </div>

                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-lg">
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="bi bi-check-circle me-2"></i>مراجعة الطلب
            </h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.agreement-request.update', [
    'agreement' => $agreement->id,
    'agreement_request' => $agreement_request->id,
]) }}">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label text-blue">القرار</label>
                        <div class="btn-group-vertical w-100" role="group">
                            <input type="radio" class="btn-check" name="status" id="status-approved" value="approved" {{
                                $agreement_request->status === 'approved' ? 'checked' : '' }}>
                            <label class="btn btn-outline-success text-start py-3" for="status-approved">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                <strong>قبول طلب تعديل الإتفاقية</strong>
                                <p class="small mb-0 text-muted">سيتم السماح بإجراء تعديل على {{ $columns[$agreement_request->edited_field] }}</p>
                            </label>

                            <input type="radio" class="btn-check" name="status" id="status-rejected" value="rejected" {{
                                $agreement_request->status === 'rejected' ? 'checked' : '' }}>
                            <label class="btn btn-outline-danger text-start py-3" for="status-rejected">
                                <i class="bi bi-x-circle-fill me-2"></i>
                                <strong>رفض التعديل للإتفاقية</strong>
                                <p class="small mb-0 text-muted">>سيتم منع إجراء تعديل على {{ $columns[$agreement_request->edited_field] }}</p>
                            </label>
                        </div>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="notes" class="form-label text-blue">ملاحظات الإدارة</label>
                        <textarea class="form-control border-blue-soft" name="notes" id="notes" rows="5"
                            placeholder="أضف أي ملاحظات للمندوب...">{{ old('notes', $agreement_request->notes) }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-4">
                    <a href="{{ route('salesrep.agreements.show', ['salesrep' => $agreement->sales_rep_id, 'agreement' => $agreement->id]) }}"
                        class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>الرجوع للإتفاقية
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send-check me-2"></i>إرسال القرار
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .bg-light-blue-gradient {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    }

    .bg-light-blue-soft {
        background-color: rgba(59, 130, 246, 0.08);
    }

    .text-blue {
        color: #1d4ed8;
    }

    .border-blue-soft {
        border-color: rgba(59, 130, 246, 0.3);
    }

    .icon-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .btn-check:checked+.btn-outline-success {
        background-color: #22c55e;
        color: white;
    }

    .btn-check:checked+.btn-outline-danger {
        background-color: #ef4444;
        color: white;
    }
</style>
@endsection
