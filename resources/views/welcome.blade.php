<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Trucking System') }}</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Optional icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        :root{
            --bg1:#e3f2fd;
            --bg2:#f3e5f5;
        }
        body{
            min-height: 100vh;
            background:
                radial-gradient(900px 500px at 10% 10%, rgba(100,181,246,.15), transparent 60%),
                radial-gradient(900px 500px at 90% 20%, rgba(186,104,200,.12), transparent 60%),
                linear-gradient(135deg, var(--bg1), var(--bg2));
        }
        .glass{
            background: rgba(255,255,255,.97);
            border: 1px solid rgba(100,181,246,.20);
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(0,0,0,.08);
            backdrop-filter: blur(10px);
        }
        .glass-dark{
            background: rgba(255,255,255,.92);
            border: 1px solid rgba(100,181,246,.15);
            border-radius: 16px;
            backdrop-filter: blur(10px);
            color: #333 !important;
        }
        .glass-dark .text-white-50 { color: #666 !important; }
        .glass-dark .text-white { color: #222 !important; }

        .hero-badge{
            font-size: .85rem;
            border: 1px solid rgba(100,181,246,.30);
            background: rgba(100,181,246,.10);
            color: #1976d2;
        }
        .feature-card{
            border: 1px solid rgba(100,181,246,.15);
            border-radius: 16px;
            transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
            background: linear-gradient(135deg, rgba(255,255,255,.98) 0%, rgba(227,242,253,.5) 100%);
        }
        .feature-card:hover{
            transform: translateY(-6px);
            box-shadow: 0 16px 40px rgba(25,118,210,.12);
            border-color: rgba(25,118,210,.25);
        }
        .icon-pill{
            width: 44px;
            height: 44px;
            display: grid;
            place-items: center;
            border-radius: 12px;
            background: rgba(100,181,246,.15);
            color: #1976d2;
            transition: all .3s ease;
        }
        .stat{
            border: 1px solid rgba(100,181,246,.15);
            border-radius: 14px;
            background: rgba(255,255,255,.85);
        }
        .nav-link.btnlike{
            padding: .5rem .9rem;
            border-radius: 10px;
            transition: all .3s ease;
        }

        /* Button Enhancements */
        .btn {
            transition: all .3s cubic-bezier(0.4, 0, 0.2, 1);
            font-weight: 600;
            letter-spacing: 0.3px;
            position: relative;
            overflow: hidden;
        }
        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255,255,255,.3);
            transform: translate(-50%, -50%);
            transition: width .6s, height .6s;
        }
        .btn:hover::before { width: 300px; height: 300px; }

        .btn-primary {
            background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%);
            border: none;
            box-shadow: 0 4px 15px rgba(33,150,243,.3);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(25,118,210,.4);
        }
        .btn-outline-primary {
            color: #1976d2;
            border: 2px solid #1976d2;
            background: rgba(33,150,243,.05);
        }
        .btn-outline-primary:hover {
            background: #1976d2;
            color: white;
            border-color: #1976d2;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(25,118,210,.3);
        }
        .btn-outline-secondary {
            color: #666;
            border-color: rgba(0,0,0,.2);
            background: rgba(0,0,0,.02);
        }
        .btn-outline-secondary:hover {
            background: #f5f5f5;
            border-color: #666;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,.1);
        }

        html { scroll-behavior: smooth; }
        .text-white-50 { color: rgba(255,255,255,.7) !important; }

        /* ========= SCROLL TRANSITIONS ========= */
        .reveal {
            opacity: 0;
            transform: translateY(24px);
            transition: opacity .85s ease, transform .85s ease;
            will-change: opacity, transform;
        }
        .reveal.show {
            opacity: 1;
            transform: translateY(0);
        }

        /* optional variants */
        .reveal-left { transform: translateX(-30px); }
        .reveal-right { transform: translateX(30px); }
        .reveal-left.show, .reveal-right.show { transform: translateX(0); }

        /* stagger children */
        .stagger > * {
            opacity: 0;
            transform: translateY(18px);
            transition: opacity .75s ease, transform .75s ease;
        }
        .stagger.show > * {
            opacity: 1;
            transform: translateY(0);
        }
        .stagger.show > *:nth-child(1){ transition-delay: .05s; }
        .stagger.show > *:nth-child(2){ transition-delay: .12s; }
        .stagger.show > *:nth-child(3){ transition-delay: .19s; }
        .stagger.show > *:nth-child(4){ transition-delay: .26s; }
        /* ===================================== */
    </style>
