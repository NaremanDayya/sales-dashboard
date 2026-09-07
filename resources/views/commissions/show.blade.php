@extends('layouts.master')

@section('content')
<x-page-header title="تفاصيل العمولة" subtitle="ملخص أداء وعمولة مندوب المبيعات">
    <x-slot name="actions">
        <x-badge color="indigo">{{ DateTime::createFromFormat('!m', $commission->month)->format('F') }} {{ $commission->year }}</x-badge>
    </x-slot>
</x-page-header>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <!-- Sales Rep Info -->
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <h2 class="text-sm font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-100">مندوب المبيعات</h2>
        <div class="flex items-center gap-4">
            <div class="flex-shrink-0 h-14 w-14 rounded-full bg-indigo-50 flex items-center justify-center">
                <span class="text-indigo-600 text-lg font-bold">{{ substr($commission->salesRep->name, 0, 1) }}</span>
            </div>
            <div>
                <p class="text-base font-semibold text-gray-900">{{ $commission->salesRep->name }}</p>
                <p class="text-sm text-gray-500">{{ $commission->salesRep->email }}</p>
                <p class="text-sm text-gray-500">{{ $commission->salesRep->phone }}</p>
            </div>
        </div>
    </div>

    <!-- Service Info -->
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <h2 class="text-sm font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-100">تفاصيل الخدمة</h2>
        <div>
            <p class="text-base font-semibold text-gray-900">{{ $commission->service->name }}</p>
            <p class="text-sm text-gray-500 mt-1">الهدف: {{ number_format($commission->target->target_amount, 2) }} ر.س</p>
            <p class="text-sm text-gray-500">المحقق: {{ number_format($commission->total_achieved_amount, 2) }} ر.س</p>
        </div>
    </div>

    <!-- Commission Summary -->
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <h2 class="text-sm font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-100">ملخص العمولة</h2>
        <div class="space-y-2.5 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-500">النسبة:</span>
                <span class="font-medium text-gray-900">{{ $commission->commission_rate }}%</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">نسبة التحقيق:</span>
                <span class="font-medium text-gray-900">{{ number_format($commission->achieved_percentage, 2) }}%</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">المبلغ:</span>
                <span class="font-semibold text-indigo-600">{{ number_format($commission->commission_amount, 2) }} ر.س</span>
            </div>
            <div class="flex justify-between pt-2 mt-2 border-t border-gray-100">
                <span class="text-gray-500">الفترة:</span>
                <span class="font-medium text-gray-900">
                    {{ DateTime::createFromFormat('!m', $commission->month)->format('F') }} {{ $commission->year }}
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Performance Visualization -->
<div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Achievement Progress -->
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <h2 class="text-sm font-semibold text-gray-900 mb-4">نسبة تحقيق الهدف</h2>
        <div class="flex items-center justify-center">
            <div class="relative w-44 h-44">
                <svg class="w-full h-full" viewBox="0 0 100 100">
                    <circle cx="50" cy="50" r="45" fill="none" stroke="#e2e8f0" stroke-width="8"/>
                    <circle cx="50" cy="50" r="45" fill="none"
                            stroke="#10b981" stroke-width="8" stroke-linecap="round"
                            stroke-dasharray="283"
                            stroke-dashoffset="{{ 283 - (283 * min($commission->achieved_percentage, 100)) / 100 }}"/>
                    <text x="50" y="50" font-family="Tajawal, Arial" font-size="16" text-anchor="middle" dominant-baseline="middle" fill="#4b5563">
                        {{ number_format($commission->achieved_percentage, 1) }}%
                    </text>
                </svg>
            </div>
        </div>
        <div class="mt-4 text-center">
            <p class="text-sm text-gray-600">
                {{ $commission->achieved_percentage >= 100 ? 'تم تجاوز الهدف!' :
                   ($commission->achieved_percentage >= 90 ? 'قارب على تحقيق الهدف' :
                   ($commission->achieved_percentage >= 50 ? 'في منتصف الطريق' : 'بحاجة إلى تحسين')) }}
            </p>
        </div>
    </div>

    <!-- Commission Breakdown -->
    <div class="bg-white border border-gray-200 rounded-xl p-5">
        <h2 class="text-sm font-semibold text-gray-900 mb-4">تفصيل العمولة</h2>
        <div class="space-y-4">
            <div>
                <div class="flex justify-between text-sm mb-1.5">
                    <span class="text-gray-500">إجمالي المبيعات</span>
                    <span class="font-medium text-gray-900">{{ number_format($commission->total_achieved_amount, 2) }} ر.س</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="bg-sky-500 h-2 rounded-full" style="width: 100%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-sm mb-1.5">
                    <span class="text-gray-500">نسبة العمولة</span>
                    <span class="font-medium text-gray-900">{{ $commission->commission_rate }}%</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="bg-indigo-500 h-2 rounded-full" style="width: {{ $commission->commission_rate }}%"></div>
                </div>
            </div>
            <div>
                <div class="flex justify-between text-sm mb-1.5">
                    <span class="text-gray-500">عمولتك</span>
                    <span class="font-medium text-gray-900">{{ number_format($commission->commission_amount, 2) }} ر.س</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-2">
                    <div class="bg-emerald-500 h-2 rounded-full"
                         style="width: {{ ($commission->commission_amount / $commission->total_achieved_amount) * 100 }}%"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Actions -->
<div class="mt-6 flex justify-end gap-3">
    <a href="{{ route('commissions.index') }}"
       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
        رجوع للعمولات
    </a>
    <button onclick="window.print()"
            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-semibold text-white bg-sky-600 hover:bg-sky-700 transition-colors">
        طباعة التفاصيل
    </button>
    <a href="{{ route('commissions.export', $commission) }}"
       class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-semibold text-white bg-emerald-600 hover:bg-emerald-700 transition-colors">
        تصدير PDF
    </a>
</div>
@endsection
