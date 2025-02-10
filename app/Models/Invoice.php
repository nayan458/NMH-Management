<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoices';

    protected $primaryKey = 'mess_fee_id';

    protected $fillable = [
        'mess_fee_id',
        'student_roll_number',
        'invoice_date',
        'total_amount',
        'status'
    ];
}
