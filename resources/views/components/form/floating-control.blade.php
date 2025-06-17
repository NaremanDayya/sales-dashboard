<div class="form-floating mb-3">
    {{ $slot }}
    {{ $label }}
    <x-form.input-error name="{{ $attributes['name'] }}" />
</div>