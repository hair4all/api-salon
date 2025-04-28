<?php

namespace App\Http\Controllers\Product;

use App\Models\Item_Sold;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ItemSoldController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        try {
            $itemSold = Item_Sold::query()->where('is_deleted', 0);
            if ($request->has('product_id')) {
                $itemSold->where('product_id', $request->query('product_id'));
            }
            return response()->json(
                $itemSold->paginate($request->query('limit') ?? 10)
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
                'inventory_id' => 'nullable|integer',
                'quantity' => 'nullable|integer',
                'price' => 'nullable|numeric',
                'sold_date' => 'nullable|date',
            ]);
            $itemSold = Item_Sold::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Item Sold created successfully',
                'data' => $itemSold,
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
            $itemSold = Item_Sold::query()->where('is_deleted', 0)->find($id);
            if (!$itemSold) {
                return response()->json([
                    'status' => false,
                    'message' => 'Item Sold not found',
                ]);
            }
            return response()->json([
                'status' => true,
                'message' => 'Item Sold',
                'data' => $itemSold
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
                'quantity' => 'nullable|integer',
                'price' => 'nullable|numeric',
                'sold_date' => 'nullable|date',
            ]);
            $itemSold = Item_Sold::query()->where('is_deleted', 0)->find($id);
            if (!$itemSold) {
                return response()->json([
                    'status' => false,
                    'message' => 'Item Sold not found',
                ]);
            }
            $itemSold->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Item Sold updated successfully',
                'data' => $itemSold,
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
            $itemSold = Item_Sold::query()->where('is_deleted', 0)->find($id);
            if (!$itemSold) {
                return response()->json([
                    'status' => false,
                    'message' => 'Item Sold not found',
                ]);
            }
            $itemSold->update(['is_deleted' => 1]);
            return response()->json([
                'status' => true,
                'message' => 'Item Sold deleted successfully',
                'data' => null
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
