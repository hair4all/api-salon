<?php

namespace App\Http\Controllers\Payment;

use App\Models\Payment_Method;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PaymentMethodController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        try {
            $paymentMethod = Payment_Method::query()->where('is_deleted', 0);
            if ($request->has('name')) {
                $paymentMethod->where('name', $request->query('name'));
            }
            return response()->json( $paymentMethod->paginate($request->query('limit') ?? 10)
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
                'name' => 'nullable|string',
                'type' => 'nullable|string',
                'description' => 'nullable|string',
            ]);
            $paymentMethod = Payment_Method::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Payment Method created successfully',
                'data' => $paymentMethod,
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
            $paymentMethod = Payment_Method::find($id)->where('is_deleted', 0);
            if (!$paymentMethod) {
                return response()->json([
                    'status' => false,
                    'message' => 'Payment Method not found',
                    'data' => null,
                ]);
            }
            return response()->json([
                'status' => true,
                'message' => 'Payment Method',
                'data' => $paymentMethod
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
            $request->validate([
                'name' => 'nullable|string',
                'type' => 'nullable|string',
                'description' => 'nullable|string',
            ]);
            $paymentMethod = Payment_Method::find($id);
            if (!$paymentMethod) {
                return response()->json([
                    'status' => false,
                    'message' => 'Payment Method not found',
                    'data' => null,
                ]);
            }
            $paymentMethod->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Payment Method updated successfully',
                'data' => $paymentMethod,
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
            $paymentMethod = Payment_Method::find($id);
            if (!$paymentMethod) {
                return response()->json([
                    'status' => false,
                    'message' => 'Payment Method not found',
                    'data' => null,
                ]);
            }
            $paymentMethod->update(['is_deleted' => 1]);
            return response()->json([
                'status' => true,
                'message' => 'Payment Method deleted successfully',
                'data' => null,
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
