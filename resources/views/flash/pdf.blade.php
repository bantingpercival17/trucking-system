<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #222;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #4f46e5;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }

        .title {
            font-size: 22px;
            font-weight: bold;
            color: #585670;
        }

        .company {
            font-size: 12px;
            color: #555;
        }

        .info-box {
            width: 100%;
            border: 1px solid #ddd;
            margin-bottom: 20px;
        }

        .info-box td {
            padding: 8px 10px;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            background: #f4f4ff;
            color: #4f46e5;
            padding: 8px 10px;
            margin-bottom: 8px;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .main-table th {
            background: #eeeff1;
            padding: 8px;
        }

        .main-table td {
            border: 1px solid #ddd;
            padding: 7px;
            text-align: center;
        }

        .summary-table {
            width: 48%;
            margin-left: auto;
            border-collapse: collapse;
        }

        .summary-table td {
            border: 1px solid #ddd;
            padding: 10px;
        }

        .net-pay {
            background: #e8f5e9;
            font-weight: bold;
            color: #1b5e20;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="title">FLASH PAYSLIP</div>
        <div class="company">
            Payroll Period:
            {{ \Carbon\Carbon::parse($payment->week_start)->format('M d') }} –
            {{ \Carbon\Carbon::parse($payment->week_end)->format('M d, Y') }}
        </div>
    </div>

    <table class="info-box">
        <tr>
            <td><strong>Name:</strong> {{ $driver->name }}</td>
            <td><strong>Type:</strong> Driver</td>
        </tr>
        <tr>
            <td><strong>Total Trips:</strong> {{ count($rows) }}</td>
            <td><strong>Generated:</strong> {{ now()->format('M d, Y h:i A') }}</td>
        </tr>
    </table>

    <div class="section-title">Payroll Breakdown</div>

    <table class="main-table">
        <tr>
            <th>DATE</th>
            <th>DESTINATION</th>
            <th>AMOUNT</th>
            <th>TOTAL</th>
        </tr>

        @foreach ($rows as $r)
            <tr>
                <td>{{ $r['date'] }}</td>
                <td>{{ $r['location'] }}</td>
                <td>{{ number_format($r['amount'], 2) }}</td>
                <td><strong>{{ number_format($r['total_salary'], 2) }}</strong></td>
            </tr>
        @endforeach
    </table>

    <table class="summary-table">
        <tr>
            <td>Total Income</td>
            <td>₱ {{ number_format($payment->amount, 2) }}</td>
        </tr>

        <tr>
            <td>Last Balance</td>
            <td>₱ {{ number_format($payment->advance_amount ?? 0, 2) }}</td>
        </tr>

        <tr>
            <td>Advance Deduction</td>
            <td>₱ {{ number_format($payment->advance_deducted ?? 0, 2) }}</td>
        </tr>

        <tr>
            <td>Remaining Balance</td>
            <td>₱ {{ number_format($payment->balance_advance ?? 0, 2) }}</td>
        </tr>

        <tr class="net-pay">
            <td>NET PAY</td>
            <td>₱ {{ number_format($payment->final_amount, 2) }}</td>
        </tr>
    </table>

</body>
</html>