@php
    $receiver = $selectedConversation->getReceiver();
@endphp
<div x-data="{
    height: 0,
    conversationElement: null,
    editingMessageId: null,
    editMessageText: '',
    deleteMessageId: null,
    deleteConversationId: null,
    imagePreview: null,
    imageCaption: '',
    showImagePreview: false,

    init() {
        this.conversationElement = document.getElementById('conversation');
        this.height = this.conversationElement.scrollHeight;
        this.$nextTick(() => this.conversationElement.scrollTop = this.height);

        // Listen for new messages to scroll to bottom
        window.addEventListener('new-message', () => {
            this.$nextTick(() => {
                this.conversationElement.scrollTop = this.conversationElement.scrollHeight;
            });
        });

        // Handle paste event for screenshots
        document.addEventListener('paste', (e) => {
            const items = e.clipboardData?.items;
            if (!items) return;

            for (let i = 0; i < items.length; i++) {
                if (items[i].type.indexOf('image') !== -1) {
                    const blob = items[i].getAsFile();
                    this.handleImagePaste(blob);
                    e.preventDefault();
                    break;
                }
            }
        });

        // Handle keyboard events
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.showImagePreview) {
                this.closeImagePreview();
            }
        });
    },
    handleImagePaste(blob) {
        const reader = new FileReader();
        reader.onload = (e) => {
            this.imagePreview = e.target.result;
            this.imageCaption = '';
            this.showImagePreview = true;
        };
        reader.readAsDataURL(blob);
    },
    handleFileSelect(event) {
        const file = event.target.files[0];
        if (file && file.type.startsWith('image/')) {
            this.handleImagePaste(file);
        }
        // Reset input
        event.target.value = '';
    },
    closeImagePreview() {
        this.showImagePreview = false;
        this.imagePreview = null;
        this.imageCaption = '';
    },
    sendImage() {
        if (this.imagePreview) {
            $wire.sendImageMessage(this.imagePreview, this.imageCaption);
            this.closeImagePreview();
        }
    },
    openEditModal(messageId, messageText) {
        this.editingMessageId = messageId;
        this.editMessageText = messageText;
    },
    closeEditModal() {
        this.editingMessageId = null;
        this.editMessageText = '';
    },
    openDeleteModal(messageId) {
        this.deleteMessageId = messageId;
    },
    closeDeleteModal() {
        this.deleteMessageId = null;
    }
}" x-init="init()"
     @scroll-bottom.window="
    $nextTick(() => conversationElement.scrollTop = conversationElement.scrollHeight);"
     class="w-full overflow-hidden" data-user-id="{{ auth()->id() }}" id="chat-box"
     data-receiver-id="{{ $receiver->id }}" data-user='@json(auth()->user())'>
    <meta name="receiver-id" content="{{ $receiver->id }}">

    <!-- Edit Message Modal -->
    <template x-teleport="body">
        <div x-show="editingMessageId" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50">
            <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold mb-4">تعديل الرسالة</h3>
                <textarea x-model="editMessageText" class="w-full p-2 border rounded-md" rows="4"></textarea>
                <div class="flex justify-end mt-4 space-x-2">
                    <button @click="closeEditModal()" class="px-4 py-2 text-gray-600 bg-gray-200 rounded-md">إلغاء</button>
                    <button @click="$wire.editMessage(editingMessageId, editMessageText).then(() => closeEditModal())"
                            class="px-4 py-2 text-white bg-purple-600 rounded-md">تعديل الرسالة </button>
                </div>
            </div>
        </div>
    </template>

    <!-- Delete Confirmation Modal -->
    <template x-teleport="body">
        <div x-show="deleteMessageId" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black bg-opacity-50">
            <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-lg">
                <h3 class="text-lg font-semibold mb-4">حذف الرسالة</h3>
                <p class="mb-4">هل أنت متأكد من حذف الرسالة؟  لن يمكنك التراجع عن ذلك</p>
                <div class="flex justify-end mt-4 space-x-2">
                    <button @click="closeDeleteModal()" class="px-4 py-2 text-gray-600 bg-gray-200 rounded-md">إلغاء</button>
                    <button @click="$wire.deleteMessage(deleteMessageId).then(() => closeDeleteModal())"
                            class="px-4 py-2 text-white bg-red-600 rounded-md">حذف</button>
                </div>
            </div>
        </div>
    </template>

    <!-- WhatsApp-Style Image Preview Modal -->
    <!-- WhatsApp-Style Image Preview Modal -->
    <!-- Replace the ENTIRE WhatsApp-Style Image Preview Modal template (lines 62-125) with this: -->

    <!-- WhatsApp-Style Image Preview Modal -->
    <template x-teleport="body">
        <div x-show="showImagePreview" x-cloak
             class="fixed inset-0 z-[9999] flex items-center justify-center p-4">

            <!-- Dark overlay background -->
            <div class="preview-overlay"></div>

            <!-- Main container -->
            <div class="preview-container"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95">

                <!-- Header with X button -->
                <div class="preview-header">
                    <button class="preview-close-btn" @click="closeImagePreview()" title="Close (Esc)">
                        <i class="bi bi-x-lg"></i>
                    </button>
                    <h6 class="mb-0">معاينة الصورة</h6>
                </div>

                <!-- Image display area -->
                <div class="preview-body">
                    <img :src="imagePreview"
                         alt="Preview"
                         id="preview-image"
                         class="preview-img">
                </div>

                <!-- Footer with caption input and send button -->
                <div class="preview-footer">
                    <div class="caption-input-group">
                        <input x-model="imageCaption"
                               type="text"
                               class="form-control caption-input"
                               id="image-caption"
                               placeholder="أضف تعليقاً..."
                               @keydown.enter="sendImage()"
                               @focus="$refs.captionInput?.select()"
                               x-ref="captionInput">
                    </div>
                    <button class="btn btn-success send-image-btn"
                            @click="sendImage()"
                            :disabled="!imagePreview"
                            title="إرسال الصورة">
                        <i class="bi bi-send-fill me-2"></i>إرسال
                    </button>
                </div>
            </div>
        </div>
    </template>    <!-- Main container -->
    <div class="flex flex-col w-full h-full py-1">

        <!-- Header Section -->
        <header class="w-full sticky inset-x-0 top-0 pt-2 z-10 bg-white border border-gray-200 rounded-lg mb-2">
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
                    @php
                        $client = $selectedConversation->client;
                    @endphp
                    <div
                        class="shrink-0 inline-flex items-center justify-center relative transition overflow-visible rounded-full border border-[var(--wc-light-secondary)] text-gray-300 bg-[#f5f5fa] dark:bg-[var(--wc-dark-secondary)] dark:border-[var(--wc-dark-secondary)] text-base h-8 w-8 lg:w-10 lg:h-10">
                        @if(!empty($selectedConversation?->client?->company_logo))
                            <a href="{{ route('sales-reps.clients.show', ['sales_rep' => $client->sales_rep_id, 'client' => $client->id]) }}">
                                <img

