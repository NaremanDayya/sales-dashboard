@props(['title' => 'لا توجد بيانات', 'description' => null, 'icon' => null])

<div {{ $attributes->class(['flex flex-col items-center justify-center text-center py-14 px-4']) }}>
    <span class="flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-gray-400 mb-4">
        {!! $icon ?? '<svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>' !!}
    </span>
    <h3 class="text-sm font-semibold text-gray-900">{{ $title }}</h3>
    @if($description)
        <p class="mt-1 text-sm text-gray-500 max-w-sm">{{ $description }}</p>
    @endif
    @isset($actions)
        <div class="mt-4">{{ $actions }}</div>
    @endisset
</div>
