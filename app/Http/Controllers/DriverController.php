<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use App\Models\Driver;
use App\Models\Helper;
use Illuminate\Support\Facades\Storage;

class DriverController extends Controller
{
    public function deleteMultiple(Request $request)
    {
        $ids = $request->ids ?? [];

        if (!empty($ids)) {
            Driver::whereIn('id', $ids)->delete();
        }

        return back()->with('success', 'Selected drivers deleted.');
    }

    private function isFlash()
    {
        return request()->routeIs('flash.*');
    }

    private function isWatson()
    {
        return request()->routeIs('watson.*');
    }

    private function companyPrefix()
    {
        if ($this->isWatson()) {
            return 'watson';
        }
        if ($this->isFlash()) {
            return 'flash';
        }
        return 'owner';
    }

    private function routeName($name)
    {
        return "{$this->companyPrefix()}.$name";
    }

    private function viewName($name)
    {
        return "{$this->companyPrefix()}.$name";
    }

    public function index()
    {
        $drivers = Driver::orderBy('name')->get();
        $helpers = Helper::orderBy('name')->get();

        // If later you add availability_status column, this will auto-detect it.
        $driverHasAvail = Schema::hasColumn((new Driver())->getTable(), 'availability_status');
        $helperHasAvail = Schema::hasColumn((new Helper())->getTable(), 'availability_status');

        // ===== DRIVER GROUPS =====
        $availableDrivers = $drivers->filter(function ($d) use ($driverHasAvail) {
            if ($driverHasAvail) {
                return ($d->status ?? 'active') === 'active' && ($d->availability_status ?? 'available') === 'available';
            }
            return ($d->status ?? '') === 'active';
        });

        $onTripDrivers = $drivers
            ->filter(function ($d) use ($driverHasAvail) {
                if ($driverHasAvail) {
                    return ($d->status ?? 'active') === 'active' && ($d->availability_status ?? '') === 'on_trip';
                }
                return ($d->status ?? '') === 'on_trip';
            })
            ->values();

        $onLeaveDrivers = $drivers
            ->filter(function ($d) use ($driverHasAvail) {
                if ($driverHasAvail) {
                    return ($d->status ?? 'active') === 'on-leave' || (($d->status ?? 'active') === 'active' && ($d->availability_status ?? '') === 'on_leave');
                }
                return ($d->status ?? '') === 'on_leave';
            })
            ->values();

        $inactiveDrivers = $drivers
            ->filter(function ($d) {
                return ($d->status ?? '') === 'inactive';
            })
            ->values();

        $availableHelpers = $helpers
            ->filter(function ($h) use ($helperHasAvail) {
                if ($helperHasAvail) {
                    return ($h->status ?? 'active') === 'active' && ($h->availability_status ?? 'available') === 'available';
                }
                return in_array($h->status ?? 'active', ['active', 'available'], true);
            })
            ->values();

        $onTripHelpers = $helpers
            ->filter(function ($h) use ($helperHasAvail) {
                if ($helperHasAvail) {
                    return ($h->status ?? 'active') === 'active' && ($h->availability_status ?? '') === 'on_trip';
                }
                return ($h->status ?? '') === 'on_trip';
            })
            ->values();

        $onLeaveHelpers = $helpers
            ->filter(function ($h) use ($helperHasAvail) {
                if ($helperHasAvail) {
                    return ($h->status ?? 'active') === 'on-leave' || (($h->status ?? 'active') === 'active' && ($h->availability_status ?? '') === 'on_leave');
                }
                return ($h->status ?? '') === 'on_leave';
            })
            ->values();

        $inactiveHelpers = $helpers
            ->filter(function ($h) {
                return ($h->status ?? '') === 'inactive';
            })
            ->values();

        $stats = [
            'total_drivers' => $drivers->count(),
            'driver_avail' => $availableDrivers->count(),

            'total_helpers' => $helpers->count(),
            'helper_avail' => $availableHelpers->count(),

            'ontrip_driver' => $onTripDrivers->count(),
            'ontrip_helper' => $onTripHelpers->count(),

            'on_leave' => $onLeaveDrivers->count() + $onLeaveHelpers->count(),
            'inactive' => $inactiveDrivers->count() + $inactiveHelpers->count(),
        ];

        $layout = session('layout', 'owner'); // default owner

        $view = match ($layout) {
            'flash' => 'flash.drivers.index',
            'watson' => 'watson.drivers.index',
            default => 'owner.drivers.index',
        };

        return view($view, compact('drivers', 'helpers', 'stats', 'availableDrivers', 'availableHelpers', 'onTripDrivers', 'onTripHelpers', 'onLeaveDrivers', 'onLeaveHelpers', 'inactiveDrivers', 'inactiveHelpers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'status' => 'required|string',
            'birthday' => 'nullable|date',
            'contact_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:1000',
            'emergency_contact_person' => 'nullable|string|max:255',
            'emergency_contact_number' => 'nullable|string|max:50',
        ]);

        $status = $request->status ?? 'active';

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'status' => $request->status,
            'birthday' => $request->birthday,
            'contact_number' => $request->contact_number,
            'address' => $request->address,
            'emergency_contact_person' => $request->emergency_contact_person,
            'emergency_contact_number' => $request->emergency_contact_number,
            'profile_photo' => $path ?? null,
        ];

