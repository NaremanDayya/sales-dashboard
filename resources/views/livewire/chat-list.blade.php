<div class="flex flex-col h-full bg-white border-r border-gray-200">
    <header class="px-3 z-10 bg-white sticky top-0 w-full py-10">
        @include('partials.chat-list-header')
    </header>

    <!-- Conversations List -->
    <div class="flex-1 overflow-y-auto" x-ref="container"
         x-data="{
        loading: @entangle('loading'),
        hasMore: @entangle('hasMore'),
        throttle: false,
        observer: null,
        setupObserver() {
            const container = this.$refs.container || this.$el;
            const sentinel = this.$refs.sentinel;

            // Pick appropriate root: if container isn't scrollable here, fall back to viewport
            const style = getComputedStyle(container);
            const isScrollable = ['auto', 'scroll'].includes(style.overflowY);
            const rootEl = isScrollable ? container : null; // null => viewport

            if (this.observer) this.observer.disconnect();

            const options = { root: rootEl, rootMargin: '0px 0px 200px 0px', threshold: 0 };
            this.observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !this.loading && this.hasMore && !this.throttle) {
                        this.throttle = true;
                        @this.loadMore();
                        setTimeout(() => { this.throttle = false; }, 400);
                    }
                });
            }, options);
            if (sentinel) this.observer.observe(sentinel);
        },
        init() {
            const container = this.$refs.container || this.$el;
            // Ensure scroll is visible when used as its own scroll root
            container.style.overflowX = 'hidden';
            if (!container.style.overflowY) container.style.overflowY = 'auto';

            this.setupObserver();

            // Listen for real-time updates
            Livewire.on('conversationUpdated', () => {
                @this.refresh();
            });

            // Stop observing when no more data
            this.$watch('hasMore', (val) => {
                const sentinel = this.$refs.sentinel;
                if (!val && this.observer && sentinel) {
                    this.observer.unobserve(sentinel);
                }
            });

            // Re-attach observer after Livewire DOM updates (e.g., when selecting a conversation)
            if (window.Livewire && typeof window.Livewire.hook === 'function') {
                window.Livewire.hook('message.processed', (message, component) => {
                    // Only for this component instance
                    try {
                        if (component.id === this.$wire.__instance.id) {
                            this.$nextTick(() => this.setupObserver());
                        }
                    } catch (e) { /* noop */ }
                });
            }

            // Prefetch if list doesn't fill container
            const prefetchIfShort = () => {
                const root = this.$refs.container || this.$el;
                if (this.hasMore && !this.loading && root.scrollHeight <= root.clientHeight + 50) {
                    @this.loadMore();
                    setTimeout(prefetchIfShort, 200);
                }
            };
            setTimeout(prefetchIfShort, 200);

            // Fallback: manually detect scroll bottom on container
            const onScroll = () => {
                try {
                    const el = this.$refs.container || this.$el;
                    const atBottom = (el.scrollTop + el.clientHeight) >= (el.scrollHeight - 2);
                    if (atBottom && this.hasMore && !this.loading && !this.throttle) {
                        this.throttle = true;
                        @this.loadMore();
                        setTimeout(() => { this.throttle = false; }, 400);
                    }
                } catch (e) { /* noop */ }
            };
            container.addEventListener('scroll', onScroll, { passive: true });
        },
        destroy() {
            if (this.observer) this.observer.disconnect();
        }
     }"
         style="overflow-y: auto; overflow-x: hidden;">

        <!-- Conversations -->
        <div class="divide-y divide-gray-100">
            @foreach($conversations as $conversation)
                @php
                    // Safe data access with null coalescing
                    $client = $conversation->client ?? null;
                    $companyName = $client->company_name ?? 'Unknown Company';
                    $companyLogo = $client->company_logo ?? null;
                    $salesRepName = $client->salesRep->name ?? 'Sales Rep';
                @endphp

                <a
                    wire:key="conv-{{ $conversation->id }}"
                    href="{{ route('client.chat', ['client' => $client->id ?? '', 'conversation' => $conversation->id]) }}"
                    class="flex items-center p-4 transition-colors duration-200 cursor-pointer group {{ $conversation->id === ($selectedConversation->id ?? null) ? 'bg-blue-50 border-r-4 border-blue-500' : 'hover:bg-gray-50' }}">

                    <!-- Avatar -->
                    <div class="relative flex-shrink-0">
                        <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                            @if($companyLogo)
                                <img
                                    src="{{ $companyLogo }}"
                                    alt="{{ $companyName }}"
                                    class="max-h-full max-w-full object-contain bg-white rounded-full">

                            @else
                                <div class="w-full h-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold text-sm">
                                    {{ substr($companyName, 0, 1) }}
                                </div>
                            @endif
                        </div>

                        <!-- Online Status -->
                        <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 rounded-full border-2 border-white"></div>
                    </div>

                    <!-- Conversation Info -->
                    <div class="flex-1 min-w-0 mr-3">
                        <div class="flex items-center justify-between">
                            <h3 class="text-sm font-semibold text-gray-900 truncate">{{ $companyName }}</h3>
                            <span class="text-xs text-gray-500 whitespace-nowrap" x-text="formatTime('{{ $conversation->latest_message_time }}')"></span>
                        </div>

                        <div class="flex items-center justify-between mt-1">
                            <p class="text-sm text-gray-600 truncate flex items-center space-x-1 space-x-reverse">
                                @if(($conversation->latest_message_sender_id ?? null) == auth()->id())
                                    @if($conversation->is_last_message_read ?? false)
                                        <span class="text-blue-500">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 16 16">
                                                <path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0l7-7z"/>
                                                <path d="M8.146 11.354l-.896-.897.707-.707.543.543 6.646-6.647a.5.5 0 0 1 .708.708l-7 7a.5.5 0 0 1-.708 0z"/>
                                            </svg>
                                        </span>
                                    @endif
                                @endif
                                <span>{{ ($conversation->latest_message_text ?? '') === 'like' ? '👍' : ($conversation->latest_message_text ?? '') }}</span>
                            </p>

                            @if(($conversation->unread_messages_count ?? 0) > 0)
                                <span class="bg-blue-500 text-white text-xs rounded-full px-2 py-1 min-w-5 text-center">{{ $conversation->unread_messages_count }}</span>
                            @endif
                        </div>

                        <p class="text-xs text-gray-500 mt-1 truncate">{{ $salesRepName }}</p>
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Sentinel for IntersectionObserver -->
        <div x-ref="sentinel" x-show="hasMore" class="h-1"></div>

        <!-- Loading More -->
        <div x-show="loading" class="p-4 text-center" x-cloak>
            <div class="inline-flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-sm text-gray-600">جاري تحميل المزيد...</span>
            </div>
        </div>

        <!-- No Conversations -->
        @if(!$loading && $conversations->count() === 0)
            <div class="p-8 text-center">
                <div class="text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <p class="text-lg font-medium mb-2">لا توجد محادثات</p>
                    <p class="text-sm">ابدأ محادثة جديدة من خلال النقر على زر "+"</p>
                </div>
            </div>
        @endif
    </div>
    @include('partials.client-selection-modal')
    @vite('resources/js/client-chat.js')

    <script>
        function formatTime(dateString) {
            if (!dateString) return '';
            try {
                const date = new Date(dateString);
                const now = new Date();
                const diffMs = now - date;
                const diffMins = Math.floor(diffMs / 60000);
                const diffHours = Math.floor(diffMs / 3600000);
                const diffDays = Math.floor(diffMs / 86400000);

                if (diffMins < 1) return 'الآن';
                if (diffMins < 60) return `${diffMins} د`;
                if (diffHours < 24) return `${diffHours} س`;
                if (diffDays < 7) return `${diffDays} يوم`;
                return date.toLocaleDateString('ar-SA');
            } catch (e) {
                return '';
            }
        }
    </script>
</div>
