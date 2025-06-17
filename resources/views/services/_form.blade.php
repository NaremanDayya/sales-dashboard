<div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md overflow-hidden p-6 space-y-6">
    <!-- Error Alert -->
    <x-form.alert name="error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4"></x-form.alert>

    <!-- Service Name -->
    <div class="space-y-2">
        <label for="name" class="block text-sm font-medium text-gray-700">Service Name</label>
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
        <label for="is_flat_price">نوع التسعير</label>
        <select name="is_flat_price" id="is_flat_price" class="form-control" required>
            <option value="0" {{ old('is_flat_price')==='0' ? 'selected' : '' }}>حسب الكمية (Mount Price)</option>
            <option value="1" {{ old('is_flat_price')==='1' ? 'selected' : '' }}>سعر ثابت (Flat Price)</option>
        </select>
    </div>

    <!-- Target Percentage -->
    <div class="space-y-2">
        <label for="target_amount" class="block text-sm font-medium text-gray-700">Default Monthly Target
            Percentage</label>
        <div class="mt-1 relative rounded-md shadow-sm">
            <input type="number" id="target_amount" name="target_amount"
                value="{{ old('target_amount', $service->target_amount ?? '') }}" min="1" max="99" required
                class="focus:ring-blue-500 focus:border-blue-500 block w-full pr-12 sm:text-sm border-gray-300 rounded-md p-3 border">
            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                <span class="text-gray-500 sm:text-sm">%</span>
            </div>
        </div>
        <p class="mt-1 text-sm text-gray-500">القيمة العددية للخدمة</p>
        @error('target_amount')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Form Actions -->
    <div class="flex justify-end pt-4 border-t border-gray-200">
        <button type="submit"
            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                    clip-rule="evenodd" />
            </svg>
            {{ $button_label ?? 'Save Service' }}
        </button>
    </div>
</div>
