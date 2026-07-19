@php
    $salesRepId = null;
    $isManager = false;
    if (Auth::user()->role == 'salesRep' && Auth::user()->salesRep) {
        $salesRepId = Auth::user()->salesRep->id;
        $isManager = method_exists(Auth::user()->salesRep, 'isManager') && Auth::user()->salesRep->isManager();
    }

    $navIcon = function (string $path) {
        return '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="' . $path . '"/></svg>';
    };
@endphp

<aside
    x-cloak
    :class="{ 'is-collapsed': sidebarCollapsed, 'is-open': sidebarOpen }"
    class="app-sidebar flex flex-col shrink-0 bg-white border-e border-gray-200 h-screen sticky top-0 z-50"
>
    <!-- Brand -->
    <div class="flex items-center gap-3 h-[--app-topbar-height] px-4 border-b border-gray-100 shrink-0">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 min-w-0">
            <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="h-9 w-9 rounded-lg object-contain shrink-0">
            <span x-show="!sidebarCollapsed" x-transition.opacity class="font-bold text-gray-900 truncate">آفاق الخليج</span>
        </a>
        <button @click="sidebarOpen = false" class="ms-auto lg:hidden text-gray-400 hover:text-gray-600 p-1 rounded-md">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M6 18L18 6M6 6l12 12"/>
            </svg>
        </button>
    </div>

    <!-- Nav -->
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-6">
        @php
            $navLink = function ($route, $label, $icon, $params = null) use ($navIcon) {
                $active = request()->routeIs($route);
                $href = $params !== null ? route($route, $params) : route($route);
                $classes = $active
                    ? 'bg-indigo-50 text-indigo-700'
                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900';
                echo '<a href="' . $href . '" class="group flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition-colors ' . $classes . '">'
                    . '<span class="' . ($active ? 'text-indigo-600' : 'text-gray-400 group-hover:text-gray-500') . '">' . $navIcon($icon) . '</span>'
                    . '<span x-show="!sidebarCollapsed" x-transition.opacity class="truncate">' . $label . '</span>'
                    . '</a>';
            };
        @endphp

        <div>
            <p x-show="!sidebarCollapsed" class="px-3 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">القائمة الرئيسية</p>
            <div class="space-y-1">
                {!! $navLink('dashboard', 'الرئيسية', 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6') !!}

                @if(Auth::user()->hasRole('admin'))
                    {!! $navLink('sales-reps.index', 'سفراء العلامة التجارية', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z') !!}
                    {!! $navLink('admin.team.index', 'إدارة الفرق', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z') !!}
                @elseif(Auth::user()->role == 'salesRep')
                    {!! $navLink('sales-rep.targets.index', 'التارجت', 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', $salesRepId) !!}
                @endif

                @if(Auth::user()->hasRole('admin'))
                    {!! $navLink('allClients', 'العملاء', 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4') !!}
                @elseif(Auth::user()->role == 'salesRep')
                    {!! $navLink('sales-reps.clients.index', 'عملائي', 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4', $salesRepId) !!}
                @endif

                {!! $navLink('services.index', 'الخدمات', 'M5 13l4 4L19 7') !!}

                @if(Auth::user()->hasRole('admin'))
                    {!! $navLink('allAgreements', 'الاتفاقيات', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2') !!}
                    {!! $navLink('allTargets', 'التارجت العام', 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z') !!}
                @elseif(Auth::user()->role == 'salesRep')
                    {!! $navLink('salesrep.agreements.index', 'اتفاقياتي', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', $salesRepId) !!}
                @endif

                @if(Auth::user()->hasRole('admin'))
                    {!! $navLink('admin.allRequests', 'الطلبات', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z') !!}
                    {!! $navLink('commissions.index', 'العمولات', 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l7-4 7 4z') !!}
                @elseif(Auth::user()->role == 'salesRep')
                    {!! $navLink('myRequests', 'طلباتي', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z', $salesRepId) !!}
                    {!! $navLink('sales-reps.commissions.index', 'عمولاتي', 'M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l7-4 7 4z', $salesRepId) !!}
                @endif
            </div>
        </div>

        @if($isManager)
            <div>
                <p x-show="!sidebarCollapsed" class="px-3 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">إدارة الفريق</p>
                <div class="space-y-1">
                    {!! $navLink('manager.dashboard', 'لوحة الفريق', 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6') !!}
                    {!! $navLink('manager.team.clients', 'عملاء الفريق', 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z') !!}
                    {!! $navLink('manager.team.agreements', 'اتفاقيات الفريق', 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2') !!}
                    {!! $navLink('manager.chats.index', 'محادثات الفريق', 'M8 3h8a5 5 0 015 5v6a5 5 0 01-5 5h-4l-4 4v-4H8a5 5 0 01-5-5V8a5 5 0 015-5z') !!}
                </div>
            </div>
        @endif

        @if(Auth::user()->hasRole('admin'))
            <div>
                <p x-show="!sidebarCollapsed" class="px-3 mb-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">النظام</p>
                <div class="space-y-1">
                    {!! $navLink('admin.sales-rep-ips.index', 'عناوين الدخول', 'M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z') !!}
                    {!! $navLink('settings.index', 'الإعدادات', 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z') !!}
                </div>
            </div>
        @endif
    </nav>

    <!-- Footer / collapse toggle -->
    <div class="border-t border-gray-100 p-3 shrink-0">
        <button
            @click="sidebarCollapsed = !sidebarCollapsed"
            class="hidden lg:flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-500 hover:bg-gray-50 hover:text-gray-900 transition-colors"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 transition-transform" :class="{ 'rotate-180': sidebarCollapsed }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
            </svg>
            <span x-show="!sidebarCollapsed" x-transition.opacity>طي القائمة</span>
        </button>
    </div>
</aside>

<!-- Mobile backdrop -->
<div
    x-show="sidebarOpen"
    x-cloak
    @click="sidebarOpen = false"
    x-transition.opacity
    class="fixed inset-0 bg-gray-900/50 z-40 lg:hidden"
></div>
