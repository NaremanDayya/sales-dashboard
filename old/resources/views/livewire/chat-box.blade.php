<style>
    [x-cloak] { display: none !important; }
</style>
<div x-data="{
  height: 0,
  conversationElement: null,
  init() {
    this.conversationElement = document.getElementById('conversation');
    this.height = this.conversationElement.scrollHeight;
    this.$nextTick(() => this.conversationElement.scrollTop = this.height);
  }
}" x-init="init()" @scroll-bottom.window="
    $nextTick(() => conversationElement.scrollTop = conversationElement.scrollHeight);
" class="w-full overflow-hidden" data-user-id="{{ auth()->id() }}" id="chat-box"
    data-receiver-id="{{ $selectedConversation->getReceiver()->id }}" data-user='@json(auth()->user())' dir="rtl">
    <meta name="receiver-id" content="{{ $selectedConversation->receiver_id }}">

    <!-- Main container -->
    <div class="flex flex-col w-full h-full py-1">

        <!-- Header Section -->
        <header class="w-full sticky inset-x-0 top-0 z-10 bg-white border border-gray-200 rounded-lg mb-2">
            <div class="flex w-full items-center gap-2 md:gap-5 justify-between bg-[#f5f5fa] p-3">
                <div class="flex items-center gap-2 md:gap-5 cursor-pointer">
                    <!-- Mobile Back Button -->
                    <a class="shrink-0 lg:hidden" href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M19.5 12h-15m0 0l6.75 6.75M4.5 12l6.75-6.75" />
                        </svg>
                    </a>

<!-- User Avatar -->
<div class="shrink-0 inline-flex items-center justify-center relative transition overflow-visible text-gray-300 dark:text-[var(--wc-dark-secondary)] text-base h-12 w-12 mx-auto border rounded-full p-2 bg-white dark:bg-[var(--wc-dark-secondary)] dark:border-[var(--wc-dark-secondary)] flex items-center justify-center">

    @if(!empty($selectedConversation?->client?->company_logo))
        <img 
            src="{{ asset('storage/' . $selectedConversation->client->company_logo) }}"
            alt="شعار الشركة"
            class="max-h-full max-w-full object-contain bg-white rounded-full"
        >
    @else
        <svg class="w-full h-full rounded-full" fill="currentColor" viewBox="0 0 24 24">
            <path
                d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z">
            </path>
        </svg>
    @endif
</div>


                    <!-- User Info -->
                    <div class="flex flex-col">
                        <h6 class="truncate font-medium tracking-wider text-gray-900">
                            {{ $selectedConversation->getReceiver()->name ?? $selectedConversation->getReceiver()->email
                            }}
                        </h6>
                        <span class="text-xs text-gray-500 truncate">
                            {{ $selectedConversation->client->company_name }}
                        </span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Messages Section -->
        <main 
		x-data="{
        editModalOpen: false,
        editingMessageId: null,
        editingMessageContent: '',
    }"		
x-init="
            window.addEventListener('new-message', (event) => {
                $wire.call('loadMessages');
                $nextTick(() => conversationElement.scrollTop = conversationElement.scrollHeight);
            });
  window.addEventListener('open-edit-modal', event => {
            editModalOpen = true;
            editingMessageId = event.detail.messageId;
            editingMessageContent = event.detail.content;
        });
        " @scroll="
            scropTop = $el.scrollTop;
            if(scropTop <= 0) { $dispatch('loadMore'); }
        " @update-chat-height.window="
            newHeight = $el.scrollHeight;
            oldHeight = height;
            $el.scrollTop = newHeight - oldHeight;
            height = newHeight;
        " id="conversation"
            class="flex flex-col gap-3 p-5 overflow-y-auto flex-grow overscroll-contain overflow-x-hidden w-full my-auto">

            @if ($loadedMessages)
            @php
            $previousMessage = null;
            @endphp

            @foreach ($loadedMessages as $key => $message)
            @if ($key > 0)
            @php $previousMessage = $loadedMessages->get($key-1) @endphp
            @endif
            @if (!$previousMessage || $previousMessage->created_at->format('l') !== $message->created_at->format('l'))
            <div
                class="sticky top-0 uppercase p-2 shadow-xs px-2.5 z-50 rounded-xl border dark:border-[var(--wc-dark-primary)] border-white text-sm flex text-center justify-center bg-[#f5f5fa] dark:bg-[var(--wc-dark-secondary)] dark:text-white w-28 mx-auto">
                {{ $message->created_at->format('l') }}
            </div>
            @endif

            <div @class([ 'max-w-[85%] md:max-w-[78%] w-auto gap-2 relative mt-2 flex' , 'mr-auto flex-row'=>
                $message->sender_id === Auth::id(),
                ])>
                <!-- Avatar (only shown for received messages and when sender changes) -->
                <div @class([ 'shrink-0' , 'invisible'=> $previousMessage?->sender_id == $message->sender_id,
                    'hidden' => $message->sender_id === Auth::id()
                    ])>
