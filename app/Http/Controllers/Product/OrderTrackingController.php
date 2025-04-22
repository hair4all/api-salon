<?php

namespace App\Http\Controllers\Product;

use App\Models\Order_Tracking;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class OrderTrackingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        try {
            $orderTracking = Order_Tracking::query()->where('is_deleted', 0);
            if ($request->has('order_id')) {
                $orderTracking->where('order_id', $request->query('order_id'));
            }
            return response()->json($orderTracking->paginate($request->query('limit') ?? 10)
            );
        } catch (\Throwable $th) {
            //throw $th;
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
                'order_id' => 'nullable|integer',
                'tracking_number' => 'nullable|string|max:255',
                'courier' => 'nullable|string|max:255',
                'status' => 'nullable|string|max:255',
                'shipping_address' => 'nullable|string|max:500',
                'estimated_delivery_date' => 'nullable|date',
                'is_deleted' => 'nullable|boolean',
            ]);
            $orderTracking = Order_Tracking::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Order Tracking created successfully',
                'data' => $orderTracking,
            ]);
        } catch (\Throwable $th) {
            //throw $th;
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
    public function show($id)
    {
        //
        try {
            $orderTracking = Order_Tracking::query()->where('is_deleted', 0)->findOrFail($id);
            return response()->json([
                'status' => true,
                'message' => 'Order Tracking',
                'data' => $orderTracking
            ]);
        } catch (\Throwable $th) {
            //throw $th;
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
    public function update(Request $request, $id)
    {
        //
        try {
            $orderTracking = Order_Tracking::query()->where('is_deleted', 0)->findOrFail($id);
            $request->validate([
                'order_id' => 'nullable|integer',
                'tracking_number' => 'nullable|string|max:255',
                'courier' => 'nullable|string|max:255',
                'status' => 'nullable|string|max:255',
                'shipping_address' => 'nullable|string|max:500',
                'estimated_delivery_date' => 'nullable|date',
                'is_deleted' => 'nullable|boolean',
            ]);
            $orderTracking->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Order Tracking updated successfully',
                'data' => $orderTracking,
            ]);
        } catch (\Throwable $th) {
            //throw $th;
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
    public function destroy($id)
    {
        //
        try {
            $orderTracking = Order_Tracking::query()->where('is_deleted', 0)->findOrFail($id);
            $orderTracking->update(['is_deleted' => 1]);
            return response()->json([
                'status' => true,
                'message' => 'Order Tracking deleted successfully',
                'data' => $orderTracking,
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => $th->getMessage(),
            ]);
        }
    }
}
