<?php

namespace App\Http\Controllers\Product;

use App\Models\Shipping_Address;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ShippingAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        try {
            $shippingAddress = Shipping_Address::query()->where('is_deleted', 0);
            if ($request->has('member_id')) {
                $shippingAddress->where('member_id', $request->query('member_id'));
            }
            return response()->json($shippingAddress->paginate($request->query('limit') ?? 10));
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
                'recipient_name' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:15',
                'province_id' => 'nullable|integer',
                'city_id' => 'nullable|integer',
                'district_id' => 'nullable|integer',
                'address' => 'nullable|string|max:500',
            ]);
            $shippingAddress = Shipping_Address::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Shipping Address created successfully',
                'data' => $shippingAddress,
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
            $shippingAddress = Shipping_Address::query()->where('is_deleted', 0)->findOrFail($id);
            return response()->json([
                'status' => true,
                'message' => 'Shipping Address',
                'data' => $shippingAddress
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
                'recipient_name' => 'nullable|string|max:255',
                'phone' => 'nullable|string|max:15',
                'province_id' => 'nullable|integer',
                'city_id' => 'nullable|integer',
                'district_id' => 'nullable|integer',
                'address' => 'nullable|string|max:500',
            ]);
            $shippingAddress = Shipping_Address::query()->where('is_deleted', 0)->findOrFail($id);
            $shippingAddress->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Shipping Address updated successfully',
                'data' => $shippingAddress,
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
            $shippingAddress = Shipping_Address::query()->where('is_deleted', 0)->findOrFail($id);
            $shippingAddress->update(['is_deleted' => 1]);
            return response()->json([
                'status' => true,
                'message' => 'Shipping Address deleted successfully',
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
