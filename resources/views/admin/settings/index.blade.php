@extends('layouts.master')

@section('title', 'الإعدادات')
@section('page-title', 'الإعدادات')

@section('content')
<x-page-header title="الإعدادات" subtitle="إعدادات عامة للنظام" />

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 max-w-4xl">
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
        <h2 class="text-base font-bold text-gray-900 mb-1">عدد أيام التأخير</h2>
        <p class="text-sm text-gray-500 mb-4">عدد الأيام التي يُعتبر بعدها العميل "متأخر التواصل" في جميع أنحاء النظام.</p>

        <form method="POST" action="{{ route('settings.update') }}" class="flex items-end gap-3">
            @csrf
            <div class="flex-1">
                <x-input-label for="late_customer_days" value="عدد الأيام" />
                <x-text-input id="late_customer_days" name="late_customer_days" type="number" min="1" max="30"
                    class="mt-1 w-full" :value="old('late_customer_days', $lateDays)" required />
                <x-input-error :messages="$errors->get('late_customer_days')" class="mt-2" />
            </div>
            <x-primary-button>حفظ</x-primary-button>
        </form>
    </div>

    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-6">
        <h2 class="text-base font-bold text-gray-900 mb-1">نسبة تحقيق التارجت المطلوبة للعمولة</h2>
        <p class="text-sm text-gray-500 mb-4">الحد الأدنى لنسبة تحقيق الهدف الشهري حتى تُستحق العمولة عليه.</p>

        <form method="POST" action="{{ route('settings.updateCommissionThreshold') }}" class="flex items-end gap-3">
            @csrf
            <div class="flex-1">
                <x-input-label for="commission_threshold" value="النسبة المطلوبة (%)" />
                <x-text-input id="commission_threshold" name="commission_threshold" type="number" min="0" max="100" step="0.01"
                    class="mt-1 w-full" :value="old('commission_threshold', $commissionThreshold)" required />
                <x-input-error :messages="$errors->get('commission_threshold')" class="mt-2" />
            </div>
            <x-primary-button>حفظ</x-primary-button>
        </form>
    </div>
</div>
@endsection
