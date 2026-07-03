@if (!$doc || !$doc->expiry_date)
    <span class="badge bg-secondary">No Data</span>
@else
    {{-- STATUS --}}
    @if ($doc->status === 'ACTIVE')
        <div class="badge bg-success mb-1">Active</div>
        @php $eyeColor = 'text-success'; @endphp
    @elseif($doc->status === 'EXPIRING')
        <div class="badge bg-warning text-dark mb-1">
            Expiring ({{ $doc->days_left }}d)
        </div>
        @php $eyeColor = 'text-warning'; @endphp
    @else
        <div class="badge bg-danger mb-1">Expired</div>
        @php $eyeColor = 'text-danger'; @endphp
    @endif

    {{-- DATE --}}
    <div class="small text-muted">
        {{ \Carbon\Carbon::parse($doc->expiry_date)->format('M d, Y') }}
    </div>

    {{-- VIEW --}}
    <div>
        @php
            $file = $doc->file_path ?? null;
        @endphp

        @if (!empty($file) && $file !== 'null' && trim($file) !== '')
            <a href="{{ url('storage/' . $file) }}" target="_blank">
                👁
            </a>
        @else
            <span class="text-muted small fst-italic">No uploaded file</span>
        @endif
    </div>

@endif
