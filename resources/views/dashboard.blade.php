@extends('layouts.owner')

@section('title', 'Dashboard')

@section('content')

    <div class="row align-items-start g-4">

        {{-- LEFT --}}
        <div class="col-lg-8">
            <div class="d-flex flex-column gap-3">

                {{-- TOP STATS --}}
                <div class="card">
                    <div class="card-body pb-xxl-0">
                        <div class="row row-cols-1 row-cols-md-2 row-cols-xl-2 row-cols-xxl-4 g-3">

                            {{-- TRUCKS --}}
                            <div class="col">
                                <div class="card mb-xxl-0 iq-purchase" data-iq-gsap="onStart" data-iq-position-y="50"
                                    data-iq-rotate="0" data-iq-trigger="scroll" data-iq-ease="power.out"
                                    data-iq-opacity="0">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <h5 class="text-primary mb-0">Trucks</h5>
                                            <a href="{{ route('owner.trucks.index') }}">
                                                <svg width="24" height="25" viewBox="0 0 24 25" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M15.1609 12.5531C15.1609 14.2991 13.7449 15.7141 11.9989 15.7141C10.2529 15.7141 8.83789 14.2991 8.83789 12.5531C8.83789 10.8061 10.2529 9.39108 11.9989 9.39108C13.7449 9.39108 15.1609 10.8061 15.1609 12.5531Z"
                                                        stroke="#7B60E7" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round"></path>
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M11.998 19.8549C15.806 19.8549 19.289 17.1169 21.25 12.5529C19.289 7.98888 15.806 5.25089 11.998 5.25089H12.002C8.194 5.25089 4.711 7.98888 2.75 12.5529C4.711 17.1169 8.194 19.8549 12.002 19.8549H11.998Z"
                                                        stroke="#7B60E7" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round"></path>
                                                </svg>
                                            </a>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h3 class="mb-0">{{ $trucksStats['total'] ?? 0 }}</h3>
                                            <p class="mb-0 ms-2 text-muted">{{ $trucksStats['active'] ?? 0 }} active</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- DRIVERS --}}
                            <div class="col">
                                <div class="card mb-xxl-0 iq-purchase" data-iq-gsap="onStart" data-iq-position-y="150"
                                    data-iq-rotate="0" data-iq-trigger="scroll" data-iq-ease="power.out"
                                    data-iq-opacity="0">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <h5 class="text-primary mb-0">Drivers</h5>
                                            <a href="{{ route('owner.drivers.index') }}">
                                                <svg width="16" height="21" viewBox="0 0 16 21" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M7.98493 13.8462C4.11731 13.8462 0.814453 14.431 0.814453 16.7729C0.814453 19.1148 4.09636 19.7205 7.98493 19.7205C11.8525 19.7205 15.1545 19.1348 15.1545 16.7938C15.1545 14.4529 11.8735 13.8462 7.98493 13.8462Z"
                                                        stroke="#7B60E7" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round"></path>
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M7.98489 10.5059C10.523 10.5059 12.5801 8.44782 12.5801 5.90972C12.5801 3.37163 10.523 1.31448 7.98489 1.31448C5.44679 1.31448 3.3887 3.37163 3.3887 5.90972C3.38013 8.43925 5.42394 10.4973 7.95251 10.5059H7.98489Z"
                                                        stroke="#7B60E7" stroke-width="1.42857" stroke-linecap="round"
                                                        stroke-linejoin="round"></path>
                                                </svg>
                                            </a>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h3 class="mb-0">{{ $driversStats['total'] ?? 0 }}</h3>
                                            <p class="mb-0 ms-2 text-muted">{{ $driversStats['active'] ?? 0 }} active</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- DESTINATIONS --}}
                            <div class="col">
                                <div class="card iq-purchase" data-iq-gsap="onStart" data-iq-position-y="250"
                                    data-iq-rotate="0" data-iq-trigger="scroll" data-iq-ease="power.out"
                                    data-iq-opacity="0">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <h5 class="text-primary mb-0">Destinations</h5>
                                            <a href="{{ route('owner.destinations.index') }}">
                                                <svg width="17" height="13" viewBox="0 0 17 13" fill="none"
                                                    xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M1 6.49905L6.0014 11.4984L16 1.49976" stroke="#7B60E7"
                                                        stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                                    </path>
                                                </svg>
                                            </a>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h3 class="mb-0">{{ $destinationsStats['total'] ?? 0 }}</h3>
                                            <p class="mb-0 ms-2">Avg:
                                                ₱{{ number_format($destinationsStats['avg_rate'], 2) }}</p>

                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- TRIPS --}}
                            <div class="col">
                                <div class="card iq-purchase" data-iq-gsap="onStart" data-iq-position-y="350"
                                    data-iq-rotate="0" data-iq-trigger="scroll" data-iq-ease="power.out"
                                    data-iq-opacity="0">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <h5 class="text-primary mb-0">Trips</h5>
                                            <a href="{{ route('owner.trips.index') }}">
                                                <svg width="15" height="13" viewBox="0 0 15 17"
                                                    fill="currentcolor" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M7.72461 0.75L7.72461 15.75" stroke="currentcolor"
                                                        stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round"></path>
                                                    <path d="M1.701 6.7998L7.725 0.749804L13.75 6.7998"
                                                        stroke="currentcolor" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round"></path>
                                                </svg>
                                            </a>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between">
                                            <h3 class="mb-0">{{ $tripsStats['total'] ?? 0 }}</h3>
                                            <p class="mb-0 ms-2 text-muted">{{ $tripsStats['completed'] ?? 0 }} done</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        {{-- MINI SUMMARY (Today / Week / Profit) --}}
                        <div class="card-body pt-3 iq-services">
                            <div class="card-group border rounded-1">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <p class="mb-0">Today</p>
                                            <span class="badge bg-soft-success text-dark">
                                                {{ $todayData['dispatched'] ?? 0 }}
                                            </span>
                                        </div>
                                        <h5
                                            class="mb-1 
                                            {{ $todayData['profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                                            ₱{{ number_format($todayData['profit'] ?? 0, 2) }}
                                        </h5>
                                        <p class="small text-muted mb-0">Trips Dispatched Today</p>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <p class="mb-0">This week</p>
                                            <span class="badge bg-soft-info text-dark">
                                                {{ $weekData['dispatched'] ?? 0 }}
                                            </span>
                                        </div>
                                        <h5 class="mb-1">₱{{ number_format($weekData['gains'] ?? 0, 2) }}</h5>
                                        <p class="small text-muted mb-0">Trips Dispatched This Week</p>
                                    </div>
                                </div>

                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <p class="mb-0">Net Profit</p>
                                            <span class="badge bg-soft-success text-dark">✓</span>
                                        </div>
                                        <h5 class="mb-1">₱{{ number_format($todayData['profit'] ?? 0, 2) }}</h5>
                                        <p class="small text-muted mb-0">Today's Net Profit</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                {{-- ACTIVE TRIPS --}}
                <div class="card" data-iq-gsap="onStart" data-iq-position-y="70" data-iq-rotate="0"
                    data-iq-trigger="scroll" data-iq-ease="power.out" data-iq-opacity="0">
                    <div class="card-header justify-content-between d-flex align-items-center">
                        <h4 class="card-title fw-bolder mb-0">Active Trips</h4>
                        <a href="{{ route('owner.trips.index') }}" class="btn btn-primary btn-sm">View All</a>
                    </div>

                    <div class="card-body">
                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                            @forelse($activeTrips as $trip)
                                <div class="col">
                                    <div class="card border h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <span class="badge bg-success">In Transit</span>
                                                <small class="text-muted">
                                                    {{ $trip->dispatch_date ? \Carbon\Carbon::parse($trip->dispatch_date)->format('M d') : '—' }}
                                                </small>
                                            </div>

                                            <h6 class="card-title mb-2">{{ $trip->trip_ticket_no ?? 'N/A' }}</h6>

                                            <p class="mb-1 small">
                                                <strong>Destination:</strong>
                                                {{ $trip->destination?->store_name ?? 'N/A' }}
                                            </p>

                                            <p class="mb-2 small">
                                                <strong>Truck:</strong> {{ $trip->truck?->plate_number ?? 'N/A' }}
                                            </p>

                                            <p class="mb-3 small text-muted fst-italic">
                                                {{ $trip->driver?->name ?? '—' }}
                                            </p>

                                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                                <span class="small text-muted">Status</span>
                                                <span class="fw-bold text-success">{{ $trip->status ?? '—' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <p class="text-muted text-center py-4 mb-0">
                                        No active trips.
                                    </p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- HIGH VALUE DESTINATIONS --}}
                <div class="card" data-iq-gsap="onStart" data-iq-position-y="70" data-iq-rotate="0"
                    data-iq-trigger="scroll" data-iq-ease="power.out" data-iq-opacity="0">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title fw-bolder mb-0">High-Value Destinations</h4>
                        <a href="{{ route('owner.destinations.index') }}" class="btn btn-primary btn-sm">View all</a>
                    </div>

                    <div class="card-body">
                        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                            @forelse($topDestinations as $destination)
                                <div class="col">
                                    <div class="card border h-100">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <span class="badge bg-success">High Rate</span>
                                                <small class="text-muted">{{ $destination->truck_type ?? '—' }}</small>
                                            </div>
                                            <h6 class="card-title mb-2">{{ $destination->store_name ?? 'N/A' }}</h6>
                                            <p class="mb-1 small"><strong>Code:</strong>
                                                {{ $destination->store_code ?? '—' }}</p>
                                            <p class="mb-2 small"><strong>Area:</strong> {{ $destination->area ?? '—' }}
                                            </p>

                                            @if (!empty($destination->remarks))
                                                <p class="mb-3 small text-muted fst-italic">{{ $destination->remarks }}
                                                </p>
                                            @endif

                                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                                <span class="small text-muted">Rate</span>
                                                <span
                                                    class="fw-bold text-success">₱{{ number_format($destination->rate ?? 0, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <p class="text-muted text-center py-4 mb-0">No destinations available</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- RIGHT --}}
        <div class="col-lg-4">
            <div class="d-flex flex-column gap-3">

                {{-- FINANCIAL SUMMARY --}}
                <div class="card bg-primary text-center text-white iq-post" data-iq-gsap="onStart"
                    data-iq-position-y="70" data-iq-rotate="0" data-iq-trigger="scroll" data-iq-ease="power.out"
                    data-iq-opacity="0">
                    <div class="card-body">
                        <h5 class="mb-3 text-white">Financial Summary</h5>
                        <div class="row g-3">
                            <div class="col-6">
                                <p class="mb-1 small text-white-50">Total Gains</p>
                                <h5 class="fw-bolder text-white mb-0" data-bs-toggle="tooltip" data-bs-html="true"
                                    style="cursor:pointer;"
                                    title="
        Billed: ₱{{ number_format($financialData['gains_billed'] ?? 0, 2) }} <br>
        Unbilled: ₱{{ number_format($financialData['gains_unbilled'] ?? 0, 2) }} <br>
        Pending: ₱{{ number_format($financialData['gains_pending'] ?? 0, 2) }}
    ">
                                    ₱{{ number_format($financialData['gains'] ?? 0, 2) }}
                                </h5>
                            </div>
                            <div class="col-6">
                                <p class="mb-1 small text-white-50">Expenses</p>
                                <h5 class="fw-bolder text-danger mb-0" data-bs-toggle="tooltip" data-bs-html="true"
                                    title="
    Fuel: ₱{{ number_format($financialData['fuel'] ?? 0, 2) }} <br>
    Load: ₱{{ number_format($financialData['load'] ?? 0, 2) }} <br>
    Deductions: ₱{{ number_format($financialData['deductions'] ?? 0, 2) }} <br>
    Payroll: ₱{{ number_format($financialData['payroll'] ?? 0, 2) }}
">
                                    ₱{{ number_format($financialData['expenses'] ?? 0, 2) }}
                                </h5>
                            </div>
                            <div class="col-12">
                                <hr class="border-white-50 my-2">
                                <p class="mb-1 small text-white-50">Net Profit</p>
                                <h4 class="fw-bolder text-success mb-0">
                                    ₱{{ number_format($financialData['profit'] ?? 0, 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- RECENT TRIPS --}}
                <div class="card" data-iq-gsap="onStart" data-iq-position-y="70" data-iq-rotate="0"
                    data-iq-trigger="scroll" data-iq-ease="power.out" data-iq-opacity="0">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title fw-bolder mb-0">Recent Trips</h4>
                        <a href="{{ route('owner.trips.history') }}" class="btn btn-primary btn-sm"> View All </a>
                    </div>

                    <div class="card-body">
                        @forelse($recentTrips as $trip)
                            <div
                                class="d-flex justify-content-between align-items-center flex-wrap py-3 @if (!$loop->last) border-bottom @endif">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $trip->trip_ticket_no ?? 'N/A' }}</h6>
                                    <span
                                        class="text-muted small">{{ $trip->destination?->store_name ?? 'No destination' }}</span>
                                </div>

                                <div class="text-end">
                                    <span
                                        class="badge bg-@if (($trip->status ?? '') === 'Completed') success @elseif(($trip->status ?? '') === 'Dispatched') warning @else secondary @endif">
                                        {{ $trip->status ?? '—' }}
                                    </span>

                                    <p class="mb-0 small text-muted mt-1">
                                        {{ $trip->dispatch_date ? \Carbon\Carbon::parse($trip->dispatch_date)->format('M d, Y') : '—' }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center py-4 mb-0">No trips yet</p>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.map(function(el) {
                return new bootstrap.Tooltip(el)
            })
        });
    </script>
@endpush
