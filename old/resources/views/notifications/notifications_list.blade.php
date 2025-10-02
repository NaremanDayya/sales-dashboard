 @forelse ($notifications as $notification)
        @php
        $link = $notification->data['url'] ?? request()->url();
        @endphp
        <a href="{{ route('notification.redirect', ['nid' => $notification->id, 'redirect_to' => $link]) }}"
            class="block p-4 hover:bg-gray-50 dark:hover:bg-neutral-800 transition-colors duration-150 {{ $notification->unread() ? 'bg-blue-50/50 dark:bg-neutral-800/80' : '' }}">
            <div class="flex items-start space-x-3">
                <!-- Notification Icon -->
                <div class="flex-shrink-0 mt-0.5">
                    <div
                        class="h-8 w-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary-600 dark:text-primary-400"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Notification Content -->
                <div class="flex-1 min-w-0">
                    <p
                        class="{{ $notification->unread() ? 'font-semibold text-gray-900 dark:text-white' : 'text-gray-800 dark:text-neutral-200' }} mb-1">
                        {{ $notification->data['message'] }}
                    </p>
                    <div class="flex items-center space-x-2 text-xs text-gray-500 dark:text-neutral-400">
                        <span>{{ $notification->created_at->diffForHumans() }}</span>
                        @if($notification->unread())
                        <span class="inline-block h-2 w-2 rounded-full bg-primary-500"></span>
                        @endif
                    </div>
                </div>

                <!-- Unread Indicator -->
                @if($notification->unread())
                <div class="flex-shrink-0">
                    <span class="h-2 w-2 rounded-full bg-primary-500 block"></span>
                </div>
                @endif
            </div>
        </a>
        @empty
        <div class="p-8 text-center">
            <div class="mx-auto h-16 w-16 text-gray-400 dark:text-neutral-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                </svg>
            </div>
            <h4 class="mt-4 text-gray-500 dark:text-neutral-400 font-medium">No notifications yet</h4>
            <p class="mt-1 text-sm text-gray-400 dark:text-neutral-500">We'll notify you when something arrives</p>
        </div>
        @endforelse
