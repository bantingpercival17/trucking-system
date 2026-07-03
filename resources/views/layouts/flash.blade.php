@php
    $prefix = session('layout') === 'flash' ? 'flash' : 'owner';
@endphp


<!doctype html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', 'Flash')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}" />

    <link rel="stylesheet" href="{{ asset('assets/css/core/libs.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/gigz.min.css?v=1.0.0') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/custom.min.css?v=1.0.0') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/@fortawesome/fontawesome-free/css/all.min.css') }}" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    @stack('styles')
</head>

<body>

    {{-- ✅ ALWAYS SHOW SIDEBAR --}}
    @include('partials.sidebar-flash')
    
    <main class="main-content">

        {{-- ✅ ALWAYS SHOW NAVBAR --}}
        @include('partials.navbar')

        <div class="container-fluid content-inner mt-3 py-0" style="padding-left: 16px;padding-right: 16px;">
            @yield('content')
        </div>

    </main>

    {{-- ✅ JS FOR ALL ROLES --}}
    <script src="{{ asset('assets/js/core/libs.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/external.min.js') }}"></script>

    <script src="{{ asset('assets/js/charts/widgetcharts.js') }}"></script>
    <script src="{{ asset('assets/js/charts/vectore-chart.js') }}"></script>
    <script src="{{ asset('assets/js/charts/dashboard.js') }}" defer></script>
    <script src="{{ asset('assets/js/plugins/fslightbox.js') }}"></script>
    <script src="{{ asset('assets/vendor/gsap/gsap.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/gsap/ScrollTrigger.min.js') }}"></script>
    <script src="{{ asset('assets/js/gsap-init.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/form-wizard.js') }}"></script>

    <script src="{{ asset('assets/js/gigz.js') }}" defer></script>

    {{-- Bootstrap --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof bootstrap === 'undefined') return;

            document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(function(toggle) {
                bootstrap.Dropdown.getOrCreateInstance(toggle);
            });

            function closeAllDropdowns() {
                document.querySelectorAll('[data-bs-toggle="dropdown"]').forEach(function(toggle) {
                    const inst = bootstrap.Dropdown.getInstance(toggle);
                    if (inst) inst.hide();
                });

                document.querySelectorAll('.dropdown-menu.show').forEach(m => m.classList.remove('show'));
                document.querySelectorAll('.dropdown-toggle.show').forEach(t => t.classList.remove('show'));
            }

            document.addEventListener('click', function(e) {
                if (!e.target.closest('.dropdown')) closeAllDropdowns();
            });

            window.addEventListener('pageshow', closeAllDropdowns);
        });
    </script>

    @stack('scripts')

</body>

</html>
