<div x-data="clientWizard()" x-init="init()"
    class="max-w-4xl mx-auto bg-white rounded-xl shadow-md overflow-hidden p-6">
    <!-- Progress Indicator -->
  <div class="mb-8">
    <div class="flex justify-between items-center mb-2">
        <h2 class="text-2xl font-bold text-gray-800">معلومات العميل</h2>
        <span class="text-sm font-medium text-gray-600">خطوة <span x-text="step"></span> من 6</span>
    </div>
    <div class="w-full bg-gray-200 rounded-full h-2.5">
        <div class="bg-blue-600 h-2.5 rounded-full" :style="'width: ' + ((step / 6) * 100) + '%'"></div>
    </div>
</div>

    <!-- Hidden Inputs for All Steps -->
    <input type="hidden" name="company_name" :value="form.company_name">
    <input type="hidden" name="address" :value="form.address">
    <input type="hidden" name="contact_person" :value="form.contact_person">
    <input type="hidden" name="contact_position" :value="form.contact_position">
    <input type="hidden" name="phone" :value="form.phone">
    <input type="hidden" name="interest_status" :value="form.interest_status">

    <!-- Error Alert -->
    <x-form.alert name="error" class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6"></x-form.alert>

    <!-- Step 1: Company Name -->
    <template x-if="step === 1">
        <div class="space-y-6">
            <div>
                <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">اسم الشركة</label>
                <input type="text" id="company_name" name="company_name" value="{{ old('client_name') }}" x-model="form.company_name"
                    @input="unlockStep()" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border">
            </div>
            <div class="flex justify-end">
 <button type="button" @click="nextStep()" x-show="canContinue"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 foc>
                    :class="{'opacity-50 cursor-not-allowed': !canContinue}">
                    التالي <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
    <path fill-rule="evenodd" 
          d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" 
          clip-rule="evenodd" />
</svg>
                </button>
            </div>
        </div>
    </template>


    <!-- Step 3: Address -->
    <template x-if="step === 2">
        <div class="space-y-6">
            <div>
                <label for="address" class="block text-sm font-medium text-gray-700 mb-1">عنوان الشركة</label>
                <input type="text" id="address" name="address" x-model="form.address" @input="unlockStep()" value="{{ old('address') }}"  required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border">
            </div>
            <div class="flex justify-between">

 <button type="button" @click="nextStep()" x-show="canContinue"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 foc>
                    :class="{'opacity-50 cursor-not-allowed': !canContinue}">
                    التالي <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
    <path fill-rule="evenodd" 
          d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" 
          clip-rule="evenodd" />
</svg>
                </button>
            </div>
        </div>
    </template>

    <!-- Step 4: Contact Person -->
    <template x-if="step === 3">
        <div class="space-y-6">
            <div>
                <label for="contact_person" class="block text-sm font-medium text-gray-700 mb-1">الشخص المسؤول</label>
                <input type="text" id="contact_person" name="contact_person" value="{{ old('contact_person') }}"  x-model="form.contact_person"
                    @input="unlockStep()" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border">
            </div>
            <div class="flex justify-between">

 <button type="button" @click="nextStep()" x-show="canContinue"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 foc>
                    :class="{'opacity-50 cursor-not-allowed': !canContinue}">
                    التالي <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
    <path fill-rule="evenodd" 
          d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" 
          clip-rule="evenodd" />
</svg>
                </button>
            </div>
        </div>
    </template>

    <!-- Step 5: Contact Position -->
    <template x-if="step === 4">
        <div class="space-y-6">
            <div>
                <label for="contact_position" class="block text-sm font-medium text-gray-700 mb-1">
                    المنصب الوظيفي</label>
                <input type="text" id="contact_position" name="contact_position" value="{{ old('contact_position') }}"  x-model="form.contact_position"
                    @input="unlockStep()"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border"
                    placeholder="Manager, Owner, etc.">
            </div>
            <div class="flex justify-between">
 <button type="button" @click="nextStep()" x-show="canContinue"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 foc>
                    :class="{'opacity-50 cursor-not-allowed': !canContinue}">
                    التالي <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
    <path fill-rule="evenodd" 
          d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" 
          clip-rule="evenodd" />
