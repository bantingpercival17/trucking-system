<?php

namespace App\Console\Commands\Import;

use App\Models\DispatchDestinationTrip;
use App\Models\Employee;
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
    protected $signature = 'app:dispatch-trip';

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
            $employee = Employee::where('name', $value->driver->name)->first();
            $helper = $value->helpers();
            $dispatchHelper = DB::table('dispatch_trip_helpers')->where('dispatch_trip_id', $value->id)->get();
            $data = array(
                'company_id' => 1,
                'destination_id' => $truckDestination->id,
                'truck_id' => $value->truck_id,
                'driver_id' => $employee->id,
                'helper_first_id' => $dispatchHelper[0]->helper_id,
                'helper_second_id' => count($dispatchHelper) > 1 ? $dispatchHelper[1]->helper_id : null,
                'dispatch_date' => $value->dispatched_at,
                'assigned_at' => $value->assigned_at,
                'dispatched_at' => $value->dispatched_at,
                'trip_ticket_no' => $value->trip_ticket_no,
                'dispatch_status' => 'Completed',
                'completed_at' => $value->completed_at,
            );
            echo json_encode($data);
            echo "\n";
            try {
                DispatchDestinationTrip::create($data);
                echo "saved \n";
            } catch (\Throwable $th) {
                echo $th->getMessage() . "\n";
            }
            echo "\n";
            //
        }
    }
}
