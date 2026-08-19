<?php

namespace App\Console\Commands\Import;

use App\Models\AllowanceRange;
use App\Models\DispatchDestinationTrip;
use App\Models\Driver;
use App\Models\Employee;
use App\Models\Helper;
use App\Models\TripPayroll;
use App\Models\TruckDestination;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DispatchTrip extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'run:dispatch-trip';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command use for export all the transaction for the previous  tables.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Export First is the Chamonix Transaction
        $chamonixDispatchTrip = \App\Models\DispatchTrip::all();
        foreach ($chamonixDispatchTrip as $key => $value) {
            $destination = $value->destination;
            $truckDestination = TruckDestination::where('destination_code', $destination->store_code)->where('company_id', 1)->first();
            $driver = Driver::find($value->driver_id);
            $employee = Employee::where('name', $driver->name)->first();
            $billingStatus = array('Unbilled' => null, 'Pending' => 0, 'Billed' => 1); // Billing Status
            $data = array(
                'company_id' => 1,
                'destination_id' => $truckDestination->id,
                'truck_id' => $value->truck_id,
                'driver_id' => $employee->id,
                'helper_first_id' => null,
                'helper_second_id' =>  null,
                'dispatch_date' => $value->dispatch_date,
                'assigned_at' => $value->assigned_at,
                'dispatched_at' => $value->dispatched_at,
                'trip_ticket_no' => $value->trip_ticket_no,
                'dispatch_status' => 'Completed',
                'completed_at' => $value->completed_at,
                'created_at' => $value->created_at,
                'updated_at' => $value->updated_at,
                'billing_status' => $billingStatus[$value->billing_status],
                'check_release_date' => $value->check_release_date,
            );
            // Check if there are helpers associated with the trip
            $dispatchHelper = DB::table('dispatch_trip_helpers')->where('dispatch_trip_id', $value->id)->get();
            if (count($dispatchHelper) > 0) {
                foreach ($dispatchHelper as $helperIndex => $helper) {
                    $data1 = Helper::find($helper->helper_id);
                    $helperEmployee = Employee::where('name', $data1->name)->first();
                    if ($helperIndex == 0) {
                        $data['helper_first_id'] = $helperEmployee->id;
                    } else {
                        $data['helper_second_id'] = $helperEmployee->id;
                    }
                }
            }


            echo json_encode($data);
            echo "\n";
            try {
                $dispatch = DispatchDestinationTrip::create($data);
                // Store Employee Payroll
                $this->employeeDispatchPay($employee, $dispatch);
                echo "Store Driver Payroll \n";
                // Store Helpers Payroll
                if ($dispatch->helper_first_id) {
                    $helperFirst = Employee::find($dispatch->helper_first_id);
                    $this->employeeDispatchPay($helperFirst, $dispatch);
                    echo "Store 1 Helper Payroll \n";
                }
                if ($dispatch->helper_second_id) {
                    $helperSecond = Employee::find($dispatch->helper_second_id);
                    $this->employeeDispatchPay($helperSecond, $dispatch);
                    echo "Store 2 Helper Payroll \n";
                }
                echo "saved \n";
            } catch (\Throwable $th) {
                echo $th->getMessage() . "\n";
            }
            echo "\n";
        }
    }
    function employeeDispatchPay($employee, $dispatch)
    {
        // Allowance
        $destinationRate = $dispatch->destination->rate;
        $allowanceRecord = AllowanceRange::where('rate_from', '<=', $destinationRate)
            ->where('rate_to', '>=', $destinationRate)
            ->first();

        // Fallback to 0 (or a default value) if the rate doesn't match any database ranges
        $allowance = $allowanceRecord ? $allowanceRecord->allowance : 0;
        $percentageRate = [.11, .08];
        $fixRate = [700, 500, 1200];
        // Flash no Helper
        $rate = 0;
        $helperCount = collect([$dispatch->helper_first_id, $dispatch->helper_second_id])
            ->filter() // Removes null, 0, false, or empty strings
            ->count();
        if ($employee->type === 'driver') {
            if ($dispatch->company_id == 3) {
                $rate = $helperCount > 0 ? $fixRate[0] : $fixRate[2];
            } else {
                if ($dispatch->company_id == 2) {
                    $rate = $dispatch->destination->rate;
                } else {
                    $rate = $dispatch->destination->rate * $percentageRate[0];
                }
            }
        } else {
            if ($dispatch->company_id == 3) {
                $rate = $fixRate[1];
            } else {
                $rate = $dispatch->destination->rate * $percentageRate[1];
            }
            $rate = $helperCount > 1 ? ($rate / 2) : $rate;
            $allowance = $helperCount > 1 ? ($allowance / 2) : $allowance;
        }
        TripPayroll::firstOrCreate([
            'dispatch_id' => $dispatch->id,
            'employee_id' => $employee->id
        ], [
            'gross_amount' => $rate,
            'allowance' => $dispatch->company_id != 2 ? $allowance : 0,
            'created_at' => $dispatch->completed_at,
            'updated_at' => $dispatch->completed_at,
        ]);
    }
}