<x-avatar 
    :src="$selectedConversation->client->company_logo 
        ? asset('storage/' . $selectedConversation->client->company_logo) 
        : null" 
    :name="$selectedConversation->getReceiver()->name" 
/>
                </div>
<!-- Message Content --> 
<div @class([ 'flex items-start gap-2 text-[15px] rounded-xl p-2.5 text-black',
 'rounded-bl-none border border-gray-200/40' => !($message->sender_id === Auth::id()) && $message->message !== 'like',
 'rounded-br-none bg-[#a855f7] text-white' => $message->sender_id === Auth::id() && $message->message !== 'like', ])> 
@if ($message->message === 'like') 
<div class="flex justify-center w-full py-1">
 <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-12 h-12 text-blue-500 transform hover:scale-110 transition-transform">
 <path d="M7.493 18.5c-.425 0-.82-.236-.975-.632A7.48 ..."/>
 </svg> 
</div> 
@else
    <!-- Dropdown dots (only for my own messages) -->
        @if ($message->sender_id === Auth::id())
            <div x-data="{ open: false }" class="relative self-end">
                <button @click="open = !open"
                        class="p-1 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4"
                         fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zm0 5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zm0 5a1.5 1.5 0 110 3 1.5 1.5 0 010-3z"/>
                    </svg>
                </button>

                <!-- Dropdown menu -->
                <div x-show="open" @click.away="open = false"
                     class="absolute left-0 mt-1 w-28 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
                    @if($message->canBeEdited() || auth()->user()->isAdmin())
<button 
    @click="
        window.dispatchEvent(new CustomEvent('open-edit-modal', {
            detail: { 
                messageId: {{ $message->id }}, 
                content: '{{ addslashes($message->message) }}' 
            }
        }));
        open = false
    " 
    class="text-black w-full text-left px-4 py-2 text-sm hover:bg-gray-100">
    ✏️ تعديل
</button>
                    @endif

                    @if(auth()->user()->isAdmin())
                        <button @click="$wire.emit('deleteMessage', {{ $message->id }})"
                                class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-100">
                            🗑️ حذف
                        </button>
                    @endif
                </div>
            </div>
        @endif

        <!-- Message text -->
        <p class="whitespace-normal text-sm md:text-base tracking-wide lg:tracking-normal flex-1">
            {{ $message->message }}
        </p>
@endif
        <!-- Edited & timestamp -->
        <div class="mt-1 text-xs flex gap-1 items-center self-end">
            @if($message->edited_at)
                <span class="italic text-black">تم التعديل</span>
                <span class="text-gray-300">|</span>
            @endif

            <span @class([
                'text-gray-500' => !($message->sender_id === Auth::id()) && $message->message !== 'like',
                'text-white' => $message->sender_id === Auth::id() && $message->message !== 'like',
                'text-gray-400' => $message->message === 'like',
            ])>
                {{ $message->created_at->format('Y-m-d') }} | {{ $message->created_at->format('h:i A') }}
            </span>
        </div>
 @if ($message->sender_id === Auth::id() && $message->message !== 'like') <br>
 <div class="text-xs"> @if ($message->isRead()) <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" viewBox="0 0 16 16" fill="currentColor">
 <path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0l7-7z" />
 <path d="m8.146 11.354-.896-.897.707-.707.543.543 6.646-6.647a.5.5 0 0 1 .708.708l-7 7a.5.5 0 0 1-.708 0z" />
 <path d="m5.354 7.146.896.897-.707.707-.897-.896a.5.5 0 1 1 .708-.708z" /> </svg>
 @else <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" viewBox="0 0 16 16" fill="currentColor">
 <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z" /> 
