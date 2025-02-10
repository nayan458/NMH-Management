<?php

namespace App\Http\Controllers;

use App\Models\Room;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function createRoom(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'room_number' => 'required|string|max:10|unique:rooms,room_number',
                'capacity' => 'required|integer|min:1|max:3',
            ]);

            $validatedData['allocated'] = 0; // Default allocated students is 0

            $room = Room::create($validatedData);

            return response()->json([
                'message' => 'Room added successfully.',
                'data' => $room,
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation error.',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error adding room.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // View all rooms
    public function viewRooms()
    {
        try {
            $rooms = Room::all();

            return response()->json([
                'message' => 'Rooms fetched successfully.',
                'data' => $rooms,
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching rooms.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // View room by ID
    public function viewRoom($id)
    {
        try {
            $room = Room::findOrFail($id);

            return response()->json([
                'message' => 'Room fetched successfully.',
                'data' => $room,
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Room not found.',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error fetching room.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Update room
    public function updateRoom(Request $request, $id)
    {
        try {
            $room = Room::findOrFail($id);

            $validatedData = $request->validate([
                'room_number' => 'nullable|string|max:10|unique:rooms,room_number,' . $id,
                'capacity' => 'nullable|integer|min:1|max:3',
            ]);

            $room->update($validatedData);

            return response()->json([
                'message' => 'Room updated successfully.',
                'data' => $room,
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation error.',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Room not found.',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error updating room.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Delete room
    public function deleteRoom($id)
    {
        try {
            $room = Room::findOrFail($id);
            $room->delete();

            return response()->json([
                'message' => 'Room deleted successfully.',
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Room not found.',
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting room.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
