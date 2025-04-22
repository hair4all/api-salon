<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        try {
            $branches = Branch::query()->where('is_deleted', 0);
            if ($request->has('name')) {
                $branches->where('name', 'like', '%' . $request->query('name') . '%');
            }
            return response()->json($branches->paginate($request->query('limit') ?? 10));
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
                'image' => 'nullable|string',
                'address' => 'required|string|max:500',
                'phone' => 'required|string|max:15',
                'email' => 'required|email|max:255|unique:branches,email',
                'status' => 'required|boolean',
                'manager_id' => 'required|integer|exists:members,id',
            ]);
            $branch = Branch::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Branch created successfully',
                'data' => $branch,
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
            $branch = Branch::query()->where('is_deleted', 0)->findOrFail($id);
            return response()->json([
                'status' => true,
                'message' => 'Branch retrieved successfully',
                'data' => $branch,
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
                'name' => 'nullable|string|max:255',
                'image' => 'nullable|string',
                'address' => 'nullable|string|max:500',
                'phone' => 'nullable|string|max:15',
                'email' => 'nullable|email|max:255|unique:branches,email,' . $id,
                'status' => 'nullable|boolean',
                'manager_id' => 'nullable|integer|exists:members,id',
            ]);
            $branch = Branch::findOrFail($id);
            $branch->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Branch updated successfully',
                'data' => $branch,
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
            $branch = Branch::findOrFail($id);
            $branch->update(['is_deleted' => 1]);
            return response()->json([
                'status' => true,
                'message' => 'Branch deleted successfully',
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
