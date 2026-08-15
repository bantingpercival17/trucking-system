@extends('layouts.appV2')
@section('title', 'Employee')
@section('sub-title', 'Employee')
@php
    $params = request()->route('company') ?: 'all';
@endphp
@php
    $prefix = match (session('layout')) {
        'flash' => 'flash',
        'watson' => 'watson',
        default => 'owner',
    };
@endphp

@section('content')
    <div class="container-fluid py-4">
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
        <div class="ui-hero p-4 mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <h4 class="mb-1 fw-bold">Drivers</h4>
                    <div class="text-muted small">
                        Availability, performance, and risk overview.
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="card ui-available-card border-bottom border-4 border-0 border-primary"
                    style="margin-bottom:10px;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-primary fw-bolder h4">
                                TOTAL EMPLOYEES
                            </div>

                            <div class="d-flex align-items-center gap-3 flex-shrink-0">
                                <div class="ui-available-number text-primary">
                                    {{ $employees->count() ?? 0 }}
                                </div>

                                <button type="button" class="btn btn-sm ui-eye-btn" data-bs-target="#onTripDriversList"
                                    aria-controls="onTripDriversList" aria-expanded="false">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="row g-3 mb-1">

                    {{-- TOTAL DRIVERS --}}
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card ui-available-card border-bottom border-4 border-0 border-primary"
                            style="margin-bottom:10px;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-muted small fw-semibold">
                                        Total Drivers 👤
                                    </div>
                                    <div class="d-flex align-items-center gap-3 flex-shrink-0">
                                        <div class="ui-available-number text-primary">
                                            {{ $driver['active']->count() ?? 0 }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- INACTIVE --}}
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card ui-available-card border-bottom border-4 border-0 border-danger"
                            style="margin-bottom:10px;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-muted small fw-semibold">
                                        Inactive ⛔
                                    </div>
                                    <div class="d-flex align-items-center gap-3 flex-shrink-0">
                                        <div class="ui-available-number text-danger">
                                            {{ $driver['inactive']->count() ?? 0 }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- DRIVER AVAILABLE --}}
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card ui-available-card border-bottom border-4 border-0 border-success"
                            style="margin-bottom:10px;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-muted small fw-semibold">
                                        Driver Available ✅
                                    </div>

                                    <div class="d-flex align-items-center gap-3 flex-shrink-0">
                                        <div class="ui-available-number text-success">
                                            {{ $driver['available']->count() ?? 0 }}
                                        </div>

                                        <button type="button" class="btn btn-sm ui-eye-btn"
                                            data-bs-target="#availDriversList" aria-controls="availDriversList"
                                            aria-expanded="false">
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
                                        @forelse($driver['available'] as $dr)
                                            <div class="ui-list-item py-1 small">{{ $dr->name }}</div>
                                        @empty
                                            <div class="text-muted small">No available drivers.</div>
                                        @endforelse
                                    </div>

                                    @if (($driver['available']->count() ?? 0) > 5)
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

                    {{-- ON TRIP DRIVERS --}}
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card ui-available-card border-bottom border-4 border-0 border-primary"
                            style="margin-bottom:10px;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-muted small fw-semibold">
                                        On Trip Drivers 🚚
                                    </div>

                                    <div class="d-flex align-items-center gap-3 flex-shrink-0">
                                        <div class="ui-available-number text-primary">
                                            {{ $driver['onTrip']->count() ?? 0 }}
                                        </div>

                                        <button type="button" class="btn btn-sm ui-eye-btn"
                                            data-bs-target="#onTripDriversList" aria-controls="onTripDriversList"
                                            aria-expanded="false">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="collapse mt-0 ui-available-dropdown" id="onTripDriversList">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body py-2">

                                    <div class="ui-paginated-list" data-per-page="5" data-target="ontripdrivers">
                                        @forelse(($driver['onTrip'] ?? collect()) as $dr)
                                            <div class="ui-list-item py-1 small">{{ $dr->name }}</div>
                                        @empty
                                            <div class="text-muted small">No on-trip drivers.</div>
                                        @endforelse
                                    </div>

                                    @if (($driver['onTrip']->count() ?? 0) > 5)
                                        <div class="d-flex justify-content-end align-items-center gap-2 mt-2 ui-list-controls"
                                            data-controls="ontripdrivers">
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
                <div class="row g-3 mb-1">

                    {{-- TOTAL HELPERS --}}
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card ui-available-card border-bottom border-4 border-0 border-primary"
                            style="margin-bottom:10px;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-muted small fw-semibold">
                                        Total Helper 👤
                                    </div>
                                    <div class="d-flex align-items-center gap-3 flex-shrink-0">
                                        <div class="ui-available-number text-primary">
                                            {{ $helper['active']->count() ?? 0 }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- INACTIVE --}}
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card ui-available-card border-bottom border-4 border-0 border-danger"
                            style="margin-bottom:10px;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-muted small fw-semibold">
                                        Inactive ⛔
                                    </div>
                                    <div class="d-flex align-items-center gap-3 flex-shrink-0">
                                        <div class="ui-available-number text-danger">
                                            {{ $helper['inactive']->count() ?? 0 }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- DRIVER AVAILABLE --}}
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card ui-available-card border-bottom border-4 border-0 border-success"
                            style="margin-bottom:10px;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-muted small fw-semibold">
                                        Driver Available ✅
                                    </div>

                                    <div class="d-flex align-items-center gap-3 flex-shrink-0">
                                        <div class="ui-available-number text-success">
                                            {{ $helper['available']->count() ?? 0 }}
                                        </div>

                                        <button type="button" class="btn btn-sm ui-eye-btn"
                                            data-bs-target="#availHelperList" aria-controls="availHelperList"
                                            aria-expanded="false">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="collapse mt-0 ui-available-dropdown" id="availHelperList">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body py-2">

                                    <div class="ui-paginated-list" data-per-page="5" data-target="Helper">
                                        @forelse($helper['available'] as $dr)
                                            <div class="ui-list-item py-1 small">{{ $dr->name }}</div>
                                        @empty
                                            <div class="text-muted small">No available Helper.</div>
                                        @endforelse
                                    </div>

                                    @if (($helper['available']->count() ?? 0) > 5)
                                        <div class="d-flex justify-content-end align-items-center gap-2 mt-2 ui-list-controls"
                                            data-controls="Helper">
                                            <button type="button" class="btn btn-sm btn-light ui-list-prev">Prev</button>
                                            <div class="small text-muted ui-list-page">1</div>
                                            <button type="button" class="btn btn-sm btn-light ui-list-next">Next</button>
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ON TRIP Helper --}}
                    <div class="col-12 col-md-6 col-lg-3">
                        <div class="card ui-available-card border-bottom border-4 border-0 border-primary"
                            style="margin-bottom:10px;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-muted small fw-semibold">
                                        On Trip Helper 🚚
                                    </div>

                                    <div class="d-flex align-items-center gap-3 flex-shrink-0">
                                        <div class="ui-available-number text-primary">
                                            {{ $helper['onTrip']->count() ?? 0 }}
                                        </div>

                                        <button type="button" class="btn btn-sm ui-eye-btn"
                                            data-bs-target="#onTripHelperList" aria-controls="onTripHelperList"
                                            aria-expanded="false">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="collapse mt-0 ui-available-dropdown" id="onTripHelperList">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body py-2">

                                    <div class="ui-paginated-list" data-per-page="5" data-target="ontripHelper">
                                        @forelse(($helper['onTrip'] ?? collect()) as $dr)
                                            <div class="ui-list-item py-1 small">{{ $dr->name }}</div>
                                        @empty
                                            <div class="text-muted small">No on-trip Helper.</div>
                                        @endforelse
                                    </div>

                                    @if (($helper['onTrip']->count() ?? 0) > 5)
                                        <div class="d-flex justify-content-end align-items-center gap-2 mt-2 ui-list-controls"
                                            data-controls="ontripHelpers">
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
            </div>
        </div>


        {{-- Driver List --}}
        <div class="card shadow-sm">
            <div class="card-header bg-white border-0">

                {{-- ADD MODAL (Driver / Helper) --}}
                <div class="modal fade" id="addPersonModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-centered">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title fw-bold">Add Driver / Helper</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <form method="POST" id="addPersonForm" action="{{ route('admin.employees.store') }}"
                                enctype="multipart/form-data">
                                @csrf

                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="mb-3 text-center">
                                                <label class="form-label d-block">Profile Picture</label>

                                                <!-- PREVIEW -->
                                                <div class="circle-wrapper mb-2 d-none" id="previewWrapper">
                                                    <img id="addPreviewImage">
                                                </div>

                                                <!-- INPUT -->
                                                <input type="file" name="profile_photo"
                                                    class="form-control border border-primary" accept="image/*"
                                                    id="addPhotoInput">
                                            </div>
                                        </div>
                                        <div class="col-md">
                                            <div class="row">
                                                <div class="mb-3 col-md">
                                                    <small class="fw-bolder text-info">Name <span
                                                            class="text-danger">*</span></small>
                                                    <input type="text" name="name"
                                                        class="form-control border border-primary" required>
                                                </div>

                                                <div class="mb-3 col-md">
                                                    <small class="fw-bolder text-info">Email <span
                                                            class="text-danger">*</span></small>
                                                    <input type="email" name="email"
                                                        class="form-control border border-primary"
                                                        placeholder="example@email.com">
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="mb-3 col-md">
                                                    <label class="form-label">Birthday</label>
                                                    <input type="date" name="birthday"
                                                        class="form-control border border-primary">
                                                </div>

                                                <div class="mb-3 col-md">
                                                    <label class="form-label">Contact Number</label>
                                                    <input type="text" name="contact_number"
                                                        class="form-control border border-primary"
                                                        placeholder="+63 9XXXXXXXXX">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Address</label>
                                                <textarea name="address" class="form-control border border-primary" rows="2" placeholder="Complete address"></textarea>
                                            </div>
                                            <div class="row">
                                                <div class="mb-3 col-md">
                                                    <label class="form-label">Status</label>
                                                    <select name="type" class="form-select" required>
                                                        <option value="driver">Driver</option>
                                                        <option value="inactive">Helper</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3 col-md">
                                                    <label class="form-label">Account Status</label>
                                                    <select name="status" class="form-select" required>
                                                        <option value="1">Active</option>
                                                        <option value="0">Inactive</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                        Cancel
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        Save
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>

                <div class="card-body">

                    {{-- SEARCH + ACTION --}}
                    <div class="d-flex justify-content-between mb-3">

                        <input type="text" id="peopleSearchInput" class="form-control border border-primary w-25"
                            placeholder="Search Employees...">

                        <div class="d-flex gap-2">
                            <button class="btn btn-primary btn-white" data-bs-toggle="modal"
                                data-bs-target="#addPersonModal">
                                ➕ Add Employee
                            </button>
                            <button class="btn btn-danger" id="deleteSelectedBtn">
                                🗑 Delete Selected
                            </button>
                        </div>

                    </div>

                    {{-- TABLE --}}
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead>
                                <tr>
                                    <th style="width:40px;">
                                        <input type="checkbox" id="selectAllDrivers">
                                    </th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Availability</th>
                                    <th>Status</th>

                                    <th class="text-end" style="width:160px;">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($employees as $staff)
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="driver-check" value="{{ $staff->id }}">
                                        </td>

                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="{{ $staff->profile_photo ? asset('storage/' . $staff->profile_photo) : asset('assets/images/page-img/14.png') }}"
                                                    class="ui-avatar">
                                                <span>{{ $staff->name }}</span>
                                            </div>
                                            <small class="text-primary">{{ $staff->email }}</small>
                                        </td>
                                        <td>
                                            <span class="fw-bolder">{{ strtoupper($staff->type) }}</span>
                                        </td>


                                        <td>
                                            <span class="badge bg-info">
                                                {{ ucfirst($staff->is_available ? 'Available' : 'unAvailable') }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $staff->is_active ? 'info' : 'danger' }}">
                                                {{ ucfirst($staff->is_active ? 'Active' : 'inActive') }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <button class="btn btn-sm btn-light toggle-details"
                                                    data-id="driver-{{ $staff->id }}">
                                                    &lt;
                                                </button>

                                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                    data-bs-target="#editDriverModal-{{ $staff->id }}">
                                                    ✏️
                                                </button>

                                                <button class="btn btn-sm btn-info driver-docs-btn" data-bs-toggle="modal"
                                                    data-bs-target="#personDocsModal" data-type="driver"
                                                    data-id="{{ $staff->id }}" data-name="{{ $staff->name }}">
                                                    📄
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr class="details-row d-none" id="driver-{{ $staff->id }}-details">
                                        <td colspan="6">
                                            <div class="p-3 bg-light rounded">

                                                <div><strong>📱 Contact:</strong>
                                                    {{ $staff->contact_number ?? '-' }}
                                                </div>

                                                <div><strong>📍 Address:</strong>
                                                    {{ $staff->address ?? '-' }}
                                                </div>

                                                <div><strong>🎂 Birthday:</strong>
                                                    {{ $staff->birthday?->format('M d, Y') ?? '-' }}
                                                </div>

                                                <div>
                                                    <strong>🚨 Emergency:</strong>
                                                    {{ $staff->emergency_contact_person ?? '-' }}
                                                    ({{ $staff->emergency_contact_number ?? '-' }})
                                                </div>

                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">
                                            No drivers found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

        {{-- ================= EDIT DRIVER MODALS ================= --}}

        @foreach ($employees as $staff)
            <div class="modal fade" id="editDriverModal-{{ $staff->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">Edit Driver</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <form method="POST" action="{{ route('admin.employees.update', $staff->id) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3 text-center">
                                            <label class="form-label d-block">Profile Picture</label>

                                            <!-- PREVIEW -->
                                            <div class="circle-wrapper mb-2 text-center">
                                                <img id="preview-driver-{{ $staff->id }}"
                                                    src="{{ $staff->profile_photo ? asset('storage/' . $staff->profile_photo) : asset('assets/images/page-img/14.png') }}">
                                            </div>

                                            <!-- INPUT -->
                                            <input type="file" name="profile_photo"
                                                class="form-control edit-photo-input"
                                                data-target="preview-driver-{{ $staff->id }}"
                                                data-original="{{ $staff->profile_photo ? asset('storage/' . $staff->profile_photo) : '' }}">

                                            {{-- CONTROLS (left aligned) --}}
                                            <div class="text-start">

                                                @if ($staff->profile_photo)
                                                    <div class="form-check mb-2">
                                                        <input class="form-check-input" type="checkbox"
                                                            name="remove_photo" value="1">
                                                        <label class="form-check-label text-danger">
                                                            Remove current picture
                                                        </label>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="row">
                                            <div class="mb-3 col-md">
                                                <small class="fw-bolder text-info">NAME <sup
                                                        class="text-danger">*</sup></small>
                                                <input type="text" name="name" value="{{ $staff->name }}"
                                                    class="form-control border border-primary" required>
                                            </div>

                                            <div class="mb-3 col-md">
                                                <small class="fw-bolder text-info">
                                                    EMAIL <sup class="text-danger">*</sup>
                                                </small>
                                                <input type="email" name="email" value="{{ $staff->email }}"
                                                    class="form-control border border-primary"
                                                    placeholder="example@email.com">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="mb-3 col-md">
                                                <small class="fw-bolder text-info">
                                                    BIRTHDAY <sup class="text-danger">*</sup>
                                                </small>
                                                <input type="date" name="birthday"
                                                    value="{{ $staff->date_of_birth?->format('m-d-Y') }}"
                                                    class="form-control border border-primary">
                                            </div>

                                            <div class="mb-3 col-md">
                                                <small class="fw-bolder text-info">
                                                    CONTACT NUMBER <sup class="text-danger">*</sup>
                                                </small>
                                                <input type="text" name="contact_number"
                                                    value="{{ $staff->contact_number }}"
                                                    class="form-control border border-primary"
                                                    placeholder="+63 9XXXXXXXXX">
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <small class="fw-bolder text-info">
                                                ADDRESS <sup class="text-danger">*</sup>
                                            </small>
                                            <textarea name="address" class="form-control border border-primary" rows="2" placeholder="Complete address">
                                                {{ $staff->address }}
                                            </textarea>
                                        </div>
                                        <div class="row">
                                            <div class="mb-3 col-md">
                                                <label class="form-label">Status</label>
                                                <select name="type" class="form-select" required>
                                                    <option value="driver"
                                                        {{ $staff->type == 'driver' ? 'selected' : '' }}>Driver</option>
                                                    <option value="inactive"
                                                        {{ $staff->type == 'inactive' ? 'selected' : '' }}>Helper</option>
                                                </select>
                                            </div>
                                            <div class="mb-3 col-md">
                                                <label class="form-label">Account Status</label>
                                                <select name="status" class="form-select" required>
                                                    <option value="1" {{ $staff->status == 1 ? 'selected' : '' }}>
                                                        Active</option>
                                                    <option value="0" {{ $staff->status == 0 ? 'selected' : '' }}>
                                                        Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button class="btn btn-primary">Update</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        @endforeach

        <div class="modal fade" id="personDocsModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <form method="POST" action="{{-- {{ route($prefix . '.person-docs.save') }} --}}" enctype="multipart/form-data">
                        @csrf

                        <div class="modal-header">
                            <h5 class="modal-title" id="docsModalTitle">Documents</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">

                            <input type="hidden" name="person_id" id="personId">
                            <input type="hidden" name="person_type" id="personTypeDoc">

                            <div class="mb-3">
                                <strong id="driverName"></strong>
                            </div>

                            <hr>

                            {{-- DOCUMENTS --}}
                            <div id="documentsContainer"></div>

                        </div>

                        <div class="modal-footer">
                            <button class="btn btn-primary">Save</button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    </div>

    <!-- DELETE MODAL -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title text-danger">Delete Drivers</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    Are you sure you want to delete selected drivers?
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            /* =========================================
             * 1. ADD DRIVER FORM ACTION
             * ========================================= */
            const initAddForm = () => {
                const form = document.getElementById('addPersonForm');
                const type = document.getElementById('personType');

                if (!form || !type) return;

                const actions = {
                    driver: @json(route('admin.employees.store'))
                };

                const updateAction = () => {
                    form.action = actions[type.value] || '';
                };

                updateAction();
                type.addEventListener('change', updateAction);
            };


            /* =========================================
             * 2. COLLAPSE (EYE BUTTON)
             * ========================================= */
            const initCollapse = () => {
                if (!window.bootstrap) return;

                document.querySelectorAll('.ui-eye-btn').forEach(btn => {
                    btn.addEventListener('click', () => {
                        const target = document.querySelector(btn.dataset.bsTarget);
                        if (!target) return;

                        const instance = bootstrap.Collapse.getOrCreateInstance(target, {
                            toggle: false
                        });
                        target.classList.contains('show') ? instance.hide() : instance.show();
                    });
                });
            };


            /* =========================================
             * 3. SELECT ALL CHECKBOX
             * ========================================= */
            const initCheckbox = () => {
                const selectAll = document.getElementById('selectAllDrivers');

                if (!selectAll) return;

                selectAll.addEventListener('change', () => {
                    document.querySelectorAll('.driver-check').forEach(cb => {
                        cb.checked = selectAll.checked;
                    });
                });
            };


            /* =========================================
             * 4. SEARCH FILTER
             * ========================================= */
            const initSearch = () => {
                const input = document.getElementById('peopleSearchInput');
                if (!input) return;

                input.addEventListener('input', () => {
                    const q = input.value.toLowerCase();

                    document.querySelectorAll('tbody tr').forEach(row => {
                        row.style.display = row.innerText.toLowerCase().includes(q) ? '' :
                            'none';
                    });
                });
            };


            /* =========================================
             * 5. DRIVER DOCUMENT MODAL
             * ========================================= */
            const initDocsModal = () => {
                const docs = ['DRUG_TEST', 'NBI', 'SSS', 'PHILHEALTH', 'PAGIBIG', 'LICENSE'];

                document.addEventListener('click', async (e) => {
                    const btn = e.target.closest('.driver-docs-btn');
                    if (!btn) return;

                    const {
                        id,
                        name,
                        type
                    } = btn.dataset;

                    document.getElementById('personId').value = id;
                    document.getElementById('personTypeDoc').value = type;
                    document.getElementById('driverName').innerText = name;

                    const container = document.getElementById('documentsContainer');
                    container.innerHTML = '';

                    let existing = {};

                    try {
                        const prefix = "{{ $prefix }}";
                        const res = await fetch(`/${prefix}/person-docs/${id}/${type}`);
                        const data = await res.json();

                        data.forEach(d => {

                            const key = d.type.toUpperCase().replace(/\s+/g, '_').trim();
                            existing[key] = d;
                        });

                    } catch (err) {
                        console.error(err);
                    }

                    docs.forEach(doc => {
                        const file = existing[doc]?.file_path;
                        const expiry = existing[doc]?.expiry_date || '';


                        container.innerHTML += `
                        <div class="mb-3">
                            <label class="fw-bold">${doc}</label>
                        
                            <div class="mb-1">
                                ${file ? `<a href="/storage/${file}" target="_blank">View File</a>` : 'No file'}
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <input type="date" name="expiry[${doc}]" value="${expiry}" class="form-control">
                                
                                    <div class="form-check mt-1">
                                        <input type="checkbox" name="delete_expiry[${doc}]" value="1">
                                        <label class="text-danger small">Remove expiry</label>
                                    </div>
                                </div>
                            
                                <div class="col-md-6">
                                    <input type="file" name="file[${doc}]" class="form-control">
                                
                                    ${file ? `
                                                                                                                                                                                                                                                                                                                                                                            <div class="form-check mt-1">
                                                                                                                                                                                                                                                                                                                                                                                <input type="checkbox" name="delete_file[${doc}]" value="1">
                                                                                                                                                                                                                                                                                                                                                                                <label class="text-danger small">Remove file</label>
                                                                                                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                                                                                                        ` : ''}
                                </div>
                            </div>
                        </div>
                        `;
                    });
                });
            };


            /* =========================================
             * 6. TOGGLE DETAILS
             * ========================================= */
            const initDetailsToggle = () => {
                document.addEventListener('click', (e) => {
                    const btn = e.target.closest('.toggle-details');
                    if (!btn) return;

                    const row = document.getElementById(btn.dataset.id + '-details');
                    if (row) row.classList.toggle('d-none');
                });
            };


            /* =========================================
             * 7. IMAGE PREVIEW
             * ========================================= */
            const initImagePreview = () => {

                document.addEventListener('change', function(e) {

                    const input = e.target.closest('.edit-photo-input');
                    if (!input) return;

                    const img = document.getElementById(input.dataset.target);
                    if (!img) return;

                    const original = input.dataset.original || ''; // original image

                    const file = input.files[0];

                    // ❌ NO FILE → RESET
                    if (!file) {
                        if (original) {
                            img.src = original; // balik sa old image
                        } else {
                            img.src = ''; // or blank
                        }
                        return;
                    }

                    // ✔ WITH FILE → PREVIEW
                    const reader = new FileReader();
                    reader.onload = function(ev) {
                        img.src = ev.target.result;
                    };
                    reader.readAsDataURL(file);

                });
            };

            /* =========================================
             * ADD MODAL IMAGE PREVIEW
             * ========================================= */
            const initAddPreview = () => {
                const input = document.getElementById('addPhotoInput');
                const img = document.getElementById('addPreviewImage');
                const wrapper = document.getElementById('previewWrapper');

                if (!input || !img) return;

                input.addEventListener('change', e => {
                    const file = e.target.files[0];

                    if (!file) {
                        img.classList.add('d-none');
                        wrapper?.classList.add('d-none');
                        return;
                    }

                    const reader = new FileReader();
                    reader.onload = ev => {
                        img.src = ev.target.result;
                        img.classList.remove('d-none');
                        wrapper?.classList.remove('d-none');
                    };

                    reader.readAsDataURL(file);
                });
            };

            const initDeleteSelected = () => {
                const btn = document.getElementById('deleteSelectedBtn');
                if (!btn) return;

                btn.addEventListener('click', () => {

                    const selected = [...document.querySelectorAll('.driver-check:checked')]
                        .map(cb => cb.value);

                    if (selected.length === 0) {
                        alert('No drivers selected.');
                        return;
                    }

                    // ✔ show modal only
                    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
                    modal.show();

                    // ✔ store selected IDs
                    window.selectedDriverIds = selected;
                });
            };

            document.getElementById('confirmDeleteBtn')?.addEventListener('click', () => {

                const ids = window.selectedDriverIds || [];

                if (ids.length === 0) return;

                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `{{ route('admin.employees.delete-multiple') }}`;

                form.innerHTML = `
        @csrf
        ${ids.map(id => `<input type="hidden" name="ids[]" value="${id}">`).join('')}
    `;

                document.body.appendChild(form);
                form.submit();
            });


            /* =========================================
             * INIT ALL
             * ========================================= */
            initAddForm();
            initCollapse();
            initCheckbox();
            initSearch();
            initDocsModal();
            initDetailsToggle();
            initImagePreview();
            initAddPreview();
            initDeleteSelected();

        });
    </script>
