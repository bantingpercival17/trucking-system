<!doctype html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{ config('app.name', 'Trucking System') }} | Register</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}" />

    <!-- Library / Plugin Css Build -->
    <link rel="stylesheet" href="{{ asset('assets/css/core/libs.min.css') }}" />

    <!-- Gigz Design System Css -->
    <link rel="stylesheet" href="{{ asset('assets/css/gigz.min.css?v=1.0.0') }}" />

    <!-- Custom Css -->
    <link rel="stylesheet" href="{{ asset('assets/css/custom.min.css?v=1.0.0') }}" />

    <!-- fontawsome Css Build -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/@fortawesome/fontawesome-free/css/all.min.css') }}">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />

    <style>
        :root {
            --bg1: #e3f2fd;
            --bg2: #f3e5f5;
        }

        body {
            min-height: 100vh;
            background:
                radial-gradient(900px 500px at 10% 10%, rgba(100,181,246,.15), transparent 60%),
                radial-gradient(900px 500px at 90% 20%, rgba(186,104,200,.12), transparent 60%),
                linear-gradient(135deg, var(--bg1), var(--bg2));
            background-attachment: fixed;
        }

        .glass {
            background: rgba(255,255,255,.97);
            border: 1px solid rgba(100,181,246,.20);
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(0,0,0,.08);
            backdrop-filter: blur(10px);
        }

        .card-body {
            background: rgba(255,255,255,.97) !important;
            border: 1px solid rgba(100,181,246,.15) !important;
            border-radius: 18px !important;
            box-shadow: 0 8px 32px rgba(0,0,0,.08) !important;
            backdrop-filter: blur(10px);
        }

        .form-label {
            color: #1976d2 !important;
            font-weight: 600;
        }

        .form-control, .form-select {
            background: rgba(255,255,255,.7) !important;
            border: 1.5px solid rgba(33,150,243,.2) !important;
            color: #333 !important;
            border-radius: 10px !important;
            transition: all 0.3s ease !important;
        }

        .form-control:focus, .form-select:focus {
            background: rgba(255,255,255,.95) !important;
            border-color: #1976d2 !important;
            box-shadow: 0 0 0 3px rgba(33,150,243,.1) !important;
            color: #333 !important;
        }

        .form-control::placeholder {
            color: rgba(100,100,100,.4) !important;
        }

        .form-check-label {
            color: #666 !important;
            font-weight: 500;
        }

        .btn-primary {
            background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%) !important;
            border: none !important;
            box-shadow: 0 4px 15px rgba(33,150,243,.3) !important;
            font-weight: 600 !important;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
            letter-spacing: 0.3px;
            padding: 0.7rem 2rem !important;
            border-radius: 10px !important;
            position: relative;
            overflow: hidden;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(33,150,243,.4) !important;
            background: linear-gradient(135deg, #1f8fe7 0%, #1565c0 100%) !important;
        }

        .btn-primary:active {
            transform: translateY(0);
        }

        h2 {
            color: #1976d2 !important;
            font-weight: 700 !important;
        }

        .text-center p {
            color: #666 !important;
        }

        a {
            color: #1976d2;
            transition: all 0.3s ease;
        }

        a:hover {
            color: #1565c0;
            text-decoration: underline !important;
        }

        .text-underline {
            font-weight: 600;
        }

        .logo-title {
            color: #1976d2 !important;
            font-weight: 700 !important;
        }

        .navbar-brand {
            transition: all 0.3s ease;
        }

        .navbar-brand:hover .logo-title {
            color: #1565c0 !important;
            transform: scale(1.02);
        }

        .alert-danger {
            background: rgba(244,67,54,.1) !important;
            border: 1px solid rgba(244,67,54,.2) !important;
            color: #d32f2f !important;
            border-radius: 10px !important;
        }

        .auth-shap {
            opacity: 0.3;
        }

        /* Social icons styling */
        .list-group-item {
            background: transparent !important;
            border: none !important;
        }

        .list-group-item a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(33,150,243,.1);
            border-radius: 50%;
            transition: all 0.3s ease;
        }

        .list-group-item a:hover {
            background: rgba(33,150,243,.2);
            transform: translateY(-3px);
            box-shadow: 0 6px 15px rgba(33,150,243,.2);
        }

        .wrapper {
            background: transparent;
        }

        /* Gradient Mesh & Icons Section */
        .auth-showcase {
            position: relative;
            width: 100%;
            height: 100%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: linear-gradient(135deg, rgba(227, 242, 253, 0.5) 0%, rgba(243, 229, 245, 0.3) 100%);
        }

        /* Animated blob shapes */
        .blob-1, .blob-2, .blob-3, .blob-4 {
            position: absolute;
            border-radius: 50%;
            opacity: 0.3;
            filter: blur(40px);
            animation: blob-float 8s ease-in-out infinite;
        }

        .blob-1 {
            width: 300px;
            height: 300px;
            background: linear-gradient(135deg, #2196f3, #1976d2);
            top: -50px;
            left: 50px;
            animation-delay: 0s;
        }

        .blob-2 {
            width: 250px;
            height: 250px;
            background: linear-gradient(135deg, #1976d2, #7b60e7);
            bottom: 100px;
            right: 50px;
            animation-delay: 2s;
        }

        .blob-3 {
            width: 200px;
            height: 200px;
            background: linear-gradient(135deg, #7b60e7, #2196f3);
            top: 50%;
            right: 10%;
            animation-delay: 4s;
        }

        .blob-4 {
            width: 180px;
            height: 180px;
            background: linear-gradient(135deg, rgba(76,175,80,0.8), #2196f3);
            bottom: 10%;
            left: 5%;
            animation-delay: 6s;
        }

        @keyframes blob-float {
            0%, 100% { transform: translate(0, 0) scale(1); }
            33% { transform: translate(30px, -30px) scale(1.1); }
            66% { transform: translate(-20px, 20px) scale(0.95); }
        }

        .mesh-background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><filter id="noise"><feTurbulence type="fractalNoise" baseFrequency="0.9" numOctaves="4" result="noise" seed="2"/></filter></defs><rect width="100" height="100" fill="%23e3f2fd" filter="url(%23noise)" opacity="0.1"/></svg>');
            animation: mesh-flow 8s ease-in-out infinite;
            opacity: 0.3;
            z-index: 1;
        }

        @keyframes mesh-flow {
            0%, 100% { transform: translateX(0) translateY(0); }
            50% { transform: translateX(10px) translateY(10px); }
        }

        .floating-icon {
            position: absolute;
            font-size: 4rem;
            opacity: 0;
            filter: drop-shadow(0 4px 12px rgba(33, 150, 243, 0.2));
            animation: float-in 2s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            z-index: 20;
        }

        @keyframes float-in {
            0% {
                opacity: 0;
                transform: translateY(30px) scale(0.8);
            }
            100% {
                opacity: 0.85;
                transform: translateY(0) scale(1);
            }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(3deg); }
        }

        @keyframes float-slow {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(-2deg); }
        }

        @keyframes float-slower {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-25px) rotate(2deg); }
        }

        .icon-truck {
            top: 10%;
            left: 15%;
            color: #2196f3;
            animation: float 4s ease-in-out infinite;
            animation-delay: 0.5s;
        }

        .icon-driver {
            top: 45%;
            right: 10%;
            color: #1976d2;
            animation: float-slow 5s ease-in-out infinite;
            animation-delay: 1s;
        }

        .icon-location {
            top: 15%;
            right: 20%;
            color: #1565c0;
            animation: float-slower 6s ease-in-out infinite;
            animation-delay: 0.2s;
        }

        .icon-clipboard {
            bottom: 15%;
            left: 10%;
            color: #2196f3;
            animation: float 4.5s ease-in-out infinite;
            animation-delay: 1.5s;
        }

        .icon-check {
            bottom: 25%;
            right: 15%;
            color: #4caf50;
            animation: float-slow 5.5s ease-in-out infinite;
            animation-delay: 0.8s;
        }

        .icon-zap {
            top: 60%;
            left: 5%;
            color: #ffc107;
            animation: float-slower 7s ease-in-out infinite;
            animation-delay: 1.2s;
        }

        /* Feature cards styling */
        .feature-card-mini {
            position: absolute;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(100, 181, 246, 0.15);
            padding: 1.2rem;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
            max-width: 160px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 15;
            opacity: 0;
            animation: card-fade-in 1s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        }

        @keyframes card-fade-in {
            0% {
                opacity: 0;
                transform: translateY(20px) scale(0.9);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .feature-card-mini:hover {
            transform: translateY(-8px);
            box-shadow: 0 16px 40px rgba(33, 150, 243, 0.2);
            border-color: rgba(33, 150, 243, 0.3);
            background: rgba(255, 255, 255, 0.98);
        }

        .card-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
            line-height: 1;
        }

        .card-title {
            font-size: 0.85rem;
            color: #1976d2;
            font-weight: 600;
            margin-bottom: 0.3rem;
        }

        .card-text {
            font-size: 0.75rem;
            color: #999;
            line-height: 1.3;
        }

        /* Top decorative circle */
        .deco-circle-top {
            position: absolute;
            top: 5%;
            right: 15%;
            width: 80px;
            height: 80px;
            border: 2px solid rgba(33, 150, 243, 0.2);
            border-radius: 50%;
            animation: rotate 20s linear infinite;
            z-index: 5;
        }

        /* Bottom decorative shapes */
        .deco-shape-bottom {
            position: absolute;
            bottom: 10%;
            left: 8%;
            width: 60px;
            height: 60px;
            background: rgba(33, 150, 243, 0.05);
            border: 2px solid rgba(33, 150, 243, 0.15);
            transform: rotate(45deg);
            animation: rotate-reverse 15s linear infinite;
            z-index: 5;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes rotate-reverse {
            0% { transform: rotate(45deg); }
            100% { transform: rotate(405deg); }
        }

        .showcase-content {
            position: relative;
            z-index: 20;
            text-align: center;
            max-width: 420px;
        }

        .showcase-title {
            color: #1976d2;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            animation: fade-in-up 1s ease-out 0.3s both;
        }

        .showcase-text {
            color: #666;
            font-size: 1.05rem;
            line-height: 1.6;
            animation: fade-in-up 1s ease-out 0.5s both;
        }

        @keyframes fade-in-up {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 991px) {
            .auth-showcase {
                display: none;
            }
        }

        @media (max-width: 768px) {
            .form-control {
                font-size: 16px !important;
            }
        }
    </style>
</head>

<body class=" ">
    <!-- loader Start -->
    <div id="loading">
        <div class="loader ">
            <div class="loader-body word-spacing">
                <!-- UPDATED: removed GIGZ loader text -->
                <h1 class="loader-title fw-bold">
                    <i class="bi bi-truck-front me-2"></i>{{ config('app.name', 'Trucking System') }}
                </h1>
            </div>
        </div>
    </div>
    <!-- loader END -->

    <div class="wrapper">
        <div class="res-hide row m-0 align-items-center vh-100">
            <div class="col-lg-5">
                <div class="auth-shap position-relative">
                    <svg class="rect1" width="19" viewBox="0 0 19 19" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <rect y="11.0215" width="14" height="14" rx="2"
                            transform="rotate(-51.9256 0 11.0215)" fill="#FDDA5F" />
                    </svg>
                    <svg class="rect2" width="11" viewBox="0 0 11 10" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <rect y="5.7207" width="8" height="8" rx="2"
                            transform="rotate(-51.9256 0 5.7207)" fill="#FDDA5F" />
                    </svg>
                    <svg class="rect3" width="14" viewBox="0 0 14 13" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <rect y="7.48828" width="10" height="10" rx="2"
                            transform="rotate(-51.9256 0 7.48828)" fill="#7B60E7" />
                    </svg>
                    <svg class="rect4" width="19" viewBox="0 0 19 19" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <rect y="11.0195" width="14" height="14" rx="2"
                            transform="rotate(-51.9256 0 11.0195)" fill="#FFE0E3" />
                    </svg>
                    <svg class="rect5" width="25" height="25" viewBox="0 0 25 25" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <rect y="14.377" width="18.2858" height="18.2858" rx="2"
                            transform="rotate(-51.9256 0 14.377)" fill="#FAD1D1" />
                    </svg>
                    <svg class="rect6" width="13" height="12" viewBox="0 0 13 12" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <rect y="6.78516" width="9.51587" height="9.51587" rx="2"
                            transform="rotate(-51.9256 0 6.78516)" fill="#7B60E7" />
                    </svg>
                    <svg class="rect7" width="19" height="19" viewBox="0 0 19 19" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <rect y="11.0195" width="14" height="14" rx="2"
                            transform="rotate(-51.9256 0 11.0195)" fill="#FDDA5F" />
                    </svg>
                    <svg class="rect8" width="17" height="16" viewBox="0 0 17 16" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <rect y="9.25195" width="12" height="12" rx="2"
                            transform="rotate(-51.9256 0 9.25195)" fill="#FDDA5F" />
                    </svg>
                </div>

                <div class="card-body">
                    <!-- UPDATED: replaced Gigz brand with Trucking -->
                    <a href="{{ url('/') }}" class="navbar-brand d-flex align-items-center gap-2 mb-3">
                        <span class="bg-white text-primary rounded-3 px-2 py-1 fw-bold"
                              style="box-shadow: 0 2px 10px rgba(25,118,210,.18);">
                            <i class="bi bi-truck-front"></i>
                        </span>
                        <h2 class="logo-title m-0 fs-4">
                            {{ config('app.name', 'Trucking System') }}
                        </h2>
                    </a>

                    <h2 class="mb-2 text-center">Sign Up</h2>
                    <p class="text-center">Create your account.</p>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="full-name" class="form-label">Full Name</label>
                                    <input type="text" name="name" class="form-control border-primary"
                                        value="{{ old('name') }}" required placeholder=" ">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control border-primary"
                                        value="{{ old('email') }}" required placeholder=" ">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control border-primary"
                                        required placeholder=" ">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="confirm-password" class="form-label">Confirm Password</label>
                                    <input type="password" name="password_confirmation"
                                        class="form-control border-primary" required placeholder=" ">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Role</label>
                                <select name="role" class="form-select" required>
                                    <option value="owner" @selected(old('role') === 'owner')>Owner</option>
                                    <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                                    <option value="user" @selected(old('role') === 'user')>User</option>
                                </select>
                            </div>

                            <div class="col-lg-12 d-flex justify-content-center">
                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" id="customCheck1">
                                    <label class="form-check-label" for="customCheck1">I agree with the terms of use</label>
                                </div>
                            </div>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn-primary">Sign Up</button>
                        </div>

                        <p class="text-center my-3">or sign in with other accounts?</p>

                        <div class="d-flex justify-content-center">
                            <ul class="list-group list-group-horizontal list-group-flush auth-social-list">
                                <li class="list-group-item border-0 pb-0">
                                    <a href="#"><img src="../../assets/images/brands/gm.svg" alt="gm"></a>
                                </li>
                                <li class="list-group-item border-0 pb-0">
                                    <a href="#"><img src="../../assets/images/brands/fb.svg" alt="fb"></a>
                                </li>
                                <li class="list-group-item border-0 pb-0">
                                    <a href="#"><img src="../../assets/images/brands/im.svg" alt="im"></a>
                                </li>
                                <li class="list-group-item border-0 pb-0">
                                    <a href="#"><img src="../../assets/images/brands/li.svg" alt="li"></a>
                                </li>
                            </ul>
                        </div>

                        <p class="mt-3 text-center">
                            Already have an Account <a href="{{ route('login') }}" class="text-underline">Sign In</a>
                        </p>
                    </form>
                </div>
            </div>

            <div class="col-lg-7 d-md-block d-none p-0">
                <div class="auth-showcase">
                    <!-- Animated Blobs -->
                    <div class="blob-1"></div>
                    <div class="blob-2"></div>
                    <div class="blob-3"></div>
                    <div class="blob-4"></div>

                    <!-- Mesh Background -->
                    <div class="mesh-background"></div>

                    <!-- Decorative Elements -->
                    <div class="deco-circle-top"></div>
                    <div class="deco-shape-bottom"></div>

                    <!-- Feature Cards -->
                    <div class="feature-card-mini" style="top: 15%; left: 8%; animation-delay: 0.2s;">
                        <div class="card-icon">🚚</div>
                        <div class="card-title">Fleet Mgmt</div>
                        <div class="card-text">Trucks & Units</div>
                    </div>

                    <div class="feature-card-mini" style="top: 22%; right: 5%; animation-delay: 0.4s;">
                        <div class="card-icon">👥</div>
                        <div class="card-title">Drivers</div>
                        <div class="card-text">Team Control</div>
                    </div>

                    <div class="feature-card-mini" style="bottom: 25%; left: 5%; animation-delay: 0.6s;">
                        <div class="card-icon">📍</div>
                        <div class="card-title">Tracking</div>
                        <div class="card-text">Route & GPS</div>
                    </div>

                    <div class="feature-card-mini" style="bottom: 20%; right: 8%; animation-delay: 0.8s;">
                        <div class="card-icon">✓</div>
                        <div class="card-title">Real-time</div>
                        <div class="card-text">Live Updates</div>
                    </div>

                    <!-- Floating Icons -->
                    <div class="floating-icon icon-truck" title="Fleet Management">
                        <i class="bi bi-truck-front"></i>
                    </div>

                    <div class="floating-icon icon-driver" title="Driver Management">
                        <i class="bi bi-person-badge"></i>
                    </div>

                    <div class="floating-icon icon-location" title="Route Tracking">
                        <i class="bi bi-geo-alt"></i>
                    </div>

                    <div class="floating-icon icon-clipboard" title="Trip Tickets">
                        <i class="bi bi-clipboard-check"></i>
                    </div>

                    <div class="floating-icon icon-check" title="Real-time Updates">
                        <i class="bi bi-check-circle"></i>
                    </div>

                    <div class="floating-icon icon-zap" title="Fast & Reliable">
                        <i class="bi bi-lightning-charge"></i>
                    </div>

                    <!-- Center Content -->
                    <div class="showcase-content">
                        <div class="showcase-title">Join Us Today</div>
                        <p class="showcase-text">
                            Start managing your fleet with our intelligent dispatch system. Secure, fast, and reliable.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="res-hide1 d-none row m-0 align-items-center vh-100">
            <div class="col-md-12  col-xl-7 d-md-block d-none p-0">
                <img src="{{ asset('assets/images/auth/07.png') }}" class="img-fluid gradient-main vh-100" alt="images">
            </div>

            <div class="col-md-12 col-xl-5">
                <div class="auth-shap position-relative">
                    <svg class="rect1" width="19" viewBox="0 0 19 19" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <rect y="11.0215" width="14" height="14" rx="2"
                            transform="rotate(-51.9256 0 11.0215)" fill="#FDDA5F" />
                    </svg>
                    <svg class="rect2" width="11" viewBox="0 0 11 10" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <rect y="5.7207" width="8" height="8" rx="2"
                            transform="rotate(-51.9256 0 5.7207)" fill="#FDDA5F" />
                    </svg>
                    <svg class="rect3" width="14" viewBox="0 0 14 13" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <rect y="7.48828" width="10" height="10" rx="2"
                            transform="rotate(-51.9256 0 7.48828)" fill="#7B60E7" />
                    </svg>
                    <svg class="rect4" width="19" viewBox="0 0 19 19" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <rect y="11.0195" width="14" height="14" rx="2"
                            transform="rotate(-51.9256 0 11.0195)" fill="#FFE0E3" />
                    </svg>
                    <svg class="rect5" width="25" height="25" viewBox="0 0 25 25" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <rect y="14.377" width="18.2858" height="18.2858" rx="2"
                            transform="rotate(-51.9256 0 14.377)" fill="#FAD1D1" />
                    </svg>
                    <svg class="rect6" width="13" height="12" viewBox="0 0 13 12" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <rect y="6.78516" width="9.51587" height="9.51587" rx="2"
                            transform="rotate(-51.9256 0 6.78516)" fill="#7B60E7" />
                    </svg>
                    <svg class="rect7" width="19" height="19" viewBox="0 0 19 19" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <rect y="11.0195" width="14" height="14" rx="2"
                            transform="rotate(-51.9256 0 11.0195)" fill="#FDDA5F" />
                    </svg>
                    <svg class="rect8" width="17" height="16" viewBox="0 0 17 16" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <rect y="9.25195" width="12" height="12" rx="2"
                            transform="rotate(-51.9256 0 9.25195)" fill="#FDDA5F" />
                    </svg>
                </div>

                <div class="card-body">
                    <!-- UPDATED: replaced Gigz brand with Trucking -->
                    <a href="{{ url('/') }}" class="navbar-brand d-flex align-items-center gap-2 mb-3">
                        <span class="bg-white text-primary rounded-3 px-2 py-1 fw-bold"
                              style="box-shadow: 0 2px 10px rgba(25,118,210,.18);">
                            <i class="bi bi-truck-front"></i>
                        </span>
                        <h2 class="logo-title m-0 fs-4">
                            {{ config('app.name', 'Trucking System') }}
                        </h2>
                    </a>

                    <h2 class="mb-2 text-center">Sign Up</h2>
                    <p class="text-center">Create your account.</p>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="full-name" class="form-label">Full Name</label>
                                    <input type="text" name="name" class="form-control border-primary"
                                        value="{{ old('name') }}" required placeholder=" ">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control border-primary"
                                        value="{{ old('email') }}" required placeholder=" ">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control border-primary"
                                        required placeholder=" ">
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label for="confirm-password" class="form-label">Confirm Password</label>
                                    <input type="password" name="password_confirmation"
                                        class="form-control border-primary" required placeholder=" ">
                                </div>
                            </div>

                            <div class="col-lg-12 d-flex justify-content-center">
                                <div class="form-check mb-3">
                                    <input type="checkbox" class="form-check-input" id="customCheck10">
                                    <label class="form-check-label" for="customCheck10">I agree with the terms of use</label>
                                </div>
                            </div>
                        </div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn-primary">Sign Up</button>
                        </div>

                        <p class="text-center my-3">or sign in with other accounts?</p>

                        <div class="d-flex justify-content-center">
                            <ul class="list-group list-group-horizontal list-group-flush">
                                <li class="list-group-item border-0 pb-0">
                                    <a href="#"><img src="{{ asset('assets/images/brands/gm.svg') }}" alt="gm"></a>
                                </li>
                                <li class="list-group-item border-0 pb-0">
                                    <a href="#"><img src="{{ asset('assets/images/brands/fb.svg') }}" alt="fb"></a>
                                </li>
                                <li class="list-group-item border-0 pb-0">
                                    <a href="#"><img src="{{ asset('assets/images/brands/im.svg') }}" alt="im"></a>
                                </li>
                                <li class="list-group-item border-0 pb-0">
                                    <a href="#"><img src="{{ asset('assets/images/brands/li.svg') }}" alt="li"></a>
                                </li>
                            </ul>
                        </div>

                        <p class="mt-3 text-center">
                            Already have an Account <a href="{{ route('login') }}" class="text-underline">Sign In</a>
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Library Bundle Script -->
    <script src="{{ asset('assets/js/core/libs.min.js') }}"></script>

    <!-- External Library Bundle Script -->
    <script src="{{ asset('assets/js/core/external.min.js') }}"></script>

    <!-- Widgetchart Script -->
    <script src="{{ asset('assets/js/charts/widgetcharts.js') }}"></script>

    <!-- mapchart Script -->
    <script src="{{ asset('assets/js/charts/vectore-chart.js') }}"></script>
    <script src="{{ asset('assets/js/charts/dashboard.js') }}" defer></script>

    <!-- fslightbox Script -->
    <script src="{{ asset('assets/js/plugins/fslightbox.js') }}"></script>

    <!-- GSAP Animation -->
    <script src="{{ asset('assets/vendor/gsap/gsap.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/gsap/ScrollTrigger.min.js') }}"></script>
    <script src="{{ asset('assets/js/gsap-init.js') }}"></script>

    <!-- Form Wizard Script -->
    <script src="{{ asset('assets/js/plugins/form-wizard.js') }}"></script>

    <!-- App Script -->
    <script src="{{ asset('assets/js/gigz.js') }}" defer></script>

    <!-- Swiper JS -->
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

    <script>
      // Smooth form transitions
      document.querySelectorAll('.form-control, .form-select').forEach(input => {
          input.addEventListener('focus', function() {
              this.parentElement.classList.add('focused');
          });

          input.addEventListener('blur', function() {
              this.parentElement.classList.remove('focused');
          });
      });

      // Button ripple effect
      document.querySelectorAll('.btn-primary').forEach(button => {
          button.addEventListener('click', function(e) {
              const rect = this.getBoundingClientRect();
              const x = e.clientX - rect.left;
              const y = e.clientY - rect.top;

              const ripple = document.createElement('span');
              ripple.style.position = 'absolute';
              ripple.style.left = x + 'px';
              ripple.style.top = y + 'px';
              ripple.style.width = '20px';
              ripple.style.height = '20px';
              ripple.style.background = 'rgba(255,255,255,0.5)';
              ripple.style.borderRadius = '50%';
              ripple.style.pointerEvents = 'none';
              ripple.style.animation = 'ripple 0.6s ease-out';
              ripple.style.zIndex = '1';

              if (window.getComputedStyle(this).position === 'static') {
                  this.style.position = 'relative';
              }
              this.appendChild(ripple);

              setTimeout(() => ripple.remove(), 600);
          });
      });

      // Add ripple animation keyframes
      const style = document.createElement('style');
      style.textContent = `
          @keyframes ripple {
              to {
                  transform: scale(4);
                  opacity: 0;
              }
          }
      `;
      document.head.appendChild(style);

      // Link hover effects
      document.querySelectorAll('a').forEach(link => {
          if (!link.classList.contains('navbar-brand')) {
              link.addEventListener('mouseenter', function() {
                  this.style.transition = 'all 0.3s ease';
              });
          }
      });

      // Social icons animation
      document.querySelectorAll('.list-group-item a').forEach(icon => {
          icon.addEventListener('mouseenter', function() {
              this.style.transform = 'translateY(-3px) scale(1.1)';
          });

          icon.addEventListener('mouseleave', function() {
              this.style.transform = 'translateY(0) scale(1)';
          });
      });

      // Parallax effect for floating icons
      if (window.innerWidth >= 992) {
          document.addEventListener('mousemove', function(e) {
              const showcase = document.querySelector('.auth-showcase');
              if (!showcase) return;

              const cards = document.querySelectorAll('.feature-card-mini');
              const mouseX = (e.clientX / window.innerWidth) - 0.5;
              const mouseY = (e.clientY / window.innerHeight) - 0.5;

              // Move cards with parallax
              cards.forEach((card, index) => {
                  const speed = (index + 1) * 3;
                  card.style.transform = `translateX(${mouseX * speed}px) translateY(${mouseY * speed}px)`;
              });

              // Move blobs with parallax
              document.querySelectorAll('[class^="blob"]').forEach((blob, index) => {
                  const speed = (index + 1) * 2;
                  blob.style.transform = `translate(${mouseX * speed * 10}px, ${mouseY * speed * 10}px)`;
              });
          });
      }

      // Hover effect on icons
      document.querySelectorAll('.floating-icon').forEach(icon => {
          icon.addEventListener('mouseenter', function() {
              this.style.opacity = '1';
              this.style.filter = 'drop-shadow(0 8px 20px rgba(33, 150, 243, 0.4)) scale(1.1)';
              this.style.transition = 'all 0.3s ease';
          });

          icon.addEventListener('mouseleave', function() {
              this.style.opacity = '0.85';
              this.style.filter = 'drop-shadow(0 4px 12px rgba(33, 150, 243, 0.2)) scale(1)';
          });
      });

      // Feature cards hover effects
      document.querySelectorAll('.feature-card-mini').forEach(card => {
          card.addEventListener('mouseenter', function() {
              this.style.transform = 'translateY(-8px) scale(1.05)';
              this.style.boxShadow = '0 20px 50px rgba(33, 150, 243, 0.3)';
          });

          card.addEventListener('mouseleave', function() {
              this.style.transform = 'translateY(0) scale(1)';
              this.style.boxShadow = '0 8px 24px rgba(0, 0, 0, 0.08)';
          });
      });

      // Smooth parallax for mobile
      document.addEventListener('deviceorientation', function(event) {
          if (window.innerWidth < 992) return;

          const beta = event.beta;
          const gamma = event.gamma;

          const cards = document.querySelectorAll('.feature-card-mini');
          cards.forEach((card) => {
              card.style.transform = `translateX(${(gamma / 90) * 20}px) translateY(${(beta / 180) * 20}px)`;
          });
      });
    </script>
</body>

</html>
