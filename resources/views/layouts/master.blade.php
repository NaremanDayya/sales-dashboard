<!DOCTYPE html>
<html lang="ar">
@include('components.toast')

@include('partials.head')

<body class="bg-gray-100" style="display: flex; flex-direction: column; min-height: 100vh; margin: 0; padding: 0;">

    <!-- ======= Header ======= -->
    @include('partials.header')
    <!-- End Header -->

    <!-- ======= Sidebar ======= -->
    @include('partials.sidebar')

    <!-- End Sidebar -->
    <main id="main" class="main h-[calc(100vh_-_3.9rem)]" style="flex: 1;">
        @if (session('success'))
        <div class="toast-container position-fixed" style="top: 100px; right: 20px; z-index: 1300;">
            <div class="toast show align-items-center text-bg-success border-0" role="alert" aria-live="assertive"
                aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>
        @endif
        @if (session('warning'))
        <div class="toast-container position-fixed" style="top: 100px; right: 20px; z-index: 1300;">
            <div class="toast show align-items-center text-bg-warning border-0" role="alert" aria-live="assertive"
                aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ session('warning') }}
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>
        @endif
        @if ($errors->any())
        <div class="toast-container position-fixed" style="top: 100px; right: 20px; z-index: 1300;">
            <div class="toast show align-items-center text-bg-warning border-0" role="alert" aria-live="assertive"
                aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ $errors->first() }}
                    </div>
                    <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        </div>
        @endif

        @yield('content')
        {{-- {{$slot}} --}}
    </main>

    <!-- ======= Footer ======= -->
    @include('partials.footer')
    <!-- End Footer -->

    <!-- Vendor JS Files -->
    <script src="{{ asset('assets/vendor/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/cart.js/chart.umd.js') }}"></script>
    <script src="{{ asset('assets/vendor/echarts/echarts.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/quill/quill.js') }}"></script>
    <script src="{{ asset('assets/vendor/simple-datatables/simple-datatables.js') }}"></script>
    <script src="{{ asset('assets/vendor/tinymce/tinymce.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/php-email-form/validate.js') }}"></script>
    <script src="//unpkg.com/alpinejs" defer></script>



    <!-- Template Main JS File -->
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.1.4/toastr.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script src="resources/js/app.js"></script>
    <script>
        window.userId = {{ Auth::id() }};
    </script>
{{-- <script>
    document.addEventListener('alpine:init', () => {
    Alpine.store('notifications', {
        unreadCount: {{ $unreadCount }},

        init() {
            if (typeof Echo !== 'undefined') {
                Echo.private(`user.notifications.${{{ auth()->id() }}}`)
                    .notification((notification) => {
                        this.unreadCount++;
                        this.refreshList();
                    });
            }
        },

        refreshList() {
            fetch('{{ route("notifications.list") }}')
                .then(response => response.text())
                .then(html => {
                    document.getElementById('notification-list').innerHTML = html;
                });
        },

        markAsRead() {
            this.unreadCount = 0;
        }
    });
});
</script> --}}

    @livewireScripts
    @wirechatAssets
    @stack('scripts')

</body>
</html>
