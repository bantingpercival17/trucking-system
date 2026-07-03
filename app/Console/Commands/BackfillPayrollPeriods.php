<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DispatchTrip;
use App\Models\PayrollPeriod;
use Carbon\Carbon;

class BackfillPayrollPeriods extends Command
{
    protected $signature = 'payroll:backfill
                            {start : YYYY-MM-DD}
                            {end : YYYY-MM-DD}
                            {--paid : Mark created weeks as PAID}';

    protected $description = 'Backfill payroll periods (Mon-Sun) from completed trips';

    public function handle()
    {
        $start = Carbon::parse($this->argument('start'))->startOfWeek(Carbon::MONDAY);
        $end = Carbon::parse($this->argument('end'))->endOfWeek(Carbon::SUNDAY);

        $weeksCreated = 0;

        for ($d = $start->copy(); $d->lte($end); $d->addWeek()) {
            $weekStart = $d->copy();
            $weekEnd = $d->copy()->endOfWeek(Carbon::SUNDAY);

            // Get completed trips in this week
            $trips = DispatchTrip::with(['driver', 'helper', 'destination'])
                ->where('status', 'Completed')
                ->whereDate('dispatch_date', '>=', $weekStart->toDateString())
                ->whereDate('dispatch_date', '<=', $weekEnd->toDateString())
                ->get();

            if ($trips->isEmpty()) {
                continue; // skip empty weeks
            }

            // Compute totals like your payroll logic
            $driversTotal = 0;
            $helpersTotal = 0;

            foreach ($trips as $t) {
                $rate = (float) ($t->rate_snapshot ?? 0);
                $allowance = $this->allowanceFromRate($rate);

                // driver always exists
                $driversTotal += round($rate * 0.12 + $allowance, 2);

                // helper only if exists
                if (!is_null($t->helper_id)) {
                    $helpersTotal += round($rate * 0.1 + $allowance, 2);
                }
            }

            $grandTotal = $driversTotal + $helpersTotal;

            PayrollPeriod::updateOrCreate(
                [
                    'week_start' => $weekStart->toDateString(),
                    'week_end' => $weekEnd->toDateString(),
                ],
                [
                    'drivers_total' => $driversTotal,
                    'helpers_total' => $helpersTotal,
                    'grand_total' => $grandTotal,
                    'is_paid' => $this->option('paid') ? true : false,
                    'paid_at' => $this->option('paid') ? now() : null,
                    'paid_by' => $this->option('paid') ? auth()->id() : null, // may be null in CLI, ok
                ],
            );

            $weeksCreated++;
        }

        $this->info("Done. Weeks processed/created: {$weeksCreated}");
        return 0;
    }

    private function allowanceFromRate(float $rate): float
    {
        return match (true) {
            $rate <= 3200 => 150,
            $rate <= 3400 => 200,
            $rate <= 3600 => 250,
            $rate <= 3800 => 300,
            $rate <= 4000 => 350,
            $rate <= 4500 => 400,
            default => 450,
        };
    }
}
