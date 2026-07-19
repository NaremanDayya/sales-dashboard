@props(['label' => null, 'value' => null, 'icon' => null, 'trend' => null, 'trendUp' => true, 'accent' => 'indigo'])

@php
    $accents = [
        'indigo' => 'bg-indigo-50 text-indigo-600',
        'emerald' => 'bg-emerald-50 text-emerald-600',
        'amber' => 'bg-amber-50 text-amber-600',
        'rose' => 'bg-rose-50 text-rose-600',
        'sky' => 'bg-sky-50 text-sky-600',
    ];
    $accentClasses = $accents[$accent] ?? $accents['indigo'];
@endphp

<div {{ $attributes->class(['bg-white border border-gray-200 rounded-xl p-5 shadow-sm']) }}>
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="text-sm font-medium text-gray-500 truncate">{{ $label }}</p>
            <p class="mt-2 text-2xl font-semibold text-gray-900">{{ $value }}</p>

            @if($trend !== null)
                <p class="mt-2 inline-flex items-center gap-1 text-xs font-medium {{ $trendUp ? 'text-emerald-600' : 'text-rose-600' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        @if($trendUp)
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"/>
                        @endif
                    </svg>
                    {{ $trend }}
                </p>
            @endif
        </div>

        @if($icon)
            <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg {{ $accentClasses }}">
                {!! $icon !!}
            </span>
        @endif
    </div>
</div>
