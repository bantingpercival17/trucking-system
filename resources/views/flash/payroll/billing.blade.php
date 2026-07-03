@extends('layouts.flash')

@section('title', 'Billing')

@section('content')

    <div class="max-w-5xl mx-auto px-4 py-6">

        {{-- Header --}}
        <div class="ui-hero p-4 md:p-6 mb-4">
            <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-3">
                <div>
                    <h4 class="mb-1 text-xl md:text-2xl font-bold">Billing</h4>
                    <div class="text-muted small md:text-sm">
                        Completed trips waiting for billing.
                    </div>
                </div>
            </div>
        </div>


        {{-- Trips Card --}}
        <div class="card ui-card border-0 mt-3 shadow-sm rounded-xl">

            <div class="card-header bg-transparent border-0 pb-0">

                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

                    {{-- LEFT SIDE --}}
                    <div>
                        <h6 class="mb-0 fw-semibold">
                            Completed Trips
                        </h6>

                        <div class="text-muted small mt-1">
                            @if ($trips->total())
                                Showing <strong>{{ $trips->firstItem() }}–{{ $trips->lastItem() }}</strong> /
                                <strong>{{ $trips->total() }}</strong>
                            @else
                                Showing <strong>0</strong> / <strong>0</strong>
                            @endif
                        </div>
                    </div>

                    {{-- RIGHT SIDE --}}
                    <div class="d-flex align-items-center gap-2 flex-wrap ms-auto">

                        {{-- ✅ TRIP HISTORY BUTTON --}}
                        <a href="{{ route('flash.trips.history') }}" class="btn btn-outline-dark ui-btn-equal">
                            <i class="bi bi-clock-history me-1"></i>
                        </a>

                        {{-- FILTER --}}
                        <form class="billing-filter">

                            <div class="d-flex align-items-center gap-2 flex-wrap flex-md-nowrap">

                                <span class="filter-label mb-0">Check Release</span>

                                <input type="date" name="check_date" value="{{ request('check_date') }}"
                                    class="form-control filter-input">

                                <button type="submit" class="btn btn-primary filter-btn">
                                    Filter
                                </button>

                                <a href="{{ route('flash.payroll.billing') }}" class="btn btn-light filter-btn-clear">
                                    Clear
                                </a>

                            </div>

                        </form>

                    </div>
                </div>

                <div class="ui-divider mt-3"></div>
            </div>


            <div class="card-body p-4 md:p-6">

                <div class="row g-3">

                    @forelse ($trips as $t)

                        <div class="col-12 col-md-6 col-lg-4 col-xl-5col">

                            <div class="card shadow-sm border-0 h-80">
                                <div class="card-body d-flex flex-column">

                                    {{-- HEADER --}}
                                    <div class="text-center">

                                        <div class="trip-ticket">
                                            {{ $t->trip_ticket_no }}
                                        </div>

                                        <div class="fw-semibold text-muted small">
                                            {{ $t->destination->store_name ?? '-' }}
                                        </div>

                                        <span class="trip-status delivery">
                                            Delivered
                                        </span>

                                    </div>

                                    <hr class="my-3">


                                    {{-- DETAILS --}}
                                    <div class="small">

                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Date:</span>

                                            <span class="fw-semibold">
                                                {{ \Carbon\Carbon::parse($t->dispatch_date)->format('d/m') }}
                                            </span>
                                        </div>


                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Truck:</span>

                                            <span class="fw-semibold">
                                                {{ $t->truck->plate_number ?? '-' }}
                                            </span>
                                        </div>


                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="text-muted">Persons:</span>

                                            <div class="person-stack">

                                                {{-- DRIVER --}}
                                                <div class="person-avatar" data-name="{{ $t->driver->name ?? '' }}"
                                                    data-initial="{{ strtoupper(substr($t->driver->name ?? '?', 0, 1)) }}">

                                                    {{ strtoupper(substr($t->driver->name ?? '?', 0, 1)) }}

                                                </div>

                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Check Release:</span>

                                            <span
                                                class="fw-semibold {{ $t->check_release_date ? 'text-primary' : 'text-muted' }}">
                                                {{ $t->check_release_date ? \Carbon\Carbon::parse($t->check_release_date)->format('d/m') : 'N/A' }}
                                            </span>
                                        </div>

                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Bank:</span>

                                            <span class="fw-semibold {{ $t->bank_name ? '' : 'text-muted' }}">
                                                {{ $t->bank_name ?? 'N/A' }}
                                            </span>
                                        </div>

                                        <div class="d-flex justify-content-between mb-2">
                                            <span class="text-muted">Check #:</span>

                                            <span class="fw-semibold {{ $t->check_number ? '' : 'text-muted' }}">
                                                {{ $t->check_number ?? 'N/A' }}
                                            </span>
                                        </div>

                                        {{-- STATUS CHIPS --}}
                                        <div class="trip-status-row">
                                            <span
                                                class="trip-status
{{ $t->billing_status == 'Billed'
    ? 'billing-billed'
    : ($t->billing_status == 'Pending'
        ? 'billing-pending'
        : 'billing-unbilled') }}">
                                                {{ $t->billing_status ?? 'Unbilled' }}
                                            </span>

                                            <span class="trip-status payment">
                                                {{ $t->payment_status ?? 'Unpaid' }}
                                            </span>

                                        </div>

                                        <hr class="my-3">

                                    </div>


                                    {{-- ACTION BUTTONS --}}
                                    <div class="trip-actions mt-0">

                                        <div class="d-flex gap-2">

                                            {{-- EDIT BUTTON --}}
                                            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#editBillingModal-{{ $t->id }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>

                                            {{-- DELETE BUTTON --}}
                                            <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#confirmDelete-{{ $t->id }}">
                                                <i class="bi bi-trash"></i>
                                            </button>

                                            {{-- MARK AS BILLED --}}
                                            @if (($t->billing_status ?? 'Unbilled') !== 'Billed')
                                                @if (!$t->check_release_date)
                                                    <button class="btn btn-secondary btn-sm flex-grow-1" disabled>
                                                        Complete Billing First
                                                    </button>
                                                @else
                                                    <button class="btn btn-success btn-sm flex-grow-1"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#billTripModal-{{ $t->id }}">
                                                        Mark as Billed
                                                    </button>
                                                @endif
                                            @endif

                                        </div>

                                        {{-- ⚠️ WARNING MESSAGE --}}
                                        @if (($t->billing_status ?? 'Unbilled') !== 'Billed' && !$t->check_release_date)
                                            <small class="text-danger d-block mt-1">
                                                Set check release date first
                                            </small>
                                        @endif

                                    </div>
                                </div>
                            </div>
                        </div>

                    @empty

                        <div class="text-center py-5">
                            <div class="text-muted mb-2">
                                <i class="bi bi-receipt fs-3"></i>
                            </div>

                            <div class="fw-semibold">
                                No trips ready for billing
                            </div>

                            <div class="text-muted small">
                                Completed trips will appear here.
                            </div>
                        </div>

                    @endforelse

                </div>

            </div>

        </div>

        @foreach ($trips as $t)
            <div class="modal fade" id="confirmDelete-{{ $t->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">

                        <div class="modal-header">
                            <h6 class="modal-title text-danger">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Delete Trip
                            </h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                            Are you sure you want to delete this trip?

                            <div class="mt-2">
                                <strong>{{ $t->trip_ticket_no }}</strong>
                            </div>

                            <div class="text-muted small mt-2">
                                This will also release resources if needed.
                            </div>
                        </div>

                        <div class="modal-footer">

                            <button class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                Cancel
                            </button>

                            <form method="POST" action="{{ route('flash.trips.destroy', $t->id) }}">
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-danger">
                                    Yes, Delete
                                </button>
                            </form>

                        </div>

                    </div>
                </div>
            </div>
        @endforeach
        {{-- Billing Modals --}}
        @foreach ($trips as $t)
            <div class="modal fade" id="billTripModal-{{ $t->id }}" tabindex="-1">

                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">

                        <form method="POST" action="{{ route('flash.trips.complete', $t->id) }}">
                            @csrf

                            <div class="modal-header">
                                <h6 class="modal-title">
                                    Mark Trip as Billed
                                </h6>

                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">

                                <div class="mb-3">
                                    <label class="form-label fw-semibold">
                                        Trip Ticket
                                    </label>

                                    <div class="form-control bg-light">
                                        {{ $t->trip_ticket_no }}
                                    </div>
                                </div>

                                <div class="text-muted small">
                                    This will mark the trip as billed.
                                </div>

                            </div>

                            <div class="modal-footer">

                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                    Cancel
                                </button>

                                <button type="submit" class="btn btn-success">
                                    Confirm Billing
                                </button>

                            </div>

                        </form>

                    </div>
                </div>

            </div>
        @endforeach

        {{-- Pagination --}}
        <div class="card-footer bg-transparent border-0 pt-0">
            <div class="d-flex justify-content-start justify-content-lg-end">
                {{ $trips->onEachSide(1)->links('vendor.pagination.ui-datatable') }}
            </div>
        </div>


    </div>


    @foreach ($trips as $t)
        <div class="modal fade" id="editBillingModal-{{ $t->id }}" tabindex="-1">

            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">

                    <form method="POST" action="{{ route('flash.trips.updateBilling', $t->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="modal-header">
                            <h6 class="modal-title">
                                Edit Billing Details
                            </h6>

                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Check Release Date
                                </label>

                                <input type="date" name="check_release_date" value="{{ $t->check_release_date }}"
                                    class="form-control">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Bank Name
                                </label>

                                <input type="text" name="bank_name" value="{{ $t->bank_name }}"
                                    class="form-control">
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Check Number
                                </label>

                                <input type="text" name="check_number" value="{{ $t->check_number }}"
                                    class="form-control">
                            </div>

                        </div>

                        <div class="modal-footer">

                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                Cancel
                            </button>

                            <button type="submit" class="btn btn-primary">
                                Save Changes
                            </button>

                        </div>

                    </form>

                </div>
            </div>

        </div>
    @endforeach

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

        .billing-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .billing-billed {
            background: #e0f2fe;
            color: #0369a1;
        }

        .na-value {
            color: #9ca3af;
            font-style: italic;
        }

        .billing-filter {
            background: #f9fafb;
            padding: 10px;
            border-radius: 12px;
            border: 1px solid #edf0f4;
            width: auto;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            flex: 1;
            min-width: 220px;
        }

        .filter-group-horizontal {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .filter-label {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 2px;
            font-weight: 600;
            white-space: nowrap;
        }

        .filter-input {
            height: 40px;
            border-radius: 10px;
            border: 1px solid #e5e7eb;
            width: 160px;
        }

        .filter-input:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 0.15rem rgba(99, 102, 241, 0.15);
        }

        .filter-btn {
            height: 40px;
            border-radius: 10px;
            font-weight: 600;
            padding: 0 18px;
        }

        .filter-btn-clear {
            height: 40px;
            border-radius: 10px;
            font-weight: 500;

            display: flex;
            align-items: center;
            justify-content: center;
        }


        /* MOBILE */
        @media (max-width: 576px) {

            .billing-filter {
                width: 100%;
            }

            .filter-input {
                width: 100%;
            }

            .filter-btn,
            .filter-btn-clear {
                width: 100%;
                flex: 1;
            }

            .filter-label {
                margin-bottom: 4px;
            }

        }
    </style>
