@extends('layouts.master')

@section('title', 'إدارة الفرق - Admin')

@push('styles')
<style>
    :root {
        --primary: #4154f1;
        --primary-light: #6a7ef9;
        --secondary: #10b981;
        --danger: #ef4444;
        --warning: #f59e0b;
        --gray-100: #f8fafc;
        --gray-200: #e2e8f0;
        --gray-300: #cbd5e1;
        --gray-400: #94a3b8;
        --gray-500: #64748b;
        --gray-600: #475569;
        --gray-700: #334155;
        --gray-800: #1e293b;
        --gray-900: #0f172a;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        color: white;
    }

    .stat-content h3 {
        margin: 0;
        font-size: 2rem;
        font-weight: 700;
    }

    .stat-content p {
        margin: 0;
        color: #6c757d;
        font-size: 0.875rem;
    }

    .table-container {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
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
        background-color: var(--gray-100);
        color: var(--gray-600);
        font-weight: 600;
        padding: 12px 15px;
        text-align: right;
        border-bottom: 2px solid var(--gray-200);
    }

    .data-table tbody tr {
        transition: background-color 0.2s ease;
    }

    .data-table tbody tr:hover {
        background-color: var(--gray-100);
    }

    .data-table tbody td {
        padding: 12px 15px;
        border-bottom: 1px solid var(--gray-200);
        text-align: right;
        vertical-align: middle;
    }

    .member-avatar-sm {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
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
        transform: translateY(-1px);
    }

    .btn-outline-primary {
        background-color: white;
        color: var(--primary);
        border: 1px solid var(--primary);
    }

    .btn-outline-primary:hover {
        background-color: var(--gray-100);
    }

    .btn-success {
        background-color: var(--secondary);
        color: white;
    }

    .btn-success:hover {
        background-color: #059669;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    .badge-blue {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .badge-green {
        background-color: #d1fae5;
        color: #065f46;
    }

    .badge-purple {
        background-color: #e9d5ff;
        color: #6b21a8;
    }

    .badge-yellow {
        background-color: #fef3c7;
        color: #92400e;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">إدارة الفرق والمندوبين</h1>
            <p class="text-gray-600 mt-2">عرض شامل لجميع المديرين وأعضاء الفريق</p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $totalStats['total_sales_reps'] }}</h3>
                    <p>إجمالي المندوبين</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $totalStats['total_managers'] }}</h3>
                    <p>المديرين</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-building"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $totalStats['total_clients'] }}</h3>
                    <p>إجمالي العملاء</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon bg-info">
                    <i class="fas fa-file-contract"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $totalStats['total_agreements'] }}</h3>
                    <p>إجمالي الاتفاقيات</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon" style="background-color: #8b5cf6;">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3>{{ $totalStats['active_agreements'] }}</h3>
                    <p>الاتفاقيات النشطة</p>
                </div>
            </div>
        </div>

        <div class="table-container">
            <div class="table-header">
                <h2 class="table-title">المديرين وفرقهم</h2>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.team.all-clients') }}" class="btn btn-outline-primary">
                        <i class="fas fa-users"></i> جميع العملاء
                    </a>
                    <a href="{{ route('admin.team.all-agreements') }}" class="btn btn-outline-primary">
                        <i class="fas fa-file-contract"></i> جميع الاتفاقيات
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>المدير</th>
                            <th>البريد الإلكتروني</th>
                            <th>عدد أعضاء الفريق</th>
                            <th>عدد العملاء</th>
                            <th>عدد الاتفاقيات</th>
                            <th>الاتفاقيات النشطة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($managers as $manager)
                            @php
                                $teamClientsCount = $manager->getTeamClientsQuery()->count();
                                $teamAgreementsCount = $manager->getTeamAgreementsQuery()->count();
                                $activeAgreementsCount = $manager->getTeamAgreementsQuery()->where('agreement_status', 'active')->count();
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $manager->user->personal_image }}" alt="{{ $manager->name }}" class="member-avatar-sm">
                                        <strong>{{ $manager->name }}</strong>
                                    </div>
                                </td>
                                <td>{{ $manager->user->email }}</td>
                                <td>
                                    <span class="badge badge-yellow">
                                        {{ $manager->teamMembers->count() }} أعضاء
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-blue">
                                        {{ $teamClientsCount }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-green">
                                        {{ $teamAgreementsCount }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-purple">
                                        {{ $activeAgreementsCount }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.team.manager.team', $manager) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> عرض الفريق
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <div class="text-gray-500">
                                        <i class="fas fa-user-tie fa-3x mb-3"></i>
                                        <p>لا يوجد مديرين بعد</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="table-container">
            <div class="table-header">
                <h2 class="table-title">جميع المندوبين</h2>
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>اسم المندوب</th>
                            <th>البريد الإلكتروني</th>
                            <th>المدير</th>
                            <th>تاريخ الالتحاق</th>
                            <th>عدد العملاء</th>
                            <th>عدد الاتفاقيات</th>
                            <th>الاتفاقيات النشطة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($allSalesReps as $salesRep)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $salesRep->user->personal_image }}" alt="{{ $salesRep->name }}" class="member-avatar-sm">
                                        <strong>{{ $salesRep->name }}</strong>
                                    </div>
                                </td>
                                <td>{{ $salesRep->user->email }}</td>
                                <td>
                                    @if($salesRep->manager)
                                        <span class="badge badge-blue">
                                            {{ $salesRep->manager->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">لا يوجد</span>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($salesRep->start_work_date)->format('Y-m-d') }}</td>
                                <td>
                                    <span class="badge badge-blue">
                                        {{ $salesRep->clients->count() }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-green">
                                        {{ $salesRep->agreements->count() }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-purple">
                                        {{ $salesRep->active_agreements_count }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.team.member.details', $salesRep) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> عرض التفاصيل
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-gray-500">
                                        <i class="fas fa-users-slash fa-3x mb-3"></i>
                                        <p>لا يوجد مندوبين بعد</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
