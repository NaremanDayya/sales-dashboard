<div class="h-full flex flex-col bg-white dark:bg-neutral-900 rounded-lg shadow-sm overflow-hidden">
    <!-- Header -->
    <div class="p-4 border-b dark:border-neutral-800 flex justify-between items-center bg-gray-50 dark:bg-neutral-800">
        <div class="flex items-center space-x-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 dark:text-neutral-400" fill="none"
                viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <h3 class="font-medium text-gray-800 dark:text-neutral-200">Notifications</h3>
        </div>
        @if($unreadCount > 0)
        <span class="bg-primary-500 text-white text-xs font-semibold px-2 py-1 rounded-full animate-pulse"
            x-text="$store.notifications.unreadCount"></span>
        @endif
    </div>

    <!-- Mark All as Read -->
    @if($unreadCount > 0)
    <div class="border-b dark:border-neutral-800 bg-gray-50 dark:bg-neutral-800">
        <form action="{{ route('notifications.markAllAsRead') }}" method="POST" class="text-center p-2">
            @csrf
            <button type="submit"
                class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 transition-colors duration-200 flex items-center justify-center space-x-1 mx-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Mark all as read</span>
            </button>
        </form>
    </div>
    @endif

    <!-- Notifications List -->
    <div class="flex-1 overflow-y-auto divide-y divide-gray-100 dark:divide-neutral-800" id="notification-list">
        @include('notifications.notifications_list')
    </div>

    <!-- Footer -->
    <div class="p-3 border-t dark:border-neutral-800 text-center bg-gray-50 dark:bg-neutral-800">
        <a href="{{ route('notifications.index') }}"
            class="text-sm text-primary-600 dark:text-primary-400 hover:text-primary-800 dark:hover:text-primary-300 transition-colors duration-200 inline-flex items-center">
            <span>View all notifications</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    </div>
</div>
