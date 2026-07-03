{{-- resources/views/owner/trips/partials/trip-table.blade.php --}}

<!-- MOBILE CARD LAYOUT for Flash Trips (≤576px) -->
@if (!empty($hideHelper))
    <div class="d-block d-md-none">
        @forelse($dataset as $t)
            @php
                $badge = match ($t->status) {
                    'Draft' => 'draft',
                    'Dispatched' => 'dispatched',
                    'Completed' => 'completed',
                    'Cancelled' => 'cancelled',
                    default => 'draft',
                };
                $helperNames = $t->helpers?->pluck('name')->filter()->values() ?? collect();
            @endphp
            <div class="ui-mobile-trip-card mb-3 p-3 rounded-4 shadow-sm bg-white">
                <div class="ui-mobile-trip-header mb-2">
                    <div class="ui-mobile-trip-no fw-bold fs-5">{{ $t->trip_ticket_no }}</div>
                    <div class="ui-mobile-trip-status d-flex flex-wrap gap-2 align-items-center mt-1">
                        <span class="ui-badge ui-badge-flash bg-warning text-dark">Flash</span>
                        <span class="ui-badge ui-badge-{{ $badge }}">
                            <span class="ui-dot ui-dot-{{ $badge }} {{ $badge === 'dispatched' ? 'ui-dot-pulse' : '' }}"></span>
                            {{ $t->status === 'Dispatched' ? 'On Trip' : $t->status }}
                        </span>
                        @if($t->billing_status !== 'Billed')
                            <span class="badge bg-secondary text-dark">Unbilled</span>
                        @endif
                        @if($t->payment_status !== 'Paid')
                            <span class="badge bg-secondary text-dark">Unpaid</span>
                        @endif
                    </div>
                </div>
                <div class="ui-mobile-trip-body mb-2">
                    <div class="ui-mobile-trip-row d-flex justify-content-between"><span class="text-muted">Date:</span> <span class="fw-semibold">{{ \Carbon\Carbon::parse($t->dispatch_date)->format('Y-m-d') }}</span></div>
                    <div class="ui-mobile-trip-row d-flex justify-content-between"><span class="text-muted">Destination:</span> <span class="fw-semibold text-truncate" title="{{ $t->destination->store_name ?? '-' }}">{{ \Illuminate\Support\Str::limit($t->destination->store_name ?? '-', 20, '...') }}</span></div>
                    <div class="ui-mobile-trip-row d-flex justify-content-between"><span class="text-muted">Truck:</span> <span class="fw-semibold">{{ $t->truck->plate_number ?? '-' }}</span></div>
                    <div class="ui-mobile-trip-row d-flex justify-content-between"><span class="text-muted">Driver:</span> <span class="fw-semibold">{{ $t->driver->name ?? '-' }}</span></div>
                    <div class="ui-mobile-trip-row d-flex justify-content-between"><span class="text-muted">Helper:</span> <span class="fw-semibold">{{ $helperNames->count() ? $helperNames->join(', ') : '-' }}</span></div>
                    <div class="ui-mobile-trip-row d-flex justify-content-between"><span class="text-muted">Dispatched At:</span> <span class="fw-semibold">{{ $t->dispatched_at ? \Carbon\Carbon::parse($t->dispatched_at)->format('Y-m-d h:i A') : '-' }}</span></div>
                </div>
                <div class="ui-mobile-trip-actions mt-2 d-flex flex-column gap-2">
                    @if ($t->status === 'Draft')
                        <form method="POST" action="{{ route('owner.flash-trips.dispatch', $t->id) }}" class="w-100 mb-1">
                            @csrf
                            <button type="submit" class="btn btn-primary rounded-pill w-100 fw-bold py-2">Dispatch</button>
                        </form>
                        <form method="POST" action="{{ route('owner.flash-trips.cancel', $t->id) }}" class="w-100 mb-1">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger rounded-pill w-100 fw-bold py-2">Cancel</button>
                        </form>
                    @elseif ($t->status === 'Dispatched')
                        <form method="POST" action="{{ route('owner.flash-trips.deliver', $t->id) }}" class="w-100 mb-1">
                            @csrf
                            <button type="submit" class="btn btn-outline-success rounded-pill w-100 fw-bold py-2">Complete</button>
                        </form>
                        <form method="POST" action="{{ route('owner.flash-trips.cancel', $t->id) }}" class="w-100 mb-1">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger rounded-pill w-100 fw-bold py-2">Cancel</button>
                        </form>
                    @endif
                    @if (in_array($t->status, ['Completed', 'Cancelled'], true))
                        <form method="POST" action="{{ route('owner.flash-trips.destroy', $t->id) }}" onsubmit="return confirm('Delete this flash trip? This cannot be undone.');" class="w-100">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-dark rounded-pill w-100 fw-bold py-2">Delete</button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <div class="text-muted mb-2"><i class="bi bi-truck fs-3"></i></div>
                <div class="fw-semibold">No flash trips found</div>
                <div class="text-muted small">Create your first flash trip to get started.</div>
            </div>
        @endforelse
    </div>
