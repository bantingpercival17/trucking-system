@foreach ($vehicles as $vehicle)
    <div class="modal fade" id="view-vehicle{{ $vehicle->id }}-modal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow">

                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold">VIEW DETAILS TRUCK / VEHICLE</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card border border-primary mb-3">
                                <div class="card-header fw-bold text-primary">
                                    <small class="text-muted">PLATE NUMBER</small>
                                    <label class="h4 fw-bolder text-primary">{{ $vehicle->plate_number }}</label>
                                    <br>
                                    <small class="fw-bolder">TYPE: <span
                                            class="text-primary">{{ $vehicle->truck_type }}</span></small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md">
                            <div class="current-trip">
                                <label class="fw-bold mb-2">CURRENT TRIP</label>
                                @if ($vehicle->status === 'on_trip')
                                    @if ($vehicle->activeDispatchTrip)
                                        <div class="trip-details">
                                            <div class="row">
                                                <div class="form-group col-md">
                                                    <small class="fw-bold mb-1">
                                                        COMPANY NAME
                                                    </small> <br>
                                                    <span
                                                        class="text-primary">{{ $vehicle->activeDispatchTrip->company->name }}</span>
                                                </div>
                                                <div class="form-group col-md">
                                                    <small class="fw-bold mb-1">
                                                        <i class="bi bi-calendar-event text-info me-2"></i>DISPATCH
                                                        DATE
                                                    </small> <br>
                                                    <span
                                                        class="text-primary">{{ $vehicle->activeDispatchTrip->dispatch_date->format('F d,Y') }}</span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <small class="fw-bold mb-1">
                                                    <i class="bi bi-geo-alt text-info me-2"></i>DESTINATION
                                                </small> <br>
                                                <span class="text-primary">
                                                    {{ $vehicle->activeDispatchTrip->destination->destinationName() }}
                                                </span>
                                            </div>
                                            <div class="form-group">
                                                <small class="fw-bold mb-1">
                                                    <i class="bi bi-truck text-info me-2"></i>STATUS
                                                </small> <br>
                                                <span class="text-primary">
                                                    {{ ucfirst($vehicle->activeDispatchTrip->status) }}
                                                </span>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <br>
                                    <span class="text-primary fw-bolder">
                                        Available for dispatch
                                    </span>
                                @endif
                            </div>

                        </div>
                    </div>
                    <div class="trip-history">
                        <div class="fw-bold mb-2"><i class="bi bi-calendar-event text-info me-2"></i>HISTORY OF
                            TRIPS </div>
                        @foreach ($vehicle->dispatchHistory as $item)
                            <div class="card p-2 m-2 border border-info">
                                <div class="card-body">
                                    {{--  //Show trip details here, such as dispatch date, destination, company name, driver, helpers and status. You can format it as needed, for example:
                                    --}}
                                    <div class="row">
                                        <div class="form-group col-md-12">
                                            <small class="fw-bold mb-1">
                                                <i class="bi bi-geo-alt text-info me-2"></i>DESTINATION
                                            </small> <br>
                                            <span
                                                class="text-primary">{{ $item->destination->destinationName() }}</span>
                                        </div>
                                        <div class="form-group col-md">
                                            <small class="fw-bold mb-1">
                                                <i class="bi bi-calendar-event text-info me-2"></i>COMPANY NAME
                                            </small> <br>
                                            <span class="text-primary">{{ $item->destination->company->name }}</span>
                                        </div>
                                        <div class="form-group col-md">
                                            <small class="fw-bold mb-1">
                                                <i class="bi bi-calendar-event text-info me-2"></i>DISPATCH DATE
                                            </small> <br>
                                            <span
                                                class="text-primary">{{ $item->dispatch_date->format('F d,Y') }}</span>
                                        </div>

                                        <div class="form-group col-md">
                                            <small class="fw-bold mb-1">
                                                <i class="bi bi-truck text-info me-2"></i>DRIVER
                                            </small> <br>
                                            <span class="text-primary">
                                                {{ $item->driver->name ?? 'Not Assigned' }}
                                            </span>
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
@endforeach
