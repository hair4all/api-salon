<?php

namespace App\Http\Controllers;

use App\Models\Province;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        try {
            $provinces = Province::query()->where('is_deleted', 0);
            if ($request->has('name')) {
                $provinces->where('name', 'like', '%' . $request->query('name') . '%');
            }
            return response()->json($provinces->paginate($request->query('limit') ?? 10));
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
                'code' => 'required|integer|exists:countries,id',
            ]);
            $province = Province::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Province created successfully',
                'data' => $province,
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
            $province = Province::findOrFail($id);
            return response()->json([
                'status' => true,
                'message' => 'Province retrieved successfully',
                'data' => $province,
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
                'code' => 'required|integer|exists:countries,id',
            ]);
            $province = Province::findOrFail($id);
            $province->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Province updated successfully',
                'data' => $province,
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
            $province = Province::findOrFail($id);
            $province->update(['is_deleted' => 1]);
            return response()->json([
                'status' => true,
                'message' => 'Province deleted successfully',
                'data' => $province,
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
