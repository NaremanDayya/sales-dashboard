@props(['color' => 'gray'])

@php
    $colors = [
        'gray' => 'bg-gray-100 text-gray-700',
        'indigo' => 'bg-indigo-50 text-indigo-700',
        'emerald' => 'bg-emerald-50 text-emerald-700',
        'amber' => 'bg-amber-50 text-amber-700',
        'rose' => 'bg-rose-50 text-rose-700',
        'sky' => 'bg-sky-50 text-sky-700',
    ];
    $classes = $colors[$color] ?? $colors['gray'];
@endphp

<span {{ $attributes->class(['inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-medium', $classes]) }}>
    {{ $slot }}
</span>
