@php
    $prefix = session('layout') === 'flash' ? 'flash' : 'owner';
@endphp


@extends('layouts.flash')

@section('title', 'Drivers')

@section('content')
    <div class="container-fluid py-4">

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

        {{-- SUMMARY CARDS (Available-style) --}}
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
                                    {{ $drivers->count() ?? ($stats['total_drivers'] ?? 0) }}
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
                                    {{ $stats['inactive'] ?? 0 }}
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
                                    {{ $availableDrivers->count() ?? 0 }}
                                </div>

                                <button type="button" class="btn btn-sm ui-eye-btn" data-bs-target="#availDriversList"
                                    aria-controls="availDriversList" aria-expanded="false">
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
                                @forelse($availableDrivers as $dr)
                                    <div class="ui-list-item py-1 small">{{ $dr->name }}</div>
                                @empty
                                    <div class="text-muted small">No available drivers.</div>
                                @endforelse
                            </div>

                            @if (($availableDrivers->count() ?? 0) > 5)
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
                                    {{ $onTripDrivers->count() ?? ($stats['on_trip_drivers'] ?? 0) }}
                                </div>

                                <button type="button" class="btn btn-sm ui-eye-btn" data-bs-target="#onTripDriversList"
                                    aria-controls="onTripDriversList" aria-expanded="false">
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
                                @forelse(($onTripDrivers ?? collect()) as $dr)
                                    <div class="ui-list-item py-1 small">{{ $dr->name }}</div>
                                @empty
                                    <div class="text-muted small">No on-trip drivers.</div>
                                @endforelse
                            </div>

                            @if (($onTripDrivers->count() ?? 0) > 5)
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

        {{-- Driver List --}}
        <div class="card shadow-sm">
            <div class="card-header bg-white border-0">

                {{-- ADD MODAL (Driver / Helper) --}}
                <div class="modal fade" id="addPersonModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-md modal-dialog-centered">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title fw-bold">Add Driver / Helper</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <form method="POST" id="addPersonForm" action="{{ route('flash.drivers.store') }}"
                                enctype="multipart/form-data">
                                @csrf

                                <div class="modal-body">
                                    <input type="hidden" name="type" value="driver">

                                    <div class="mb-3 text-center">
                                        <label class="form-label d-block">Profile Picture</label>

                                        <!-- PREVIEW -->
                                        <div class="circle-wrapper mb-2 d-none" id="previewWrapper">
                                            <img id="addPreviewImage">
                                        </div>

                                        <!-- INPUT -->
                                        <input type="file" name="profile_photo" class="form-control" accept="image/*"
                                            id="addPhotoInput">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Name</label>
                                        <input type="text" name="name" class="form-control" required>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control"
                                            placeholder="example@email.com">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Birthday</label>
                                        <input type="date" name="birthday" class="form-control">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Contact Number</label>
                                        <input type="text" name="contact_number" class="form-control"
                                            placeholder="+63 9XXXXXXXXX">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Address</label>
                                        <textarea name="address" class="form-control" rows="2" placeholder="Complete address"></textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Emergency Contact Person</label>
                                        <input type="text" name="emergency_contact_person" class="form-control"
                                            placeholder="Full name">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Emergency Contact Number</label>
                                        <input type="text" name="emergency_contact_number" class="form-control"
                                            placeholder="+63 9XXXXXXXXX">
                                    </div>

                                    <div class="mb-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select" required>
                                            <option value="active">Active</option>
                                            <option value="on-leave">On Leave</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
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

                        <input type="text" id="peopleSearchInput" class="form-control w-25"
                            placeholder="Search drivers...">

                        <div class="d-flex gap-2">
                            <button class="btn btn-danger" id="deleteSelectedBtn">
                                🗑 Delete Selected
                            </button>

                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPersonModal">
                                ➕ Add Driver
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
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Availability</th>
                                    <th class="text-end" style="width:160px;">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($drivers as $driver)
                                    <tr>
                                        <!-- MAIN ROW (VISIBLE) -->
                                        <td>
                                            <input type="checkbox" class="driver-check" value="{{ $driver->id }}">
                                        </td>

                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="{{ $driver->profile_photo ? asset('storage/' . $driver->profile_photo) : asset('assets/images/page-img/14.png') }}"
                                                    class="ui-avatar">
                                                <span>{{ $driver->name }}</span>
                                            </div>
                                        </td>

                                        <td>{{ $driver->email }}</td>

                                        <td>
                                            <span class="badge bg-success">
                                                {{ ucfirst($driver->status) }}
                                            </span>
                                        </td>

                                        <td>
                                            <span class="badge bg-info">
                                                {{ ucfirst($driver->availability_status ?? 'available') }}
                                            </span>
                                        </td>

                                        <td>
                                            <div class="d-flex gap-1">
                                                <button class="btn btn-sm btn-light toggle-details"
                                                    data-id="driver-{{ $driver->id }}">
                                                    &lt;
                                                </button>

                                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                    data-bs-target="#editDriverModal-{{ $driver->id }}">
                                                    ✏️
                                                </button>

                                                <button class="btn btn-sm btn-info driver-docs-btn" data-bs-toggle="modal"
                                                    data-bs-target="#personDocsModal" data-type="driver"
                                                    data-id="{{ $driver->id }}" data-name="{{ $driver->name }}">
                                                    📄
                                                </button>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr class="details-row d-none" id="driver-{{ $driver->id }}-details">
                                        <td colspan="6">
                                            <div class="p-3 bg-light rounded">

                                                <div><strong>📱 Contact:</strong>
                                                    {{ $driver->contact_number ?? '-' }}
                                                </div>

                                                <div><strong>📍 Address:</strong>
                                                    {{ $driver->address ?? '-' }}
                                                </div>

                                                <div><strong>🎂 Birthday:</strong>
                                                    {{ $driver->birthday?->format('M d, Y') ?? '-' }}
                                                </div>

                                                <div>
                                                    <strong>🚨 Emergency:</strong>
                                                    {{ $driver->emergency_contact_person ?? '-' }}
                                                    ({{ $driver->emergency_contact_number ?? '-' }})
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

        @foreach ($drivers as $driver)
            <div class="modal fade" id="editDriverModal-{{ $driver->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-md modal-dialog-centered">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">Edit Driver</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <form method="POST" action="{{ route('flash.drivers.update', $driver->id) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="modal-body">
                                <div class="mb-3">
                                    <label class="form-label d-block text-center">Profile Picture</label>

                                    {{-- IMAGE (centered) --}}
                                    <div class="circle-wrapper mb-2 text-center">
                                        <img id="preview-driver-{{ $driver->id }}"
                                            src="{{ $driver->profile_photo ? asset('storage/' . $driver->profile_photo) : asset('assets/images/page-img/14.png') }}">
                                    </div>

                                    <input type="file" name="profile_photo" class="form-control edit-photo-input"
                                        data-target="preview-driver-{{ $driver->id }}"
                                        data-original="{{ $driver->profile_photo ? asset('storage/' . $driver->profile_photo) : '' }}">

                                    {{-- CONTROLS (left aligned) --}}
                                    <div class="text-start">

                                        @if ($driver->profile_photo)
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="remove_photo"
                                                    value="1">
                                                <label class="form-check-label text-danger">
                                                    Remove current picture
                                                </label>
                                            </div>
                                        @endif



                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Name</label>
                                    <input class="form-control" name="name" value="{{ $driver->name }}" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email"
                                        value="{{ $driver->email }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Birthday</label>
                                    <input type="date" name="birthday" class="form-control"
                                        value="{{ $driver->birthday?->format('Y-m-d') }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Contact Number</label>
                                    <input type="text" class="form-control" name="contact_number"
                                        value="{{ $driver->contact_number }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea class="form-control" name="address" rows="2">{{ $driver->address }}</textarea>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Emergency Contact Person</label>
                                    <input type="text" class="form-control" name="emergency_contact_person"
                                        value="{{ $driver->emergency_contact_person }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Emergency Contact Number</label>
                                    <input type="text" class="form-control" name="emergency_contact_number"
                                        value="{{ $driver->emergency_contact_number }}">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="active" {{ $driver->status === 'active' ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="on-leave" {{ $driver->status === 'on-leave' ? 'selected' : '' }}>On
                                            Leave</option>
                                        <option value="inactive" {{ $driver->status === 'inactive' ? 'selected' : '' }}>
                                            Inactive</option>
                                    </select>
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

                    <form method="POST" action="{{ route($prefix . '.person-docs.save') }}"
                        enctype="multipart/form-data">
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
                    driver: @json(route('flash.drivers.store'))
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
                form.action = `{{ route($prefix . '.drivers.deleteMultiple') }}`;

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
