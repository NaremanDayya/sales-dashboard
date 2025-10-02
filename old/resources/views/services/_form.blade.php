<div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md overflow-hidden p-6 space-y-6">
    <!-- Error Alert -->
    <x-form.alert name="error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4"></x-form.alert>

    <!-- Service Name -->
    <div class="space-y-2">
        <label for="name" class="block text-sm font-medium text-gray-700">إسم الخدمة</label>
        <input type="text" id="name" name="name" value="{{ $service->name ?? old('name') }}" placeholder="Service Name"
            required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border">
        @error('name')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Service Description -->
    <div class="space-y-2">
        <label for="description" class="block text-sm font-medium text-gray-700">وصف الخدمة</label>
        <textarea id="description" name="description" rows="3"
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
            placeholder="Describe the service in detail">{{ $service->description ?? old('description') }}</textarea>
        @error('description')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
    <div class="form-group">
        <label for="is_flat_price">نوع الخدمة</label>
        <select name="is_flat_price" id="is_flat_price" class="form-control" required>
	<option value="0" {{ old('is_flat_price')==='0' ? 'selected' : '' }}>كميًا</option>
            <option value="1" {{ old('is_flat_price')==='1' ? 'selected' : '' }}>سعر</option>
        </select>
<div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>

    </div>

    <!-- Target Percentage -->

<div class="space-y-2">
    <label for="target_amount" id="target_label" class="block text-sm font-medium text-gray-700">
        {{ old('is_flat_price', $service->is_flat_price ?? 0) == 1 ? 'السعر المطلوب تحقيقه' : 'العدد المطلوب تحقيقه' }}
    </label>

    <div class="mt-1 relative rounded-md shadow-sm">
        <input type="number" id="target_amount" name="target_amount"
            value="{{ old('target_amount', isset($service->target_amount) ? rtrim(rtrim(number_format($service->target_amount, 2, '.', ''), '0'), '.') : '') }}" required
            class="focus:ring-blue-500 focus:border-blue-500 block w-full pr-4 pl-14 sm:text-sm border-gray-300 rounded-md p-3 border text-right">

        @if(old('is_flat_price', $service->is_flat_price ?? 0) == 1)
            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                <span class="text-gray-500 sm:text-sm">{{ config('app.currency', '₪') }}</span>
            </div>
        @endif
    </div>

    @error('target_amount')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
<div class="space-y-2">
        <label for="commission_rate" class="block text-sm font-medium text-gray-700">نسبة العمولة</label> 
        <div class="mt-1 relative rounded-md shadow-sm">
            <input type="number" id="commission_rate" name="commission_rate"
                step="0.01" value="{{ old('commission_rate', isset($service->commission_rate) ? rtrim(rtrim(number_format($service->commission_rate, 2, '.', ''), '0'), '.') : '') }}"
 required
                class="focus:ring-blue-500 focus:border-blue-500 block w-full pr-12 sm:text-sm border-gray-300 rounded-md p-3 border">
        </div>
        @error('commission_rate')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Form Actions -->
    <div class="flex justify-end pt-4 border-t border-gray-200">
        <button type="submit"
            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            {{ $button_label ?? 'إضافة خدمة' }}
        </button>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const isFlatPriceSelect = document.getElementById('is_flat_price');
        const targetLabel = document.getElementById('target_label');

        function updateLabel() {
            const value = isFlatPriceSelect.value;
            targetLabel.textContent = value === '1' ? 'السعر المطلوب تحقيقه' : 'العدد المطلوب تحقيقه';
        }

        // Initial call in case the value is pre-selected
        updateLabel();

        // Update label on change
        isFlatPriceSelect.addEventListener('change', updateLabel);
    });
</script>
