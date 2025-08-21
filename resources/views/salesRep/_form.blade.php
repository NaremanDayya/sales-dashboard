@error('error')
    <div class="text-red-600 text-sm mt-2">
        {{ $message }}<br>
        يرجى إعادة إدخال كلمة المرور
    </div>
@enderror
   <div class="max-w-4xl mx-auto bg-white rounded-xl shadow-md overflow-hidden p-6 space-y-6">
        <!-- Error Alert -->
        @if ($errors->any())
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Personal Information Section -->
        <div class="bg-gray-50 p-4 rounded-lg mb-6">
            <h3 class="text-lg font-medium text-gray-800 mb-4 border-b pb-2">المعلومات الشخصية</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Full Name -->
                <div class="space-y-2">
                    <label for="name" class="block text-sm font-medium text-gray-700">الإسم الكامل</label>
                    <div class="relative">
                        <input type="text" id="name" name="name" value="{{ old('name', $salesRep->name ?? $salesRep->user?->name ?? '') }}"
                            placeholder="الإسم الكامل" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border pr-10">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Email -->
                <div class="space-y-2">
                    <label for="email" class="block text-sm font-medium text-gray-700">البريد الإلكتروني</label>
                    <div class="relative">
                        <input type="email" id="email" name="email" value="{{ old('email', $salesRep->user?->email ?? '') }}"
                            placeholder="البريد الإلكتروني" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border pr-10">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                            </svg>
                        </div>
                    </div>
                </div>

<div class="space-y-2">
    <label for="password" class="block text-sm font-medium text-gray-700">كلمة المرور</label>
    <div class="relative">
        <input type="password" id="password" name="password" 
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border pr-10">
        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-gray-400">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 11c1.657 0 3 1.343 3 3v2a3 3 0 01-3 3m0-8c-1.657 0-3 1.343-3 3v2a3 3 0 003 3m0-8V7a3 3 0 016 0v4M6 21h12" />
            </svg>
        </div>
    </div>
</div>
</div>
            <!-- Additional Personal Info -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
                <!-- Birthday -->
                <div class="space-y-2">
                    <label for="birthday" class="block text-sm font-medium text-gray-700">تاريخ الميلاد</label>
                    <div class="relative">
<input type="text" id="birthday" name="birthday"
    value="{{ old('birthday', isset($salesRep) && $salesRep->user && $salesRep->user->birthday ? \Carbon\Carbon::parse($salesRep->user->birthday)->format('Y-m-d') : '') }}"
    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border pr-10"
    placeholder="يوم / شهر / سنة">
</div>

                </div>

                <!-- ID Card -->
                <div class="space-y-2">
                    <label for="id_card" class="block text-sm font-medium text-gray-700">رقم الهوية</label>
                    <div class="relative">
                        <input type="text" id="id_card" name="id_card" value="{{ old('id_card', $salesRep->user?->id_card ?? '') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border pr-10">
                    </div>
                </div>

                <!-- Nationality -->
                <div class="space-y-2">
                    <label for="nationality" class="block text-sm font-medium text-gray-700">الجنسية</label>
                    <div class="relative">
                        <input type="text" id="nationality" name="nationality" value="{{ old('nationality', $salesRep->user?->nationality ?? '') }}"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border pr-10">
                    </div>
                </div>

                <!-- Gender -->
                <div class="space-y-2">
                    <label for="gender" class="block text-sm font-medium text-gray-700">الجنس</label>
                    <div class="relative">
                        <select id="gender" name="gender"
                            class="mt-1 block w-full appearance-none rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border pr-10 text-right">
                            <option value="">اختر الجنس</option>
                            <option value="male" {{ old('gender', $salesRep->user?->gender ?? '') == 'male' ? 'selected' : '' }}>ذكر</option>
                            <option value="female" {{ old('gender', $salesRep->user?->gender ?? '') == 'female' ? 'selected' : '' }}>أنثى</option>
                        </select>
                    </div>
                </div>

                <!-- Personal Image -->
