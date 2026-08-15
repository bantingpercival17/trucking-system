@extends('layouts.appV2')
@section('title', 'Destinations')
@section('sub-title', 'DESTINATIONS')
@php
    $params = request()->route('company') ?: 'all';
@endphp
@section('content')
    <div class="container-fluid py-4">

        {{-- TRUCK TYPE TABS --}}
        {{--  <ul class="nav nav-pills mb-3 gap-2">
            <li class="nav-item">
                <a class="nav-link {{ !request('type') ? 'active' : '' }}"
                    href="{{ route('watson.destinations.index', array_filter(['q' => request('q')])) }}">
                    All
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('type') === '6W' ? 'active' : '' }}"
                    href="{{ route('watson.destinations.index', array_filter(['q' => request('q'), 'type' => '6W'])) }}">
                    6W <span class="badge bg-light text-dark ms-1">{{ $counts['6W'] }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('type') === '4W' ? 'active' : '' }}"
                    href="{{ route('watson.destinations.index', array_filter(['q' => request('q'), 'type' => '4W'])) }}">
                    4W <span class="badge bg-light text-dark ms-1">{{ $counts['4W'] }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('type') === 'AUV' ? 'active' : '' }}"
                    href="{{ route('watson.destinations.index', array_filter(['q' => request('q'), 'type' => 'AUV'])) }}">
                    AUV <span class="badge bg-light text-dark ms-1">{{ $counts['AUV'] }}</span>
                </a>
            </li>
        </ul> --}}

        <div class="card shadow-sm">
            <div class="card-header bg-transparent border-0 pb-0">
                <div
                    class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">
                    <div class="ui-trips-head-left">
                        <h3 class="mb-0 fw-bolder text-primary">Trip Destinations Fees</h3>
                        <div class="text-info fw-bolder small">
                            Routes & Rate Card — 6W / 4W / AUV, planning and costing.
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
                    <form method="GET" action="{{ route('company.destinations.index', ['company' => $params]) }}"
                        class="d-flex flex-column flex-sm-row gap-2 align-items-stretch align-items-sm-center m-0 flex-grow-1">

                        <div class="ui-search ui-header-search" style="max-width: 520px; width: 100%;">
                            <input type="text" name="q" value="{{ request('q') }}"
                                class="form-control border border-primary ui-search-input"
                                placeholder="Search destination, area, origin...">
                        </div>



                        @if (request('sort'))
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                        @endif
                        @if (request('dir'))
                            <input type="hidden" name="dir" value="{{ request('dir') }}">
                        @endif

                        @if (request('q'))
                            <a href="{{ route('company.destinations.index', ['company' => $params]) }}"
                                class="btn btn-outline-secondary btn-sm ui-pill-btn ui-btn-equal">
                                Clear
                            </a>
                        @endif


                    </form>

                    <div class="text-muted small mt-1 ui-showing">
                        @if ($destinations->total())
                            Showing <strong>{{ $destinations->firstItem() }}–{{ $destinations->lastItem() }}</strong>
                            /
                            <strong>{{ $destinations->total() }}</strong>
                        @else
                            Showing <strong>0</strong> / <strong>0</strong>
                        @endif
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
                        <button type="button" class="btn ui-btn ui-btn-add" data-bs-toggle="modal"
                            data-bs-target="#addDestinationModal">
                            <i class="bi bi-plus-lg me-1"></i> Add
                        </button>
                    </div>
                </div>
                <div class="d-flex justify-content-start justify-content-lg-end mt-3">
                    <ul class="nav nav-pills mb-3 gap-2">
                        <li class="nav-item">
                            <a class="nav-link {{ !request('type') ? 'active' : '' }}"
                                href="{{ route('watson.destinations.index', array_filter(['q' => request('q')])) }}">
                                All
                            </a>
                        </li>
                        @foreach ($truckTypes as $item)
                            <li class="nav-item">
                                <a class="nav-link {{ request('type') === $item ? 'active' : '' }}" {{-- 'company.destinations.index', ['company' => $params] --}}
                                    href="{{ route('company.destinations.index', ['company' => $params, 'q' => request('q'), 'type' => $item]) }}">
                                    {{ $item }} <span
                                        class="badge bg-light text-dark ms-1">{{ $counts[$item] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                    <div class="ms-auto">
                        {{ $destinations->onEachSide(1)->links('vendor.pagination.ui-datatable') }}
                    </div>
                </div>
                <div class="ui-divider mt-3"></div>
            </div>
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>COMPANY</th>
                                <th>DESTINATION CODE</th>
                                <th>STORE NAME</th>
                                <th>AREA</th>
                                <th>TRUCK TYPE</th>
                                <th>RATE</th>
                                <th>REMARKS</th>
                                <th width="120">ACTION</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($destinations as $d)
                                <tr class="position-relative">
                                    <td class="text-{{ $d->company->badge_color }} fw-bolder">
                                        {{ $d->company->name ?? '-' }}</td>
                                    <td class="text-info fw-bolder">{{ $d->destination_code ?? '-' }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $d->store_name ?? '-' }}</div>
                                    </td>
                                    <td class="text-info fw-bolder">{{ $d->area ?? '-' }}</td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-dark">{{ $d->truck_type }}</span>
                                    </td>
                                    <td class="fw-bold text-info">
                                        ₱ {{ number_format($d->rate, 2) }}
                                    </td>
                                    <td class="text-info fw-bolder">
                                        {{ $d->remarks ?? '-' }}
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                data-bs-target="#edit-{{ $d->id }}">
                                                ✏️
                                            </button>
                                            <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $d->id }}"
                                                data-name="{{ $d->area }} ({{ $d->truck_type }})">
                                                🗑️
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                {{-- EDIT MODAL --}}
                                <div class="modal fade" id="edit-{{ $d->id }}">
                                    <div class="modal-dialog modal-md modal-dialog-centered">
                                        <div class="modal-content">

                                            <div class="modal-header">
                                                <h5 class="text-info fw-bolder">EDIT DESTINATION</h5>
                                                <button class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <form method="POST"
                                                action="{{ route('company.destinations.update', ['company' => $params, 'destination' => $d->id]) }}">
                                                @csrf
                                                @method('PUT')

                                                <div class="modal-body">
                                                    <small class="text-muted">COMPANY <sup
                                                            class="text-danger">*</sup></small>
                                                    <select name="company_id" id=""
                                                        class="form-select border border-primary" required>
                                                        <option value="" disabled selected>Select Company
                                                        </option>
                                                        @foreach ($companyList as $company)
                                                            <option value="{{ $company->id }}"
                                                                {{ $d->company_id == $company->id ? 'selected' : '' }}>
                                                                {{ $company->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <div class="form-group">
                                                        <small class="text-muted">CODE <sup
                                                                class="text-info">(OPTIONAL)</sup></small>
                                                        <input class="form-control border border-primary text-primary"
                                                            name="destination_code" value="{{ $d->destination_code }}"
                                                            placeholder="e.g. PMP (optional)">
                                                    </div>
                                                    <div class="form-group">
                                                        <small class="text-muted">STORE NAME <sup
                                                                class="text-info">(OPTIONAL)</sup></small>
                                                        <input class="form-control border border-primary text-primary"
                                                            name="store_name" value="{{ $d->store_name }}"
                                                            placeholder="e.g. PUREGOLD (optional)">
                                                    </div>
                                                    <div class="form-group">
                                                        <small class="text-muted">DESTINATION / AREA<sup
                                                                class="text-danger">*</sup></small>
                                                        <input class="form-control border border-primary text-primary"
                                                            name="area" value="{{ $d->area }}"
                                                            placeholder="e.g. BALIWAG BULACAN">
                                                    </div>

                                                    <div class=form-group>
                                                        <label class="form-label small text-info fw-bolder">Truck
                                                            Type</label>
                                                        <select class="form-select" name="truck_type" required>
                                                            @foreach ($truckTypes as $type)
                                                                <option value="{{ $type }}"
                                                                    {{ $d->truck_type === $type ? 'selected' : '' }}>
                                                                    {{ $type }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class=form-group>
                                                        <label class="form-label small text-info fw-bolder">Rate</label>
                                                        <input type="number" step="0.01"
                                                            class="form-control border border-primary text-primary"
                                                            name="rate" value="{{ $d->rate }}" required>
                                                    </div>

                                                    <div class=form-group>
                                                        <label class="form-label small text-info fw-bolder">Remarks</label>
                                                        <input class="form-control border border-primary text-primary"
                                                            name="remarks" placeholder="Remarks (optional)"
                                                            value="{{ $d->remarks }}">
                                                    </div>

                                                </div>

                                                <div class="modal-footer">
                                                    <button class="btn btn-primary">Update</button>
                                                </div>

                                            </form>

                                        </div>
                                    </div>
                                </div>

                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-info fw-bolder">
                                        No destinations found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

    {{-- DELETE MODAL --}}
    <div class="modal fade" id="deleteModal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="text-danger">Delete Destination</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    Are you sure you want to delete:
                    <strong id="deleteName"></strong>?
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>

                    <form method="POST" id="deleteForm"
                        action="{{ route('company.destinations.destroy', [
                            'company' => $params,
                            'destination' => '__ID__',
                        ]) }}">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="btn btn-danger">
                            Delete
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    {{-- ADD MODAL --}}
    <div class="modal fade" id="addDestinationModal">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="text-primary fw-bolder">ADD DESTINATION</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form method="POST" action="{{ route('company.destinations.store', $params) }}">
                    @csrf

                    <div class="modal-body">
                        <div class="form-group">
                            <small class="text-muted">COMPANY <sup class="text-danger">*</sup></small>
                            <select name="company_id" id="" class="form-select border border-primary" required>
                                <option value="" disabled selected>Select Company</option>
                                @foreach ($companyList as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <small class="text-muted">CODE <sup class="text-info">(OPTIONAL)</sup></small>
                            <input class="form-control border border-primary text-primary" name="destination_code"
                                placeholder=" (e.g. PMP, optional)">
                        </div>
                        <div class="form-group">
                            <small class="text-muted">STORE NAME <sup class="text-info">(OPTIONAL)</sup></small>
                            <input class="form-control border border-primary text-primary" name="store_name"
                                placeholder="ex. Store / Branch (optional)">
                        </div>
                        <div class="form-group">
                            <small class="text-muted">DESTINATION / AREA<sup class="text-info">*</sup></small>
                            <input class="form-control border border-primary text-primary" name="area"
                                placeholder="Area / Province" required>
                        </div>
                        <div class="form-group">
                            <small class="text-muted">COMPANY <sup class="text-danger">*</sup></small>
                            <select class="form-select border border-primary text-primary" name="truck_type" required>
                                <option value="" disabled selected>Truck Type</option>
                                @foreach ($truckTypes as $item)
                                    <option value="{{ $item }}">{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <small class="text-muted">RATE <sup class="text-danger">*</sup></small>
                            <input type="number" step="0.01" class="form-control border border-primary text-primary"
                                name="rate" placeholder="Rate" required>
                        </div>

                        <div class="form-group">
                            <small class="text-muted">REMARKS <sup class="text-info">(OPTIONAL)</sup></small>
                            <input class="form-control border border-primary text-primary" name="remarks"
                                placeholder="Remarks (optional)">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary">Save</button>
                    </div>

                </form>

            </div>
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
        .ui-rounded {
            border-radius: 12px;
        }

        .ui-shadow {
            box-shadow: 0 8px 25px rgba(16, 24, 40, .06);
        }

        .ui-hero {
            border-radius: 20px;
            border: 1px solid rgba(0, 0, 0, .05);
            background:
                radial-gradient(900px 500px at 10% 10%, rgba(99, 102, 241, .10), transparent 60%),
                radial-gradient(900px 500px at 90% 20%, rgba(16, 185, 129, .10), transparent 60%),
                linear-gradient(135deg, #ffffff, #f9fafb);
            box-shadow: 0 20px 40px rgba(17, 24, 39, .06);
            padding: 24px 28px;
        }

        .ui-search-wrapper {
            position: relative;
            width: 220px;
        }

        .ui-search-wrapper i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 13px;
            color: #98a2b3;
        }

        .ui-search-wrapper input {
            padding-left: 32px;
            height: 40px;
            border-radius: 10px;
        }

        .ui-btn {
            height: 40px;
            border-radius: 10px;
            padding: 0 14px;
            font-weight: 500;
        }

        .ui-btn-clear {
            background: #fff;
            border: 1px solid #e5e7eb;
        }

        .ui-btn-add {
            background: linear-gradient(135deg, #7c3aed, #6366f1);
            color: #fff;
            border: none;
            font-weight: 600;
            transition: 0.2s ease;
            padding: 0 16px;
        }

        .ui-btn,
        .ui-btn-add,
        .ui-btn-clear {
            height: 38px;
            display: flex;
            align-items: center;
        }

        .ui-btn-add:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(99, 102, 241, 0.25);
        }

        .ui-sort {
            color: inherit;
            text-decoration: none;
            font-weight: 600;
        }

        .ui-sort:hover {
            color: #6366f1;
        }

        .ui-sort span {
            font-size: 12px;
            margin-left: 4px;
        }

        @media (max-width: 768px) {
            .ui-search-wrapper {
                width: 100%;
            }

            .ui-btn {
                width: 100%;
            }
        }
    </style>
@endpush
