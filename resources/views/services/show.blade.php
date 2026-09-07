@extends('layouts.master')
@section('title', 'عرض الخدمة')
@section('content')

<x-page-header :title="$service->name" subtitle="تفاصيل الخدمة">
    <x-slot name="actions">
        <a href="{{ route('services.edit', $service->id) }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition-colors">
            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536M9 11l6 6m-3-6v6m0-6h6"/>
            </svg>
            تعديل
        </a>
        <a href="{{ route('services.index') }}"
           class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg border border-gray-300 text-gray-700 text-sm font-semibold hover:bg-gray-50 transition-colors">
            رجوع
        </a>
    </x-slot>
</x-page-header>

<div class="max-w-2xl bg-white border border-gray-200 rounded-xl shadow-sm divide-y divide-gray-100">
    <div class="px-6 py-4">
        <div class="text-xs font-medium text-gray-400 mb-1">الوصف</div>
        <div class="text-sm font-medium text-gray-900">{{ $service->description }}</div>
    </div>
    <div class="px-6 py-4">
        <div class="text-xs font-medium text-gray-400 mb-1">الهدف الافتراضي</div>
        <div class="text-sm font-medium text-gray-900">{{ $service->target_percentage }}</div>
    </div>
</div>
@endsection
