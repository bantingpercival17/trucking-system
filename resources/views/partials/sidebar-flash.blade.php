@php
    $role = auth()->user()->role;

    function hasRole($roles)
    {
        return in_array(auth()->user()->role, (array) $roles);
    }

    $parts = explode(' ', auth()->user()->name);
    $first = $parts[0];
    $last = end($parts);

    $operationsOpen =
        str_contains(request()->route()->getName(), 'flash.trips') ||
        str_contains(request()->route()->getName(), 'flash.trucks') ||
        str_contains(request()->route()->getName(), 'flash.drivers') ||
        str_contains(request()->route()->getName(), 'flash.destinations');

@endphp

<aside class="sidebar sidebar-default navs-rounded-all">
    <div class="sidebar-header d-flex align-items-center justify-content-center">
        <a href="{{ route('dashboard') }}" class="navbar-brand d-flex flex-column">
            <span class="fw-bold">{{ $first }}</span>
            <span class="small text-muted">FLASH</span>
        </a>

        <div class="sidebar-toggle d-xl-none" data-toggle="sidebar" data-active="true">
            <i class="icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                    xmlns="http://www.w3.org/2000/svg">
                    <path d="M4.25 12.2744L19.25 12.2744" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M10.2998 18.2988L4.2498 12.2748L10.2998 6.24976" stroke="currentColor" stroke-width="1.5"
                        stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </i>
        </div>
    </div>

    <div class="sidebar-body pt-0 data-scrollbar">
        <div class="sidebar-list">
            <!-- Sidebar Menu Start -->
            <ul class="navbar-nav iq-main-menu" id="sidebar-menu">

                <!-- HOME -->
                <li class="nav-item static-item">
                    <a class="nav-link static-item disabled" href="#" tabindex="-1">
                        <span class="default-icon">Home</span>
                        <span class="mini-icon">-</span>
                    </a>
                </li>

                <!-- Dashboard (single link) -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('flash.dashboard') ? 'active' : '' }}"
                        href="{{ route('flash.dashboard') }}">
                        <i class="icon">
                            <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4"
                                    d="M16.0756 2H19.4616C20.8639 2 22.0001 3.14585 22.0001 4.55996V7.97452C22.0001 9.38864 20.8639 10.5345 19.4616 10.5345H16.0756C14.6734 10.5345 13.5371 9.38864 13.5371 7.97452V4.55996C13.5371 3.14585 14.6734 2 16.0756 2Z"
                                    fill="currentColor"></path>
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M4.53852 2H7.92449C9.32676 2 10.463 3.14585 10.463 4.55996V7.97452C10.463 9.38864 9.32676 10.5345 7.92449 10.5345H4.53852C3.13626 10.5345 2 9.38864 2 7.97452V4.55996C2 3.14585 3.13626 2 4.53852 2ZM4.53852 13.4655H7.92449C9.32676 13.4655 10.463 14.6114 10.463 16.0255V19.44C10.463 20.8532 9.32676 22 7.92449 22H4.53852C3.13626 22 2 20.8532 2 19.44V16.0255C2 14.6114 3.13626 13.4655 4.53852 13.4655ZM19.4615 13.4655H16.0755C14.6732 13.4655 13.537 14.6114 13.537 16.0255V19.44C13.537 20.8532 14.6732 22 16.0755 22H19.4615C20.8637 22 22 20.8532 22 19.44V16.0255C22 14.6114 20.8637 13.4655 19.4615 13.4655Z"
                                    fill="currentColor"></path>
                            </svg>
                        </i>
                        <span class="item-name">Dashboard</span>
                    </a>
                </li>

                <li>
                    <hr class="hr-horizontal">
                </li>
                @if (hasRole(['owner', 'it', 'admin', 'secretary']))
                    <li class="nav-item {{ $operationsOpen ? 'active' : '' }}">
                        <a class="nav-link {{ $operationsOpen ? 'active' : 'collapsed' }}" data-bs-toggle="collapse"
                            href="#sidebar-operations" role="button"
                            aria-expanded="{{ $operationsOpen ? 'true' : 'false' }}" aria-controls="sidebar-operations">

                            <i class="icon">
                                <svg width="20" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4"
                                        d="M3 6.5C3 4.57 4.57 3 6.5 3H17.5C19.43 3 21 4.57 21 6.5V7.5C21 9.43 19.43 11 17.5 11H6.5C4.57 11 3 9.43 3 7.5V6.5Z"
                                        fill="currentColor" />
                                    <path
                                        d="M3 16.5C3 14.57 4.57 13 6.5 13H17.5C19.43 13 21 14.57 21 16.5V17.5C21 19.43 19.43 21 17.5 21H6.5C4.57 21 3 19.43 3 17.5V16.5Z"
                                        fill="currentColor" />
                                </svg>
                            </i>


                            <span class="item-name">Operations</span>

                            <i class="right-icon {{ $operationsOpen ? 'rotate-90' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </i>
                        </a>

                        <ul class="sub-nav collapse {{ $operationsOpen ? 'show' : '' }}" id="sidebar-operations">

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs($prefix . '.trips.*') ? 'active' : '' }}"
                                    href="{{ route($prefix . '.trips.index') }}">
                                    <i class="icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24"
                                            fill="currentColor">
                                            <circle cx="12" cy="12" r="8" />
                                        </svg>
                                    </i>
                                    <i class="sidenav-mini-icon">T</i>
                                    <span class="item-name">Trips / Dispatch</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs($prefix . '.trucks.*') ? 'active' : '' }}"
                                    href="{{ route($prefix . '.trucks.index')}}">
                                    <i class="icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24"
                                            fill="currentColor">
                                            <circle cx="12" cy="12" r="8" />
                                        </svg>
                                    </i>
                                    <i class="sidenav-mini-icon">K</i>
                                    <span class="item-name">Trucks</span>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs($prefix . '.drivers.*') ? 'active' : '' }}"
                                    href="{{ route($prefix . '.drivers.index') }}">
                                    <i class="icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24"
                                            fill="currentColor">
                                            <circle cx="12" cy="12" r="8" />
                                        </svg>
                                    </i>
                                    <i class="sidenav-mini-icon">D</i>
                                    <span class="item-name">Drivers & Crew</span>
                                </a>
                            </li>


                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs($prefix . '.destinations.*') ? 'active' : '' }}"
                                    href="{{ route($prefix . '.destinations.index') }}">
                                    <i class="icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24"
                                            fill="currentColor">
                                            <circle cx="12" cy="12" r="8" />
                                        </svg>
                                    </i>
                                    <i class="sidenav-mini-icon">L</i>
                                    <span class="item-name">Destinations</span>
                                </a>
                            </li>

                        </ul>
                    </li>
                @endif
                <li>
                    <hr class="hr-horizontal">
                </li>
                @if (hasRole(['owner', 'it', 'admin', 'secretary']))
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="collapse" href="#sidebar-finance" role="button"
                            aria-expanded="false" aria-controls="sidebar-finance">
                            <i class="icon">
                                <svg width="20" viewBox="0 0 24 24" fill="none"
                                    xmlns="http://www.w3.org/2000/svg">
                                    <path opacity="0.4"
                                        d="M21.25 13.4764C20.429 13.4764 19.761 12.8145 19.761 12.001C19.761 11.1865 20.429 10.5246 21.25 10.5246C21.449 10.5246 21.64 10.4463 21.78 10.3076C21.921 10.1679 22 9.97864 22 9.78146L21.999 7.10415C21.999 4.84102 20.14 3 17.856 3H6.144C3.86 3 2.001 4.84102 2.001 7.10415L2 9.86766C2 10.0648 2.079 10.2541 2.22 10.3938C2.36 10.5325 2.551 10.6108 2.75 10.6108C3.599 10.6108 4.239 11.2083 4.239 12.001C4.239 12.8145 3.571 13.4764 2.75 13.4764C2.336 13.4764 2 13.8093 2 14.2195V16.8949C2 19.158 3.858 21 6.143 21H17.857C20.142 21 22 19.158 22 16.8949V14.2195C22 13.8093 21.664 13.4764 21.25 13.4764Z"
                                        fill="currentColor"></path>
                                    <path
                                        d="M15.4303 11.5887L14.2513 12.7367L14.5303 14.3597C14.5783 14.6407 14.4653 14.9177 14.2343 15.0837C14.0053 15.2517 13.7063 15.2727 13.4543 15.1387L11.9993 14.3737L10.5413 15.1397C10.4333 15.1967 10.3153 15.2267 10.1983 15.2267C10.0453 15.2267 9.89434 15.1787 9.76434 15.0847C9.53434 14.9177 9.42134 14.6407 9.46934 14.3597L9.74734 12.7367L8.56834 11.5887C8.36434 11.3907 8.29334 11.0997 8.38134 10.8287C8.47034 10.5587 8.70034 10.3667 8.98134 10.3267L10.6073 10.0897L11.3363 8.61268C11.4633 8.35868 11.7173 8.20068 11.9993 8.20068H12.0013C12.2843 8.20168 12.5383 8.35968 12.6633 8.61368L13.3923 10.0897L15.0213 10.3277C15.2993 10.3667 15.5293 10.5587 15.6173 10.8287C15.7063 11.0997 15.6353 11.3907 15.4303 11.5887Z"
                                        fill="currentColor"></path>
                                </svg>
                            </i>
                            <span class="item-name">Finance</span>
                            <i class="right-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7" />
                                </svg>
                            </i>
                        </a>

                        <ul class="sub-nav collapse" id="sidebar-finance" data-bs-parent="#sidebar-menu">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('flash.payroll.billing') ? 'active' : '' }}"
                                    href="{{ route('flash.payroll.billing') }}">
                                    <i class="icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" viewBox="0 0 24 24"
                                            fill="currentColor">
                                            <circle cx="12" cy="12" r="8"></circle>
                                        </svg>
                                    </i>
                                    <i class="sidenav-mini-icon">B</i>
                                    <span class="item-name">Billing</span>
                                </a>
                            </li>
                            @if (hasRole(['owner', 'it']))
                                <li class="nav-item">
                                    <a class="nav-link {{ request()->routeIs('flash.payroll.dashboard') ? 'active' : '' }}"
                                        href="{{ route('flash.payroll.dashboard') }}">
                                        <i class="icon">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="10"
                                                viewBox="0 0 24 24" fill="currentColor">
                                                <circle cx="12" cy="12" r="8"></circle>
                                            </svg>
                                        </i>
                                        <i class="sidenav-mini-icon">P</i>
                                        <span class="item-name">Payroll</span>
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </li>
                @endif

                <li>
                    <hr class="hr-horizontal">
                </li>
            </ul>
            <!-- Sidebar Menu End -->

            {{-- Optional promo card: keep or remove --}}
            <div class="card bg-primary text-white text-center iq-post m-3">
                <div class="card-body">
                    <h3 class="mb-2 text-white">Quick Tip</h3>
                    <p class="text-white mb-4">Build Trips/Dispatch first. Everything connects to it.</p>
                    <a href="#" class="btn bg-white text-primary iq-btn">View Details</a>
                </div>
            </div>
        </div>

        <div class="p-2"></div>
    </div>
</aside>
