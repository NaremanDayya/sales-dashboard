<div>
    <div x-data="{
        type: 'all',
        conversation: $wire.entangle('conversation'),
        loading: $wire.entangle('loading'),
        hasMore: $wire.entangle('hasMore'),
        init() {
            setTimeout(() => {
                if (this.conversation) {
                    const conversationElement = document.getElementById('conversation-'+this.conversation);
                    if(conversationElement) {
                        conversationElement.scrollIntoView({'behavior':'smooth'});
                    }
                }
            }, 100);

            // Infinite scroll implementation - FIXED
            const mainElement = this.$el.querySelector('main');
            const scrollHandler = () => {
                if (this.loading || !this.hasMore) return;

                const scrollTop = mainElement.scrollTop;
                const scrollHeight = mainElement.scrollHeight;
                const clientHeight = mainElement.clientHeight;

                // Load more when 100px from bottom - FIXED LOGIC
                if (scrollHeight - scrollTop <= clientHeight + 100) {
                    this.loadMore();
                }
            };

            mainElement.addEventListener('scroll', scrollHandler);

            // Cleanup event listener
            this.$el._scrollHandler = scrollHandler;

            Echo.private('users.{{Auth()->User()->id}}')
            .notification((notification)=>{
                if(notification['type']== 'App\\Notifications\\MessageRead'||notification['type']== 'App\\Notifications\\MessageSent')
                {
                    $wire.refresh();
                }
            });
        },
        async loadMore() {
            if (this.loading || !this.hasMore) return;

            this.loading = true;
            try {
                await $wire.call('loadMore');
            } catch (error) {
                console.error('Error loading more conversations:', error);
                this.loading = false;
            }
        },
        destroy() {
            // Cleanup event listener
            const mainElement = this.$el.querySelector('main');
            if (mainElement && this.$el._scrollHandler) {
                mainElement.removeEventListener('scroll', this.$el._scrollHandler);
            }
        }
    }" class="flex flex-col transition-all h-full overflow-hidden">

        <header class="px-3 z-10 bg-white sticky top-0 w-full py-10">
            @include('partials.chat-list-header')
        </header>

        <main class="overflow-y-scroll overflow-hidden grow h-full relative" style="contain:content">
            {{-- chatlist --}}
            <ul id="conversationsList" class="p-2 grid w-full space-y-2">
                @if ($conversations && $conversations->count() > 0)
                    @foreach ($conversations as $key => $conversation)
                        <li id="conversation-{{$conversation->id}}" wire:key="{{$conversation->id}}"
                            data-name="{{ Str::lower($conversation->getReceiver()->name) }}"
                            data-company="{{ Str::lower($conversation->client->company_name) }}"
                            class="py-3 hover:bg-gray-50 rounded-2xl dark:hover:bg-gray-700/70 transition-colors duration-150 flex gap-4 relative w-full cursor-pointer px-2 {{$conversation->id==$selectedConversation?->id ? 'bg-gray-100/70':''}}">
                            <!-- ... your existing conversation item code ... -->
                        </li>
                    @endforeach
                @else
                    <li class="text-center py-8 text-gray-500">
                        لا توجد محادثات
                    </li>
                @endif
            </ul>

            {{-- Loading indicator --}}
            <div x-show="loading" class="text-center py-4">
                <div class="inline-flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    جاري تحميل المزيد...
                </div>
            </div>

            {{-- No more conversations --}}
            <div x-show="!hasMore && {{ $conversations->count() }} > 0" class="text-center py-4 text-gray-500">
                لا توجد محادثات أخرى
            </div>

            <div style="min-height: 20vh;"></div>
        </main>

        @include('partials.client-selection-modal')
    </div>

    @vite('resources/js/client-chat.js')
</div>