<!-- User Avatar -->
<div class="shrink-0 inline-flex items-center justify-center relative transition overflow-visible text-gray-300 dark:text-[var(--wc-dark-secondary)] text-base h-12 w-12 mx-auto border rounded-full p-2 bg-white dark:bg-[var(--wc-dark-secondary)] dark:border-[var(--wc-dark-secondary)] flex items-center justify-center">

    @if(!empty($selectedConversation?->client?->company_logo))
         <img

                                    src="{{ $selectedConversation?->client->company_logo}}"
                                    alt="شعار الشركة"
                                    class="max-h-full max-w-full object-contain bg-white rounded-full"
                                />

    @else
        <svg class="w-full h-full rounded-full" fill="currentColor" viewBox="0 0 24 24">
            <path
                d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z">
            </path>
        </svg>
    @endif
</div>

                            </a>
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
                            {{ $receiver->name ?? $receiver->email }}
                        </h6>
                        <a href="{{ route('sales-reps.clients.show', ['sales_rep' => $client->sales_rep_id, 'client' => $client->id]) }}"
                           class="text-xs text-gray-500 truncate hover:underline">
                            {{ $client->company_name }}
                        </a>
                    </div>
                </div>

                <!-- Admin Dropdown for Conversation Actions -->
                @if(auth()->user()->isAdmin())
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="p-2 text-gray-500 hover:text-gray-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                            </svg>
                        </button>

                        <div x-show="open" x-cloak @click.away="open = false"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                            <button wire:click="deleteConversation({{ $selectedConversation->id }})"
                                    @click="open = false; $wire.deleteConversation({{ $selectedConversation->id }})"
                                    class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                حذف المحادثة
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </header>

        <!-- Messages Section -->
        <main x-init="window.addEventListener('new-message', (event) => {
            $wire.call('loadMessages');
            $nextTick(() => conversationElement.scrollTop = conversationElement.scrollHeight);
        });"
              @scroll="
        scropTop = $el.scrollTop;
        if(scropTop <= 0) { $dispatch('loadMore'); }
    "
              @update-chat-height.window="
        newHeight = $el.scrollHeight;
        oldHeight = height;
        $el.scrollTop = newHeight - oldHeight;
        height = newHeight;
    "
              id="conversation"
              class="flex flex-col gap-3 pb-10 p-5 overflow-y-auto flex-grow overscroll-contain overflow-x-hidden w-full my-auto" dir="rtl">

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

                    <div @class([
                    'max-w-[85%] md:max-w-[78%] w-auto gap-2 relative mt-2 flex group',
                    'mr-auto flex-row-reverse' => $message->sender_id === Auth::id(),
                ]) x-data="{ showMenu: false }" @click.away="showMenu = false">
                        <!-- Avatar (only shown for received messages and when sender changes) -->
                        <div @class([
                        'shrink-0',
                        'invisible' => $previousMessage?->sender_id == $message->sender_id,
                        'hidden' => $message->sender_id === Auth::id(),
                    ])>
                            <x-avatar :src="$selectedConversation->getReceiver()->avatar_url" :name="$selectedConversation->getReceiver()->name" />
                        </div>

                        <!-- Message Content -->
                        <div @class([
                        'flex flex-col text-[15px] rounded-xl p-2.5 text-black',
                        'rounded-bl-none border border-gray-200/40' =>
                            !($message->sender_id === Auth::id()) && $message->message !== 'like',
                        'rounded-br-none bg-[#a855f7] text-white' =>
                            $message->sender_id === Auth::id() && $message->message !== 'like',
                    ])>
                            @if($message->message === 'like')
                                <div class="flex justify-center w-full py-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24"
                                         class="w-12 h-12 text-blue-500 transform hover:scale-110 transition-transform">
                                        <path fill-rule="evenodd"
                                              d="M7.493 18.5c-.425 0-.82-.236-.975-.632A7.48 7.48 0 0 1 6 15.125c0-1.75.599-3.358 1.602-4.634.151-.192.373-.309.6-.397.473-.183.89-.514 1.212-.924a9.042 9.042 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V2.75A.75.75 0 0 1 15 2a2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 0 1-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 0 0-1.423-.230H7.493Z"
                                              clip-rule="evenodd" />
                                    </svg>
                                </div>
                            @elseif(str_contains($message->message, '||IMAGE||'))
                                @php
                                    $parts = explode('||IMAGE||', $message->message);
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
                                        <p class="whitespace-normal text-sm md:text-base tracking-wide lg:tracking-normal">
                                            {!! nl2br(e($caption)) !!}
                                        </p>
                                    @endif
                                </div>
                            @else
                                <p class="whitespace-normal text-sm md:text-base tracking-wide lg:tracking-normal">
                                    {!! nl2br(e($message->message)) !!}
                                </p>
                            @endif

                            <div class="mr-auto flex gap-2 items-center mt-1">
                                <p @class([
                                'text-xs flex items-center',
                                'text-gray-500' =>
                                    !($message->sender_id === Auth::id()) && $message->message !== 'like',
                                'text-white' =>
                                    $message->sender_id === Auth::id() && $message->message !== 'like',
                                'text-gray-400' => $message->message === 'like',
                            ])>
                                    {{ $message->created_at->format('Y-m-d') }} |
                                    {{ $message->created_at->format('h:i A') }}

                                    @if($message->isEdited())
                                        <span class="ml-2 text-xs italic">|تم التعديل</span>
                                    @endif
                                </p>

                                @if ($message->sender_id === Auth::id() && $message->message !== 'like')
                                    <div class="text-xs">
                                        @if ($message->isRead())
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-grey-500" viewBox="0 0 16 16" fill="currentColor">
                                                <path d="M12.354 4.354a.5.5 0 0 0-.708-.708L5 10.293 1.854 7.146a.5.5 0 1 0-.708.708l3.5 3.5a.5.5 0 0 0 .708 0l7-7z"/>
                                                <path d="M8.146 11.354l-.896-.897.707-.707.543.543 6.646-6.647a.5.5 0 0 1 .708.708l-7 7a.5.5 0 0 1-.708 0z"/>
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-grey-500" viewBox="0 0 16 16" fill="currentColor">
                                                <path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
                                            </svg>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>

                        @php
                            $canEdit = $message->canBeEdited() || auth()->user()->isAdmin();
                            $canDelete = auth()->user()->isAdmin() && $message->sender_id === Auth::id();
                        @endphp

                        @if($canEdit || $canDelete)
                            <div class="relative" x-data="{ showMenu: false }">
                                <button @click="showMenu = !showMenu"
                                        class="p-1 text-gray-400 hover:text-gray-600 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                    </svg>
                                </button>

                                <div x-show="showMenu" x-cloak
                                     class="absolute right-0 z-10 w-40 mt-2 bg-white rounded-md shadow-lg">
                                    <div class="py-1">
                                        @if($canEdit)
                                            <button @click="openEditModal('{{ $message->id }}', '{{ $message->message }}')"
                                                    class="block w-full px-4 py-2 text-sm text-left text-gray-700 hover:bg-gray-100">
                                                تعديل
                                            </button>
                                        @endif

                                        @if($canDelete)
                                            <button @click="openDeleteModal('{{ $message->id }}')"
                                                    class="block w-full px-4 py-2 text-sm text-left text-red-600 hover:bg-gray-100">
                                                حذف
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                @endforeach
            @endif
        </main>

        <!-- Footer Section - FIXED -->
        <footer class="shrink-0 z-10 bg-white dark:bg-[var(--wc-dark-secondary)] inset-x-0">
            <div class="p-2 border-t dark:border-[var(--wc-dark-primary)]">
                <!-- Use Livewire's native form handling instead of Alpine.js -->
                <form wire:submit.prevent="sendMessage" class="flex items-center w-full gap-2 sm:gap-5">
                    @csrf
                    <input type="hidden" autocomplete="false">

                    <!-- Attachment Button -->
                    <div class="flex items-center">
                        <input type="file"
                               id="imageInput"
                               accept="image/*"
                               class="hidden"
                               @change="handleFileSelect($event)">
                        <button type="button"
                                @click="$el.previousElementSibling.click()"
                                class="p-2 text-gray-500 hover:text-purple-600 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                            </svg>
                        </button>
                    </div>

                    <!-- Message Input -->
                    <div class="flex gap-2 sm:px-2 w-full">
                        <input wire:model="message" wire:loading.attr="disabled" autofocus type="text"
                               placeholder="Type a message" id="message"
                               wire:key="message-input-{{ time() }}"
                               maxlength="1700" rows="1"
                               @keydown.enter.prevent="$event.shiftKey || $wire.call('sendMessage').then(() => {
           $nextTick(() => conversationElement.scrollTop = conversationElement.scrollHeight);
       })"
                               class="w-full disabled:cursor-progress resize-none h-auto max-h-20 sm:max-h-72 flex grow border-0 outline-0 focus:border-0 focus:ring-0 hover:ring-0 rounded-lg dark:text-white bg-none dark:bg-inherit focus:outline-hidden">       </div>


                    <!-- Send Button - FIXED (no longer hidden) -->
                    <div class="w-[5%] justify-start min-w-max items-center gap-2">
                        <button wire:loading.attr="disabled" type="submit"
                                class="cursor-pointer hover:text-[var(--wc-brand-primary)] transition-color mr-auto disabled:cursor-progress font-bold">
                            <svg class="w-7 h-7 dark:text-gray-200" xmlns="http://www.w3.org/2000/svg" width="36"
                                 height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M9.912 12H4L2.023 4.135A.662.662 0 0 1 2 3.995c-.022-.721.772-1.221 1.46-.891L22 12 3.46 20.896c-.68.327-1.464-.159-1.46-.867a.66.66 0 0 1 .033-.186L3.5 15">
                                </path>
                            </svg>
                        </button>
                    </div>

                    <!-- Like Button - FIXED (removed 'hidden' class) -->
                    <div class="w-10 flex max-w-fit items-center">
                        <button type="button" wire:click="sendLike"
                                class="cursor-pointer hover:scale-105 transition-transform disabled:cursor-progress rounded-full p-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                 stroke-width="1.5" stroke="currentColor"
                                 class="w-6 h-6 text-blue-500 hover:text-blue-600 dark:text-blue-400 dark:hover:text-blue-300">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1
           2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0
           .322-1.672V2.75a.75.75 0 0 1 .75-.75A2.25 2.25 0 0 1
           16.5 4.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725
           1.282h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068
           1.285a11.95 11.95 0 0 1-2.649 7.521c-.388.482-.987.729-1.605.729H13.48
           c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 0
           0-1.423-.23H5.904m10.598-9.75H14.25m-8.346 9.75c.083.205.173.405.27.602.197.4
           -.078.898-.523.898h-.908c-.889 0-1.713-.518-1.972-1.368a12
           12 0 0 1-.521-3.507c0-1.553.295-3.036.831-4.398C3.387
           9.953 4.167 9.5 5 9.5h1.053c.472 0 .745.556.5.96a8.958
           8.958 0 0 0-1.302 4.665c0 1.194.232 2.333.654 3.375Z" />
                            </svg>

                        </button>
                    </div>
                </form>
            </div>
        </footer>
    </div>
</div>
