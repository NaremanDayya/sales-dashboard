{{-- Import helper function to use in chatbox --}}
@use('Namu\WireChat\Helpers\Helper')
@use('Namu\WireChat\Facades\WireChat')

@php
    $primaryColor = WireChat::getColor();
@endphp



@assets
<style>

    emoji-picker {
        width: 100% !important;
        height: 100%;
    }

    /* Emoji picker configuration */
    emoji-picker {
        --background: none !important;
        --border-radius: 12px;
        --input-border-color: rgb(229 229 229);
        --input-padding: 0.45rem;
        --outline-color: none;
        --outline-size: 1px;
        --num-columns: 8;
        /* Mobile-first default */
        --emoji-padding: 0.7rem;
        --emoji-size: 1.5rem;
        /* Smaller size for mobile */
        --border-color: none;
        --indicator-color: #9ca3af;
    }


    @media screen and (min-width: 600px) {
        emoji-picker {
            --num-columns: 10;
            /* Increase columns for larger screens */
            --emoji-size: 1.8rem;
            /* Larger size for desktop */
        }
    }

    @media screen and (min-width: 900px) {
        emoji-picker {
            --num-columns: 16;
            /* Increase columns for larger screens */
            --emoji-size: 1.9rem;
            /* Larger size for desktop */
        }
    }
    /* Dark mode using prefers-color-scheme */
    @media (prefers-color-scheme: dark) {
        emoji-picker {
            --background: none !important;
            --input-border-color: var(--wc-dark-border);
            --outline-color: none;
            --outline-size: 1px;
            --border-color: none;
            --input-font-color: white;
            --indicator-color: var(--wc-dark-accent);
            --button-hover-background: var(--wc-dark-accent);
        }
    }


    /* Ensure dark mode takes precedence */
    .dark emoji-picker {
        --background: none !important;
        --input-border-color: var(--wc-dark-border);
        --outline-color: none;
        --outline-size: 1px;
        --border-color: none;
        --input-font-color: white;
        --indicator-color: var(--wc-dark-accent);
        --button-hover-background: var(--wc-dark-accent);
    }
</style>

@endassets

