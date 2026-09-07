@extends('layouts.master')

@section('content')
<x-page-header title="اتفاقيات الفريق" subtitle="جميع الاتفاقيات التي يديرها فريقك">
    <x-slot name="actions">
        <a href="{{ route('manager.dashboard') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 text-sm font-semibold hover:bg-gray-50 transition-colors">
            <i class="fas fa-arrow-right"></i> رجوع للوحة التحكم
        </a>
    </x-slot>
</x-page-header>

<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
    @if($agreements->isEmpty())
        <x-empty-state title="لا توجد اتفاقيات" />
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">العميل</th>
                        <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">مندوب المبيعات</th>
                        <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">رقم الاتفاقية</th>
                        <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">تاريخ البداية</th>
                        <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">تاريخ النهاية</th>
                        <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">القيمة</th>
                        <th class="px-4 py-3 text-end text-xs font-semibold text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($agreements as $agreement)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $agreement->client->company_name }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $agreement->salesRep->user->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $agreement->agreement_number }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $agreement->start_date?->format('Y-m-d') }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $agreement->end_date?->format('Y-m-d') }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <x-badge :color="$agreement->agreement_status == 'active' ? 'emerald' : 'gray'">{{ ucfirst($agreement->agreement_status) }}</x-badge>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ number_format($agreement->agreement_amount ?? 0, 2) }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-end">
                                <a href="{{ route('agreements.show', $agreement) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-semibold hover:bg-indigo-100 transition-colors">عرض</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <x-empty-state title="لا توجد اتفاقيات" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $agreements->links() }}
        </div>
    @endif
</div>
@endsection
