<h2>Payroll Ready</h2>

<p>Hello {{ $person->name }},</p>

<p>Your payroll for the following week is ready.</p>

<p>
<strong>Week Covered:</strong>
{{ \Carbon\Carbon::parse($weekStart)->format('F d, Y') }}
-
{{ \Carbon\Carbon::parse($weekEnd)->format('F d, Y') }}
</p>

<table width="100%" cellpadding="8" cellspacing="0" border="1" style="border-collapse:collapse;font-family:Arial;font-size:14px;">

<thead style="background:#f3f4f6;">
<tr>
<th>Date</th>
<th>Destination</th>
<th>Amount</th>
<th>Allowance</th>
<th>Total</th>
</tr>
</thead>

<tbody>
@foreach($rows as $row)
<tr>
<td>{{ $row['date'] }}</td>
<td>{{ $row['location'] }}</td>
<td>₱{{ number_format($row['amount'],2) }}</td>
<td>₱{{ number_format($row['allowance'],2) }}</td>
<td><strong>₱{{ number_format($row['total_salary'],2) }}</strong></td>
</tr>
@endforeach
</tbody>

<tfoot>
<tr style="background:#f9fafb;font-weight:bold;">
<td colspan="4" align="right">TOTAL INCOME</td>
<td>₱{{ number_format($amount,2) }}</td>
</tr>
</tfoot>

</table>

<br>

<table width="50%" cellpadding="8" cellspacing="0" border="1" style="border-collapse:collapse;font-family:Arial;font-size:14px;margin-left:auto;">

<tr>
<td><strong>Last Balance</strong></td>
<td align="right">₱{{ number_format($lastBalance ?? 0,2) }}</td>
</tr>

<tr>
<td><strong>Advance Deduction</strong></td>
<td align="right">₱{{ number_format($advanceDeducted ?? 0,2) }}</td>
</tr>

<tr>
<td><strong>Remaining Advance Balance</strong></td>
<td align="right">₱{{ number_format($remainingBalance ?? 0,2) }}</td>
</tr>

<tr style="background:#e8f5e9;font-weight:bold;">
<td><strong>NET PAY</strong></td>
<td align="right">₱{{ number_format($netPay ?? $amount,2) }}</td>
</tr>

</table>

<p style="margin-top:20px;">
Please coordinate with accounting for the release of your payment.
</p>

<p>– {{ $company }}</p>