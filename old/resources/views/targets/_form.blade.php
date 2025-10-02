<x-form.alert name="error" class="alert-danger"></x-form.alert>

{{-- Sales Rep --}}
<x-form.floating-control name="user_id">
    <x-slot:label>
        <label for="user_id">Sales Representative</label>
    </x-slot:label>
    <select name="user_id" id="user_id" class="form-control" required>
        @foreach($salesReps as $rep)
            <option value="{{ $rep->id }}" {{ old('user_id', $target->user_id ?? '') == $rep->id ? 'selected' : '' }}>
                {{ $rep->name }}
            </option>
        @endforeach
    </select>
</x-form.floating-control>

{{-- Service --}}
<x-form.floating-control name="service_id">
    <x-slot:label>
        <label for="service_id">Service</label>
    </x-slot:label>
    <select name="service_id" id="service_id" class="form-control" required>
        @foreach($services as $service)
            <option value="{{ $service->id }}" {{ old('service_id', $target->service_id ?? '') == $service->id ? 'selected' : '' }}>
                {{ $service->name }}
            </option>
        @endforeach
    </select>
</x-form.floating-control>

{{-- Month --}}
<x-form.floating-control name="month">
    <x-slot:label>
        <label for="month">Month</label>
    </x-slot:label>
    <input type="number" name="month" id="month" class="form-control" value="{{ old('month', $target->month ?? '') }}" required min="1" max="12">
</x-form.floating-control>

{{-- Year --}}
<x-form.floating-control name="year">
    <x-slot:label>
        <label for="year">Year</label>
    </x-slot:label>
    <input type="number" name="year" id="year" class="form-control" value="{{ old('year', $target->year ?? '') }}" required>
</x-form.floating-control>

{{-- Target Amount --}}
<x-form.floating-control name="target_amount">
    <x-slot:label>
        <label for="target_amount">Target Amount</label>
    </x-slot:label>
    <input type="number" name="target_amount" id="target_amount" class="form-control" value="{{ old('target_amount', $target->target_amount ?? '') }}" required min="0">
</x-form.floating-control>

{{-- Submit Button --}}
<button type="submit" class="btn ms-2" style="background-color: #198754; color: white;">
    {{ $button_label }}
</button>
