<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeductionController extends Controller
{
    /**
     * Optional standalone page if gusto mo hiwalay,
     * pero pwede rin gamitin lang sa payroll page include.
     */
    public function index(Request $request)
    {
        $month = $request->get('month');
        $year = $request->get('year');

        $query = DB::table('deductions')->orderByDesc('date_paid');

        if ($month && $year) {
            $query->whereMonth('date_paid', $month)->whereYear('date_paid', $year);
        }

        $deductions = $query->get();

        return view('owner.deductions.index', compact('deductions'));
    }

    /**
     * Save new deduction
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'plate_number' => 'required|string|max:50',
            'deduction_type' => 'required|in:sss,pagibig,philhealth',
            'date_paid' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'receipt_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'remarks' => 'nullable|string|max:255',
        ]);

        try {
            $receiptPath = null;

            if ($request->hasFile('receipt_image')) {
                $receiptPath = $request->file('receipt_image')->store('deductions_receipts', 'public');
            }

            DB::table('deductions')->insert([
                'plate_number' => $validated['plate_number'],
                'deduction_type' => $validated['deduction_type'],
                'date_paid' => $validated['date_paid'],
                'amount' => $validated['amount'],
                'receipt_image' => $receiptPath,
                'remarks' => $validated['remarks'] ?? null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Deduction added successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Failed to save deduction: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Update existing deduction
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'id' => 'required|integer|exists:deductions,id',
            'plate_number' => 'required|string|max:50',
            'deduction_type' => 'required|in:sss,pagibig,philhealth',
            'date_paid' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'receipt_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'remarks' => 'nullable|string|max:255',
        ]);

        try {
            $deduction = DB::table('deductions')->where('id', $validated['id'])->first();

            if (!$deduction) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Deduction not found.',
                    ],
                    404,
                );
            }

            $receiptPath = $deduction->receipt_image;

            if ($request->hasFile('receipt_image')) {
                // Delete old receipt if exists
                if ($receiptPath && Storage::disk('public')->exists($receiptPath)) {
                    Storage::disk('public')->delete($receiptPath);
                }

                $receiptPath = $request->file('receipt_image')->store('deductions_receipts', 'public');
            }

            DB::table('deductions')
                ->where('id', $validated['id'])
                ->update([
                    'plate_number' => $validated['plate_number'],
                    'deduction_type' => $validated['deduction_type'],
                    'date_paid' => $validated['date_paid'],
                    'amount' => $validated['amount'],
                    'receipt_image' => $receiptPath,
                    'remarks' => $validated['remarks'] ?? null,
                    'updated_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'message' => 'Deduction updated successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Failed to update deduction: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Delete deduction
     */
    public function destroy($id)
    {
        try {
            $deduction = DB::table('deductions')->where('id', $id)->first();

            if (!$deduction) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Deduction not found.',
                    ],
                    404,
                );
            }

            // Delete receipt image
            if ($deduction->receipt_image && Storage::disk('public')->exists($deduction->receipt_image)) {
                Storage::disk('public')->delete($deduction->receipt_image);
            }

            DB::table('deductions')->where('id', $id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Deduction deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Failed to delete deduction: ' . $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Optional:
     * Fetch by plate number (helpful if gusto mo dynamic row display)
     */
    public function byPlate($plateNumber)
    {
        $deductions = DB::table('deductions')->where('plate_number', $plateNumber)->orderByDesc('date_paid')->get();

        return view('owner.payroll.expenses', compact('expenses', 'credits', 'trucks', 'deductions', 'totalCredit', 'totalDebit', 'avgKmPerLiter'));
    }
}
