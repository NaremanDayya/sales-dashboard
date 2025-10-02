@extends('layouts.master')

@section('title', 'طلبات تعديل بيانات العملاء')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">طلبات تعديل بيانات العملاء</h2>
                    <div class="actions">
                        <!-- Add any action buttons here if needed -->
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="requestsTable">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-end">#</th>
                                    <th class="text-end">العميل</th>
                                    <th class="text-end">سفير العلامة التجارية</th>
                                    <th class="text-end">نوع الطلب</th>
                                    <th class="text-end">الوصف</th>
                                    <th class="text-end">الحقل المعدل</th>
                                    <th class="text-end">تاريخ الطلب</th>
                                    <th class="text-end">الحالة</th>
                                    <th class="text-end">ملاحظات الإدارة</th>
                                    <th class="text-end">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($clientEditRequests as $request)
                                    <tr class="border-bottom">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="ms-3">
                                                    <h6 class="mb-0">{{ $request->client->company_name ?? 'غير متوفر' }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $request->salesRep->name ?? 'غير معروف' }}</td>
                                        <td>
                                            <span class="badge bg-primary bg-opacity-10 text-primary">
                                                {{ $request->request_type }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="text-truncate" style="max-width: 150px;" data-bs-toggle="tooltip" title="{{ $request->description }}">
                                                    {{ \Illuminate\Support\Str::limit($request->description, 30) }}
                                                </span>
                                            </div>
                                        </td>
                                        <td>{{ $request->edited_field ?? '-' }}</td>
                                        <td>
                                            <span class="text-muted">{{ $request->created_at->format('Y-m-d') }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $badgeClass = match($request->status) {
                                                    'pending' => 'bg-warning text-dark',
                                                    'approved' => 'bg-success',
                                                    'rejected' => 'bg-danger',
                                                    default => 'bg-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }} rounded-pill">
                                                {{ __('status.' . $request->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ $request->notes ?? '-' }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a href="{{ route('admin.client-edit-requests.show', $request->id) }}"
                                                   class="btn btn-sm btn-icon btn-outline-info rounded-circle"
                                                   data-bs-toggle="tooltip"
                                                   title="عرض التفاصيل">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fas fa-inbox fa-3x text-muted mb-2"></i>
                                                <h5 class="text-muted">لا توجد طلبات تعديل حالياً</h5>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($clientEditRequests->hasPages())
                    <div class="mt-4 d-flex justify-content-center">
                        {{ $clientEditRequests->links('pagination::bootstrap-5') }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .card {
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.03);
    }

    .card-header {
        padding: 1.25rem 1.5rem;
        border-radius: 12px 12px 0 0 !important;
    }

    .table {
        margin-bottom: 0;
    }

    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        color: #6c757d;
        border-bottom-width: 1px;
    }

    .table td, .table th {
        padding: 1rem 1.25rem;
        vertical-align: middle;
    }

    .table-hover tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }

    .btn-icon {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .text-truncate {
        max-width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: inline-block;
    }

    .empty-state {
        padding: 3rem 0;
    }
</style>
@endpush

@push('scripts')
<script>
    $(document).ready(function() {
        // Enable tooltips
        $('[data-bs-toggle="tooltip"]').tooltip({
            placement: 'top'
        });
    });
</script>
@endpush
