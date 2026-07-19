@props(['title' => null, 'subtitle' => null])

<div {{ $attributes->class(['flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mb-6']) }}>
    <div class="min-w-0">
        @if($title)
            <h1 class="text-xl font-semibold text-gray-900 truncate">{{ $title }}</h1>
        @endif
        @if($subtitle)
            <p class="mt-1 text-sm text-gray-500">{{ $subtitle }}</p>
        @endif
        {{ $slot }}
    </div>

    @isset($actions)
        <div class="flex shrink-0 flex-wrap items-center gap-2">
            {{ $actions }}
        </div>
    @endisset
</div>
