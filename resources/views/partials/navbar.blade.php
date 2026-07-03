@php
    $layout = session('layout', 'owner');
    $isFlash = $layout === 'flash';

    $dashboardRoute = $isFlash ? 'flash.dashboard' : 'dashboard';
@endphp

<nav class="nav navbar navbar-expand-lg navbar-light iq-navbar py-lg-0">
    <div class="container-fluid navbar-inner">

        {{-- Brand --}}
        <a href="{{ route($dashboardRoute) }}" class="navbar-brand">
            <svg width="80" height="26" viewBox="0 0 80 26" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path
                    d="M17.4453 8.66406H21.875C21.1367 3.95312 17.0586 0.671874 11.75 0.671874C5.46875 0.671874 0.757813 5.28906 0.757813 13.0234C0.757813 20.6172 5.25781 25.3281 11.8789 25.3281C17.8203 25.3281 22.0742 21.5078 22.0742 15.3203V12.4375H12.3359V15.8359H17.8672C17.7969 19.2578 15.5117 21.4258 11.9023 21.4258C7.88281 21.4258 5.12891 18.4141 5.12891 12.9766C5.12891 7.57422 7.92969 4.57422 11.8086 4.57422C14.7031 4.57422 16.6719 6.12109 17.4453 8.66406Z"
                    fill="#1C1F34" />
            </svg>
        </a>

        {{-- Sidebar Toggle --}}
        <div class="sidebar-toggle" data-toggle="sidebar" data-active="true">
            <i class="icon">
                <svg width="20" height="20" viewBox="0 0 24 24">
                    <path fill="currentColor"
                        d="M4,11V13H16L10.5,18.5L11.92,19.92L19.84,12L11.92,4.08L10.5,5.5L16,11H4Z" />
                </svg>
            </i>
        </div>

        {{-- Mobile Toggle --}}
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarSupportedContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        {{-- Menu --}}
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto align-items-center">

                @php
                    $layout = session('layout', 'default');
                    $label = match ($layout) {
                        'flash' => 'Flash',
                        'watson' => 'Watson',
                        default => 'Chamonix',
                    };
                @endphp

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="layoutDropdown" role="button"
                        data-bs-toggle="dropdown">
                        Layout: {{ $label }}
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('layout.switch', 'default') }}">
                                Chamonix
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('layout.switch', 'flash') }}">
                                Flash
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('layout.switch', 'watson') }}">
                                Watson
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- User --}}
                @auth
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="{{ route('logout') }}"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>

                @endauth

            </ul>
        </div>
    </div>
</nav>
