@extends('layouts.master')

@section('content')
<x-page-header title="لوحة العمولات" subtitle="عمولات المبيعات لكل مندوب وخدمة">
    <x-slot name="actions">
        <x-badge color="indigo">{{ now()->translatedFormat('F Y') }}</x-badge>
    </x-slot>
</x-page-header>

<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
    <!-- Filters -->
    <div class="bg-gray-50 px-6 py-4 border-b border-gray-100">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="month" class="block text-sm font-medium text-gray-700 mb-1.5">الشهر</label>
                <select id="month" name="month" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <label for="year" class="block text-sm font-medium text-gray-700 mb-1.5">السنة</label>
                <select id="year" name="year" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    @for($i = now()->year; $i >= now()->year - 5; $i--)
                        <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label for="sales_rep" class="block text-sm font-medium text-gray-700 mb-1.5">مندوب المبيعات</label>
                <select id="sales_rep" name="sales_rep" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">الكل</option>
                    @foreach($salesReps as $rep)
                        <option value="{{ $rep->id }}" {{ request('sales_rep') == $rep->id ? 'selected' : '' }}>
                            {{ $rep->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg text-sm font-semibold text-white hover:bg-indigo-700 transition-colors">
                    تصفية
                </button>
            </div>
        </form>
    </div>

    <!-- Stats Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-6 border-b border-gray-100">
        <x-stat-card label="إجمالي العمولات" :value="number_format($totalCommissions, 2) . ' ر.س'" accent="indigo"
            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l7-4 7 4z"/></svg>' />
        <x-stat-card label="الأعلى تحصيلاً" :value="$topPerformer->salesRep->name ?? 'لا يوجد'" accent="emerald"
            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>' />
        <x-stat-card label="الخدمة الأكثر تحقيقاً" :value="$topService->service->name ?? 'لا يوجد'" accent="sky"
            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2"/></svg>' />
        <x-stat-card label="متوسط التحقيق" :value="number_format($averageAchievement, 2) . '%'" accent="amber"
            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>' />
    </div>

    <!-- Commissions Table -->
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">مندوب المبيعات</th>
                    <th scope="col" class="px-6 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">الخدمة</th>
                    <th scope="col" class="px-6 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">الفترة</th>
                    <th scope="col" class="px-6 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">نسبة التحقيق</th>
                    <th scope="col" class="px-6 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">العمولة</th>
                    <th scope="col" class="px-6 py-3 text-end text-xs font-semibold text-gray-500 uppercase tracking-wider">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @foreach($commissions as $commission)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            <div class="flex-shrink-0 h-10 w-10 rounded-full bg-indigo-50 flex items-center justify-center">
                                <span class="text-indigo-600 font-semibold">{{ substr($commission->salesRep->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <div class="text-sm font-semibold text-gray-900">{{ $commission->salesRep->name }}</div>
                                <div class="text-sm text-gray-500">{{ $commission->salesRep->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">{{ $commission->service->name }}</div>
                        <div class="text-sm text-gray-500">الهدف: {{ number_format($commission->target->target_amount, 2) }} ر.س</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">
                            {{ DateTime::createFromFormat('!m', $commission->month)->format('F') }} {{ $commission->year }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-2">
                            <div class="w-20">
                                <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-emerald-500 rounded-full"
                                         style="width: {{ min($commission->achieved_percentage, 100) }}%"></div>
                                </div>
                            </div>
                            <div class="text-sm font-medium text-gray-900">
                                {{ number_format($commission->achieved_percentage, 2) }}%
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold text-indigo-600">
                            {{ number_format($commission->commission_amount, 2) }} ر.س
                        </div>
                        <div class="text-xs text-gray-500">
                            النسبة: {{ $commission->commission_rate }}%
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-end text-sm font-medium">
                        <div class="flex items-center justify-end gap-3">
                            <a href="{{ route('commissions.show', $commission) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                                عرض
                            </a>
                            <a href="#" class="text-emerald-600 hover:text-emerald-800 font-medium" onclick="event.preventDefault(); document.getElementById('export-form-{{ $commission->id }}').submit();">
                                تصدير
                            </a>
                            <form id="export-form-{{ $commission->id }}" action="{{ route('commissions.export', $commission) }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
        {{ $commissions->links() }}
    </div>
</div>
@endsection
