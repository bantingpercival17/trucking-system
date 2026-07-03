
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Forgot Password</title>

    <!-- USE SAME CSS AS LOGIN -->
    <link rel="stylesheet" href="{{ asset('assets/css/core/libs.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/gigz.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/custom.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/vendor/@fortawesome/fontawesome-free/css/all.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
    :root {
      --bg1: #e3f2fd;
      --bg2: #f3e5f5;
    }
    
    
    .auth-center-wrapper {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}

/* Make showcase full background */

/* Keep login always visible */
.auth-center-card {
  position: relative;
  z-index: 10;
  width: 100%;
  max-width: 420px;
  padding: 1rem;
}

@media (max-width: 991px) {

  /* Hide only heavy visuals */
  .blob-1, .blob-2, .blob-3, .blob-4,
  .floating-icon,
  .feature-card-mini,
  .mesh-background,
  .deco-circle-top,
  .deco-shape-bottom {
    display: none;
  }

  /* Keep simple clean background */
  .auth-showcase {
    background: linear-gradient(135deg, #e3f2fd, #f3e5f5);
  }

  /* Adjust card spacing */
  .auth-center-card {
    max-width: 100%;
    padding: 1rem;
  }
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

    .auth-padding { padding: 2rem !important; }

    .form-label {
      color: #1976d2 !important;
      font-weight: 600;
    }

    .form-control {
      background: rgba(255,255,255,.7) !important;
      border: 1.5px solid rgba(33,150,243,.2) !important;
      color: #333 !important;
      border-radius: 10px !important;
      transition: all 0.3s ease !important;
    }

    .form-control:focus {
      background: rgba(255,255,255,.95) !important;
      border-color: #1976d2 !important;
      box-shadow: 0 0 0 3px rgba(33,150,243,.1) !important;
      color: #333 !important;
    }

    .form-control::placeholder { color: rgba(100,100,100,.4) !important; }

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

    .btn-primary:active { transform: translateY(0); }

    h2 { color: #1976d2 !important; font-weight: 700 !important; }

    .text-center p { color: #666 !important; }

    a {
      color: #1976d2;
      transition: all 0.3s ease;
    }

    a:hover {
      color: #1565c0;
      text-decoration: underline !important;
    }

    .text-underline { font-weight: 600; }

    .logo-title { color: #1976d2 !important; font-weight: 700 !important; }

    .navbar-brand { transition: all 0.3s ease; }
    .navbar-brand:hover .logo-title { color: #1565c0 !important; }

    .alert-danger {
      background: rgba(244,67,54,.1) !important;
      border: 1px solid rgba(244,67,54,.2) !important;
      color: #d32f2f !important;
      border-radius: 10px !important;
    }

    .auth-shap { opacity: 0.3; }

    /* Social icons styling */
    .list-group-item { background: transparent !important; border: none !important; }

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

    /* Gradient Mesh & Icons Section */
    .auth-showcase {
      position: absolute;   /* KEEP THIS */
  inset: 0;
  z-index: 1;

  display: flex;
  align-items: center;
  justify-content: center;
  overflow: hidden;

  background: linear-gradient(
    135deg,
    rgba(227, 242, 253, 0.5),
    rgba(243, 229, 245, 0.3)
    );
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
      width: 300px; height: 300px;
      background: linear-gradient(135deg, #2196f3, #1976d2);
      top: -50px; left: 50px;
      animation-delay: 0s;
    }

    .blob-2 {
      width: 250px; height: 250px;
      background: linear-gradient(135deg, #1976d2, #7b60e7);
      bottom: 100px; right: 50px;
      animation-delay: 2s;
    }

    .blob-3 {
      width: 200px; height: 200px;
      background: linear-gradient(135deg, #7b60e7, #2196f3);
      top: 50%; right: 10%;
      animation-delay: 4s;
    }

    .blob-4 {
      width: 180px; height: 180px;
      background: linear-gradient(135deg, rgba(76,175,80,0.8), #2196f3);
      bottom: 10%; left: 5%;
      animation-delay: 6s;
    }

    @keyframes blob-float {
      0%, 100% { transform: translate(0, 0) scale(1); }
      33% { transform: translate(30px, -30px) scale(1.1); }
      66% { transform: translate(-20px, 20px) scale(0.95); }
    }

    .mesh-background {
      position: absolute;
      top: 0; left: 0;
      width: 100%; height: 100%;
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
      0% { opacity: 0; transform: translateY(30px) scale(0.8); }
      100% { opacity: 0.85; transform: translateY(0) scale(1); }
    }

    @keyframes float { 0%, 100% { transform: translateY(0px) rotate(0deg); } 50% { transform: translateY(-20px) rotate(3deg); } }
    @keyframes float-slow { 0%, 100% { transform: translateY(0px) rotate(0deg); } 50% { transform: translateY(-15px) rotate(-2deg); } }
    @keyframes float-slower { 0%, 100% { transform: translateY(0px) rotate(0deg); } 50% { transform: translateY(-25px) rotate(2deg); } }

    .icon-truck { top: 10%; left: 15%; color: #2196f3; animation: float 4s ease-in-out infinite; animation-delay: 0.5s; }
    .icon-driver { top: 45%; right: 10%; color: #1976d2; animation: float-slow 5s ease-in-out infinite; animation-delay: 1s; }
    .icon-location { top: 15%; right: 20%; color: #1565c0; animation: float-slower 6s ease-in-out infinite; animation-delay: 0.2s; }
    .icon-clipboard { bottom: 15%; left: 10%; color: #2196f3; animation: float 4.5s ease-in-out infinite; animation-delay: 1.5s; }
    .icon-check { bottom: 25%; right: 15%; color: #4caf50; animation: float-slow 5.5s ease-in-out infinite; animation-delay: 0.8s; }
    .icon-zap { top: 60%; left: 5%; color: #ffc107; animation: float-slower 7s ease-in-out infinite; animation-delay: 1.2s; }

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
      0% { opacity: 0; transform: translateY(20px) scale(0.9); }
      100% { opacity: 1; transform: translateY(0) scale(1); }
    }

    .feature-card-mini:hover {
      transform: translateY(-8px);
      box-shadow: 0 16px 40px rgba(33, 150, 243, 0.2);
      border-color: rgba(33, 150, 243, 0.3);
      background: rgba(255, 255, 255, 0.98);
    }

    .card-icon { font-size: 2rem; margin-bottom: 0.5rem; line-height: 1; }
    .card-title { font-size: 0.85rem; color: #1976d2; font-weight: 600; margin-bottom: 0.3rem; }
    .card-text { font-size: 0.75rem; color: #999; line-height: 1.3; }

    /* Top decorative circle */
    .deco-circle-top {
      position: absolute;
      top: 5%; right: 15%;
      width: 80px; height: 80px;
      border: 2px solid rgba(33, 150, 243, 0.2);
      border-radius: 50%;
      animation: rotate 20s linear infinite;
      z-index: 5;
    }

    /* Bottom decorative shapes */
    .deco-shape-bottom {
      position: absolute;
      bottom: 10%; left: 8%;
      width: 60px; height: 60px;
      background: rgba(33, 150, 243, 0.05);
      border: 2px solid rgba(33, 150, 243, 0.15);
      transform: rotate(45deg);
      animation: rotate-reverse 15s linear infinite;
      z-index: 5;
    }

    @keyframes rotate { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    @keyframes rotate-reverse { 0% { transform: rotate(45deg); } 100% { transform: rotate(405deg); } }

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
    
    .auth-center-wrapper {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
}

.auth-center-card {
  position: relative;
  z-index: 50;
  width: 100%;
  max-width: 420px;
}

/* FLOAT POSITIONS (4 SIDES) */
.icon-truck { top: 5%; left: 10%; }
.icon-location { top: 5%; right: 10%; }
.icon-clipboard { bottom: 8%; left: 12%; }
.icon-check { bottom: 8%; right: 12%; }

    @keyframes fade-in-up {
      from { opacity: 0; transform: translateY(20px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @media (max-width: 991px) {
      .auth-showcase { display: none; }
    }
  </style>
</head>

<body>

<div class="auth-center-wrapper">

    <!-- SAME BACKGROUND -->
    <div class="auth-showcase">
        <div class="blob-1"></div>
        <div class="blob-2"></div>
        <div class="blob-3"></div>
        <div class="blob-4"></div>

        <div class="mesh-background"></div>

        <!-- floating icons -->
        <div class="floating-icon icon-truck"><i class="bi bi-truck-front"></i></div>
        <div class="floating-icon icon-location"><i class="bi bi-geo-alt"></i></div>
        <div class="floating-icon icon-clipboard"><i class="bi bi-clipboard-check"></i></div>
        <div class="floating-icon icon-check"><i class="bi bi-check-circle"></i></div>
    </div>

    <!-- CENTER CARD -->
    <div class="auth-center-card">
        <div class="card-body auth-padding">

            <!-- LOGO -->
            <a href="{{ url('/') }}" class="navbar-brand d-flex align-items-center gap-2 mb-3">
                <span class="bg-white text-primary rounded-3 px-2 py-1 fw-bold"
                    style="box-shadow: 0 2px 10px rgba(25,118,210,.18);">
                    <i class="bi bi-truck-front"></i>
                </span>
                <h2 class="logo-title m-0 fs-4">
                    {{ config('app.name', 'Trucking System') }}
                </h2>
            </a>

            <!-- TITLE -->
            <h2 class="text-center mb-2">Forgot Password</h2>
            <p class="text-center mb-4">
                Enter your email and we’ll send you a reset link.
            </p>

            <!-- SESSION STATUS -->
            @if (session('status'))
                <div class="alert alert-success text-center">
                    {{ session('status') }}
                </div>
            @endif

            <!-- FORM -->
            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="form-group mb-3">
                    <label class="form-label">Email Address</label>
                    <input 
                        type="email" 
                        name="email" 
                        class="form-control"
                        value="{{ old('email') }}" 
                        required 
                        autofocus
                        placeholder="Enter your email"
                    >
                </div>

                @error('email')
                    <div class="alert alert-danger">
                        {{ $message }}
                    </div>
                @enderror

                <div class="text-center">
                    <button type="submit" class="btn btn-primary w-100">
                        Send Reset Link
                    </button>
                </div>

                <div class="text-center mt-3">
                    <a href="{{ route('login') }}">
                        ← Back to Login
                    </a>
                </div>

            </form>
        </div>
    </div>

</div>

<!-- SAME JS AS LOGIN -->
<script src="{{ asset('assets/js/core/libs.min.js') }}"></script>
<script src="{{ asset('assets/js/gigz.js') }}"></script>

</body>
</html>