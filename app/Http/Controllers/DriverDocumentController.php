<?php

namespace App\Http\Controllers;

use App\Models\DriverDocument;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\PersonDocument;

class DriverDocumentController extends Controller
{
    public function getDocs($id, $type)
    {
        return response()->json(PersonDocument::where('person_id', $id)->where('person_type', $type)->get());
    }

    public function savePersonDocs(Request $request)
    {
        $personId = $request->person_id;
        $personType = $request->person_type;

        $expiries = $request->input('expiry', []);
        $files = $request->file('file', []);
        $deleteExpiries = $request->input('delete_expiry', []);
        $deleteFiles = $request->input('delete_file', []);

        // 🔥 IMPORTANT: collect all types
        $types = array_unique(array_merge(array_keys($expiries), array_keys($files), array_keys($deleteExpiries), array_keys($deleteFiles)));

        foreach ($types as $docType) {
            $expiry = $expiries[$docType] ?? null;
            $file = $files[$docType] ?? null;
            $deleteExpiry = isset($deleteExpiries[$docType]);
            $deleteFile = isset($deleteFiles[$docType]);

            $doc = PersonDocument::firstOrNew([
                'person_id' => $personId,
                'person_type' => $personType,
                'type' => $docType,
            ]);

            if ($deleteExpiry) {
                $doc->expiry_date = null;
            } elseif ($expiry) {
                $doc->expiry_date = $expiry;
            }

            if ($deleteFile) {
                if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
                    Storage::disk('public')->delete($doc->file_path);
                }
                $doc->file_path = null;
            }

            if ($file) {
                if ($doc->file_path && Storage::disk('public')->exists($doc->file_path)) {
                    Storage::disk('public')->delete($doc->file_path);
                }

                $path = $file->store("documents/persons/$personId", 'public');
                $doc->file_path = $path;
            }

            if ($doc->expiry_date || $doc->file_path) {
                $doc->save();
            } elseif ($doc->exists) {
                $doc->delete();
            }
        }
        return back()->with('success', 'Documents updated successfully');
    }
}
