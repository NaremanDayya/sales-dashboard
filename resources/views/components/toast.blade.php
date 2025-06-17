<script>
    document.addEventListener('DOMContentLoaded', function () {
        @if (session('error'))
            Toastify({
                text: "{{ session('error') }}",
                duration: 5000,
                close: true,
                gravity: "top",
                position: "{{ app()->getLocale() == 'ar' ? 'left' : 'right' }}",
                backgroundColor: "#ff4d4d",
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
                backgroundColor: "#28a745",
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
                backgroundColor: "#ffc107", 
                rtl: {{ app()->getLocale() == 'ar' ? 'true' : 'false' }}
            }).showToast();
        @endif
    });
</script>