@endif

<!-- TABLE LAYOUT for Tablet & Desktop (≥577px) -->
<div class="ui-table-wrap d-none d-md-block">
    <div class="ui-table-scroll">
        <table class="table ui-table align-middle mb-0 w-full" style="min-width:600px;">
            <thead>
                <tr>
                    <th><a class="table-sort" href="{{ $sortUrl('trip_ticket_no') }}">Trip Ticket No <i class="{{ $sortIcon('trip_ticket_no') }}"></i></a></th>
                    <th><a class="table-sort" href="{{ $sortUrl('dispatch_date') }}">Date <i class="{{ $sortIcon('dispatch_date') }}"></i></a></th>
                    <th>Destination</th>
                    <th>Truck</th>
                    <th>Driver</th>
                    @if(empty($hideHelper))
                        <th>Helper</th>
                    @endif
                    <th><a class="table-sort" href="{{ $sortUrl('status') }}">Status <i class="{{ $sortIcon('status') }}"></i></a></th>
                    <th><a class="table-sort" href="{{ $sortUrl('dispatched_at') }}">Dispatched At <i class="{{ $sortIcon('dispatched_at') }}"></i></a></th>
                    <th><a class="table-sort" href="{{ $sortUrl('billing_status') }}">Billing Status <i class="{{ $sortIcon('billing_status') }}"></i></a></th>
                    <th>Check Released Date</th>
                    <th><a class="table-sort" href="{{ $sortUrl('payment_status') }}">Payment Status <i class="{{ $sortIcon('payment_status') }}"></i></a></th>
                    <th class="text-center" style="width: 120px; min-width: 80px;">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dataset as $t)
                    @php
                        $badge = match ($t->status) {
                            'Draft' => 'draft',
                            'Dispatched' => 'dispatched',
                            'Completed' => 'completed',
                            'Cancelled' => 'cancelled',
                            default => 'draft',
                        };
                        $helperNames = $t->helpers?->pluck('name')->filter()->values() ?? collect();
                    @endphp
                    <tr>
                        <td class="fw-semibold text-truncate" style="max-width: 120px; cursor:pointer;" title="{{ $t->trip_ticket_no }}">{{ \Illuminate\Support\Str::limit($t->trip_ticket_no, 20, '...') }}</td>
                        <td>{{ \Carbon\Carbon::parse($t->dispatch_date)->format('Y-m-d') }}</td>
                        <td class="text-truncate" style="max-width: 120px; cursor:pointer;" title="{{ $t->destination->store_name ?? '-' }}">
                            {{ \Illuminate\Support\Str::limit($t->destination->store_name ?? '-', 20, '...') }}
                        </td>
                        <td class="text-truncate" style="max-width: 100px; cursor:pointer;" title="{{ $t->truck->plate_number ?? '-' }}">{{ \Illuminate\Support\Str::limit($t->truck->plate_number ?? '-', 15, '...') }}</td>
                        <td>{{ $t->driver->name ?? '-' }}</td>
                        @if(empty($hideHelper))
                            <td>{{ $helperNames->count() ? $helperNames->join(', ') : '-' }}</td>
                        @endif
                        <td>
                            <span class="ui-badge ui-badge-{{ $badge }} d-inline-flex align-items-center">
                                <span class="ui-dot ui-dot-{{ $badge }} {{ $badge === 'dispatched' ? 'ui-dot-pulse' : '' }}" style="vertical-align:middle;"></span>
                                <span style="vertical-align:middle;">{{ $t->status === 'Dispatched' ? 'On Trip' : $t->status }}</span>
                            </span>
                        </td>
                        <td class="text-truncate text-muted" style="max-width: 120px; cursor:pointer;" title="{{ $t->dispatched_at ? \Carbon\Carbon::parse($t->dispatched_at)->format('Y-m-d h:i A') : '-' }}">{{ \Illuminate\Support\Str::limit($t->dispatched_at ? \Carbon\Carbon::parse($t->dispatched_at)->format('Y-m-d h:i A') : '-', 20, '...') }}</td>
                        <td>
                            @if (!empty($hideHelper))
                                {{-- Flash Trips: billing status button only when Completed --}}
                                @if ($t->status === 'Completed')
                                    <button type="button" class="btn p-0 billing-toggle" data-url="{{ route('owner.flash-trips.toggleBilling', $t->id) }}">
                                        <span class="badge {{ $t->billing_status === 'Billed' ? 'bg-success' : 'bg-secondary' }} small" title="{{ $t->billing_status }}">
                                            {{ \Illuminate\Support\Str::limit($t->billing_status, 15, '...') }}
                                        </span>
                                    </button>
                                @else
                                    <span>
                                        <span class="badge {{ $t->billing_status === 'Billed' ? 'bg-success' : 'bg-secondary' }} small" title="{{ $t->billing_status }}">
                                            {{ \Illuminate\Support\Str::limit($t->billing_status, 15, '...') }}
                                        </span>
                                    </span>
                                @endif
                            @else
                                {{-- Main Trips: unchanged --}}
                                @if ($t->status === 'Completed')
                                    <button type="button" class="btn p-0 billing-toggle" data-url="{{ route('owner.trips.toggleBilling', $t->id) }}">
                                        <span class="badge {{ $t->billing_status === 'Billed' ? 'bg-success' : 'bg-secondary' }} small" title="{{ $t->billing_status }}">
                                            {{ \Illuminate\Support\Str::limit($t->billing_status, 15, '...') }}
                                        </span>
                                    </button>
                                @else
                                    <span>
                                        <span class="badge {{ $t->billing_status === 'Billed' ? 'bg-success' : 'bg-secondary' }} small" title="{{ $t->billing_status }}">
                                            {{ \Illuminate\Support\Str::limit($t->billing_status, 15, '...') }}
                                        </span>
                                    </span>
                                @endif
                            @endif
                        </td>
                        <td>
                            @if (!empty($hideHelper))
                                <span class="badge check-release-badge {{ !empty($t->check_released_date) ? 'bg-success' : 'bg-secondary' }} small" title="{{ !empty($t->check_released_date) ? \Carbon\Carbon::parse($t->check_released_date)->format('Y-m-d') : 'Not Released' }}">
                                    {{ !empty($t->check_released_date) ? '' . \Carbon\Carbon::parse($t->check_released_date)->format('Y-m-d') : 'Not Released' }}
                                </span>
                            @else
                                <span class="badge check-release-badge {{ !empty($t->check_released_at) ? 'bg-success' : 'bg-secondary' }} small" title="{{ !empty($t->check_released_at) ? \Carbon\Carbon::parse($t->check_released_at)->format('Y-m-d') : 'Not Released' }}">
                                    {{ !empty($t->check_released_at) ? '' . \Carbon\Carbon::parse($t->check_released_at)->format('Y-m-d') : 'Not Released' }}
                                </span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $t->payment_status === 'Paid' ? 'bg-success' : 'bg-secondary' }} small" title="{{ $t->payment_status }}">
                                {{ \Illuminate\Support\Str::limit($t->payment_status, 15, '...') }}
                            </span>
                        </td>
                        <td style="width: 120px; min-width: 80px; text-align: center; vertical-align: middle;">
                            <div class="d-flex justify-content-center gap-1 align-items-center" style="padding:0; min-width:0;">
                                @if (!empty($hideHelper))
                                    {{-- Flash Trips Actions --}}
                                    @if ($t->status === 'Draft')
                                        <form method="POST" action="{{ route('owner.flash-trips.dispatch', $t->id) }}" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary ui-action-btn">
                                                <i class="bi bi-play-circle"></i> Dispatch
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('owner.flash-trips.cancel', $t->id) }}" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger ui-action-btn">
                                                <i class="bi bi-x-circle"></i> Cancel
                                            </button>
                                        </form>
                                    @elseif ($t->status === 'Dispatched')
                                        <form method="POST" action="{{ route('owner.flash-trips.deliver', $t->id) }}" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-success ui-action-btn">
                                                <i class="bi bi-check-circle"></i> Complete
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('owner.flash-trips.cancel', $t->id) }}" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger ui-action-btn">
                                                <i class="bi bi-x-circle"></i> Cancel
                                            </button>
                                        </form>
                                    @endif
                                    @if (in_array($t->status, ['Completed', 'Cancelled'], true))
                                        <form method="POST" action="{{ route('owner.flash-trips.destroy', $t->id) }}" onsubmit="return confirm('Delete this flash trip? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm ui-icon-btn" title="Delete">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    {{-- Main Trips Actions --}}
                                    @if ($t->status === 'Draft')
                                        <button type="button"
                                            class="btn btn-sm btn-primary ui-action-btn"
                                            data-bs-toggle="modal"
                                            data-bs-target="#confirmDispatch-{{ $t->id }}">
                                            <i class="bi bi-play-circle"></i> Dispatch
                                        </button>
                                        <form method="POST"
                                            action="{{ route('owner.trips.cancel', $t->id) }}" style="display:inline;">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-sm btn-outline-danger ui-action-btn">
                                            <i class="bi bi-x-circle"></i> Cancel
                                        </button>
                                        </form>
                                    @elseif ($t->status === 'Dispatched')
                                        <form method="POST"
                                            action="{{ route('owner.trips.complete', $t->id) }}" style="display:inline;">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-sm btn-outline-success ui-action-btn">
                                            <i class="bi bi-check-circle"></i> Complete
                                        </button>
                                        </form>
                                        <form method="POST"
                                            action="{{ route('owner.trips.cancel', $t->id) }}" style="display:inline;">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-sm btn-outline-danger ui-action-btn">
                                            <i class="bi bi-x-circle"></i> Cancel
                                        </button>
                                        </form>
                                    @endif
                                    @if (in_array($t->status, ['Completed', 'Cancelled'], true))
                                        <form method="POST"
                                            action="{{ route('owner.trips.destroy', $t->id) }}"
                                            onsubmit="return confirm('Delete this trip? This cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm ui-icon-btn" title="Delete">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-5">
                            <div class="text-muted mb-2"><i class="bi bi-truck fs-3"></i></div>
                            <div class="fw-semibold">No trips found</div>
                            <div class="text-muted small">Create your first dispatch to get started.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
    @media (max-width: 390px) {
        .ui-table-wrap {
            border-radius: 12px;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            margin-bottom: 1rem;
        }
        .ui-table-scroll {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .ui-table {
            min-width: 600px;
            font-size: 0.95rem;
        }
        .ui-table thead th, .ui-table tbody td {
            padding: 8px 8px;
        }
    }
</style>

