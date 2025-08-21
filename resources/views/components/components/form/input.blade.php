@props(['name', 'type' => 'text', 'value' => ''])
@php
    $old_name = str_replace('[', '.', $name);
    $old_name = str_replace(']', '', $old_name);
@endphp
<input type={{ $type }} value="{{ old($old_name, $value) }}" name="{{ $name }}" id="{{ $id ?? $name }}"
    {{ $attributes->class(['form-control', 'is-invalid' => $errors->has($old_name)]) }}>
