<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            font-weight: 400;
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
            letter-spacing: 1px;
        }

        .company {
            font-size: 12px;
            color: #555;
        }

        .info-box {
            width: 100%;
            border: 1px solid #ddd;
            margin-bottom: 20px;
            border-radius: 6px;
        }

        .info-box td {
            padding: 8px 10px;
            border-bottom: 1px solid #eee;
        }

        .info-box tr:last-child td {
            border-bottom: none;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            background: #f4f4ff;
            color: #4f46e5;
            padding: 8px 10px;
            border-left: 4px solid #4f46e5;
            margin-bottom: 8px;
        }

        .main-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .main-table th {
            background: #eeeff1;
            /* light gray */
            color: #111827;
            /* dark text */
            padding: 8px;
            font-size: 11px;
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
            table-layout: fixed;
        }

        .summary-table td {
            border: 1px solid #ddd;
            padding: 10px 12px;
        }

        .summary-table td:first-child {
            width: 65%;
        }

        .summary-table td:last-child {
            width: 35%;
            text-align: right;
            white-space: nowrap;
        }

        .summary-label {
            font-weight: bold;
            background: #fafafa;
        }

        .net-pay {
            background: #e8f5e9;
            font-size: 14px;
            font-weight: 700;
            color: #1b5e20;
        }

        .signature {
            margin-top: 50px;
            width: 100%;
        }

        .signature td {
            width: 50%;
            text-align: center;
            padding-top: 35px;
            font-size: 11px;
        }

        .line {
            border-top: 1px solid #000;
            width: 80%;
            margin: 0 auto 6px auto;
        }

        .section-title,
        .main-table th,
        .summary-label {
            font-weight: 600;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="title">CHAMONIX PAYSLIP</div>
        <div class="company">
            Payroll Period:
            {{ \Carbon\Carbon::parse($weekStart)->format('M d') }} –
            {{ \Carbon\Carbon::parse($weekEnd)->format('M d, Y') }}
        </div>
    </div>

    <table class="info-box">
        <tr>
            <td><strong>Name:</strong> {{ $p['name'] }}</td>
            <td>
                <strong>Type:</strong>
                {{ $p['person_type'] === 'helper' ? 'Helper' : 'Driver' }}
            </td>
        </tr>
        <tr>
            <td><strong>Total Trips:</strong> {{ count($p['rows']) }}</td>
            <td><strong>Generated:</strong> {{ now()->format('M d, Y h:i A') }}</td>
        </tr>
    </table>

    <div class="section-title">Payroll Breakdown</div>

    <table class="main-table">
        <tr>
            <th>DATE</th>
            <th>DESTINATION</th>
            <th>AMOUNT</th>
            <th>ALLOWANCE</th>
            <th>TOTAL</th>
        </tr>

        @foreach ($p['rows'] as $r)
            <tr>
                <td>{{ $r['date'] }}</td>
                <td>{{ $r['location'] }}</td>
                <td>{{ number_format($r['amount'] ?? 0, 2) }}</td>
                <td>{{ number_format($r['allowance'] ?? 0, 2) }}</td>
                <td><strong>{{ number_format($r['total_salary'] ?? 0, 2) }}</strong></td>
            </tr>
        @endforeach
    </table>

    <table class="summary-table">
        <tr>
            <td>Total Income</td>
            <td>₱ {{ number_format($p['payroll_total'], 2) }}</td>
        </tr>

        <tr>
            <td>Last Balance</td>
            <td>₱ {{ number_format($deductions['last_balance'] ?? 0, 2) }}</td>
        </tr>

        <tr>
            <td>Advance Deduction</td>
            <td>₱ {{ number_format($deductions['advance_deducted'] ?? 0, 2) }}</td>
        </tr>

        <tr>
            <td>Remaining Advance Balance</td>
            <td>₱ {{ number_format($deductions['remaining_balance'] ?? 0, 2) }}</td>
        </tr>

        <tr class="net-pay">
            <td>NET PAY</td>
            <td>₱ {{ number_format($netPay, 2) }}</td>
        </tr>
    </table>

    <table class="signature">
        <tr>
            <td>
                <div class="line"></div>
                Employer Signature
            </td>
            <td>
                <div class="line"></div>
                Employee Signature
            </td>
        </tr>
    </table>

</body>

</html>
