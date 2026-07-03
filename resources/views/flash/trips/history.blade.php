@extends('layouts.flash')

@section('title', 'Trips History')

@section('content')

    <div class="container-fluid py-4">

        <div class="ui-hero p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold mb-1">Trips History</h4>
                    <div class="text-muted small">
                        Completed and cancelled trips.
                    </div>
                </div>
            </div>
        </div>

        <div class="card ui-card border-0">

            <div class="card-header bg-transparent border-0 pb-0">

                {{-- Title + pager --}}
                <div
                    class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">

                    <div class="ui-trips-head-left">
                        <h6 class="mb-0 fw-semibold">Trips History</h6>

                        <div class="text-muted small mt-1 ui-showing">

                            @if ($trips->total())
                                Showing
                                <strong>{{ $trips->firstItem() }}–{{ $trips->lastItem() }}</strong>
                                /
                                <strong>{{ $trips->total() }}</strong>
                            @else
                                Showing <strong>0</strong> / <strong>0</strong>
                            @endif

                        </div>
                    </div>

                    {{-- RIGHT SIDE BUTTONS --}}
                    <div class="d-flex align-items-center gap-2">

                        <a href="{{ route('flash.payroll.billing') }}" class="btn btn-outline-secondary btn-sm ui-pill-btn">
                            <i class="bi bi-receipt me-1"></i> Billing
                        </a>

                        <a href="{{ route('flash.trips.index') }}" class="btn btn-outline-secondary btn-sm ui-pill-btn">
                            <i class="bi bi-truck me-1"></i> Trips
                        </a>

                    </div>

                </div>


                {{-- Controls --}}
                <div
                    class="mt-3 d-flex flex-column flex-lg-row gap-2 align-items-stretch align-items-lg-center justify-content-between">

                    <form method="GET" action="{{ route('flash.trips.history') }}"
                        class="d-flex flex-column flex-sm-row gap-2 align-items-stretch align-items-sm-center m-0 flex-grow-1">

                        <div class="ui-search ui-header-search" style="max-width:520px;width:100%;">
                            <i class="bi bi-search ui-search-icon"></i>

                            <input type="text" name="q" value="{{ request('q') }}"
                                class="form-control ui-search-input" placeholder="Search trip ticket, truck, driver...">
                        </div>


                        @if (request('q'))
                            <a href="{{ route('flash.trips.history', request()->except('q', 'page')) }}"
                                class="btn btn-outline-secondary btn-sm ui-pill-btn ui-btn-equal">
                                Clear
                            </a>
                        @endif


                        {{-- SHOW ENTRIES --}}
                        <div class="ui-trips-head-right">

                            <div class="d-flex align-items-center gap-2">

                                <label class="small text-muted m-0">Show</label>

                                <select name="per_page" class="form-select form-select-sm" style="width:auto;">

                                    @foreach ([10, 25, 50, 100] as $n)
                                        <option value="{{ $n }}"
                                            {{ (int) request('per_page', 10) === $n ? 'selected' : '' }}>

                                            {{ $n }}

                                        </option>
                                    @endforeach

                                </select>

                                <span class="small text-muted">entries</span>

                            </div>

                        </div>

                    </form>

                </div>

                <div class="ui-divider mt-3"></div>

            </div>

            <div class="card-body">

                <div class="ui-table-wrap">
                    <div class="ui-table-scroll">

                        <table class="table ui-table align-middle mb-0">

                            <thead>
                                <tr>
                                    <th>PVD Number</th>
                                    <th>Date</th>
                                    <th>Destination</th>
                                    <th>Truck</th>
                                    <th>People</th>
                                    <th>Status</th>
                                    <th>Billing</th>
                                    <th>Check Release Date</th>
                                    <th>Payment</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>

                            <tbody>

                                @forelse($trips as $t)
                                    @php
                                        $badge = match ($t->status) {
                                            'Completed' => 'completed',
                                            'Cancelled' => 'cancelled',
                                            default => 'completed',
                                        };
                                    @endphp

                                    <tr>

                                        <td class="fw-semibold">
                                            {{ $t->trip_ticket_no }}
                                        </td>

                                        <td>
                                            {{ \Carbon\Carbon::parse($t->dispatch_date)->format('Y-m-d') }}
                                        </td>

                                        <td>
                                            {{ $t->destination->store_name ?? '-' }}
                                        </td>

                                        <td>
                                            {{ $t->truck->plate_number ?? '-' }}
                                        </td>

                                        <td>

                                            <div class="person-stack">

                                                {{-- DRIVER --}}
                                                @if ($t->driver)
                                                    <div class="person-avatar color-1"
                                                        data-name="{{ $t->driver->name }} (Driver)">
                                                        {{ strtoupper(substr($t->driver->name, 0, 1)) }}
                                                    </div>
                                                @endif

                                            </div>

                                        </td>

                                        <td>
                                            <span class="ui-badge ui-badge-{{ $badge }}">
                                                <span class="ui-dot ui-dot-{{ $badge }}"></span>
                                                {{ $t->status }}
                                            </span>
                                        </td>

                                        <td>

                                            <button type="button" class="btn p-0 billing-toggle"
                                                data-url="{{ route('flash.trips.updateBilling', $t->id) }}">

                                                <span
                                                    class="badge
                                                    {{ $t->billing_status == 'Billed'
                                                        ? 'bg-success'
                                                        : ($t->billing_status == 'Pending'
                                                            ? 'bg-warning text-dark'
                                                            : 'bg-secondary') }}">

                                                    {{ $t->billing_status ?? 'Unbilled' }}
                                                </span>

                                            </button>

                                        </td>

                                        <td>
                                           {{ $t->check_release_date ?? '-' }}
                                        </td>

                                        <td>


                                            @php
                                                $paymentStatus = $t->computed_payment_status ?? 'Unpaid';
                                            @endphp

                                            <span
                                                class="badge
        {{ $paymentStatus == 'Paid'
            ? 'bg-success'
            : ($paymentStatus == 'Partial'
                ? 'bg-warning text-dark'
                : 'bg-secondary') }}">

                                                {{ $paymentStatus }}

                                            </span>


                                        </td>

                                        <td class="text-center d-flex justify-content-center gap-1">

                                            @if (in_array(auth()->user()->role, ['owner', 'it']))
                                                <form action="{{ route('flash.trips.destroy', $t->id) }}" method="POST"
                                                    onsubmit="return confirm('Delete this trip?')">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            <button type="button" class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editBillingModal{{ $t->id }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>

                                        </td>

                                    </tr>

                                @empty

                                    <tr>
                                        <td colspan="10" class="text-center py-5">

                                            <div class="text-muted mb-2">
                                                <i class="bi bi-clock-history fs-3"></i>
                                            </div>

                                            <div class="fw-semibold">
                                                No trip history found
                                            </div>

                                        </td>
                                    </tr>
                                @endforelse

                            </tbody>

                        </table>


                    </div>
                </div>

                {{-- ✅ MODALS HERE --}}
                @foreach ($trips as $t)
                    <div class="modal fade" id="editBillingModal{{ $t->id }}">
                        <div class="modal-dialog">
                            <form method="POST" action="{{ route('flash.trips.updateBilling', $t->id) }}">
                                @csrf
                                @method('PUT')

                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5>Edit Billing</h5>
                                    </div>

                                    <div class="modal-body">
                                        <input type="date" name="check_release_date"
                                            value="{{ $t->check_release_date }}" class="form-control mb-2">

                                        <input type="text" name="bank_name" value="{{ $t->bank_name }}"
                                            class="form-control mb-2" placeholder="Bank Name">

                                        <input type="text" name="check_number" value="{{ $t->check_number }}"
                                            class="form-control" placeholder="Check #">
                                    </div>

                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                            Cancel
                                        </button>

                                        <button type="submit" class="btn btn-primary">
                                            Save
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="card-footer bg-transparent border-0">

                <div class="d-flex justify-content-end">
                    {{ $trips->links('vendor.pagination.ui-datatable') }}
                </div>

            </div>

        </div>

    </div>



@endsection

@push('styles')
    <style>
        body.modal-open .ui-card {
            transform: none !important;
        }

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

        .person-stack {
            display: flex;
            align-items: center;
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
