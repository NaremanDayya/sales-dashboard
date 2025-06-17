<head>
    <!-- Primary Meta Tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('description', 'Admin Dashboard')">
    <meta name="keywords" content="@yield('keywords', 'dashboard, admin')">
    <title>@yield('title', 'Admin Dashboard')</title>

    <!-- Security & Identification -->
    <meta name="user-id" content="{{ Auth::id() }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Preconnect & Preload Critical Resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="{{ asset('assets/css/style.css') }}" as="style">
    <link rel="preload" href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" as="style">

    <!-- Favicons -->
    <link rel="icon" href="{{ asset('assets/img/favicon.png') }}" type="image/png">
    <link rel="apple-touch-icon" href="{{ asset('assets/img/apple-touch-icon.png') }}">

    <!-- CSS Reset & Base Normalization -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/8.0.1/normalize.min.css"
        crossorigin="anonymous">

    <!-- Font Loading (Optimized) -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&family=Amiri&family=Open+Sans:wght@300;400;600;700&family=Nunito:wght@300;400;600;700&family=Poppins:wght@300;400;500;600;700&display=swap"
        crossorigin="anonymous">

    <!-- Font Awesome (Latest) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-iecdLmaskl7CVkqkXNQ/ZH/XLlvWZOJyj7Yy7tcenmpD1ypASozpmT/E0iPtmFIB46ZmdtAc9eNBvH0H/ZpiBw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- Core CSS Libraries (Order Matters!) -->
    <!-- Bootstrap 5 -->
    <link href="{{ asset('assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Vendor CSS (Combined where possible) -->
    <link href="{{ asset('assets/vendor/boxicons/css/boxicons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/remixicon/remixicon.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/vendor/simple-datatables/style.css') }}" rel="stylesheet">

    <!-- Quill Editor (Load only if needed) -->
    @stack('quill-styles')

    <!-- Animation Library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"
        crossorigin="anonymous" />

    <!-- Toast Notifications -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css" crossorigin="anonymous">

    <!-- Application Styles -->
    @vite(["resources/css/app.css", "resources/js/app.js"])
    @livewireStyles
    @wirechatStyles

    <!-- Template Main CSS (Should come after all libraries) -->
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet"
        crossorigin="anonymous">
    @stack('multiselect-styles')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer
        crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    @stack('multiselect-scripts')

    <!-- Additional Styles -->
    @stack('styles')

</head>
