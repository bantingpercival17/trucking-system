@extends('layouts.flash')

@section('title', 'Flash Payroll')

@section('content')

    <div class="container-fluid py-4">

        <div class="ui-hero p-3 p-lg-4 mb-3 mb-lg-4">
            <div
                class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <div>
                    <h4 class="mb-1">Payroll Dashboard</h4>
                    <div class="text-muted small">
                        Overview before processing payroll
                    </div>
                </div>

                <div class="d-flex gap-2">

                    <a class="btn btn-outline-secondary" href="{{ route('flash.payroll.index', [
                            'from' => request('from'),
                            'to' => request('to'),
                        ]) }}">
                        → Payroll
                    </a>

                    <a class="btn btn-outline-secondary" href="{{ route('flash.payroll.history') }}">
                        → History
                    </a>

                </div>
            </div>
        </div>

        {{-- KPI --}}

        <div class="row g-3 mb-4">

            {{-- Trips Ready --}}
            <div class="col-12 col-md-8 col-lg-4 d-flex">
                <div class="card ui-card border-0 ui-indicator ui-indicator-primary ui-kpi-card h-80 w-100"
                    style="margin-bottom: 0px;">
                    <div class="card-body text-center ui-kpi-body">
                        <div class="ui-kpi-label">Trips Ready 🚚</div>
                        <div class="ui-kpi-number text-primary">
                            {{ $pendingTrips }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Drivers --}}
            <div class="col-12 col-md-8 col-lg-4 d-flex">
                <div class="card ui-card border-0 ui-indicator ui-indicator-success ui-kpi-card h-80 w-100"
                    style="margin-bottom: 0px;">
                    <div class="card-body text-center ui-kpi-body">
                        <div class="ui-kpi-label">Drivers to Pay 👨‍✈️</div>
                        <div class="ui-kpi-number text-success">
                            {{ $drivers }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payroll Total --}}
            <div class="col-12 col-md-8 col-lg-4 d-flex">
                <div class="card ui-card border-0 ui-indicator ui-indicator-warning ui-kpi-card h-80 w-100"
                    style="margin-bottom: 0px;">
                    <div class="card-body text-center ui-kpi-body">
                        <div class="ui-kpi-label">Payroll Total 💰</div>
                        <div class="ui-kpi-number text-warning">
                            ₱ {{ number_format($total, 2) }}
                        </div>
                    </div>
                </div>
            </div>

        </div>


        {{-- QUEUE --}}
        <div class="card mb-4">
            <div class="card-body">

                <div class="queue-header mb-3">

                    <div class="queue-title">
                        <h5 class="mb-1 fw-semibold">Payroll Queue</h5>
                        <div class="queue-range">
                            {{ $from->format('M d, Y') }} – {{ $to->format('M d, Y') }}
                        </div>
                    </div>

                    <form method="GET" class="queue-filter">

                        <input type="date" name="from" value="{{ request('from') }}"
                            class="form-control form-control-sm">

                        <input type="date" name="to" value="{{ request('to') }}"
                            class="form-control form-control-sm">

                        <button class="btn btn-sm btn-primary">
                            Apply
                        </button>

                    </form>

                </div>



                <table class="table">
                    <thead>
                        <tr>
                            <th>Driver</th>
                            <th>Trips</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($queue as $q)
                            <tr>
                                <td>{{ $q['name'] }}</td>
                                <td>{{ $q['trips'] }}</td>
                                <td>₱ {{ number_format($q['amount'], 2) }}</td>
                                <td>
                                    <span
                                        class="badge 
                                        {{ $q['status'] === 'Unpaid' ? 'bg-danger' : 'bg-success' }}">
                                        {{ $q['status'] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
        </div>

        @if ($driversPayroll->isEmpty())

            <div class="card text-center p-5">
                <h5 class="text-muted mb-2">No Payroll for this date</h5>
                <small class="text-muted">
                    Try selecting a different date range
                </small>
            </div>
        @else
            @foreach ($driversPayroll as $d)
                <div class="card mb-4">
                    <div class="card-body">

                        <h6 class="fw-bold text-primary">
                            {{ $d['plate'] }}
                        </h6>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Driver</th> {{-- 👈 ADD --}}
                                        <th>Destination</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse ($d['rows'] as $r)
                                        <tr>
                                            <td>{{ $r['date'] }}</td>
                                            <td>{{ $r['driver'] }}</td>
                                            <td>{{ $r['destination'] }}</td>
                                            <td>₱ {{ number_format($r['amount'], 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">
                                                No Payroll for this date
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3">TOTAL</th>
                                        <th>
                                            ₱ {{ number_format($d['total'], 2) }}
                                        </th>
                                    </tr>
                                </tfoot>

                            </table>
                        </div>
                    </div>
                </div>
            @endforeach

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

        .billing-unbilled {
            background: #fff7ed;
            color: #ea580c;
        }

        .billing-billed {
            background: #e0f2fe;
            color: #0369a1;
        }

        @media (max-width: 768px) {

            .ui-available-number {
                font-size: 24px;
            }

            .card-body {
                padding: 14px;
            }

            table th,
            table td {
                font-size: 13px;
                padding: 10px;
            }

        }

        @media (max-width: 576px) {

            .ui-available-card .card-body {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

        }

        .ui-kpi-number {
            font-size: 20px;
            font-weight: 800;
            line-height: 1;
            /* eto ang nagtatanggal ng extra space */
            margin-top: 2px;
            margin-bottom: -2px;
            /* bawas lower space */
        }

        .ui-kpi-label {
            font-size: 14px;
            /* mas malaki */
            font-weight: 700;
            /* mas kapal */
            color: #4b5563;
            margin-bottom: 10px;
            /* maliit lang gap */
        }

        /* QUEUE HEADER */

        .queue-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .queue-range {
            font-size: 13px;
            color: #6b7280;
        }

        .queue-filter {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
        }

        .queue-filter input {
            width: 120px;
        }

        .queue-filter button {
            white-space: nowrap;
        }

        /* MOBILE QUEUE CARDS */

        .queue-card {
            background: white;
            border-radius: 16px;
            padding: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .05);
        }

        .queue-card-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .queue-name {
            font-weight: 600;
            font-size: 15px;
        }

        .queue-role {
            display: block;
            font-size: 12px;
            color: #6b7280;
            font-weight: 500;
        }

        .queue-stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .queue-stat span {
            font-size: 12px;
            color: #6b7280;
            display: block;
        }

        .queue-stat strong {
            font-size: 15px;
        }

        .queue-btn {
            border-radius: 10px;
            font-weight: 600;
        }

        .queue-details {
            margin-bottom: 14px;
        }

        .queue-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }

        .queue-row span {
            font-size: 13px;
            color: #6b7280;
        }

        .queue-row strong {
            font-size: 16px;
            font-weight: 700;
        }

        /* MOBILE HEADER STACK */

        @media (max-width:576px) {

            .queue-header {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .queue-title {
                width: 100%;
            }


            .queue-filter {
                display: flex;
                gap: 8px;
                flex-wrap: wrap;
                align-items: center;
                flex-direction: column;
                width: 100%;
            }

            .queue-filter input {
                width: 120px;
            }

            .queue-filter button {
                white-space: nowrap;
            }


            .queue-filter input,
            .queue-filter button {
                width: 100%;
            }

            .queue-range {
                font-size: 13px;
                color: #6b7280;
                margin-top: 2px;
                margin-bottom: 8px;
            }

        }
    </style>
@endpush

@push('scripts')
    <script>
        document.getElementById('addRowBtn').addEventListener('click', function() {

            const table = document.getElementById('allowanceTable');

            const row = `
        <tr>
            <td>
                <input type="number" name="rate_from[]" class="form-control form-control-sm">
            </td>

            <td>
                <input type="number" name="rate_to[]" class="form-control form-control-sm">
            </td>

            <td>
                <input type="number" name="allowance[]" class="form-control form-control-sm">
            </td>

            <td>
                <button type="button" class="btn btn-sm btn-outline-danger removeRow">
                    Delete
                </button>
            </td>
        </tr>
    `;

            table.insertAdjacentHTML('beforeend', row);

        });

        document.addEventListener('click', function(e) {

            if (e.target.classList.contains('removeRow')) {
                e.target.closest('tr').remove();
            }

        });
    </script>
@endpush
