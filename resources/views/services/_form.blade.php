<div class="max-w-3xl mx-auto bg-white border border-gray-200 rounded-xl shadow-sm p-6 sm:p-8 space-y-6">
    <x-form.alert name="error" class="bg-red-50 border border-red-200 text-red-700 rounded-lg p-4 text-sm"></x-form.alert>

    <!-- Service Name -->
    <div>
        <x-input-label for="name" value="إسم الخدمة" />
        <x-text-input type="text" id="name" name="name" value="{{ $service->name ?? old('name') }}" placeholder="اسم الخدمة" required autofocus />
        @error('name')
            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Service Description -->
    <div>
        <x-input-label for="description" value="وصف الخدمة" />
        <textarea id="description" name="description" rows="3"
            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            placeholder="وصف تفصيلي للخدمة">{{ $service->description ?? old('description') }}</textarea>
        @error('description')
            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Pricing type -->
    <div>
        <x-input-label for="is_flat_price" value="نوع الخدمة" />
        <select name="is_flat_price" id="is_flat_price" required
                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
            <option value="0" {{ old('is_flat_price')==='0' ? 'selected' : '' }}>كميًا</option>
            <option value="1" {{ old('is_flat_price')==='1' ? 'selected' : '' }}>سعر</option>
        </select>
    </div>

    <!-- Target amount -->
    <div>
        <x-input-label for="target_amount" id="target_label">
            {{ old('is_flat_price', $service->is_flat_price ?? 0) == 1 ? 'السعر المطلوب تحقيقه' : 'العدد المطلوب تحقيقه' }}
        </x-input-label>

        <div class="relative">
            <input type="number" id="target_amount" name="target_amount"
                value="{{ old('target_amount', isset($service->target_amount) ? rtrim(rtrim(number_format($service->target_amount, 2, '.', ''), '0'), '.') : '') }}" required
                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm pe-14 text-end">

            @if(old('is_flat_price', $service->is_flat_price ?? 0) == 1)
                <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                    <span class="text-gray-400 text-sm">{{ config('app.currency', '₪') }}</span>
                </div>
            @endif
        </div>

        @error('target_amount')
            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Commission rate -->
    <div>
        <x-input-label for="commission_rate" value="نسبة العمولة" />
        <div class="relative">
            <input type="number" id="commission_rate" name="commission_rate" step="0.01"
                value="{{ old('commission_rate', isset($service->commission_rate) ? rtrim(rtrim(number_format($service->commission_rate, 2, '.', ''), '0'), '.') : '') }}"
                required
                class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm pe-10 text-end">
            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none">
                <span class="text-gray-400 text-sm">%</span>
            </div>
        </div>
        @error('commission_rate')
            <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Form Actions -->
    <div class="flex justify-end pt-4 border-t border-gray-100">
        <x-primary-button type="submit">
            {{ $button_label ?? 'إضافة خدمة' }}
        </x-primary-button>
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

        updateLabel();
        isFlatPriceSelect.addEventListener('change', updateLabel);
    });
</script>
