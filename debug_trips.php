<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Use fully-qualified class names because `use` cannot appear after executable code
// (we bootstrapped the app already).


try {
    $rows = \App\Models\DispatchTrip::latest()->take(10)->get()->toArray();
    $counts = [
        'dispatch_trips' => \App\Models\DispatchTrip::count(),
        'destinations' => \App\Models\Destination::count(),
        'trucks' => \App\Models\Truck::count(),
        'drivers' => \App\Models\Driver::count(),
        'helpers' => \App\Models\Helper::count(),
    ];

    echo json_encode(['counts' => $counts, 'latest' => $rows], JSON_PRETTY_PRINT);
} catch (Throwable $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
    // If no trips exist, create a sample Draft trip (for debugging only)
    try {
        if (\App\Models\DispatchTrip::count() === 0) {
            $dest = \App\Models\Destination::first();
            $truck = \App\Models\Truck::first();
            $driver = \App\Models\Driver::first();

            if ($dest && $truck && $driver) {
                $trip = \App\Models\DispatchTrip::create([
                    'trip_ticket_no' => 'TT-DEBUG-' . time(),
                    'dispatch_date' => date('Y-m-d'),
                    'dispatch_time' => date('H:i:s'),
                    'destination_id' => $dest->id,
                    'truck_id' => $truck->id,
                    'driver_id' => $driver->id,
                    'remarks' => 'Debug trip',
                    'rate_snapshot' => $dest->rate ?? null,
                    'status' => 'Draft',
                ]);

                echo "\nCreated debug trip id=" . $trip->id . "\n";
            } else {
                echo "\nInsufficient related records to create debug trip.\n";
            }
        }
    } catch (Throwable $e) {
        echo "\nCreate error: " . $e->getMessage() . "\n";
    }
