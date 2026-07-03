<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TruckController;
use App\Http\Controllers\DestinationController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HelperController;
use App\Http\Controllers\DispatchTripController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PayrollPaymentController;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\DriverDocumentController;
use App\Http\Controllers\Flash\DestinationController as FlashDestinationController;
use App\Http\Controllers\FlashTripController;
use App\Http\Controllers\FlashPayrollController;
use App\Http\Controllers\FlashPayrollPaymentController;
use App\Http\Controllers\FlashDashboardController;
use App\Http\Controllers\Owner\DeductionController;

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => view('welcome'));

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'role:owner,it,admin,secretary'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| PROFILE
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
});

/*
|--------------------------------------------------------------------------
| OWNER + IT ONLY (INCOME REPORTS + USER MANAGEMENT)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:owner,it'])
    ->prefix('owner')
    ->name('owner.')
    ->group(function () {
        // USERS
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

       

        // 🔴 INCOME REPORTS (RESTRICTED)
        Route::get('/users/reports', [UserController::class, 'reports'])->name('users.reports');

        Route::get('/payroll/dashboard', [PayrollController::class, 'dashboard'])->name('payroll.dashboard');
        Route::get('/payroll/history', [PayrollController::class, 'history'])->name('payroll.history');
    });

/*
|--------------------------------------------------------------------------
| FILES (ALL ROLES EXCEPT RESTRICTED)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:owner,it,admin,secretary'])
    ->prefix('owner')
    ->name('owner.')
    ->group(function () {
        // DESTINATIONS
        Route::get('/destinations', [DestinationController::class, 'index'])->name('destinations.index');
        Route::post('/destinations', [DestinationController::class, 'store'])->name('destinations.store');
        Route::put('/destinations/{destination}', [DestinationController::class, 'update'])->name('destinations.update');

        Route::delete('/destinations/{destination}', [DestinationController::class, 'destroy'])->name('destinations.destroy');

        // TRUCKS
        Route::delete('/trucks/delete-all', [TruckController::class, 'destroyAll'])->name('trucks.destroyAll');
        Route::resource('trucks', TruckController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::get('/trucks/sidebar/{id}', [TruckController::class, 'sidebar'])->name('trucks.sidebar');

        // DRIVERS
        Route::get('/drivers', [DriverController::class, 'index'])->name('drivers.index');
        Route::post('/drivers', [DriverController::class, 'store'])->name('drivers.store');
        Route::put('/drivers/{driver}', [DriverController::class, 'update'])->name('drivers.update');
        Route::delete('/drivers/{driver}', [DriverController::class, 'destroy'])->name('drivers.destroy');

        // HELPERS
        Route::post('/helpers', [HelperController::class, 'store'])->name('helpers.store');
        Route::put('/helpers/{helper}', [HelperController::class, 'update'])->name('helpers.update');
        Route::delete('/helpers/{helper}', [HelperController::class, 'destroy'])->name('helpers.destroy');

        Route::delete('/people/bulk-delete', [DriverController::class, 'bulkDestroyPeople'])->name('people.bulkDestroy');

        Route::get('/payroll/expenses', [PayrollController::class, 'expenses'])->name('payroll.expenses');
    });

/*
|--------------------------------------------------------------------------
| TRIPS (ADMIN CAN ACCESS)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:owner,it,admin,secretary'])
    ->prefix('owner')
    ->name('owner.')
    ->group(function () {
        Route::get('/trips', [DispatchTripController::class, 'index'])->name('trips.index');
        Route::post('/trips', [DispatchTripController::class, 'store'])->name('trips.store');
         Route::delete('/trips/{id}', [DispatchTripController::class, 'destroy'])->name('trips.destroy');

        Route::get('/trips/{id}/edit', [DispatchTripController::class, 'edit'])->name('trips.edit');
        Route::put('/trips/{id}', [DispatchTripController::class, 'update'])->name('trips.update');

        Route::post('/trips/{id}/assign', [DispatchTripController::class, 'assign'])->name('trips.assign');
        Route::post('/trips/{id}/dispatch', [DispatchTripController::class, 'dispatch'])->name('trips.dispatch');
        Route::post('/trips/{id}/deliver', [DispatchTripController::class, 'deliver'])->name('trips.deliver');

        Route::delete('/trips/delete-all', [DispatchTripController::class, 'destroyAll'])->name('trips.destroyAll');
        Route::delete('/trips/{id}', [DispatchTripController::class, 'destroy'])->name('trips.destroy');
        Route::get('/trips/history', [DispatchTripController::class, 'history'])->name('trips.history');
    });

/*
|--------------------------------------------------------------------------
| BILLING + PAYROLL (ADMIN + SECRETARY + OWNER + IT)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:owner,it,admin,secretary'])
    ->prefix('owner')
    ->name('owner.')
    ->group(function () {
        Route::get('/payroll/expenses/last-odometer/{plate}', [PayrollController::class, 'getLastOdometer']);
        Route::delete('/payroll/expenses/{id}', [PayrollController::class, 'deleteExpense']);

        Route::get('/person-docs/{id}/{type}', [DriverDocumentController::class, 'getDocs'])->name('person-docs.get');

        Route::post('/person-docs/save', [DriverDocumentController::class, 'savePersonDocs'])->name('person-docs.save');
        Route::post('/trips/{dispatch_trip_id}/complete', [DispatchTripController::class, 'complete'])->name('trips.complete');
        Route::post('/allowances/update', [PayrollController::class, 'updateAllowances'])->name('allowances.update');

        Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');

        Route::get('/billing', [PayrollController::class, 'billing'])->name('payroll.billing');
        Route::get('/billing/history', [PayrollController::class, 'billingHistory'])->name('billing.history');
        // ✅ ADD THIS
        Route::get('/payroll/pdf/{id}', [PayrollController::class, 'generateIndividualPDF'])->name('payroll.pdf.individual');

        Route::get('/payroll/pdf/{type}/{id}', [PayrollController::class, 'downloadPDF'])->name('payroll.pdf');

        Route::delete('/payroll/payment/{id}', [PayrollController::class, 'destroy'])->name('payroll.delete-payment');

        Route::post('/trips/{id}/add-to-payroll', [PayrollController::class, 'addToPayroll'])->name('trips.addToPayroll');
        Route::put('/trips/{id}/billing-update', [PayrollController::class, 'updateBilling'])->name('trips.updateBilling');

        Route::get('/payroll/edit', [PayrollController::class, 'edit'])->name('payroll.edit');

        Route::post('/payroll/update', [PayrollController::class, 'update'])->name('payroll.update');

        Route::post('/payroll/pay', [PayrollPaymentController::class, 'store'])->name('payroll.pay');
        Route::post('/payroll/finalize', [PayrollController::class, 'finalizeWeek'])->name('payroll.finalize');

        Route::post('/payroll/expenses', [PayrollController::class, 'storeExpense'])->name('payroll.expenses.store');

        Route::post('/payroll/expenses/update', [PayrollController::class, 'updateExpense']);
        Route::post('/payroll/expenses/update-load', [PayrollController::class, 'updateLoadExpense']);

        Route::delete('/payroll/expenses/{id}', [PayrollController::class, 'deleteExpense']);
        Route::delete('/payroll/credits/{id}', [PayrollController::class, 'deleteCredit']);

        Route::post('/payroll/expenses/{id}/billed', [PayrollController::class, 'updateBilled']);

        Route::post('/payroll/credits/store', [PayrollController::class, 'storeCredit'])->name('payroll.credits.store');
        Route::post('/payroll/credits/update', [PayrollController::class, 'updateCredit'])->name('payroll.credits.update');
        Route::post('/company-docs/save', [MaintenanceController::class, 'saveCompanyDoc'])->name('company-docs.save');
        Route::controller(PayrollController::class)->group(function () {
            Route::get('/payroll/v2/', 'index')->name('payroll.view');
        });

        Route::prefix('/payroll/deductions')->group(function () {
            Route::get('/', [DeductionController::class, 'index'])->name('payroll.deductions.index');

            Route::post('/store', [DeductionController::class, 'store'])->name('payroll.deductions.store');

            Route::post('/update', [DeductionController::class, 'update'])->name('payroll.deductions.update');

            Route::delete('/{id}', [DeductionController::class, 'destroy'])->name('payroll.deductions.destroy');
        });
    });

/*
|--------------------------------------------------------------------------
| MAINTENANCE (OPTIONAL FOR ALL)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:owner,it,admin,secretary'])
    ->prefix('owner')
    ->name('owner.')
    ->group(function () {
        Route::get('/reports/maintenance', [MaintenanceController::class, 'index'])->name('reports.maintenance');
        Route::post('/maintenance/save', [MaintenanceController::class, 'save'])->name('maintenance.save');
        Route::post('/personal-vehicle-docs/save', [MaintenanceController::class, 'savePersonalVehicle'])->name('personal-vehicle-docs.save');
        Route::post('/personal-vehicles/store', [MaintenanceController::class, 'storePersonalVehicle'])->name('personal-vehicles.store');
        Route::delete('/personal-vehicles/{id}', [MaintenanceController::class, 'deletePersonalVehicle'])->name('personal-vehicles.delete');
    });

// web.php
Route::get('/switch-layout/{type}', function ($type) {
    if (!in_array($type, ['flash', 'watson', 'default'])) {
        abort(404);
    }

    session(['layout' => $type]);

    if ($type === 'flash') {
        return redirect()->route('flash.dashboard');
    }

    if ($type === 'watson') {
        return redirect()->route('watson.dashboard');
    }

    return redirect()->route('dashboard');
})->name('layout.switch');
/*
|--------------------------------------------------------------------------
| LOGOUT
|--------------------------------------------------------------------------
*/
Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| OWNER + IT ONLY (INCOME REPORTS + USER MANAGEMENT)
|--------------------------------------------------------------------------
*/

