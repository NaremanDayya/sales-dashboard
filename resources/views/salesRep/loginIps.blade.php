@extends('layouts.master')
@section('title' , 'أجهزة تسجيل الدخول للمندوبين')
@section('content')
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">📡 إدارة أجهزة تسجيل الدخول للمندوبين</h1>

        <form method="GET" class="mb-6 flex flex-wrap items-center gap-4">
            <input
                type="text"
                name="search"
                value="{{ request('search') }}"
                placeholder="🔍 اسم المندوب"
                class="px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />

            <button
                type="submit"
                class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition"
            >
                بحث
            </button>

            @if (request('search'))
                <a
                    href="{{ route('admin.sales-rep-ips.index') }}"
                    class="text-sm text-red-500 underline hover:text-red-700"
                >
                    إعادة تعيين
                </a>
            @endif
        </form>

        @forelse ($salesReps as $salesRep)
            <div class="bg-white rounded shadow p-4 mb-6">
                <h2 class="text-xl font-semibold mb-2">{{ $salesRep->user->name ?? 'اسم غير متوفر' }}</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse ($salesRep->loginIps as $ip)
                        <div class="border rounded p-3 shadow-sm">
                            <p class="text-sm font-medium text-gray-700">
                                🖥 IP Address: <span class="text-black font-bold">{{ $ip->ip_address }}</span>
                            </p>

                            <p class="text-sm mt-1">
                                📆 التاريخ: {{ $ip->created_at->format('Y-m-d H:i') }}
                            </p>

                            <p class="text-sm mt-1">
                                🔒 الحالة: 
                                @if ($ip->is_blocked)
                                    <span class="text-red-500 font-semibold">محظور</span>
                                @else
                                    <span class="text-green-500 font-semibold">مسموح</span>
                                @endif
                            </p>

                            <div class="mt-2 flex gap-2">
                                @if ($ip->is_blocked)
                                    <form method="POST" action="{{ route('admin.sales-rep-ips.unblock', $ip) }}">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="text-sm bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600"
                                        >
                                            فك الحظر
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.sales-rep-ips.block', $ip) }}">
                                        @csrf
                                        <button
                                            type="submit"
                                            class="text-sm bg-red-500 text-white px-3 py-1 rounded hover:bg-red-600"
                                        >
                                            حظر
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 col-span-full">لا توجد سجلات IP لهذا المندوب.</p>
                    @endforelse
                </div>

                {{-- إضافة IP مؤقت --}}
                <form method="POST" action="{{ route('admin.sales-rep-ips.add-temp-ip', $salesRep) }}" class="mt-4">
                    @csrf
                    <label for="ip_address_{{ $salesRep->id }}" class="block text-sm font-medium text-gray-700 mb-1">
                        إضافة IP مؤقت:
                    </label>
                    <div class="flex gap-2">
                        <input
                            type="text"
                            name="ip_address"
                            id="ip_address_{{ $salesRep->id }}"
                            placeholder="192.168.1.100"
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-md"
                            required
                        />
   <input type="text" id="allowed_until" name="allowed_until"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-right rtl" placeholder="صالح لغاية"
                        required>
                        <button
                            type="submit"
                            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                        >
                            إضافة
                        </button>
                    </div>
                </form>
            </div>
        @empty
            <p class="text-center text-gray-600">لا يوجد مندوبون مطابقون للبحث.</p>
        @endforelse
    </div>
@push('scripts')
<script>
    flatpickr("#allowed_until", {
    locale: "ar",
    enableTime: true,
    dateFormat: "Y-m-d H:i",
    altInput: true,
    altFormat: "F j, Y - H:i",
    allowInput: true,
    defaultHour: 12,
});
</script>
@endpush
@endsection
