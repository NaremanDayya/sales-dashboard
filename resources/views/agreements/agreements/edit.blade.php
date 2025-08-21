@extends('layouts.master')
@section('title','تعديل الإتفاقية')
@section('content')
<form action="{{ route('salesrep.agreements.update', [
    'salesrep' => $agreement->sales_rep_id,
    'agreement' => $agreement->id,
]) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md overflow-hidden p-6 space-y-6">
        <!-- Error Alert -->
        <x-form.alert name="error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4"></x-form.alert>

        <!-- Hidden Fields -->
        <input type="hidden" name="agreement_id" value="{{ old('agreement_id', $agreement->id ?? '') }}">
        <input type="hidden" name="total_amount" id="total_amount"
            value="{{ old('total_amount', $agreement->total_amount ?? '') }}">

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Client Selection -->
            <div class="space-y-2">
                <label for="client_id" class="block text-sm font-medium text-gray-700">العميل</label>
                <select id="client_id" name="client_id" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                    {{ $editableField !=='client_id' ? 'disabled' : '' }}>
                    @php
                    $currentClientId = old('client_id', $agreement->client_id ?? '');
                    $clientExistsInList = $clients->pluck('id')->contains($currentClientId);
                    @endphp

                    @if (!$clientExistsInList && isset($agreement))
                    <option value="{{ $agreement->client->id }}" selected>
                        {{ $agreement->client->company_name }}
                    </option>
                    @endif
                    @foreach ($clients as $client)
                    @if ($client)
                    <option value="{{ $client->id }}" {{ $currentClientId==$client->id ? 'selected' : '' }}>
                        {{ $client->company_name }}
                    </option>
                    @endif
                    @endforeach
                </select>
                @error('client_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Service Selection -->
            <div class="space-y-2">
                <label for="service_id" class="block text-sm font-medium text-gray-700">نوع الخدمة</label>
                <select id="service_id" name="service_id" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                    {{ $editableField !=='service_id' ? 'disabled' : '' }}>
                    <option value="" disabled {{ old('service_id', $agreement->service_id ?? '') === null ? 'selected' :
                        ''
                        }}>
                        إختر الخدمة
                    </option>
                    @foreach ($services as $service)
                    <option value="{{ $service->id }}" {{ old('service_id', $agreement->service_id ?? '') ==
                        $service->id ?
                        'selected' : '' }}>
                        {{ $service->name }}
                    </option>
                    @endforeach
                </select>
                @error('service_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Dates Section -->
        <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="text-lg font-medium text-gray-800 mb-4">تواريخ الإتفاقية</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Signing Date -->
                <div class="space-y-2">
                    <label for="signing_date" class="block text-sm font-medium text-gray-700">تاريخ التوقيع</label>
                    <input type="date" id="signing_date" name="signing_date"  placeholder="dd-mm-yyyy"
                        value="{{ old('signing_date', $agreement->signing_date ?? '') }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                        {{ $editableField !=='signing_date' ? 'disabled' : '' }}>
                    @error('signing_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Implementation Date -->
                <div class="space-y-2">
                    <label for="implementation_date" class="block text-sm font-medium text-gray-700">
                        تاريخ التنفيذ</label>
                    <input type="date" id="implementation_date" name="implementation_date" placeholder="dd-mm-yyyy"
                        value="{{ old('implementation_date', $agreement->implementation_date ?? '') }}"
                        min="{{ old('signing_date', $agreement->signing_date ?? '') }}" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                        {{ $editableField !=='implementation_date' ? 'disabled' : '' }}>
                    @error('implementation_date')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Duration -->
                <div class="space-y-2">
                    <label for="duration_years" class="block text-sm font-medium text-gray-700">مدة الإتفاقية(بالسنوات)</label>
                    <input type="number" id="duration_years" name="duration_years"
                        value="{{ old('duration_years', $agreement->duration_years ?? '') }}" min="1" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                        {{ $editableField !=='duration_years' ? 'disabled' : '' }}>
                    @error('duration_years')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Expiration Date (Read-only) -->
            <div class="mt-4 space-y-2">
                <label for="expiration_date" class="block text-sm font-medium text-gray-700">تاريخ الإنتهاء</label>
                <input type="text" id="expiration_date"
                    class="mt-1 block w-full rounded-md bg-gray-100 border-gray-300 shadow-sm p-3 border" readonly
                    value="{{ isset($agreement) ? $agreement->end_date->format('Y-m-d') : '' }}" </div>
            </div>

            <!-- Terms Section -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-800 mb-4">شروط الإتفاقية</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Termination Type -->
                    <div class="space-y-2">
                        <label for="termination_type" class="block text-sm font-medium text-gray-700">نوع إنهاء الإتفاقية
                            </label>
                        <select id="termination_type" name="termination_type" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                            {{ $editableField !=='termination_type' ? 'disabled' : '' }}>
                            <option value="returnable" {{ old('termination_type', $agreement->termination_type ?? '') ==
                                'returnable' ? 'selected' : '' }}>
                                مشروطة بمقابل
                            </option>
                            <option value="non_returnable" {{ old('termination_type', $agreement->termination_type ??
                                '') ==
                                'non_returnable' ? 'selected' : '' }}>
                                غير مشروطة بمقابل
                            </option>
                        </select>
                        @error('termination_type')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Notice Period -->
                    <div class="space-y-2">
                        <label for="notice_months" class="block text-sm font-medium text-gray-700">فترة الإخطار
                            (بالأشهر)</label>
                        <input type="number" id="notice_months" name="notice_months"
                            value="{{ old('notice_months', $agreement->notice_months ?? '') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                            {{ $editableField !=='notice_months' ? 'disabled' : '' }}>
                        @error('notice_months')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Agreement Status -->
                    <div class="space-y-2">
                        <label for="agreement_status" class="block text-sm font-medium text-gray-700">حالة الإتفاقية
                            </label>
                        <select id="agreement_status" name="agreement_status" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                            {{ $editableField !=='agreement_status' ? 'disabled' : '' }}>
                            <option value="active" {{ old('agreement_status', $agreement->agreement_status ?? '') ==
                                'active' ?
                                'selected' : '' }}>
                                سارية
                            </option>
                            <option value="in_active" {{ old('agreement_status', $agreement->agreement_status ?? '') ==
                                'expired' ? 'selected' : '' }}>
                                 منتهية
                            </option>
                            <option value="pending" {{ old('agreement_status', $agreement->agreement_status ?? '') ==
                                'pending'
                                ? 'selected' : '' }}>
                                معلقة
                            </option>
                        </select>
                        @error('agreement_status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

            </div>

            <!-- Financial Section -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h3 class="text-lg font-medium text-gray-800 mb-4">التفاصيل المالية</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Product Quantity -->
                    <div class="space-y-2">
                        <label for="product_quantity" class="block text-sm font-medium text-gray-700">كمية الخدمة
                            </label>
                        <input type="number" id="product_quantity" name="product_quantity"
                            value="{{ old('product_quantity', $agreement->product_quantity ?? '') }}" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                            {{ $editableField !=='product_quantity' ? 'disabled' : '' }}>
                        @error('product_quantity')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Price per Service -->
                    <div class="space-y-2">
                        <label for="price" class="block text-sm font-medium text-gray-700">التسعيرة المتفق عليها</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <input type="number" id="price" name="price"
                                value="{{ old('price', $agreement->price ?? '') }}" required step="0.01"
                                class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-12 pr-12 sm:text-sm border-gray-300 rounded-md p-3 border"
                                {{ $editableField !=='price' ? 'disabled' : '' }}>
                            <div class="absolute inset-y-0 left-0 pr-3 flex items-center pointer-events-none">
                                {{ config('app.currency') }}
                            </div>
                        </div>
                        @error('price')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Auto-calculated Total -->
                <div class="mt-4 space-y-2">
                    <label class="block text-sm font-medium text-gray-700">السعر الكلي</label>
                    <div class="mt-1 relative rounded-md shadow-sm">

                        <input type="text" id="display_total_amount"
                            class="block w-full pl-12 pr-12 sm:text-sm border-gray-300 rounded-md p-3 border bg-gray-100"
                            readonly
                            value="{{ isset($agreement) ? number_format($agreement->total_amount, 2) : '0.00' }}">
                        <div class="absolute inset-y-0 left-0 pr-3 flex items-center pointer-events-none">
                            {{ config('app.currency') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end pt-4 border-t border-gray-200">
                <button type="submit"
                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                    {{ 'تعديل الإتفاقية' }}
                </button>
            </div>
        </div>
</form>
@endsection
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Date validation
        const signingDate = document.getElementById('signing_date');
        const implementationDate = document.getElementById('implementation_date');

        signingDate.addEventListener('change', function() {
            implementationDate.min = this.value;
            if (new Date(implementationDate.value) < new Date(this.value)) {
                implementationDate.value = this.value;
            }
            calculateExpirationDate();
        });

        // Dynamic calculations
        const priceInput = document.getElementById('price');
        const quantityInput = document.getElementById('product_quantity');
        const durationInput = document.getElementById('duration_years');
        const totalAmountInput = document.getElementById('total_amount');
        const displayTotalAmount = document.getElementById('display_total_amount');
        const expirationDateInput = document.getElementById('expiration_date');

        function calculateValues() {
            // Calculate total amount
            const total = (parseFloat(priceInput.value) || 0) * (parseInt(quantityInput.value) || 0);
            totalAmountInput.value = total.toFixed(2);
            displayTotalAmount.value = total.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
        }


        [priceInput, quantityInput, durationInput].forEach(el => {
            el.addEventListener('input', calculateValues);
        });

        // Initialize calculations
        calculateValues();
    });
</script>
@endpush
