@extends('layouts.owner')

@section('title', 'Expenses')

@section('content')
    @php
        use Carbon\Carbon;
    @endphp

    <div class="container-fluid px-3">


        <!-- HEADER -->
        <div class="ui-hero p-3 mb-3">
            <h5 class="fw-bold mb-1">Expenses Ledger</h5>
            <div class="text-muted small">Track fuel, load, salaries, benefits, and balance.</div>
        </div>

        @php
            // =========================
            // EXPENSES
            // =========================
            $fuelExpense = $expenses->where('type', 'fuel')->sum('debit');
            $loadExpense = $expenses->where('type', 'load')->sum('debit');

            // =========================
            // BENEFITS = ALL DEDUCTIONS
            // =========================
            $benefitsExpense = collect($deductions ?? [])->sum('amount');

            // =========================
            // SALARY = ALL PASAHOD
            // =========================
            $salaryExpense = \Illuminate\Support\Facades\DB::table('payroll_payments')->sum('final_amount');

            // =========================
            // AVERAGE FUEL
            // =========================
            $avgKmPerLiter =
                \Illuminate\Support\Facades\DB::table('expenses')
                    ->where('type', 'fuel')
                    ->whereNotNull('km_per_liter')
                    ->where('km_per_liter', '>', 0)
                    ->avg('km_per_liter') ?? 0;

            // =========================
            // TOTAL DEBIT
            // =========================
            $totalDebit = $fuelExpense + $loadExpense + $benefitsExpense;

            // =========================
            // BALANCE
            // =========================
            $budgetBalance = $totalCreditAll - $totalDebitAll;
        @endphp

        <div class="row g-3 summary-cards-row mb-4">

            <!-- Total Credit -->
            <div class="col-xl-3 col-md-6">
                <div class="card ui-card text-center h-80">
                    <div class="card-body py-3">
                        <div class="ui-kpi-label">Overall Credit 💰</div>
                        <div class="ui-kpi-number text-success">
                            PHP{{ number_format($totalCreditAll, 2) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Debit -->
            <div class="col-xl-3 col-md-6">
                <div class="card ui-card text-center h-80">
                    <div class="card-body py-3">
                        <div class="ui-kpi-label">Overall Debit 💸</div>
                        <div class="ui-kpi-number text-danger">
                            PHP{{ number_format($totalDebitAll, 2) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Load Expense -->
            <div class="col-xl-3 col-md-6">
                <div class="card ui-card text-center h-100">
                    <div class="card-body py-3">
                        <div class="ui-kpi-label">Total Load Expense 📱</div>
                        <div class="ui-kpi-number text-info">
                            PHP{{ number_format($loadExpense, 2) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Fuel Expense -->
            <div class="col-xl-3 col-md-6">
                <div class="card ui-card text-center h-100">
                    <div class="card-body py-3">
                        <div class="ui-kpi-label">Total Fuel Expense ⛽</div>
                        <div class="ui-kpi-number text-warning">
                            PHP{{ number_format($fuelExpense, 2) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Benefits Expense -->
            <div class="col-xl-3 col-md-6">
                <div class="card ui-card text-center h-100">
                    <div class="card-body py-3">
                        <div class="ui-kpi-label">Total Benefits Expense 🎁</div>
                        <div class="ui-kpi-number text-primary">
                            PHP{{ number_format($benefitsExpense, 2) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Salary Expense -->
            <div class="col-xl-3 col-md-6">
                <div class="card ui-card text-center h-100">
                    <div class="card-body py-3">
                        <div class="ui-kpi-label">Total Salary Expense 🧾</div>
                        <div class="ui-kpi-number text-secondary">
                            PHP{{ number_format($salaryExpense, 2) }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Average Fuel -->
            <div class="col-xl-3 col-md-6">
                <div class="card ui-card text-center h-100">
                    <div class="card-body py-3">
                        <div class="ui-kpi-label">Average Fuel 🚛</div>
                        <div class="ui-kpi-number text-dark">
                            {{ number_format($avgKmPerLiter, 2) }} km/L
                        </div>
                    </div>
                </div>
            </div>

            <!-- Budget Balance -->
            <div class="col-xl-3 col-md-6">
                <div class="card ui-card text-center h-100">
                    <div class="card-body py-3">
                        <div class="ui-kpi-label">Budget Balance 🏦</div>
                        <div class="ui-kpi-number {{ $budgetBalance >= 0 ? 'text-success' : 'text-danger' }}">
                            PHP{{ number_format($budgetBalance, 2) }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <div class="row g-3 mb-4">

            <!-- THIS MONTH CREDIT -->
            <div class="col-xl-6 col-md-6">
                <div class="card ui-card text-center h-100 border-success">
                    <div class="card-body py-3">
                        <div class="ui-kpi-label">This Month Credit 📅💰</div>
                        <div class="ui-kpi-number text-success">
                            PHP{{ number_format($totalCredit, 2) }}
                        </div>
                        <small class="text-muted">
                            Based on selected month filter
                        </small>
                    </div>
                </div>
            </div>

            <!-- THIS MONTH DEBIT -->
            <div class="col-xl-6 col-md-6">
                <div class="card ui-card text-center h-100 border-danger">
                    <div class="card-body py-3">
                        <div class="ui-kpi-label">This Month Debit 📅💸</div>
                        <div class="ui-kpi-number text-danger">
                            PHP{{ number_format($totalDebit, 2) }}
                        </div>
                        <small class="text-muted">
                            Fuel + Load + Benefits (filtered)
                        </small>
                    </div>
                </div>
            </div>

        </div>


        <div class="row g-3 mb-4">

            <!-- LEFT: CREDIT -->
            <div class="col-xl-7 col-lg-7">

                <!-- CREDIT TABLE -->
                <div class="card ui-card border-0 flex-grow-1">

                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                        <h6 class="fw-semibold mb-0">Credit History</h6>

                        <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#addCreditModal">
                            + Add Credit
                        </button>
                    </div>

                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped mb-0 rounded overflow-hidden">
                                <thead>
                                    <tr>
                                        <th class="text-center">DATE</th>
                                        <th class="text-end">AMOUNT</th>
                                        <th class="text-center">ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($credits ?? [] as $c)
                                        <tr>
                                            <td class="text-center">
                                                {{ Carbon::parse($c->date)->format('d') }}
                                            </td>
                                            <td class="text-end">
                                                {{ number_format($c->amount, 2) }}
                                            </td>
                                            <td class="text-center">

                                                <!-- EDIT BUTTON -->
                                                <button class="btn btn-sm btn-primary edit-credit-btn"
                                                    data-id="{{ $c->id }}" data-date="{{ $c->date }}"
                                                    data-amount="{{ $c->amount }}" data-bs-toggle="modal"
                                                    data-bs-target="#editCreditModal" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </button>

                                                <!-- DELETE BUTTON -->
                                                <button class="btn btn-sm btn-outline-danger delete-credit-btn"
                                                    data-id="{{ $c->id }}" data-date="{{ $c->date }}"
                                                    data-amount="{{ $c->amount }}" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>

                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center text-muted">
                                                No credits recorded.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            {{ $credits->links('vendor.pagination.ui-datatable') }}
                        </div>
                    </div>



                </div>
            </div>

            <!-- RIGHT: RECENT TRIPS -->
            <div class="col-xl-5 col-lg-5">
                {{-- RECENT TRIPS --}}
                <div class="card" data-iq-gsap="onStart" data-iq-position-y="70" data-iq-rotate="0"
                    data-iq-trigger="scroll" data-iq-ease="power.out" data-iq-opacity="0">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title fw-bolder mb-0">Recent Trips</h4>
                        <a href="{{ route('owner.trips.history') }}" class="btn btn-primary btn-sm"> View All </a>
                    </div>

                    <div class="card-body">
                        @forelse($recentTrips as $trip)
                            <div
                                class="d-flex justify-content-between align-items-center flex-wrap py-3 @if (!$loop->last) border-bottom @endif">
                                <div class="flex-grow-1">
                                    <h6 class="mb-0">{{ $trip->trip_ticket_no ?? 'N/A' }}</h6>
                                    <span
                                        class="text-muted small">{{ $trip->destination?->store_name ?? 'No destination' }}</span>
                                </div>

                                <div class="text-end">
                                    <span
                                        class="badge bg-@if (($trip->status ?? '') === 'Completed') success @elseif(($trip->status ?? '') === 'Dispatched') warning @else secondary @endif">
                                        {{ $trip->status ?? '—' }}
                                    </span>

                                    <p class="mb-0 small text-muted mt-1">
                                        {{ $trip->dispatch_date ? \Carbon\Carbon::parse($trip->dispatch_date)->format('M d, Y') : '—' }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted text-center py-4 mb-0">No trips yet</p>
                        @endforelse
                    </div>
                </div>

            </div>
        </div>


        <div class="mb-3 d-flex justify-content-between align-items-center flex-wrap gap-2 mt-5">

            {{-- LEFT SIDE BUTTONS --}}
            <div class="d-flex gap-2 flex-wrap">
                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                    + Add Expense
                </button>
            </div>

            {{-- RIGHT SIDE FILTER --}}
            <form method="GET" action="{{ route('owner.payroll.expenses') }}" class="expenses-filter">

                <select name="month" class="form-select form-select-sm" style="width:140px;">
                    @foreach (range(1, 12) as $m)
                        <option value="{{ $m }}" {{ request('month', now()->month) == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                    @endforeach
                </select>

                <select name="year" class="form-select form-select-sm" style="width:100px;">
                    @foreach (range(now()->year - 2, now()->year + 5) as $y)
                        <option value="{{ $y }}" {{ request('year', now()->year) == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endforeach
                </select>

                <button class="btn btn-sm btn-primary">
                    Apply
                </button>

            </form>

        </div>

        {{-- COMPANY LOAD --}}
        <div class="col-12">

            <div class="card ui-card border-0 h-100">

                <div class="card-header bg-white border-0 pb-0">
                    <h6 class="fw-semibold mb-0">Company Load</h6>
                    <div class="text-muted small">Load assigned to the company #</div>
                </div>

                <div class="card-body">

                    <div class="table-responsive">

                        <table class="table table-hover ui-grid-table" id="expensesTable">

                            <thead style="background-color:#f3f4f6; color:#374151;">
                                <tr>
                                    <th>Load Date</th>
                                    <th>Next Load Date</th>
                                    <th>Time</th>
                                    <th>Plate #</th>
                                    <th class="text-end">Amount</th>
                                    <th>Receipt (Y/N)</th>
                                    <th>Remarks</th>
                                    <th>Billed</th>
                                    <th>ACTIONS</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach ($expenses->where('type', 'load') as $i => $row)
                                    @php
                                        $d = \Carbon\Carbon::parse($row->date);
                                        $next = $d->copy()->addDays(7);
                                    @endphp

                                    <tr style="display:{{ $i < 10 ? 'table-row' : 'none' }};">

                                        <!-- Load Date -->
                                        <td class="text-center fw-semibold">
                                            {{ $d->format('M d') }}
                                        </td>

                                        <!-- Next Load Date -->
                                        <td class="text-center">
                                            {{ $next->format('M d') }}
                                        </td>

                                        <!-- Time -->
                                        <td class="text-center">
                                            {{ $row->time ? \Carbon\Carbon::parse($row->time)->format('h:i A') : '-' }}
                                        </td>

                                        <!-- Plate -->
                                        <td class="text-center">
                                            {{ $row->plate_number }}
                                        </td>

                                        <!-- Amount -->
                                        <td class="text-end">
                                            {{ number_format($row->debit, 2) }}
                                        </td>

                                        <!-- Receipt -->
                                        <td class="text-center">
                                            {{ $row->receipt_surrendered ?? '-' }}
                                        </td>

                                        <!-- Remarks -->
                                        <td>
                                            {{ $row->remarks ?? '-' }}
                                        </td>

                                        <!-- Billed -->
                                        <td class="text-center">
                                            <input type="checkbox" class="billed-checkbox" data-id="{{ $row->id }}"
                                                {{ $row->billed ? 'checked' : '' }}>
                                        </td>


                                        <td class="text-center d-flex justify-content-center gap-2">

                                            {{-- LOAD TABLE EDIT BUTTON --}}
                                            <button class="btn btn-sm btn-primary edit-load-btn"
                                                data-id="{{ $row->id }}" data-type="load"
                                                data-plate="{{ $row->plate_number }}" data-date="{{ $row->date }}"
                                                data-time="{{ $row->time }}" data-debit="{{ $row->debit }}"
                                                data-receipt="{{ $row->receipt_surrendered }}"
                                                data-remarks="{{ $row->remarks }}"
                                                data-image="{{ $row->receipt_image ? asset('storage/' . $row->receipt_image) : '' }}"
                                                data-bs-toggle="modal" data-bs-target="#editLoadModal">
                                                <i class="bi bi-pencil"></i>
                                            </button>

                                            <!-- DELETE -->
                                            <button class="btn btn-sm btn-outline-danger delete-expense-btn"
                                                data-id="{{ $row->id }}" data-plate="{{ $row->plate_number }}"
                                                data-date="{{ $row->date }}" data-debit="{{ $row->debit }}">
                                                <i class="bi bi-trash"></i>
                                            </button>

                                            <!-- VIEW IMAGE -->
                                            <button type="button"
                                                class="btn btn-sm btn-outline-secondary view-receipt-btn"
                                                data-image="{{ $row->receipt_image ? asset('storage/' . $row->receipt_image) : '' }}">
                                                <i class="bi bi-eye"></i>
                                            </button>

                                        </td>

                                    </tr>
                                @endforeach

                            </tbody>

                        </table>

                    </div>




                </div>

            </div>

        </div>

        {{-- fuel consumption --}}
        <div class="card mt-4">
            <div class="card-header fw-bold">
                Fuel Consumption Analysis 🚛
            </div>

            <div class="card-body">

                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Plate #</th>
                                <th>Start</th>
                                <th>End</th>
                                <th>Liters</th>
                                <th>Distance (km)</th>
                                <th>KM/L</th>
                                <th>ACTIONS</th>

                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $fuelExpenses = \Illuminate\Support\Facades\DB::table('expenses')
                                    ->where('type', 'fuel')
                                    ->orderByDesc('date')
                                    ->orderByDesc('id')
                                    ->get();
                            @endphp

                            @foreach ($fuelExpenses as $e)
                                <tr>
                                    <td>{{ $e->date }}</td>
                                    <td>{{ $e->plate_number }}</td>
                                    <td>{{ $e->start_odometer }}</td>
                                    <td>{{ $e->odometer }}</td>
                                    <td>{{ $e->liters }}</td>
                                    <td>{{ $e->distance }}</td>
                                    <td>{{ $e->km_per_liter }}</td>

                                    <td class="text-center d-flex justify-content-center gap-2">

                                        <button class="btn btn-sm btn-primary edit-fuel-btn"
                                            data-id="{{ $e->id }}" data-type="fuel"
                                            data-plate="{{ $e->plate_number }}" data-date="{{ $e->date }}"
                                            data-debit="{{ $e->debit }}" data-liters="{{ $e->liters }}"
                                            data-start="{{ $e->start_odometer }}" data-end="{{ $e->odometer }}"
                                            data-receipt="{{ $e->receipt_surrendered }}"
                                            data-remarks="{{ $e->remarks }}"
                                            data-image="{{ $e->receipt_image ? asset('storage/' . $e->receipt_image) : '' }}"
                                            data-bs-toggle="modal" data-bs-target="#editFuelModal">
                                            <i class="bi bi-pencil"></i>
                                        </button>

                                        <button class="btn btn-sm btn-outline-danger delete-expense-btn"
                                            data-id="{{ $e->id }}" data-plate="{{ $e->plate_number }}"
                                            data-date="{{ $e->date }}" data-debit="{{ $e->debit }}">
                                            <i class="bi bi-trash"></i>
                                        </button>

                                        <button type="button" class="btn btn-sm btn-outline-secondary view-receipt-btn"
                                            data-image="{{ $e->receipt_image ? asset('storage/' . $e->receipt_image) : '' }}">
                                            <i class="bi bi-eye"></i>
                                        </button>

                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>

    </div>



    <!-- Add Expense Modal -->
    <div class="modal fade" id="addExpenseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Expense</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form id="expenseForm" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">

                            <!-- TYPE -->
                            <div class="col-6">
                                <label class="form-label">Type</label>
                                <select name="type" id="expenseType" class="form-select" required>
                                    <option value="" selected disabled>Select Type</option>
                                    <option value="fuel">Fuel</option>
                                    <option value="load">Load</option>
                                </select>
                            </div>

                            <!-- PLATE -->
                            <div class="col-6">
                                <label class="form-label">Plate Number</label>
                                <select name="plate_number" id="plateSelect" class="form-select" required>
                                    <option value="">Select Plate Number</option>
                                    @foreach ($trucks ?? [] as $truck)
                                        <option value="{{ $truck->plate_number }}"
                                            data-company="{{ $truck->company_number }}">
                                            {{ $truck->plate_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- CONTACT -->
                            <div class="col-6" id="contactWrapper" style="display:none;">
                                <label class="form-label">Contact #</label>
                                <input type="text" id="contactField" class="form-control" readonly>
                            </div>

                            <!-- DATE -->
                            <div class="col-6">
                                <label class="form-label">Date</label>
                                <input type="date" name="date" class="form-control" required>
                            </div>

                            <div class="col-6" id="timeWrapper" style="display:none;">
                                <label class="form-label">Time</label>
                                <input type="time" name="time" class="form-control">
                            </div>

                            <!-- FUEL FIELDS -->
                            <div class="row g-3 fuel-fields">
                                <div class="col-6">
                                    <label class="form-label">Start Odometer</label>
                                    <input type="number" name="start_odometer" class="form-control">
                                </div>

                                <div class="col-6">
                                    <label class="form-label">End Odometer</label>
                                    <input type="number" name="odometer" class="form-control">
                                </div>

                                <div class="col-6">
                                    <label class="form-label">Liters</label>
                                    <input type="number" step="0.01" name="liters" class="form-control">
                                </div>
                            </div>

                            <!-- AMOUNT -->
                            <div class="col-6">
                                <label class="form-label">Amount (PHP)</label>
                                <input type="number" step="0.01" name="debit" class="form-control" required>
                            </div>

                            <!-- RECEIPT -->
                            <div class="col-6">
                                <label class="form-label">Receipt</label>
                                <select name="receipt_surrendered" class="form-select">
                                    <option value="">Select</option>
                                    <option value="YES">Yes</option>
                                    <option value="NO">No</option>
                                </select>
                            </div>

                            <!-- IMAGE -->
                            <div class="col-12">
                                <label class="form-label">Receipt Image</label>
                                <input type="file" name="receipt_image" class="form-control">
                            </div>

                            <!-- REMARKS -->
                            <div class="col-12">
                                <label class="form-label">Remarks</label>
                                <input type="text" name="remarks" class="form-control">
                            </div>

                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button id="saveExpense" type="button" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Credit Modal -->
    <div class="modal fade" id="addCreditModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Credit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="creditForm">
                        @csrf
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Date</label>
                                <input type="date" name="date" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Amount (PHP)</label>
                                <input type="number" step="0.01" name="amount" class="form-control" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Remarks</label>
                                <input type="text" name="remarks" class="form-control"
                                    placeholder="e.g. Payment received">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button id="saveCredit" type="button" form="creditForm" class="btn btn-primary">Save</button>
                </div>
            </div>
        </div>
    </div>


    <!-- EDIT CREDIT MODAL -->
    <div class="modal fade" id="editCreditModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Credit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form id="editCreditForm">
                        @csrf

                        <!-- ONLY ONE SET OF INPUTS -->
                        <input type="hidden" id="editCreditId">

                        <div class="mb-3">
                            <label class="form-label">Date</label>
                            <input type="date" id="editCreditDate" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Amount (PHP)</label>
                            <input type="number" step="0.01" id="editCreditAmount" class="form-control" required>
                        </div>

                    </form>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" id="updateCreditBtn">Update</button>
                </div>

            </div>
        </div>
    </div>

    <!-- Alert Modal -->
    <div class="modal fade" id="alertModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" id="alertHeader">
                    <h5 class="modal-title" id="alertTitle">Alert</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="alertBody"></div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Expense Confirmation Modal -->
    <div class="modal fade" id="deleteExpenseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Confirm Delete
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">Are you sure you want to delete this expense?</p>
                    <div class="alert alert-warning">
                        <strong>Expense Details:</strong><br>
                        <span id="deleteExpenseDetails"></span>
                    </div>
                    <p class="text-muted small mb-0">This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteExpense">
                        <i class="bi bi-trash me-1"></i>
                        Delete Expense
                    </button>
                </div>
            </div>
        </div>
    </div>



    <!-- EDIT LOAD MODAL -->
    <div class="modal fade" id="editLoadModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Company Load</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form id="editLoadForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="editLoadId" name="id">

                        <div class="row g-3">
                            <div class="col-6">
                                <label>Plate Number</label>
                                <select id="editLoadPlate" name="plate_number" class="form-select">
                                    @foreach ($trucks ?? [] as $truck)
                                        <option value="{{ $truck->plate_number }}">
                                            {{ $truck->plate_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6">
                                <label>Date</label>
                                <input type="date" id="editLoadDate" name="date" class="form-control">
                            </div>

                            <div class="col-6">
                                <label>Time</label>
                                <input type="time" id="editLoadTime" name="time" class="form-control">
                            </div>

                            <div class="col-6">
                                <label>Amount</label>
                                <input type="number" step="0.01" id="editLoadDebit" name="debit"
                                    class="form-control">
                            </div>

                            <div class="col-6">
                                <label>Receipt</label>
                                <select id="editLoadReceipt" name="receipt_surrendered" class="form-select">
                                    <option value="">Select</option>
                                    <option value="YES">Yes</option>
                                    <option value="NO">No</option>
                                </select>
                            </div>

                            <div class="col-6">
                                <label>Upload New Receipt</label>
                                <input type="file" id="editLoadReceiptImage" name="receipt_image"
                                    class="form-control">
                            </div>

                            <div class="col-6">
                                <label class="form-label d-block">Current Receipt</label>

                                <button type="button" id="viewCurrentLoadReceiptBtn"
                                    class="btn btn-outline-primary btn-sm d-none view-receipt-btn">
                                    <i class="bi bi-eye"></i> View Current Receipt
                                </button>

                                <span id="noCurrentLoadReceipt" class="text-muted small">
                                    No receipt uploaded
                                </span>
                            </div>

                            <div class="col-6">
                                <label>Remarks</label>
                                <input type="text" id="editLoadRemarks" name="remarks" class="form-control">
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="updateLoadBtn" class="btn btn-primary">
                        Update Load
                    </button>
                </div>

            </div>
        </div>
    </div>

    <!-- EDIT FUEL MODAL -->
    <div class="modal fade" id="editFuelModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Fuel Expense</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form id="editFuelForm" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" id="editFuelId" name="id">

                        <div class="row g-3">
                            <div class="col-6">
                                <label>Plate Number</label>
                                <select id="editFuelPlate" name="plate_number" class="form-select">
                                    @foreach ($trucks ?? [] as $truck)
                                        <option value="{{ $truck->plate_number }}">
                                            {{ $truck->plate_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-6">
                                <label>Date</label>
                                <input type="date" id="editFuelDate" name="date" class="form-control">
                            </div>

                            <div class="col-6">
                                <label>Liters</label>
                                <input type="number" step="0.01" id="editFuelLiters" name="liters"
                                    class="form-control">
                            </div>

                            <div class="col-6">
                                <label>Amount</label>
                                <input type="number" step="0.01" id="editFuelDebit" name="debit"
                                    class="form-control">
                            </div>

                            <div class="col-6">
                                <label>Start Odometer</label>
                                <input type="number" id="editFuelStart" name="start_odometer" autocomplete="off"
                                    class="form-control">
                            </div>

                            <div class="col-6">
                                <label>End Odometer</label>
                                <input type="number" id="editFuelEnd" name="odometer" autocomplete="off"
                                    class="form-control">
                            </div>

                            <div class="col-6">
                                <label>Receipt</label>
                                <select id="editFuelReceipt" name="receipt_surrendered" class="form-select">
                                    <option value="">Select</option>
                                    <option value="YES">Yes</option>
                                    <option value="NO">No</option>
                                </select>
                            </div>

                            <div class="col-6">
                                <label>Upload New Receipt</label>
                                <input type="file" id="editFuelReceiptImage" name="receipt_image"
                                    class="form-control">
                            </div>

                            <div class="col-12">
                                <label class="form-label d-block">Current Receipt</label>

                                <button type="button" id="viewCurrentFuelReceiptBtn"
                                    class="btn btn-outline-primary btn-sm d-none view-receipt-btn">
                                    <i class="bi bi-eye"></i> View Current Receipt
                                </button>

                                <span id="noCurrentFuelReceipt" class="text-muted small">
                                    No receipt uploaded
                                </span>
                            </div>

                            <div class="col-12">
                                <label>Remarks</label>
                                <input type="text" id="editFuelRemarks" name="remarks" class="form-control">
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="updateFuelBtn" class="btn btn-primary">
                        Update Fuel
                    </button>
                </div>

            </div>
        </div>
    </div>




    {{-- =========================
   GOVERNMENT DEDUCTIONS
   SHOW ONLY PLATES WITH RECORDS
========================= --}}
    @php
        $deductionGroups = collect($deductions ?? [])->groupBy('plate_number');
    @endphp

    <div class="card mt-4">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h6 class="fw-bold mb-0">Government Benefits Deductions 🧾</h6>
                <small class="text-muted">SSS, PAG-IBIG, PhilHealth payment records per plate</small>
            </div>

            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#addDeductionModal">
                + Add Deduction
            </button>
        </div>

        <div class="card-body">

            @forelse($deductionGroups as $plate => $records)

                @php
                    $sss = $records->where('deduction_type', 'sss')->sortByDesc('date_paid')->first();
                    $pagibig = $records->where('deduction_type', 'pagibig')->sortByDesc('date_paid')->first();
                    $philhealth = $records->where('deduction_type', 'philhealth')->sortByDesc('date_paid')->first();
                @endphp

                <div class="mb-4 border rounded-4 p-3 shadow-sm">

                    <h6 class="fw-bold text-primary mb-3">
                        Plate #: {{ $plate }}
                    </h6>

                    {{-- =========================
                        CENTERED DEDUCTIONS TABLE
                    ========================= --}}

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle text-center">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center align-middle">SSS</th>
                                    <th class="text-center align-middle">PAG-IBIG</th>
                                    <th class="text-center align-middle">PhilHealth</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr>

                                    {{-- SSS --}}
                                    <td class="text-center align-middle">
                                        @if ($sss)
                                            <div class="fw-semibold mb-1">
                                                Date: {{ $sss->date_paid }}
                                            </div>

                                            <div class="fw-bold mb-2">
                                                PHP{{ number_format($sss->amount, 2) }}
                                            </div>

                                            <div class="d-flex justify-content-center gap-2 flex-wrap">

                                                @if ($sss->receipt_image)
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-secondary view-receipt-btn"
                                                        data-image="{{ asset('storage/' . $sss->receipt_image) }}">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                @endif

                                                <button class="btn btn-sm btn-primary edit-deduction-btn"
                                                    data-id="{{ $sss->id }}" data-plate="{{ $sss->plate_number }}"
                                                    data-type="{{ $sss->deduction_type }}"
                                                    data-date="{{ $sss->date_paid }}" data-amount="{{ $sss->amount }}"
                                                    data-remarks="{{ $sss->remarks }}"
                                                    data-image="{{ $sss->receipt_image ? asset('storage/' . $sss->receipt_image) : '' }}"
                                                    data-bs-toggle="modal" data-bs-target="#editDeductionModal">
                                                    <i class="bi bi-pencil"></i>
                                                </button>

                                                <button class="btn btn-sm btn-outline-danger delete-deduction-btn"
                                                    data-id="{{ $sss->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>

                                            </div>
                                        @else
                                            <span class="text-muted">No payment yet</span>
                                        @endif
                                    </td>

                                    {{-- PAGIBIG --}}
                                    <td class="text-center align-middle">
                                        @if ($pagibig)
                                            <div class="fw-semibold mb-1">
                                                Date: {{ $pagibig->date_paid }}
                                            </div>

                                            <div class="fw-bold mb-2">
                                                PHP{{ number_format($pagibig->amount, 2) }}
                                            </div>

                                            <div class="d-flex justify-content-center gap-2 flex-wrap">

                                                @if ($pagibig->receipt_image)
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-secondary view-receipt-btn"
                                                        data-image="{{ asset('storage/' . $pagibig->receipt_image) }}">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                @endif

                                                <button class="btn btn-sm btn-primary edit-deduction-btn"
                                                    data-id="{{ $pagibig->id }}"
                                                    data-plate="{{ $pagibig->plate_number }}"
                                                    data-type="{{ $pagibig->deduction_type }}"
                                                    data-date="{{ $pagibig->date_paid }}"
                                                    data-amount="{{ $pagibig->amount }}"
                                                    data-remarks="{{ $pagibig->remarks }}"
                                                    data-image="{{ $pagibig->receipt_image ? asset('storage/' . $pagibig->receipt_image) : '' }}"
                                                    data-bs-toggle="modal" data-bs-target="#editDeductionModal">
                                                    <i class="bi bi-pencil"></i>
                                                </button>

                                                <button class="btn btn-sm btn-outline-danger delete-deduction-btn"
                                                    data-id="{{ $pagibig->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>

                                            </div>
                                        @else
                                            <span class="text-muted">No payment yet</span>
                                        @endif
                                    </td>

                                    {{-- PHILHEALTH --}}
                                    <td class="text-center align-middle">
                                        @if ($philhealth)
                                            <div class="fw-semibold mb-1">
                                                Date: {{ $philhealth->date_paid }}
                                            </div>

                                            <div class="fw-bold mb-2">
                                                PHP{{ number_format($philhealth->amount, 2) }}
                                            </div>

                                            <div class="d-flex justify-content-center gap-2 flex-wrap">

                                                @if ($philhealth->receipt_image)
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-secondary view-receipt-btn"
                                                        data-image="{{ asset('storage/' . $philhealth->receipt_image) }}">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                @endif

                                                <button class="btn btn-sm btn-primary edit-deduction-btn"
                                                    data-id="{{ $philhealth->id }}"
                                                    data-plate="{{ $philhealth->plate_number }}"
                                                    data-type="{{ $philhealth->deduction_type }}"
                                                    data-date="{{ $philhealth->date_paid }}"
                                                    data-amount="{{ $philhealth->amount }}"
                                                    data-remarks="{{ $philhealth->remarks }}"
                                                    data-image="{{ $philhealth->receipt_image ? asset('storage/' . $philhealth->receipt_image) : '' }}"
                                                    data-bs-toggle="modal" data-bs-target="#editDeductionModal">
                                                    <i class="bi bi-pencil"></i>
                                                </button>

                                                <button class="btn btn-sm btn-outline-danger delete-deduction-btn"
                                                    data-id="{{ $philhealth->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>

                                            </div>
                                        @else
                                            <span class="text-muted">No payment yet</span>
                                        @endif
                                    </td>

                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>

            @empty
                <div class="text-center py-4 text-muted">
                    No deductions recorded yet.
                </div>
            @endforelse

        </div>
    </div>


    {{-- =========================
   ADD DEDUCTION MODAL
========================= --}}
    <div class="modal fade" id="addDeductionModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Add Deduction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form id="deductionForm" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Plate Number</label>
                                <select name="plate_number" class="form-select" required>
                                    <option value="">Select Plate</option>
                                    @foreach ($trucks as $truck)
                                        <option value="{{ $truck->plate_number }}">
                                            {{ $truck->plate_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Deduction Type</label>
                                <select name="deduction_type" class="form-select" required>
                                    <option value="">Select Type</option>
                                    <option value="sss">SSS</option>
                                    <option value="pagibig">PAG-IBIG</option>
                                    <option value="philhealth">PhilHealth</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Date Paid</label>
                                <input type="date" name="date_paid" class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Amount</label>
                                <input type="number" step="0.01" name="amount" class="form-control" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Receipt Image</label>
                                <input type="file" name="receipt_image" class="form-control">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Remarks</label>
                                <input type="text" name="remarks" class="form-control">
                            </div>

                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="saveDeductionBtn" class="btn btn-success">
                        Save Deduction
                    </button>
                </div>

            </div>
        </div>
    </div>

    {{-- =========================
   EDIT DEDUCTION MODAL
   PLACE THIS BELOW ADD DEDUCTION MODAL
========================= --}}
    <div class="modal fade" id="editDeductionModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Deduction</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form id="editDeductionForm" enctype="multipart/form-data">
                        @csrf

                        <input type="hidden" id="editDeductionId" name="id">

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="form-label">Plate Number</label>
                                <select id="editDeductionPlate" name="plate_number" class="form-select" required>
                                    @foreach ($trucks as $truck)
                                        <option value="{{ $truck->plate_number }}">
                                            {{ $truck->plate_number }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Deduction Type</label>
                                <select id="editDeductionType" name="deduction_type" class="form-select" required>
                                    <option value="sss">SSS</option>
                                    <option value="pagibig">PAG-IBIG</option>
                                    <option value="philhealth">PhilHealth</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Date Paid</label>
                                <input type="date" id="editDeductionDate" name="date_paid" class="form-control"
                                    required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Amount</label>
                                <input type="number" step="0.01" id="editDeductionAmount" name="amount"
                                    class="form-control" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Upload New Receipt</label>
                                <input type="file" id="editDeductionReceipt" name="receipt_image"
                                    class="form-control">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label d-block">Current Receipt</label>

                                <button type="button" id="viewCurrentDeductionReceiptBtn"
                                    class="btn btn-outline-primary btn-sm d-none view-receipt-btn">
                                    <i class="bi bi-eye"></i> View Current Receipt
                                </button>

                                <span id="noCurrentDeductionReceipt" class="text-muted small">
                                    No receipt uploaded
                                </span>
                            </div>

                            <div class="col-12">
                                <label class="form-label">Remarks</label>
                                <input type="text" id="editDeductionRemarks" name="remarks" class="form-control">
                            </div>

                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="updateDeductionBtn" class="btn btn-primary">
                        Update Deduction
                    </button>
                </div>

            </div>
        </div>
    </div>


    <div class="modal fade" id="viewReceiptModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">

                <div class="modal-header border-0">
                    <h5 class="modal-title">Receipt Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body text-center">

                    <img id="receiptPreview" src="" class="img-fluid rounded shadow d-none"
                        style="max-height:80vh; object-fit:contain;">

                    <div id="noReceiptText" class="text-muted fw-semibold d-none">
                        No receipt uploaded
                    </div>

                </div>

            </div>
        </div>
    </div>

@endsection


@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            let deleteExpenseId = null;

            // ======================
            // DELETE EXPENSE MODAL
            // ======================
            document.querySelectorAll('.delete-expense-btn').forEach(btn => {
                btn.addEventListener('click', function() {

                    deleteExpenseId = this.dataset.id;

                    const details = `
                Plate: ${this.dataset.plate} <br>
                Date: ${this.dataset.date} <br>
                Amount: PHP ${parseFloat(this.dataset.debit).toFixed(2)}
            `;

                    document.getElementById('deleteExpenseDetails').innerHTML = details;

                    bootstrap.Modal.getOrCreateInstance(
                        document.getElementById('deleteExpenseModal')
                    ).show();
                });
            });

            // ======================
            // EDIT CREDIT (YOU ARE MISSING THIS)
            // ======================
            document.querySelectorAll('.edit-credit-btn').forEach(btn => {
                btn.addEventListener('click', function() {

                    document.getElementById('editCreditId').value = this.dataset.id || '';
                    document.getElementById('editCreditDate').value = this.dataset.date || '';
                    document.getElementById('editCreditAmount').value = this.dataset.amount || '';

                });
            });

            document.getElementById('confirmDeleteExpense').addEventListener('click', function() {

                if (!deleteExpenseId) return;

                fetch(`/owner/payroll/expenses/${deleteExpenseId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success) return showAlert(data.message || 'Delete failed', 'error');

                        showAlert('Expense deleted successfully!', 'success');
                        setTimeout(() => location.reload(), 500);
                        closeModal('deleteExpenseModal');
                    })
                    .catch(err => showAlert(err.message, 'error'));
            });

            // ======================
            // DELETE CREDIT
            // ======================
            document.querySelectorAll('.delete-credit-btn').forEach(btn => {
                btn.addEventListener('click', function() {

                    const id = this.dataset.id;
                    if (!confirm('Delete this credit?')) return;

                    fetch(`/owner/payroll/credits/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (!data.success) return showAlert(data.message || 'Delete failed',
                                'error');

                            showAlert('Credit deleted successfully!', 'success');
                            setTimeout(() => location.reload(), 500);
                        })
                        .catch(err => showAlert(err.message, 'error'));
                });
            });

            // ======================
            // ELEMENTS
            // ======================
            const typeSelect = document.getElementById('expenseType');
            const plateSelect = document.getElementById('plateSelect');
            const contactField = document.getElementById('contactField');
            const contactWrapper = document.getElementById('contactWrapper');
            const fuelFields = document.querySelector('.fuel-fields');
            const remarksInput = document.querySelector('#addExpenseModal input[name="remarks"]');
            const timeWrapper = document.getElementById('timeWrapper');
            const lastOdoUrl = "{{ url('/owner/payroll/expenses/last-odometer') }}";

            let totalCredit = {{ $totalCredit }};
            let totalDebit = {{ $totalDebit }};

            // ======================
            // MAIN UI LOGIC (FUEL / LOAD)
            // ======================
            function updateExpenseUI() {

                const type = typeSelect.value;

                // DEFAULT
                if (!type) {
                    fuelFields.style.display = 'none';
                    contactWrapper.style.display = 'none';
                    timeWrapper.style.display = 'none';

                    fuelFields.querySelectorAll('input').forEach(i => i.disabled = true);
                    contactField.value = '';
                    return;
                }

                if (type === 'fuel') {

                    fuelFields.style.display = '';
                    fuelFields.querySelectorAll('input').forEach(i => i.disabled = false);

                    contactWrapper.style.display = 'none';
                    timeWrapper.style.display = 'none';

                    contactField.value = '';

                    // IMPORTANT:
                    // If plate already selected, auto-fetch last odometer
                    const plate = plateSelect.value;

                    if (plate) {
                        plateSelect.dispatchEvent(new Event('change'));
                    }
                } else if (type === 'load') {

                    fuelFields.style.display = 'none';
                    fuelFields.querySelectorAll('input').forEach(i => i.disabled = true);

                    contactWrapper.style.display = '';
                    timeWrapper.style.display = ''; // ✅ FIXED

                    const selected = plateSelect.options[plateSelect.selectedIndex];
                    const company = selected?.getAttribute('data-company') || '';

                    contactField.value = company;

                    if (company) {
                        remarksInput.value = `Load payment sent to ${company}`;
                    }
                }
            }

            // ======================
            // EVENTS
            // ======================
            typeSelect.addEventListener('change', updateExpenseUI);

            plateSelect.addEventListener('change', function() {

                const plate = this.value;

                if (plate && typeSelect.value === 'fuel') {

                    const addStart = document.querySelector(
                        '#addExpenseModal input[name="start_odometer"]'
                    );

                    const addEnd = document.querySelector(
                        '#addExpenseModal input[name="odometer"]'
                    );

                    if (!addStart) return;

                    // ONLY clear if truly blank first load
                    const currentManualStart = addStart.value;

                    // NEVER touch end automatically
                    if (addEnd && !addEnd.dataset.userTyped) {
                        addEnd.value = '';
                    }

                    fetch(`${lastOdoUrl}/${plate}`)
                        .then(res => res.json())
                        .then(data => {

                            // EXISTING RECORD = auto previous odometer
                            if (data.odometer !== null && data.odometer !== undefined) {

                                addStart.value = data.odometer;
                                addStart.setAttribute('readonly', true);

                            } else {

                                // FIRST ENTRY = preserve manual input
                                addStart.value = currentManualStart || '';
                                addStart.removeAttribute('readonly');
                            }
                        })
                        .catch(err => {
                            console.error('Failed to fetch last odometer:', err);

                            // Keep user input
                            addStart.value = currentManualStart || '';
                            addStart.removeAttribute('readonly');
                        });
                }

                if (typeSelect.value === 'load') {
                    const selected = this.options[this.selectedIndex];
                    const company = selected.getAttribute('data-company') || '';

                    contactField.value = company;

                    if (company) {
                        remarksInput.value = `Load payment sent to ${company}`;
                    }
                }
            });

            document.querySelector('#addExpenseModal input[name="odometer"]')
                ?.addEventListener('input', function() {
                    this.dataset.userTyped = "true";
                });

            // ======================
            // MODAL RESET
            // ======================
            const addExpenseModal = document.getElementById('addExpenseModal');

            if (addExpenseModal) {
                addExpenseModal.addEventListener('show.bs.modal', function() {


                    const today = new Date().toISOString().split('T')[0];
                    document.querySelector('#addExpenseModal input[name="date"]').value = today;

                    document.getElementById('expenseForm').reset();

                    const startField = document.querySelector(
                        '#addExpenseModal input[name="start_odometer"]');
                    const endField = document.querySelector('#addExpenseModal input[name="odometer"]');

                    if (startField) {
                        startField.value = '';
                        startField.removeAttribute('readonly');
                    }

                    if (endField) {
                        endField.value = '';
                    };

                    plateSelect.value = '';
                    typeSelect.value = '';
                    fuelFields.style.display = 'none';
                    contactWrapper.style.display = 'none';
                    timeWrapper.style.display = 'none';



                    updateExpenseUI();
                });
            }

            // LOAD ELEMENTS
            const viewLoadBtn = document.getElementById('viewCurrentLoadReceiptBtn');
            const noLoadReceipt = document.getElementById('noCurrentLoadReceipt');

            // FUEL ELEMENTS
            const viewFuelBtn = document.getElementById('viewCurrentFuelReceiptBtn');
            const noFuelReceipt = document.getElementById('noCurrentFuelReceipt');

            // ======================
            // EDIT LOAD
            // ======================
            document.querySelectorAll('.edit-load-btn').forEach(btn => {
                btn.addEventListener('click', function() {

                    editLoadId.value = this.dataset.id;
                    editLoadPlate.value = this.dataset.plate;
                    editLoadDate.value = this.dataset.date;
                    editLoadTime.value = this.dataset.time;
                    editLoadDebit.value = this.dataset.debit;
                    editLoadReceipt.value = this.dataset.receipt;
                    editLoadRemarks.value = this.dataset.remarks;

                    const image = this.dataset.image || '';

                    if (image) {
                        viewLoadBtn.classList.remove('d-none');
                        noLoadReceipt.classList.add('d-none');
                        viewLoadBtn.dataset.image = image;
                    } else {
                        viewLoadBtn.classList.add('d-none');
                        noLoadReceipt.classList.remove('d-none');
                        viewLoadBtn.dataset.image = '';
                    }
                });
            });


            // ======================
            // EDIT FUEL (FIXED START/END)
            // ======================
            document.querySelectorAll('.edit-fuel-btn').forEach(btn => {
                btn.addEventListener('click', function() {

                    editFuelId.value = this.dataset.id || '';
                    editFuelPlate.value = this.dataset.plate || '';
                    editFuelDate.value = this.dataset.date || '';
                    editFuelLiters.value = this.dataset.liters || '';
                    editFuelDebit.value = this.dataset.debit || '';

                    // HARD RESET
                    // HARD RESET
                    editFuelStart.value = '';
                    editFuelEnd.value = '';

                    // EXACT VALUES ONLY
                    editFuelStart.value = this.dataset.start ?? '';
                    editFuelEnd.value = this.dataset.end ?? '';

                    editFuelReceipt.value = this.dataset.receipt || '';
                    editFuelRemarks.value = this.dataset.remarks || '';

                    const image = this.dataset.image || '';

                    if (image) {
                        viewFuelBtn.classList.remove('d-none');
                        noFuelReceipt.classList.add('d-none');
                        viewFuelBtn.dataset.image = image;
                    } else {
                        viewFuelBtn.classList.add('d-none');
                        noFuelReceipt.classList.remove('d-none');
                        viewFuelBtn.dataset.image = '';
                    }
                });
            });

            document.getElementById('viewReceiptModal').addEventListener('hidden.bs.modal', function() {
                document.body.classList.add('modal-open');
            });

            document.querySelectorAll('.view-receipt-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const modalEl = document.getElementById('viewReceiptModal');

                    const img = document.getElementById('receiptPreview');
                    const text = document.getElementById('noReceiptText');

                    const image = this.dataset.image?.trim();

                    // RESET
                    img.classList.add('d-none');
                    text.classList.add('d-none');
                    img.removeAttribute('src');

                    if (image) {
                        img.src = image;
                        img.classList.remove('d-none');
                    } else {
                        text.classList.remove('d-none');
                    }

                    // SHOW RECEIPT MODAL WITHOUT BREAKING PARENT MODAL
                    const modal = new bootstrap.Modal(modalEl, {
                        backdrop: true,
                        keyboard: true,
                        focus: false
                    });

                    modal.show();
                });
            });

            // ======================
            // UPDATE LOAD
            // ======================
            document.getElementById('updateLoadBtn')?.addEventListener('click', function(e) {
                e.preventDefault();

                const form = document.getElementById('editLoadForm');
                const formData = new FormData(form);

                fetch('/owner/payroll/expenses/update-load', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success) {
                            return showAlert(data.message || 'Failed to update load', 'error');
                        }

                        showAlert('Load updated successfully!', 'success');
                        closeModal('editLoadModal');
                        setTimeout(() => location.reload(), 700);
                    })
                    .catch(err => showAlert(err.message, 'error'));
            });

            // ======================
            // UPDATE FUEL
            // ======================
            document.getElementById('updateFuelBtn').addEventListener('click', function() {
                const form = document.getElementById('editFuelForm');
                const formData = new FormData(form);

                fetch('/owner/payroll/expenses/update', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (!data.success) return showAlert(data.message || 'Update failed', 'error');

                        showAlert('Fuel updated successfully!', 'success');
                        setTimeout(() => location.reload(), 500);
                    })
                    .catch(err => showAlert(err.message, 'error'));
            });

            // ======================
            // SAVE CREDIT
            // ======================
            document.getElementById('saveCredit').addEventListener('click', function(e) {
                e.preventDefault();

                const form = document.getElementById('creditForm');
                const formData = new FormData(form);
                const token = form.querySelector('input[name="_token"]').value;

                fetch('/owner/payroll/credits/store', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        body: formData
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (!data.success) return showAlert(data.message || 'Failed to save credit',
                            'error');

                        showAlert('Credit added successfully!', 'success');
                        setTimeout(() => location.reload(), 500);
                        closeModal('addCreditModal');
                    })
                    .catch(err => showAlert(err.message, 'error'));
            });

            // ======================
            // SAVE EXPENSE
            // ======================
            document.getElementById('saveExpense').addEventListener('click', function(e) {
                e.preventDefault();

                const form = document.getElementById('expenseForm');
                const formData = new FormData(form);
                const token = form.querySelector('input[name="_token"]').value;

                fetch('{{ route('owner.payroll.expenses.store') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        body: formData
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (!data.success) return showAlert(data.message || 'Failed to save expense',
                            'error');

                        showAlert('Expense added successfully!', 'success');
                        setTimeout(() => location.reload(), 500);
                        closeModal('addExpenseModal');
                    })
                    .catch(err => showAlert(err.message, 'error'));
            });

            // ======================
            // UI HELPERS
            // ======================
            function formatPHP(num) {
                return 'PHP' + Number(num).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            }

            function updateBalanceDisplay() {
                document.getElementById('creditBalance').textContent = formatPHP(totalCredit - totalDebit);
            }

            function updateDebitDisplay() {
                document.getElementById('debitTotal').textContent = formatPHP(totalDebit);
            }

            function updateTotalCreditDisplay() {
                document.getElementById('totalCredit').textContent = formatPHP(totalCredit);
            }

            function showAlert(message, type = 'info') {

                const header = document.getElementById('alertHeader');
                const title = document.getElementById('alertTitle');
                const body = document.getElementById('alertBody');

                header.className = 'modal-header';

                if (type === 'success') {
                    header.classList.add('bg-success', 'text-white');
                    title.textContent = '✓ Success';
                } else if (type === 'error') {
                    header.classList.add('bg-danger', 'text-white');
                    title.textContent = '✗ Error';
                }

                body.innerHTML = message;

                bootstrap.Modal.getOrCreateInstance(document.getElementById('alertModal')).show();
            }

            function closeModal(id) {
                bootstrap.Modal.getOrCreateInstance(document.getElementById(id)).hide();
                document.body.classList.remove('modal-open');
                document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
            }

            document.querySelectorAll('.billed-checkbox').forEach(cb => {
                cb.addEventListener('change', function() {

                    const id = this.dataset.id;
                    const billed = this.checked ? 1 : 0;

                    fetch(`/owner/payroll/expenses/${id}/billed`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content,
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                billed: billed
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (!data.success) {
                                alert('Update failed');
                            }
                        });
                });
            });

            // =============================
            // DEDUCTIONS (PUT THIS NEAR SAVE CREDIT / SAVE EXPENSE)
            // IMPORTANT:
            // Place this INSIDE document.addEventListener('DOMContentLoaded', function() { ... })
            // BEFORE INIT section
            // =============================

            // SAVE DEDUCTION
            const saveDeductionBtn = document.getElementById('saveDeductionBtn');

            if (saveDeductionBtn) {
                saveDeductionBtn.addEventListener('click', function(e) {
                    e.preventDefault();

                    const form = document.getElementById('deductionForm');

                    if (!form) return;

                    const formData = new FormData(form);
                    const token = form.querySelector('input[name="_token"]').value;

                    fetch('/owner/payroll/deductions/store', {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            },
                            body: formData
                        })
                        .then(res => res.json())
                        .then(data => {

                            if (!data.success) {
                                return showAlert(data.message || 'Failed to save deduction', 'error');
                            }

                            showAlert('Deduction added successfully!', 'success');

                            closeModal('addDeductionModal');

                            setTimeout(function() {
                                location.reload();
                            }, 700);
                        })
                        .catch(err => {
                            console.error(err);
                            showAlert('Something went wrong while saving deduction.', 'error');
                        });
                });
            }


            // DELETE DEDUCTION
            document.querySelectorAll('.delete-deduction-btn').forEach(function(btn) {

                btn.addEventListener('click', function() {

                    const id = this.dataset.id;

                    if (!id) return;

                    if (!confirm('Delete this deduction record?')) return;

                    fetch('/owner/payroll/deductions/' + id, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector(
                                    'meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {

                            if (!data.success) {
                                return showAlert(data.message || 'Delete failed', 'error');
                            }

                            showAlert('Deduction deleted successfully!', 'success');

                            setTimeout(function() {
                                location.reload();
                            }, 700);
                        })
                        .catch(err => {
                            console.error(err);
                            showAlert('Something went wrong while deleting.', 'error');
                        });

                });

            });


            // ======================
            // EDIT DEDUCTION
            // PLACE INSIDE DOMContentLoaded
            // ======================
            const viewDeductionBtn = document.getElementById('viewCurrentDeductionReceiptBtn');
            const noDeductionReceipt = document.getElementById('noCurrentDeductionReceipt');

            document.querySelectorAll('.edit-deduction-btn').forEach(btn => {
                btn.addEventListener('click', function() {

                    document.getElementById('editDeductionId').value = this.dataset.id || '';
                    document.getElementById('editDeductionPlate').value = this.dataset.plate || '';
                    document.getElementById('editDeductionType').value = this.dataset.type || '';
                    document.getElementById('editDeductionDate').value = this.dataset.date || '';
                    document.getElementById('editDeductionAmount').value = this.dataset.amount ||
                        '';
                    document.getElementById('editDeductionRemarks').value = this.dataset.remarks ||
                        '';

                    const image = this.dataset.image || '';

                    if (image) {
                        viewDeductionBtn.classList.remove('d-none');
                        noDeductionReceipt.classList.add('d-none');
                        viewDeductionBtn.dataset.image = image;
                    } else {
                        viewDeductionBtn.classList.add('d-none');
                        noDeductionReceipt.classList.remove('d-none');
                        viewDeductionBtn.dataset.image = '';
                    }
                });
            });


            // ======================
            // UPDATE DEDUCTION
            // ======================
            document.getElementById('updateDeductionBtn')?.addEventListener('click', function(e) {
                e.preventDefault();

                const form = document.getElementById('editDeductionForm');

                if (!form) return;

                const formData = new FormData(form);

                fetch('/owner/payroll/deductions/update', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(res => res.json())
                    .then(data => {

                        if (!data.success) {
                            return showAlert(data.message || 'Failed to update deduction', 'error');
                        }

                        showAlert('Deduction updated successfully!', 'success');

                        closeModal('editDeductionModal');

                        setTimeout(() => {
                            location.reload();
                        }, 700);
                    })
                    .catch(err => {
                        console.error(err);
                        showAlert('Something went wrong while updating deduction.', 'error');
                    });
            });

            // ======================
            // INIT
            // ======================
            updateBalanceDisplay();
            updateDebitDisplay();
            updateTotalCreditDisplay();
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

        .ui-table thead th {
            background: #f8fafc;
            color: #667085;
            font-weight: 600;
            font-size: .80rem;
            letter-spacing: .02em;
            border-bottom: none !important;
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

        /* ===== COMPACT SUMMARY CARDS ===== */

        /* KPI SECTION ONLY */
        .row.g-3 {
            --bs-gutter-x: .35rem !important;
            /* horizontal dikit */
            --bs-gutter-y: .35rem !important;
            /* vertical dikit */
        }

        /* card mismo */
        .ui-card {
            margin-bottom: 0 !important;
            border-radius: 14px;
        }

        /* optional: bawas side padding ng columns */
        .row.g-3>[class*="col-"] {
            padding-right: .2rem;
            padding-left: .2rem;
        }

        /* mobile tighter */
        @media (max-width: 768px) {
            .row.g-3 {
                --bs-gutter-x: .3rem !important;
                --bs-gutter-y: .3rem !important;
            }

            .row.g-3>[class*="col-"] {
                padding-right: .15rem;
                padding-left: .15rem;
            }
        }


        /* ===== FIXED SUMMARY CARD SPACING (BALANCED) ===== */

        .ui-card .card-body {
            padding: .9rem .9rem .8rem !important;
            min-height: 92px;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
        }

        /* title */
        .ui-kpi-label {
            font-size: 13px;
            font-weight: 700;
            color: #6b7280;
            line-height: 1.25;
            margin-top: 2px;
            margin-bottom: 18px;
            /* enough gap */
        }

        /* amount */
        .ui-kpi-number {
            font-size: 22px;
            font-weight: 800;
            line-height: 1;
            margin-top: 0;
            /* remove auto push */
            transform: translateY(0);
            /* remove extra baba */
            letter-spacing: -.02em;
        }

        /* average fuel */
        .ui-kpi-number.text-dark {
            font-size: 21px;
        }

        /* mobile */
        @media (max-width: 768px) {
            .ui-card .card-body {
                min-height: 82px;
                padding: .75rem .65rem !important;
            }

            .ui-kpi-label {
                font-size: 11px;
                margin-bottom: 12px;
            }

            .ui-kpi-number {
                font-size: 18px;
            }

            .ui-kpi-number.text-dark {
                font-size: 17px;
            }
        }

        /* mismong card */
        .ui-card {
            border-radius: 14px;
            box-shadow: 0 8px 20px rgba(16, 24, 40, .06);
        }

        /* bawas hover para di masyadong tumalon */
        .ui-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(16, 24, 40, .08);
        }


        /* hero header bawas din */
        .ui-hero {
            padding: .9rem 1rem !important;
            margin-bottom: .75rem !important;
            border-radius: 16px;
        }

        .ui-hero h5 {
            font-size: 18px;
            margin-bottom: 2px !important;
        }

        .ui-hero .small {
            font-size: 12px;
        }

        /* mobile */
        @media (max-width: 768px) {
            .ui-kpi-number {
                font-size: 15px;
            }

            .ui-kpi-label {
                font-size: 11px;
            }

            .ui-card .card-body {
                padding: .65rem .6rem !important;
            }
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

        .expenses-filter {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
            /* 🔥 important */
        }

        /* inputs adaptive */
        .expenses-filter select {
            flex: 1;
            min-width: 120px;
        }

        /* buttons fixed */
        .expenses-filter button {
            flex-shrink: 0;
            white-space: nowrap;
        }

        @media (max-width:576px) {

            .expenses-filter {
                flex-direction: column;
                width: 100%;
            }

            .expenses-filter select,
            .expenses-filter button {
                width: 100%;
            }

        }

        .ui-grid-table {
            border-collapse: separate;
            border-spacing: 0 10px;
            /* spacing between rows */
        }

        .ui-grid-table thead th {
            border: none;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #6b7280;
        }

        .ui-grid-table tbody tr {
            background: #fff;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.04);
            border-radius: 12px;
        }

        .ui-grid-table tbody td {
            border: none;
            padding: 14px;
            vertical-align: middle;
        }

        /* rounded row */
        .ui-grid-table tbody tr td:first-child {
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
        }

        .ui-grid-table tbody tr td:last-child {
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
        }
    </style>
@endpush
