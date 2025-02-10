<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rebate extends Model
{
    
    //
    protected $primaryKey = 'student_fees_id';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $table = 'rebates';

    protected $fillable = [
        'student_fees_id',
        'days_applied',
        'days_approved',
    ];

    public function student_mess_fee()
    {
        return $this->belongsTo(Student_mess_fee::class, 'student_fees_id', 'id');
    }

    public function mess_fee()
    {
        return $this->belongsTo(Mess_fee::class, 'mess_fee_id', 'id');
    }
}
