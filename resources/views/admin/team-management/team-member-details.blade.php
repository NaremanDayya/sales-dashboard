@extends('layouts.master')

@section('title', 'تفاصيل المندوب - ' . $teamMember->name)

@push('styles')
<style>
    :root {
        --primary: #4154f1;
        --primary-light: #6a7ef9;
        --secondary: #10b981;
        --danger: #ef4444;
        --warning: #f59e0b;
    }

    .table-container {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin: 1rem;
    }

    .table-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 1.25rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .table-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1e293b;
    }

    .data-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 800px;
    }

    .data-table thead th {
        background-color: #f8fafc;
        color: #64748b;
        font-weight: 600;
        padding: 12px 15px;
        text-align: right;
        border-bottom: 2px solid #e2e8f0;
    }

    .data-table tbody tr {
        transition: background-color 0.2s ease;
    }

    .data-table tbody tr:hover {
        background-color: #f8fafc;
    }

    .data-table tbody td {
        padding: 12px 15px;
        border-bottom: 1px solid #e2e8f0;
        text-align: right;
        vertical-align: middle;
    }

    .company-logo-sm {
        width: 32px;
        height: 32px;
        border-radius: 4px;
        object-fit: cover;
        margin-left: 0.5rem;
    }

    .btn {
        padding: 8px 16px;
        border-radius: 6px;
        font-weight: 500;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        text-decoration: none;
    }

    .btn-primary {
        background-color: var(--primary);
        color: white;
    }

    .btn-primary:hover {
        background-color: var(--primary-light);
    }

    .btn-outline-primary {
        background-color: white;
        color: var(--primary);
        border: 1px solid var(--primary);
    }

    .btn-outline-secondary {
        background-color: white;
        color: #6c757d;
        border: 1px solid #e2e8f0;
    }

    .nav-tabs {
        border-bottom: 2px solid #e2e8f0;
        margin-bottom: 1.5rem;
    }

    .nav-tabs .nav-link {
        border: none;
        color: #64748b;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        border-bottom: 2px solid transparent;
        margin-bottom: -2px;
    }

    .nav-tabs .nav-link.active {
        color: var(--primary);
        border-bottom-color: var(--primary);
        background: transparent;
    }

    .badge {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }

    .badge-success {
        background-color: #ecfdf5;
        color: #059669;
    }

    .badge-danger {
        background-color: #fee2e2;
        color: #dc2626;
    }

    .badge-secondary {
        background-color: #f1f5f9;
        color: #64748b;
    }

    .member-info-card {
        background: white;
        border-radius: 0.75rem;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .member-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .info-item {
        padding: 0.75rem;
        background: #f8fafc;
        border-radius: 6px;
    }

    .info-label {
        font-size: 0.75rem;
        color: #64748b;
        margin-bottom: 0.25rem;
    }

    .info-value {
        font-size: 1rem;
        font-weight: 600;
        color: #1e293b;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-4">
            <a href="{{ route('admin.team.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right"></i> العودة لإدارة الفرق
            </a>
        </div>

        <div class="member-info-card">
            <div class="d-flex align-items-center gap-3 mb-3">
                <img src="{{ $teamMember->user->personal_image }}" alt="{{ $teamMember->name }}" style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover;">
                <div>
                    <h2 class="table-title mb-1">{{ $teamMember->name }}</h2>
                    <p class="text-muted mb-0">{{ $teamMember->user->email }}</p>
                    @if($teamMember->manager)
                        <p class="text-muted mb-0">المدير: <strong>{{ $teamMember->manager->name }}</strong></p>
                    @endif
                </div>
            </div>

            <div class="member-info-grid">
                <div class="info-item">
                    <div class="info-label">تاريخ الالتحاق</div>
                    <div class="info-value">{{ \Carbon\Carbon::parse($teamMember->start_work_date)->format('Y-m-d') }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">مدة العمل</div>
                    <div class="info-value">{{ $teamMember->work_duration }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">عدد العملاء</div>
                    <div class="info-value">{{ $teamMember->clients->count() }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">عدد الاتفاقيات</div>
                    <div class="info-value">{{ $teamMember->agreements->count() }}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">الاتفاقيات النشطة</div>
                    <div class="info-value">{{ $teamMember->active_agreements_count }}</div>
                </div>
            </div>
        </div>

        <div class="table-container">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#clients">
                        العملاء ({{ $clients->total() }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#agreements">
                        الاتفاقيات ({{ $agreements->total() }})
                    </a>
                </li>
            </ul>

            <div class="tab-content">
                <div id="clients" class="tab-pane fade show active">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>اسم الشركة</th>
                                    <th>الشخص المسؤول</th>
                                    <th>رقم الهاتف</th>
                                    <th>حالة الاهتمام</th>
                                    <th>آخر تواصل</th>
                                    <th>عدد الاتفاقيات</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($clients as $client)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="{{ $client->company_logo }}" alt="{{ $client->company_name }}" class="company-logo-sm">
                                                <strong>{{ $client->company_name }}</strong>
                                            </div>
                                        </td>
                                        <td>{{ $client->contact_person }}</td>
                                        <td>{{ $client->phone }}</td>
                                        <td>
                                            <span class="badge badge-{{ $client->interest_status == 'interested' ? 'success' : ($client->interest_status == 'not interested' ? 'danger' : 'secondary') }}">
                                                {{ $client->interest_status == 'interested' ? 'مهتم' : ($client->interest_status == 'not interested' ? 'غير مهتم' : 'مؤجل') }}
                                            </span>
                                        </td>
                                        <td>{{ $client->last_contact_date?->format('Y-m-d') ?? '-' }}</td>
                                        <td>
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $client->agreements->count() }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> عرض
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">لا توجد عملاء</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4">
                        {{ $clients->links() }}
                    </div>
                </div>

                <div id="agreements" class="tab-pane fade">
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>العميل</th>
                                    <th>رقم الاتفاقية</th>
                                    <th>تاريخ البداية</th>
                                    <th>تاريخ النهاية</th>
                                    <th>الحالة</th>
                                    <th>المبلغ</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($agreements as $agreement)
                                    <tr>
                                        <td><strong>{{ $agreement->client->company_name }}</strong></td>
                                        <td>{{ $agreement->agreement_number }}</td>
                                        <td>{{ $agreement->start_date?->format('Y-m-d') ?? '-' }}</td>
                                        <td>{{ $agreement->end_date?->format('Y-m-d') ?? '-' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $agreement->agreement_status == 'active' ? 'success' : 'secondary' }}">
                                                {{ $agreement->agreement_status == 'active' ? 'نشط' : 'غير نشط' }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($agreement->agreement_amount ?? 0, 2) }}</td>
                                        <td>
                                            <a href="{{ route('agreements.show', $agreement) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> عرض
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">لا توجد اتفاقيات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4">
                        {{ $agreements->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
