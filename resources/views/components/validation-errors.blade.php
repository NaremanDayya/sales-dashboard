@if ($errors->any())
    <div {{ $attributes->merge(['class' => 'rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-center text-sm font-medium text-red-700']) }}>
        {{ rtrim($errors->first(), '.') }}
    </div>
@endif
