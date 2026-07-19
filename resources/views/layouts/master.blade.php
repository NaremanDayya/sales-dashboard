<!DOCTYPE html>
<html lang="ar" dir="rtl">
@include('components.toast')

@include('partials.head')

<body class="bg-slate-50 antialiased">
    <div class="app-shell flex" x-data="{ sidebarOpen: false, sidebarCollapsed: false }">
        @include('partials.sidebar')

        <div class="app-main flex-1 flex flex-col min-h-screen">
            @include('partials.header')

            <main id="main" class="flex-1 px-4 py-6 sm:px-6 lg:px-8 w-full">
                @if (session('success'))
                    <div class="mb-4 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <p class="flex-1">{{ session('success') }}</p>
                        <button @click="show = false" class="text-emerald-500 hover:text-emerald-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif
                @if (session('warning'))
                    <div class="mb-4 flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                        <p class="flex-1">{{ session('warning') }}</p>
                        <button @click="show = false" class="text-amber-500 hover:text-amber-700">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-4 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                        <p class="flex-1">{{ $errors->first() }}</p>
                    </div>
                @endif
                @if(session()->has('impersonator_id'))
                    <div class="mb-4 flex flex-wrap items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">
                        <span>انت تستخدم الأن حساب سفير العلامة التجارية : {{ session('sales_Rep_name') }}</span>
                        <a href="{{ url('/admin/impersonate/stop') }}" class="font-semibold underline hover:text-amber-900">العودة لحساب الادمن</a>
                    </div>
                @endif

                @yield('content')
            </main>

            @include('partials.footer')
        </div>
    </div>

    <script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/ar.js"></script>

    <script>
        window.userId = {{ Auth::id() }};
    </script>

    @livewireScripts
    @wirechatAssets
    @stack('scripts')
</body>
</html>
