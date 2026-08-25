@extends('layouts.master')

@section('title', 'سجل عمل الموظفين')

@push('styles')
<style>
    :root {
        --primary: #4154f1;
        --success: #10b981;
        --danger: #ef4444;
        --gray-100: #f8fafc;
        --gray-200: #e2e8f0;
        --gray-400: #94a3b8;
        --gray-500: #64748b;
        --gray-700: #334155;
        --gray-900: #0f172a;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.25rem;
        margin-bottom: 1.5rem;
    }

    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        text-align: center;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .stat-card h3 {
        margin: 0;
        font-size: 2rem;
        font-weight: 700;
        color: var(--gray-900);
    }

    .stat-card p {
        margin: .35rem 0 .5rem;
        color: var(--gray-500);
        font-size: .875rem;
        font-weight: 600;
    }

    .stat-card .stat-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        color: var(--primary);
        background: rgba(65, 84, 241, .1);
    }

    .filters-card,
    .table-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
    }

    .filters-card {
        padding: 1.25rem;
    }

    .filters-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        align-items: end;
    }

    .filters-grid label {
        display: block;
        font-weight: 600;
        font-size: .8rem;
        color: var(--gray-700);
        margin-bottom: .4rem;
    }

    .filters-grid input,
    .filters-grid select {
        width: 100%;
        border: 1px solid var(--gray-200);
        border-radius: .5rem;
        padding: .55rem .75rem;
        font-size: .875rem;
    }

    .history-table {
        width: 100%;
        border-collapse: collapse;
    }

    .history-table th,
    .history-table td {
        padding: .9rem 1.25rem;
        text-align: right;
        border-bottom: 1px solid var(--gray-200);
        font-size: .875rem;
        color: var(--gray-700);
        vertical-align: middle;
    }

    .history-table th {
        background: var(--gray-100);
        font-weight: 700;
        color: var(--gray-500);
        text-transform: uppercase;
        font-size: .72rem;
        letter-spacing: .03em;
    }

    .history-table tbody tr:hover {
        background: var(--gray-100);
    }

    .employee-cell {
        display: flex;
        align-items: center;
        gap: .65rem;
    }

    .employee-cell img {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
    }

    .employee-cell .name {
        font-weight: 600;
        color: var(--gray-900);
    }

    .employee-cell .email {
        font-size: .78rem;
        color: var(--gray-500);
    }

    .period-badge {
        display: inline-block;
        padding: .25rem .6rem;
        border-radius: 9999px;
        background: rgba(65, 84, 241, .1);
        color: var(--primary);
        font-weight: 600;
        font-size: .8rem;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        padding: .3rem .7rem;
        border-radius: 9999px;
        font-weight: 700;
        font-size: .78rem;
    }

    .status-pill.active {
        background: rgba(16, 185, 129, .12);
        color: var(--success);
    }

    .status-pill.ended {
        background: rgba(239, 68, 68, .12);
        color: var(--danger);
    }

    .empty-state {
        padding: 3rem 1.5rem;
        text-align: center;
        color: var(--gray-500);
    }

    @media print {
        .no-print,
        .filters-card,
        .stats-grid {
            display: none !important;
        }
    }
</style>
@endpush

@section('content')
<div class="pagetitle mb-4 d-flex flex-wrap justify-content-between align-items-center gap-2 no-print">
    <h1 class="h4 mb-0 d-flex align-items-center gap-2">
        <i class="bi bi-clock-history text-primary"></i>
        سجل عمل الموظفين
        @if($filteredSalesRep)
            <span class="text-muted fw-normal fs-6">— {{ $filteredSalesRep->name }}</span>
            <a href="{{ route('work-history.index') }}" class="btn btn-sm btn-outline-secondary ms-2">
                <i class="bi bi-x-circle me-1"></i> إزالة الفلتر
            </a>
        @endif
    </h1>

    <div class="d-flex gap-2">
        <a href="{{ route('work-history.export', request()->query()) }}" class="btn btn-sm btn-success">
            <i class="bi bi-file-earmark-excel me-1"></i> تصدير Excel
        </a>
        <button type="button" onclick="window.print()" class="btn btn-sm btn-danger">
            <i class="bi bi-file-earmark-pdf me-1"></i> تصدير PDF
        </button>
    </div>
