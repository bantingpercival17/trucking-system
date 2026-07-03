<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WatsonDashboardController;
use App\Http\Controllers\WatsonTripController;
use App\Http\Controllers\WatsonPayrollController;
use App\Http\Controllers\WatsonPayrollPaymentController;
use App\Http\Controllers\Watson\DestinationController as WatsonDestinationController;
use App\Http\Controllers\TruckController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\HelperController;
use App\Http\Controllers\DriverDocumentController;

/*
|--------------------------------------------------------------------------
| WATSON DASHBOARD
|--------------------------------------------------------------------------
*/
Route::get('/watson/dashboard', [WatsonDashboardController::class, 'index'])
    ->middleware(['auth', 'watson.layout'])
    ->name('watson.dashboard');

Route::middleware(['auth', 'role:owner,it', 'watson.layout'])
    ->prefix('watson')
    ->name('watson.')
    ->group(function () {
        Route::get('/payroll/dashboard', [WatsonPayrollController::class, 'dashboard'])->name('payroll.dashboard');
        Route::get('/payroll/history', [WatsonPayrollController::class, 'history'])->name('payroll.history');
    });

/*
|--------------------------------------------------------------------------
| FILES (DESTINATIONS, TRUCKS, DRIVERS) — ALL ROLES EXCEPT RESTRICTED
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:owner,it,admin,secretary', 'watson.layout'])
    ->prefix('watson')
    ->name('watson.')
    ->group(function () {
        // DESTINATIONS
        Route::get('/destinations', [WatsonDestinationController::class, 'index'])->name('destinations.index');
        Route::post('/destinations', [WatsonDestinationController::class, 'store'])->name('destinations.store');
        Route::put('/destinations/{id}', [WatsonDestinationController::class, 'update'])->name('destinations.update');
        Route::delete('/destinations/{id}', [WatsonDestinationController::class, 'destroy'])->name('destinations.destroy');

        // TRUCKS (shared `trucks` table — same TruckController used by owner.* and flash.*)
        Route::delete('/trucks/delete-all', [TruckController::class, 'destroyAll'])->name('trucks.destroyAll');
        Route::resource('trucks', TruckController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::get('/trucks/sidebar/{id}', [TruckController::class, 'sidebar'])->name('trucks.sidebar');

        // DRIVERS (shared `drivers`/`helpers` tables — same DriverController/HelperController)
        Route::get('/drivers', [DriverController::class, 'index'])->name('drivers.index');
        Route::post('/drivers', [DriverController::class, 'store'])->name('drivers.store');
        Route::put('/drivers/{driver}', [DriverController::class, 'update'])->name('drivers.update');
        Route::delete('/drivers/{driver}', [DriverController::class, 'destroy'])->name('drivers.destroy');
        Route::post('/drivers/delete-multiple', [DriverController::class, 'deleteMultiple'])->name('drivers.deleteMultiple');

        // HELPERS
        Route::post('/helpers', [HelperController::class, 'store'])->name('helpers.store');
        Route::put('/helpers/{helper}', [HelperController::class, 'update'])->name('helpers.update');
        Route::delete('/helpers/{helper}', [HelperController::class, 'destroy'])->name('helpers.destroy');

        Route::get('/person-docs/{id}/{type}', [DriverDocumentController::class, 'getDocs'])->name('person-docs.get');
        Route::post('/person-docs/save', [DriverDocumentController::class, 'savePersonDocs'])->name('person-docs.save');

        Route::delete('/people/bulk-delete', [DriverController::class, 'bulkDestroyPeople'])->name('people.bulkDestroy');
    });

/*
|--------------------------------------------------------------------------
| TRIPS (ADMIN CAN ACCESS)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:owner,it,admin,secretary', 'watson.layout'])
    ->prefix('watson')
    ->name('watson.')
    ->group(function () {
        Route::get('/trips', [WatsonTripController::class, 'index'])->name('trips.index');
        Route::post('/trips', [WatsonTripController::class, 'store'])->name('trips.store');
        Route::post('/trips/{id}/assign', [WatsonTripController::class, 'assign'])->name('trips.assign');
        Route::post('/trips/{id}/dispatch', [WatsonTripController::class, 'dispatch'])->name('trips.dispatch');
        Route::post('/trips/{id}/deliver', [WatsonTripController::class, 'deliver'])->name('trips.deliver');
        Route::put('/trips/{id}', [WatsonTripController::class, 'update'])->name('trips.update');

        Route::delete('/trips/all/delete', [WatsonTripController::class, 'destroyAll'])->name('trips.destroyAll');
        Route::delete('/trips/{id}', [WatsonTripController::class, 'destroy'])->name('trips.destroy');

        Route::get('/trips/history', [WatsonTripController::class, 'history'])->name('trips.history');
    });

/*
|--------------------------------------------------------------------------
| BILLING + PAYROLL (ADMIN + SECRETARY + OWNER + IT)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:owner,it,admin,secretary', 'watson.layout'])
    ->prefix('watson')
    ->name('watson.')
    ->group(function () {
        Route::get('/payroll', [WatsonPayrollController::class, 'index'])->name('payroll.index');
        Route::post('/payroll/pay', [WatsonPayrollPaymentController::class, 'store'])->name('payroll.pay');

        Route::get('/payroll/pdf/{id}', [WatsonPayrollController::class, 'downloadPDF'])->name('payroll.pdf');

        Route::delete('/payroll/delete/{id}', [WatsonPayrollController::class, 'destroy'])->name('payroll.delete');

        Route::get('/billing', [WatsonPayrollController::class, 'billing'])->name('payroll.billing');

        Route::post('/trips/{id}/complete', [WatsonPayrollController::class, 'complete'])->name('trips.complete');

        Route::put('/trips/{id}/billing-update', [WatsonPayrollController::class, 'updateBilling'])->name('trips.updateBilling');

        Route::post('/payroll/finalize', [WatsonPayrollController::class, 'finalize'])->name('payroll.finalize');
    });
