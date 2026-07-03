@extends('layouts.owner')

@section('title', 'Destinations')

@section('content')
    <div class="container-fluid py-4">

        {{-- Header (UI HERO – Destinations) --}}
        <div class="ui-hero p-4 mb-4">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                <div>
                    <h4 class="mb-1 fw-bold">Destinations</h4>
                    <div class="text-muted small">
                        Routes &amp; Job Points — planning, costing, and optimisation.
                    </div>
                </div>
            </div>
        </div>

        {{-- Destination List --}}
        <div class="card shadow-sm">

            {{-- HEADER --}}
            <div class="card-header bg-white border-0">
                <div class="ui-card-header">
                    <h5 class="mb-0 fw-bold ui-card-title">Destinations &amp; Rates</h5>

                    <form method="GET" action="{{ route('owner.destinations.index') }}"
                        class="ui-card-actions ui-card-actions--destinations">

                        {{-- keep tab on search --}}
                        <input type="hidden" name="tab" value="{{ request('tab', '6w') }}">

                        <div class="ui-searchbox ui-searchbox--destinations">
                            <i class="bi bi-search"></i>
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                                placeholder="Search store code, name, area...">
                        </div>

                        @if (request('q'))
                            <a href="{{ route('owner.destinations.index', request()->except('q', 'page6w', 'pageL300')) }}"
                                class="btn btn-outline-secondary btn-sm ui-btn-40 ui-btn-40--block">
                                Clear
                            </a>
                        @endif

                        <button type="button" class="btn btn-primary btn-sm ui-btn-40 ui-btn-40--block"
                            data-bs-toggle="modal" data-bs-target="#addDestinationModal">
                            <i class="bi bi-plus-lg me-1"></i> Add Destination
                        </button>
                    </form>
                </div>
            </div>

            {{-- ADD DESTINATION MODAL (outside header) --}}
            <div class="modal fade" id="addDestinationModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">Add Destination</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <form method="POST" action="{{ route('owner.destinations.store') }}">
                            @csrf
                            <div class="modal-body">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">Store Code</label>
                                        <input type="text" name="store_code" class="form-control" required>
                                    </div>

                                    <div class="col-md-8">
                                        <label class="form-label">Store Name</label>
                                        <input type="text" name="store_name" class="form-control" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Area</label>
                                        <input type="text" name="area" class="form-control">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Truck Type</label>
                                        <select name="truck_type" class="form-select" required>
                                            <option value="">-- Select --</option>
                                            <option value="6W">6W</option>
                                            <option value="L300">L300</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Rate</label>
                                        <input type="number" name="rate" step="0.01" class="form-control" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Remarks</label>
                                        <input type="text" name="remarks" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-primary">Save Destination</button>
                            </div>
                        </form>

                    </div>
                </div>
            </div>

            <div class="card-body">

                {{-- TABS --}}
                @php $activeTab = request('tab', '6w'); @endphp

                <ul class="nav nav-tabs mb-3 ui-tabs" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link {{ $activeTab === '6w' ? 'active' : '' }}" data-bs-toggle="tab"
                            data-bs-target="#tab-6w" type="button">
                            <span class="d-none d-md-inline">🚛 6W Truck</span>
                            <span class="d-inline d-md-none">🚛 6W</span>
                        </button>
                    </li>

                    <li class="nav-item">
                        <button class="nav-link {{ $activeTab === 'l300' ? 'active' : '' }}" data-bs-toggle="tab"
                            data-bs-target="#tab-l300" type="button">
                            <span class="d-none d-md-inline">🚚 L300</span>
                            <span class="d-inline d-md-none">🚚 L3</span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content">

                    {{-- 6W TAB --}}
                    <div class="tab-pane fade {{ $activeTab === '6w' ? 'show active' : '' }}" id="tab-6w">

                        {{-- MOBILE --}}
                        <div class="d-block d-lg-none">
                            @forelse($destinations6w as $d)
                                <div class="card border-0 shadow-sm mb-3 ui-mobile-destination">
                                    <div class="card-body">

                                        {{-- STORE NAME + RATE --}}
                                        <div class="ui-dest-header">
                                            <div class="ui-dest-name">{{ $d->store_name }}</div>
                                            <div class="ui-dest-rate ui-rate-badge">
                                                ₱ {{ number_format($d->rate, 2) }}
                                            </div>
                                        </div>

                                        {{-- DETAILS --}}
                                        <div class="mt-3 ui-dest-meta">
                                            <div class="ui-dest-row">
                                                <span class="ui-dest-label">Code</span>
                                                <span class="ui-dest-value">{{ $d->store_code ?? '-' }}</span>
                                            </div>
                                            <div class="ui-dest-row">
                                                <span class="ui-dest-label">Area</span>
                                                <span class="ui-dest-value">{{ $d->area ?? '-' }}</span>
                                            </div>
                                            <div class="ui-dest-row">
                                                <span class="ui-dest-label">Remarks</span>
                                                <span class="ui-dest-value">{{ $d->remarks ?? '-' }}</span>
                                            </div>
                                        </div>

                                        {{-- ACTIONS --}}
                                        <div class="mt-3 d-flex justify-content-center gap-2">
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                data-bs-target="#editDestinationModal-{{ $d->id }}">✏️</button>

                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteDestinationModal" data-id="{{ $d->id }}"
                                                data-name="{{ $d->store_name }}">
                                                🗑️
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <div class="text-muted mb-2"><i class="bi bi-geo-alt fs-3"></i></div>
                                    <div class="fw-semibold">No destinations found</div>
                                </div>
                            @endforelse
                        </div>

                        {{-- DESKTOP/TABLET --}}
                        <div class="d-none d-lg-block">
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Store Code</th>
                                            <th>Store Name</th>
                                            <th>Area</th>
                                            <th>Rate</th>
                                            <th style="width: 140px;">Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse($destinations6w as $d)
                                            <tr class="position-relative">
                                                <td>{{ $d->store_code }}</td>
                                                <td>{{ $d->store_name }}</td>
                                                <td>{{ $d->area ?? '—' }}</td>
                                                <td>
                                                    <span class="ui-rate-badge">
                                                        ₱ {{ number_format($d->rate, 2) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-1 position-relative">

                                                        {{-- EDIT --}}
                                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                            data-bs-target="#editDestinationModal-{{ $d->id }}">
                                                            ✏️
                                                        </button>

                                                        {{-- DELETE --}}
                                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                            data-bs-target="#deleteDestinationModal"
                                                            data-id="{{ $d->id }}"
                                                            data-name="{{ $d->store_name }}">
                                                            🗑️
                                                        </button>

                                                        {{-- TOGGLE REMARKS --}}
                                                        <button class="btn btn-sm btn-info toggle-remarks"
                                                            data-id="{{ $d->id }}">
                                                            &gt;
                                                        </button>

                                                    </div>
                                                </td>
                                                {{-- ✅ OVERLAY (NOW FULL ROW WIDTH) --}}
                                                <td colspan="5" class="p-0 border-0">
                                                    <div class="remarks-overlay d-none" id="remarks-{{ $d->id }}">
                                                        {{ $d->remarks ? $d->remarks : 'No Remarks for this' }}
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">
                                                    No destinations found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- PAGINATION (6W) --}}
                        @if ($destinations6w->hasPages())
                            <div
                                class="mt-2 ui-pagination-responsive d-flex justify-content-center justify-content-lg-end flex-wrap">
                                {{ $destinations6w->appends(request()->except('page6w', 'pageL300'))->appends(['tab' => '6w'])->onEachSide(1)->links('vendor.pagination.ui-datatable') }}
                            </div>
                        @endif

                        {{-- EDIT MODALS (6W) --}}
                        @foreach ($destinations6w as $d)
                            <div class="modal fade" id="editDestinationModal-{{ $d->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">

                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Edit Destination</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <form method="POST"
                                                action="{{ route('owner.destinations.update', $d->id) }}">
                                                @csrf
                                                @method('PUT')

                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Store Code</label>
                                                        <input class="form-control" name="store_code"
                                                            value="{{ $d->store_code }}" required>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Store Name</label>
                                                        <input class="form-control" name="store_name"
                                                            value="{{ $d->store_name }}" required>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Area</label>
                                                        <input class="form-control" name="area"
                                                            value="{{ $d->area }}">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Truck Type</label>
                                                        <select name="truck_type" class="form-select" required>
                                                            <option value="6W"
                                                                {{ $d->truck_type === '6W' ? 'selected' : '' }}>6W</option>
                                                            <option value="L300"
                                                                {{ $d->truck_type === 'L300' ? 'selected' : '' }}>L300
                                                            </option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Rate</label>
                                                        <input type="number" step="0.01" class="form-control"
                                                            name="rate" value="{{ $d->rate }}" required>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Remarks</label>
                                                        <input class="form-control" name="remarks"
                                                            value="{{ $d->remarks }}">
                                                    </div>
                                                </div>

                                                <div class="d-flex justify-content-end mt-4">
                                                    <button class="btn btn-primary">Update</button>
                                                </div>
                                            </form>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- L300 TAB --}}
                    <div class="tab-pane fade {{ $activeTab === 'l300' ? 'show active' : '' }}" id="tab-l300">

                        {{-- MOBILE --}}
                        <div class="d-block d-lg-none">
                            @forelse($destinationsL300 as $d)
                                <div class="card border-0 shadow-sm mb-3 ui-mobile-destination">
                                    <div class="card-body">

                                        <div class="ui-dest-header">
                                            <div class="ui-dest-name">{{ $d->store_name }}</div>
                                            <div class="ui-dest-rate ui-rate-badge">
                                                ₱ {{ number_format($d->rate, 2) }}
                                            </div>
                                        </div>

                                        <div class="mt-3 ui-dest-meta">
                                            <div class="ui-dest-row">
                                                <span class="ui-dest-label">Code</span>
                                                <span class="ui-dest-value">{{ $d->store_code ?? '-' }}</span>
                                            </div>
                                            <div class="ui-dest-row">
                                                <span class="ui-dest-label">Area</span>
                                                <span class="ui-dest-value">{{ $d->area ?? '-' }}</span>
                                            </div>
                                            <div class="ui-dest-row">
                                                <span class="ui-dest-label">Remarks</span>
                                                <span class="ui-dest-value">{{ $d->remarks ?? '-' }}</span>
                                            </div>
                                        </div>

                                        <div class="mt-3 d-flex justify-content-center gap-2">
                                            <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                data-bs-target="#editDestinationModal-{{ $d->id }}">✏️</button>

                                            <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                data-bs-target="#deleteDestinationModal" data-id="{{ $d->id }}"
                                                data-name="{{ $d->store_name }}">
                                                🗑️
                                            </button>
                                        </div>

                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <div class="text-muted mb-2"><i class="bi bi-geo-alt fs-3"></i></div>
                                    <div class="fw-semibold">No destinations found</div>
                                </div>
                            @endforelse
                        </div>

                        {{-- DESKTOP/TABLET --}}
                        <div class="d-none d-lg-block">
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Store Code</th>
                                            <th>Store Name</th>
                                            <th>Area</th>
                                            <th>Rate</th>
                                            <th style="width: 140px;">Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                        @forelse($destinationsL300 as $d)
                                            <tr class="position-relative">
                                                <td>{{ $d->store_code }}</td>
                                                <td>{{ $d->store_name }}</td>
                                                <td>{{ $d->area ?? '—' }}</td>
                                                <td>
                                                    <span class="ui-rate-badge">
                                                        ₱ {{ number_format($d->rate, 2) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-1">
                                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                                            data-bs-target="#editDestinationModal-l300-{{ $d->id }}">✏️</button>

                                                        <button class="btn btn-sm btn-danger" data-bs-toggle="modal"
                                                            data-bs-target="#deleteDestinationModal"
                                                            data-id="{{ $d->id }}"
                                                            data-name="{{ $d->store_name }}">
                                                            🗑️
                                                        </button>
                                                        {{-- TOGGLE REMARKS --}}
                                                        <button class="btn btn-sm btn-info toggle-remarks"
                                                            data-id="{{ $d->id }}">
                                                            &gt;
                                                        </button>
                                                    </div>
                                                </td>
                                                {{-- ✅ OVERLAY (NOW FULL ROW WIDTH) --}}
                                                <td colspan="5" class="p-0 border-0">
                                                    <div class="remarks-overlay d-none" id="remarks-{{ $d->id }}">
                                                        {{ $d->remarks ? $d->remarks : 'No Remarks for this' }}
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center text-muted">No destinations found.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- PAGINATION (L300) --}}
                        @if ($destinationsL300->hasPages())
                            <div
                                class="mt-2 ui-pagination-responsive d-flex justify-content-center justify-content-lg-end flex-wrap">
                                {{ $destinationsL300->appends(request()->except('page6w', 'pageL300'))->appends(['tab' => 'l300'])->onEachSide(1)->links('vendor.pagination.ui-datatable') }}
                            </div>
                        @endif

                        {{-- EDIT MODALS (L300) --}}
                        @foreach ($destinationsL300 as $d)
                            <div class="modal fade" id="editDestinationModal-l300-{{ $d->id }}" tabindex="-1"
                                aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content">

                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Edit Destination</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>

                                        <div class="modal-body">
                                            <form method="POST"
                                                action="{{ route('owner.destinations.update', $d->id) }}">
                                                @csrf
                                                @method('PUT')

                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label">Store Code</label>
                                                        <input class="form-control" name="store_code"
                                                            value="{{ $d->store_code }}" required>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Store Name</label>
                                                        <input class="form-control" name="store_name"
                                                            value="{{ $d->store_name }}" required>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Area</label>
                                                        <input class="form-control" name="area"
                                                            value="{{ $d->area }}">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Truck Type</label>
                                                        <select name="truck_type" class="form-select" required>
                                                            <option value="6W"
                                                                {{ $d->truck_type === '6W' ? 'selected' : '' }}>6W</option>
                                                            <option value="L300"
                                                                {{ $d->truck_type === 'L300' ? 'selected' : '' }}>L300
                                                            </option>
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Rate</label>
                                                        <input type="number" step="0.01" class="form-control"
                                                            name="rate" value="{{ $d->rate }}" required>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label">Remarks</label>
                                                        <input class="form-control" name="remarks"
                                                            value="{{ $d->remarks }}">
                                                    </div>
                                                </div>

                                                <div class="d-flex justify-content-end mt-4">
                                                    <button class="btn btn-primary">Update</button>
                                                </div>
                                            </form>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>
        </div>
    </div>


    {{-- DELETE DESTINATION MODAL --}}
    <div class="modal fade" id="deleteDestinationModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-danger">
                        Delete Destination
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p class="mb-1">Are you sure you want to delete:</p>
                    <strong id="deleteDestinationName"></strong>
                </div>

                <div class="modal-footer">

                    <button class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <form method="POST" id="deleteDestinationForm">
                        @csrf
                        @method('DELETE')

                        <button class="btn btn-danger">
                            <i class="bi bi-trash"></i> Delete
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
{{-- ✅ Keep tab open after refresh / pagination --}}
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const tab = @json(request('tab', '6w'));
            const btn = document.querySelector(`[data-bs-target="#tab-${tab}"]`);

            if (btn) {
                const bsTab = new bootstrap.Tab(btn);
                bsTab.show();
            }

            // REMARKS
            document.querySelectorAll('.toggle-remarks').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const overlay = document.getElementById('remarks-' + id);

                    document.querySelectorAll('.remarks-overlay').forEach(el => {
                        if (el !== overlay) el.classList.add('d-none');
                    });

                    overlay.classList.toggle('d-none');
                });
            });

            // DELETE DESTINATION
            document.querySelectorAll('[data-bs-target="#deleteDestinationModal"]').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const name = this.getAttribute('data-name');

                    document.getElementById('deleteDestinationName').textContent = name;

                    document.getElementById('deleteDestinationForm').action =
                        `/owner/destinations/${id}`;
                });
            });

        });
    </script>
