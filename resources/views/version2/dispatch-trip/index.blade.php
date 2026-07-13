{{-- resources/views/owner/trips/index.blade.php --}}
@extends('layouts.appV2')
@section('title', 'Dispatch Trip')
@section('sub-title', 'DISPATCH TRIP')
@php
    $params = request()->route('company') ?: 'all';
@endphp
@section('content')
    <div class="container-fluid py-3 px-1 px-lg-4">

        {{-- Flash messages --}}
        @if (session('success'))
            <div class="alert alert-success mb-3">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger mb-3">{{ session('error') }}</div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        {{-- Header (TEAM UI HERO) --}}
        <div class="ui-hero p-3 p-lg-4 mb-3 mb-lg-4">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <h4 class="mb-1 fw-bold">Trips / Dispatch</h4>
                    <div class="text-muted small">
                        Dispatch control center — live trips, assignments, and performance.
                    </div>
                </div>
            </div>
        </div>

        {{-- Available Resources --}}
        <div class="row g-3 mb-1">

            {{-- AVAILABLE TRUCKS --}}
            <div class="col-12 col-md">
                <div class="card ui-available-card border-bottom border-4 border-0 border-primary"
                    style="margin-bottom:10px;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small fw-semibold text-primary">Available Trucks 🚚</div>

                            <div class="d-flex align-items-center gap-3 flex-shrink-0">
                                <div class="ui-available-number text-primary">{{ $trucks->count() }}</div>

                                <button type="button" class="btn btn-sm ui-eye-btn collapse-toggle"
                                    data-target="#availTrucksList">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="collapse mt-0 ui-available-dropdown" id="availTrucksList">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body py-2">
                            <div class="ui-paginated-list" data-per-page="5" data-target="trucks">
                                @forelse($trucks as $t)
                                    <div class="ui-list-item py-1 small">
                                        {{ $t->plate_number }}
                                        @if ($t->truck_type)
                                            <span class="text-muted">({{ $t->truck_type }})</span>
                                        @endif
                                    </div>
                                @empty
                                    <div class="text-muted small">No available trucks.</div>
                                @endforelse
                            </div>

                            @if ($trucks->count() > 5)
                                <div class="d-flex justify-content-end align-items-center gap-2 mt-2 ui-list-controls"
                                    data-controls="trucks">
                                    <button type="button" class="btn btn-sm btn-light ui-list-prev">Prev</button>
                                    <div class="small text-muted ui-list-page">1</div>
                                    <button type="button" class="btn btn-sm btn-light ui-list-next">Next</button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- AVAILABLE DRIVERS --}}
            <div class="col-12 col-md">
                <div class="card ui-available-card border-bottom border-4 border-0 border-success"
                    style="margin-bottom:10px;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small fw-semibold text-primary">Available Drivers 👤</div>

                            <div class="d-flex align-items-center gap-3 flex-shrink-0">
                                <div class="ui-available-number text-success">{{ $drivers->count() }}</div>

                                <button type="button" class="btn btn-sm ui-eye-btn collapse-toggle"
                                    data-target="#availDriversList">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="collapse mt-0 ui-available-dropdown" id="availDriversList">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body py-2">
                            <div class="ui-paginated-list" data-per-page="5" data-target="drivers">
                                @forelse($drivers as $dr)
                                    <div class="ui-list-item py-1 small">{{ $dr->name }}</div>
                                @empty
                                    <div class="text-muted small">No available drivers.</div>
                                @endforelse
                            </div>

                            @if ($drivers->count() > 5)
                                <div class="d-flex justify-content-end align-items-center gap-2 mt-2 ui-list-controls"
                                    data-controls="drivers">
                                    <button type="button" class="btn btn-sm btn-light ui-list-prev">Prev</button>
                                    <div class="small text-muted ui-list-page">1</div>
                                    <button type="button" class="btn btn-sm btn-light ui-list-next">Next</button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            {{-- AVAILABLE HELPERS --}}
            <div class="col-12 col-md">
                <div class="card ui-available-card border-bottom border-4 border-0 border-success"
                    style="margin-bottom:10px;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small fw-semibold">Available Helpers 👷</div>

                            <div class="d-flex align-items-center gap-3 flex-shrink-0">
                                <div class="ui-available-number text-warning">{{ $helpers->count() }}</div>

                                <button type="button" class="btn btn-sm ui-eye-btn collapse-toggle"
                                    data-target="#availHelpersList">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="collapse mt-0 ui-available-dropdown" id="availHelpersList">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body py-2">
                            <div class="ui-paginated-list" data-per-page="5" data-target="helpers">
                                @forelse($helpers as $h)
                                    <div class="ui-list-item py-1 small">{{ $h->name }}</div>
                                @empty
                                    <div class="text-muted small">No available helpers.</div>
                                @endforelse
                            </div>

                            @if ($helpers->count() > 5)
                                <div class="d-flex justify-content-end align-items-center gap-2 mt-2 ui-list-controls"
                                    data-controls="helpers">
                                    <button type="button" class="btn btn-sm btn-light ui-list-prev">Prev</button>
                                    <div class="small text-muted ui-list-page">1</div>
                                    <button type="button" class="btn btn-sm btn-light ui-list-next">Next</button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            {{-- AVAILABLE DESTINATIONS --}}
            <div class="col-12 col-md">
                <div class="card ui-available-card border-bottom border-4 border-0 border-warning"
                    style="margin-bottom:10px;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted small fw-semibold text-primary">Available Destinations 📍</div>

                            <div class="d-flex align-items-center gap-3 flex-shrink-0">
                                <div class="ui-available-number text-warning">{{ $destinations->count() }}</div>

                                <button type="button" class="btn btn-sm ui-eye-btn collapse-toggle"
                                    data-target="#availDestinationsList">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="collapse mt-0 ui-available-dropdown" id="availDestinationsList">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body py-2">
                            <div class="ui-paginated-list" data-per-page="5" data-target="destinations">
                                @forelse($destinations as $d)
                                    <div class="ui-list-item py-1 small">{{ $d->destination_name . ' ' . $d->area }}
                                    </div>
                                @empty
                                    <div class="text-muted small">No available destinations.</div>
                                @endforelse
                            </div>

                            @if ($destinations->count() > 5)
                                <div class="d-flex justify-content-end align-items-center gap-2 mt-2 ui-list-controls"
                                    data-controls="destinations">
                                    <button type="button" class="btn btn-sm btn-light ui-list-prev">Prev</button>
                                    <div class="small text-muted ui-list-page">1</div>
                                    <button type="button" class="btn btn-sm btn-light ui-list-next">Next</button>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- New Trip Modal --}}
        <div class="modal fade" id="newTripModal" tabindex="-1" aria-labelledby="newTripModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <form method="POST" action="{{ route('admin.dispatch.store') }}">
                        @csrf

                        <div class="modal-header bg-light">
                            <div>
                                <h4 class="modal-title fw-bolder text-primary" id="newTripModalLabel">
                                    CREATE NEW DISPATCH TRIP
                                </h4>
                                <small class="text-muted">Fill in the trip details and assign resources.</small>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>

                        <div class="modal-body">

                            {{-- Trip Details --}}
                            <div class="mb-3">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <h6 class="mb-0 fw-semibold text-primary">DISPATCH TRIP DETAILS</h6>
                                    <span class="badge bg-secondary-subtle text-secondary">Draft</span>
                                </div>

                                <div class="row">
                                    <div class="col-lg-8 col-md">
                                        <small class="fw-bolder text-muted">
                                            COMPANY
                                            <span class="text-danger">*</span>
                                        </small>
                                        <select name="company" class="companySelect form-select border border-primary">
                                            <option value="" disabled selected>Select Company</option>
                                            @foreach ($company as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    {{-- Row 1 --}}

                                    <div class="col-md-4">
                                        <small class="fw-bolder text-muted">
                                            Date
                                            <span class="text-danger">*</span>
                                        </small>
                                        <input type="date" name="dispatch_date"
                                            class="form-control border border-primary" required
                                            value="{{ old('dispatch_date') }}">
                                    </div>

                                    {{-- Row 2 --}}
                                    <div class="col-md-12">
                                        <small class="fw-bolder text-muted">
                                            DESTINATION
                                            <span class="text-danger">*</span>
                                        </small>
                                        <select name="destination"
                                            class="destinationSelect form-select select2-destination border border-primary"
                                            required>

                                            <option value="" disabled {{ old('destination') ? '' : 'selected' }}>
                                                Select destination
                                            </option>

                                            @foreach ($destinations as $d)
                                                <option value="{{ $d->id }}">
                                                    {{ $d->destination_name }} ({{ $d->truck_type }}) —
                                                    ₱{{ number_format($d->rate, 2) }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>
                            </div>

                            <hr class="my-4">

                            {{-- Assignment --}}
                            <div class="mb-2">
                                <h6 class="mb-2 fw-bolder text-primary">ASSIGNED DETAILS </h6>

                                <div class="row g-3">

                                    {{-- Row 3 --}}
                                    <div class="col-md-6">
                                        <small class="fw-bolder text-muted">
                                            TRUCK
                                            <span class="text-danger">*</span>
                                        </small>
                                        <select name="truck" class="truckSelect form-select border border-primary"
                                            required>
                                            <option value="" disabled selected>Select truck</option>

                                            @foreach ($trucks as $t)
                                                <option value="{{ $t->id }}" data-type="{{ $t->truck_type }}">

                                                    {{ $t->plate_number }}
                                                    {{ $t->truck_type ? '(' . $t->truck_type . ')' : '' }}

                                                </option>
                                            @endforeach

                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <small class="fw-bolder text-muted">
                                            DRIVER
                                            <span class="text-danger">*</span>
                                        </small>
                                        <select name="driver" class="form-select border border-primary" required>
                                            <option value="" disabled {{ old('driver') ? '' : 'selected' }}>
                                                Select driver
                                            </option>
                                            @foreach ($drivers as $dr)
                                                <option value="{{ $dr->id }}"
                                                    {{ old('driver_id') == $dr->id ? 'selected' : '' }}>
                                                    {{ $dr->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="fw-bolder text-muted">
                                            HELPER 1
                                            <span class="text-warning">OPTIONAL</span>
                                        </small>
                                        <select name="helper1" id="helper1" class="form-select border border-primary">
                                            <option value="">Select helper</option>
                                            @foreach ($helpers as $h)
                                                <option value="{{ $h->id }}"
                                                    {{ old('helper1') == $h->id ? 'selected' : '' }}>
                                                    {{ $h->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="fw-bolder text-muted">
                                            HELPER 2
                                            <sup class="text-warning">OPTIONAL</sup>
                                        </small>
                                        <select name="helper2" id="helper2" class="form-select border border-primary">
                                            <option value="">Select helper</option>
                                            @foreach ($helpers as $h)
                                                <option value="{{ $h->id }}"
                                                    {{ old('helper2') == $h->id ? 'selected' : '' }}>
                                                    {{ $h->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-primary">
                                            Trip Number <span class="text-danger">*</span>
                                        </label>

                                        <select name="trip_number" class="form-select border border-primary" required>
                                            <option value="" disabled selected>Select trip</option>
                                            <option value="1">1st Trip</option>
                                            <option value="2">2nd Trip</option>
                                            <option value="3">3rd Trip</option>
                                        </select>
                                    </div>

                                    {{-- Row 5 --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold text-primary">Remarks</label>
                                        <input type="text" name="remarks" class="form-control border border-primary"
                                            placeholder="Optional notes (e.g. urgent, fragile, special instructions)"
                                            value="{{ old('remarks') }}">
                                    </div>

                                </div>
                            </div>

                        </div>

                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                Cancel
                            </button>
                            <button type="submit" class="btn btn-primary">
                                Save Draft
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        {{-- Delete All Confirm Modal --}}

        @foreach ($dispatchList as $t)
            @if (in_array($t->status, ['Draft', 'Assigned', 'Dispatched']))
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
                                    This action cannot be undone.
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                    Cancel
                                </button>

                                <form method="POST" action="{{ route('watson.trips.destroy', $t->id) }}">
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
            @endif
        @endforeach
        {{-- Confirm Delete Trip --}}
        @foreach ($dispatchList as $t)
            @if ($t->status === 'Draft')
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
                                    This action cannot be undone.
                                </div>

                            </div>

                            <div class="modal-footer">

                                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                    Cancel
                                </button>

                                <form method="POST" action="{{ route('watson.trips.destroy', $t->id) }}">
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
            @endif
        @endforeach

        {{-- Dispatch Modal --}}
        @foreach ($dispatchList as $t)
            <div class="modal fade" id="dispatchModal-{{ $t->id }}" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">

                        <form method="POST" action="{{ route('admin.dispatch.dispatch', $t->id) }}">
                            @csrf

                            <div class="modal-header">
                                <h6 class="modal-title">Dispatch Trip</h6>
                            </div>

                            <div class="modal-body">

                                <label class="form-label">PVD Number</label>
                                <input type="text" name="trip_ticket_no" class="form-control border border-primary"
                                    required>

                            </div>

                            <div class="modal-footer">

                                <button class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                    Cancel
                                </button>

                                <button type="submit" class="btn btn-primary">
                                    Dispatch
                                </button>

                            </div>

                        </form>

                    </div>
                </div>
            </div>
        @endforeach

        {{-- Edit Trip Confirm Modal --}}
        @foreach ($dispatchList as $t)
            @if ($t->status === 'Draft')
                <div class="modal fade" id="editTripModal-{{ $t->id }}" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content border-0 shadow">

                            <form method="POST" action="{{ route('watson.trips.update', $t->id) }}">
                                @csrf
                                @method('PUT')

                                <div class="modal-header bg-light">
                                    <div>
                                        <h5 class="modal-title fw-semibold text-primary">Edit Trip</h5>
                                        <small class="text-muted">Update trip details and assignments.</small>
                                    </div>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>

                                <div class="modal-body">

                                    {{-- Trip Details --}}
                                    <div class="mb-3">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <h6 class="mb-0 fw-semibold text-primary">Trip Details</h6>
                                            <span class="badge bg-secondary-subtle text-secondary">Draft</span>
                                        </div>

                                        <div class="row g-2 g-lg-3">

                                            <div class="col-md-4">
                                                <label class="form-label fw-semibold text-primary">
                                                    Date <span class="text-danger">*</span>
                                                </label>

                                                <input type="date" name="dispatch_date"
                                                    class="form-control border border-primary"
                                                    value="{{ \Carbon\Carbon::parse($t->dispatch_date)->format('Y-m-d') }}"
                                                    required>
                                            </div>

                                            <div class="col-md-8">
                                                <label class="form-label fw-semibold text-primary">
                                                    Destination <span class="text-danger">*</span>
                                                </label>

                                                <select name="destination" class="form-select border border-primary"
                                                    required>

                                                    @foreach ($destinations as $d)
                                                        <option value="{{ $d->id }}">
                                                            {{ $d->destination_name }} ({{ $d->truck_type }}) —
                                                            ₱{{ number_format($d->rate, 2) }}
                                                        </option>
                                                    @endforeach

                                                </select>
                                            </div>

                                        </div>
                                    </div>

                                    <hr class="my-4">

                                    {{-- Assignment --}}
                                    <div class="mb-2">
                                        <h6 class="mb-2 fw-semibold text-primary">Assignment</h6>

                                        <div class="row g-3">

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold text-primary">
                                                    Truck <span class="text-danger">*</span>
                                                </label>

                                                <select name="truck_id" class="form-select" required>

                                                    @foreach ($trucks as $truck)
                                                        <option value="{{ $truck->id }}">
                                                            {{ $truck->plate_number . ' (' . $truck->truck_type . ')' }}
                                                        </option>
                                                    @endforeach

                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold text-primary">
                                                    Driver <span class="text-danger">*</span>
                                                </label>

                                                <select name="driver_id" class="form-select" required>

                                                    @foreach ($drivers as $dr)
                                                        <option value="{{ $dr->id }}"
                                                            {{ $dr->id == $t->driver_id ? 'selected' : '' }}>
                                                            {{ $dr->name }}
                                                        </option>
                                                    @endforeach

                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold">Helper 1 (optional)</label>
                                                <select name="helper_1_id" id="helper1"
                                                    class="form-select border border-primary">
                                                    <option value="">Select helper</option>
                                                    @foreach ($helpers as $h)
                                                        <option value="{{ $h->id }}"
                                                            {{ old('helper_1_id') == $t->helper?->id ? 'selected' : '' }}>
                                                            {{ $h->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold text-primary">
                                                    Trip Number <span class="text-danger">*</span>
                                                </label>

                                                <select name="trip_number" class="form-select" required>
                                                    <option value="1" {{ $t->trip_number == 1 ? 'selected' : '' }}>
                                                        1st
                                                        Trip</option>
                                                    <option value="2" {{ $t->trip_number == 2 ? 'selected' : '' }}>
                                                        2nd
                                                        Trip</option>
                                                    <option value="3" {{ $t->trip_number == 3 ? 'selected' : '' }}>
                                                        3rd
                                                        Trip</option>
                                                </select>

                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold text-primary">Remarks</label>

                                                <input type="text" name="remarks"
                                                    class="form-control border border-primary"
                                                    value="{{ $t->remarks }}">
                                            </div>
                                        </div>

                                    </div>



                                </div> {{-- modal-body --}}

                                <div class="modal-footer bg-light">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                        Cancel
                                    </button>

                                    <button type="submit" class="btn btn-primary">
                                        Update Trip
                                    </button>
                                </div>



                            </form>

                        </div>
                    </div>
                </div>
            @endif
        @endforeach


        {{-- Trips Card --}}
        @php
            $currentSort = request('sort');
            $currentDir = request('dir', 'desc');

            $sortUrl = function ($field) use ($currentSort, $currentDir) {
                $dir = $currentSort === $field && $currentDir === 'asc' ? 'desc' : 'asc';
                return request()->fullUrlWithQuery(['sort' => $field, 'dir' => $dir]);
            };

            $sortIcon = function ($field) use ($currentSort, $currentDir) {
                if ($currentSort !== $field) {
                    return 'bi bi-arrow-down-up text-muted';
                }
                return $currentDir === 'asc' ? 'bi bi-caret-up-fill' : 'bi bi-caret-down-fill';
            };
        @endphp

        <div class="card ui-card border-0 mt-3">
            <div class="card-header bg-transparent border-0 pb-0">

                {{-- Title + pager --}}
                <div
                    class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center gap-3">

                    <div class="ui-trips-head-left">
                        <h3 class="mb-0 fw-bolder text-primary">Current Dispatch Trips</h3>
                        <div class="text-muted small mt-1 ui-showing">
                            @if ($dispatchList->total())
                                Showing <strong>{{ $dispatchList->firstItem() }}–{{ $dispatchList->lastItem() }}</strong>
                                /
                                <strong>{{ $dispatchList->total() }}</strong>
                            @else
                                Showing <strong>0</strong> / <strong>0</strong>
                            @endif
                        </div>
                    </div>

                    {{-- RIGHT SIDE BUTTON --}}
                    <a href="{{ route('admin.dispatch-trip.history') }}"
                        class="btn btn-info btn-sm text-white ui-pill-btn">
                        <i class="bi bi-clock-history me-1"></i> Dispatch Trips History
                    </a>

                </div>

                {{-- Controls --}}
                <div
                    class="mt-3 d-flex flex-column flex-lg-row gap-2 align-items-stretch align-items-lg-center justify-content-between">
                    <form method="GET" action="{{ route('company.dispatch.index', ['company' => $params]) }}"
                        class="d-flex flex-column flex-sm-row gap-2 align-items-stretch align-items-sm-center m-0 flex-grow-1">


                        <div class="ui-search ui-header-search" style="max-width: 520px; width: 100%;">
                            <i class="bi bi-search ui-search-icon"></i>
                            <input type="text" name="q" value="{{ request('q') }}"
                                class="form-control border border-primary ui-search-input"
                                placeholder="Search trip ticket, truck, driver...">
                        </div>



                        @if (request('sort'))
                            <input type="hidden" name="sort" value="{{ request('sort') }}">
                        @endif
                        @if (request('dir'))
                            <input type="hidden" name="dir" value="{{ request('dir') }}">
                        @endif

                        @if (request('q'))
                            <a href="{{ route('company.dispatch.index', ['company' => $params, request()->except('q', 'page')]) }}"
                                class="btn btn-outline-secondary btn-sm ui-pill-btn ui-btn-equal">
                                Clear
                            </a>
                        @endif

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


                    <div class="d-flex flex-column flex-sm-row gap-2">
                        <button type="button" class="btn btn-primary btn-sm ui-pill-btn ui-btn-wide"
                            data-bs-toggle="modal" data-bs-target="#newTripModal">
                            <i class="bi bi-plus-lg me-1"></i> New Trip
                        </button>

                        <button type="button" class="btn btn-outline-danger btn-sm ui-pill-btn ui-btn-equal"
                            data-bs-toggle="modal" data-bs-target="#deleteAllTripsModal"
                            {{ $dispatchList->total() ? '' : 'disabled' }}>
                            <i class="bi bi-trash3 me-1"></i> Delete All
                        </button>
                    </div>
                </div>

                <div class="ui-divider mt-3"></div>
            </div>
            <div class="card-body">
                <div id="list-view-container">
                    @forelse ($dispatchList as $item)
                        <div
                            class="card h-100 rounded-4 p-4 trip-card bg-white d-flex flex-column justify-content-between gap-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-2">
                                    <span
                                        class="badge bg-{{ $item->company->badge_color }} px-2.5 py-1.5 rounded-3 fw-bold">{{ $item->company->name }}</span>
                                    <span
                                        class="text-muted small font-monospace">{{ $item->trip_ticket_no ?? '-' }}</span>
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
                                <div class="col-md-2 dispatch-action">
                                    @switch($item->dispatch_status)
                                        @case('Draft')
                                            {{-- ASSIGN TRIP --}}
                                            <form method="POST" action="{{ route('admin.dispatch.assign', $item->id) }}"
                                                class="trip-dispatch">
                                                @csrf
                                                <button class="btn btn-info text-white btn-sm mb-3 w-100">
                                                    Assign Trip
                                                </button>
                                            </form>

                                            {{-- EDIT --}}
                                            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#editTripModal-{{ $item->id }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                        @break

                                        @case('Assigned')
                                            <div class="d-flex gap-2">
                                                <button class="btn btn-primary btn-sm w-100" data-bs-toggle="modal"
                                                    data-bs-target="#dispatchModal-{{ $item->id }}">
                                                    Ready to Dispatch
                                                </button>
                                            </div>
                                        @break

                                        @case('Dispatched')
                                            <div class="d-flex gap-2">
                                                <form method="POST" action="{{ route('admin.dispatch.deliver', $item->id) }}"
                                                    class="trip-dispatch w-100">
                                                    @csrf
                                                    <button class="btn btn-success btn-sm text-white w-100">
                                                        Delivered
                                                    </button>
                                                </form>
                                            </div>
                                        @break
                                    @endswitch

                                    @if ($item->dispatch_status !== 'Dispatched')
                                        <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#confirmDelete-{{ $item->id }}">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif


                                </div>
                                <div class="col-md destination-information">
                                    <span class="text-uppercase fw-bold text-muted small tracking-wider d-block">
                                        Destination
                                    </span>
                                    <h4 class="h5 fw-bold text-dark mt-1 mb-1">{{ $item->destination->name() }}</h4>
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
            </div>
        </div>
        <div class="card-footer bg-transparent border-0 pt-0">
            <div class="d-flex justify-content-start justify-content-lg-end">
                {{ $dispatchList->onEachSide(1)->links('vendor.pagination.ui-datatable') }}
            </div>
        </div>
        </div>

        {{-- ✅ Confirm Dispatch Modals (ONE ONLY, outside table/cards) --}}
        @foreach ($dispatchList as $t)
            @if (in_array($t->status, ['Draft']))
                <div class="modal fade" id="confirmDispatch-{{ $t->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header">
                                <h6 class="modal-title">Confirm Dispatch</h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                Dispatch <strong>{{ $t->trip_ticket_no }}</strong> now?
                                <div class="text-muted small mt-2">
                                    Truck/Driver/Destinations will be marked <strong>On Trip</strong>.
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary ui-pill-btn" data-bs-dismiss="modal">
                                    Cancel
                                </button>

                                <form method="POST" action="{{ route('watson.trips.dispatch', $t->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-primary ui-pill-btn">
                                        Yes, Dispatch
                                    </button>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            @endif
        @endforeach

        <div class="modal fade" id="deleteAllTripsModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">

                    <div class="modal-header">
                        <h6 class="modal-title text-danger">
                            Delete All Trips
                        </h6>
                        <button class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        Are you sure you want to delete ALL trips?
                        <div class="text-muted small mt-2">
                            This cannot be undone.
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>

                        <form method="POST" action="{{ route('admin.dispatch.destroy-all') }}">
                            @csrf
                            <button type="submit" class="btn btn-danger">
                                Yes, Delete All
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    @endsection

    @push('scripts')
        <script>
            $(document).ready(function() {

                const $company = $('.companySelect');
                const $destination = $('.destinationSelect');
                const $truck = $('.truckSelect');

                // ==========================================
                // 1. WHEN COMPANY CHANGES -> FETCH DESTINATIONS
                // ==========================================
                $company.on('change', function() {
                    const selectedCompanyId = $(this).val();

                    // Instantly reset downstream dropdowns
                    $destination.html('<option value="">Loading destinations...</option>').prop('disabled',
                        true);
                    $truck.html('<option value="">Select truck</option>').prop('disabled', true);

                    if (!selectedCompanyId) {
                        $destination.html('<option value="">Search destination...</option>');
                        return;
                    }

                    // AJAX Request to fetch destinations assigned to this company
                    $.ajax({
                        url: '/admin/destinations/' +
                            selectedCompanyId, // Replace with your actual backend URL
                        type: 'GET', // Pass company ID to the backend
                        dataType: 'json',
                        success: function(response) {
                            $destination.html('<option value="">Search destination...</option>');

                            // Loop through server response
                            // Expected response format: [{ id: 1, name: "Warehouse A", truck_type: "Flatbed" }]
                            $.each(response.data, function(index, dest) {
                                const $option = $('<option>', {
                                    value: dest.id,
                                    text: dest.name
                                });

                                // Securely attach the truck type string to this DOM element data attributes
                                $option.attr('data-truck-type', dest.truck_type);

                                $destination.append($option);
                            });

                            $destination.prop('disabled', false); // Unlock the dropdown
                        },
                        error: function(xhr, status, error) {
                            console.error("Failed to fetch destinations:", error);
                            $destination.html('<option value="">Error loading data</option>');
                        }
                    });
                });

                // ==========================================
                // 2. WHEN DESTINATION CHANGES -> FETCH TRUCKS
                // ==========================================
                $destination.on('change', function() {
                    const selectedDestId = $(this).val();
                    const selectedCompanyId = $company.val();

                    // Reset truck dropdown
                    $truck.html('<option value="">Loading available trucks...</option>').prop('disabled', true);

                    if (!selectedDestId) {
                        $truck.html('<option value="">Select truck</option>');
                        return;
                    }

                    // Extract the needed truck type directly from the selected destination's attribute
                    const requiredTruckType = $(this).find('option:selected').attr('data-truck-type');

                    // AJAX Request to fetch trucks matching BOTH filters
                    $.ajax({
                        url: '/admin/truck-destinations/' +
                            selectedDestId, // Replace with your actual backend URL
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            $truck.html('<option value="">Select truck</option>');

                            // Loop through server response
                            // Expected response format: [{ id: 101, plate_number: "FL-101", type: "Flatbed" }]
                            $.each(response.data, function(index, truck) {
                                $truck.append($('<option>', {
                                    value: truck.id,
                                    text: `${truck.name}` // Shows format: FL-101 (Flatbed)
                                }));
                            });

                            $truck.prop('disabled', false); // Unlock the dropdown
                        },
                        error: function(xhr, status, error) {
                            console.error("Failed to fetch trucks:", error);
                            $truck.html('<option value="">Error loading data</option>');
                        }
                    });
                });

            });
        </script>
        <script>
            // ✅ Safe Select2 init (won’t error if select2 is not loaded)
            document.addEventListener('DOMContentLoaded', function() {
                const modal = document.getElementById('newTripModal');
                if (!modal) return;

                modal.addEventListener('shown.bs.modal', function() {
                    if (!window.jQuery || !window.jQuery.fn || typeof window.jQuery.fn.select2 !== 'function') {
                        return; // select2 not loaded
                    }

                    const $el = window.jQuery('.select2-destination');
                    if ($el.length && !$el.hasClass('select2-hidden-accessible')) {
                        $el.select2({
                            placeholder: 'Search destination...',
                            allowClear: true,
                            width: '100%',
                            dropdownParent: window.jQuery('#newTripModal')
                        });
                    }
                });

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

                document.querySelectorAll('.person-avatar').forEach(function(el) {

                    const initial = el.dataset.initial || "A";

                    const index = initial.charCodeAt(0) % 8 + 1;

                    el.classList.add("color-" + index);

                });
            });
        </script>
    @endpush

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

            .ui-badge-assigned {
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

            .ui-dot-assigned {
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
                justify-content: center;
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
                background: #f3eeff;
                color: #c546e5;
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

            /* Destination dropdown filtering */
            .option-6w {
                color: #007bff !important;
            }

            .option-l300 {
                color: #28a745 !important;
            }

            .select2-container--default .select2-results__option[aria-disabled="true"] {
                color: #6c757d !important;
                background-color: #dee2e6 !important;
            }

            .select2-container--default .select2-results__option.enabled-option {
                font-weight: bold !important;
            }

            .option-6w.enabled-option {
                background-color: #cce5ff !important;
                /* Light blue for enabled 6W */
            }

            .option-l300.enabled-option {
                background-color: #d4edda !important;
                /* Light green for enabled L300 */
            }

            .text-violet {
                color: var(--bs-primary);
            }

            .bg-violet-light {
                background-color: var(--bs-primary-light);
                color: var(--bs-primary);
            }

            .btn-violet {
                background-color: var(--bs-primary);
                color: #ffffff;
                border: none;
            }

            .btn-violet:hover {
                background-color: var(--bs-primary-hover);
                color: #ffffff;
            }


            #tab-list:checked~#dashboard-container #list-view-container {
                display: flex !important;
                /* Bootstrap 'row' flexbox */
            }

            #tab-list:checked~#dashboard-container #btn-view-list {
                background-color: #ffffff !important;
                color: var(--bs-primary) !important;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            }

            #tab-table:checked~#dashboard-container #table-view-container {
                display: block !important;
            }

            #tab-table:checked~#dashboard-container #btn-view-table {
                background-color: #ffffff !important;
                color: var(--bs-primary) !important;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            }

            /* 2. Modal Toggle Logic (Pure CSS) */
            #modal-toggle:checked~#trip-modal {
                display: flex !important;
            }

            /* Modal Background Blur and Position Overrides */
            #trip-modal {
                background-color: rgba(15, 23, 42, 0.5);
                backdrop-filter: blur(4px);
            }

            /* Custom Hover Transitions for Cards */
            .trip-card {
                transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
                border: 1px solid #e2e8f0;
            }

            .trip-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -4px rgba(0, 0, 0, 0.05) !important;
                border-color: #c084fc;
            }

            /* Custom Scrollbar Styles */
            ::-webkit-scrollbar {
                width: 6px;
                height: 6px;
            }

            ::-webkit-scrollbar-track {
                background: #f1f5f9;
                border-radius: 8px;
            }

            ::-webkit-scrollbar-thumb {
                background: #cbd5e1;
                border-radius: 8px;
            }

            ::-webkit-scrollbar-thumb:hover {
                background: #94a3b8;
            }
        </style>
    @endpush
