@extends('layouts.master')
@section('title' , 'أجهزة تسجيل الدخول للمندوبين')
@section('content')
<x-page-header title="إدارة أجهزة تسجيل الدخول للمندوبين" subtitle="مراقبة عناوين IP المستخدمة لتسجيل الدخول، والسماح أو الحظر حسب الحاجة" />

<!-- Search -->
<form method="GET" class="mb-6 flex flex-wrap items-center gap-3">
    <div class="relative flex-1 min-w-[220px] max-w-sm">
        <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
        </div>
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="اسم المندوب"
            class="w-full ps-9 pe-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
        />
    </div>

    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
        بحث
    </button>

    @if (request('search'))
        <a href="{{ route('admin.sales-rep-ips.index') }}" class="text-sm text-gray-500 hover:text-gray-700 underline">
            إعادة تعيين
        </a>
    @endif
</form>

@forelse ($salesReps as $salesRep)
    <div class="bg-white border border-gray-200 rounded-xl shadow-sm p-5 mb-6">
        <h2 class="text-base font-semibold text-gray-900 mb-4">{{ $salesRep->user->name ?? 'اسم غير متوفر' }}</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse ($salesRep->loginIps as $ip)
                <div class="border border-gray-200 rounded-lg p-4">
                    <p class="text-sm font-medium text-gray-500 flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25"/></svg>
                        عنوان IP: <span class="text-gray-900 font-semibold ltr-number" dir="ltr">{{ $ip->ip_address }}</span>
                    </p>

                    <p class="text-sm text-gray-500 mt-2">التاريخ: {{ $ip->created_at->format('Y-m-d H:i') }}</p>
                    <p class="text-sm text-gray-500 mt-1">الموقع: {{ $ip->location ?? 'غير معروف' }}</p>
                    <p class="text-sm mt-2">
                        الحالة:
                        @if ($ip->is_blocked)
                            <x-badge color="rose">محظور</x-badge>
                        @elseif($ip->is_allowed)
                            @if($ip->allowed_until && $ip->allowed_until->isFuture())
                                @if($ip->is_temporary)
                                    <x-badge color="sky">مسموح مؤقت (حتى {{ $ip->allowed_until->format('Y-m-d H:i') }})</x-badge>
                                @else
                                    <x-badge color="emerald">مسموح دائم</x-badge>
                                @endif
                            @elseif($ip->is_temporary)
                                <x-badge color="amber">صلاحية مؤقتة منتهية</x-badge>
                            @else
                                <x-badge color="emerald">مسموح دائم</x-badge>
                            @endif
                        @else
                            <x-badge color="amber">قيد الانتظار</x-badge>
                        @endif
                    </p>

                    <div class="mt-3 flex flex-wrap gap-2">
                        @if (!$ip->is_allowed)
                            <button type="button"
                                    class="text-xs font-semibold bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded-lg hover:bg-indigo-100 transition-colors open-allow-modal"
                                    data-ip-id="{{ $ip->id }}"
                                    data-ip-address="{{ $ip->ip_address }}">
                                منح صلاحية
                            </button>
                        @endif

                        @if ($ip->is_blocked)
                            <form method="POST" action="{{ route('admin.sales-rep-ips.unblock', $ip) }}">
                                @csrf
                                <button type="submit" class="text-xs font-semibold bg-emerald-50 text-emerald-700 px-3 py-1.5 rounded-lg hover:bg-emerald-100 transition-colors">
                                    فك الحظر
                                </button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.sales-rep-ips.block', $ip) }}">
                                @csrf
                                <button type="submit" class="text-xs font-semibold bg-rose-50 text-rose-700 px-3 py-1.5 rounded-lg hover:bg-rose-100 transition-colors">
                                    حظر
                                </button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('admin.sales-rep-ips.destroy', $ip) }}"
                              onsubmit="return confirm('هل أنت متأكد من حذف هذا الـ IP؟');" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-semibold bg-gray-100 text-gray-700 px-3 py-1.5 rounded-lg hover:bg-gray-200 transition-colors">
                                حذف
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-gray-400 text-sm col-span-full">لا توجد سجلات IP لهذا المندوب.</p>
            @endforelse
        </div>

        <form method="POST" action="{{ route('admin.sales-rep-ips.add-temp-ip', $salesRep) }}" class="mt-5 pt-4 border-t border-gray-100">
            @csrf
            <label for="ip_address_{{ $salesRep->id }}" class="block text-sm font-medium text-gray-700 mb-1">
                إضافة IP مؤقت
            </label>
            <p class="text-xs text-gray-400 mb-2">بإمكانك عدم تحديد وقت للصلاحية إذا كان الـ IP دائمًا</p>
            <div class="flex flex-wrap gap-2">
                <input
                    type="text"
                    name="ip_address"
                    id="ip_address_{{ $salesRep->id }}"
                    placeholder="192.168.1.100"
                    class="flex-1 min-w-[160px] px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm ltr-number"
                    dir="ltr"
                />
                <input type="text" id="allowed_until" name="allowed_until"
                       class="flex-1 min-w-[160px] px-4 py-2 border border-gray-300 rounded-lg text-end focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="صالح لغاية"
                       required>
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                    إضافة
                </button>
            </div>
        </form>
    </div>