@endpush

@push('styles')
    <style>
        /* ====== UI HERO ====== */
        .ui-hero {
            border-radius: 20px;
            border: 1px solid rgba(0, 0, 0, .05);
            background:
                radial-gradient(900px 500px at 10% 10%, rgba(99, 102, 241, .10), transparent 60%),
                radial-gradient(900px 500px at 90% 20%, rgba(16, 185, 129, .10), transparent 60%),
                linear-gradient(135deg, #ffffff, #f9fafb);
            box-shadow: 0 20px 40px rgba(17, 24, 39, .06);
        }

        /* ====== Available cards (reused) ====== */
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
            background: #ffffff;
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

        /* ====== Header layout ====== */
        .ui-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding-top: 6px;
            padding-bottom: 6px;
        }

        .ui-card-title {
            white-space: nowrap;
        }

        /* actions wrapper */
        .ui-card-actions {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            margin: 0;
            flex: 0 0 auto;
            min-width: 0;
        }

        /* ====== Search box (DESKTOP/TABLET) ====== */
        .ui-searchbox {
            position: relative;
            width: 320px;
            /* ✅ shorter by default */
            max-width: 320px;
            min-width: 240px;
            flex: 0 0 auto;
        }

        .ui-searchbox i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #98A2B3;
            font-size: 14px;
            pointer-events: none;
        }

        .ui-searchbox input {
            height: 40px;
            padding-left: 36px;
            border-radius: 12px;
            /* ✅ nicer */
        }

        /* ====== Buttons ====== */
        .ui-btn-40 {
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            /* ✅ match search */
            padding: 0 14px;
            white-space: nowrap;
        }

        /* optional helper: full width button (mobile) */
        .ui-btn-40--block {
            width: 100%;
        }

        /* ====== Tabs scroll on small devices ====== */
        .ui-tabs {
            flex-wrap: nowrap;
            overflow-x: auto;
            overflow-y: hidden;
            -webkit-overflow-scrolling: touch;
        }

        .ui-tabs .nav-link {
            white-space: nowrap;
        }

        /* ====== Mobile cards for destinations ====== */
        .ui-mobile-destination {
            border-radius: 16px;
            transition: .2s ease;
        }

        .ui-mobile-destination:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(16, 24, 40, .08);
        }

        /* Action buttons on mobile cards */
        .ui-action-btn {
            border-radius: 999px;
            font-weight: 600;
        }

        /* ====== MOBILE / TABLET ====== */
        @media (max-width: 991.98px) {
            .ui-card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }

            /* ✅ make search + button stack cleanly */
            .ui-card-actions {
                width: 100%;
                flex-direction: column;
                align-items: stretch;
                justify-content: flex-start;
                flex-wrap: nowrap;
                gap: 10px;
            }

            .ui-searchbox {
                width: 100%;
                max-width: 100%;
                min-width: 0;
            }

            /* buttons become full width on mobile */
            .ui-btn-40 {
                width: 100%;
            }

            /* ===== Mobile Destination Layout ===== */
            .ui-dest-header {
                margin-bottom: 6px;
                display: flex;
                flex-direction: column;
                align-items: center;
                text-align: center;
                gap: 4px;

            }

            .ui-dest-name {
                font-weight: 700;
                font-size: 1rem;
                line-height: 1.25;
                word-break: break-word;
                overflow-wrap: anywhere;
            }

            .ui-dest-rate {
                font-weight: 800;
                /* ✅ stronger */
                font-size: .95rem;
                margin-top: 4px;
            }

            .ui-dest-meta {
                font-size: .85rem;
            }

            .ui-dest-row {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                gap: 12px;
                padding: 6px 0;
                border-top: 1px solid #f1f3f6;
            }

            .ui-dest-row:first-child {
                border-top: none;
            }

            .ui-dest-label {
                color: #98a2b3;
                flex: 0 0 40%;
            }

            .ui-dest-value {
                text-align: right;
                font-weight: 600;
                flex: 1;
                word-break: break-word;
                overflow-wrap: anywhere;
            }
        }

        /* ====== VERY SMALL PHONES (extra polish) ====== */
        @media (max-width: 420px) {
            .ui-searchbox input {
                font-size: 14px;
            }

            .ui-btn-40 {
                padding: 0 12px;
            }
        }

        /* ====== Responsive Pagination ====== */
        .ui-pagination-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 0.25rem;
        }

        .ui-pagination-responsive .pagination {
            flex-wrap: wrap;
            justify-content: center;
            gap: 0.25rem;
        }

        .ui-pagination-responsive .page-item .page-link {
            padding: 0.3rem 0.6rem;
            min-width: 2.2rem;
            text-align: center;
        }

        @media (max-width: 767.98px) {
            .ui-pagination-responsive .page-link {
                font-size: 0.8rem;
                padding: 0.25rem 0.45rem;
            }
        }

        .ui-rate-badge {

            color: #259c39;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 8px;
            font-size: 13px;
        }

        /* ===== REMARKS DRAWER ===== */
        .remarks-drawer {
            position: fixed;
            top: 0;
            right: -400px;
            width: 350px;
            height: 100%;
            background: #fff;
            box-shadow: -10px 0 30px rgba(0, 0, 0, 0.1);
            z-index: 1055;
            transition: right 0.3s ease;
            padding: 20px;
        }

        .remarks-drawer.open {
            right: 0;
        }

        .remarks-content {
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        /* ===== REMARKS OVERLAY ===== */
        .remarks-overlay {
            position: absolute;

            /* 🔥 start AFTER store code column */
            left: 120px;
            /* adjust depende sa width ng Store Code */
            right: 80px;
            /* leave space for buttons */

            top: 50%;
            transform: translateY(-50%);

            background: #22c1c3;
            color: #fff;
            padding: 10px 16px;
            border-radius: 8px;

            z-index: 10;
            text-align: center;
            font-weight: 600;

            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);

            opacity: 0;
            transition: 0.2s ease;
        }

        .remarks-overlay:not(.d-none) {
            opacity: 1;
        }
    </style>
@endpush
