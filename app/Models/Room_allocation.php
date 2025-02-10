<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room_allocation extends Model
{
    use HasFactory;

    protected $table = 'room_allocations';

    protected $fillable = [
        'room_id',
        'student_roll_number'
    ];


}
