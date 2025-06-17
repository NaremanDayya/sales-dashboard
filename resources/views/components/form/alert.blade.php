@props([
    'name',
])
{{-- @php
    $class = $name =='error'?'danger':'success';
@endphp --}}
@if (session()->has($name))
{{-- <div class="alert alert-{{ $class }}" {{ $attributes }}> --}}
    <div {{ 
    $attributes 
    ->class(['alert'])//رح يدمج كلمة alert قبل قيمة الكلاس يلي رح نمررها
    ->merge([
        'id' => 'x',//لو ما مررنا قيمة id رح ياخد x
    ])
    }}>
        {{ session($name) }}
    </div>
@endif