@empty
    <x-empty-state title="لا يوجد مندوبون مطابقون للبحث" />
@endforelse

<div id="allowModal" class="fixed inset-0 bg-gray-900/50 flex items-center justify-center hidden z-50 px-4">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
        <div class="p-6">
            <h3 class="text-base font-semibold text-gray-900 mb-4">منح صلاحية للـ IP</h3>

            <form id="allowForm" method="POST">
                @csrf
                <input type="hidden" name="ip_id" id="modal_ip_id">

                <div class="mb-4">
                    <label for="modal_ip_address" class="block text-sm font-medium text-gray-700 mb-1.5">
                        عنوان IP
                    </label>
                    <input type="text" id="modal_ip_address"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-gray-50 ltr-number"
                           dir="ltr" readonly>
                </div>

                <div class="mb-4">
                    <label for="modal_allowed_until" class="block text-sm font-medium text-gray-700 mb-1.5">
                        صلاحية حتى
                    </label>
                    <input type="text" id="modal_allowed_until" name="allowed_until"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg text-end focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                           placeholder="اختر التاريخ والوقت" required>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" id="closeModal"
                            class="px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors">
                        إلغاء
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                        حفظ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            flatpickr("#allowed_until", {
                locale: "ar",
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                altInput: true,
                altFormat: "F j, Y - H:i",
                allowInput: true,
                defaultHour: 12,
            });


            const modalDatePicker = flatpickr("#modal_allowed_until", {
                locale: "ar",
                enableTime: true,
                dateFormat: "Y-m-d H:i",
                altInput: true,
                altFormat: "F j, Y - H:i",
                allowInput: true,
                defaultHour: 12,
                minDate: "today"
            });


            const modal = document.getElementById('allowModal');
            const allowForm = document.getElementById('allowForm');
            const modalIpId = document.getElementById('modal_ip_id');
            const modalIpAddress = document.getElementById('modal_ip_address');
            const closeModalBtn = document.getElementById('closeModal');


            document.querySelectorAll('.open-allow-modal').forEach(button => {
                button.addEventListener('click', function() {
                    const ipId = this.getAttribute('data-ip-id');
                    const ipAddress = this.getAttribute('data-ip-address');

                    modalIpId.value = ipId;
                    modalIpAddress.value = ipAddress;

                    // Reset the date picker
                    modalDatePicker.setDate(null);

                    // CORRECTED: Use Laravel's route function with parameter
                    allowForm.action = "{{ route('admin.sales-rep-ips.allow', ':id') }}".replace(':id', ipId);

                    // Show the modal
                    modal.classList.remove('hidden');
                });
            });
            closeModalBtn.addEventListener('click', function() {
                modal.classList.add('hidden');
            });

            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    modal.classList.add('hidden');
                }
            });
        });
    </script>
@endpush

@endsection
