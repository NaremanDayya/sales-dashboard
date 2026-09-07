@props([
    'name',
])
{{-- @php
    $class = $name =='error'?'danger':'success';
@endphp --}}
@if (session()->has($name))
    <div {{
    $attributes
    ->class(['rounded-lg border p-4 text-sm'])
    ->merge([
        'id' => 'x',//لو ما مررنا قيمة id رح ياخد x
    ])
    }}>
        {{ session($name) }}
    </div>
@endif
