<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student_mess_fee extends Model
{
    use HasFactory;

    protected $table = 'student_fees';


    protected $fillable = [
        'mess_fee_id',
        'student_roll_number',
        'status',
        'total_fee',

        'rebate_in_days',
        'rebate_status',

        'razorpay_order_id',
        'razorpay_payment_id',
        'razorpay_signature',

        'payment_date',
    ];

    public function mess_fee()
    {
        return $this->belongsTo(Mess_fee::class, 'mess_fee_id', 'id');
    }

    public function rebate()
    {
        return $this->hasOne(Rebate::class, 'student_fees_id', 'id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_roll_number', 'roll_number');
    }

}
