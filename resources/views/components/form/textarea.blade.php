@props([
    'name', 'value' => '', 'id' => null
    ])

    <textarea
     name="{{ $name }}"
      id="{{ $id ?? $name }}"
      {{ $attributes->class([
        'block w-full rounded-lg shadow-sm sm:text-sm focus:ring-indigo-500',
        'border-gray-300 focus:border-indigo-500' => !$errors->has($name),
        'border-red-300 focus:border-red-500' => $errors->has($name),
    ]) }}>{{ old($name, $value) }}</textarea>

