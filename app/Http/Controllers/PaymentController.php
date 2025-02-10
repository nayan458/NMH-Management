<?php

namespace App\Http\Controllers;

use App\Models\Mess_fee;
use App\Models\Student_mess_fee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Razorpay\Api\Api;

class PaymentController extends Controller
{
    //
    private $razorpay;

    public function __construct()
    {
        $this->razorpay = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
    }

    public function createOrder(Request $request)
    {
        $data = $request->validate([
            'id' => 'required',
        ]);

        // Lock the row for update to prevent concurrent modifications
        $studentMessFee = Student_mess_fee::where('id', $data['id'])->lockForUpdate()->firstOrFail();

        // check fine
        if($studentMessFee->status == 'pending'){
            $mess_fee = Mess_fee::where('id', $studentMessFee->mess_fee_id)->first();
            $dueDate = $mess_fee->due_date;
            $daysOverdue = ceil(Carbon::parse($dueDate)->diffInDays(Carbon::now()));
            $fineAmount = $daysOverdue * $mess_fee->fine_per_day;
            if($studentMessFee->rebate_status == 'approved'){
                $rebateAmount = $mess_fee->fee_per_day * $studentMessFee->rebate_in_days;
            }else{
                $rebateAmount = 0;
            }
        }
        
        
        // return $fineAmount;
        $amount = ($studentMessFee->total_fee + $fineAmount - $rebateAmount) * 100;
        $order = $this->razorpay->order->create([
            'amount' => $amount,
            'currency' => 'INR',
            'receipt' => 'order_' . time(),
        ]);

        $studentMessFee->update([
            'razorpay_order_id' => $order->id,
        ]);

        return response()->json([
            'order_id' => $order->id,
            'amount' => $amount,
        ]);
    }

    public function handlePayment(Request $request)
    {
        $request->validate([
            'razorpay_payment_id' => 'required',
            'razorpay_order_id' => 'required',
            'razorpay_signature' => 'required',
        ]);

        // Lock the row to prevent concurrent updates
        $order = Student_mess_fee::where('razorpay_order_id', $request->razorpay_order_id)
            ->lockForUpdate()
            ->firstOrFail();

        $attributes = [
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_order_id' => $request->razorpay_order_id,
            'razorpay_signature' => $request->razorpay_signature,
        ];

        try {
            $this->razorpay->utility->verifyPaymentSignature($attributes);

            $order->update([
                'razorpay_payment_id' => $request->razorpay_payment_id,
                'razorpay_signature' => $request->razorpay_signature,
                'status' => 'paid',
            ]);

            return response()->json(['message' => 'Payment successful']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Payment verification failed'], 400);
        }
    }
}
