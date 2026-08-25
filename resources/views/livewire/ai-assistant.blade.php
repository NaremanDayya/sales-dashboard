<div x-data="{ aiOpen: false }" class="relative">
    <button @click="aiOpen = !aiOpen"
            class="p-2 text-gray-500 hover:text-purple-600 dark:hover:text-purple-400 rounded-full hover:bg-gray-100 dark:hover:bg-neutral-800 transition-colors duration-200 relative"
            title="المساعد الذكي">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.987-2.386l-.548-.547z" />
        </svg>
    </button>

    <div x-show="aiOpen" @click.away="aiOpen = false"
         x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1"
         x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1"
         class="absolute left-0 mt-2 w-96 origin-top-left z-50"
         x-cloak>
        <div class="bg-white border dark:border-neutral-700 rounded-xl shadow-xl border-neutral-200/70 dark:bg-neutral-900 flex flex-col h-[500px]">
            <div class="flex items-center justify-between px-4 py-3 border-b dark:border-neutral-700">
                <h3 class="font-semibold text-gray-800 dark:text-gray-100 text-sm">
                    <i class="fas fa-robot text-purple-600"></i>
                    المساعد الذكي
                </h3>
                <button wire:click="clearConversation" class="text-xs text-gray-400 hover:text-red-500" title="محادثة جديدة">
                    <i class="fas fa-broom"></i>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-4 py-3 space-y-3 text-sm">
                @forelse ($displayMessages as $message)
                    <div class="flex {{ $message['role'] === 'user' ? 'justify-start' : 'justify-end' }}">
                        <div class="max-w-[85%] rounded-lg px-3 py-2 whitespace-pre-line {{ $message['role'] === 'user' ? 'bg-gray-100 dark:bg-neutral-800 text-gray-800 dark:text-gray-100' : 'bg-purple-50 dark:bg-purple-900/30 text-purple-900 dark:text-purple-100' }}">
                            {{ $message['text'] }}
                        </div>
                    </div>
                @empty
                    <p class="text-gray-400 text-center text-xs mt-6">
                        اسألني عن عملائك أو اتفاقياتك أو التارجت الخاص بك...
                    </p>
                @endforelse

                @if ($loading)
                    <div class="flex justify-end">
                        <div class="bg-purple-50 dark:bg-purple-900/30 rounded-lg px-3 py-2 text-purple-900 dark:text-purple-100">
                            <i class="fas fa-spinner fa-spin"></i>
                        </div>
                    </div>
                @endif
            </div>

            <form wire:submit.prevent="send" class="flex items-center gap-2 px-3 py-3 border-t dark:border-neutral-700">
                <input type="text" wire:model="input" placeholder="اكتب سؤالك هنا..."
                       class="flex-1 rounded-lg border-gray-300 dark:bg-neutral-800 dark:border-neutral-700 dark:text-gray-100 text-sm focus:ring-purple-500 focus:border-purple-500"
                       @if($loading) disabled @endif>
                <button type="submit"
                        class="p-2 rounded-lg bg-purple-600 text-white hover:bg-purple-700 disabled:opacity-50"
                        @if($loading) disabled @endif>
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>
</div>
