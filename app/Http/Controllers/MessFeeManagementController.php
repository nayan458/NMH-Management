<?php

namespace App\Http\Controllers;

use App\Jobs\AssignMessFeeToStudents;
use App\Models\Mess_fee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class MessFeeManagementController extends Controller
{
    // Add mess fee
    public function addMessFee(Request $request)
    {
        try {
            // Validate input data
            $validatedData = $request->validate([
                'month' => 'required|integer|min:1|max:12',
                'year' => 'required|integer|min:2000|max:' . date('Y'),
                'fee_per_day' => 'required|numeric|min:0',
                'fine_per_day' => 'required|numeric|min:0',
                'days_in_month' => 'nullable|integer|min:1|max:31',
                'due_date' => 'required|date|after_or_equal:today', // Ensure due date is not in the past
            ]);

            // Calculate the number of days in the month if not provided
            $daysInMonth = $validatedData['days_in_month'] ?? cal_days_in_month(CAL_GREGORIAN, $validatedData['month'], $validatedData['year']);
            
            // Store the number of days in the month (either from the request or calculated automatically)
            $validatedData['days_in_month'] = $daysInMonth;

            // Calculate the total fee
            $validatedData['total_fee'] = $validatedData['fee_per_day'] * $daysInMonth;

            // due_date has time of 23:59:59
            $validatedData['due_date'] = $validatedData['due_date'] . ' 23:59:59';

            // Create the new MessFee record
            $messFee = Mess_fee::create($validatedData);

            // Dispatch the job to assign the mess fee to students in the background
            AssignMessFeeToStudents::dispatchSync($messFee);

            // Return success response immediately
            return response()->json([
                'message' => "job dispatched",
                'data' => $messFee,
            ], 201);

        } catch (Throwable $e) {
            // Log the error
            Log::error('Failed to add mess fee: ' . $e->getMessage());

            // Return error response
            return response()->json([
                'message' => 'An error occurred while adding the mess fee. Please try again later.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Update mess fee
    public function updateMessFee(Request $request, $id)
    {
        try {
            // Find the existing MessFee record
            $messFee = Mess_fee::findOrFail($id);

            // Validate the input data
            $validatedData = $request->validate([
                'fee_per_day' => 'nullable|numeric|min:0', // Allow fee_per_day to be nullable
                'fine_per_day' => 'nullable|numeric|min:0', // Allow fine_per_day to be nullable
                'due_date' => 'nullable|date|after_or_equal:today', // Allow due_date to be nullable
            ]);

            // If fee_per_day is provided, update it
            if (isset($validatedData['fee_per_day'])) {
                $messFee->fee_per_day = $validatedData['fee_per_day'];

                // Recalculate the total fee based on the updated fee_per_day and days_in_month
                $messFee->total_fee = $messFee->fee_per_day * $messFee->days_in_month;
            }

            // If fine_per_day is provided, update it
            if (isset($validatedData['fine_per_day'])) {
                $messFee->fine_per_day = $validatedData['fine_per_day'];
            }

            // If due_date is provided, update it
            if (isset($validatedData['due_date'])) {
                $messFee->due_date = $validatedData['due_date'];
            }

            // Save the changes
            $messFee->save();

            // Return the updated record
            return response()->json([
                'message' => 'Mess fee updated successfully.',
                'data' => $messFee,
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            // Return error response if record is not found
            return response()->json([
                'message' => 'Mess fee not found.',
            ], 404);

        } catch (Throwable $e) {
            // Log the error
            Log::error('Failed to update mess fee: ' . $e->getMessage());

            // Return error response
            return response()->json([
                'message' => 'An error occurred while updating the mess fee. Please try again later.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
}
