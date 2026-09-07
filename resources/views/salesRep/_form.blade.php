@error('error')
    <div class="text-red-600 text-sm mt-2">
        {{ $message }}<br>
        يرجى إعادة إدخال كلمة المرور
    </div>
@enderror
<div class="max-w-4xl mx-auto bg-white border border-gray-200 rounded-xl shadow-sm p-6 sm:p-8 space-y-6">
    <!-- Error Alert -->
    @if ($errors->any())
        <div class="rounded-lg border border-red-200 bg-red-50 text-red-700 p-4 text-sm">
            <ul class="list-disc ps-4 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Personal Information Section -->
    <div class="bg-gray-50 rounded-lg p-4 sm:p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">المعلومات الشخصية</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Full Name -->
            <div>
                <x-input-label for="name" value="الإسم الكامل" />
                <x-text-input type="text" id="name" name="name" value="{{ old('name', $salesRep->name ?? $salesRep->user?->name ?? '') }}" placeholder="الإسم الكامل" required />
            </div>

            <!-- Email -->
            <div>
                <x-input-label for="email" value="البريد الإلكتروني" />
                <x-text-input type="email" id="email" name="email" value="{{ old('email', $salesRep->user?->email ?? '') }}" placeholder="البريد الإلكتروني" required />
            </div>

            <!-- Password -->
            <div>
                <x-input-label for="password" value="كلمة المرور" />
                <x-text-input type="password" id="password" name="password" />
            </div>
        </div>

        <!-- Additional Personal Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-4">
            <!-- Birthday -->
            <div>
                <x-input-label for="birthday" value="تاريخ الميلاد" />
                <x-text-input type="text" id="birthday" name="birthday"
                    value="{{ old('birthday', isset($salesRep) && $salesRep->user && $salesRep->user->birthday ? \Carbon\Carbon::parse($salesRep->user->birthday)->format('Y-m-d') : '') }}"
                    placeholder="يوم / شهر / سنة" />
            </div>

            <!-- ID Card -->
            <div>
                <x-input-label for="id_card" value="رقم الهوية" />
                <x-text-input type="text" id="id_card" name="id_card" value="{{ old('id_card', $salesRep->user?->id_card ?? '') }}" />
            </div>

            <!-- Nationality -->
            <div>
                <x-input-label for="nationality" value="الجنسية" />
                <x-text-input type="text" id="nationality" name="nationality" value="{{ old('nationality', $salesRep->user?->nationality ?? '') }}" />
            </div>

            <!-- Gender -->
            <div>
                <x-input-label for="gender" value="الجنس" />
                <select id="gender" name="gender"
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">اختر الجنس</option>
                    <option value="male" {{ old('gender', $salesRep->user?->gender ?? '') == 'male' ? 'selected' : '' }}>ذكر</option>
                    <option value="female" {{ old('gender', $salesRep->user?->gender ?? '') == 'female' ? 'selected' : '' }}>أنثى</option>
                </select>
            </div>

            <!-- Personal Image -->
            <div class="md:col-span-2">
                <x-input-label for="personal_image" value="الصورة الشخصية" />

                @if(isset($salesRep) && $salesRep->user?->personal_image)
                    <div class="mb-3">
                        <span class="block text-sm text-gray-500 mb-1">الصورة الحالية:</span>
                        <img src="{{ asset('storage/' . $salesRep->user?->personal_image) }}"
                             alt="الصورة الشخصية الحالية"
                             class="h-16 w-16 rounded-full object-cover border-2 border-white shadow-sm">
                    </div>
                @endif

                <input type="file" id="personal_image" name="personal_image"
                       accept="image/png,image/jpeg,image/jpg,image/webp"
                       class="block w-full text-sm text-gray-700 bg-white rounded-lg border border-gray-300 cursor-pointer focus:outline-none focus:border-indigo-500 focus:ring-indigo-500 file:me-3 file:py-2.5 file:px-4 file:border-0 file:bg-gray-50 file:text-gray-700 file:text-sm"
                       onchange="previewImage(this)">
                <div id="image-preview" class="mt-2 hidden">
                    <span class="block text-sm text-gray-500 mb-1">الصورة المحددة:</span>
                    <img id="preview" class="h-16 w-16 rounded-full object-cover border-2 border-white shadow-sm">
                </div>

                @if(isset($salesRep) && $salesRep->user?->personal_image)
                    <div class="flex items-center gap-2 mt-2">
                        <input type="hidden" name="remove_personal_image" value="0">
                        <x-checkbox id="remove_personal_image" name="remove_personal_image" value="1" :checked="old('remove_personal_image', false)" />
                        <label for="remove_personal_image" class="text-sm text-gray-700">إزالة الصورة الحالية</label>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Work Information Section -->
    <div class="bg-gray-50 rounded-lg p-4 sm:p-5">
        <h3 class="text-sm font-semibold text-gray-900 mb-4 pb-2 border-b border-gray-200">معلومات العمل</h3>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Start Work Date -->
            <div>
                <x-input-label for="start_work_date" value="تاريخ بداية العمل" />
                <x-text-input type="date" id="start_work_date" name="start_work_date"
                    value="{{ old('start_work_date', isset($salesRep) && $salesRep->start_work_date ? \Carbon\Carbon::parse($salesRep->start_work_date)->format('Y-m-d') : '') }}"
                    required />
            </div>

            <!-- Account Status -->
            <div>
                <x-input-label for="status" value="حالة الحساب" />
                <select id="status" name="status" required
                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="active" {{ old('status', $salesRep->user?->account_status ?? '') == 'active' ? 'selected' : '' }}>نشط</option>
                    <option value="inactive" {{ old('status', $salesRep->user?->account_status ?? '') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                </select>
            </div>

            <!-- Stop Work Date (only relevant when marking the rep inactive) -->
            <div id="stop_work_date_wrapper" class="{{ old('status', $salesRep->user?->account_status ?? '') == 'inactive' ? '' : 'hidden' }}">
                <x-input-label for="stop_work_date" value="تاريخ توقف العمل" />
                <x-text-input type="date" id="stop_work_date" name="stop_work_date"
                    value="{{ old('stop_work_date', isset($salesRep) && $salesRep->stop_work_date ? \Carbon\Carbon::parse($salesRep->stop_work_date)->format('Y-m-d') : '') }}" />
            </div>

            <!-- Phone -->
            <div>
                <x-input-label for="phone" value="رقم الجوال" />
                <x-text-input type="tel" name="phone" id="phone" required
                    value="{{ old('phone', isset($salesRep) ? (json_decode($salesRep->user?->contact_info, true)['phone'] ?? '') : '') }}"
                    pattern="[0-9]{10}" title="يجب إدخال 10 أرقام" />
            </div>
        </div>
    </div>

    <!-- Form Submit Button -->
    <div class="flex justify-end pt-4 border-t border-gray-100">
        <x-primary-button type="submit" class="px-6 py-3">
            {{ $button_label ?? 'حفظ سفير العلامة التجارية' }}
        </x-primary-button>
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

    flatpickr("#stop_work_date", {
        locale: "ar",
        dateFormat: "Y-m-d",
        allowInput: true,
        defaultHour: 0,
        defaultMinute: 0,
 defaultDate: null,
    });

    // Show/require the stop-work-date field only while the account is being marked inactive
    function toggleStopWorkDate() {
        const statusSelect = document.getElementById('status');
        const wrapper = document.getElementById('stop_work_date_wrapper');
        const stopDateInput = document.getElementById('stop_work_date');
        if (!statusSelect || !wrapper || !stopDateInput) return;

        if (statusSelect.value === 'inactive') {
            wrapper.classList.remove('hidden');
            stopDateInput.setAttribute('required', 'required');
        } else {
            wrapper.classList.add('hidden');
            stopDateInput.removeAttribute('required');
            // Clear any stale stop date left over from a previous inactive period so
            // reactivating with a new start date can't fail the after_or_equal check.
            stopDateInput.value = '';
        }
    }
    document.getElementById('status')?.addEventListener('change', toggleStopWorkDate);
    document.addEventListener('DOMContentLoaded', toggleStopWorkDate);

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
