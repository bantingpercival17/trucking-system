@extends('layouts.watson')

@section('title', 'Destinations')

@section('content')
    <div class="container-fluid py-4">

        {{-- HEADER --}}
        <div class="ui-hero p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

                <div>
                    <h4 class="fw-bold mb-1">Destinations</h4>
                    <div class="text-info fw-bolder small">
                        Routes & Rate Card — 6W / 4W / AUV, planning and costing.
                    </div>
                </div>

                <form method="GET" action="{{ route('watson.destinations.index') }}" class="d-flex align-items-center gap-2">

                    <div class="ui-search-wrapper">
                        <i class="bi bi-search"></i>
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                            placeholder="Search destination, area, origin...">
                    </div>

                    @if (request('type'))
                        <input type="hidden" name="type" value="{{ request('type') }}">
                    @endif

                    @if (request('q'))
                        <a href="{{ route('watson.destinations.index', array_filter(['type' => request('type')])) }}"
                            class="btn ui-btn ui-btn-clear">
                            Clear
                        </a>
                    @endif

                    <button type="button" class="btn ui-btn ui-btn-add" data-bs-toggle="modal"
                        data-bs-target="#addDestinationModal">
                        <i class="bi bi-plus-lg me-1"></i> Add
                    </button>

                </form>

            </div>
        </div>

        {{-- TRUCK TYPE TABS --}}
        <ul class="nav nav-pills mb-3 gap-2">
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
        </ul>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table align-middle">

                        @php
                            function watsonSortDirection($column)
                            {
                                if (request('sort') !== $column) {
                                    return 'asc';
                                }
                                if (request('direction') === 'asc') {
                                    return 'desc';
                                }
                                if (request('direction') === 'desc') {
                                    return null;
                                }
                                return 'asc';
                            }

                            function watsonSortIcon($column)
                            {
                                if (request('sort') !== $column) {
                                    return '↕';
                                }
                                if (request('direction') === 'asc') {
                                    return '↑';
                                }
                                if (request('direction') === 'desc') {
                                    return '↓';
                                }
                                return '↕';
                            }
                        @endphp

                        <thead>
                            <tr>
                                <th>ORIGIN</th>
                                <th>
                                    <a href="{{ route(
                                        'watson.destinations.index',
                                        array_filter([
                                            'q' => request('q'),
                                            'type' => request('type'),
                                            'sort' => watsonSortDirection('destination_name') ? 'destination_name' : null,
                                            'direction' => watsonSortDirection('destination_name'),
                                        ]),
                                    ) }}"
                                        class="ui-sort">
                                        AREA {!! watsonSortIcon('destination_name') !!}
                                    </a>
                                </th>
                                {{--   <th>
                                    <a href="{{ route(
                                        'watson.destinations.index',
                                        array_filter([
                                            'q' => request('q'),
                                            'type' => request('type'),
                                            'sort' => watsonSortDirection('area') ? 'area' : null,
                                            'direction' => watsonSortDirection('area'),
                                        ]),
                                    ) }}"
                                        class="ui-sort">
                                        AREA {!! watsonSortIcon('area') !!}
                                    </a>
                                </th> --}}
                                <th>TRUCK TYPE</th>
                                <th>
                                    <a href="{{ route(
                                        'watson.destinations.index',
                                        array_filter([
                                            'q' => request('q'),
                                            'type' => request('type'),
                                            'sort' => watsonSortDirection('rate') ? 'rate' : null,
                                            'direction' => watsonSortDirection('rate'),
                                        ]),
                                    ) }}"
                                        class="ui-sort">
                                        RATE {!! watsonSortIcon('rate') !!}
                                    </a>
                                </th>
                                <th>REMARKS</th>
                                <th width="120">ACTION</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($destinations as $d)
                                <tr class="position-relative">
                                    <td class="text-info fw-bolder">{{ $d->origin ?? '-' }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $d->destination_name }}</div>
                                    </td>
                                    {{-- <td class="text-info fw-bolder">{{ $d->area ?? '-' }}</td> --}}
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
                                                data-name="{{ $d->destination_name }} ({{ $d->truck_type }})">
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
                                                <h5>Edit Destination</h5>
                                                <button class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>

                                            <form method="POST"
                                                action="{{ route('watson.destinations.update', $d->id) }}">
                                                @csrf
                                                @method('PUT')

                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label class="form-label small text-info fw-bolder">Origin</label>
                                                        <input class="form-control border border-primary text-primary"
                                                            name="origin" value="{{ $d->origin }}"
                                                            placeholder="e.g. PMP (optional)">
                                                    </div>

                                                    <div class="form-group">
                                                        <label class="form-label small text-info fw-bolder">Area /
                                                            Province</label>
                                                        <input class="form-control border border-primary text-primary"
                                                            name="destination_name" value="{{ $d->destination_name }}"
                                                            required>
                                                    </div>

                                                    {{-- <div class="col-md-6">
                                                            <label class="form-label small text-info fw-bolder">Area</label>
                                                            <input class="form-control border border-primary text-primary" name="area"
                                                                value="{{ $d->area }}" placeholder="Area (optional)">
                                                        </div> --}}

                                                    <div class=form-group>
                                                        <label class="form-label small text-info fw-bolder">Truck
                                                            Type</label>
                                                        <select class="form-select" name="truck_type" required>
                                                            @foreach (['6W', '4W', 'AUV'] as $type)
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

                <div class="mt-3">
                    {{ $destinations->links() }}
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

                    <form method="POST" id="deleteForm">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger">Delete</button>
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
                    <h5>Add Destination</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <form method="POST" action="{{ route('watson.destinations.store') }}">
                    @csrf

                    <div class="modal-body">
                        <div class="form-group">
                            <input class="form-control border border-primary text-primary" name="origin"
                                placeholder="Origin (e.g. PMP, optional)">
                        </div>
                        <div class="form-group">
                            <input class="form-control border border-primary text-primary" name="destination_name"
                                placeholder="Area / Province (optional)" required>
                        </div>
                        {{--   <div class="form-group">
                            <input class="form-control border border-primary text-primary" name="area"
                                placeholder="Area / Province (optional)">
                        </div> --}}
                        <div class="form-group">
                            <select class="form-select border border-primary text-primary" name="truck_type" required>
                                <option value="" disabled selected>Truck Type</option>
                                <option value="6W">6W</option>
                                <option value="4W">4W</option>
                                <option value="AUV">AUV</option>
                                <option value="4WCV">4WCV</option>
                                <option value="L300">L300</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="number" step="0.01" class="form-control border border-primary text-primary"
                                name="rate" placeholder="Rate" required>
                        </div>

                        <div class="form-group">
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
                    document.getElementById('deleteForm').action =
                        `/watson/destinations/${id}`;

                    new bootstrap.Modal(document.getElementById('deleteModal')).show();
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
