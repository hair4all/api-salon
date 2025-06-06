<?php

namespace App\Http\Controllers\Product;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        try {
            $categories = Category::query()->where('is_deleted', 0);
            if ($request->has('name')) {
                $categories->where('name', 'like', '%' . $request->query('name') . '%');
            }
            return response()->json(
                $categories->paginate($request->query('limit') ?? 10)
            );
        } catch (\Throwable $th) {
            //throw $th;
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
                'name' => 'required|string',
                // 'description' => 'nullable|string',
            ]);
            $category = Category::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Category created successfully',
                'data' => $category,
            ]);
        } catch (\Throwable $th) {
            //throw $th;
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
            $category = Category::query()->where('is_deleted', 0)->findOrFail($id);
            
            if (!$category) {
                return response()->json([
                    'status' => false,
                    'message' => 'Category not found',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Category',
                'data' => $category
            ]);
        } catch (\Throwable $th) {
            //throw $th;
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
            $category = Category::query()->where('is_deleted', 0)->findOrFail($id);
            $request->validate([
                'name' => 'nullable|string',
                // 'description' => 'nullable|string',
            ]);
            $category->update($request->all());

            if (!$category) {
                return response()->json([
                    'status' => false,
                    'message' => 'Category not found',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'message' => 'Category updated successfully',
                'data' => $category,
            ]);
        } catch (\Throwable $th) {
            //throw $th;
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
            $category = Category::query()->where('is_deleted', 0)->findOrFail($id);
            $category->update(['is_deleted' => 1]);
            return response()->json([
                'status' => true,
                'message' => 'Category deleted successfully',
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => $th->getMessage(),
            ], 500);
        }
    }
}
