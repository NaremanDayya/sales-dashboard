@extends('layouts.master')

@section('title', 'نظرة عامة')
@section('page-title', 'نظرة عامة')

@section('content')
@php
    $statusLabels = ['active' => 'نشطة', 'terminated' => 'تم إيقافها', 'expired' => 'منتهية'];
    $statusColors = ['active' => 'emerald', 'terminated' => 'rose', 'expired' => 'gray'];
@endphp

<div class="flex flex-col gap-1 mb-6">
    <a href="{{ route('allClients') }}" class="self-start inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition-colors mb-3">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
        إضافة عميل
    </a>
    <h1 class="text-2xl font-bold text-gray-900">مرحبًا، {{ Auth::user()->name }}</h1>
    <p class="text-sm font-medium text-gray-500">هذا ملخص أداء المبيعات لهذا الشهر</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <x-stat-card label="معدل تحقيق الهدف" value="{{ $data['targetAchievementRate'] }}%" accent="indigo"
        :trend="abs($data['targetAchievementRate'] - 100) . '% ' . ($data['targetAchievementRate'] >= 100 ? 'فوق الهدف' : 'عن الهدف')"
        :trend-up="$data['targetAchievementRate'] >= 100"
        icon='<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>' />

    <x-stat-card label="الطلبات المعلقة" :value="$data['pendingRequestsCount']" accent="amber" trend="بحاجة إلى مراجعة" :trend-up="null"
        icon='<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' />

    <x-stat-card label="الاتفاقيات النشطة" :value="$data['activeAgreements']" accent="emerald"
        :trend="abs($data['agreementsGrowth']) . '% هذا الشهر'" :trend-up="$data['agreementsGrowth'] >= 0"
        icon='<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' />

    <x-stat-card label="إجمالي العملاء" :value="number_format($data['totalClients'])" accent="sky"
        :trend="abs($data['clientsGrowth']) . '% هذا الشهر'" :trend-up="$data['clientsGrowth'] >= 0"
        icon='<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87m6-3.13a4 4 0 10-4-4 4 4 0 004 4zm6 0a4 4 0 10-4-4"/></svg>' />
</div>

<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
        <h2 class="text-base font-bold text-gray-900">أحدث الاتفاقيات</h2>
        <a href="{{ route('allAgreements') }}" class="text-sm text-indigo-600 hover:text-indigo-700 font-semibold inline-flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            عرض الكل
        </a>
    </div>

    @if($data['latestAgreements']->isEmpty())
        <x-empty-state title="لا توجد اتفاقيات بعد" />
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">العميل</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">المندوب</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">القيمة</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">الحالة</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($data['latestAgreements'] as $agreement)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2.5">
                                    <img src="{{ $agreement->client->company_logo }}" alt="{{ $agreement->client->company_name }}" class="h-9 w-9 rounded-full object-cover shrink-0">
                                    <div class="min-w-0">
                                        <div class="text-sm font-semibold text-gray-900 truncate">{{ $agreement->client->company_name }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 text-sm font-medium text-gray-700 whitespace-nowrap">{{ $agreement->salesRep->name }}</td>
                            <td class="px-5 py-3.5 text-sm font-medium text-gray-700 whitespace-nowrap">{{ number_format($agreement->total_amount) }} ر.س</td>
                            <td class="px-5 py-3.5">
                                <x-badge :color="$statusColors[$agreement->agreement_status] ?? 'gray'">
                                    {{ $statusLabels[$agreement->agreement_status] ?? $agreement->agreement_status }}
                                </x-badge>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
