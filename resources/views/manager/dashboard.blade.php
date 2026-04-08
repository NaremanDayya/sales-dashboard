@extends('layouts.master')

@section('title', 'لوحة تحكم المدير')

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

    .impersonation-badge {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: #fef3c7;
        border: 1px solid #fbbf24;
        border-radius: 6px;
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
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-6">
    <div class="max-w-full mx-auto px-4 sm:px-6 lg:px-8">
        @if(auth()->user()->isImpersonatingManager())
            <div class="impersonation-badge mb-4">
                <span class="font-semibold">عرض كـ {{ $salesRep->name }}</span>
                <form action="{{ route('admin.impersonation.stop') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">إنهاء العرض</button>
                </form>
            </div>
        @endif

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon bg-primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $teamStats['total_members'] }}</h3>
                <p>Team Members</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-success">
                <i class="fas fa-building"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $teamStats['total_clients'] }}</h3>
                <p>Total Clients</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-info">
                <i class="fas fa-file-contract"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $teamStats['total_agreements'] }}</h3>
                <p>Total Agreements</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-warning">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $teamStats['active_agreements'] }}</h3>
                <p>Active Agreements</p>
            </div>
        </div>
    </div>

        <div class="table-container">
            <div class="table-header">
                <h2 class="table-title">أعضاء الفريق</h2>
                <div class="d-flex gap-2">
                    <a href="{{ route('manager.team.clients', request()->only('manager_id')) }}" class="btn btn-outline-primary">
                        <i class="fas fa-users"></i> جميع العملاء
                    </a>
                    <a href="{{ route('manager.team.agreements', request()->only('manager_id')) }}" class="btn btn-outline-primary">
                        <i class="fas fa-file-contract"></i> جميع الاتفاقيات
                    </a>
                </div>
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>اسم المندوب</th>
                            <th>البريد الإلكتروني</th>
                            <th>تاريخ الالتحاق</th>
                            <th>مدة العمل</th>
                            <th>عدد العملاء</th>
                            <th>عدد الاتفاقيات</th>
                            <th>الاتفاقيات النشطة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teamMembers as $member)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <img src="{{ $member->user->personal_image }}" alt="{{ $member->name }}" class="member-avatar-sm">
                                        <strong>{{ $member->name }}</strong>
                                    </div>
                                </td>
                                <td>{{ $member->user->email }}</td>
                                <td>{{ \Carbon\Carbon::parse($member->start_work_date)->format('Y-m-d') }}</td>
                                <td>{{ $member->work_duration }}</td>
                                <td>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ $member->clients->count() }}
                                    </span>
                                </td>
                                <td>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ $member->agreements->count() }}
                                    </span>
                                </td>
                                <td>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        {{ $member->active_agreements_count }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('manager.team-member.details', $member) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> عرض التفاصيل
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <div class="text-gray-500">
                                        <i class="fas fa-users-slash fa-3x mb-3"></i>
                                        <p>لا يوجد أعضاء فريق معينين بعد</p>
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
