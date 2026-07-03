@extends('layouts.owner')

@section('title', 'Credit History')

@section('content')
<div class="container mt-4">
    <div class="mb-3">
        <a href="{{ url()->previous() }}" class="btn btn-secondary btn-sm">&larr; Back</a>
    </div>
    <h4>Credit History</h4>
    <div class="table-responsive mt-3">
        <table class="table table-bordered table-striped">
            <thead class="table-dark">
                <tr>
                    <th>Date</th>
                    <th>Time</th>
                    <th class="text-end">Amount (PHP)</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @forelse($credits as $c)
                    @php
                        $cd = isset($c->created_at) ? \Carbon\Carbon::parse($c->created_at) : null;
                    @endphp
                    <tr>
                        <td>{{ $cd ? $cd->format('Y-m-d') : ($c->date ?? '—') }}</td>
                        <td>{{ $cd ? $cd->format('h:i A') : ($c->time ?? '—') }}</td>
                        <td class="text-end">{{ number_format($c->amount ?? 0, 2) }}</td>
                        <td>{{ $c->remarks ?? '' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted">No credits recorded yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">
            {{ $credits->links() }}
        </div>
    </div>
</div>
@endsection
