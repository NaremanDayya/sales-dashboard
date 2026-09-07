@extends('layouts.master')
@section('title', 'خدمات الشركة')
@section('content')

<x-page-header title="خدمات الشركة" subtitle="إدارة الخدمات المقدمة وأهداف المبيعات الخاصة بكل خدمة">
    @if (Auth::user()->hasRole('admin'))
        <x-slot name="actions">
            <a href="{{ route('services.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                إضافة خدمة جديدة
            </a>
        </x-slot>
    @endif
</x-page-header>

@if($services->isEmpty())
    <x-empty-state
        title="لا توجد خدمات بعد"
        description="ابدأ بإضافة أول خدمة لتظهر هنا مع أهدافها ونسبة عمولتها."
        icon='<svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2"/></svg>'
        class="bg-white border border-gray-200 rounded-xl"
    />
@else
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-5 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">نوع الخدمة</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">التارجت</th>
                        @if (Auth::user()->hasRole('admin'))
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">نسبة العمولة</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">الاتفاقيات النشطة</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">الاتفاقيات غير النشطة</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">الإجراءات</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach ($services as $service)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-indigo-600">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01"></path>
                                        </svg>
                                    </span>
                                    <div class="text-sm font-semibold text-gray-900">{{ $service->name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <div class="text-sm text-gray-700">
                                    {{ $service->is_flat_price
                                        ? number_format($service->target_amount) . ' ' . config('app.currency')
                                        : number_format($service->target_amount) }}
                                </div>
                            </td>
                            @if (Auth::user()->hasRole('admin'))
                                <td class="px-6 py-4 text-center whitespace-nowrap text-sm text-gray-700">
                                    {{ rtrim(rtrim(number_format($service->commission_rate, 2, '.', ''), '0'), '.') }}%
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <x-badge color="emerald">{{ $service->active_agreements_count }}</x-badge>
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <x-badge color="gray">{{ $service->inactive_agreements_count }}</x-badge>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex justify-center items-center gap-2">
                                        <a href="{{ route('services.edit', $service->id) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition-colors">
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 11l6 6m-3-6v6m0-6h6"/>
                                            </svg>
                                            تعديل
                                        </a>
                                        <form action="{{ route('services.destroy', $service->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الخدمة؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-rose-700 bg-rose-50 hover:bg-rose-100 transition-colors">
                                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                                حذف
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if(method_exists($services, 'links'))
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $services->links() }}
            </div>
        @endif
    </div>
@endif
@endsection
