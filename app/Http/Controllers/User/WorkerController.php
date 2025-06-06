<?php

namespace App\Http\Controllers\User;

use App\Models\Worker;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WorkerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        try {
            $workers = Worker::query()->where('is_deleted', 0);
            if ($request->has('name')) {
                $workers->where('name', 'like', '%' . $request->query('name') . '%');
            }
            return response()->json($workers->paginate($request->query('limit') ?? 10));
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => $th->getMessage(),
            ], 500);
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
                'member_id' => 'required|integer',
                'branch_id' => 'required|integer',
                'name' => 'required|string|max:255',
                'phone' => 'required|string|max:15',
                'email' => 'required|email|max:255|unique:workers,email',
                'address' => 'nullable|string|max:500',
                'position_id' => 'required|integer',
                'status' => 'required|boolean',
                'salary' => 'required|numeric|min:0',
            ]);
            $worker = Worker::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Worker created successfully',
                'data' => $worker,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //

        try {
            $worker = Worker::query()->where('is_deleted', 0)->findOrFail($id);
            return response()->json([
                'status' => true,
                'message' => 'Worker retrieved successfully',
                'data' => $worker,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //

        try {
            $request->validate([
                'member_id' => 'nullable|integer',
                'branch_id' => 'nullable|integer',
                'name' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:15',
                'email' => 'nullable|email|max:255|unique:workers,email,' . $id,
                'address' => 'nullable|string|max:500',
                'position_id' => 'nullable|integer',
                'status' => 'nullable|boolean',
                'salary' => 'nullable|numeric|min:0',
            ]);
            $worker = Worker::findOrFail($id);
            $worker->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Worker updated successfully',
                'data' => $worker,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //

        try {
            $worker = Worker::findOrFail($id);
            $worker->update(['is_deleted' => 1]);
            return response()->json([
                'status' => true,
                'message' => 'Worker deleted successfully',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => $th->getMessage(),
            ], 500);
        }
    }
}