</svg>
 @endif </div> @endif </div> </div> @endforeach @endif
    <!-- Edit Modal -->
<div x-show="editModalOpen" x-cloak class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
 <div class="bg-white rounded-lg p-6 w-96 mx-4"> <h3 class="text-lg font-semibold mb-4 text-right">تعديل الرسالة</h3> 
<textarea x-model="editingMessageContent" class="w-full p-3 border rounded-lg resize-none text-right" rows="4" placeholder="عدل رسالتك هنا...">
</textarea> <div class="flex justify-end space-x-3 mt-4"> 
<button @click="editModalOpen = false" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50"> إلغاء </button> &nbsp;&nbsp;
<button 
    @click="
        window.dispatchEvent(new CustomEvent('save-edit-message', {
            detail: {
                messageId: editingMessageId,
                content: editingMessageContent
            }
        }));
        editModalOpen = false;
    "
    class="px-6 py-2 bg-[#a855f7] hover:bg-[#9333ea] text-white rounded-lg transition">
    حفظ
</button>
 </div>
 </div> 
</div>
 </div>
        </main>
 <!-- Footer Section --> 
<footer class="shrink-0 z-10 bg-white dark:bg-[var(--wc-dark-secondary)] inset-x-0 fixed bottom-0">
            <div class="p-2 border-t dark:border-[var(--wc-dark-primary)]">
                <form x-data="{ message: @entangle('message'), openEmojiPicker: false }"
                    @submit.prevent="$wire.sendMessage" method="POST" autocapitalize="off"
                    class="flex items-center w-full gap-2 sm:gap-5">
                    @csrf
                    <input type="hidden" autocomplete="false" style="display:none">

                    <!-- Message Input -->
                    <div class="flex gap-2 sm:px-2 w-full">
                        <input x-model="message" wire:loading.delay.longest.attr="disabled" wire:target="sendMessage"
                            autofocus type="text" placeholder="Type a message" id="message" maxlength="1700" rows="1"
                            @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px';"
                            @keyup.enter.prevent="$event.shiftKey ? null : (message.trim().length > 0 ? $wire.sendMessage() : null)"
                            class="w-full disabled:cursor-progress resize-none h-auto max-h-20 sm:max-h-72 flex grow border-0 outline-0 focus:border-0 focus:ring-0 hover:ring-0 rounded-lg dark:text-white bg-none dark:bg-inherit text-right focus:outline-hidden">
                    </div>

                    <!-- Send Button -->
                    <div class="w-[5%] justify-end min-w-max items-center gap-2">
                        <button x-show="message?.trim()?.length>0" wire:loading.attr="disabled"
                            wire:target="sendMessage" type="submit"
                            class="cursor-pointer hover:text-[var(--wc-brand-primary)] transition-color ml-auto disabled:cursor-progress font-bold"
                            style="display: none;">
                            <svg class="w-7 h-7 dark:text-gray-200" xmlns="http://www.w3.org/2000/svg" width="36"
                                height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M9.912 12H4L2.023 4.135A.662.662 0 0 1 2 3.995c-.022-.721.772-1.221 1.46-.891L22 12 3.46 20.896c-.68.327-1.464-.159-1.46-.867a.66.66 0 0 1 .033-.186L3.5 15">
                                </path>
                            </svg>
                        </button>
                    </div>
                    <div class="w-10 hidden sm:flex max-w-fit items-center">
<button type="button" @click="message = 'like'; $wire.sendMessage();"
                            class="cursor-pointer hover:scale-105 transition-transform disabled:cursor-progress rounded-full p-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor"
                                class="w-6 h-6 text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282m0 0h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 0 1-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 0 0-1.423-.23H5.904m10.598-9.75H14.25M5.904 18.5c.083.205.173.405.27.602.197.4-.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12 12 0 0 1-.521-3.507c0-1.553.295-3.036.831-4.398C3.387 9.953 4.167 9.5 5 9.5h1.053c.472 0 .745.556.5.96a8.958 8.958 0 0 0-1.302 4.665c0 1.194.232 2.333.654 3.375Z" />
                            </svg>
                        </button>
                    </div>
                </form>
            </div>

        </footer>
    </div>
</div>
