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
                'name' => 'nullable|string|max:255',
                'image' => 'nullable',
                'address' => 'nullable|string|max:500',
                'phone' => 'nullable|string|max:15',
                'email' => 'nullable|email|max:255|unique:branches,email',
                'status' => 'nullable|boolean',
                'manager_id' => 'nullable|integer|exists:members,id',
            ]);
            $data = $request->all();
            $data['branch_code'] = 'BR-' . strtoupper(uniqid());
            $data['cash'] = 0;
            $branch = Branch::create($data);
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
                'name' => 'nullable|string|max:255',
                'image' => 'nullable',
                'address' => 'nullable|string|max:500',
                'phone' => 'nullable|string|max:15',
                'email' => 'nullable|email|max:255|unique:branches,email,' . $id,
                'status' => 'nullable|boolean',
                'manager_id' => 'nullable|integer|exists:members,id',
            ]);
            $branch = Branch::findOrFail($id);
            if($branch->is_deleted) {
                return response()->json([
                    'status' => false,
                    'message' => 'Branch not found',
                ], 404);
            }
            

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
            ], 500);
        }
    }
}
