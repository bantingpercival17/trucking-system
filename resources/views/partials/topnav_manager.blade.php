@php
    $role = auth()->user()->role ?? null;

    $isOps = $role === 'operations_manager';
    $isBill = $role === 'billing_manager';

    $opsActive = request()->routeIs('owner.trips.*', 'owner.trucks.*', 'owner.drivers.*', 'owner.destinations.*');
    $billActive = request()->routeIs('owner.payroll.*');
@endphp

<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-4" style="z-index:1030;">
    {{-- Brand --}}
    <a class="navbar-brand fw-bold me-3" href="{{ route('dashboard') }}">
        Trucking
    </a>

    {{-- Mobile toggle --}}
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#managerNav"
        aria-controls="managerNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="managerNav">
        {{-- Right side --}}
        <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-3">

            {{-- Dashboard --}}
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active fw-semibold' : '' }}"
                    href="{{ route('dashboard') }}">
                    Dashboard
                </a>
            </li>

            {{-- Operations Dropdown (floating, no navbar resize) --}}
            @if ($isOps)
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="opsDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Operations
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="opsDropdown">
                        <li><a class="dropdown-item" href="{{ route('owner.trips.index') }}">Trips / Dispatch</a></li>
                        <li><a class="dropdown-item" href="{{ route('owner.trucks.index') }}">Trucks</a></li>
                        <li><a class="dropdown-item" href="{{ route('owner.drivers.index') }}">Drivers & Crew</a></li>
                        <li><a class="dropdown-item" href="{{ route('owner.destinations.index') }}">Destinations</a>
                        </li>
                    </ul>
                </li>
            @endif

            {{-- Billing/Finance Dropdown (floating, no navbar resize) --}}
            @if ($isBill)
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="billDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Finance
                    </a>


                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="billDropdown">
                        <li><a class="dropdown-item {{ request()->routeIs('owner.payroll.index') ? 'active' : '' }}"
                                href="{{ route('owner.payroll.index') }}">Payroll</a></li>
                        <li><a class="dropdown-item {{ request()->routeIs('owner.payroll.history') ? 'active' : '' }}"
                                href="{{ route('owner.payroll.history') }}">Payroll History</a></li>
                        <li><a class="dropdown-item" href="#">Expenses</a></li>
                    </ul>
                </li>
            @endif

            {{-- Logout Icon --}}
            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}" class="d-flex">
                    @csrf
                    <button type="submit" class="nav-link border-0 bg-transparent text-danger p-0 ms-lg-2"
                        title="Logout">
                        <i class="bi bi-box-arrow-right fs-5"></i>
                    </button>
                </form>
            </li>

        </ul>
    </div>
</nav>

{{-- Optional: small dropdown spacing polish --}}
<style>
    /* Keep navbar clickable and above any overlays */
    .navbar {
        position: sticky;
        top: 0;
        z-index: 99999 !important;
    }

    /* prevent parent containers from clipping dropdown */
    .navbar,
    .navbar .collapse,
    .navbar-nav {
        overflow: visible !important;
    }

    .dropdown-menu {
        z-index: 999999 !important;
    }

    /* If any overlay exists, it should not steal clicks */
    #loading,
    .loader,
    .dropdown-backdrop {
        pointer-events: none !important;
    }
</style>
