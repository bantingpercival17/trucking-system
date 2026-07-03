<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Truck;
use App\Models\TruckDocument;
use App\Models\CompanyDocument;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use App\Models\PersonalVehicle;

class MaintenanceController extends Controller
{
    public function index()
    {
        $today = Carbon::now()->startOfDay();

        // =========================
        // TRUCK DOCUMENTS
        // =========================

        $expiringDocs = [];
        $expiredDocs = [];

        $today = Carbon::now()->startOfDay();

        // =========================
        // GROUP PER TRUCK
        // =========================
        $trucks = Truck::with(['documents'])
            ->get()
            ->map(function ($truck) use ($today, &$expiringDocs, &$expiredDocs) {
                $types = ['ORCR', 'INSURANCE', 'LTFRB', 'PMS'];

                foreach ($types as $type) {
                    $doc = $truck->documents->where('type', $type)->first();

                    if ($doc && $doc->expiry_date) {
                        $expiry = Carbon::parse($doc->expiry_date)->startOfDay();
                        $reminder = $doc->reminder_days ?? 30;

                        if ($expiry->lt($today)) {
                            $doc->status = 'EXPIRED';
                            $doc->days_left = 0;
                            $expiredDocs[] = $doc;
                        } else {
                            $days = $today->diffInDays($expiry);
                            $doc->days_left = $days;

                            if ($days <= $reminder) {
                                $doc->status = 'EXPIRING';
                                $expiringDocs[] = $doc;
                            } else {
                                $doc->status = 'ACTIVE';
                            }
                        }
                    }

                    $truck->{strtolower($type)} = $doc;
                }

                return $truck;
            });

        // =========================
        // PERSONAL VEHICLES
        // =========================
        $personalVehicles = PersonalVehicle::with(['documents'])
            ->get()
            ->map(function ($car) use ($today) {
                foreach (['ORCR', 'INSURANCE', 'PMS'] as $type) {
                    $doc = $car->documents->where('type', $type)->first();

                    if ($doc && $doc->expiry_date) {
                        $expiry = Carbon::parse($doc->expiry_date)->startOfDay();
                        $reminder = $doc->reminder_days ?? 30;

                        if ($expiry->lt($today)) {
                            $doc->status = 'EXPIRED';
                        } else {
                            $days = $today->diffInDays($expiry);

                            if ($days <= $reminder) {
                                $doc->status = 'EXPIRING';
                            } else {
                                $doc->status = 'ACTIVE';
                            }
                        }
                    }

                    $car->{strtolower($type)} = $doc;
                }

                $car->ltfrb = null;

                return $car;
            });

        // =========================
        // COMPANY DOCUMENTS
        // =========================
        $companyDocsRaw = CompanyDocument::all();

        $companyDocs = [];

        foreach ($companyDocsRaw as $doc) {
            if (!$doc->expiry_date) {
                $doc->status = 'NO_DATE';
                continue;
            }

            $expiry = Carbon::parse($doc->expiry_date);

            if ($expiry->isPast()) {
                $doc->status = 'EXPIRED';
                $doc->days_left = 0;
            } else {
                $days = $today->diffInDays($expiry);
                $doc->days_left = $days;

                if ($days <= $doc->reminder_days) {
                    $doc->status = 'EXPIRING';
                } else {
                    $doc->status = 'ACTIVE';
                }
            }

            $companyDocs[$doc->type] = $doc;
        }

        // =========================
        // COUNTS (KPI)
        // =========================
        $totalTrucks = $trucks->count();
        $expiringCount = count($expiringDocs);
        $expiredCount = count($expiredDocs);

        // PMS (simple count)
        $pmsDueCount = collect($trucks)->map(fn($truck) => $truck->pms)->filter(fn($doc) => $doc && $doc->status !== 'ACTIVE')->count();
        // =========================
        return view('owner.reports.maintenance', compact('trucks', 'personalVehicles', 'expiringDocs', 'expiredDocs', 'companyDocs', 'totalTrucks', 'expiringCount', 'expiredCount', 'pmsDueCount'));
    }

    public function storePersonalVehicle(Request $request)
    {
        $request->validate([
            'plate_number' => 'required|string|max:255|unique:personal_vehicles,plate_number',
            'vehicle_name' => 'nullable|string|max:255',
        ]);

        PersonalVehicle::create([
            'plate_number' => strtoupper($request->plate_number),
            'vehicle_name' => $request->vehicle_name,
        ]);

        return back()->with('success', 'Personal vehicle added successfully');
    }

