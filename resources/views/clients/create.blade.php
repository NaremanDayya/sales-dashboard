@extends('layouts.master')
@section('title', 'أضف عميل')
@section('content')
<div class="container">
<div class="mb-6">
        <h1 class="text-center text-2xl font-bold">أضف عميل جديد</h1>
    </div>
    <form action="{{ route('sales-reps.clients.store', Auth::user()->salesRep->id) }}" method="POST"
        enctype="multipart/form-data">
        @csrf

        <!-- Logo Section -->
        <div class="mb-8 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h3 class="text-lg font-medium text-gray-800 mb-4">شعار الشركة</h3>
            <div class="flex items-center space-x-6">
                @if(session('temp_company_logo'))
                <div class="shrink-0">
                    <img src="{{ asset('storage/' . session('temp_company_logo')) }}"
                        class="h-16 w-16 object-cover rounded-full border border-gray-200">
                    <input type="hidden" name="company_logo_temp" value="{{ session('temp_company_logo') }}">
                </div>
                @endif

                <div class="w-full">
<label class="block">
    <span class="sr-only">إختر شعار الشركة</span>
    <input
        type="file"
        name="company_logo"
        class="block w-full text-sm text-gray-500
               file:mr-4 file:py-2 file:px-4
               file:rounded-lg file:border-0
               file:text-sm file:font-semibold
               file:bg-indigo-50 file:text-blue-700
               hover:file:bg-blue-100"
        accept="image/jpg,image/jpeg,image/png,image/gif,image/bmp,image/webp,image/svg+xml,image/tiff,image/x-icon"
        {{ session('temp_company_logo') ? '' : 'required' }}
    >
</label>
                    @if(session('temp_company_logo'))
                    <p class="mt-2 text-sm text-green-600">✓ تم تحميل الشعار</p>
                    @endif

                    @error('company_logo')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Client Form Fields -->
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 space-y-6">
            <!-- Company Name -->
            <div>
                <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">اسم الشركة</label>
                <input type="text" id="company_name" name="company_name" value="{{ old('company_name') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3 border
                              {{ $errors->has('company_name') ? 'border-red-500' : '' }}" {{ old('company_name') &&
                    !$errors->has('company_name') ? 'readonly' : '' }}
                required>
                <ul id="companySuggestions"
                    class="absolute z-50 bg-white shadow rounded w-full border border-gray-200 hidden"></ul>

                @if(old('company_name') && !$errors->has('company_name'))
                <p class="mt-2 text-sm text-green-600">✓ تم إدخال الاسم</p>
                @endif
                <p class="mt-1 text-sm text-blue-500">
                    🔍 إذا كنت تتعامل مع شركة سبق إضافتها، يرجى اختيار اسم الشركة من الاقتراحات لتفادي التكرار.
                </p>
                @error('company_name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Address -->
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">عنوان الشركة</label>
                <input type="text" id="address" name="address" value="{{ old('address') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3 border
                              {{ $errors->has('address') ? 'border-red-500' : '' }}" {{ old('address') &&
                    !$errors->has('address') ? 'readonly' : '' }}
                required>
                @if(old('address') && !$errors->has('address'))
                <p class="mt-2 text-sm text-green-600">✓ تم إدخال العنوان</p>
                @endif
                @error('address')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
		<!-- Interested Service -->
            <div class="mt-4">
                <div class="flex items-center justify-between mb-1">
                    <label for="interested_service" class="block text-sm font-medium text-gray-700">
                        الخدمة المهتم بها
                    </label>
                    <button type="button" id="suggestServiceBtn"
                            class="inline-flex items-center gap-1 text-xs font-semibold text-indigo-600 hover:text-indigo-800 disabled:opacity-50 disabled:cursor-not-allowed">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.987-2.386l-.548-.547z"/></svg>
                        <span id="suggestServiceBtnLabel">اقترح الخدمة المناسبة</span>
                    </button>
                </div>

                <div class="flex gap-3 items-center">
                    <!-- خدمة -->
                    <select id="interested_service" name="interested_service"
                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3 border
            {{ $errors->has('interested_service') ? 'border-red-500' : '' }}">
                        <option value="">-- اختر الخدمة --</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}" {{ old('interested_service') == $service->id ? 'selected' : '' }}>
                                {{ $service->name }}
                            </option>
                        @endforeach
                    </select>

                    <!-- عدد الخدمات المهتم بها -->
                    <div class="w-32">
                        <label for="interested_service_count" class="block text-sm font-medium text-gray-700 mb-1">العدد المهتم به</label>
                        <input type="number" id="interested_service_count" name="interested_service_count"
                               value="{{ old('interested_service_count', $client->interested_service_count ?? 0) }}"
                               min="0"
                               class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-lg focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>

                <p id="suggestServiceReason" class="mt-1.5 text-sm text-indigo-700 hidden"></p>

                @error('interested_service')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Contact Person -->
            <div>
                <label for="contact_person" class="block text-sm font-medium text-gray-700 mb-1">الشخص المسؤول</label>
                <input type="text" id="contact_person" name="contact_person" value="{{ old('contact_person') }}" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3 border
                              {{ $errors->has('contact_person') ? 'border-red-500' : '' }}" {{ old('contact_person') &&
                    !$errors->has('contact_person') ? 'readonly' : '' }}
                required>
                @if(old('contact_person') && !$errors->has('contact_person'))
                <p class="mt-2 text-sm text-green-600">✓ تم إدخال المسؤول</p>
                @endif
                @error('contact_person')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Contact Position -->
            <div>
                <label for="contact_position" class="block text-sm font-medium text-gray-700 mb-1">المنصب
                    الوظيفي</label>
                <input type="text" id="contact_position" name="contact_position" value="{{ old('contact_position') }}"
                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3 border
                              {{ $errors->has('contact_position') ? 'border-red-500' : '' }}" {{
                    old('contact_position') && !$errors->has('contact_position') ? 'readonly' : '' }}
                placeholder="Manager, Owner, etc.">
                @if(old('contact_position') && !$errors->has('contact_position'))
                <p class="mt-2 text-sm text-green-600">✓ تم إدخال المنصب</p>
                @endif
                @error('contact_position')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <!-- Last Contact Date -->
            <div>
                @php
                use Carbon\Carbon;

                $date = old('last_contact_date');
                $formattedDate = null;

                if ($date) {
                $carbonDate = Carbon::parse($date);
                $carbonDate->locale('ar');

                $formattedDate = $carbonDate->isoFormat('D MMMM YYYY');
                }
                @endphp
                <label for="last_contact_date" class="block text-sm font-medium text-gray-700 mb-1">تاريخ آخر
                    تواصل</label>
                <div class="relative">
