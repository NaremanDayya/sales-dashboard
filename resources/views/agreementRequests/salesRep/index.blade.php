@extends('layouts.master')
@section('title', 'All Edit Requests')

@section('content')
<x-page-header title="طلبات تعديل العملاء" subtitle="جميع طلبات التعديل المقدَّمة">
    <x-slot name="actions">
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" type="button" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition-colors">
                <i class="bi bi-funnel"></i>تصفية
            </button>
            <div x-show="open" @click.away="open = false" x-cloak class="absolute end-0 mt-2 w-56 bg-white border border-gray-200 rounded-xl shadow-lg py-1 z-20">
                <a href="{{ request()->fullUrlWithQuery(['status' => '']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">كل الطلبات</a>
                <div class="my-1 border-t border-gray-100"></div>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">قيد الانتظار</a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'approved']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">تمت الموافقة</a>
                <a href="{{ request()->fullUrlWithQuery(['status' => 'rejected']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">مرفوض</a>
                <div class="my-1 border-t border-gray-100"></div>
                <a href="{{ request()->fullUrlWithQuery(['urgent' => 'true']) }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">عاجل (أقدم من يوم)</a>
            </div>
        </div>
    </x-slot>
</x-page-header>

<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
        <h5 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
            <i class="bi bi-list-check text-indigo-500"></i>قائمة الطلبات
        </h5>
        <div class="text-xs text-gray-400">
            عرض {{ $requests->firstItem() }} - {{ $requests->lastItem() }} من {{ $requests->total() }} طلب
        </div>
    </div>

    @if ($requests->count())
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">العميل</th>
                        <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">النوع</th>
                        <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">الوصف</th>
                        <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">التاريخ</th>
                        <th class="px-4 py-3 text-end text-xs font-semibold text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach ($requests as $request)
                    <tr class="hover:bg-gray-50 transition-colors {{ $request->created_at < now()->subDay() && $request->status === 'pending' ? 'bg-amber-50/40' : '' }}">
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-700">
                            {{ $loop->iteration + ($requests->currentPage() - 1) * $requests->perPage() }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-indigo-50 text-indigo-600">
                                    <i class="bi bi-building"></i>
                                </span>
                                <div>
                                    <p class="text-sm font-semibold text-gray-900">{{ $request->client->company_name }}</p>
                                    <p class="text-xs text-gray-400">ID: {{ $request->client_id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <x-badge color="indigo">{{ \App\Models\ClientEditRequest::REQUEST_TYPES[$request->request_type] ?? ucfirst($request->request_type) }}</x-badge>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm text-gray-600 truncate max-w-[200px]" title="{{ $request->description }}">
                                {{ $request->description }}
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @if($request->status === 'approved')
                                <x-badge color="emerald"><i class="bi bi-check-circle"></i> {{ ucfirst($request->status) }}</x-badge>
                            @elseif($request->status === 'rejected')
                                <x-badge color="rose"><i class="bi bi-x-circle"></i> {{ ucfirst($request->status) }}</x-badge>
                            @else
                                <x-badge color="amber"><i class="bi bi-hourglass"></i> {{ ucfirst($request->status) }}</x-badge>
                            @endif
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="text-xs text-gray-400" title="{{ $request->created_at->format('M d, Y h:i A') }}">
                                {{ $request->created_at->diffForHumans() }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-end">
                            <a href="{{ route('sales-reps.client-requests.show', ['client' => $request->client_id, 'client_request' => $request->id]) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full border border-indigo-200 text-indigo-700 text-xs font-semibold hover:bg-indigo-50 transition-colors">
                                <i class="bi bi-eye"></i> عرض
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <x-empty-state title="لا توجد طلبات تعديل" description="عندما يُقدّم العملاء طلبات تعديل، ستظهر هنا." />
    @endif

    @if ($requests->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
            <div class="text-xs text-gray-400">
                عرض {{ $requests->firstItem() }} - {{ $requests->lastItem() }} من {{ $requests->total() }}
            </div>
            <div>
                {{ $requests->onEachSide(1)->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
