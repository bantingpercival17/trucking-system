@extends('layouts.owner')

@section('title', 'Maintenance')

@section('content')
    <div class="container-fluid py-4">

        {{-- HEADER --}}
        <div class="ui-hero p-4 md:p-6 mb-4">
            <h4 class="mb-1">Truck Maintenance</h4>
            <p class="text-muted">Monitor documents, insurance, and PMS status</p>
        </div>

        {{-- KPI CARDS --}}
        <div class="row">

            <div class="col-md-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="text-muted small">Total Trucks</div>
                        <h4 class="fw-bold">{{ $totalTrucks ?? 0 }}</h4>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="text-warning small">Expiring Soon</div>
                        <h4 class="fw-bold text-warning">{{ $expiringCount ?? 0 }}</h4>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="text-danger small">Expired</div>
                        <h4 class="fw-bold text-danger">{{ $expiredCount ?? 0 }}</h4>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <div class="text-info small">PMS Due</div>
                        <h4 class="fw-bold text-info">{{ $pmsDueCount ?? 0 }}</h4>
                    </div>
                </div>
            </div>

        </div>

        {{-- ALERTS --}}
        <div class="row mb-4">

            {{-- EXPIRING --}}
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-warning text-white fw-bold">
                        ⚠ Expiring Soon
                    </div>
                    <div class="card-body">
                        @forelse($expiringDocs ?? [] as $doc)
                            <div class="mb-2">
                                🚛 <strong>{{ $doc->truck->plate_number ?? '-' }}</strong>
                                - {{ $doc->type }}
                                <span class="text-muted">({{ $doc->days_left }} days)</span>
                            </div>
                        @empty
                            <div class="text-muted">No expiring documents</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- EXPIRED --}}
            <div class="col-md-6">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-danger text-white fw-bold">
                        ❌ Expired
                    </div>
                    <div class="card-body">
                        @forelse($expiredDocs ?? [] as $doc)
                            <div class="mb-2">
                                🚛 <strong>{{ $doc->truck->plate_number ?? '-' }}</strong>
                                - {{ $doc->type }}
                            </div>
                        @empty
                            <div class="text-muted">No expired documents</div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

        {{-- TRUCK TABLE --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header fw-bold">
                🚛 Truck Documents
            </div>

            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead class="text-center">
                        <tr>
                            <th>Plate No</th>
                            <th>ORCR</th>
                            <th>Insurance</th>
                            <th>LTFRB</th>
                            <th>PMS</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($trucks ?? [] as $truck)
                            <tr>
                                <td class="fw-bold">{{ $truck->plate_number }}</td>

                                {{-- ORCR --}}
                                <td class="text-center">
                                    @include('partials.doc-status', ['doc' => $truck->orcr])
                                </td>

                                {{-- INSURANCE --}}
                                <td class="text-center">
                                    @include('partials.doc-status', ['doc' => $truck->insurance])
                                </td>

                                {{-- LTFRB --}}
                                <td class="text-center">
                                    @include('partials.doc-status', ['doc' => $truck->ltfrb])
                                </td>

                                {{-- PMS --}}
                                <td class="text-center">
                                    @include('partials.doc-status', ['doc' => $truck->pms])
                                </td>

                                <td class="text-center">
                                    <button class="btn btn-sm btn-primary edit-btn" data-bs-toggle="modal"
                                        data-bs-target="#editModal" data-truck-id="{{ $truck->id }}"
                                        data-plate="{{ $truck->plate_number }}"
                                        data-orcr="{{ optional($truck->orcr)->expiry_date }}"
                                        data-insurance="{{ optional($truck->insurance)->expiry_date }}"
                                        data-ltfrb="{{ optional($truck->ltfrb)->expiry_date }}"
                                        data-pms="{{ optional($truck->pms)->expiry_date }}"
                                        data-orcr-file="{{ optional($truck->orcr)->file_path }}"
                                        data-insurance-file="{{ optional($truck->insurance)->file_path }}"
                                        data-ltfrb-file="{{ optional($truck->ltfrb)->file_path }}"
                                        data-pms-file="{{ optional($truck->pms)->file_path }}">
                                        ✏
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    No trucks found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- COMPANY DOCUMENTS --}}
        <div class="card border-0 shadow-sm">
            <div class="card-header fw-bold">
                🏢 Company Documents
            </div>

            <div class="card-body">

                <div class="row">

                    {{-- DTI --}}
                    <div class="col-md-6">
                        <div class="d-flex align-items-center justify-content-between border-end pe-3">

                            <div style="width: 60px;" class="fw-bold">
                                DTI
                            </div>

                            <div style="width: 120px;">
                                @if (isset($companyDocs['DTI']))
                                    <span
                                        class="badge 
                                {{ $companyDocs['DTI']->status === 'ACTIVE'
                                    ? 'bg-success'
                                    : ($companyDocs['DTI']->status === 'EXPIRING'
                                        ? 'bg-warning text-dark'
                                        : 'bg-danger') }}">
                                        {{ ucfirst(strtolower($companyDocs['DTI']->status)) }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">No Data</span>
                                @endif
                            </div>

                            <div style="width: 130px;" class="text-muted small">
                                {{ isset($companyDocs['DTI']) && $companyDocs['DTI']->expiry_date
                                    ? \Carbon\Carbon::parse($companyDocs['DTI']->expiry_date)->format('M d, Y')
                                    : '-' }}
                            </div>

                            <div style="width: 40px; text-align:center;">
                                @if (isset($companyDocs['DTI']) && $companyDocs['DTI']->file_path)
                                    <a href="{{ url('storage/' . $companyDocs['DTI']->file_path) }}" target="_blank">
                                        👁
                                    </a>
                                @endif
                            </div>

                            <div style="width: 40px;">
                                <button class="btn btn-sm btn-primary company-edit-btn" data-bs-toggle="modal"
                                    data-bs-target="#companyModal" data-type="DTI"
                                    data-expiry="{{ optional($companyDocs['DTI'] ?? null)->expiry_date }}"
                                    data-file="{{ optional($companyDocs['DTI'] ?? null)->file_path }}">
                                    ✏
                                </button>
                            </div>

                        </div>
                    </div>

                    {{-- BIR --}}
                    <div class="col-md-6">
                        <div class="d-flex align-items-center justify-content-between ps-3">

                            <div style="width: 60px;" class="fw-bold">
                                BIR
                            </div>

                            <div style="width: 120px;">
                                @if (isset($companyDocs['BIR']))
                                    <span
                                        class="badge 
                                {{ $companyDocs['BIR']->status === 'ACTIVE'
                                    ? 'bg-success'
                                    : ($companyDocs['BIR']->status === 'EXPIRING'
                                        ? 'bg-warning text-dark'
                                        : 'bg-danger') }}">
                                        {{ ucfirst(strtolower($companyDocs['BIR']->status)) }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">No Data</span>
                                @endif
                            </div>

                            <div style="width: 130px;" class="text-muted small">
                                {{ isset($companyDocs['BIR']) && $companyDocs['BIR']->expiry_date
                                    ? \Carbon\Carbon::parse($companyDocs['BIR']->expiry_date)->format('M d, Y')
                                    : '-' }}
                            </div>

                            <div style="width: 40px; text-align:center;">
                                @if (isset($companyDocs['BIR']) && $companyDocs['BIR']->file_path)
                                    <a href="{{ url('storage/' . $companyDocs['BIR']->file_path) }}" target="_blank">
                                        👁
                                    </a>
                                @endif
                            </div>

                            <div style="width: 40px;">
                                <button class="btn btn-sm btn-primary company-edit-btn" data-bs-toggle="modal"
                                    data-bs-target="#companyModal" data-type="BIR"
                                    data-expiry="{{ optional($companyDocs['BIR'] ?? null)->expiry_date }}"
                                    data-file="{{ optional($companyDocs['BIR'] ?? null)->file_path }}">
                                    ✏
                                </button>
                            </div>

                        </div>
                    </div>

                </div>

            </div>
        </div>

    </div>

    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">

                <form method="POST" action="{{ route('owner.maintenance.save') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Truck Documents</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <input type="hidden" name="truck_id" id="modalTruckId">

                        <div class="mb-3">
                            <label class="fw-bold">Truck</label>
                            <input type="text" id="modalPlate" class="form-control" readonly>
                        </div>

                        <hr>

                        {{-- ORCR --}}
                        <h6 class="fw-bold">ORCR</h6>
                        <div id="orcrPreview" class="mt-2"></div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Expiry Date</label>
                                <input type="date" name="ORCR_expiry" id="orcrInput" class="form-control">

                                <div class="form-check mt-1">
                                    <input type="checkbox" name="delete_ORCR_expiry" value="1"
                                        class="form-check-input">
                                    <label class="form-check-label text-danger">Delete Expiry Date</label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label>Upload File</label>
                                <input type="file" name="ORCR_file" class="form-control">

                                <div class="form-check mt-1">
                                    <input type="checkbox" name="delete_ORCR_file" value="1"
                                        class="form-check-input">
                                    <label class="form-check-label text-danger">Delete File</label>
                                </div>
                            </div>
                        </div>

                        {{-- INSURANCE --}}
                        <h6 class="fw-bold">Insurance</h6>

                        <div id="insurancePreview" class="mt-2"></div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Expiry Date</label>
                                <input type="date" name="INSURANCE_expiry" id="insuranceInput" class="form-control">

                                <div class="form-check mt-1">
                                    <input type="checkbox" name="delete_INSURANCE_expiry" value="1"
                                        class="form-check-input">
                                    <label class="form-check-label text-danger">Delete Expiry Date</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label>Upload File</label>
                                <input type="file" name="INSURANCE_file" class="form-control">
                                <div class="form-check mt-1">
                                    <input type="checkbox" name="delete_INSURANCE_file" value="1"
                                        class="form-check-input">
                                    <label class="form-check-label text-danger">Delete File</label>
                                </div>
                            </div>
                        </div>

                        {{-- LTFRB --}}
                        <h6 class="fw-bold">LTFRB</h6>
                        <div id="ltfrbPreview" class="mt-2"></div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Expiry Date</label>
                                <input type="date" name="LTFRB_expiry" id="ltfrbInput" class="form-control">

                                <div class="form-check mt-1">
                                    <input type="checkbox" name="delete_LTFRB_expiry" value="1"
                                        class="form-check-input">
                                    <label class="form-check-label text-danger">Delete Expiry Date</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label>Upload File</label>
                                <input type="file" name="LTFRB_file" class="form-control">
                                <div class="form-check mt-1">
                                    <input type="checkbox" name="delete_LTFRB_file" value="1"
                                        class="form-check-input">
                                    <label class="form-check-label text-danger">Delete File</label>
                                </div>
                            </div>
                        </div>

                        {{-- PMS --}}
                        <h6 class="fw-bold">PMS</h6>
                        <div id="pmsPreview" class="mt-2"></div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label>Next PMS Date</label>
                                <input type="date" name="PMS_expiry" id="pmsInput" class="form-control">

                                <div class="form-check mt-1">
                                    <input type="checkbox" name="delete_PMS_expiry" value="1"
                                        class="form-check-input">
                                    <label class="form-check-label text-danger">Delete Expiry Date</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label>Upload File</label>
                                <input type="file" name="PMS_file" class="form-control">

                                <div class="form-check mt-1">
                                    <input type="checkbox" name="delete_PMS_file" value="1"
                                        class="form-check-input">
                                    <label class="form-check-label text-danger">Delete File</label>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>

                </form>

            </div>
        </div>
    </div>

    <div class="modal fade" id="companyModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <form method="POST" action="{{ route('owner.company-docs.save') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title">Edit Company Document</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <input type="hidden" name="type" id="companyType">

                        <h6 id="companyTitle"></h6>

                        <div id="companyPreview" class="mb-2"></div>

                        <div class="mb-3">
                            <label>Expiry Date</label>
                            <input type="date" name="expiry_date" id="companyExpiry" class="form-control">

                            <div class="form-check mt-1">
                                <input type="checkbox" name="delete_expiry" value="1" class="form-check-input">
                                <label class="form-check-label text-danger">Delete Expiry Date</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Upload File</label>
                            <input type="file" name="file" class="form-control">

                            <div class="form-check mt-1">
                                <input type="checkbox" name="delete_file" value="1" class="form-check-input">
                                <label class="form-check-label text-danger">Delete File</label>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary">Save</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .d-flex:hover {
            background: #f8f9fa;
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

        .modal-body {
            max-height: 70vh;
            overflow-y: auto;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('click', function(e) {

            // =========================
            // TRUCK EDIT (existing)
            // =========================
            const btn = e.target.closest('.edit-btn');

            if (btn) {

                document.getElementById('modalTruckId').value = btn.dataset.truckId;
                document.getElementById('modalPlate').value = btn.dataset.plate;

                document.getElementById('orcrInput').value = formatDate(btn.dataset.orcr);
                document.getElementById('insuranceInput').value = formatDate(btn.dataset.insurance);
                document.getElementById('ltfrbInput').value = formatDate(btn.dataset.ltfrb);
                document.getElementById('pmsInput').value = formatDate(btn.dataset.pms);

                setPreview('orcrPreview', btn.dataset.orcrFile);
                setPreview('insurancePreview', btn.dataset.insuranceFile);
                setPreview('ltfrbPreview', btn.dataset.ltfrbFile);
                setPreview('pmsPreview', btn.dataset.pmsFile);
            }

            // =========================
            // COMPANY EDIT (NEW)
            // =========================
            const companyBtn = e.target.closest('.company-edit-btn');

            if (companyBtn) {

                document.getElementById('companyType').value = companyBtn.dataset.type;
                document.getElementById('companyTitle').innerText = companyBtn.dataset.type;

                document.getElementById('companyExpiry').value = formatDate(companyBtn.dataset.expiry);

                setPreview('companyPreview', companyBtn.dataset.file);
            }

        });

        function hasFile(filePath) {
            return filePath &&
                filePath !== 'null' &&
                filePath !== 'undefined' &&
                filePath !== '' &&
                filePath !== '/' &&
                filePath !== 'storage/' &&
                filePath.trim() !== '';
        }

        function setPreview(elementId, filePath) {
            const el = document.getElementById(elementId);

            if (hasFile(filePath)) {
                el.innerHTML = `
            <a href="/storage/${filePath}" target="_blank" class="text-primary">
                👁 View Current File
            </a>
        `;
            } else {
                el.innerHTML = `<span class="text-muted">No file uploaded</span>`;
            }
        }

        function formatDate(date) {
            if (!date) return '';

            let d = new Date(date);

            let year = d.getFullYear();
            let month = String(d.getMonth() + 1).padStart(2, '0');
            let day = String(d.getDate()).padStart(2, '0');

            return `${year}-${month}-${day}`;
        }
    </script>
@endpush
