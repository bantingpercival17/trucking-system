@extends('layouts.owner')

@section('title', 'User Reports')

@section('content')

    {{-- HERO --}}
    <div class="ui-hero p-3 p-lg-4 mb-4">

        <h4 class="mb-1 fw-bold">Reports Dashboard</h4>

        <div class="text-muted small">
            Overview of maintenance, trips, fuel logs, vehicle statistics and fuel consumption.
        </div>

    </div>


    {{-- SUMMARY KPI CARDS --}}
    <div class="row g-3 mb-0">

        <div class="col-md-4">
            <div class="card ui-card border-0 h-80" style="margin-bottom: 0px;">
                <div class="card-body text-center">
                    <div class="text-muted small">Maintenance Records</div>
                    <div class="ui-available-number text-secondary">
                        {{ $maintenanceCount ?? '—' }}
                    </div>
                    <div class="small text-muted">
                        Last: {{ $lastMaintenance ?? '—' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card ui-card border-0 h-80" style="margin-bottom: 0px;">
                <div class="card-body text-center">
                    <div class="text-muted small">Trips Completed</div>
                    <div class="ui-available-number text-success">
                        {{ $tripCount ?? '—' }}
                    </div>
                    <div class="small text-muted">
                        Last: {{ $lastTripDate ?? '—' }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card ui-card border-0 h-80" style="margin-bottom: 0px;">
                <div class="card-body text-center">
                    <div class="text-muted small">Fuel Logs</div>
                    <div class="ui-available-number text-primary">
                        {{ $fuelLogCount ?? '—' }}
                    </div>
                    <div class="small text-muted">
                        Last: {{ $lastFuelEntry ?? '—' }}
                    </div>
                </div>
            </div>
        </div>

    </div>


    {{-- VEHICLE REPORT --}}
    <div class="card ui-card border-0 mb-4" style="margin-top: 20px;">

        <div class="card-header bg-transparent border-0 pb-0">

            <h6 class="fw-semibold mb-1">Vehicle Report</h6>
            <div class="text-muted small">Grouped by plate number</div>

            <div class="ui-divider mt-3"></div>

        </div>

        <div class="card-body">

            {{-- FILTER --}}
            <form method="GET" class="row g-2 mb-3">

                <div class="col-md-3">
                    <input type="date" name="vehicle_from" class="form-control" value="{{ request('vehicle_from') }}">
                </div>

                <div class="col-md-3">
                    <input type="date" name="vehicle_to" class="form-control" value="{{ request('vehicle_to') }}">
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary ui-pill-btn w-100">
                        Filter
                    </button>
                </div>

            </form>


            <div class="ui-table-wrap">

                <div class="ui-table-scroll">

                    <table class="table ui-table">

                        <thead>
                            <tr>
                                <th>Plate</th>
                                <th>Total Trips</th>
                                <th>Fuel Cost</th>
                                <th>Fuel Liters</th>
                                <th>Maintenance Cost</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($vehicleReports as $report)
                                <tr>
                                    <td class="fw-semibold">{{ $report['plate_number'] }}</td>
                                    <td>{{ $report['total_trips'] }}</td>
                                    <td>₱ {{ number_format($report['total_fuel_cost'], 2) }}</td>
                                    <td>{{ number_format($report['total_fuel_liters'], 2) }}</td>
                                    <td>₱ {{ number_format($report['total_maintenance_cost'], 2) }}</td>
                                </tr>

                            @empty

                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        No data found.
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>


    {{-- TRIP REPORT --}}
    <div class="card ui-card border-0 mb-4">

        <div class="card-header bg-transparent border-0 pb-0">

            <h6 class="fw-semibold mb-1">Trip Report</h6>
            <div class="text-muted small">History of trips</div>

            <div class="ui-divider mt-3"></div>

        </div>

        <div class="card-body">

            <form method="GET" class="row g-2 mb-3">

                <div class="col-md-3">
                    <select name="trip_plate_number" class="form-select">
                        <option value="">All Plates</option>

                        @foreach ($plateNumbers ?? [] as $plate)
                            <option value="{{ $plate }}" @selected(request('trip_plate_number') == $plate)>
                                {{ $plate }}
                            </option>
                        @endforeach

                    </select>
                </div>

                <div class="col-md-3">
                    <input type="date" name="trip_from" class="form-control" value="{{ request('trip_from') }}">
                </div>

                <div class="col-md-3">
                    <input type="date" name="trip_to" class="form-control" value="{{ request('trip_to') }}">
                </div>

                <div class="col-md-2">
                    <button class="btn btn-primary ui-pill-btn w-100">
                        Filter
                    </button>
                </div>

            </form>


            <div class="ui-table-wrap">
                <div class="ui-table-scroll">

                    <table class="table ui-table">

                        <thead>
                            <tr>
                                <th>Plate</th>
                                <th>Driver</th>
                                <th>Date</th>
                                <th>Destination</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($tripReports as $trip)
                                <tr>
                                    <td>{{ $trip['plate_number'] }}</td>
                                    <td>{{ $trip['driver_name'] }}</td>
                                    <td>{{ $trip['trip_date'] }}</td>
                                    <td>{{ $trip['destination'] }}</td>
                                </tr>

                            @empty

                                <tr>
                                    <td colspan="4" class="text-center text-muted">
                                        No trips found.
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>
            </div>

        </div>

    </div>


    {{-- FUEL REPORT --}}
    <div class="card ui-card border-0 mb-4">

        <div class="card-header bg-transparent border-0 pb-0">

            <h6 class="fw-semibold mb-1">Fuel Consumption</h6>
            <div class="text-muted small">Fuel entries per vehicle</div>

            <div class="ui-divider mt-3"></div>

        </div>

        <div class="card-body">

            <div class="ui-table-wrap">
                <div class="ui-table-scroll">

                    <table class="table ui-table">

                        <thead>
                            <tr>
                                <th>Plate</th>
                                <th>Date</th>
                                <th>Liters</th>
                                <th>Cost</th>
                                <th>Odometer</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($fuelReports as $fuel)
                                <tr>
                                    <td>{{ $fuel['plate_number'] }}</td>
                                    <td>{{ $fuel['fuel_date'] }}</td>
                                    <td>{{ $fuel['liters'] }}</td>
                                    <td>₱ {{ number_format($fuel['cost'], 2) }}</td>
                                    <td>{{ $fuel['odometer'] }}</td>
                                </tr>

                            @empty

                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        No fuel records found.
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>
            </div>

        </div>

    </div>


    <div class="row g-3 mb-4">

        {{-- TOTAL FUEL COST --}}
        <div class="col-lg-6">

            <div class="card ui-card border-0 h-100">

                <div class="card-body">

                    <h6 class="fw-semibold mb-3">Total Fuel Cost per Vehicle</h6>

                    <ul class="mb-0">

                        @foreach ($totalFuelCost ?? [] as $plate => $cost)
                            <li>
                                <strong>{{ $plate }}</strong>
                                — ₱ {{ number_format($cost, 2) }}
                            </li>
                        @endforeach

                    </ul>

                </div>

            </div>

        </div>


        {{-- AVERAGE CONSUMPTION --}}
        @if (isset($averageConsumption))
            <div class="col-lg-6">

                <div class="card ui-card border-0 h-100">

                    <div class="card-body text-center">

                        <div class="text-muted small">
                            Average Fuel Consumption
                        </div>

                        <div class="ui-available-number text-primary mt-2">
                            {{ $averageConsumption }}
                        </div>

                    </div>

                </div>

            </div>
        @endif

    </div>


@endsection

@push('styles')
    <style>
        /* ===== Shipments-like UI ===== */
        .ui-card {
            border-radius: 18px;
            box-shadow: 0 14px 40px rgba(16, 24, 40, .08);
            transition: all .25s ease;
        }

        .ui-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 50px rgba(16, 24, 40, .12);
        }

        .ui-hero {
            border-radius: 20px;
            border: 1px solid rgba(0, 0, 0, .05);
            background:
                radial-gradient(900px 500px at 10% 10%, rgba(99, 102, 241, .10), transparent 60%),
                radial-gradient(900px 500px at 90% 20%, rgba(16, 185, 129, .10), transparent 60%),
                linear-gradient(135deg, #ffffff, #f9fafb);
            box-shadow: 0 20px 40px rgba(17, 24, 39, .06);
        }

        .ui-divider {
            height: 1px;
            background: #edf0f4;
            width: 100%;
        }

        /* Search */
        .ui-search {
            position: relative;
        }

        .ui-search-input {
            height: 42px;
            border-radius: 999px;
            padding-left: 40px;
            border: 1px solid #e6e8ec;
            background: #fafbfc;
            transition: .2s ease;
        }

        .ui-search-input:focus {
            background: #fff;
            border-color: #cfd6ff;
            box-shadow: 0 0 0 .20rem rgba(13, 110, 253, .10);
        }

        .ui-search-icon {
            position: absolute;
            top: 50%;
            left: 14px;
            transform: translateY(-50%);
            color: #98a2b3;
            pointer-events: none;
        }

        /* pills */
        .ui-pill-btn {
            border-radius: 999px;
            padding: .45rem .90rem;
        }

        /* Make header buttons match input height */
        .ui-btn-wide,
        .ui-btn-equal {
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 1.1rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .ui-btn-wide {
            min-width: 140px;
        }

        /* Table */
        .ui-table-wrap {
            border: 1px solid #edf0f4;
            border-radius: 16px;
            background: #fff;
        }

        .ui-table-scroll {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 16px;
        }

        .ui-table {
            /* min-width removed to prevent horizontal scroll bar */
        }

        .ui-table thead th {
            background: #f8fafc;
            color: #667085;
            font-weight: 600;
            font-size: .80rem;
            letter-spacing: .02em;
            border-bottom: 1px solid #edf0f4 !important;
            padding: 14px 16px;
            white-space: nowrap;
        }

        .ui-table tbody td {
            padding: 14px 16px;
            border-top: 1px solid #f1f3f6 !important;
            vertical-align: middle;
        }

        .ui-table tbody tr:hover {
            background: #fafcff;
        }

        /* top pagination */
        .ui-pager-top {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 6px;
        }

        .ui-pager-top .pagination {
            flex-wrap: nowrap;
            white-space: nowrap;
            margin-bottom: 0;
        }

        .ui-showing {
            white-space: nowrap;
        }

        /* Sort links */
        .table-sort {
            color: inherit;
            text-decoration: none;
            display: inline-flex;
            gap: .35rem;
            align-items: center;
            font-weight: 600;
        }

        .table-sort:hover {
            text-decoration: underline;
        }

        /* Status badges */
        .ui-badge {
            display: inline-flex;
            align-items: center;
            padding: .35rem .75rem;
            border-radius: 999px;
            font-size: .75rem;
            font-weight: 600;
            border: 1px solid transparent;
        }

        .ui-badge-draft {
            background: #f2f4f7;
            color: #344054;
            border-color: #eaecf0;
        }

        .ui-badge-dispatched {
            background: #e8f1ff;
            color: #175cd3;
            border-color: #cfe1ff;
        }

        .ui-badge-completed {
            background: #e7f8ef;
            color: #027a48;
            border-color: #bff0d4;
        }

        .ui-badge-cancelled {
            background: #ffeceb;
            color: #b42318;
            border-color: #ffd1cf;
        }

        .ui-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }

        .ui-dot-draft {
            background: #667085;
        }

        .ui-dot-dispatched {
            background: #175cd3;
        }

        .ui-dot-completed {
            background: #027a48;
        }

        .ui-dot-cancelled {
            background: #b42318;
        }

        .ui-dot-pulse {
            position: relative;
        }

        .ui-dot-pulse::after {
            content: "";
            position: absolute;
            inset: -4px;
            border-radius: 999px;
            border: 1px solid rgba(23, 92, 211, .35);
            animation: uiPulse 1.6s ease-out infinite;
        }

        @keyframes uiPulse {
            0% {
                transform: scale(.65);
                opacity: .9;
            }

            100% {
                transform: scale(1.25);
                opacity: 0;
            }
        }

        .ui-action-btn {
            border-radius: 999px;
            padding: .25rem .5rem;
            font-weight: 600;
        }

        .ui-icon-btn {
            border-radius: 12px;
            border: 1px solid #f1f3f6;
            background: #fff;
            padding: .35rem .6rem;
        }

        .ui-icon-btn:hover {
            background: #f8fafc;
        }

        /* Available cards */
        .ui-available-card {
            border-radius: 14px;
            box-shadow: 0 8px 25px rgba(16, 24, 40, .06);
            transition: .2s ease;
        }

        .ui-available-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(16, 24, 40, .10);
        }

        .ui-available-number {
            font-size: 30px;
            font-weight: 800;
            line-height: 1;
        }

        .ui-eye-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1px solid #d0d5dd;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: .2s ease;
        }

        .ui-eye-btn i {
            font-size: 16px;
            color: #344054;
        }

        .ui-eye-btn:hover {
            background: #f2f4f7;
        }

        .ui-available-dropdown {
            margin-top: 6px;
        }

        .ui-list-controls .btn {
            border-radius: 999px;
            padding: .25rem .7rem;
        }

        .ui-mobile-trip {
            border-radius: 16px;
        }

        .ui-mobile-trip .card-body {
            padding: 14px 14px;
        }

        @media (max-width: 575.98px) {
            .ui-btn-wide {
                width: 100%;
            }

            .ui-btn-equal {
                width: 100%;
            }
        }

        @media (min-width:1200px) {
            .col-xl-5col {
                width: 20%;
                flex: 0 0 20%;
                max-width: 20%;
            }
        }

        #newTripModal .select2-container {
            width: 100% !important;
        }

        #newTripModal .select2-container--default .select2-selection--single {
            height: calc(2.375rem + 8px);
            padding: .375rem .75rem;
            border: 1px solid var(--bs-border-color, #ced4da);
            border-radius: .5rem;
            background-color: #fff;
            display: flex;
            align-items: center;
        }

        #newTripModal .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding-left: 0 !important;
            line-height: 1.5;
            color: var(--bs-body-color, #212529);
        }

        #newTripModal .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
            right: .5rem;
        }

        #newTripModal .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #86b7fe;
            box-shadow: 0 0 0 .25rem rgba(13, 110, 253, .25);
        }

        #newTripModal .select2-dropdown {
            border: 1px solid var(--bs-border-color, #ced4da);
            border-radius: .5rem;
            overflow: hidden;
        }

        #newTripModal .select2-search__field {
            border-radius: .375rem;
            border: 1px solid var(--bs-border-color, #ced4da) !important;
            padding: .375rem .5rem;
            outline: none;
        }

        .person-stack {
            display: flex;
            align-items: center;
        }

        .person-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: #374151;
            border: 2px solid #fff;
        }

        .person-avatar:not(:first-child) {
            margin-left: -10px;
        }

        .trip-ticket {
            font-weight: 700;
            font-size: 15px;
            color: #4f46e5;
            background: #eef2ff;
            padding: 4px 10px;
            border-radius: 8px;
            display: inline-block;
        }

        .trip-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        /* top icons */
        .trip-icons {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        /* equal icon buttons */
        .trip-icons .btn {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* dispatch full width */
        .trip-dispatch button {
            width: 100%;
            height: 42px;
            border-radius: 10px;
            font-weight: 600;
        }

        .trip-actions .btn {
            border-radius: 10px;
        }

        .trip-actions .btn-primary {
            padding-left: 14px;
            padding-right: 14px;
        }

        /* mobile optimization */
        @media (max-width:420px) {

            .trip-actions {
                justify-content: space-between;
            }

            .trip-actions .btn-primary {
                flex: 1;
            }

        }

        @media (max-width: 320px) {
            .ui-available-card .card-body {
                padding: 10px 12px;
            }
        }

        .person-avatar {
            position: relative;
            cursor: pointer;
        }

        /* tooltip */
        .person-avatar::after {
            content: attr(data-name);
            position: absolute;
            bottom: 120%;
            left: 50%;
            transform: translateX(-50%);
            background: #111827;
            color: white;
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 6px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: 0.2s ease;
        }

        /* show on hover */
        .person-avatar:hover::after {
            opacity: 1;
        }

        .trip-status-row {
            display: flex;
            gap: 6px;
            margin-top: 6px;
        }

        .trip-status {
            font-size: 12px;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 8px;
            background: #f1f3f6;
            color: #344054;
        }

        /* delivery */
        .trip-status.delivery {
            background: #eef2ff;
            color: #4f46e5;
        }

        /* billing */
        .trip-status.billing {
            background: #fff7ed;
            color: #ea580c;
        }

        /* payment */
        .trip-status.payment {
            background: #ecfdf5;
            color: #16a34a;
        }

        .person-avatar.color-1 {
            background: #fee2e2;
            color: #991b1b;
        }

        .person-avatar.color-2 {
            background: #dbeafe;
            color: #1e3a8a;
        }

        .person-avatar.color-3 {
            background: #dcfce7;
            color: #166534;
        }

        .person-avatar.color-4 {
            background: #fef9c3;
            color: #854d0e;
        }

        .person-avatar.color-5 {
            background: #ede9fe;
            color: #5b21b6;
        }

        .person-avatar.color-6 {
            background: #fce7f3;
            color: #9d174d;
        }

        .person-avatar.color-7 {
            background: #cffafe;
            color: #155e75;
        }

        .person-avatar.color-8 {
            background: #f3f4f6;
            color: #374151;
        }
    </style>
@endpush
