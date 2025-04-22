<?php

namespace App\Http\Controllers\Payment;

use App\Models\Payment_Gateway_Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PaymentGatewayResponseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        try {
            $paymentGatewayResponse = Payment_Gateway_Response::query()->where('is_deleted', 0);
            if ($request->has('payment_id')) {
                $paymentGatewayResponse->where('payment_id', $request->query('payment_id'));
            }
            return response()->json($paymentGatewayResponse->paginate($request->query('limit') ?? 10)
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
                'transaction_id' => 'nullable|string',
                'gateway' => 'nullable|string',
                'metadata' => 'nullable|string',
                'status' => 'nullable|string',
            ]);
            $paymentGatewayResponse = Payment_Gateway_Response::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Payment Gateway Response created successfully',
                'data' => $paymentGatewayResponse,
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
            $paymentGatewayResponse = Payment_Gateway_Response::query()->where('is_deleted', 0)->find($id);
            if (!$paymentGatewayResponse) {
                return response()->json([
                    'status' => false,
                    'message' => 'Payment Gateway Response not found',
                ]);
            }
            return response()->json([
                'status' => true,
                'message' => 'Payment Gateway Response',
                'data' => $paymentGatewayResponse
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
                'transaction_id' => 'nullable|string',
                'gateway' => 'nullable|string',
                'metadata' => 'nullable|string',
                'status' => 'nullable|string',
            ]);
            $paymentGatewayResponse = Payment_Gateway_Response::query()->where('is_deleted', 0)->find($id);
            if (!$paymentGatewayResponse) {
                return response()->json([
                    'status' => false,
                    'message' => 'Payment Gateway Response not found',
                ]);
            }
            $paymentGatewayResponse->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Payment Gateway Response updated successfully',
                'data' => $paymentGatewayResponse,
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
            $paymentGatewayResponse = Payment_Gateway_Response::query()->where('is_deleted', 0)->find($id);
            if (!$paymentGatewayResponse) {
                return response()->json([
                    'status' => false,
                    'message' => 'Payment Gateway Response not found',
                ]);
            }
            $paymentGatewayResponse->update(['is_deleted' => 1]);
            return response()->json([
                'status' => true,
                'message' => 'Payment Gateway Response deleted successfully',
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