Route::get('/flash/dashboard', [FlashDashboardController::class, 'index'])
    ->middleware(['auth', 'flash.layout'])
    ->name('flash.dashboard');

Route::middleware(['auth', 'role:owner,it', 'flash.layout'])
    ->prefix('flash')
    ->name('flash.')
    ->group(function () {
        // USERS

        Route::get('/payroll/dashboard', [FlashPayrollController::class, 'dashboard'])->name('payroll.dashboard');
        Route::get('/payroll/history', [FlashPayrollController::class, 'history'])->name('payroll.history');
    });

/*
|--------------------------------------------------------------------------
| FILES (ALL ROLES EXCEPT RESTRICTED)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:owner,it,admin,secretary', 'flash.layout'])
    ->prefix('flash')
    ->middleware('flash.layout')
    ->name('flash.')
    ->group(function () {
        // DESTINATIONS
        Route::get('/destinations', [FlashDestinationController::class, 'index'])->name('destinations.index');

        Route::post('/destinations', [FlashDestinationController::class, 'store'])->name('destinations.store');

        Route::put('/destinations/{id}', [FlashDestinationController::class, 'update'])->name('destinations.update');

        Route::delete('/destinations/{id}', [FlashDestinationController::class, 'destroy'])->name('destinations.destroy');

        // TRUCKS
        Route::delete('/trucks/delete-all', [TruckController::class, 'destroyAll'])->name('trucks.destroyAll');
        Route::resource('trucks', TruckController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::get('/trucks/sidebar/{id}', [TruckController::class, 'sidebar'])->name('trucks.sidebar');

        // DRIVERS
        Route::get('/drivers', [DriverController::class, 'index'])->name('drivers.index');
        Route::post('/drivers', [DriverController::class, 'store'])->name('drivers.store');
        Route::put('/drivers/{driver}', [DriverController::class, 'update'])->name('drivers.update');
        Route::delete('/drivers/{driver}', [DriverController::class, 'destroy'])->name('drivers.destroy');

        Route::post('/drivers/delete-multiple', [DriverController::class, 'deleteMultiple'])->name('drivers.deleteMultiple');

        Route::get('/person-docs/{id}/{type}', [DriverDocumentController::class, 'getDocs'])->name('person-docs.get');
        Route::post('/person-docs/save', [DriverDocumentController::class, 'savePersonDocs'])->name('person-docs.save');

        Route::delete('/people/bulk-delete', [DriverController::class, 'bulkDestroyPeople'])->name('people.bulkDestroy');
    });

/*
|--------------------------------------------------------------------------
| TRIPS (ADMIN CAN ACCESS)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:owner,it,admin,secretary', 'flash.layout'])
    ->prefix('flash')
    ->name('flash.')
    ->group(function () {
        Route::get('/trips', [FlashTripController::class, 'index'])->name('trips.index');
        Route::post('/trips', [FlashTripController::class, 'store'])->name('trips.store');
        Route::post('/trips/{id}/assign', [FlashTripController::class, 'assign'])->name('trips.assign');
        Route::post('/trips/{id}/dispatch', [FlashTripController::class, 'dispatch'])->name('trips.dispatch');
        Route::post('/trips/{id}/deliver', [FlashTripController::class, 'deliver'])->name('trips.deliver');
        Route::put('/trips/{id}', [FlashTripController::class, 'update'])->name('trips.update');

        Route::delete('/trips/all/delete', [FlashTripController::class, 'destroyAll'])->name('trips.destroyAll');
        Route::delete('/trips/{id}', [FlashTripController::class, 'destroy'])->name('trips.destroy');
    });

/*
|--------------------------------------------------------------------------
| BILLING + PAYROLL (ADMIN + SECRETARY + OWNER + IT)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:owner,it,admin,secretary', 'flash.layout'])
    ->prefix('flash')
    ->name('flash.')
    ->group(function () {
        Route::get('/payroll', [FlashPayrollController::class, 'index'])->name('payroll.index');
        Route::post('/payroll/pay', [FlashPayrollPaymentController::class, 'store'])->name('payroll.pay');

        Route::get('/payroll/pdf/{id}', [FlashPayrollController::class, 'downloadPDF'])->name('payroll.pdf');

        Route::delete('/payroll/delete/{id}', [FlashPayrollController::class, 'destroy'])->name('payroll.delete');

        Route::get('/billing', [FlashPayrollController::class, 'billing'])->name('payroll.billing');

        Route::post('/trips/{id}/complete', [FlashPayrollController::class, 'complete'])->name('trips.complete');

        Route::put('/trips/{id}/billing-update', [FlashPayrollController::class, 'updateBilling'])->name('trips.updateBilling');

        Route::get('/trips/history', [FlashTripController::class, 'history'])->name('trips.history');

        Route::post('/payroll/finalize', [FlashPayrollController::class, 'finalize'])->name('payroll.finalize');
    });


require __DIR__ . '/auth.php';
require __DIR__ . '/watson-routes.php';
