<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\Room_allocation;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentManagementController extends Controller
{
    // add student
    public function addStudent(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'roll_number' => 'required|string|max:10|unique:students,roll_number',
                'name' => 'required|string|max:100',
                'email' => 'required|email|max:100|unique:students,email',
                'phone_number' => 'required|string|max:15|unique:students,phone_number',
                'date_of_birth' => 'required|date',
                'address' => 'required|string',
                'password' => 'required| string'
            ]);
            

            $student = Student::create($validatedData);
    
            return response()->json([
                'message' => 'Student added successfully.',
                'data' => $student,
            ], 201);
            
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error adding student',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    

    public function allocateRoom(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'room_id' => 'required|exists:rooms,id',
                'student_roll_number' => 'required|exists:students,roll_number',
            ]);

            $room = Room::findOrFail($validatedData['room_id']);

            // Check room capacity
            if ($room->allocated >= $room->capacity) {
                return response()->json([
                    'message' => 'Room is already at full capacity.',
                ], 400);
            }

            // Check if student is already allocated
            $existingAllocation = Room_allocation::where('student_roll_number', $validatedData['student_roll_number'])->exists();
            if ($existingAllocation) {
                return response()->json([
                    'message' => 'Student is already allocated to a room.',
                ], 400);
            }

            // Create room allocation
            Room_allocation::create([
                'room_id' => $validatedData['room_id'],
                'student_roll_number' => $validatedData['student_roll_number'],
                'allocation_date' => now()->toDateString(),
            ]);

            // Update room allocation count
            $room->increment('allocated');

            return response()->json([
                'message' => 'Room allocated successfully.',
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation error.',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Room or student not found.',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error allocating room.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // View all students
    public function viewAllStudents()
    {
        try {
            $students = Student::all();

            return response()->json([
                'data' => $students,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching students.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // View student by id
    public function viewStudentById($id)
    {
        try {
            $student = Student::findOrFail($id);

            return response()->json([
                'data' => $student,
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Student not found.',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching student.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Update student by id
    public function updateStudentById(Request $request, $id)
    {
        try {
            $student = Student::findOrFail($id);

            $validatedData = $request->validate([
                'roll_number' => 'string|max:10|unique:students,roll_number,' . $student->id,
                'name' => 'string|max:100',
                'email' => 'email|max:100|unique:students,email,' . $student->id,
                'phone_number' => 'string|max:15|unique:students,phone_number,' . $student->id,
                'date_of_birth' => 'date',
                'address' => 'string',
            ]);

            $student->update($validatedData);

            return response()->json([
                'message' => 'Student updated successfully.',
                'data' => $student,
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Student not found.',
            ], 404);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation error.',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating student.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Delete student by id
    public function deleteStudentById($id)
    {
        try {
            $student = Student::findOrFail($id);

            $student->delete();

            return response()->json([
                'message' => 'Student deleted successfully.',
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Student not found.',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting student.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
