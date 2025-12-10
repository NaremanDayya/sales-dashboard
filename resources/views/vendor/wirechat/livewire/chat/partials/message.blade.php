@use('Namu\WireChat\Facades\WireChat')


@php

   $isSameAsNext = ($message?->sendable_id === $nextMessage?->sendable_id) && ($message?->sendable_type === $nextMessage?->sendable_type);
   $isNotSameAsNext = !$isSameAsNext;
   $isSameAsPrevious = ($message?->sendable_id === $previousMessage?->sendable_id) && ($message?->sendable_type === $previousMessage?->sendable_type);
   $isNotSameAsPrevious = !$isSameAsPrevious;
@endphp

<div


{{-- We use style here to make it easy for dynamic and safe injection --}}
@style([
'background-color:var(--wc-brand-primary)' => $belongsToAuth==true
])

@class([
    'flex flex-wrap max-w-fit text-[15px] border border-gray-200/40 dark:border-none rounded-xl p-2.5 flex flex-col text-black bg-[#f6f6f8fb]',
    'text-white' => $belongsToAuth, // Background color for messages sent by the authenticated user
    'bg-[var(--wc-light-secondary)] dark:bg-[var(--wc-dark-secondary)] dark:text-white' => !$belongsToAuth,

    // Message styles based on position and ownership

    // RIGHT
    // First message on RIGHT
    'rounded-br-md rounded-tr-2xl' => ($isSameAsNext && $isNotSameAsPrevious && $belongsToAuth),

    // Middle message on RIGHT
    'rounded-r-md' => ($isSameAsPrevious && $belongsToAuth),

    // Standalone message RIGHT
    'rounded-br-xl rounded-r-xl' => ($isNotSameAsPrevious && $isNotSameAsNext && $belongsToAuth),

    // Last Message on RIGHT
    'rounded-br-2xl' => ($isNotSameAsNext && $belongsToAuth),

    // LEFT
    // First message on LEFT
    'rounded-bl-md rounded-tl-2xl' => ($isSameAsNext && $isNotSameAsPrevious && !$belongsToAuth),

    // Middle message on LEFT
    'rounded-l-md' => ($isSameAsPrevious && !$belongsToAuth),

    // Standalone message LEFT
    'rounded-bl-xl rounded-l-xl' => ($isNotSameAsPrevious && $isNotSameAsNext && !$belongsToAuth),

    // Last message on LEFT
    'rounded-bl-2xl' => ($isNotSameAsNext && !$belongsToAuth),
])
>
@if (!$belongsToAuth && $isGroup)
<div    
    @class([
        'shrink-0 font-medium text-purple-500',
        // Hide avatar if the next message is from the same user
        'hidden' => $isSameAsPrevious
    ])>
    {{ $message?->sendable?->display_name }}
</div>
@endif

<pre class="whitespace-pre-line tracking-normal text-sm md:text-base dark:text-white lg:tracking-normal"
    style="font-family: inherit;">
    @if(str_contains($message?->body, '||IMAGE||'))
        @php
            $parts = explode('||IMAGE||', $message->body);
            $caption = trim($parts[0]);
            $imagePath = trim($parts[1] ?? '');
        @endphp
        <div class="flex flex-col gap-2">
            @if($imagePath)
                <div class="relative group/image">
                    <img src="{{ asset('storage/' . $imagePath) }}"
                         alt="Shared image"
                         class="max-w-full max-h-96 rounded-lg object-contain cursor-pointer hover:opacity-95 transition-opacity"
                         onclick="window.open('{{ asset('storage/' . $imagePath) }}', '_blank')">
                    <!-- Download button overlay -->
                    <a href="{{ asset('storage/' . $imagePath) }}"
                       download
                       class="absolute top-2 right-2 p-2 bg-black/50 hover:bg-black/70 text-white rounded-full opacity-0 group-hover/image:opacity-100 transition-opacity">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </a>
                </div>
            @endif
            @if($caption && $caption !== '[صورة]')
                <span class="text-sm md:text-base">{{ $caption }}</span>
            @endif
        </div>
    @else
        {!! $message?->body !!}
    @endif
</pre>


{{-- Display the created time based on different conditions --}}
<span
@class(['text-[11px] ml-auto ',  'text-gray-700 dark:text-gray-300' => !$belongsToAuth,'text-gray-100' => $belongsToAuth])>
    @php
        // If the message was created today, show only the time (e.g., 1:00 AM)
        echo $message?->created_at->format('H:i');
    @endphp
</span>

</div>
