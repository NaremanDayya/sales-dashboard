<div class="w-full max-w-md mx-auto p-6 bg-white rounded shadow">
    @if (session()->has('success'))
        <div class="p-4 mb-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
    @endif

    @if ($step === 1)
        <div>
            <label class="block mb-1">Company Name</label>
            <input type="text" wire:model.defer="company_name" class="input" />
            @error('company_name') <span class="text-red-600">{{ $message }}</span> @enderror
        </div>
    @elseif ($step === 2)
        <div>
            <label class="block mb-1">Company Logo</label>
            <input type="file" wire:model="company_logo" class="input" />
            @error('company_logo') <span class="text-red-600">{{ $message }}</span> @enderror
        </div>
    @elseif ($step === 3)
        <div>
            <label class="block mb-1">Address</label>
            <input type="text" wire:model.defer="address" class="input" />
            @error('address') <span class="text-red-600">{{ $message }}</span> @enderror
        </div>
    @elseif ($step === 4)
        <div>
            <label class="block mb-1">Contact Person</label>
            <input type="text" wire:model.defer="contact_person" class="input" />
            @error('contact_person') <span class="text-red-600">{{ $message }}</span> @enderror
        </div>
    @elseif ($step === 5)
        <div>
            <label class="block mb-1">Contact Position</label>
            <input type="text" wire:model.defer="contact_position" class="input" />
            @error('contact_position') <span class="text-red-600">{{ $message }}</span> @enderror
        </div>
    @elseif ($step === 6)
        <div>
            <label class="block mb-1">Phone</label>
            <input type="text" wire:model.defer="phone" class="input" />
            @error('phone') <span class="text-red-600">{{ $message }}</span> @enderror
        </div>
    @elseif ($step === 7)
        <div>
            <label class="block mb-1">Interest Status</label>
            <select wire:model.defer="interest_status" class="input">
                <option value="">Choose...</option>
                <option value="interested">Interested</option>
                <option value="not_interested">Not Interested</option>
                <option value="pending">Pending</option>
            </select>
            @error('interest_status') <span class="text-red-600">{{ $message }}</span> @enderror
        </div>
    @endif

    <button wire:click="next"
        class="mt-4 w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">
        {{ $step < 7 ? 'Next' : 'Submit' }}
    </button>
</div>

<style>
.input {
    @apply block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50;
}
</style>
