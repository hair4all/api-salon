<?php

namespace App\Http\Controllers\Product;

use App\Models\Cart;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        try {
            $cart = Cart::query()->where('is_deleted', 0);
            if ($request->has('client_id')) {
                $cart->where('client_id', $request->query('client_id'));
            }
            return response()->json(
                $cart->paginate($request->query('limit') ?? 10)
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
                'client_id' => 'nullable|integer',
                'product_id' => 'nullable|integer',
                'quantity' => 'nullable|integer',
                'status' => 'nullable|string',
            ]);
            $cart = Cart::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Cart created successfully',
                'data' => $cart,
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
            $cart = Cart::query()->where('is_deleted', 0)->where('id', $id)->first();
            if (!$cart) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cart not found',
                ]);
            }
            return response()->json([
                'status' => true,
                'message' => 'Cart',
                'data' => $cart
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
                'client_id' => 'nullable|integer',
                'product_id' => 'nullable|integer',
                'quantity' => 'nullable|integer',
                'status' => 'nullable|string',
            ]);
            $cart = Cart::query()->where('is_deleted', 0)->where('id', $id)->first();
            if (!$cart) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cart not found',
                ]);
            }
            $cart->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Cart updated successfully',
                'data' => $cart,
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
            $cart = Cart::query()->where('is_deleted', 0)->where('id', $id)->first();
            if (!$cart) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cart not found',
                ]);
            }
            $cart->update(['is_deleted' => 1]);
            return response()->json([
                'status' => true,
                'message' => 'Cart deleted successfully',
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
