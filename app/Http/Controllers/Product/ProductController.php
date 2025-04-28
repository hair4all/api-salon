<?php

namespace App\Http\Controllers\Product;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        try {
            $products = Product::query()->where('is_deleted', 0);
            if ($request->has('name')) {
                $products->where('name', 'like', '%' . $request->query('name') . '%');
            }
            return response()->json([
                'status' => true,
                'message' => 'Products',
                'data' => $products->paginate($request->query('limit') ?? 10)
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try {
            $request->validate([
                'inventory_id' => 'required|integer',
                'category_id' => 'required|integer',
                'branch_id' => 'required|integer',
                'discount' => 'nullable|numeric',
                'expiry_discount_date' => 'nullable|date',
                'status' => 'required|boolean',
            ]);
            $product = Product::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Product created successfully',
                'data' => $product,
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
            $product = Product::query()->where('is_deleted', 0)->findOrFail($id);
            return response()->json([
                'status' => true,
                'message' => 'Product',
                'data' => $product,
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
            $request->validate([
                'inventory_id' => 'nullable|integer',
                'category_id' => 'nullable|integer',
                'branch_id' => 'nullable|integer',
                'discount' => 'nullable|numeric',
                'expiry_discount_date' => 'nullable|date',
                'status' => 'nullable|boolean',
            ]);
            $product = Product::query()->where('is_deleted', 0)->findOrFail($id);
            $product->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Product updated successfully',
                'data' => $product,
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
            $product = Product::query()->where('is_deleted', 0)->findOrFail($id);
            $product->update(['is_deleted' => 1]);
            return response()->json([
                'status' => true,
                'message' => 'Product deleted successfully',
                'data' => null,
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
