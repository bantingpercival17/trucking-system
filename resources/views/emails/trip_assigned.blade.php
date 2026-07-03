<h2>🚚 New Trip Assigned</h2>

<p>Hello {{ $person->name }},</p>

<p>You have been assigned to a new trip for <strong>{{ $company }}</strong>.</p>

<hr>

<p>
<strong>Date:</strong>
{{ \Carbon\Carbon::parse($trip->dispatch_date)->format('F d, Y') }}
</p>

<p>
<strong>Destination:</strong>
{{ $trip->destination->store_name ?? '-' }}
</p>

<p>
<strong>Truck:</strong>
{{ $trip->truck->plate_number ?? '-' }}
</p>

<p>
<strong>Driver:</strong>
{{ $trip->driver->name ?? '-' }}
</p>

<p>
<strong>Helper(s):</strong>
@if(isset($trip->helpers) && $trip->helpers && $trip->helpers->count())
    {{ $trip->helpers->pluck('name')->join(', ') }}
@else
    None
@endif
</p>

@if($trip->remarks)
<p>
<strong>Remarks:</strong>
{{ $trip->remarks }}
</p>
@endif

<hr>

<p>Please prepare accordingly and coordinate with dispatch if needed.</p>

<p>Safe travels,<br>– {{ $company }}</p>