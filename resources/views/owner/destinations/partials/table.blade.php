<table class="table table-striped align-middle">
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
        @forelse($destinations as $destination)
            <tr>
                <td>{{ $destination->store_code }}</td>
                <td>{{ $destination->store_name }}</td>
                <td>{{ $destination->area ?? '—' }}</td>
                <td>₱ {{ number_format($destination->rate, 2) }}</td>

                <td>
                    <div class="d-flex gap-1">

                        {{-- EDIT --}}
                        <button class="btn btn-sm btn-warning"
                            data-bs-toggle="modal"
                            data-bs-target="#editDestinationModal-{{ $destination->id }}">
                            ✏️
                        </button>

                        {{-- DELETE --}}
                        <form action="{{ route('owner.destinations.destroy', $destination->id) }}"
                              method="POST"
                              onsubmit="return confirm('Delete this destination?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">🗑️</button>
                        </form>
                        
                        <button class="btn btn-sm btn-info show-remarks-btn" data-remarks="{{ $d->remarks }}" title="View Remarks"> &gt; </button>

                    </div>
                </td>
            </tr>

            {{-- EDIT MODAL --}}
            <div class="modal fade" id="editDestinationModal-{{ $destination->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">Edit Destination</h5>
                            <button class="btn-close" data-bs-dismiss="modal"></button>
                        </div>

                        <div class="modal-body">
                                <form method="POST"
                                    action="{{ route('owner.destinations.update', $destination->id) }}">
                                @csrf
                                @method('PUT')

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label">Store Code</label>
                                        <input class="form-control" name="store_code"
                                               value="{{ $destination->store_code }}" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Store Name</label>
                                        <input class="form-control" name="store_name"
                                               value="{{ $destination->store_name }}" required>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Area</label>
                                        <input class="form-control" name="area"
                                               value="{{ $destination->area }}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label">Rate</label>
                                        <input type="number" step="0.01" class="form-control" name="rate"
                                               value="{{ $destination->rate }}" required>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label">Remarks</label>
                                        <textarea class="form-control" name="remarks"
                                                  rows="2">{{ $destination->remarks }}</textarea>
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

        @empty
            <tr>
                <td colspan="6" class="text-center text-muted">
                    No destinations found.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>
