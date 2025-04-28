<?php

namespace App\Http\Controllers\Product;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        try {
            $orders = Order::query()->where('is_deleted', 0);
            if ($request->has('customer_id')) {
                $orders->where('customer_id', $request->query('customer_id'));
            }
            return response()->json(
                $orders->paginate($request->query('limit') ?? 10)
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
                'member_id' => 'nullable|integer',
                'position_id' => 'nullable|integer',
                'district_id' => 'nullable|integer',
                'city_id' => 'nullable|integer',
                'order_number' => 'nullable|string|max:255',
                'cart_id' => 'nullable|integer',
                'shipping_address_id' => 'nullable|integer',
                'total_price' => 'nullable|numeric',
                'courier' => 'nullable|string|max:255',
                'shipping_cost' => 'nullable|numeric',
                'status' => 'nullable|string|max:255',
            ]);
            $order = Order::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Order created successfully',
                'data' => $order,
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
            $order = Order::query()->where('is_deleted', 0)->findOrFail($id);
            return response()->json([
                'status' => true,
                'message' => 'Order',
                'data' => $order
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
                'member_id' => 'nullable|integer',
                'position_id' => 'nullable|integer',
                'district_id' => 'nullable|integer',
                'city_id' => 'nullable|integer',
                'order_number' => 'nullable|string|max:255',
                'cart_id' => 'nullable|integer',
                'shipping_address_id' => 'nullable|integer',
                'total_price' => 'nullable|numeric',
                'courier' => 'nullable|string|max:255',
                'shipping_cost' => 'nullable|numeric',
                'status' => 'nullable|string|max:255',
            ]);
            $order = Order::findOrFail($id);
            $order->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Order updated successfully',
                'data' => $order,
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
            $order = Order::query()->where('is_deleted', 0)->findOrFail($id);
            $order->update(['is_deleted' => 1]);
            return response()->json([
                'status' => true,
                'message' => 'Order deleted successfully',
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
