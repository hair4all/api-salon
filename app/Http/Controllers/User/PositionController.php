<?php

namespace App\Http\Controllers\User;

use App\Models\Position;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PositionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        try {
            $positions = Position::query()->where('is_deleted', 0);
            if ($request->has('name')) {
                $positions->where('name', 'like', '%' . $request->query('name') . '%');
            }
            return response()->json($positions->paginate($request->query('limit') ?? 10));
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => $th->getMessage(),
            ]);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);
            $position = Position::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Position created successfully',
                'data' => $position,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => $th->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show( $id)
    {
        //
        try {
            $position = Position::findOrFail($id);
            return response()->json([
                'status' => true,
                'message' => 'Position retrieved successfully',
                'data' => $position,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => $th->getMessage(),
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $id)
    {
        //
        try {
            $request->validate([
                'name' => 'required|string|max:255',
            ]);
            $position = Position::findOrFail($id);
            $position->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Position updated successfully',
                'data' => $position,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => $th->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $id)
    {
        //
        try {
            $position = Position::findOrFail($id);
            $position->update(['is_deleted' => 1]);
            return response()->json([
                'status' => true,
                'message' => 'Position deleted successfully',
                'data' => null,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => $th->getMessage(),
            ]);
        }
    }
}
