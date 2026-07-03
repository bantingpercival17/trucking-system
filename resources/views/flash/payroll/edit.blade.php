<form method="POST" action="{{ route('flash.payroll.update') }}">
        @csrf

        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Destination</th>
                    <th>Rate</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rows as $row)
                    <tr>
                        <input type="hidden" name="rows[{{ $row->id }}][id]" value="{{ $row->id }}">

                        <td>{{ $row->trip_date }}</td>
                        <td>{{ $row->location }}</td>

                        <td>
                            <input type="number" step="0.01" name="rows[{{ $row->id }}][rate]"
                                value="{{ $row->rate }}">
                        </td>

                        <td>
                            <input type="number" step="0.01" name="rows[{{ $row->id }}][amount]"
                                value="{{ $row->amount }}">
                        </td>
                        
                    </tr>
                @endforeach
            </tbody>
        </table>

        <button class="btn btn-primary">Save Changes</button>
    </form>