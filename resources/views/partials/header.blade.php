<header dir="rtl" class="sticky top-0 z-30 flex items-center gap-3 h-[--app-topbar-height] px-4 sm:px-6 bg-white/80 backdrop-blur border-b border-gray-200">
    <!-- Mobile: open sidebar -->
    <button @click="sidebarOpen = true" class="lg:hidden p-2 -ms-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    <!-- Page title slot -->
    <div class="min-w-0 flex-1">
        @hasSection('page-title')
            <h1 class="text-base font-semibold text-gray-900 truncate">@yield('page-title')</h1>
        @else
            <span class="hidden sm:block"></span>
        @endif
    </div>

    <!-- Header Actions -->
    <div class="flex items-center gap-1.5 sm:gap-2">
        <!-- Client Chat Icon -->
        <div x-data="{ clientChatDropdownOpen: false }" class="relative" dir="rtl">
            @include('partials.chatClient')
        </div>

        <!-- Chat Icon -->
        <div x-data="{ chatDropdownOpen: false }" class="relative">
            <button @click="chatDropdownOpen = !chatDropdownOpen; $wire.markAllAsRead(); fetchUnreadCount()"
                    class="p-2 text-gray-500 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-full hover:bg-gray-100 dark:hover:bg-neutral-800 transition-colors duration-200 relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 3h8a5 5 0 0 1 5 5v6a5 5 0 0 1-5 5h-4l-4 4v-4H8a5 5 0 0 1-5-5V8a5 5 0 0 1 5-5z" />
                </svg>

                @livewire('unread-badge', ['selectedConversationId' => request()->conversation])
            </button>

            <div
                x-show="chatDropdownOpen"
                @click.away="chatDropdownOpen = false"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-1"
                class="absolute end-0 mt-2 w-[22rem] max-w-[92vw] origin-top-end"
                x-cloak
            >
                <div class="text-sm h-[500px] z-50 bg-white border overflow-hidden dark:border-neutral-700 rounded-xl shadow-xl border-gray-200 text-neutral-700 dark:bg-neutral-900">
                    <livewire:wirechat.chats />
                </div>
            </div>
        </div>

        <!-- Notifications Dropdown -->
        @php
            $user = Auth::user();
            $unreadCount = $user ? $user->unreadNotifications()->count() : 0;
        @endphp
        <div x-data="{ notifDropdownOpen: false }" class="relative">
            <button @click="notifDropdownOpen = !notifDropdownOpen"
                    class="p-2 text-gray-500 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-full hover:bg-gray-100 dark:hover:bg-neutral-800 transition-colors duration-200 relative">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1h6z" />
                </svg>

                @if($unreadCount > 0)
                    <span class="absolute -top-0.5 -end-0.5 bg-red-500 text-white text-[10px] font-semibold rounded-full h-4.5 min-w-[1.125rem] px-1 flex items-center justify-center">
                        {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                    </span>
                @endif
            </button>
            <div x-show="notifDropdownOpen" @click.away="notifDropdownOpen=false"
                 x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1"
                 x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-1"
                 class="absolute end-0 mt-2 w-96 max-w-[92vw] origin-top-end z-50" x-cloak>
                <div class="text-sm h-[500px] bg-white border overflow-hidden dark:border-neutral-700 rounded-xl shadow-xl border-gray-200 text-neutral-700 dark:bg-neutral-900">
                    <x-user-notification-menu count:5 />
                </div>
            </div>
        </div>

        <div class="w-px h-6 bg-gray-200 mx-1 hidden sm:block"></div>

        <!-- Profile Dropdown -->
        <div x-data="{ dropdownOpen: false }" class="relative">
            <button @click="dropdownOpen = !dropdownOpen" type="button"
                    class="flex items-center gap-2 rounded-full p-0.5 pe-2 hover:bg-gray-100 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2">
                <span class="sr-only">Open user menu</span>
                <img class="h-8 w-8 rounded-full object-cover ring-1 ring-gray-200"
                     src="{{ Auth::user()->personal_image }}"
                     alt="{{ Auth::user()->name }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 hidden sm:block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>
            <div x-show="dropdownOpen" @click.away="dropdownOpen = false"
                 x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                 class="absolute end-0 mt-2 w-56 origin-top-end rounded-xl bg-white shadow-xl ring-1 ring-black ring-opacity-5 focus:outline-none dark:bg-neutral-800 dark:border dark:border-neutral-700"
                 x-cloak>
                <div class="px-4 py-3 border-b border-gray-100 dark:border-neutral-700">
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ Auth::user()->name }}</p>
                    <p class="text-sm text-gray-500 truncate dark:text-gray-300">{{ Auth::user()->email }}</p>
                </div>
                <div class="py-1">
                    <a href="{{ route('profile.show.custom') }}"
                       class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-neutral-700 transition-colors duration-200">
                        <svg class="me-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span>الملف الشخصي</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="flex w-full items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-neutral-700 transition-colors duration-200">
                            <svg class="me-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            <span>تسجيل الخروج</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
