@vite(['resources/css/app.css', 'resources/js/app.js'])
@livewireScripts
@livewireStyles
<script>
    document.title = "محادثات العملاء";
</script>

<meta name="user-id" content="{{ Auth::id() }}">
@php
    $salesRepId = null;
    if (Auth::user()->role == 'salesRep' && Auth::user()->salesRep) {
    $salesRepId = Auth::user()->salesRep->id;
    }
@endphp

<div class="min-h-screen flex flex-col">
    <header dir="rtl" class="sticky top-0 z-40">
        <!-- Your existing header from index.blade.php -->
        <div class="header-container flex items-center justify-between h-24 px-6 py-4 bg-white dark:bg-neutral-900 shadow-sm border-b border-gray-200 dark:border-neutral-700 w-full">
            <!-- Logo -->
            <div class="header-brand flex-shrink-0">
                <a href="{{ route('dashboard') }}" class="logo block transition-transform hover:scale-105">
                    <div class="flex items-center space-x-2 space-x-reverse">
                        <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="logo-img h-16 w-auto">
                    </div>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="nav flex-row items-center gap-1 mx-4 hidden lg:flex">
                <!-- Your existing navigation items -->
                @if(Auth::user()->hasRole('admin'))
                    <a href="{{ route('sales-reps.index') }}" class="nav-link px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors duration-200 {{ request()->routeIs('sales-reps.index') ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-neutral-800' }}">
                        <!-- SVG icon -->
                        <span class="nav-text">سفراء العلامة التجارية</span>
                    </a>
                @elseif(Auth::user()->role == 'salesRep')
                    <a href="{{ route('sales-rep.targets.index',$salesRepId) }}" class="nav-link px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2 transition-colors duration-200 {{ request()->routeIs('sales-rep.targets.index',Auth::id()) ? 'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-neutral-800' }}">
                        <!-- SVG icon -->
                        <span class="nav-text">التارجت</span>
                    </a>
                @endif

                <!-- Other nav items... -->
            </nav>

            <!-- Right Actions -->
            <div class="header-actions flex items-center gap-3">
                <!-- Chat Dropdown -->
                <div x-data="{ chatDropdownOpen: false }" class="relative">
                    <button @click="chatDropdownOpen = !chatDropdownOpen" class="p-2 text-gray-500 hover:text-blue-600 dark:hover:text-blue-400 rounded-full hover:bg-gray-100 dark:hover:bg-neutral-800 transition-colors duration-200 relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 3h8a5 5 0 0 1 5 5v6a5 5 0 0 1-5 5h-4l-4 4v-4H8a5 5 0 0 1-5-5V8a5 5 0 0 1 5-5z" />
                        </svg>
                    </button>
                    <!-- Dropdown content -->
                </div>

                <!-- Notifications and Profile dropdowns... -->
            </div>
        </div>
    </header>

    <body>
    <script src="https://cdn.jsdelivr.net/npm/@joeattardi/emoji-button@4.6.4/dist/index.min.js"></script>

    <div class="pt-24"></div>
    <!-- Main Chat Layout -->
    <div class="fixed pt-20 h-full flex bg-white border lg:shadow-sm overflow-hidden inset-0 lg:inset-x-2 lg:inset-y-2 m-auto lg:h-[100%] rounded-t-lg">
        <!-- Chat List - Independent Component -->
        <div class="relative w-full md:w-[320px] xl:w-[400px] overflow-y-auto shrink-0 h-full border">
            <livewire:chat-list
                :selectedConversationId="$initialConversationId"
                :client="$client"
                key="chat-list-{{ $initialConversationId }}-{{ now()->timestamp }}"
            />
        </div>

        <!-- Chat Box - Independent Component -->
        <div class="rtl w-full border-l h-full relative overflow-y-auto" style="contain:content">
            @if($initialConversationId)
                <livewire:chat-box
                    :selectedConversationId="$initialConversationId"
                    :client="$client"
                    key="chat-box-{{ $initialConversationId }}-{{ now()->timestamp }}"
                />
            @else
                <div class="m-auto text-center justify-center flex flex-col gap-3">
                    <h4 class="font-medium text-lg">
                        اختر محادثة لبدء الدردشة
                    </h4>
                </div>
            @endif
        </div>
    </div>
    </body>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Global function to handle conversation selection
        window.selectConversation = function(conversationId) {
            Livewire.dispatch('selectConversation', { conversationId: conversationId });
        };

        // Your existing chat JavaScript functionality
        function normalizeText(text) {
            if (!text) return '';
            return text.toString()
                .toLowerCase()
                .normalize('NFD')
                .replace(/[\u064B-\u065F]/g, '')
                .replace(/[أإآءئ]/g, 'ا')
                .replace(/ة/g, 'ه')
                .replace(/[^\w\u0600-\u06FF]/g, '')
                .trim();
        }

        // Rest of your existing JavaScript...
    });
</script>