@endpush

@push('styles')
    <style>
        /* =========================================
                                                                                                                                                                                                                                                                                                                                                                                                                                                                       1. GLOBAL UI TOKENS
                                                                                                                                                                                                                                                                                                                                                                                                                                                                    ========================================= */
        :root {
            --ui-radius: 14px;
            --ui-shadow: 0 10px 25px rgba(0, 0, 0, .06);
            --ui-shadow-hover: 0 14px 35px rgba(0, 0, 0, .10);
            --ui-text-muted: #667085;
        }


        /* =========================================
                                                                                                                                                                                                                                                                                                                                                                                                                                                                       2. HERO HEADER
                                                                                                                                                                                                                                                                                                                                                                                                                                                                    ========================================= */
        .ui-hero {
            border-radius: 18px;
            padding: 24px;
            background: linear-gradient(135deg, #ffffff, #f9fafb);
            box-shadow: var(--ui-shadow);
        }


        /* =========================================
                                                                                                                                                                                                                                                                                                                                                                                                                                                                       3. SUMMARY CARDS
                                                                                                                                                                                                                                                                                                                                                                                                                                                                    ========================================= */
        .ui-available-card {
            border-radius: var(--ui-radius);
            box-shadow: var(--ui-shadow);
            transition: 0.2s ease;
            margin-bottom: 10px;
        }

        .ui-available-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--ui-shadow-hover);
        }

        .ui-available-number {
            font-size: 28px;
            font-weight: 800;
        }


        /* =========================================
                                                                                                                                                                                                                                                                                                                                                                                                                                                                       4. BUTTON SYSTEM
                                                                                                                                                                                                                                                                                                                                                                                                                                                                    ========================================= */
        .btn {
            border-radius: 10px;
            font-weight: 600;
        }

        .ui-eye-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1px solid #d0d5dd;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
        }

        .ui-eye-btn:hover {
            background: #f2f4f7;
        }


        /* =========================================
                                                                                                                                                                                                                                                                                                                                                                                                                                                                       5. TABLE
                                                                                                                                                                                                                                                                                                                                                                                                                                                                    ========================================= */
        .table {
            border-radius: var(--ui-radius);
            overflow: hidden;
        }

        .table thead th {
            font-size: 13px;
            font-weight: 700;
            color: #344054;
        }

        .table tbody tr:hover {
            background: #f9fafb;
        }


        /* =========================================
                                                                                                                                                                                                                                                                                                                                                                                                                                                                       6. SEARCH + ACTION BAR
                                                                                                                                                                                                                                                                                                                                                                                                                                                                    ========================================= */
        .ui-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }

        .ui-search {
            width: 260px;
        }

        .ui-search input {
            border-radius: 10px;
            height: 38px;
            font-weight: 500;
        }


        /* =========================================
                                                                                                                                                                                                                                                                                                                                                                                                                                                                       7. MODALS
                                                                                                                                                                                                                                                                                                                                                                                                                                                                    ========================================= */
        .modal-content {
            border-radius: 14px;
        }

        .modal-header {
            border-bottom: none;
        }

        .modal-footer {
            border-top: none;
        }


        /* =========================================
                                                                                                                                                                                                                                                                                                                                                                                                                                                                       8. IMAGE (CIRCLE)
                                                                                                                                                                                                                                                                                                                                                                                                                                                                    ========================================= */
        .circle-wrapper {
            width: 180px;
            height: 180px;
            margin: auto;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid #ddd;
        }

        .circle-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }


        /* =========================================
                                                                                                                                                                                                                                                                                                                                                                                                                                                                       9. DROPDOWN LIST (AVAILABLE)
                                                                                                                                                                                                                                                                                                                                                                                                                                                                    ========================================= */
        .ui-available-dropdown {
            margin-top: 6px;
        }

        .ui-list-controls .btn {
            border-radius: 999px;
            padding: 4px 10px;
        }


        .ui-avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, .1);
        }


        /* =========================================
                                                                                                                                                                                                                                                                                                                                                                                                                                                                       10. MOBILE RESPONSIVE
                                                                                                                                                                                                                                                                                                                                                                                                                                                                    ========================================= */
        @media (max-width: 768px) {

            .ui-toolbar {
                flex-direction: column;
                align-items: stretch;
            }

            .ui-search {
                width: 100%;
            }

            .btn {
                width: 100%;
            }
        }


        /* =========================================
                                                                                                                                                                                                                                                                                                                                                                                                                                                                       11. SMALL SCREEN TWEAK
                                                                                                                                                                                                                                                                                                                                                                                                                                                                    ========================================= */
        @media (max-width: 400px) {
            .ui-available-number {
                font-size: 22px;
            }
        }
    </style>
@endpush
