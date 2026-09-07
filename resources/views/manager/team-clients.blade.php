@extends('layouts.master')

@section('content')
<x-page-header title="عملاء الفريق" subtitle="جميع العملاء الذين يديرهم فريقك">
    <x-slot name="actions">
        <a href="{{ route('manager.dashboard') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 text-sm font-semibold hover:bg-gray-50 transition-colors">
            <i class="fas fa-arrow-right"></i> رجوع للوحة التحكم
        </a>
    </x-slot>
</x-page-header>

<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
    @if($clients->isEmpty())
        <x-empty-state title="لا يوجد عملاء" />
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">الشركة</th>
                        <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">مندوب المبيعات</th>
                        <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">الشخص المسؤول</th>
                        <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">الجوال</th>
                        <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">حالة الاهتمام</th>
                        <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">آخر تواصل</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">الاتفاقيات</th>
                        <th class="px-4 py-3 text-end text-xs font-semibold text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($clients as $client)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <img src="{{ $client->company_logo }}" alt="{{ $client->company_name }}" class="h-8 w-8 rounded-lg object-cover border border-gray-200">
                                    <span class="text-sm font-semibold text-gray-900">{{ $client->company_name }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $client->salesRep->user->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $client->contact_person }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $client->phone }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @php
                                    $badgeColor = $client->interest_status == 'interested' ? 'emerald' : ($client->interest_status == 'not interested' ? 'rose' : 'gray');
                                @endphp
                                <x-badge :color="$badgeColor">{{ ucfirst($client->interest_status) }}</x-badge>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $client->last_contact_date?->format('Y-m-d') }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-center text-sm text-gray-600">{{ $client->agreements->count() }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-end">
                                <a href="{{ route('clients.show.manager', $client) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-semibold hover:bg-indigo-100 transition-colors">عرض</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <x-empty-state title="لا يوجد عملاء" />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-100">
            {{ $clients->links() }}
        </div>
    @endif
</div>
@endsection
