@extends('layouts.master')

@section('title', 'لوحة تحكم المدير')

@section('content')
<x-page-header title="لوحة تحكم المدير" subtitle="أداء فريقك: الأعضاء، العملاء، والاتفاقيات">
    <x-slot name="actions">
        <a href="{{ route('manager.team.clients') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 text-sm font-semibold hover:bg-gray-50 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            جميع العملاء
        </a>
        <a href="{{ route('manager.team.agreements') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 text-sm font-semibold hover:bg-gray-50 transition-colors">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            جميع الاتفاقيات
        </a>
    </x-slot>
</x-page-header>

@if(auth()->user()->isImpersonatingManager())
    <div class="mb-4 flex items-center gap-3 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
        <span class="font-semibold">عرض كـ {{ $salesRep->name }}</span>
        <form action="{{ route('admin.impersonation.stop') }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="text-amber-700 hover:text-amber-900 font-semibold underline">إنهاء العرض</button>
        </form>
    </div>
@endif

<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
    <x-stat-card label="أعضاء الفريق" :value="$teamStats['total_members']" accent="indigo"
        icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>' />
    <x-stat-card label="إجمالي العملاء" :value="$teamStats['total_clients']" accent="emerald"
        icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"/></svg>' />
    <x-stat-card label="إجمالي الاتفاقيات" :value="$teamStats['total_agreements']" accent="sky"
        icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>' />
    <x-stat-card label="الاتفاقيات النشطة" :value="$teamStats['active_agreements']" accent="amber"
        icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>' />
</div>

<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100">
        <h2 class="text-sm font-semibold text-gray-900">أعضاء الفريق</h2>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200" style="min-width: 900px;">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">اسم المندوب</th>
                    <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">البريد الإلكتروني</th>
                    <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">تاريخ الالتحاق</th>
                    <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">مدة العمل</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">عدد العملاء</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">عدد الاتفاقيات</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">الاتفاقيات النشطة</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">الإجراءات</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($teamMembers as $member)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-2">
                                <img src="{{ $member->user->personal_image }}" alt="{{ $member->name }}" class="h-9 w-9 rounded-full object-cover border border-gray-200">
                                <span class="text-sm font-semibold text-gray-900">{{ $member->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $member->user->email }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ \Carbon\Carbon::parse($member->start_work_date)->format('Y-m-d') }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $member->work_duration }}</td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <x-badge color="indigo">{{ $member->clients->count() }}</x-badge>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <x-badge color="emerald">{{ $member->agreements->count() }}</x-badge>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <x-badge color="sky">{{ $member->active_agreements_count }}</x-badge>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-center">
                            <a href="{{ route('manager.team-member.details', $member) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-semibold hover:bg-indigo-100 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                عرض التفاصيل
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <x-empty-state title="لا يوجد أعضاء فريق معينين بعد" />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