<div class="space-y-2">
    <label for="personal_image" class="block text-sm font-medium text-gray-700">الصورة الشخصية</label>

    @if(isset($salesRep) && $salesRep->user?->personal_image)
    <div class="mb-3">
        <span class="block text-sm text-gray-500 mb-1">الصورة الحالية:</span>
        <img src="{{ asset('storage/' . $salesRep->user?->personal_image) }}"
             alt="الصورة الشخصية الحالية"
             class="h-20 w-20 rounded-full object-cover border-2 border-gray-200">
    </div>
    @endif

    <div class="relative">
        <input type="file" id="personal_image" name="personal_image"
               accept="image/png,image/jpeg,image/jpg,image/webp"
               class="mt-1 block w-full text-sm text-gray-700 bg-white rounded-md border border-gray-300 cursor-pointer focus:outline-none focus:border-blue-500 focus:ring-blue-500 p-3 pr-10"
               onchange="previewImage(this)">
        <div id="image-preview" class="mt-2 hidden">
            <span class="block text-sm text-gray-500 mb-1">الصورة المحددة:</span>
            <img id="preview" class="h-20 w-20 rounded-full object-cover border-2 border-gray-200">
        </div>
    </div>

@if(isset($salesRep) && $salesRep->user?->personal_image)
    <div class="flex items-center mt-2">
        <input type="hidden" name="remove_personal_image" value="1">
        <input type="checkbox" 
               id="remove_personal_image" 
               name="remove_personal_image" 
               value="1"
               @checked(old('remove_personal_image', false))
               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
        <label for="remove_personal_image" class="ml-2 block text-sm text-gray-700">إزالة الصورة الحالية</label>
    </div>
@endif
</div>
        <!-- Work Information Section -->
<!-- Work Information Section -->
<div class="bg-gray-50 p-4 rounded-lg mb-6 mt-3">
    <h3 class="text-lg font-medium text-gray-800 mb-4 border-b pb-2">معلومات العمل</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Start Work Date -->
        <div class="space-y-2">
            <label for="start_work_date" class="block text-sm font-medium text-gray-700">تاريخ بداية العمل</label>
            <div class="relative">
                <input type="date" 
                       id="start_work_date" 
                       name="start_work_date"
                       value="{{ old('start_work_date', isset($salesRep) && $salesRep->start_work_date ? \Carbon\Carbon::parse($salesRep->start_work_date)->format('Y-m-d') : '') }}"
                       required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border pr-10">
            </div>
        </div>

        <!-- Account Status -->
        <div class="space-y-2">
            <label for="status" class="block text-sm font-medium text-gray-700">حالة الحساب</label>
            <div class="relative">
                <select id="status" 
                        name="status" 
                        required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 appearance-none focus:ring-blue-500 p-3 border text-right">
                    <option value="active" {{ old('status', $salesRep->user?->account_status ?? '') == 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="inactive" {{ old('status', $salesRep->user?->account_status ?? '') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                </select>
            </div>
        </div>

        <!-- Phone -->
        <div class="space-y-2">
            <label for="phone" class="block text-sm font-medium text-gray-700">رقم الجوال</label>
            <div class="relative">
                <input type="tel" 
                       name="phone" 
                       id="phone" 
                       required
                       value="{{ old('phone', isset($salesRep) ? (json_decode($salesRep->user?->contact_info, true)['phone'] ?? '') : '') }}"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border pr-10"
                       pattern="[0-9]{10}"
                       title="يجب إدخال 10 أرقام">
            </div>
        </div>
    </div>
</div>

<!-- Form Submit Button -->
<div class="flex justify-end pt-4 border-t border-gray-200">
    <button type="submit"
            class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-200">
        <span class="text-white">{{ $button_label ?? 'حفظ سفير العلامة التجارية' }}</span>
    </button>
</div>
    </div>
@push('scripts')
<script>
    flatpickr("#birthday", {
        locale: "ar",
        dateFormat: "Y-m-d",
        allowInput: true,
        defaultHour: 0,
        defaultMinute: 0,
 defaultDate: null,
    });

    flatpickr("#start_work_date", {
        locale: "ar",
        dateFormat: "Y-m-d",
        allowInput: true,
        defaultHour: 0,
        defaultMinute: 0,
 defaultDate: null,
    });

  document.getElementById('personal_image')?.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('preview').src = e.target.result;
                document.getElementById('image-preview').classList.remove('hidden');
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
function previewImage(input) {
    const preview = document.getElementById('preview');
    const previewContainer = document.getElementById('image-preview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewContainer.classList.remove('hidden');
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Restore preview if there was a validation error and old input exists
document.addEventListener('DOMContentLoaded', function() {
    @if(old('personal_image_preview'))
        const preview = document.getElementById('preview');
        const previewContainer = document.getElementById('image-preview');
        preview.src = '{{ old('personal_image_preview') }}';
        previewContainer.classList.remove('hidden');
    @endif
});
</script>
@endpush
