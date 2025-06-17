@props(['src' => null, 'name' => null])

@php
    // Closure for mb_ucfirst
    $mb_ucfirst = function($string) {
        $firstChar = mb_substr($string, 0, 1);
        $then = mb_substr($string, 1);
        return mb_strtoupper($firstChar) . $then;
    };

    $initials = '';
    if ($name) {
        $words = preg_split('/\s+/u', trim($name));
        if (count($words) === 1) {
            // single word - take first 2 characters uppercase
            $initials = mb_strtoupper(mb_substr($words[0], 0, 2));
        } else {
            // multiple words - first char of first two words uppercase
            $initials = mb_strtoupper(mb_substr($words[0], 0, 1)) . mb_strtoupper(mb_substr($words[1], 0, 1));
        }
    }
@endphp

<div {{ $attributes->merge(['class' => 'shrink-0 inline-flex items-center justify-center rounded-full border border-gray-300 dark:border-gray-600 w-10 h-10 text-sm font-semibold text-white']) }}
    style="{{ $src ? '' : 'background-color:#a855f7;' }}">

    @if ($src)
        <img
            src="{{ $src }}"
            alt="{{ $name ?? 'Avatar' }}"
            class="w-full h-full object-cover rounded-full"
            loading="lazy"
        />
    @elseif ($initials)
        <span class="select-none leading-none">{{ $initials }}</span>
    @else
        <svg
            class="w-6 h-6 text-gray-400"
            fill="currentColor"
            viewBox="0 0 24 24"
            aria-hidden="true">
            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
    @endif
</div>
