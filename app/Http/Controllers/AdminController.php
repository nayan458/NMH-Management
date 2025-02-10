<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Student;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // Add room
    // function createRoom(Request $request) {
    //     try {
    //         $data = $request->validate([
    //             'room_number' => 'required',
    //             'capacity' => 'required'
    //         ]);

    //         $room = Room::create($data);
    //         return $room;
    //     } catch (\Throwable $th) {
    //         return $th;
    //     }
    // }
    // // Add student
    // function addStudent(Request $request) {
    //     try {
    //         $data = $request->validate([
    //             'roll_number' => 'required',
    //             'name' => 'required',
    //             'email' => 'required',
    //             'phone_number' => 'required',
    //             'date_of_birth' => 'required',
    //             'address' => 'required'
    //         ]);

    //         $student = Student::create($data);
    //         return $student;
    //     } catch (\Throwable $th) {
    //         return $th;
    //     }
    // }
    // // view student join room
    // function allocateRoom(Request $request) {
    //     try {
    //         $data = $request->validate([
    //             'room_id' => 'required',
    //             'student_id' => 'required'
    //         ]);

    //         $room = Room::find($data['room_id']);
    //         $student = Student::find($data['student_id']);

    //         $room->students()->attach($student);
    //         return $room;
    //     } catch (\Throwable $th) {
    //         return $th;
    //     }
    // }
    // // update mess fee
    
}
