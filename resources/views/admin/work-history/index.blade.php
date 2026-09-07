@extends('layouts.master')

@section('title', 'سجل عمل الموظفين')

@push('styles')
<style>
    @media print {
        .no-print {
            display: none !important;
        }
    }
</style>
@endpush

@section('content')
<x-page-header title="سجل عمل الموظفين" :subtitle="$filteredSalesRep ? 'عرض سجل: ' . $filteredSalesRep->name : 'سجل فترات العمل لجميع مندوبي المبيعات'" class="no-print">
    <x-slot name="actions">
        @if($filteredSalesRep)
            <a href="{{ route('work-history.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 text-sm font-semibold hover:bg-gray-50 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                إزالة الفلتر
            </a>
        @endif
        <a href="{{ route('work-history.export', request()->query()) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H8a2 2 0 01-2-2V5a2 2 0 012-2h6l6 6v9a2 2 0 01-2 2z"/></svg>
            تصدير Excel
        </a>
        <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-rose-600 text-white text-sm font-semibold hover:bg-rose-700 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4H7v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
            تصدير PDF
        </button>
    </x-slot>
</x-page-header>

<!-- Filters -->
<div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 mb-6 no-print">
    <form method="GET" action="{{ route('work-history.index') }}" class="flex flex-wrap items-end gap-4">
        @if(request()->filled('sales_rep_id'))
            <input type="hidden" name="sales_rep_id" value="{{ request('sales_rep_id') }}">
        @endif

        @if($isAdmin)
            <div class="w-full sm:w-56">
                <label for="search" class="block text-xs font-semibold text-gray-500 mb-1.5">البحث بالاسم</label>
                <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="ابحث باسم الموظف..."
                    class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
        @endif

        <div class="w-full sm:w-44">
            <label for="from_date" class="block text-xs font-semibold text-gray-500 mb-1.5">من تاريخ</label>
            <input type="date" id="from_date" name="from_date" value="{{ request('from_date') }}"
                class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div class="w-full sm:w-44">
            <label for="to_date" class="block text-xs font-semibold text-gray-500 mb-1.5">إلى تاريخ</label>
            <input type="date" id="to_date" name="to_date" value="{{ request('to_date') }}"
                class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>

        <div class="w-full sm:w-40">
            <label for="status" class="block text-xs font-semibold text-gray-500 mb-1.5">الحالة</label>
            <select id="status" name="status" class="w-full rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">جميع الحالات</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                <option value="ended" {{ request('status') == 'ended' ? 'selected' : '' }}>منتهي</option>
            </select>
        </div>

        <div class="flex items-center gap-2">
            <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607Z"/></svg>
                بحث
            </button>
            <a href="{{ route('work-history.index', request()->only('sales_rep_id')) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 text-sm font-semibold hover:bg-gray-50 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                إعادة تعيين
            </a>
        </div>
    </form>
</div>

<!-- Stats -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <x-stat-card label="متوسط المدة (أيام)" :value="$stats['average_days']" accent="indigo"
        icon='<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>' />
    <x-stat-card label="إجمالي أيام العمل" :value="number_format($stats['total_work_days'])" accent="sky"
        icon='<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' />
    <x-stat-card label="فترات نشطة" :value="$stats['active_periods']" accent="emerald"
        icon='<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l14 9-14 9V3z"/></svg>' />
    <x-stat-card label="إجمالي الفترات" :value="$stats['total_periods']" accent="amber"
        icon='<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>' />
</div>

<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden" id="print-area">
    @if($histories->isEmpty())
        <x-empty-state title="لا توجد سجلات عمل مطابقة" />
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">الموظف</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">الفترة الزمنية</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">المدة</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($histories as $row)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2.5">
                                    <img src="{{ $row['avatar'] }}" alt="{{ $row['name'] }}" class="h-9 w-9 rounded-full object-cover shrink-0">
                                    <div class="min-w-0">
                                        <div class="text-sm font-medium text-gray-900 truncate">{{ $row['name'] }}</div>
                                        @if($row['email'])
                                            <div class="text-xs text-gray-500 truncate">{{ $row['email'] }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-sm text-gray-700 whitespace-nowrap">
                                من {{ optional($row['start_date'])->format('Y-m-d') }}
                                إلى {{ $row['end_date'] ? $row['end_date']->format('Y-m-d') : 'الآن' }}
                            </td>
                            <td class="px-5 py-3.5">
                                <x-badge color="indigo">{{ $row['period_label'] }}</x-badge>
                            </td>
                            <td class="px-5 py-3.5">
                                @if($row['is_active'])
                                    <x-badge color="emerald">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l14 9-14 9V3z"/></svg>
                                        نشط
                                    </x-badge>
                                @else
                                    <x-badge color="rose">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><rect x="6" y="6" width="12" height="12" rx="1"/></svg>
                                        منتهي
                                    </x-badge>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-gray-100 no-print">
            {{ $histories->links() }}
        </div>
    @endif
</div>
@endsection