        if ($status === 'inactive') {
            $data['availability_status'] = 'unavailable';
        } elseif ($status === 'on-leave') {
            $data['availability_status'] = 'on_leave';
        } else {
            $data['availability_status'] = $request->availability_status ?? 'available';
        }

        if ($request->hasFile('profile_photo')) {
            $data['profile_photo'] = $request->file('profile_photo')->store('drivers', 'public');
        }

        if ($request->has('availability_status') && Schema::hasColumn((new Driver())->getTable(), 'availability_status')) {
            $data['availability_status'] = $request->availability_status;
        }

        Driver::create($data);

        return back()->with('success', 'Driver added.');
    }

    public function update(Request $request, Driver $driver)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255|unique:drivers,email,' . $driver->id . ',id',
            'status' => 'nullable|in:active,inactive,on-leave',
            'profile_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $status = $request->status ?? $driver->status;

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'status' => $request->status,
            'birthday' => $request->birthday,
            'contact_number' => $request->contact_number,
            'address' => $request->address,
            'emergency_contact_person' => $request->emergency_contact_person,
            'emergency_contact_number' => $request->emergency_contact_number,
        ];

        // 🔥 AUTO FORCE AVAILABILITY
        if ($status === 'inactive') {
            $data['availability_status'] = 'unavailable';
        } elseif ($status === 'on-leave') {
            $data['availability_status'] = 'on_leave';
        } else {
            $data['availability_status'] = $driver->availability_status;
        }

        // REMOVE PHOTO (checkbox)
        // REMOVE PHOTO
        if ($request->boolean('remove_photo') && $driver->profile_photo) {
            if (Storage::disk('public')->exists($driver->profile_photo)) {
                Storage::disk('public')->delete($driver->profile_photo);
            }
            $data['profile_photo'] = null;
        }

        // UPLOAD NEW PHOTO (overrides remove if both present)
        if ($request->hasFile('profile_photo')) {
            if ($driver->profile_photo && Storage::disk('public')->exists($driver->profile_photo)) {
                Storage::disk('public')->delete($driver->profile_photo);
            }

            $data['profile_photo'] = $request->file('profile_photo')->store('drivers', 'public');
        }

        $driver->update($data);

        return back()->with('success', 'Driver updated.');
    }

    public function destroy(Driver $driver)
    {
        $driver->delete();
        return back()->with('success', 'Driver deleted.');
    }

    public function bulkDestroyPeople(Request $request)
    {
        $driverIds = collect(explode(',', (string) $request->driver_ids))->filter()->map(fn($id) => (int) $id)->values()->all();

        $helperIds = collect(explode(',', (string) $request->helper_ids))->filter()->map(fn($id) => (int) $id)->values()->all();

        if (empty($driverIds) && empty($helperIds)) {
            return back()->with('error', 'No records selected.');
        }

        if (!empty($driverIds)) {
            Driver::whereIn('id', $driverIds)->delete();
        }

        if (!empty($helperIds)) {
            Helper::whereIn('id', $helperIds)->delete();
        }

        return back()->with('success', 'Selected records deleted.');
    }
}
