{{-- Partial for truck sidebar details --}}
<div class="p-4">
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 18px;">
        <div class="card-body d-flex align-items-center gap-3">
            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:2rem;">
                <i class="bi bi-truck"></i>
            </div>
            <div>
                <div class="fw-bold fs-5 mb-1">{{ $truck->plate_number }}</div>
                <div class="text-muted small">Type: {{ $truck->truck_type }}</div>
            </div>
        </div>
    </div>

    <div class="card border-0 mb-3" style="border-radius: 16px;">
        <div class="card-body">
            <div class="d-flex align-items-center mb-2">
                <i class="bi bi-person-circle text-secondary me-2" style="font-size:1.3rem;"></i>
                <span class="fw-bold">Driver:</span>
                <span class="ms-2">{{ $driver ? $driver->name : 'N/A' }}</span>
            </div>
            <div class="d-flex align-items-center mb-2">
                <i class="bi bi-fuel-pump text-success me-2" style="font-size:1.3rem;"></i>
                <span class="fw-bold">Fuel (Last Week):</span>
                <span class="ms-2">{{ $fuelTotal }} L</span>
                <span class="ms-3 text-muted">|</span>
                <span class="ms-2"><i class="bi bi-cash-coin text-warning me-1"></i> ₱{{ number_format($fuelAmount, 2) }}</span>
            </div>
        </div>
    </div>

    <div class="card border-0 mb-3" style="border-radius: 16px;">
        <div class="card-body">
            <div class="fw-bold mb-2"><i class="bi bi-calendar-event text-info me-2"></i>Trips (Last Week)</div>
            <ul class="list-group list-group-flush">
                @forelse($trips as $trip)
                    <li class="list-group-item px-0">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-arrow-right-circle text-primary"></i>
                            <span class="fw-semibold">
                                @if($trip->dispatch_date)
                                    {{ \Carbon\Carbon::parse($trip->dispatch_date)->format('M d, Y') }}
                                @else
                                    <span class="text-muted">No date</span>
                                @endif
                            </span>
                            <span class="ms-2">{{ $trip->origin ?? ($trip->destination->store_name ?? 'Unknown') }} → {{ $trip->destination->store_name ?? 'Unknown' }}</span>
                        </div>
                    </li>
                @empty
                    <li class="list-group-item text-muted px-0">No trips found.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
