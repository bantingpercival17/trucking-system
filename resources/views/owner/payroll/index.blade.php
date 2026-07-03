@extends('layouts.owner')

@section('title', 'Payroll')

@section('content')


    <div class="max-w-6xl mx-auto px-4 py-4">

        <div class="ui-hero p-3 p-lg-4 mb-3 mb-lg-4">
            <div
                class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">

                <div>
                    <h4 class="mb-1">Payroll</h4>
                    <div class="text-muted small">
                        Working payroll preview
                        <span class="badge bg-light text-primary border ms-2">
                            {{ $weekStart->format('M d, Y') }} – {{ $weekEnd->format('M d, Y') }}
                        </span>
                    </div>
                </div>

                <div class="d-flex gap-2">

                    <a class="btn btn-outline-secondary w-full w-sm-auto" href="{{ route('owner.payroll.dashboard') }}">
                        ← Dashboard
                    </a>

                    <a class="btn btn-outline-secondary w-full w-sm-auto" href="{{ route('owner.payroll.history') }}">
                        → History
                    </a>

                </div>

            </div>
        </div>



        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card ui-card border-0 shadow-sm">
            <div class="card-body">
                {{-- Tabs --}}
                <ul class="nav nav-pills d-flex flex-column flex-md-row gap-2 mb-3 align-items-start align-items-md-center">

                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabDrivers" type="button">

                            Drivers
                            <span class="badge bg-light text-dark ms-1">
                                {{ $driversPayroll->count() }}
                            </span>

                        </button>
                    </li>

                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabHelpers" type="button">

                            Helpers
                            <span class="badge bg-light text-dark ms-1">
                                {{ $helpersPayroll->count() }}
                            </span>

                        </button>
                    </li>

                    <form method="POST" action="{{ route('owner.payroll.finalize') }}"
                        class="d-flex w-full w-md-auto justify-content-start mt-2 mt-md-0">
                        @csrf

                        <input type="hidden" name="week_start" value="{{ $weekStart->toDateString() }}">
                        <input type="hidden" name="week_end" value="{{ $weekEnd->toDateString() }}">

                        <button class="btn btn-primary w-full w-md-auto">
                            Finalize Payroll Week
                        </button>
                    </form>

                </ul>

                <div class="tab-content">

                    {{-- DRIVERS --}}
                    <div class="tab-pane fade show active" id="tabDrivers">
                        @forelse($driversPayroll as $p)
                            @php
                                $status = $p['status'] ?? 'UNPAID';
                                $badge = match ($status) {
                                    'PAID' => 'success',
                                    'PARTIAL' => 'warning',
                                    'OVERPAID' => 'info',
                                    'NO TRIPS' => 'secondary',
                                    default => 'danger',
                                };
                            @endphp

                            <form method="POST" action="{{ route('owner.payroll.update') }}">
                                @csrf

                                <input type="hidden" name="from" value="{{ $from }}">
                                <input type="hidden" name="to" value="{{ $to }}">
                                <input type="hidden" name="active_tab" class="active-tab-input" value="tabDrivers">
                                <input type="hidden" name="person_type" value="driver">
                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body p-0">

                                        <div
                                            class="p-3 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-2">
                                            <div>
                                                <div class="fw-bold text-info">{{ $p['name'] }} <span
                                                        class="text-muted small ms-1">Driver</span></div>
                                            </div>

                                            <div class="d-flex justify-content-end align-items-center gap-2">

                                                <span class="badge bg-{{ $badge }}">{{ $status }}</span>

                                                @if ($status !== 'PAID')
                                                    <button type="button" class="btn btn-sm btn-warning edit-payroll-btn">
                                                        Edit
                                                    </button>

                                                    <button type="submit"
                                                        class="btn btn-sm btn-primary save-payroll-btn d-none">
                                                        Save
                                                    </button>

                                                    <button type="button" class="btn btn-sm btn-success pay-btn"
                                                        data-bs-toggle="modal" data-bs-target="#payModal"
                                                        data-person-id="{{ $p['person_id'] }}" data-person-type="driver"
                                                        data-name="{{ $p['name'] }}"
                                                        data-trips="{{ count($p['rows']) }}"
                                                        data-amount="{{ $p['payroll_total'] }}"
                                                        data-balance-advance="{{ $p['latest_balance_advance'] ?? 0 }}">
                                                        Pay
                                                    </button>
                                                @endif

                                            </div>
                                        </div>

                                        <div class="overflow-x-auto">
                                            <table class="table table-bordered payroll-table mb-0 w-full">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th class="whitespace-nowrap">DATE</th>
                                                        <th class="break-words">DESTINATION</th>
                                                        <th class="vertical">RATE</th>
                                                        <th class="vertical">AMOUNT</th>
                                                        <th class="vertical">ALLOWANCE</th>
                                                        <th class="vertical">TOTALS</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @foreach ($p['rows'] as $r)
                                                        <tr>
                                                            <td class="text-center" data-label="Date">{{ $r['date'] }}
                                                            </td>
                                                            <td class="break-words" data-label="Destination">
                                                                {{ $r['location'] }}</td>

                                                            <td>{{ number_format($r['rate'], 2) }}</td>

                                                            <td>
                                                                <input type="number" step="0.01"
                                                                    name="rows[{{ $r['id'] }}][amount]"
                                                                    value="{{ $r['amount'] }}"
                                                                    class="form-control form-control-sm payroll-edit-input"
                                                                    readonly>
                                                            </td>

                                                            <td>
                                                                <input type="number" step="0.01"
                                                                    name="rows[{{ $r['id'] }}][allowance]"
                                                                    value="{{ $r['allowance'] }}"
                                                                    class="form-control form-control-sm payroll-edit-input"
                                                                    readonly>
                                                            </td>

                                                            <td class="text-end fw-bold" data-label="Totals">
                                                                {{ number_format($r['total_salary'], 2) }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>

                                                <tfoot>
                                                    <tr>
                                                        <th colspan="5" class="text-end">TOTAL</th>
                                                        <th class="text-end">
                                                            ₱ {{ number_format($p['payroll_total'], 2) }}
                                                        </th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        @empty
                            <div class="alert alert-light border">No driver payroll rows found.</div>
                        @endforelse
                    </div>

                    {{-- HELPERS --}}
                    <div class="tab-pane fade" id="tabHelpers">
                        @forelse($helpersPayroll as $p)
                            @php
                                $status = $p['status'] ?? 'UNPAID';
                                $badge = match ($status) {
                                    'PAID' => 'success',
                                    'PARTIAL' => 'warning',
                                    'OVERPAID' => 'info',
                                    'NO TRIPS' => 'secondary',
                                    default => 'danger',
                                };
                            @endphp
                            <form method="POST" action="{{ route('owner.payroll.update') }}">
                                @csrf

                                <input type="hidden" name="from" value="{{ $from }}">
                                <input type="hidden" name="to" value="{{ $to }}">
                                <input type="hidden" name="active_tab" class="active-tab-input" value="tabHelpers">
                                <input type="hidden" name="person_type" value="helper">
                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body p-0">

                                        <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="fw-bold text-info">{{ $p['name'] }} <span
                                                        class="text-muted small ms-1">Helper</span></div>
                                            </div>

                                            <div class="d-flex justify-content-end align-items-center gap-2">

                                                <span class="badge bg-{{ $badge }}">{{ $status }}</span>

                                                @if ($status !== 'PAID')
                                                    <button type="button"
                                                        class="btn btn-sm btn-warning edit-payroll-btn">
                                                        Edit
                                                    </button>

                                                    <button type="submit"
                                                        class="btn btn-sm btn-primary save-payroll-btn d-none">
                                                        Save
                                                    </button>

                                                    <button type="button" class="btn btn-sm btn-success pay-btn"
                                                        data-bs-toggle="modal" data-bs-target="#payModal"
                                                        data-person-id="{{ $p['person_id'] }}" data-person-type="helper"
                                                        data-name="{{ $p['name'] }}"
                                                        data-trips="{{ count($p['rows']) }}"
                                                        data-amount="{{ $p['payroll_total'] }}"
                                                        data-balance-advance="{{ $p['latest_balance_advance'] ?? 0 }}">
                                                        Pay
                                                    </button>
                                                @endif

                                            </div>
                                        </div>

                                        <div class="overflow-x-auto">
                                            <table class="table table-bordered payroll-table mb-0 w-full">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th class="whitespace-nowrap">DATE</th>
                                                        <th class="break-words">DESTINATION</th>
                                                        <th class="vertical whitespace-nowrap">RATE</th>
                                                        <th class="vertical">AMOUNT</th>
                                                        <th class="vertical">ALLOWANCE</th>
                                                        <th class="vertical">TOTALS</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    @foreach ($p['rows'] as $r)
                                                        <tr>
                                                            <td class="text-center" data-label="Date">{{ $r['date'] }}
                                                            </td>

                                                            <td class="break-words" data-label="Destination">
                                                                {{ $r['location'] }}</td>

                                                            <td>{{ number_format($r['rate'], 2) }}</td>

                                                            <td>
                                                                <input type="number" step="0.01"
                                                                    name="rows[{{ $r['id'] }}][amount]"
                                                                    value="{{ $r['amount'] }}"
                                                                    class="form-control form-control-sm payroll-edit-input"
                                                                    readonly>
                                                            </td>
                                                            <td>
                                                                <input type="number" step="0.01"
                                                                    name="rows[{{ $r['id'] }}][allowance]"
                                                                    value="{{ $r['allowance'] }}"
                                                                    class="form-control form-control-sm payroll-edit-input"
                                                                    readonly>
                                                            </td>

                                                            <td class="text-end fw-bold" data-label="Totals">
                                                                {{ number_format($r['total_salary'], 2) }}
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>

                                                <tfoot>
                                                    <tr>
                                                        <th colspan="5" class="text-end">TOTAL</th>
                                                        <th class="text-end">
                                                            ₱ {{ number_format($p['payroll_total'], 2) }}
                                                        </th>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            </form>
                        @empty
                            <div class="alert alert-light border">No helper payroll rows found.</div>
                        @endforelse
                    </div>


                </div>

            </div>
        </div>
    </div>


    <!-- ✅ SINGLE GLOBAL MODAL ONLY -->
    <div class="modal fade" id="payModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">

                <form method="POST" action="{{ route('owner.payroll.pay') }}">
                    @csrf
                    <div class="modal-header">
                        <h6 class="modal-title">Payroll Payment</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- HIDDEN -->
                        <input type="hidden" name="person_type" id="modalPersonType">
                        <input type="hidden" name="person_id" id="modalPersonId">
                        <input type="hidden" name="total_trips" id="modalTrips">
                        <input type="hidden" name="amount" id="modalAmount">
                        

                        <input type="hidden" name="week_start" value="{{ $weekStart->toDateString() }}">
                        <input type="hidden" name="week_end" value="{{ $weekEnd->toDateString() }}">

                        <!-- DISPLAY -->
                        <div class="mb-2">
                            <label class="form-label">Name</label>
                            <input class="form-control" id="modalName" readonly>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Total Trips</label>
                            <input class="form-control" id="modalTripsDisplay" readonly>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Total Amount</label>
                            <input class="form-control" id="modalAmountDisplay" readonly>
                        </div>

                        <!-- INPUTS (ETO ANG FIXED PART MO) -->

                        <div class="mb-2">
                            <label class="form-label">Advance Balance</label>
                            <input type="number" step="0.01" name="balance_advance" class="form-control"
                                value="0">
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Advance Deduction</label>
                            <input type="number" step="0.01" name="advance_deducted" class="form-control"
                                value="0">
                        </div>

                        <!-- PAYMENT -->
                        <div class="mb-2">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_mode" class="form-select payment-method">
                                <option value="cash">Cash</option>
                                <option value="gcash">GCash</option>
                                <option value="bank">Bank</option>
                            </select>
                        </div>

                        <div class="mb-2 transaction-field d-none">
                            <label class="form-label">Transaction ID</label>
                            <input type="text" name="transaction_id" class="form-control">
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-success">
                            Confirm Payment
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

@endsection


@push('scripts')
    <script>
        document.addEventListener('click', function(e) {

            if (e.target.classList.contains('pay-btn')) {

                const btn = e.target;

                document.getElementById('modalPersonType').value = btn.dataset.personType;
                document.getElementById('modalPersonId').value = btn.dataset.personId;
                document.getElementById('modalTrips').value = btn.dataset.trips;
                document.getElementById('modalAmount').value = btn.dataset.amount;

                document.getElementById('modalName').value = btn.dataset.name;
                document.getElementById('modalTripsDisplay').value = btn.dataset.trips;
                document.getElementById('modalAmountDisplay').value = '₱ ' + Number(btn.dataset.amount)
                    .toLocaleString();

                document.querySelector('input[name="balance_advance"]').value =
                    btn.dataset.balanceAdvance || 0;

            }

        });

        document.addEventListener('change', function(e) {

            if (e.target.classList.contains('payment-method')) {

                const method = e.target.value;
                const modal = e.target.closest('.modal');


                const transactionField = modal.querySelector('.transaction-field');

                if (method === 'gcash' || method === 'bank') {
                    transactionField.classList.remove('d-none');
                } else {
                    transactionField.classList.add('d-none');
                }

            }

        });

        document.addEventListener('click', function(e) {

            if (e.target.classList.contains('edit-payroll-btn')) {

                const form = e.target.closest('form');

                form.querySelectorAll('.payroll-edit-input').forEach(input => {
                    input.removeAttribute('readonly');
                });

                form.querySelector('.save-payroll-btn').classList.remove('d-none');

                e.target.classList.add('d-none');
            }

        });

        document.addEventListener('DOMContentLoaded', function() {

            const activeTab = new URLSearchParams(window.location.search).get('active_tab');

            if (activeTab) {
                const trigger = document.querySelector(`[data-bs-target="#${activeTab}"]`);

                if (trigger) {
                    new bootstrap.Tab(trigger).show();
                }
            }

        });

        document.addEventListener('click', function(e) {

            if (e.target.classList.contains('save-payroll-btn')) {

                const activeTabBtn = document.querySelector('.nav-link.active');

                const activeTab = activeTabBtn?.getAttribute('data-bs-target')?.replace('#', '');

                const form = e.target.closest('form');

                if (activeTab) {
                    form.querySelector('.active-tab-input').value = activeTab;
                }
            }

        });
    </script>
@endpush

@push('styles')
    <style>
        /* ===== Shipments-like UI ===== */
        .ui-card {
            border-radius: 18px;
            box-shadow: 0 14px 40px rgba(16, 24, 40, .08);
            transition: all .25s ease;
        }

        .ui-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 50px rgba(16, 24, 40, .12);
        }

        .ui-hero {
            border-radius: 20px;
            border: 1px solid rgba(0, 0, 0, .05);
            background:
                radial-gradient(900px 500px at 10% 10%, rgba(99, 102, 241, .10), transparent 60%),
                radial-gradient(900px 500px at 90% 20%, rgba(16, 185, 129, .10), transparent 60%),
                linear-gradient(135deg, #ffffff, #f9fafb);
            box-shadow: 0 20px 40px rgba(17, 24, 39, .06);
        }

        .ui-divider {
            height: 1px;
            background: #edf0f4;
            width: 100%;
        }

        /* Search */
        .ui-search {
            position: relative;
        }

        .ui-search-input {
            height: 42px;
            border-radius: 999px;
            padding-left: 40px;
            border: 1px solid #e6e8ec;
            background: #fafbfc;
            transition: .2s ease;
        }

        .ui-search-input:focus {
            background: #fff;
            border-color: #cfd6ff;
            box-shadow: 0 0 0 .20rem rgba(13, 110, 253, .10);
        }

        .ui-search-icon {
            position: absolute;
            top: 50%;
            left: 14px;
            transform: translateY(-50%);
            color: #98a2b3;
            pointer-events: none;
        }

        /* pills */
        .ui-pill-btn {
            border-radius: 999px;
            padding: .45rem .90rem;
        }

        /* Make header buttons match input height */
        .ui-btn-wide,
        .ui-btn-equal {
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 1.1rem;
            font-weight: 600;
            white-space: nowrap;
        }

        .ui-btn-wide {
            min-width: 140px;
        }

        /* Table */
        .ui-table-wrap {
            border: 1px solid #edf0f4;
            border-radius: 16px;
            background: #fff;
        }

        .ui-table-scroll {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            border-radius: 16px;
        }

        .ui-table {
            /* min-width removed to prevent horizontal scroll bar */
        }

        .ui-table thead th {
            background: #f8fafc;
            color: #667085;
            font-weight: 600;
            font-size: .80rem;
            letter-spacing: .02em;
            border-bottom: 1px solid #edf0f4 !important;
            padding: 14px 16px;
            white-space: nowrap;
        }

        .ui-table tbody td {
            padding: 14px 16px;
            border-top: 1px solid #f1f3f6 !important;
            vertical-align: middle;
        }

        .ui-table tbody tr:hover {
            background: #fafcff;
        }

        /* top pagination */
        .ui-pager-top {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 6px;
        }

        .ui-pager-top .pagination {
            flex-wrap: nowrap;
            white-space: nowrap;
            margin-bottom: 0;
        }

        .ui-showing {
            white-space: nowrap;
        }

        /* Sort links */
        .table-sort {
            color: inherit;
            text-decoration: none;
            display: inline-flex;
            gap: .35rem;
            align-items: center;
            font-weight: 600;
        }

        .table-sort:hover {
            text-decoration: underline;
        }

        /* Status badges */
        .ui-badge {
            display: inline-flex;
            align-items: center;
            padding: .35rem .75rem;
            border-radius: 999px;
            font-size: .75rem;
            font-weight: 600;
            border: 1px solid transparent;
        }

        .ui-badge-draft {
            background: #f2f4f7;
            color: #344054;
            border-color: #eaecf0;
        }

        .ui-badge-dispatched {
            background: #e8f1ff;
            color: #175cd3;
            border-color: #cfe1ff;
        }

        .ui-badge-completed {
            background: #e7f8ef;
            color: #027a48;
            border-color: #bff0d4;
        }

        .ui-badge-cancelled {
            background: #ffeceb;
            color: #b42318;
            border-color: #ffd1cf;
        }

        .ui-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }

        .ui-dot-draft {
            background: #667085;
        }

        .ui-dot-dispatched {
            background: #175cd3;
        }

        .ui-dot-completed {
            background: #027a48;
        }

        .ui-dot-cancelled {
            background: #b42318;
        }

        .ui-dot-pulse {
            position: relative;
        }

        .ui-dot-pulse::after {
            content: "";
            position: absolute;
            inset: -4px;
            border-radius: 999px;
            border: 1px solid rgba(23, 92, 211, .35);
            animation: uiPulse 1.6s ease-out infinite;
        }

        @keyframes uiPulse {
            0% {
                transform: scale(.65);
                opacity: .9;
            }

            100% {
                transform: scale(1.25);
                opacity: 0;
            }
        }

        .ui-action-btn {
            border-radius: 999px;
            padding: .25rem .5rem;
            font-weight: 600;
        }

        .ui-icon-btn {
            border-radius: 12px;
            border: 1px solid #f1f3f6;
            background: #fff;
            padding: .35rem .6rem;
        }

        .ui-icon-btn:hover {
            background: #f8fafc;
        }

        /* Available cards */
        .ui-available-card {
            border-radius: 14px;
            box-shadow: 0 8px 25px rgba(16, 24, 40, .06);
            transition: .2s ease;
        }

        .ui-available-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(16, 24, 40, .10);
        }

        .ui-available-number {
            font-size: 30px;
            font-weight: 800;
            line-height: 1;
        }

        .ui-eye-btn {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1px solid #d0d5dd;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: .2s ease;
        }

        .ui-eye-btn i {
            font-size: 16px;
            color: #344054;
        }

        .ui-eye-btn:hover {
            background: #f2f4f7;
        }

        .ui-available-dropdown {
            margin-top: 6px;
        }

        .ui-list-controls .btn {
            border-radius: 999px;
            padding: .25rem .7rem;
        }

        .ui-mobile-trip {
            border-radius: 16px;
        }

        .ui-mobile-trip .card-body {
            padding: 14px 14px;
        }

        @media (max-width: 575.98px) {
            .ui-btn-wide {
                width: 100%;
            }

            .ui-btn-equal {
                width: 100%;
            }
        }

        @media (min-width:1200px) {
            .col-xl-5col {
                width: 20%;
                flex: 0 0 20%;
                max-width: 20%;
            }
        }

        #newTripModal .select2-container {
            width: 100% !important;
        }

        #newTripModal .select2-container--default .select2-selection--single {
            height: calc(2.375rem + 8px);
            padding: .375rem .75rem;
            border: 1px solid var(--bs-border-color, #ced4da);
            border-radius: .5rem;
            background-color: #fff;
            display: flex;
            align-items: center;
        }

        #newTripModal .select2-container--default .select2-selection--single .select2-selection__rendered {
            padding-left: 0 !important;
            line-height: 1.5;
            color: var(--bs-body-color, #212529);
        }

        #newTripModal .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
            right: .5rem;
        }

        #newTripModal .select2-container--default.select2-container--focus .select2-selection--single {
            border-color: #86b7fe;
            box-shadow: 0 0 0 .25rem rgba(13, 110, 253, .25);
        }

        #newTripModal .select2-dropdown {
            border: 1px solid var(--bs-border-color, #ced4da);
            border-radius: .5rem;
            overflow: hidden;
        }

        #newTripModal .select2-search__field {
            border-radius: .375rem;
            border: 1px solid var(--bs-border-color, #ced4da) !important;
            padding: .375rem .5rem;
            outline: none;
        }

        .person-stack {
            display: flex;
            align-items: center;
        }

        .person-avatar {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
            color: #374151;
            border: 2px solid #fff;
        }

        .person-avatar:not(:first-child) {
            margin-left: -10px;
        }

        .trip-ticket {
            font-weight: 700;
            font-size: 15px;
            color: #4f46e5;
            background: #eef2ff;
            padding: 4px 10px;
            border-radius: 8px;
            display: inline-block;
        }

        .trip-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        /* top icons */
        .trip-icons {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        /* equal icon buttons */
        .trip-icons .btn {
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* dispatch full width */
        .trip-dispatch button {
            width: 100%;
            height: 42px;
            border-radius: 10px;
            font-weight: 600;
        }

        .trip-actions .btn {
            border-radius: 10px;
        }

        .trip-actions .btn-primary {
            padding-left: 14px;
            padding-right: 14px;
        }

        /* mobile optimization */
        @media (max-width:420px) {

            .trip-actions {
                justify-content: space-between;
            }

            .trip-actions .btn-primary {
                flex: 1;
            }

        }

        @media (max-width: 320px) {
            .ui-available-card .card-body {
                padding: 10px 12px;
            }
        }

        .person-avatar {
            position: relative;
            cursor: pointer;
        }

        /* tooltip */
        .person-avatar::after {
            content: attr(data-name);
            position: absolute;
            bottom: 120%;
            left: 50%;
            transform: translateX(-50%);
            background: #111827;
            color: white;
            font-size: 12px;
            padding: 4px 8px;
            border-radius: 6px;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: 0.2s ease;
        }

        /* show on hover */
        .person-avatar:hover::after {
            opacity: 1;
        }

        .trip-status-row {
            display: flex;
            gap: 6px;
            margin-top: 6px;
        }

        .trip-status {
            font-size: 12px;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 8px;
            background: #f1f3f6;
            color: #344054;
        }

        /* delivery */
        .trip-status.delivery {
            background: #eef2ff;
            color: #4f46e5;
        }

        /* billing */
        .trip-status.billing {
            background: #fff7ed;
            color: #ea580c;
        }

        /* payment */
        .trip-status.payment {
            background: #ecfdf5;
            color: #16a34a;
        }

        .person-avatar.color-1 {
            background: #fee2e2;
            color: #991b1b;
        }

        .person-avatar.color-2 {
            background: #dbeafe;
            color: #1e3a8a;
        }

        .person-avatar.color-3 {
            background: #dcfce7;
            color: #166534;
        }

        .person-avatar.color-4 {
            background: #fef9c3;
            color: #854d0e;
        }

        .person-avatar.color-5 {
            background: #ede9fe;
            color: #5b21b6;
        }

        .person-avatar.color-6 {
            background: #fce7f3;
            color: #9d174d;
        }

        .person-avatar.color-7 {
            background: #cffafe;
            color: #155e75;
        }

        .person-avatar.color-8 {
            background: #f3f4f6;
            color: #374151;
        }

        .billing-unbilled {
            background: #fff7ed;
            color: #ea580c;
        }

        .billing-billed {
            background: #e0f2fe;
            color: #0369a1;
        }

        @media (max-width: 768px) {

            .ui-available-number {
                font-size: 24px;
            }

            .card-body {
                padding: 14px;
            }

            table th,
            table td {
                font-size: 13px;
                padding: 10px;
            }

        }

        @media (max-width: 576px) {

            .ui-available-card .card-body {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }

        }

        .ui-kpi-number {
            font-size: 20px;
            font-weight: 800;
            line-height: 1;
            /* eto ang nagtatanggal ng extra space */
            margin-top: 2px;
            margin-bottom: -2px;
            /* bawas lower space */
        }

        .ui-kpi-label {
            font-size: 14px;
            /* mas malaki */
            font-weight: 700;
            /* mas kapal */
            color: #4b5563;
            margin-bottom: 10px;
            /* maliit lang gap */
        }

        /* QUEUE HEADER */

        .queue-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }

        .queue-range {
            font-size: 13px;
            color: #6b7280;
        }

        .queue-filter {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .queue-filter input {
            max-width: 140px;
        }

        /* MOBILE QUEUE CARDS */

        .queue-card {
            background: white;
            border-radius: 16px;
            padding: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .05);
        }

        .queue-card-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .queue-name {
            font-weight: 600;
            font-size: 15px;
        }

        .queue-role {
            display: block;
            font-size: 12px;
            color: #6b7280;
            font-weight: 500;
        }

        .queue-stats {
            display: flex;
            justify-content: space-between;
            margin-bottom: 14px;
        }

        .queue-stat span {
            font-size: 12px;
            color: #6b7280;
            display: block;
        }

        .queue-stat strong {
            font-size: 15px;
        }

        .queue-btn {
            border-radius: 10px;
            font-weight: 600;
        }

        .queue-details {
            margin-bottom: 14px;
        }

        .queue-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }

        .queue-row span {
            font-size: 13px;
            color: #6b7280;
        }

        .queue-row strong {
            font-size: 16px;
            font-weight: 700;
        }

        /* MOBILE HEADER STACK */

        @media (max-width:576px) {

            .queue-header {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .queue-title {
                width: 100%;
            }


            .queue-filter {
                flex-direction: column;
                width: 100%;
            }

            .queue-filter input {
                width: 100%;
                max-width: none;
            }

            .queue-filter button {
                width: 100%;
            }


            .queue-range {
                font-size: 13px;
                color: #6b7280;
                margin-top: 2px;
                margin-bottom: 8px;
            }

        }

        .nav-pills .nav-link {
            border-radius: 999px;
            padding: 6px 14px;
            font-weight: 600;
        }

        .payroll-table {
            width: 100%;
            table-layout: fixed;
        }

        .payroll-table th,
        .payroll-table td {
            word-break: break-word;
            white-space: normal !important;
            padding: .35rem .4rem;
            font-size: .82rem;
        }

        .payroll-table th.whitespace-nowrap,
        .payroll-table td.whitespace-nowrap,
        .payroll-table .text-end.whitespace-nowrap {
            white-space: nowrap !important;
        }

        .payroll-table th:nth-child(2),
        .payroll-table td:nth-child(2) {
            min-width: 120px;
        }

        .payroll-table th:nth-child(6),
        .payroll-table td:nth-child(6) {
            min-width: 90px;
        }

        .pay-status-row {
            min-height: 38px;
        }

        .pay-status-row .badge {
            flex-shrink: 0;
        }

        .pay-status-row .pay-btn {
            flex-shrink: 0;
            width: 100%;
            max-width: 140px;
        }

        @media (max-width: 768px) {
            .pay-status-row {
                flex-direction: column;
                align-items: stretch;
                gap: .45rem;
            }

            .pay-status-row .pay-btn {
                max-width: 100%;
            }

            .payroll-table thead {
                display: none;
            }

            .payroll-table,
            .payroll-table tbody,
            .payroll-table tr,
            .payroll-table td {
                display: block;
                width: 100%;
            }

            .payroll-table tr {
                margin-bottom: 1rem;
            }

            .payroll-table td {
                text-align: right;
                padding-left: 50%;
                position: relative;
            }

            .payroll-table td::before {
                position: absolute;
                left: 0;
                width: 45%;
                padding-left: 0.5rem;
                white-space: nowrap;
                font-weight: 700;
                content: attr(data-label);
                text-align: left;
            }
        }
    </style>
@endpush
