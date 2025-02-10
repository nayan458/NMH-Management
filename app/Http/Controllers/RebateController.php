<?php

namespace App\Http\Controllers;

use App\Models\Rebate;
use App\Models\Student_mess_fee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RebateController extends Controller
{
    //
    function requestRebate(Request $request)
    {
        $validatedData = $request->validate([
            'student_fees_id' => 'required|integer|exists:student_fees,id',
            'days_applied' => 'required|numeric|min:0',
        ]);

        // return $validatedData;
        try {
            $studentFee = Student_mess_fee::findOrFail($validatedData['student_fees_id']);
            // if ($studentFee->rebate_status === 'pending') {
            //     return response()->json([
            //         'message' => 'A rebate request is already pending for this student fee.',
            //     ], 400);
            // }

            $studentFee->rebate_status = 'pending';
            $studentFee->save();

            $rebate = Rebate::create([
                'student_fees_id' => $validatedData['student_fees_id'],
                'days_applied' => $validatedData['days_applied'],
                'days_approved' => 0, // default to 0 until approved
            ]);

            return response()->json([
                'message' => 'Rebate added successfully',
                'data' => $rebate,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while processing the rebate request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    function updateRebateStatus(Request $request)
    {
        $validatedData = $request->validate([
            'student_fees_id' => 'required|integer|exists:student_fees,id',
            'status' => 'required|string|in:approved,rejected',
            'days_approved' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Ensure the student fee record exists
            $studentFee = Student_mess_fee::findOrFail($validatedData['student_fees_id']);
            if($studentFee->rebate_status !== 'pending') {
                return response()->json([
                    'message' => 'Rebate status can only be updated for pending rebate requests.',
                ], 400);
            }

            // if student fee dont exist, throw an exception
            if (!$studentFee) {
                throw new \Exception('Student fee record not found.');
            }
            // if rebate dont exist, throw an exception
            
            
            // Find the rebate record, or throw an exception if not found
            $rebate = Rebate::where('student_fees_id', $validatedData['student_fees_id'])->firstOrFail();

            if (!$rebate) {
                throw new \Exception('Student fee record not found.');
            }

            // Update student fee record
            $studentFee->rebate_status = $validatedData['status'];
            $studentFee->rebate_in_days = $validatedData['days_approved'];
            $studentFee->save();

            // Update rebate record
            $rebate->days_approved = $validatedData['days_approved'];
            $rebate->save();

            // Commit the transaction if everything is successful
            DB::commit();

            return response()->json([
                'message' => 'Rebate status updated successfully',
                'rebate' => $rebate,
                'studentFee' => $studentFee,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Student fee or rebate not found.',
                'error' => $e->getMessage(),
            ], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'An error occurred while updating the rebate status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
