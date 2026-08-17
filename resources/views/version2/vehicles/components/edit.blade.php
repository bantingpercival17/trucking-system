@foreach ($vehicles as $vehicle)
    <div class="modal fade" id="edit-vehicle{{ $vehicle->id }}-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content border-0 shadow">

                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold">EDIT DETAILS TRUCK / VEHICLE</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form method="POST" action="{{ route('admin.vehicles.update', $vehicle->id) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <small class="fw-bold text-primary">PLATE NUMBER <sup class="text-danger">*</sup></small>
                            <input class="form-control border border-primary text-primary" name="plate_number"
                                value="{{ $vehicle->plate_number }}" placeholder="e.g. ABC-1234" required>
                        </div>
                        <div class="mb-3">
                            <small class="fw-bold text-primary">COMPANY CONTACT NUMBER <sup
                                    class="text-info">(OPTIONAL)</sup></small>
                            <input class="form-control border border-primary text-primary" name="company_number"
                                value="{{ $vehicle->company_number }}" placeholder="e.g. 09171234567">
                        </div>
                        <div class="mb-3">
                            <small class="fw-bold text-primary">TRUCK TYPE <sup class="text-danger">*</sup></small>
                            <select class="form-select border border-primary text-primary" name="truck_type" required>
                                <option value="" disabled selected>Select type</option>
                                @foreach ($truckTypes as $item)
                                    <option value="{{ $item }}"
                                        {{ $vehicle->truck_type == $item ? 'selected' : '' }}>{{ $item }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <small class="fw-bold text-primary">STATUS <sup class="text-danger">*</sup></small>
                            <select class="form-select border border-primary text-primary" name="status">
                                <option value="active" {{ $vehicle->status == 'active' ? 'selected' : '' }}>Active
                                </option>
                                <option value="inactive" {{ $vehicle->status == 'inactive' ? 'selected' : '' }}>Inactive
                                </option>
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
@endforeach
