@extends('layouts.master')

@section('title', 'طلبات تعديل بيانات العملاء')

@section('content')
<x-page-header title="طلبات تعديل بيانات العملاء" subtitle="جميع طلبات تعديل بيانات العملاء المقدَّمة من المندوبين" />

<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
    @if($clientEditRequests->isEmpty())
        <x-empty-state title="لا توجد طلبات تعديل حالياً" />
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="requestsTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">#</th>
                        <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">العميل</th>
                        <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">سفير العلامة التجارية</th>
                        <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">نوع الطلب</th>
                        <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">الوصف</th>
                        <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">الحقل المعدل</th>
                        <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">تاريخ الطلب</th>
                        <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">الحالة</th>
                        <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">ملاحظات الإدارة</th>
                        <th class="px-4 py-3 text-end text-xs font-semibold text-gray-500 uppercase tracking-wider">الإجراءات</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach ($clientEditRequests as $request)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-700">{{ $loop->iteration }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $request->client->company_name ?? 'غير متوفر' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $request->salesRep->name ?? 'غير معروف' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                <x-badge color="indigo">{{ $request->request_type }}</x-badge>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm text-gray-600 truncate block max-w-[150px]" title="{{ $request->description }}">
                                    {{ \Illuminate\Support\Str::limit($request->description, 30) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $request->edited_field ?? '-' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-xs text-gray-400">{{ $request->created_at->format('Y-m-d') }}</td>
                            <td class="px-4 py-3 whitespace-nowrap">
                                @php
                                    $badgeColor = match($request->status) {
                                        'pending' => 'amber',
                                        'approved' => 'emerald',
                                        'rejected' => 'rose',
                                        default => 'gray'
                                    };
                                @endphp
                                <x-badge :color="$badgeColor">{{ __('status.' . $request->status) }}</x-badge>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500">{{ $request->notes ?? '-' }}</td>
                            <td class="px-4 py-3 whitespace-nowrap text-end">
                                <a href="{{ route('admin.client-edit-requests.show', $request->id) }}"
                                   class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition-colors"
                                   title="عرض التفاصيل">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($clientEditRequests->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 flex justify-center">
                {{ $clientEditRequests->links() }}
            </div>
        @endif
    @endif
</div>

@push('scripts')
<script>
    $(document).ready(function() {
        // Enable tooltips
        $('[data-bs-toggle="tooltip"]').tooltip({
            placement: 'top'
        });
    });
</script>
@endpush
@endsection
