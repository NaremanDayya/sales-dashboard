@extends('layouts.master')
@section('title','أضف إتفاقية جديدة')
@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-xl shadow-md overflow-hidden p-6 mb-6">
<div class="flex justify-between items-center mb-6">
    <div class="flex-1 text-center"> <!-- Added wrapper div with flex-1 and text-center -->
        <h2 class="text-2xl font-bold text-gray-800 inline-block">أضف إتفاقية جديدة</h2>
    </div>
    <a href="{{ route('salesrep.agreements.index', $salesrep) }}"
        class="text-blue-600 hover:text-blue-800 flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20"
            fill="currentColor">
            <path fill-rule="evenodd"
                d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                clip-rule="evenodd" />
        </svg>
        الرجوع للإتفاقيات
    </a>
</div>
            <form action="{{ route('salesrep.agreements.store', $salesrep) }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Client Selection -->
                    <div class="space-y-2">
                        <label for="client_id" class="block text-sm font-medium text-gray-700">العميل</label>
                        <select id="client_id" name="client_id" required
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md shadow-sm">
                            <option value="">إختر العميل</option>
                            @foreach($clients as $client)
                            <option value="{{ $client->id }}" {{ old('client_id')==$client->id ? 'selected' : '' }}>
                                {{ $client->company_name }}
                            </option>
                            @endforeach
                        </select>
                        @error('client_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Service Selection -->
                    <div class="space-y-2">
                        <label for="service_id" class="block text-sm font-medium text-gray-700">الخدمة</label>
                        <select id="service_id" name="service_id" onchange="handleServiceChange()" class="form-control mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md shadow-sm"
                            required>
                            @foreach($services as $service)
                            <option value="{{ $service->id }}"
                                data-is-flat-price="{{ $service->is_flat_price ? '1' : '0' }}" {{
                                old('service_id')==$service->id ? 'selected' : '' }}
                                >
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
    <input type="text" id="signing_date" name="signing_date" dir="rtl"
        value="{{ old('signing_date') }}"
        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5 sm:text-sm text-right">
    @error('signing_date')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<!-- Implementation Date -->
<div class="space-y-2">
    <label for="implementation_date" class="block text-sm font-medium text-gray-700">تاريخ التنفيذ</label>
    <input type="text" id="implementation_date" name="implementation_date" dir="rtl"
        value="{{ old('implementation_date') }}"
        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5 sm:text-sm text-right">
    @error('implementation_date')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<!-- Duration -->
<div class="space-y-2">
    <label for="duration_years" class="block text-sm font-medium text-gray-700">مدة الاتفاقية (بالسنوات)</label>
    <input type="number" id="duration_years" name="duration_years"
        value="{{ old('duration_years') }}" min="1" required
        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-2.5 sm:text-sm">
    @error('duration_years')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

                    </div>
                </div>

                <!-- Terms Section -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h3 class="text-lg font-medium text-gray-800 mb-4">شروط الاتفاقية</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Termination Type -->
                        <div class="space-y-2">
                            <label for="termination_type" class="block text-sm font-medium text-gray-700">نوع إنهاء الإتفاقية</label>
                            <select id="termination_type" name="termination_type" required
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md shadow-sm">
                                <option value="">Select Type</option>
                                <option value="returnable" {{ old('termination_type')=='returnable' ? 'selected' : ''
                                    }}>مشروط بمقابل</option>
                                <option value="non_returnable" {{ old('termination_type')=='non_returnable' ? 'selected'
                                    : '' }}>غير مشروط بمقابل</option>
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
                                value="{{ old('notice_months', 0) }}" min="0"
                                class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('notice_months')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Status -->
                        <div class="space-y-2">
                            <label for="status" class="block text-sm font-medium text-gray-700">حالة الإتفاقية</label>
                            <select id="status" name="status" required
                                class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md shadow-sm">
                                <option value="">إختر الحالة</option>
                                <option value="active" {{ old('status')=='active' ? 'selected' : '' }}>سارية
                                </option>
                                <option value="terminated" {{ old('status')=='terminated' ? 'selected' : '' }}>
                                    تم وقفها</option>
                                <option value="expired" {{ old('status')=='expired' ? 'selected' : '' }}>
                                    منتهية</option>
                            </select>
                            @error('status')
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
                        <div class="space-y-2" id="product_quantity_wrapper">
                            <label for="product_quantity" class="block text-sm font-medium text-gray-700">الكمية المطلوبة</label>
                            <input type="number" id="product_quantity" name="product_quantity"
                                value="{{ old('product_quantity') }}" min="1"
                                class="mt-1 focus:ring-blue-500 focus:border-blue-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                            @error('product_quantity')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Price -->
                        <div class="space-y-2">
                            <label for="price" class="block text-sm font-medium text-gray-700">السعر المتفق عليه</label>
                            <div class="mt-1 relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 sm:text-sm">{{ config('app.currency') }}</span>
                                </div>
                                <input type="number" id="price" name="price" value="{{ old('price') }}" step="0.01"
                                    min="0" required
                                    class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md">
                            </div>
                            @error('price')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3">
                    <button type="reset"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        إعادة تعيين
                    </button>
                    <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        إنشاء إتفاقية
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
function handleServiceChange() {
    const serviceSelect = document.getElementById('service_id');
    const selectedOption = serviceSelect.options[serviceSelect.selectedIndex];
    const isFlatPrice = selectedOption.getAttribute('data-is-flat-price') === '1';

    const quantityWrapper = document.getElementById('product_quantity_wrapper');
    const quantityInput = document.getElementById('product_quantity');

    if (isFlatPrice) {
        quantityWrapper.style.display = 'none';
        quantityInput.removeAttribute('required');
        quantityInput.removeAttribute('min');
        quantityInput.value = '1'; // Set to default value
    } else {
        quantityWrapper.style.display = 'block';
        quantityInput.setAttribute('required', 'required');
        quantityInput.setAttribute('min', '1');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Run the handler once when page loads
    handleServiceChange();
    
    // Add event listener for changes
    document.getElementById('service_id').addEventListener('change', handleServiceChange);
});

flatpickr("#implementation_date", {
        dateFormat: "Y-m-d",
        locale: "ar",
        disableMobile: true,
        defaultDate: "{{ old('implementation_date') }}"
    });
flatpickr("#signing_date", {
        dateFormat: "Y-m-d",
        locale: "ar",
        disableMobile: true,
        defaultDate: "{{ old('signing_date') }}"
    });
 
</script>
@endpush
