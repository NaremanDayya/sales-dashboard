<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if (session('error'))
            Toastify({
                text: "{{ session('error') }}",
                duration: 5000,
                close: true,
                gravity: "top",
                position: "{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}",
                backgroundColor: "#e11d48",
                rtl: {{ app()->getLocale() == 'ar' ? 'true' : 'false' }}
            }).showToast();
        @endif

        @if (session('success'))
            Toastify({
                text: "{{ session('success') }}",
                duration: 5000,
                close: true,
                gravity: "top",
                position: "{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}",
                backgroundColor: "#059669",
                rtl: {{ app()->getLocale() == 'ar' ? 'true' : 'false' }}
            }).showToast();
        @endif
        @if (session('warning'))
            Toastify({
                text: "{{ session('warning') }}",
                duration: 5000,
                close: true,
                gravity: "top",
                position: "{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}",
                backgroundColor: "#d97706",
                rtl: {{ app()->getLocale() == 'ar' ? 'true' : 'false' }}
            }).showToast();
        @endif
    });
</script>
