@extends('layouts.appV2')
@section('title', 'Billing')
@section('sub-title', 'Billing')
@php
    $params = request()->route('company') ?: 'all';
@endphp
@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            {{-- <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body d-flex flex-column justify-content-center">
                        <div class="text-muted small mb-1">
                            Total Billing Amount
                        </div>
                        <h3 class="fw-bold mb-0 text-primary">
                            ₱{{ number_format($dashboardData['totalBilledAmount'], 2) }}
                        </h3>
                        <div class="small text-muted mt-2">
                            {{ request('check_date') ? \Carbon\Carbon::parse(request('check_date'))->format('F d, Y') : 'Billing total' }}
                        </div>

                    </div>

                </div>
            </div> --}}
            <div class="col">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body  row">
                        <div class="billed col">
                            <div class="text-muted small mb-1">
                                BILLED AMOUNT
                            </div>
                            <h3 class="fw-bold mb-0 text-primary">
                                ₱{{ number_format($dashboardData['billedTrips'], 2) }}
                            </h3>
                            <div class="small text-muted mt-2">
                                BILLED TRIPS {{ $dashboardData['billedTripsCount'] }}
                            </div>
                        </div>
                        <div class="pending col">
                            <div class="text-muted small mb-1">
                                PENDING AMOUNT
                            </div>
                            <h3 class="fw-bold mb-0 text-primary">
                                ₱{{ number_format($dashboardData['pendingTrips'], 2) }}
                            </h3>
                            <div class="small text-muted mt-2">
                                PENDING TRIPS {{ $dashboardData['pendingTripsCount'] }}
                            </div>
                        </div>
                        <div class="unbilled col">
                            <div class="text-muted small mb-1">
                                UNBILLED AMOUNT
                            </div>
                            <h3 class="fw-bold mb-0 text-primary">
                                ₱{{ number_format($dashboardData['unbilledTrips'], 2) }}
                            </h3>
                            <div class="small text-muted mt-2">
                                UNBILLED TRIPS {{ $dashboardData['unbilledTripsCount'] }}
                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>
        <div class="card shadow-sm">
            <div class="card-header bg-transparent border-0 pb-0">
                <div
                    class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                    <div class="ui-trips-head-left">
                        <h3 class="mb-0 fw-bolder text-primary">Billing</h3>
                        <div class="text-info fw-bolder small">
                            Billing information for dispatched trips. You can update the billing details for each trip here.
                        </div>

                    </div>
                    @session('error')
                        <div class="alert alert-danger alert-dismissible fade show m-0" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endsession
                    @session('success')
                        <div class="alert alert-success alert-dismissible fade show m-0" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endsession
                </div>

                {{-- Controls --}}
                <form method="GET" action="{{ route('company.billing.show', ['company' => $params]) }}" class="row">

                    <div class="ui-search ui-header-search col">
                        <input type="text" name="q" value="{{ request('q') }}"
                            class="form-control border border-primary ui-search-input"
                            placeholder="Search destination, area, origin...">
                    </div>
                    <div class="col">
                        <small class="fw-semibold text-muted">Payment Status</small>
                        <select name="status" class="col form-control form-select border border-primary rounded"
                            onchange="this.form.submit()">
                            <option value="a" {{ request('status') == '' ? 'selected' : 'All' }}>
                                All
                            </option>
                            <option value="Billed" {{ request('status') == 'Billed' ? 'selected' : '' }}>
                                Billed
                            </option>
                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>
                                Pending
                            </option>
                            <option value="Unbilled" {{ request('status') == 'Unbilled' ? 'selected' : '' }}>
                                Unbilled
                            </option>

                        </select>
                    </div>

                    <div class="col">
                        <small class="fw-semibold text-muted">Check Release</small>
                        <input type="date" name="check_date" value="{{ request('check_date') }}"
                            class="form-control filter-input" onchange="this.form.submit()">
                    </div>



                    <div class="d-flex justify-content-start justify-content-lg-end mt-3">
                        <div class="text-muted small  ui-showing me-2 me-lg-3">
                            @if ($dispatchList->total())
                                Showing
                                <strong>{{ $dispatchList->firstItem() }}–{{ $dispatchList->lastItem() }}</strong>
                                /
                                <strong>{{ $dispatchList->total() }}</strong>
                            @else
                                Showing <strong>0</strong> / <strong>0</strong>
                            @endif

                        </div>
                        <div class="ui-trips-head-right">
                            <div class="d-flex align-items-center gap-2">
                                <label class="small text-muted m-0">Show</label>

                                <select name="per_page" class="form-select form-select-sm" style="width:auto;"
                                    onchange="this.form.submit()">
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
                        <div class="ms-auto">
                            {{ $dispatchList->onEachSide(1)->links('vendor.pagination.ui-datatable') }}
                        </div>
                    </div>
                </form>
                <div class="ui-divider mt-3"></div>
            </div>
            <div class="card-body">
                <div id="list-view-container">
                    @forelse ($dispatchList as $item)
                        <div
                            class="card h-100 rounded-4 p-4 trip-card bg-white d-flex flex-column justify-content-between gap-3 border border-info">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <span
                                        class="badge bg-{{ $item->company->badge_color }} px-2.5 py-1.5 rounded-3 fw-bold">{{ $item->company->name }}</span>
                                    <span class="badge bg-info">{{ $item->trip_ticket_no ?? '-' }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-1.5 text-muted small fw-medium">
                                    @if (in_array($item->dispatch_status, ['Draft', 'Dispatched', 'Assigned', 'Completed']))
                                        @php
                                            $statusClass = strtolower($item->dispatch_status);
                                        @endphp

                                        <small class="ui-badge ui-badge-{{ $statusClass }}">
                                            <small class="ui-dot ui-dot-{{ $statusClass }}"></small>
                                            {{ $item->dispatch_status }}
                                        </small>
                                    @endif

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md  dispatch-action">
                                    @php
                                        $color =
                                            $item->billing_status == true
                                                ? 'primary'
                                                : ($item->billing_status == 0
                                                    ? 'success'
                                                    : 'warning');
                                    @endphp

                                    <div class="rounded-4 small">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <div
                                                    class="badge bg-{{ $color }}-subtle text-{{ $color }} border border-{{ $color }}-subtle py-2 rounded-3 d-flex align-items-center gap-1.5 align-self-start fw-bold fs-7">
                                                    <svg width="14" height="14" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Billing Status:
                                                    {{ $item->billing_status ? 'Billed' : 'Pending' ?? 'Unbilled' }}
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <span class="text-muted d-block small">CHECK RELEASE DATE</span>
                                                <span
                                                    class="text-dark fw-bold d-block mt-0.5">{{ $item->check_release_date ? \Carbon\Carbon::parse($item->check_release_date)->format('F d,Y') : '-' }}</span>
                                            </div>
                                            <div class="col-6 border-top border-light-subtle pt-2 mt-2">
                                                <span class="text-muted d-block small mb-1">BANK NAME</span>
                                                <span class="text-dark fw-bold d-block mt-0.5 text-truncate">
                                                    {{ $item->bank_name ?: '-' }}
                                                </span>
                                            </div>
                                            <div class="col-6 border-top border-light-subtle pt-2 mt-2">
                                                <span class="text-muted d-block small mb-1">CHECK NO.</span>
                                                <span class="text-dark fw-bold d-block mt-0.5 text-truncate">
                                                    {{ $item->check_number ?: '-' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    @if ($item->billing_status != 1)
                                        <div class="action-button mt-2 row">
                                            <div class="col-md">
                                                <button type="button" class="btn btn-sm btn-outline-primary w-100"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editBillingModal{{ $item->id }}">
                                                    <i class="bi bi-money"></i> Posting Check Payments
                                                </button>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                                <div class="col-md destination-information">
                                    <span class="text-uppercase fw-bold text-muted small tracking-wider d-block">
                                        Destination
                                    </span>
                                    <h4 class="h5 fw-bold text-dark mt-1 mb-1">{{ $item->destination->destinationName() }}
                                    </h4>
                                    <p class="text-muted small mb-0 d-flex align-items-center gap-1">
                                        <svg width="14" height="14" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                            </path>
                                        </svg>
                                        {{ $item->dispatch_date ? \Carbon\Carbon::parse($item->dispatch_date)->format('F d,Y') : '-' }}
                                    </p>
                                </div>
                                <div class="col-md dispatch-employee">
                                    <div class="rounded-4 small">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <span class="text-muted d-block small">TRUCK</span>
                                                <span
                                                    class="text-dark fw-bold d-block mt-0.5">{{ $item->truck->plate_number }}</span>
                                            </div>
                                            <div class="col-6">
                                                <span class="text-muted d-block small">DRIVER</span>
                                                <span class="text-dark fw-bold d-block mt-0.5 text-truncate">
                                                    {{ $item->driver->name }}
                                                </span>
                                            </div>
                                            <div class="col-12 border-top border-light-subtle pt-2 mt-2">
                                                <span class="text-muted d-block small mb-1">HELPERS(s)</span>
                                                <div class="d-flex flex-wrap gap-1">
                                                    @if ($item->helper1)
                                                        <span class="badge bg-white text-dark border px-2 py-1 rounded">
                                                            {{ $item->helper1->name }}
                                                        </span>
                                                    @endif
                                                    @if ($item->helper2)
                                                        <span class="badge bg-white text-dark border px-2 py-1 rounded">
                                                            {{ $item->helper2->name }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div
                            class="card h-100 rounded-4 p-4 trip-card bg-white text-center d-flex flex-column justify-content-between gap-3">
                            <div class="card-body">
                                <div class="text-muted mb-2">
                                    <i class="bi bi-clock-history fs-3"></i>
                                </div>

                                <div class="fw-semibold">
                                    No trip history found
                                </div>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- ✅ MODALS HERE --}}
            @foreach ($dispatchList as $t)
                <div class="modal fade" id="editBillingModal{{ $t->id }}">
                    <div class="modal-dialog">
                        <form method="POST" action="{{ route('admin.dispatch.updateBilling', $t->id) }}">
                            @csrf
                            @method('PUT')

                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5>Edit Billing</h5>
                                </div>

                                <div class="modal-body">
                                    <input type="date" name="check_release_date" value="{{ $t->check_release_date }}"
                                        class="form-control mb-2">

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
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.dataset.id;
                    const name = this.dataset.name;
                    document.getElementById('deleteName').textContent = name;
                    const form = document.getElementById('deleteForm');
                    form.action = form.action.replace('__ID__', id);
                    new bootstrap.Modal(
                        document.getElementById('deleteModal')
                    ).show();
                });
            });
        });
    </script>
@endpush

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