</head>

<body class="d-flex flex-column">

    <!-- HEADER / NAVBAR -->
    <header class="container py-3 reveal reveal-left">
        <nav class="navbar navbar-expand-lg glass px-3 px-lg-4">
            <a class="navbar-brand text-primary fw-bold d-flex align-items-center gap-2" href="{{ url('/') }}">
                <span class="bg-white text-primary rounded-3 px-2 py-1 fw-bold" style="box-shadow: 0 2px 8px rgba(25,118,210,.2);">🚚</span>
                <span>{{ config('app.name', 'Trucking System') }}</span>
            </a>

            <button class="navbar-toggler border-0 text-primary" type="button" data-bs-toggle="collapse" data-bs-target="#topNav">
                <span class="navbar-toggler-icon" style="filter: invert(0.3);"></span>
            </button>

            <div class="collapse navbar-collapse" id="topNav">
                <ul class="navbar-nav ms-auto me-lg-3 mt-3 mt-lg-0"></ul>

                <div class="d-flex gap-2">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-sm fw-semibold">
                                <i class="bi bi-speedometer2 me-1"></i> Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm fw-semibold">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Login
                            </a>

                            <!--@if (Route::has('register'))-->
                            <!--    <a href="{{ route('register') }}" class="btn btn-primary btn-sm fw-semibold">-->
                            <!--        <i class="bi bi-person-plus me-1"></i> Register-->
                            <!--    </a>-->
                            <!--@endif-->
                        @endauth
                    @endif
                </div>
            </div>
        </nav>
    </header>

    <!-- MAIN -->
    <main class="container flex-grow-1 pb-5">

        <!-- HERO -->
        <section class="row justify-content-center g-4 mt-2 reveal">
            <div class="col-lg-11">
                <div class="glass p-4 p-md-5">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-7 reveal reveal-left">
                            <span class="badge rounded-pill text-white hero-badge px-3 py-2 mb-3" style="background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%);">
                                <i class="bi bi-shield-check me-1"></i> Secure • Organized • Fast Dispatch Tracking
                            </span>

                            <h1 class="display-6 fw-bold mb-3" style="color: #1976d2;">
                                Manage dispatch trips, trucks, drivers, and destinations — in one system.
                            </h1>
                            <p class="text-muted fs-6 mb-4" style="color: #666;">
                                Less manual tracking, fewer mistakes, and clearer reporting.
                                Perfect for daily trucking operations.
                            </p>

                            <div class="d-flex gap-2 flex-wrap">
                                @guest
                                    <a href="{{ route('login') }}" class="btn btn-primary btn-lg fw-semibold">
                                        <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
                                    </a>
                                    <!--@if (Route::has('register'))-->
                                    <!--    <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg fw-semibold">-->
                                    <!--        <i class="bi bi-person-plus me-1"></i> Create Account-->
                                    <!--    </a>-->
                                    <!--@endif-->
                                @else
                                    <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-lg fw-semibold">
                                        <i class="bi bi-speedometer2 me-1"></i> Go to Dashboard
                                    </a>
                                @endguest

                                <a href="#features" class="btn btn-outline-secondary btn-lg fw-semibold">
                                    <i class="bi bi-stars me-1"></i> See Features
                                </a>
                            </div>

                            <!-- quick stats -->
                            <div class="row g-3 mt-4 stagger">
                                <div class="col-6 col-md-4">
                                    <div class="p-3 text-white rounded-4" style="background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%); box-shadow: 0 4px 15px rgba(33,150,243,.2);">
                                        <div class="fw-bold">Dispatch</div>
                                        <small class="opacity-75">Trip tickets & logs</small>
                                    </div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <div class="p-3 text-white rounded-4" style="background: linear-gradient(135deg, #424242 0%, #212121 100%); box-shadow: 0 4px 15px rgba(0,0,0,.15);">
                                        <div class="fw-bold">Monitoring</div>
                                        <small class="opacity-75">Status & activity</small>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <div class="p-3 text-white rounded-4" style="background: linear-gradient(135deg, #4caf50 0%, #388e3c 100%); box-shadow: 0 4px 15px rgba(76,175,80,.2);">
                                        <div class="fw-bold">Reporting</div>
                                        <small class="opacity-75">Cleaner audit trail</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- right panel -->
                        <div class="col-lg-5 reveal reveal-right">
                            <div class="p-4 rounded-4 bg-white border" style="border-color: rgba(33,150,243,.15); box-shadow: 0 4px 15px rgba(33,150,243,.1);">
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div class="fw-bold" style="color: #1976d2;"><i class="bi bi-clipboard2-check me-1"></i> Quick Overview</div>
                                    <span class="badge bg-info text-white">Live-ready</span>
                                </div>

                                <div class="d-grid gap-3">
                                    <div class="d-flex gap-3 align-items-start">
                                        <div class="icon-pill"><i class="bi bi-truck-front"></i></div>
                                        <div>
                                            <div class="fw-semibold" style="color: #1976d2;">Truck Records</div>
                                            <small class="text-muted">Track units, types, and availability.</small>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-3 align-items-start">
                                        <div class="icon-pill"><i class="bi bi-person-badge"></i></div>
                                        <div>
                                            <div class="fw-semibold" style="color: #1976d2;">Driver & Helper</div>
                                            <small class="text-muted">Manage people, status, and assignments.</small>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-3 align-items-start">
                                        <div class="icon-pill"><i class="bi bi-geo-alt"></i></div>
                                        <div>
                                            <div class="fw-semibold" style="color: #1976d2;">Destinations & Rates</div>
                                            <small class="text-muted">Store locations, areas, and pricing.</small>
                                        </div>
                                    </div>

                                    <div class="d-flex gap-3 align-items-start">
                                        <div class="icon-pill"><i class="bi bi-journal-text"></i></div>
                                        <div>
                                            <div class="fw-semibold" style="color: #1976d2;">Audit Trail</div>
                                            <small class="text-muted">Keep notes, history, and updates.</small>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <div class="fw-semibold" style="color: #1976d2;">Ready to start?</div>
                                        <small class="text-muted">Login to access your dashboard.</small>
                                    </div>
                                    @guest
                                        <a href="{{ route('login') }}" class="btn btn-primary fw-semibold btn-sm">
                                            Login
                                        </a>
                                    @else
                                        <a href="{{ url('/dashboard') }}" class="btn btn-primary fw-semibold btn-sm">
                                            Dashboard
                                        </a>
                                    @endguest
                                </div>
                            </div>

                            <div class="mt-3 glass-dark p-3 stat" style="background: rgba(227,242,253,.6);">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="fw-bold" style="color: #1976d2;">💡 Pro Tip</div>
                                        <small style="color: #666;">Use statuses to keep your list clean (Active/Inactive).</small>
                                    </div>
                                    <i class="bi bi-lightbulb fs-3" style="color: #ffc107;"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- FEATURES -->
        <section id="features" class="row justify-content-center mt-4 reveal">
            <div class="col-lg-11">
                <div class="mt-4 mb-3 reveal reveal-left">
                    <h2 class="fw-bold mb-1" style="color: #1976d2;">✨ Features</h2>
                    <p class="mb-0" style="color: #666;">Everything you need for basic dispatch operations. Click any feature to see more details.</p>
                </div>

                <div class="row g-3 stagger">
                    <div class="col-md-6 col-lg-3">
                        <div class="feature-card bg-white p-4 h-100" data-bs-toggle="collapse" href="#feature1">
                            <div class="icon-pill mb-3"><i class="bi bi-receipt"></i></div>
                            <div class="fw-bold" style="color: #1976d2;">Trip Tickets</div>
                            <small class="text-muted d-block mb-2">Create, update, and track dispatch trips.</small>
                            <div class="collapse mt-3" id="feature1">
                                <small class="text-muted d-block">
                                    Full trip management with real-time status updates, driver assignments, and destination tracking. Generate reports on-the-fly.
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="feature-card bg-white p-4 h-100" data-bs-toggle="collapse" href="#feature2">
                            <div class="icon-pill mb-3"><i class="bi bi-people"></i></div>
                            <div class="fw-bold" style="color: #1976d2;">People Management</div>
                            <small class="text-muted d-block mb-2">Drivers & helpers with status control.</small>
                            <div class="collapse mt-3" id="feature2">
                                <small class="text-muted d-block">
                                    Easily manage driver and helper profiles, track availability, assign roles, and monitor active status across all operations.
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="feature-card bg-white p-4 h-100" data-bs-toggle="collapse" href="#feature3">
                            <div class="icon-pill mb-3"><i class="bi bi-truck"></i></div>
                            <div class="fw-bold" style="color: #1976d2;">Truck Monitoring</div>
                            <small class="text-muted d-block mb-2">Track truck types, usage, and condition.</small>
                            <div class="collapse mt-3" id="feature3">
                                <small class="text-muted d-block">
                                    Monitor truck availability, maintenance schedules, fuel usage, and maintain inventory of all vehicles in your fleet.
                                </small>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-lg-3">
                        <div class="feature-card bg-white p-4 h-100" data-bs-toggle="collapse" href="#feature4">
                            <div class="icon-pill mb-3"><i class="bi bi-graph-up-arrow"></i></div>
                            <div class="fw-bold" style="color: #1976d2;">Better Reporting</div>
                            <small class="text-muted d-block mb-2">Consistent logs for easier summaries.</small>
                            <div class="collapse mt-3" id="feature4">
                                <small class="text-muted d-block">
                                    Generate comprehensive reports with audit trails, performance metrics, and historical data for analysis and compliance.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- HOW IT WORKS -->
        <section id="how" class="row justify-content-center mt-5 reveal">
            <div class="col-lg-11">
                <div class="glass p-4 p-md-5">
                    <div class="row g-4">
                        <div class="col-md-4 reveal reveal-left">
                            <h3 class="fw-bold mb-2" style="color: #1976d2;">🚀 How it works</h3>
                            <p class="text-muted mb-0" style="color: #666;">Simple flow for your operations.</p>
                        </div>
                        <div class="col-md-8 stagger">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="border rounded-4 p-3 h-100" data-bs-toggle="collapse" href="#step1" style="border-color: rgba(33,150,243,.2); background: rgba(227,242,253,.3);">
                                        <div class="fw-bold" style="color: #1976d2;"><i class="bi bi-1-circle me-1"></i> Setup</div>
                                        <small class="text-muted">Add trucks, drivers, helpers, destinations.</small>
                                        <div class="collapse mt-2" id="step1">
                                            <hr class="my-2">
                                            <small class="text-muted d-block">Get started by entering all your assets and team members into the system.</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded-4 p-3 h-100" data-bs-toggle="collapse" href="#step2" style="border-color: rgba(33,150,243,.2); background: rgba(227,242,253,.3);">
                                        <div class="fw-bold" style="color: #1976d2;"><i class="bi bi-2-circle me-1"></i> Dispatch</div>
                                        <small class="text-muted">Create trip tickets and assign people.</small>
                                        <div class="collapse mt-2" id="step2">
                                            <hr class="my-2">
                                            <small class="text-muted d-block">Create trips, assign teams, and set destinations and schedules.</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="border rounded-4 p-3 h-100" data-bs-toggle="collapse" href="#step3" style="border-color: rgba(33,150,243,.2); background: rgba(227,242,253,.3);">
                                        <div class="fw-bold" style="color: #1976d2;"><i class="bi bi-3-circle me-1"></i> Track</div>
                                        <small class="text-muted">Monitor status and keep audit notes.</small>
                                        <div class="collapse mt-2" id="step3">
                                            <hr class="my-2">
                                            <small class="text-muted d-block">Follow progress in real-time and maintain detailed records of every trip.</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- ABOUT / FOOTER CARD -->
        <section id="about" class="row justify-content-center mt-4 reveal">
            <div class="col-lg-11">
                <div class="glass p-4 p-md-5" style="background: linear-gradient(135deg, rgba(255,255,255,.95) 0%, rgba(227,242,253,.4) 100%); border: 1px solid rgba(33,150,243,.15);">
                    <div class="row g-3 align-items-center">
                        <div class="col-md-8 reveal reveal-left">
                            <h4 class="fw-bold mb-2" style="color: #1976d2;">ℹ️ About this system</h4>
                            <p class="mb-0" style="color: #666;">
                                A simple trucking/dispatch management web app built with Laravel + MySQL.
                                Designed to be clean, fast, and easy to use.
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end reveal reveal-right">
                            @guest
                                <a href="{{ route('login') }}" class="btn btn-primary fw-semibold">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Login Now
                                </a>
                            @else
                                <a href="{{ url('/dashboard') }}" class="btn btn-primary fw-semibold">
                                    <i class="bi bi-speedometer2 me-1"></i> Open Dashboard
                                </a>
                            @endguest
                        </div>
                    </div>
                </div>

                <p class="text-center reveal" style="color: #999; margin-top: 2rem; margin-bottom: 0;">
                    © {{ date('Y') }} {{ config('app.name', 'Trucking System') }} • Built with Laravel & Bootstrap
                </p>
            </div>
        </section>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Add hover cursor to interactive elements
        document.querySelectorAll('.feature-card, [data-bs-toggle="collapse"]').forEach(card => {
            card.style.cursor = 'pointer';
        });

        // Smooth scroll for anchors
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                if (href !== '#' && document.querySelector(href)) {
                    e.preventDefault();
                    document.querySelector(href).scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        });

        // Button ripple effect
        document.querySelectorAll('.btn').forEach(button => {
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

                if (window.getComputedStyle(this).position === 'static') {
                    this.style.position = 'relative';
                }
                this.appendChild(ripple);

                setTimeout(() => ripple.remove(), 600);
            });
        });

        const rippleStyle = document.createElement('style');
        rippleStyle.textContent = `
            @keyframes ripple {
                to { transform: scale(4); opacity: 0; }
            }
        `;
        document.head.appendChild(rippleStyle);

        // ========= SCROLL REVEAL (SHOW + HIDE) =========
        // Also treat stagger containers as reveal blocks
        document.querySelectorAll('.stagger').forEach(el => el.classList.add('reveal'));

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                const el = entry.target;

                if (entry.isIntersecting) {
                    el.classList.add('show');

                    // if it's a stagger container, keep show so children animate
                    if (el.classList.contains('stagger')) {
                        el.classList.add('show');
                    }
                } else {
                    // ✅ hide again when going up (or leaving view)
                    el.classList.remove('show');
                }
            });
        }, {
            threshold: 0.15,
            rootMargin: "0px 0px -10% 0px"
        });

        document.querySelectorAll('.reveal, .stagger').forEach(el => observer.observe(el));
        // =============================================
    </script>
</body>
</html>
