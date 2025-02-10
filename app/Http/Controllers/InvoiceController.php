<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Mess_fee;
use App\Models\Student_mess_fee;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    // generate invoice
    public function generateInvoice($studentRollNumber, $messFeeId)
    {
        $messFee = Mess_fee::findOrFail($messFeeId);

        $invoice = Invoice::create([
            'student_roll_number' => $studentRollNumber,
            'mess_fee_id' => $messFeeId,
            'total_amount' => $messFee->total_fee,
            'status' => 'generated',
        ]);

        return response()->json([
            'message' => 'Invoice generated successfully.',
            'data' => $invoice,
        ], 201);
    }

    // mark fee as paid
    public function markFeeAsPaid($studentRollNumber, $messFeeId)
    {
        $studentFee = Student_mess_fee::where('student_roll_number', $studentRollNumber)
            ->where('mess_fee_id', $messFeeId)
            ->firstOrFail();

        $studentFee->update([
            'status' => 'paid',
            'payment_date' => now(),
        ]);

        Invoice::where('student_roll_number', $studentRollNumber)
            ->where('mess_fee_id', $messFeeId)
            ->update(['status' => 'paid']);

        return response()->json([
            'message' => 'Fee marked as paid.',
        ]);
    }

}