    public function save(Request $request)
    {
        $truckId = $request->truck_id;

        $types = ['ORCR', 'INSURANCE', 'LTFRB', 'PMS'];

        foreach ($types as $type) {
            $expiry = $request->input($type . '_expiry');
            $file = $request->file($type . '_file');

            $deleteExpiry = $request->has('delete_' . $type . '_expiry');
            $deleteFile = $request->has('delete_' . $type . '_file');

            $doc = TruckDocument::firstOrNew([
                'truck_id' => $truckId,
                'type' => $type,
            ]);

            /**
             * ✅ 1. DELETE EXPIRY
             */
            if ($deleteExpiry) {
                $doc->expiry_date = null;
            } elseif ($expiry) {
                $doc->expiry_date = $expiry;
            }

            /**
             * ✅ 2. DELETE FILE
             */
            if ($deleteFile) {
                if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
                    Storage::disk('public')->delete($doc->file_path);
                }

                $doc->file_path = null;
                $doc->file_name = null;
                $doc->file_type = null;
                $doc->file_size = null;
            }

            /**
             * ✅ 3. UPLOAD FILE (OVERRIDE DELETE if both present)
             */
            if ($file) {
                // delete old file first
                if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
                    Storage::disk('public')->delete($doc->file_path);
                }

                $path = $file->store("documents/trucks/$truckId", 'public');

                $doc->file_path = $path;
                $doc->file_name = $file->getClientOriginalName();
                $doc->file_type = $file->getClientOriginalExtension();
                $doc->file_size = $file->getSize();
            }

            /**
             * ✅ Save only if may laman (optional optimization)
             */
            if ($doc->expiry_date || $doc->file_path) {
                $doc->save();
            } else {
                // optional: delete record if empty
                if ($doc->exists) {
                    $doc->delete();
                }
            }
        }

        return back()->with('success', 'Documents updated successfully');
    }

    public function deletePersonalVehicle($id)
    {
        $vehicle = PersonalVehicle::findOrFail($id);

        // delete related files + docs
        $docs = TruckDocument::where('personal_vehicle_id', $id)->get();

        foreach ($docs as $doc) {
            if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
                Storage::disk('public')->delete($doc->file_path);
            }

            $doc->delete();
        }

        $vehicle->delete();

        return back()->with('success', 'Personal vehicle deleted successfully');
    }

    public function saveCompanyDoc(Request $request)
    {
        $doc = CompanyDocument::firstOrNew([
            'type' => $request->type,
        ]);

        $deleteExpiry = $request->has('delete_expiry');
        $deleteFile = $request->has('delete_file');
        $file = $request->file('file');

        /**
         * ✅ 1. HANDLE EXPIRY
         */
        if ($deleteExpiry) {
            $doc->expiry_date = null;
        } elseif ($request->expiry_date) {
            $doc->expiry_date = $request->expiry_date;
        }

        /**
         * ✅ 2. HANDLE FILE DELETE
         */
        if ($deleteFile) {
            if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
                Storage::disk('public')->delete($doc->file_path);
            }

            $doc->file_path = null;
            $doc->file_name = null;
            $doc->file_type = null;
            $doc->file_size = null;
        }

        /**
         * ✅ 3. HANDLE UPLOAD (override delete if both present)
         */
        if ($file) {
            if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
                Storage::disk('public')->delete($doc->file_path);
            }

            $path = $file->store('documents/company', 'public');

            $doc->file_path = $path;
            $doc->file_name = $file->getClientOriginalName();
            $doc->file_type = $file->getClientOriginalExtension();
            $doc->file_size = $file->getSize();
        }

        /**
         * ✅ OPTIONAL CLEANUP
         */
        if (!$doc->expiry_date && !$doc->file_path) {
            if ($doc->exists) {
                $doc->delete();
            }
        } else {
            $doc->save();
        }

        return back()->with('success', 'Company document updated');
    }

    public function savePersonalVehicle(Request $request)
    {
        $vehicleId = $request->personal_vehicle_id;

        $types = ['ORCR', 'INSURANCE', 'PMS'];

        foreach ($types as $type) {
            $expiry = $request->input($type . '_expiry');
            $file = $request->file($type . '_file');

            $deleteExpiry = $request->has('delete_' . $type . '_expiry');
            $deleteFile = $request->has('delete_' . $type . '_file');

            $doc = TruckDocument::firstOrNew([
                'personal_vehicle_id' => $vehicleId,
                'type' => $type,
            ]);

            // DELETE EXPIRY
            if ($deleteExpiry) {
                $doc->expiry_date = null;
            } elseif ($expiry) {
                $doc->expiry_date = $expiry;
            }

            // DELETE FILE
            if ($deleteFile) {
                if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
                    Storage::disk('public')->delete($doc->file_path);
                }

                $doc->file_path = null;
                $doc->file_name = null;
                $doc->file_type = null;
                $doc->file_size = null;
            }

            // UPLOAD FILE
            if ($file) {
                if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
                    Storage::disk('public')->delete($doc->file_path);
                }

                $path = $file->store("documents/personal/$vehicleId", 'public');

                $doc->file_path = $path;
                $doc->file_name = $file->getClientOriginalName();
                $doc->file_type = $file->getClientOriginalExtension();
                $doc->file_size = $file->getSize();
            }

            if ($doc->expiry_date || $doc->file_path) {
                $doc->save();
            } else {
                if ($doc->exists) {
                    $doc->delete();
                }
            }
        }

        return back()->with('success', 'Personal vehicle documents updated successfully');
    }
}
