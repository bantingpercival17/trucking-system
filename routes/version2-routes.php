<?php

use App\Http\Controllers\Version2\CompanyController;
use App\Http\Controllers\Version2\DashboardController;
use App\Http\Controllers\Version2\DestinationController;
use App\Http\Controllers\Version2\DispatchTripController;
use App\Http\Controllers\Version2\EmployeeController;
use App\Http\Controllers\Version2\PayrollController;
use App\Http\Controllers\Version2\VehicleController;
use App\Models\Destination;
use App\Models\Driver;
use App\Models\Employee;
use App\Models\FlashDestination;
use App\Models\Helper;
use App\Models\TruckDestination;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:owner,it,admin,secretary'])
    ->prefix('/admin')->group(function () {
        Route::get('/', [DashboardController::class, 'indexAdmin']);
        Route::get('/dashboard', [DashboardController::class, 'indexAdmin'])->name('admin.dashboard');

        Route::prefix('{company}')->group(function () {
            Route::get('dashboard', [DashboardController::class, 'indexCompany'])
                ->name('company.dashboard');
            Route::resource('dispatch-trip', DispatchTripController::class)->names([
                'index' => 'company.dispatch.index',
                'create' => 'company.dispatch.create',
                'store' => 'company.dispatch.store',
                'edit' => 'company.dispatch.edit',
                'update' => 'company.dispatch.update',
                'destroy' => 'company.dispatch.destroy',
                //'show' => 'company.dispatch.history',
            ]);
            Route::get('/dispatch-trip/history', [DispatchTripController::class, 'show'])->name('company.dispatch.history');

            Route::resource('destination', DestinationController::class)->names([
                'index' => 'company.destinations.index',
                'create' => 'company.destinations.create',
                'store' => 'company.destinations.store',
                'show' => 'company.destinations.show',
                'edit' => 'company.destinations.edit',
                'update' => 'company.destinations.update',
                'destroy' => 'company.destinations.destroy',
            ]);
            // Payoll
            Route::get('/payroll/dashboard', [PayrollController::class, 'dashboard'])->name('company.payrolls.dashboard');
            Route::resource('payroll', PayrollController::class)->names([
                'index' => 'company.payrolls.index',
                'create' => 'company.payrolls.create',
                'store' => 'company.payrolls.store',
                'show' => 'company.payrolls.show',
                'edit' => 'company.payrolls.edit',
                'update' => 'company.payrolls.update',
                'destroy' => 'company.payrolls.destroy',
            ]);
        });
        Route::resource('dispatch-trip', DispatchTripController::class)->names([
            'index' => 'admin.dispatch.index',
            'create' => 'admin.dispatch.create',
            'store' => 'admin.dispatch.store',
            'edit' => 'admin.dispatch.edit',
            'update' => 'admin.dispatch.update',
            'destroy' => 'admin.dispatch.destroy',
        ]);
        Route::get('/dispatch-trip/list', function () {
            return 'true';
        })->name('admin.dispatch-trip.list');
        // Route::get('/dispatch-trip/history', [DispatchTripController::class, 'dispatchHistory'])->name('admin.dispatch-trip.history');
        Route::post('/dispatch-trip/destroy-all', [DispatchTripController::class, 'destroyAll'])->name('admin.dispatch.destroy-all');
        Route::post('/dispatch-trip/assign/{data}', [DispatchTripController::class, 'assign'])->name('admin.dispatch.assign');
        Route::post('/dispatch-trip/dispatch/{data}', [DispatchTripController::class, 'dispatch'])->name('admin.dispatch.dispatch');
        Route::post('/dispatch-trip/deliver/{data}', [DispatchTripController::class, 'deliver'])->name('admin.dispatch.deliver');
        Route::put('/dispatch-trip/{id}/billing-update', [DispatchTripController::class, 'updateBilling'])->name('admin.dispatch.updateBilling');
        Route::resource('destination', DestinationController::class)->names([
            'index' => 'admin.destinations.index',
            'create' => 'admin.destinations.create',
            'store' => 'admin.destinations.store',
            'show' => 'admin.destinations.show',
            'edit' => 'admin.destinations.edit',
            'update' => 'admin.destinations.update',
            'destroy' => 'admin.destinations.destroy',
        ]);
        Route::resource('vehicle', VehicleController::class)->names([
            'index' => 'admin.vehicles.index',
            'create' => 'admin.vehicles.create',
            'store' => 'admin.vehicles.store',
            'show' => 'admin.vehicles.show',
            'edit' => 'admin.vehicles.edit',
            'update' => 'admin.vehicles.update',
            'destroy' => 'admin.vehicles.destroy',
        ]);
        Route::get('/destinations/{data}', [DestinationController::class, 'destinationList']);
        Route::get('/truck-destinations/{data}', [DestinationController::class, 'truckList']);
        Route::prefix('/company')->controller(CompanyController::class)->group(function () {
            Route::get('/', 'index')->name('admin.company');
            Route::post('/store', 'store')->name('admin.company.store');
            Route::put('/update/{data}', 'update')->name('admin.company.update');
            Route::delete('/remove/{data}', 'destroy')->name('admin.company.destroy');
        });
        Route::resource('employees', EmployeeController::class)->names([
            'index' => 'admin.employees.index',
            'create' => 'admin.employees.create',
            'store' => 'admin.employees.store',
            'show' => 'admin.employees.show',
            'edit' => 'admin.employees.edit',
            'update' => 'admin.employees.update',
            'destroy' => 'admin.employees.destroy',
        ]);
        Route::post('/employees/delete-multiple', [EmployeeController::class, 'deleteMultiple'])->name('admin.employees.delete-multiple');
        Route::get('/employee', function () {
            Driver::each(function ($driver) {
                Employee::firstOrCreate(
                    ['name' => $driver->name,],
                    [
                        'email' => $driver->email,
                        'type' => 'driver',
                        'phone' => $driver->contact_number,
                        'address' => $driver->address,
                        'date_of_birth' => $driver->birthday,
                        'license_number' => null,
                        'profile_picture' => $driver->profile_photo,
                        'is_available' => true,
                    ]
                );
            });
            echo 'Drivers imported successfully.<br>';
            Helper::each(function ($driver) {
                Employee::firstOrCreate(
                    ['name' => $driver->name,],
                    [
                        'email' => $driver->email,
                        'type' => 'helper',
                        'phone' => $driver->contact_number,
                        'address' => $driver->address,
                        'date_of_birth' => $driver->birthday,
                        'license_number' => null,
                        'profile_picture' => $driver->profile_photo,
                        'is_available' => true,
                    ]
                );
            });
            echo 'Helper imported successfully.<br>';
            return 'Employees imported successfully.';
        });
        Route::get('destination-trips', function () {
            // Table: Owner
            Destination::each(function ($tripRate) {
                TruckDestination::firstOrCreate([
                    'company_id' => 1,
                    'destination_code' => $tripRate->store_code,
                    'store_name' => $tripRate->store_name,
                ], [
                    'area' => $tripRate->area,
                    'truck_type' => $tripRate->truck_type,
                    'rate' => $tripRate->rate,
                ]);
            });
            echo 'Chamonix imported successfully.<br>';
            // Table: Flash
            FlashDestination::each(function ($tripRate) {
                TruckDestination::firstOrCreate([
                    'company_id' => 2,
                    'destination_code' => $tripRate->hub_code,
                    'area' => $tripRate->area,
                ], [
                    'store_name' => null,
                    'truck_type' => 'L300',
                    'rate' => $tripRate->rate,
                ]);
            });
            echo 'Flash imported successfully.<br>';
        });


        // Billing : for the payment of the trip, the admin can update the billing information of the trip
        Route::prefix('/{company}/billing')->group(function () {
            Route::controller(DispatchTripController::class)->group(function () {
                Route::get('/', 'showBilling')->name('company.billing.show');
                Route::put('/update/{id}', 'updateBilling')->name('admin.billing.update');
            });
            //Route::put('/update/{id}', 'updateBilling')->name('admin.billing.update');
        });
    });