<input type="text" id="last_contact_date" name="last_contact_date" dir="rtl"
    value="{{ old('last_contact_date') }}"
    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3 border text-right">                </div>

                @if(old('last_contact_date') && !$errors->has('last_contact_date'))
                <p class="mt-2 text-sm text-green-600">{{ $formattedDate }} ✓ تم إدخال تاريخ آخر تواصل</p>
                @endif

                @error('last_contact_date')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

<div>
    <label for="contact_details" class="block text-sm font-medium text-gray-700 mb-1">تفاصيل التواصل</label>
    <textarea id="contact_details" name="contact_details"
        class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3 border
              {{ $errors->has('contact_details') ? 'border-red-500' : '' }}"
        placeholder="أدخل تفاصيل إضافية للتواصل مثل الملاحظات أو معلومات أخرى"
        {{ old('contact_details') && !$errors->has('contact_details') ? 'readonly' : '' }}>{{ old('contact_details') }}</textarea>

    @if(old('contact_details') && !$errors->has('contact_details'))
        <p class="mt-2 text-sm text-green-600">✓ تم إدخال تفاصيل التواصل</p>
    @endif
    @error('contact_details')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>


            <div x-data="{ countryCode: '{{ old('country_code', '') }}' }" class="flex items-start gap-2"> <!-- قللنا الفجوة من 4 إلى 2 -->

                <!-- كود الدولة -->
                <div>
                    <label for="country_code" class="block text-sm font-medium text-gray-700 mb-1">
                        كود الدولة (واتساب)
                    </label>
                    <div class="flex items-center gap-1"> <!-- أضفنا gap-1 بين +code و input -->
                        <span class="text-gray-700 font-bold text-lg">+<span x-text="countryCode"></span></span>
                        <input type="text" id="country_code" name="country_code"
                               x-model="countryCode"
                               class="mt-1 block w-28 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3 border
            {{ $errors->has('country_code') ? 'border-red-500' : '' }}"
                               placeholder="971" required>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        ⚠️ أدخل الكود بدون (+)
                    </p>
                    @error('country_code')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- رقم الهاتف -->
                <div class="flex-1">
                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                        رقم الجوال
                    </label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}"
                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3 border
        {{ $errors->has('phone') ? 'border-red-500' : '' }}"
                           placeholder="501234567" required>
                    <p class="mt-1 text-xs text-gray-500">
                        ⚠️ أدخل الرقم بدون الصفر في البداية إذا كان يبدأ بـ 0.
                    </p>
                    @if(old('phone') && !$errors->has('phone'))
                        <p class="mt-2 text-sm text-green-600">
                            ✓ تم إدخال الرقم: {{ old('phone') }}
                        </p>
                    @endif
                    @error('phone')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

            </div>


            <!-- Interest Status -->
            <div>
                <label for="interest_status" class="block text-sm font-medium text-gray-700 mb-1">حالة الاهتمام</label>
                <select id="interest_status" name="interest_status" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 p-3 border
                              {{ $errors->has('interest_status') ? 'border-red-500' : '' }}" {{ old('interest_status')
                    && !$errors->has('interest_status') ? 'readonly' : '' }}
                    required>
                    <option value="" {{ !old('interest_status') ? 'selected' : '' }}>اختر حالة الاهتمام</option>
                    <option value="interested" {{ old('interest_status')=='interested' ? 'selected' : '' }}>مهتم
                    </option>
                    <option value="not interested" {{ old('interest_status')=='not interested' ? 'selected' : '' }}>غير
                        مهتم</option>
                    <option value="neutral" {{ old('interest_status')=='neutral' ? 'selected' : '' }}>مؤجل</option>
                </select>
                @if(old('interest_status') && !$errors->has('interest_status'))
                <p class="mt-2 text-sm text-green-600">✓ تم تحديد الحالة</p>
                @endif
                @error('interest_status')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