<div x-data="{
    initializing: true,
    conversationId:@js($conversation->id),
    conversationElement: document.getElementById('conversation'),
    imagePreview: null,
    imageCaption: '',
    showImagePreview: false,

    loadEmojiPicker() {
        if (!document.head.querySelector('script[src=\'https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js\']')) {
            let script = document.createElement('script');
            script.type = 'module';
            script.async = true;
            script.src = 'https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js';
            document.head.appendChild(script);
        }
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

    get isWidget() {
        return $wire.widget == true;
    }
}"

     x-init="setTimeout(() => {
    requestAnimationFrame(() => {
        initializing = false;
        $wire.dispatch('focus-input-field');
        loadEmojiPicker();

        // Handle paste event for screenshots
        document.addEventListener('paste', (e) => {
            const items = e.clipboardData?.items;
            if (!items) return;

            for (let i = 0; i < items.length; i++) {
                if (items[i].type.indexOf('image') !== -1) {
                    const blob = items[i].getAsFile();
                    handleImagePaste(blob);
                    e.preventDefault();
                    break;
                }
            }
        });

        // Handle keyboard events
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && showImagePreview) {
                closeImagePreview();
            }
        });

        {{-- if (isWidget) { --}}
            //NotifyListeners about chat opened
            $wire.dispatch('chat-opened',{conversation:conversationId});
        {{-- } --}}
    });
}, 120);"
     class="w-full transition bg-[var(--wc-light-primary)] dark:bg-[var(--wc-dark-primary)] overflow-hidden h-full relative" style="contain:content">

    <div class=" flex flex-col  grow h-full   relative ">
        {{-- ---------- --}}
        {{-- --Header-- --}}
        {{-- ---------- --}}
        @include('wirechat::livewire.chat.partials.header', [ 'conversation' => $conversation, 'receiver' => $receiver])
        {{-- ---------- --}}
        {{-- -Body----- --}}
        {{-- ---------- --}}
        @include('wirechat::livewire.chat.partials.body', [ 'conversation' => $conversation, 'authParticipant' => $authParticipant, 'loadedMessages' => $loadedMessages, 'isPrivate' => $conversation->isPrivate(), 'isGroup' => $conversation->isGroup(), 'receiver' => $receiver])
        {{-- ---------- --}}
        {{-- -Footer--- --}}
        {{-- ---------- --}}
        @include('wirechat::livewire.chat.partials.footer', [ 'conversation' => $conversation, 'authParticipant' => $authParticipant, 'media' => $media, 'files' => $files, 'replyMessage' => $replyMessage])

    </div>

    <livewire:wirechat.chat.drawer />

    <!-- WhatsApp-Style Image Preview Modal with SOLID BOLD Background -->
    <template x-teleport="body">
        <div x-show="showImagePreview" x-cloak
             class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-[#0b141a]"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">

            <!-- Main Preview Container -->
            <div class="relative w-full max-w-5xl max-h-[90vh] bg-[#111b21] rounded-2xl overflow-hidden shadow-2xl shadow-black/50 border border-[#2a3942]"
                 @click.away="closeImagePreview()"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="scale-95 opacity-0"
                 x-transition:enter-end="scale-100 opacity-100">

                <!-- Close Button - Top Right -->
                <button @click="closeImagePreview()"
                        class="absolute top-4 right-4 z-20 p-3 bg-black/70 hover:bg-black/90 text-white rounded-full transition-all duration-200 hover:scale-110 active:scale-95 shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <!-- Image Preview Area -->
                <div class="relative h-[65vh] flex items-center justify-center overflow-hidden bg-gradient-to-br from-[#0f1419] to-[#1a2027]">
                    <!-- Image -->
                    <img :src="imagePreview"
                         alt="Preview"
                         class="max-w-full max-h-full object-contain p-4">

                    <!-- Loading shimmer effect -->
                    <div x-show="!imagePreview" class="absolute inset-0 bg-gradient-to-r from-[#202c33] via-[#2a3942] to-[#202c33] animate-pulse"></div>
                </div>

                <!-- Caption Input & Send Button -->
                <div class="bg-[#202c33] border-t border-[#2a3942]">
                    <div class="p-6">
                        <div class="flex items-center gap-3">
                            <!-- Caption Input -->
                            <div class="flex-1 relative">
                                <input x-model="imageCaption"
                                       x-ref="captionInput"
                                       type="text"
                                       placeholder="أضف تعليقاً (اختياري)..."
                                       class="w-full bg-[#2a3942] text-black placeholder:text-gray-400/70 border border-[#374248] focus:border-[#00a884] focus:ring-2 focus:ring-[#00a884]/30 rounded-xl px-4 py-3.5 text-base transition-all duration-200 caret-[#00a884] outline-none"
                                       @keydown.enter="sendImage()"
                                       @focus="$refs.captionInput.select()">

                                <!-- Character counter -->
                                <div x-show="imageCaption.length > 0"
                                     class="absolute bottom-2 right-3 text-xs"
                                     :class="imageCaption.length > 100 ? 'text-red-400' : 'text-gray-400'">
                                    <span x-text="imageCaption.length"></span>/100
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center gap-2">
                                <button @click="closeImagePreview()"
                                        class="p-3.5 text-gray-400 hover:text-white hover:bg-white/10 rounded-xl transition-all duration-200 border border-[#374248] hover:border-gray-500"
                                        title="إلغاء">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>

                                <button @click="sendImage()"
                                        :class="imageCaption.trim() ? 'bg-[#00a884] hover:bg-[#06cf9c] shadow-lg shadow-[#00a884]/20' : 'bg-[#00a884]/70 hover:bg-[#00a884]/90'"
                                        class="p-3.5 text-white rounded-xl transition-all duration-200 transform hover:scale-105 active:scale-95 flex items-center gap-2 px-6 font-medium"
                                        title="إرسال الصورة">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                              d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                                    </svg>
                                    <span>إرسال</span>
                                </button>
                            </div>
                        </div>

                        <!-- Helper Text -->
                        <div class="mt-4 pt-4 border-t border-[#374248]/50">
                            <div class="flex items-center justify-center gap-6 text-gray-400/70 text-sm">
                                <div class="flex items-center gap-2">
                                    <kbd class="px-2.5 py-1 bg-[#2a3942] rounded-lg text-xs font-medium text-gray-300">Esc</kbd>
                                    <span>للإلغاء</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <kbd class="px-2.5 py-1 bg-[#2a3942] rounded-lg text-xs font-medium text-gray-300">Enter</kbd>
                                    <span>للإرسال</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template></div>
<script>
    window.addEventListener('open-edit-modal', event => {
        const editModal = new bootstrap.Modal(document.getElementById('editMessageModal'));
        editModal.show();
    });
</script>
