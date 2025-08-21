@extends('layouts.master')

@section('title', 'طلبات العملاء من المحادثة')
@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent border-bottom d-flex justify-content-between align-items-center">
                    <h2 class="mb-0">طلبات العملاء من المحادثة</h2>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" id="chatRequestsTable">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-end">#</th>
                                    <th class="text-end">العميل</th>
                                    <th class="text-end">المندوب</th>
                                    <th class="text-end">الرسالة</th>
                                    <th class="text-end">تاريخ الطلب</th>
                                    <th class="text-end">الحالة</th>
                                    <th class="text-end">ملاحظات الإدارة</th>
                                    <th class="text-end">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($clientRequests as $request)
                                    <tr class="border-bottom">
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $request->client->company_name ?? 'غير متوفر' }}</td>
                                        <td>{{ $request->salesRep->name ?? 'غير معروف' }}</td>
                                        <td>
                                            <span class="text-truncate" style="max-width: 180px; display:inline-block;" data-bs-toggle="tooltip" title="{{ $request->message }}">
                                                {{ \Illuminate\Support\Str::limit($request->message, 35) }}
                                            </span>
                                        </td>
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
                                        <td>{{ $request->notes ?? '-' }}</td>
                                        <td>
                                            <a href="{{ route('admin.chat-client-request.review', [$request->client_id, $request->id]) }}"
                                               class="btn btn-sm btn-icon btn-outline-info rounded-circle"
                                               data-bs-toggle="tooltip" title="مراجعة">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="fas fa-comments fa-3x text-muted mb-2"></i>
                                                <h5 class="text-muted">لا توجد طلبات محادثة حالياً</h5>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($clientRequests->hasPages())
                        <div class="mt-4 d-flex justify-content-center">
                            {{ $clientRequests->links('pagination::bootstrap-5') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
    });
</script>
@endpush