<div class="flex justify-end pt-6">
<button type="submit"
        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
        style="position: relative; z-index: 999"> <!-- Force above other elements -->
    {{ $button_label ?? 'إضافة العميل' }}
</button>
        </div>
</div>
    </form>
</div>
@endsection
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('company_name');
    const suggestionBox = document.getElementById('companySuggestions');

    input.addEventListener('input', function () {
        const query = this.value;
        if (query.length < 2) {
            suggestionBox.innerHTML = '';
            suggestionBox.classList.add('hidden');
            return;
        }

        fetch(`/company-name-suggestions?term=${encodeURIComponent(query)}`)
            .then(response => response.json())
            .then(suggestions => {
                suggestionBox.innerHTML = '';
                if (suggestions.length > 0) {
                    suggestionBox.classList.remove('hidden');
                    suggestions.forEach(name => {
                        const item = document.createElement('li');
                        item.textContent = name;
                        item.className = 'px-4 py-2 cursor-pointer hover:bg-gray-100';
                        item.onclick = () => {
                            input.value = name;
                            suggestionBox.classList.add('hidden');
                        };
                        suggestionBox.appendChild(item);
                    });
                } else {
                    suggestionBox.classList.add('hidden');
                }
            });
    });
});
document.getElementById('suggestServiceBtn').addEventListener('click', function () {
    const companyName = document.getElementById('company_name').value.trim();
    const btn = this;
    const label = document.getElementById('suggestServiceBtnLabel');
    const reasonEl = document.getElementById('suggestServiceReason');

    if (!companyName) {
        reasonEl.textContent = 'يرجى إدخال اسم الشركة أولًا.';
        reasonEl.classList.remove('hidden');
        return;
    }

    btn.disabled = true;
    label.textContent = 'جاري الاقتراح...';
    reasonEl.classList.add('hidden');

    fetch('{{ route('clients.suggest-service') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({ company_name: companyName }),
    })
        .then(response => response.json())
        .then(data => {
            if (data.service_id) {
                const select = document.getElementById('interested_service');
                select.value = data.service_id;
                reasonEl.textContent = data.reason ? '✨ ' + data.reason : '✨ تم اقتراح خدمة مناسبة، يمكنك تغييرها.';
            } else {
                reasonEl.textContent = 'تعذر اقتراح خدمة مناسبة، يرجى الاختيار يدويًا.';
            }
            reasonEl.classList.remove('hidden');
        })
        .catch(() => {
            reasonEl.textContent = 'تعذر الاتصال بخدمة الاقتراح، يرجى الاختيار يدويًا.';
            reasonEl.classList.remove('hidden');
        })
        .finally(() => {
            btn.disabled = false;
            label.textContent = 'اقترح الخدمة المناسبة';
        });
});

flatpickr("#last_contact_date", {
        dateFormat: "Y-m-d",
        locale: "ar",
        disableMobile: true,
        defaultDate: "{{ old('last_contact_date') }}"
    });

</script>
@endpush

