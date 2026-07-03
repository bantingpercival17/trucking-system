<!doctype html>
<html lang="en" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Dashboard')</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}"/>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/core/libs.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/gigz.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendor/@fortawesome/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css"/>
</head>

<body>
    
<div id="loading">
    <div class="loader">
        <div class="loader-body">
            <h1 class="loader-title fw-bold">GIGZ</h1>
        </div>
    </div>
</div>

@php
    $role = auth()->user()->role ?? null;
    $isOwner = $role === 'owner';
@endphp

@if($isOwner)
    @include('partials.sidebar')
@endif

<main class="main-content">
    @if($isOwner)
        @include('partials.navbar')
    @else
        @include('partials.topnav_manager')
        <style>.main-content{margin-left:0!important;}</style>
    @endif

    <div class="container-fluid content-inner mt-6 py-0">
        @yield('content')
    </div>

    @if($isOwner)
        @include('partials.footer')
    @endif
</main>


<!-- JS -->
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
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

</body>
</html>


