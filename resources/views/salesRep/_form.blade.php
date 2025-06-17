<div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md overflow-hidden p-6 space-y-6">
    <!-- Error Alert -->
    <x-form.alert name="error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4"></x-form.alert>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Full Name -->
        <div class="space-y-2">
            <label for="name" class="block text-sm font-medium text-gray-700">الإسم الكامل</label>
            <input type="text" id="name" name="name" value="{{ $salesRep->name ?? old('name') }}"
                placeholder="Full Name" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border">
            @error('name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Email -->
        <div class="space-y-2">
            <label for="email" class="block text-sm font-medium text-gray-700">البريد الإلكتروني</label>
            <input type="email" id="email" name="email" value="{{ $salesRep->user->email ?? old('email') }}"
                placeholder="Email Address" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border">
            @error('email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Start Work Date -->
        <div class="space-y-2">
            <label for="start_work_date" class="block text-sm font-medium text-gray-700">تاريخ بداية العمل</label>
            <input type="date" id="start_work_date" name="start_work_date"
                value="{{ old('start_work_date', $salesRep->start_work_date ?? '') }}" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border">
            @error('start_work_date')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Account Status -->
        <div class="space-y-2">
            <label for="status" class="block text-sm font-medium text-gray-700">حالة الحساب</label>
            <select id="status" name="status" required
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border">
                <option value="active" {{ old('status', $salesRep->status ?? '') == 'active' ? 'selected' : '' }}>نشط
                </option>
                <option value="inactive" {{ old('status', $salesRep->status ?? '') == 'inactive' ? 'selected' : ''
                    }}>غير نشط</option>
            </select>
            @error('status')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <div class="space-y-2">
            <label for="phone" class="block text-sm font-medium text-gray-700">رقم الجوال</label>
            <input type="text" name="phone" id="phone" required
                value="{{ old('phone', $salesRep->contact_info['phone'] ?? '') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border">
            @error('phone')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

    </div>

    <!-- Permissions Section -->
    <div class="bg-gray-50 p-4 rounded-lg">
        <h3 class="text-lg font-medium text-gray-800 mb-4">الصلاحيات</h3>
        <x-permissions.salesrep-permission-checklist :permissions="$allPermissions"
            :selected-permissions="old('permissions', isset($salesRep) && $salesRep && $salesRep->user ? $salesRep->user->permissions->pluck('id')->toArray() : [])"
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4" />
    </div>

    <!-- Form Actions -->
    <div class="flex justify-end pt-4 border-t border-gray-200">
        <button type="submit"
            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
            <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5 text-white" viewBox="0 0 20 20"
                fill="currentColor">
                <path fill-rule="evenodd"
                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                    clip-rule="evenodd" />
            </svg>
            <span class="text-white">{{ $button_label ?? 'حفظ المندوب' }}</span>
        </button>
    </div>
</div>