</svg>
                </button>
            </div>
        </div>
    </template>

    <!-- Step 6: Phone -->
    <template x-if="step === 5">
        <div class="space-y-6">
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">رقم الجوال</label>
                <input type="tel" id="phone" name="phone" value="{{ old('') }}"  x-model="form.phone" @input="unlockStep()" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border">
            </div>
            <div class="flex justify-between">

 <button type="button" @click="nextStep()" x-show="canContinue"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 foc>
                    :class="{'opacity-50 cursor-not-allowed': !canContinue}">
                    التالي <svg xmlns="http://www.w3.org/2000/svg" class="mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
    <path fill-rule="evenodd" 
          d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" 
          clip-rule="evenodd" />
</svg>
                </button>
            </div>
        </div>
    </template>

    <!-- Step 7: Interest Status -->
    <template x-if="step === 6">
        <div class="space-y-6">
@php
    $selectedStatus = old('interest_status', $client->interest_status ?? '');
@endphp

<div>
    <label for="interest_status" class="block text-sm font-medium text-gray-700 mb-1">Interest Status</label>
    <select id="interest_status" name="interest_status" 
            x-model="form.interest_status"
            @change="unlockStep()" required
            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 p-3 border">
        <option value="" disabled {{ $selectedStatus == '' ? 'selected' : '' }}>حالة الاهتمام</option>
        <option value="interested" {{ $selectedStatus == 'interested' ? 'selected' : '' }}>مهتم</option>
        <option value="not interested" {{ $selectedStatus == 'not interested' ? 'selected' : '' }}>غير مهتم</option>
        <option value="neutral" {{ $selectedStatus == 'neutral' ? 'selected' : '' }}>مؤجل</option>
    </select>
</div>
<div class="flex justify-between">

                <button type="submit" x-show="canContinue"
                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                            clip-rule="evenodd" />
                    </svg>
                    {{ $button_label ?? 'إضافة العميل' }}
                </button>
            </div>        </div>
    </template>
</div>

<script>

function clientWizard() {
    return {
        step: 1,
        canContinue: false,
        previewLogo: null,
        form: {
            company_name: '',
            address: '',
            contact_person: '',
            contact_position: '',
            phone: '',
            interest_status: '',
        },
init() {
    this.canContinue = false;
    
    // Initialize form values - prioritize old input, then existing client, then empty
    this.form = {
        company_name: '{{ old('company_name', isset($client) ? $client->company_name : '') }}',
        address: '{{ old('address', isset($client) ? $client->address : '') }}',
        contact_person: '{{ old('contact_person', isset($client) ? $client->contact_person : '') }}',
        contact_position: '{{ old('contact_position', isset($client) ? $client->contact_position : '') }}',
        phone: '{{ old('phone', isset($client) ? $client->phone : '') }}',
        interest_status: '{{ old('interest_status', isset($client) ? $client->interest_status : '') }}',
    };

    // Handle logo preview - prioritize old input, then existing client
    @if(old('company_logo') || (isset($client) && $client->company_logo))
        this.previewLogo = '{{ old('company_logo') ? 
                            old('company_logo') : 
                            (isset($client) ? asset('storage/' . $client->company_logo) : '') }}';
    @endif

    // Set the current step based on which fields have errors
    this.setInitialStep();
},

        handleLogoUpload(event) {
            const file = event.target.files[0];
            if (file) {
                // Create preview
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.previewLogo = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        },

        // Rest of your existing wizard methods...
        nextStep() {
            this.step++;
            this.canContinue = false;
        },

        unlockStep() {
            const value = this.currentFieldValue();
            this.canContinue = value !== '' && value !== null;
        },
setInitialStep() {
    const errors = @json($errors->keys() ?? []);
    
    if (errors.includes('company_name')) {
        this.step = 1;
    } else if (errors.includes('address')) {
        this.step = 2;
    } else if (errors.includes('contact_person')) {
        this.step = 3;
    } else if (errors.includes('contact_position')) {
        this.step = 4;
    } else if (errors.includes('phone')) {
        this.step = 5;
    } else if (errors.includes('interest_status')) {
        this.step = 6;
    } else {
        this.step = 1;
    }
    
    // Unlock the step if we're returning to a step with existing data
    if (this.currentFieldValue()) {
        this.canContinue = true;
    }
}
        currentFieldValue() {
            switch (this.step) {
                case 1: return this.form.company_name;
                case 2: return this.form.address;
                case 3: return this.form.contact_person;
                case 4: return this.form.contact_position;
                case 5: return this.form.phone;
                case 6: return this.form.interest_status;
                default: return null;
            }
        }
    };
}
</script>
