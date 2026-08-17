@extends('layouts.appV2')
@section('title', 'VEHICLES (TRUCKS)')
@section('sub-title', 'VEHICLES')
@php
    $params = request()->route('company') ?: 'all';
@endphp
@section('content')
    <div class="container-fluid py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-transparent border-0 pb-0">
                <div
                    class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                    <div class="ui-trips-head-left">
                        <h3 class="mb-0 fw-bolder text-primary">List of Vehicles</h3>
                        <div class="text-info fw-bolder small">
                            Details of the Vehicles (Trucks) available in the system.
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
                <div
                    class="mt-3 d-flex flex-column flex-lg-row gap-2 align-items-stretch align-items-lg-center justify-content-between">
                    <form method="GET" action="{{ route('admin.vehicles.index') }}"
                        class="d-flex flex-column flex-sm-row gap-2 align-items-stretch align-items-sm-center m-0 flex-grow-1">

                        <div class="ui-search ui-header-search" style="max-width: 520px; width: 100%;">
                            <input type="text" name="q" value="{{ request('q') }}"
                                class="form-control border border-primary ui-search-input"
                                placeholder="Search Plate Number, Truck Type, or Driver Name">
                        </div>



                        @if (request('sort'))
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                        @endif
                        @if (request('dir'))
                            <input type="hidden" name="dir" value="{{ request('dir') }}">
                        @endif

                        @if (request('q'))
                            <a href="{{ route('admin.vehicles.index') }}"
                                class="btn btn-outline-secondary btn-sm ui-pill-btn ui-btn-equal">
                                Clear
                            </a>
                        @endif


                    </form>

                    <div class="text-muted small mt-1 ui-showing">
                        {{--  @if ($vehicles->total())
                            Showing <strong>{{ $vehicles->firstItem() }}–{{ $vehicles->lastItem() }}</strong>
                            /
                            <strong>{{ $vehicles->total() }}</strong>
                        @else
                            Showing <strong>0</strong> / <strong>0</strong>
                        @endif --}}
                    </div>
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
                    <div class="d-flex flex-column flex-sm-row gap-2">
                        <button type="button" class="btn ui-btn ui-btn-add btn-primary" data-bs-toggle="modal"
                            data-bs-target="#createTruckModal">
                            <i class="bi bi-plus-lg me-1"></i> Add
                        </button>
                    </div>
                </div>
                <div class="d-flex justify-content-start justify-content-lg-end mt-3">
                    <ul class="nav nav-pills mb-3 gap-2">
                        <li class="nav-item">
                            <a class="nav-link {{ !request('type') ? 'active' : '' }}"
                                href="{{ route('admin.vehicles.index', array_filter(['q' => request('q')])) }}">
                                All
                            </a>
                        </li>
                        @foreach ($truckTypes as $item)
                            <li class="nav-item">
                                <a class="nav-link {{ request('type') === $item ? 'active' : '' }}" {{-- 'admin.vehicles.index', ['company' => $params] --}}
                                    href="{{ route('admin.vehicles.index', ['company' => $params, 'q' => request('q'), 'type' => $item]) }}">
                                    {{ $item }} <span
                                        class="badge bg-light text-dark ms-1">{{ $counts[$item] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <div class="ms-auto">
                        {{ $vehicles->onEachSide(1)->links('vendor.pagination.ui-datatable') }}
                    </div>
                </div>
                <div class="ui-divider mt-3"></div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr class="text-uppercase fw-bold">
                                <th>PLATE NUMBER
                                    <br>
                                    <small class="text-muted">CONTACT NUMBER</small>
                                </th>
                                <th>STATUS</th>
                                <th>TRUCK TYPE</th>
                                <th width="120">ACTION</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($vehicles as $vehicle)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $vehicle->plate_number }}</div>
                                        <small class="text-muted">{{ $vehicle->company_number }}</small>
                                    </td>
                                    <td>
                                        @php
                                            $status = $vehicle->status ?? '';
                                        @endphp
                                        <span
                                            class="ui-badge 
                                                                {{ $status === 'active' ? 'ui-badge-completed' : '' }}
                                                                {{ $status === 'inactive' ? 'ui-badge-cancelled' : '' }}
                                                                {{ $status === 'on_trip' ? 'ui-badge-primary' : '' }}
                                                            ">
                                            <span
                                                class="ui-dot 
                                                                {{ $status === 'active' ? 'ui-dot-completed' : '' }}
                                                                {{ $status === 'inactive' ? 'ui-dot-cancelled' : '' }}
                                                                {{ $status === 'on_trip' ? 'ui-dot-dispatched' : '' }}
                                                            "></span>
                                            {{ $vehicle->status ?? ucfirst($vehicle->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $vehicle->truck_type }}</td>
                                    <td>
                                        <div class="d-inline-flex gap-2">
                                            <button class="btn btn-sm btn-warning ui-icon-btn" data-bs-toggle="modal"
                                                data-bs-target="#edit-vehicle{{ $vehicle->id }}-modal" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </button>



                                            <button class="btn btn-sm btn-info ui-icon-btn" data-bs-toggle="modal"
                                                data-bs-target="#view-vehicle{{ $vehicle->id }}-modal"
                                                title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>

                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        No vehicles found.
                                    </td>
                                </tr>
                            @endforelse
                    </table>
                </div>
            </div>
        </div>
        <div class="modal fade" id="createTruckModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-centered">
                <div class="modal-content border-0 shadow">

                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold">ADD TRUCK / VEHICLE</h5>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <form method="POST" action="{{ route('admin.vehicles.store') }}">
                            @csrf

                            <div class="mb-3">
                                <small class="fw-bold text-primary">PLATE NUMBER <sup class="text-danger">*</sup></small>
                                <input class="form-control border border-primary text-primary" name="plate_number"
                                    placeholder="e.g. ABC-1234" required>
                            </div>
                            <div class="mb-3">
                                <small class="fw-bold text-primary">COMPANY CONTACT NUMBER <sup
                                        class="text-info">(OPTIONAL)</sup></small>
                                <input class="form-control border border-primary text-primary" name="company_number"
                                    placeholder="e.g. 09171234567">
                            </div>
                            <div class="mb-3">
                                <small class="fw-bold text-primary">TRUCK TYPE <sup class="text-danger">*</sup></small>
                                <select class="form-select border border-primary text-primary" name="truck_type" required>
                                    <option value="" disabled selected>Select type</option>
                                    @foreach ($truckTypes as $item)
                                        <option value="{{ $item }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <small class="fw-bold text-primary">STATUS <sup class="text-danger">*</sup></small>
                                <select class="form-select border border-primary text-primary" name="status">
                                    <option value="active" selected>Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-outline-secondary ui-pill-btn"
                                    data-bs-dismiss="modal">
                                    Cancel
                                </button>
                                <button class="btn btn-primary ui-pill-btn">Save Truck</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
        @include('version2.vehicles.components.edit')
        @include('version2.vehicles.components.view')
    </div>
@endsection
@push('styles')
    <style>
        /* Reuse the same premium UI system from Trips page */
        .ui-card {
            border-radius: 18px;
            box-shadow: 0 14px 40px rgba(16, 24, 40, .08);
            transition: all .25s ease;
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

        .ui-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 50px rgba(16, 24, 40, .12);
        }

        .ui-divider {
            height: 1px;
            background: #edf0f4;
            width: 100%;
        }

        .ui-tabs {
            display: flex;
            gap: 10px;
            border-bottom: 1px solid #edf0f4;
            padding-bottom: 10px;
        }

        .ui-tabs .nav-link {
            border: 1px solid #edf0f4;
            background: #fff;
            color: #344054;
            border-radius: 999px;
            padding: .45rem .9rem;
            font-weight: 700;
            font-size: .85rem;
        }

        .ui-tabs .nav-link.active {
            background: #f4f8ff;
            border-color: #cfe1ff;
            color: #175cd3;
        }

        .ui-pill-btn {
            border-radius: 999px;
            padding: .45rem .90rem;
        }

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
            overflow: hidden;
            background: #fff;
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

        /* Status badge + dot */
        .ui-badge {
            display: inline-flex;
            align-items: center;
            padding: .25rem .6rem;
            border-radius: 999px;
            font-size: .78rem;
            font-weight: 600;
            border: 1px solid transparent;
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

        .ui-dot-completed {
            background: #027a48;
        }

        .ui-dot-cancelled {
            background: #b42318;
        }

        .ui-badge-primary {
            background: #e8f1ff;
            color: #175cd3;
            border-color: #cfe1ff;
        }

        .ui-dot-dispatched {
            background: #175cd3;
        }

        /* Base icon button */
        .ui-icon-btn {
            border-radius: 12px;
            border: 1px solid transparent;
            background: #f9fafb;
            padding: .45rem .65rem;
            transition: all .2s ease;
        }

        /* EDIT */
        .ui-icon-btn.btn-warning {
            background: #fff7ed;
            color: #b45309;
            border-color: #fde68a;
        }

        .ui-icon-btn.btn-warning:hover {
            background: #ffedd5;
        }

        /* DELETE */
        .ui-icon-btn.btn-danger,
        .ui-icon-btn.delete-btn {
            background: #fef2f2;
            color: #b91c1c;
            border-color: #fecaca;
        }

        .ui-icon-btn.btn-danger:hover {
            background: #fee2e2;
        }

        /* VIEW */
        .ui-icon-btn.btn-info {
            background: #eff6ff;
            color: #1d4ed8;
            border-color: #bfdbfe;
        }

        .ui-icon-btn.btn-info:hover {
            background: #dbeafe;
        }

        /* KPI indicator top borders */
        .ui-indicator {
            position: relative;
            overflow: hidden;
        }

        .ui-indicator::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            border-radius: 18px 18px 0 0;
        }

        .ui-indicator-neutral::before {
            background: #94a3b8;
        }

        .ui-indicator-primary::before {
            background: #0d6efd;
        }

        .ui-indicator-success::before {
            background: #198754;
        }

        .ui-indicator-warning::before {
            background: #ffc107;
        }

        .ui-indicator-danger::before {
            background: #dc3545;
        }

        .ui-section-pill {
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            padding: .35rem .7rem;
            border-radius: 999px;
            border: 1px solid #edf0f4;
            background: #fff;
            font-weight: 800;
            font-size: .85rem;
            color: #344054;
        }

        .ui-section-pill {
            border: 1px solid #edf0f4;
            background: #fff;
            color: #344054;
            border-radius: 999px;
            padding: .35rem .75rem;
            font-weight: 800;
            font-size: .80rem;
            display: inline-flex;
            align-items: center;
        }

        /* same scroll behavior from your trips page */
        .ui-table-scroll {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* keep table usable and allow horizontal scroll if needed */
        .ui-table {
            min-width: 520px;
        }

        /* prevent pager from being cut */
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

        /* Compact KPI cards */
        .ui-kpi-card .card-body {
            padding: 25px 10px;
            /* smaller height */
        }

        .ui-kpi-number {
            font-size: 1.9rem;
            /* bigger number */
            font-weight: 800;
            line-height: 1.1;
        }

        /* Make numbers even bigger on desktop */
        @media (min-width: 992px) {
            .ui-kpi-number {
                font-size: 2.2rem;
            }
        }

        /* ===== Mobile Trucks Layout (same pattern as destinations) ===== */
        .ui-mobile-truck {
            border-radius: 16px;
            transition: .2s ease;
        }

        .ui-mobile-truck:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(16, 24, 40, .08);
        }

        .ui-truck-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .ui-truck-name {
            font-weight: 700;
            font-size: 1rem;
            line-height: 1.25;
            word-break: break-word;
        }

        .ui-truck-type {
            font-size: .7rem;
            font-weight: 800;
            padding: .25rem .6rem;
            border-radius: 999px;
            letter-spacing: .02em;
        }

        /* 6W */
        .ui-truck-type.type-6w {
            background: #eff6ff;
            color: #1d4ed8;
        }

        /* L300 */
        .ui-truck-type.type-l300 {
            background: #ecfdf5;
            color: #047857;
        }

        .ui-truck-meta {
            font-size: .85rem;
        }

        .ui-truck-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            border-top: 1px solid #f1f3f6;
        }

        .ui-truck-row:first-child {
            border-top: none;
        }

        .ui-truck-label {
            color: #98a2b3;
        }

        .ui-truck-value {
            font-weight: 600;
        }

        /* ===== Header Action Buttons ===== */
        .ui-header-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        /* Desktop */
        @media (min-width: 992px) {
            .ui-header-actions {
                flex-direction: row;
            }

            .ui-header-actions .btn {
                min-width: 150px;
            }
        }

        /* Mobile */
        /* ===== Header Action Buttons ===== */
        .ui-header-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        /* Mobile only */
        @media (max-width: 767.98px) {
            .ui-header-actions {
                flex-direction: column;
                width: 100%;
                margin-top: 16px;
            }

            .ui-header-actions .btn {
                width: 100%;
            }
        }

        /* Tablet and up */
        @media (min-width: 768px) {
            .ui-header-actions {
                flex-direction: row;
            }

            .ui-header-actions .btn {
                min-width: 150px;
            }
        }
    </style>
@endpush
