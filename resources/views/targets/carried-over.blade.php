@extends('layouts.master')

@section('title', 'المرحل حسب السنوات')
@section('page-title', 'المرحل حسب السنوات')

@section('content')
@php
    $months = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
    $years = range(now()->year, ($salesRep->start_work_date?->year ?? now()->year));
@endphp

<x-page-header :title="'المرحل حسب السنوات — ' . $salesRep->name" subtitle="القيمة المرحلة (مديونية أو رصيد إضافي) لكل خدمة، شهرًا بشهر">
    <x-slot name="actions">
        <a href="{{ route('sales-rep.targets.index', $salesRep->id) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 text-sm font-semibold hover:bg-gray-50 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            رجوع لجدول التارجت
        </a>
    </x-slot>
</x-page-header>

<form method="GET" class="flex items-center gap-3 mb-4">
    <label for="year" class="text-sm font-medium text-gray-700">السنة</label>
    <select id="year" name="year" onchange="this.form.submit()"
        class="rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
        @foreach($years as $year)
            <option value="{{ $year }}" {{ $year == $selectedYear ? 'selected' : '' }}>{{ $year }}</option>
        @endforeach
    </select>
</form>

<div class="mb-4 flex flex-wrap items-center gap-4 text-xs text-gray-500">
    <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-rose-500"></span> مديونية (لم يتحقق التارجت)</span>
    <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-emerald-500"></span> رصيد إضافي (تحقيق زائد)</span>
    <span class="inline-flex items-center gap-1.5"><span class="h-2.5 w-2.5 rounded-full bg-gray-300"></span> غير محتسب (قبل الالتحاق أو شهر مستقبلي)</span>
</div>

<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider sticky right-0 bg-gray-50">الخدمة</th>
                    @foreach($months as $label)
                        <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider whitespace-nowrap">{{ $label }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($Rows as $row)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 text-sm font-semibold text-gray-900 whitespace-nowrap sticky right-0 bg-white">{{ $row['service_type'] }}</td>
                        @for($m = 1; $m <= 12; $m++)
                            @php $value = $row["month_$m"]; @endphp
                            <td class="px-3 py-3 text-sm text-center font-medium {{ $value === '-' ? 'text-gray-300' : ($value < 0 ? 'text-rose-600' : ($value > 0 ? 'text-emerald-600' : 'text-gray-400')) }}">
                                {{ $value === '-' ? '-' : number_format($value) }}
                            </td>
                        @endfor
                    </tr>
                @empty
                    <tr><td colspan="13"><x-empty-state title="لا توجد بيانات لهذه السنة" /></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