@endpush

@push('scripts')
    <script>
        // pagination for available lists
        document.addEventListener('DOMContentLoaded', function() {
            function initPaginatedList(container, key) {
                const perPage = parseInt(container.dataset.perPage || "5", 10);
                const items = Array.from(container.querySelectorAll('.ui-list-item'));
                const controls = document.querySelector(`.ui-list-controls[data-controls="${key}"]`);

                if (!items.length) return;

                let page = 1;
                const totalPages = Math.ceil(items.length / perPage);

                function render() {
                    const start = (page - 1) * perPage;
                    const end = start + perPage;

                    items.forEach((el, idx) => {
                        el.style.display = (idx >= start && idx < end) ? '' : 'none';
                    });

                    if (controls) {
                        controls.querySelector('.ui-list-page').textContent = `${page} / ${totalPages}`;
                        controls.querySelector('.ui-list-prev').disabled = page <= 1;
                        controls.querySelector('.ui-list-next').disabled = page >= totalPages;
                    }
                }

                if (controls) {
                    controls.querySelector('.ui-list-prev').addEventListener('click', function() {
                        if (page > 1) {
                            page--;
                            render();
                        }
                    });

                    controls.querySelector('.ui-list-next').addEventListener('click', function() {
                        if (page < totalPages) {
                            page++;
                            render();
                        }
                    });
                }

                render();
            }

            document.querySelectorAll('.ui-paginated-list').forEach(list => {
                initPaginatedList(list, list.dataset.target);
            });

            // ✅ Trips per_page dropdown (server-side Laravel pagination)
            const perPageSelect = document.querySelector('select[name="per_page"]');
            if (perPageSelect && perPageSelect.form) {
                perPageSelect.addEventListener('change', function() {
                    const form = this.form;

                    // remove page so it goes back to page 1 when changing per_page
                    const pageInput = form.querySelector('input[name="page"]');
                    if (pageInput) pageInput.remove();

                    form.submit();
                });
            }

            // Toggle collapse with icon change
            document.querySelectorAll('.collapse-toggle').forEach(btn => {

                const targetSelector = btn.dataset.target;
                const targetEl = document.querySelector(targetSelector);

                if (!targetEl) return;

                const collapseInstance = new bootstrap.Collapse(targetEl, {
                    toggle: false
                });

                btn.addEventListener('click', function() {

                    const isOpen = targetEl.classList.contains('show');

                    if (isOpen) {
                        collapseInstance.hide();
                        btn.querySelector('i').classList.remove('bi-eye-slash');
                        btn.querySelector('i').classList.add('bi-eye');
                    } else {
                        collapseInstance.show();
                        btn.querySelector('i').classList.remove('bi-eye');
                        btn.querySelector('i').classList.add('bi-eye-slash');
                    }

                });

            });

            function applyAvatarColors() {

                document.querySelectorAll('.person-avatar').forEach(function(el) {

                    const initial = el.dataset.initial || "A";

                    const index = (initial.charCodeAt(0) % 8) + 1;

                    el.classList.add("color-" + index);

                });

            }

            applyAvatarColors();
        });
    </script>
@endpush
