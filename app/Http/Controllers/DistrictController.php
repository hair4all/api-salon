<?php

namespace App\Http\Controllers;

use App\Models\District;
use Illuminate\Http\Request;

class DistrictController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        try {
            $districts = District::query()->where('is_deleted', 0);
            if ($request->has('name')) {
                $districts->where('name', 'like', '%' . $request->query('name') . '%');
            }
            return response()->json($districts->paginate($request->query('limit') ?? 10));
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
                'name' => 'required|string|max:255',
                'code' => 'required|integer|exists:cities,id',
            ]);
            $district = District::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'District created successfully',
                'data' => $district,
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
            $district = District::findOrFail($id);
            return response()->json([
                'status' => true,
                'message' => 'District retrieved successfully',
                'data' => $district,
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
                'name' => 'required|string|max:255',
                'code' => 'required|integer|exists:cities,id',
            ]);
            $district = District::findOrFail($id);
            $district->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'District updated successfully',
                'data' => $district,
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
            $district = District::findOrFail($id);
            $district->update(['is_deleted' => 1]);
            return response()->json([
                'status' => true,
                'message' => 'District deleted successfully',
                'data' => $district,
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
