<div>
    <div x-data="{
        type: 'all',
        conversation: $wire.entangle('conversation'),
        init() {
            setTimeout(() => {
                if (this.conversation) {
                    const conversationElement = document.getElementById('conversation-'+this.conversation);
                    if(conversationElement) {
                        conversationElement.scrollIntoView({'behavior':'smooth'});
                    }
                }
            }, 100);

            Echo.private('users.{{Auth()->User()->id}}')
            .notification((notification)=>{
                if(notification['type']== 'App\\Notifications\\MessageRead'||notification['type']== 'App\\Notifications\\MessageSent')
                {
                    $wire.refresh();
                }
            });
        }
    }" class="flex flex-col transition-all h-full overflow-hidden">

        <header class="px-3 z-10 bg-white sticky top-0 w-full py-10">
            @include('partials.chat-list-header')
        </header>

        <main class="overflow-y-auto overflow-hidden grow h-full relative">
            {{-- chatlist --}}
            <ul id="conversationsList" class="p-2 grid w-full space-y-2">
                @if ($conversations && $conversations->count() > 0)
                    @foreach ($conversations as $key => $conversation)
                        <li id="conversation-{{$conversation->id}}" wire:key="{{$conversation->id}}"
                            data-name="{{ Str::lower($conversation->getReceiver()->name) }}"
                            data-company="{{ Str::lower($conversation->client->company_name) }}"
                            class="py-3 hover:bg-gray-50 rounded-2xl dark:hover:bg-gray-700/70 transition-colors duration-150 flex gap-4 relative w-full cursor-pointer px-2 {{$conversation->id==$selectedConversation?->id ? 'bg-gray-100/70':''}}">
                            <!-- User Avatar -->
                            <div class="shrink-0 inline-flex items-center justify-center relative transition overflow-visible text-gray-300 dark:text-[var(--wc-dark-secondary)] text-base h-12 w-12 mx-auto border rounded-full p-2 bg-white dark:bg-[var(--wc-dark-secondary)] dark:border-[var(--wc-dark-secondary)] flex items-center justify-center">
                                @if(!empty($conversation?->client?->company_logo))
                                    <img
                                        src="{{ $conversation->client->company_logo}}"
                                        alt="شعار الشركة"
                                        class="max-h-full max-w-full object-contain"
                                    />
                                @else
                                    <svg class="w-full h-full rounded-full" fill="currentColor" viewBox="0 0 24 24">
                                        <path
                                            d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z">
                                        </path>
                                    </svg>
                                @endif
                            </div>

                            <aside class="grid grid-cols-12 w-full">
                                <a href="{{route('client.chat',[
                                    'client' => $conversation->client->id,
                                    'conversation' => $conversation->id,
                                    ])}}"
                                   class="col-span-11 border-b pb-2 border-gray-200 relative overflow-hidden truncate leading-5 w-full flex-nowrap p-1">

                                    {{-- name and date --}}
                                    <div class="flex justify-between w-full items-center">
                                        <div class="flex flex-col">
                                            <h6 class="truncate font-medium tracking-wider text-gray-900">
                                                {{ $conversation->getReceiver()->name }}
                                            </h6>
                                            <span class="text-xs text-gray-500 truncate">
                                                {{ $conversation->client->company_name }}
                                            </span>
                                        </div>
                                        <small
                                            class="text-gray-700">{{ $conversation->latest_message_time?->shortAbsoluteDiffForHumans() }}

                                        </small>
                                    </div>

                                    {{-- Message body --}}
                                    <div class="flex gap-x-2 items-center">
                                        @if ($conversation->messages?->last()?->sender_id==auth()->id())
                                            @if ($conversation->isLastMessageReadByUser())
                                                {{-- double tick --}}
                                                <span class="text-blue-500">
                                                    <svg viewBox="0 0 18 18" height="18" width="18" preserveAspectRatio="xMidYMid meet"
                                                         class="" version="1.1" x="0px" y="0px" enable-background="new 0 0 18 18">
                                                        <path fill="currentColor"
                                                              d="M17.394,5.035l-0.57-0.444c-0.188-0.147-0.462-0.113-0.609,0.076l-6.39,8.198 c-0.147,0.188-0.406,0.206-0.577,0.039l-0.427-0.388c-0.171-0.167-0.431-0.15-0.578,0.038L7.792,13.13 c-0.147,0.188-0.128,0.478,0.043,0.645l1.575,1.51c0.171,0.167,0.43,0.149,0.577-0.039l7.483-9.602 C17.616,5.456,17.582,5.182,17.394,5.035z M12.502,5.035l-0.57-0.444c-0.188-0.147-0.462-0.113-0.609,0.076l-6.39,8.198 c-0.147,0.188-0.406,0.206-0.577,0.039l-2.614-2.556c-0.171-0.167-0.447-0.164-0.614,0.007l-0.505,0.516 c-0.167,0.171-0.164,0.447,0.007,0.614l3.887,3.8c0.171,0.167,0.43,0.149,0.577-0.039l7.483-9.602 C12.724,5.456,12.69,5.182,12.502,5.035z">
                                                        </path>
                                                    </svg>
                                                </span>
                                            @else
                                                {{-- single tick --}}
                                                <span class="text-gray-500">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24"
                                                         fill="currentColor">
                                                        <path d="M1 12.5l5.5 5.5L20.5 4.5l2 2L6.5 22.5 0 16z" />
                                                    </svg>
                                                </span>
                                            @endif
                                        @endif

                                        @php
                                            $lastMessage =  $conversation->latest_message_text  ?? '';
                                        @endphp

                                        <p class="grow truncate text-sm font-[100]">
                                            @if ($lastMessage === 'like')
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                                                     class="w-6 h-6 text-blue-500 group-hover:text-blue-600 dark:text-blue-400 dark:group-hover:text-blue-300 transition-colors">
                                                    <path
                                                        d="M7.493 18.5c-.425 0-.82-.236-.975-.632A7.48 7.48 0 0 1 6 15.125c0-1.75.599-3.358 1.602-4.634.151-.192.373-.309.6-.397.473-.183.89-.514 1.212-.924a9.042 9.042 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75A.75.75 0 0 1 15 2a2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 0 1-2.649 7.521c-.388.482-.987.729-1.605.729H14.23c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 0 0-1.423-.23h-.777ZM2.331 10.727a11.969 11.969 0 0 0-.831 4.398 12 12 0 0 0 .52 3.507C2.28 19.482 3.105 20 3.994 20H4.9c.445 0 .72-.498.523-.898a8.963 8.963 0 0 1-.924-3.977c0-1.708.476-3.305 1.302-4.666.245-.403-.028-.959-.5-.959H4.25c-.832 0-1.612.453-1.918 1.227Z" />
                                                </svg>
                                            @else
                                                {{ $lastMessage }}
                                            @endif
                                        </p>

                                        {{-- unread count --}}
                                        @if ($conversation->unread_messages_count >0)
                                            <span class="font-bold p-px px-2 text-xs shrink-0 rounded-full bg-[#a855f7] text-white">
                                                {{$conversation->unread_messages_count }}
                                            </span>
                                        @endif
                                    </div>
                                </a>

                                {{-- Dropdown --}}
                                <div class="col-span-1 flex flex-col text-center my-auto">
                                    <x-dropdown align="right" width="48">
                                        <x-slot name="trigger">
                                            <button>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                                     class="bi bi-three-dots-vertical w-7 h-7 text-gray-700" viewBox="0 0 16 16">
                                                    <path
                                                        d="M9.5 13a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm0-5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z" />
                                                </svg>
                                            </button>
                                        </x-slot>

                                        <x-slot name="content">
                                            <div class="w-full p-1">
                                                <button
                                                    href="{{ route('sales-reps.clients.show',[$conversation->client->salesRep->id, $conversation->client_id]) }}"
                                                    class="items-center gap-3 flex w-full px-4 py-2 text-left text-sm leading-5 text-gray-500 hover:bg-gray-100 transition-all duration-150 ease-in-out focus:outline-none focus:bg-gray-100">
                                                    <span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                             fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                                                            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z" />
                                                            <path fill-rule="evenodd"
                                                                  d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z" />
                                                        </svg>
                                                    </span>
                                                    View Profile
                                                </button>
                                            </div>
                                        </x-slot>
                                    </x-dropdown>
                                </div>
                            </aside>
                        </li>
                    @endforeach
                @else
                    <li class="text-center py-8 text-gray-500">
                        لا توجد محادثات
                    </li>
                @endif
            </ul>

            <div style="min-height: 20vh;"></div>
        </main>

        @include('partials.client-selection-modal')
    </div>

    @vite('resources/js/client-chat.js')
</div>
