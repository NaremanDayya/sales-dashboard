@extends('layouts.master')

@section('title', 'عملاء الفريق - ' . $manager->name)

@push('styles')
<style>
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
        text-decoration: none;
    }

    .btn-outline-secondary {
        background-color: white;
        color: #6c757d;
        border: 1px solid #e2e8f0;
    }

    .btn-outline-primary {
        background-color: white;
        color: #4154f1;
        border: 1px solid #4154f1;
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
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-4">
            <a href="{{ route('admin.team.manager.team', $manager) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right"></i> العودة لفريق المدير
            </a>
        </div>

        <div class="table-container">
            <div class="table-header">
                <h2 class="table-title">عملاء فريق {{ $manager->name }}</h2>
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>اسم الشركة</th>
                            <th>المندوب</th>
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
                                <td>{{ $client->salesRep->name }}</td>
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
                                <td colspan="8" class="text-center text-muted py-4">لا توجد عملاء</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-4">
                {{ $clients->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
