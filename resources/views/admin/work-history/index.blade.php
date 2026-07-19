@extends('layouts.master')

@section('title', 'سجل العمل')

@push('styles')
<style>
    :root {
        --primary: #4154f1;
        --gray-100: #f8fafc;
        --gray-200: #e2e8f0;
        --gray-500: #64748b;
        --gray-700: #334155;
        --gray-900: #0f172a;
    }

    .table-container {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
    }

    .table-header {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        justify-content: space-between;
        align-items: center;
        padding: 1.25rem;
        border-bottom: 1px solid var(--gray-200);
    }

    .table-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--gray-900);
        margin: 0;
        display: flex;
        align-items: center;
        gap: .5rem;
    }

    .filters-bar {
        display: flex;
        flex-wrap: wrap;
        gap: .75rem;
        padding: 1.25rem;
        border-bottom: 1px solid var(--gray-200);
        background: var(--gray-100);
    }

    .filters-bar input,
    .filters-bar select {
        border: 1px solid var(--gray-200);
        border-radius: .5rem;
        padding: .5rem .75rem;
        font-size: .9rem;
    }

    .history-table {
        width: 100%;
        border-collapse: collapse;
    }

    .history-table th,
    .history-table td {
        padding: .85rem 1.25rem;
        text-align: right;
        border-bottom: 1px solid var(--gray-200);
        font-size: .9rem;
        color: var(--gray-700);
    }

    .history-table th {
        background: var(--gray-100);
        font-weight: 700;
        color: var(--gray-500);
        text-transform: uppercase;
        font-size: .75rem;
        letter-spacing: .03em;
    }

    .history-table tbody tr:hover {
        background: var(--gray-100);
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

    .empty-state {
        padding: 3rem 1.5rem;
        text-align: center;
        color: var(--gray-500);
    }
</style>
@endpush

@section('content')
<div class="pagetitle mb-4">
    <h1 class="h4 mb-1 d-flex align-items-center gap-2">
        <i class="bi bi-calendar-check text-primary"></i>
        سجل العمل
        @if($filteredSalesRep)
            <span class="text-muted fw-normal">— {{ $filteredSalesRep->name }}</span>
        @endif
    </h1>
</div>

<div class="table-container">
    <div class="table-header">
        <h2 class="table-title">
            <i class="bi bi-clock-history"></i>
            {{ $filteredSalesRep ? 'سجل عمل ' . $filteredSalesRep->name : 'جميع سجلات العمل' }}
        </h2>

        @if($isAdmin && $filteredSalesRep)
            <a href="{{ route('work-history.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-x-circle me-1"></i> إزالة الفلتر
            </a>
        @endif
    </div>

    <form method="GET" action="{{ route('work-history.index') }}" class="filters-bar">
        @if(request()->filled('sales_rep_id'))
            <input type="hidden" name="sales_rep_id" value="{{ request('sales_rep_id') }}">
        @endif

        @if($isAdmin)
            <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث بالاسم..." class="flex-grow-1" style="min-width:200px;">
        @endif

        <select name="year" onchange="this.form.submit()">
            <option value="">كل السنوات</option>
            @foreach($years as $year)
                <option value="{{ $year }}" {{ (string) request('year') === (string) $year ? 'selected' : '' }}>{{ $year }}</option>
            @endforeach
        </select>

        <button type="submit" class="btn btn-sm btn-primary">
            <i class="bi bi-search me-1"></i> بحث
        </button>

        @if(request()->filled('search') || request()->filled('year'))
            <a href="{{ route('work-history.index', request()->only('sales_rep_id')) }}" class="btn btn-sm btn-outline-secondary">
                إعادة تعيين
            </a>
        @endif
    </form>

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
                        <th>الاسم</th>
                        <th>تاريخ البداية</th>
                        <th>تاريخ النهاية</th>
                        <th>مدة العمل</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($histories as $history)
                        <tr>
                            <td class="fw-semibold">{{ $history->sales_rep_name }}</td>
                            <td>{{ optional($history->start_date)->format('Y-m-d') }}</td>
                            <td>{{ optional($history->end_date)->format('Y-m-d') }}</td>
                            <td><span class="period-badge">{{ $history->period }}</span></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-3">
            {{ $histories->links() }}
        </div>
    @endif
</div>
@endsection
