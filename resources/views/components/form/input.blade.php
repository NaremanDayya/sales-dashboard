@props(['name', 'type' => 'text', 'value' => ''])
@php
    $old_name = str_replace('[', '.', $name);
    $old_name = str_replace(']', '', $old_name);
@endphp
<input type={{ $type }} value="{{ old($old_name, $value) }}" name="{{ $name }}" id="{{ $id ?? $name }}"
    {{ $attributes->class([
        'block w-full rounded-lg shadow-sm sm:text-sm focus:ring-indigo-500',
        'border-gray-300 focus:border-indigo-500' => !$errors->has($old_name),
        'border-red-300 focus:border-red-500' => $errors->has($old_name),
    ]) }}>