</div>

<!-- Filters -->
<div class="filters-card no-print">
    <form method="GET" action="{{ route('work-history.index') }}" class="filters-grid">
        @if(request()->filled('sales_rep_id'))
            <input type="hidden" name="sales_rep_id" value="{{ request('sales_rep_id') }}">
        @endif

        @if($isAdmin)
            <div>
                <label for="search">البحث بالاسم</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="ابحث باسم الموظف...">
            </div>
        @endif

        <div>
            <label for="from_date">من تاريخ</label>
            <input type="date" id="from_date" name="from_date" value="{{ request('from_date') }}">
        </div>

        <div>
            <label for="to_date">إلى تاريخ</label>
            <input type="date" id="to_date" name="to_date" value="{{ request('to_date') }}">
        </div>

        <div>
            <label for="status">الحالة</label>
            <select id="status" name="status">
                <option value="">جميع الحالات</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                <option value="ended" {{ request('status') == 'ended' ? 'selected' : '' }}>منتهي</option>
            </select>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-sm btn-primary flex-grow-1">
                <i class="bi bi-search me-1"></i> بحث
            </button>
            <a href="{{ route('work-history.index', request()->only('sales_rep_id')) }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-clockwise me-1"></i> إعادة تعيين
            </a>
        </div>
    </form>
</div>

<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <span class="stat-icon"><i class="bi bi-graph-up"></i></span>
        <h3>{{ $stats['average_days'] }}</h3>
        <p>متوسط المدة (أيام)</p>
    </div>
    <div class="stat-card">
        <span class="stat-icon"><i class="bi bi-clock"></i></span>
        <h3>{{ number_format($stats['total_work_days']) }}</h3>
        <p>إجمالي أيام العمل</p>
    </div>
    <div class="stat-card">
        <span class="stat-icon text-success" style="background: rgba(16,185,129,.12);"><i class="bi bi-play-fill"></i></span>
        <h3>{{ $stats['active_periods'] }}</h3>
        <p>فترات نشطة</p>
    </div>
    <div class="stat-card">
        <span class="stat-icon"><i class="bi bi-calendar-range"></i></span>
        <h3>{{ $stats['total_periods'] }}</h3>
        <p>إجمالي الفترات</p>
    </div>
</div>

<div class="table-container" id="print-area">
    @if($histories->isEmpty())
        <div class="empty-state">
            <i class="bi bi-inbox" style="font-size:2rem;"></i>
            <p class="mt-2 mb-0">لا توجد سجلات عمل مطابقة</p>
        </div>
    @else
        <div class="table-responsive">
            <table class="history-table">
                <thead>
                    <tr>
                        <th>الموظف</th>
                        <th>الفترة الزمنية</th>
                        <th>المدة</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($histories as $row)
                        <tr>
                            <td>
                                <div class="employee-cell">
                                    <img src="{{ $row['avatar'] }}" alt="{{ $row['name'] }}">
                                    <div>
                                        <div class="name">{{ $row['name'] }}</div>
                                        @if($row['email'])
                                            <div class="email">{{ $row['email'] }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                من {{ optional($row['start_date'])->format('Y-m-d') }}
                                إلى {{ $row['end_date'] ? $row['end_date']->format('Y-m-d') : 'الآن' }}
                            </td>
                            <td><span class="period-badge">{{ $row['period_label'] }}</span></td>
                            <td>
                                @if($row['is_active'])
                                    <span class="status-pill active"><i class="bi bi-play-fill"></i> نشط</span>
                                @else
                                    <span class="status-pill ended"><i class="bi bi-stop-fill"></i> منتهي</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-3 no-print">
            {{ $histories->links() }}
        </div>
    @endif
</div>
@endsection
