@if ($errors->any())
    <div {{ $attributes->merge(['class' => 'text-center text-red-600 text-sm']) }}>
        <p style="color:red; font-size:20px; font-weight:700;">{{ rtrim($errors->first(), '.') }}</p>
    </div>
@endif
