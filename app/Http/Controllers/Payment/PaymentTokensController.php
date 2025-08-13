<?php

namespace App\Http\Controllers\Payment;

use App\Models\Payment_Tokens;
use Illuminate\Http\Request;
use Log;

use App\Http\Controllers\Controller;

class PaymentTokensController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        try {
            $paymentTokens = Payment_Tokens::query();
            
            if (request()->has('user_id')) {
                $paymentTokens->where('user_id', request()->query('user_id'));
            }
            if (request()->has('expiry')) {
                $paymentTokens->where('expiry', request()->query('expiry'));
            }
            
            return response()->json($paymentTokens);
        } catch (\Throwable $th) {
                            Log::error('OrderController@store error', [
                    'message' => $th->getMessage(),
                    'trace'   => $th->getTraceAsString(),
                ]);
            return response()->json(['error' => 'Failed to retrieve payment tokens'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
        try {
            $paymentToken = Payment_Tokens::create([
                'token' => $request->input('token'),
                'user_id' => $request->input('user_id'),
                'expiry' => $request->input('expiry'),
            ]);

            return response()->json($paymentToken, 201);
        } catch (\Throwable $th) {
                            Log::error('OrderController@store error', [
                    'message' => $th->getMessage(),
                    'trace'   => $th->getTraceAsString(),
                ]);
            return response()->json(['error' => 'Failed to create payment token'], 500);
        }
    }

    public function generate(Request $request)
    {
        //
        try {
            $request->validate([
                'user_id' => 'required',
            ]);
            $token = bin2hex(random_bytes(16)); // Generate a random token

            // Get timezone from request or use default
            $timezone = $request->input('timezone', config('app.timezone', 'UTC'));
            $expiry = now()->setTimezone($timezone)->addDay();

            $paymentToken = Payment_Tokens::create([
                'token' => $token,
                'user_id' => $request->input('user_id'),
                'expiry' => $expiry,
            ]);

            return response()->json($paymentToken, 201);
        } catch (\Throwable $th) {
            
                Log::error('OrderController@store error', [
                    'message' => $th->getMessage(),
                    'trace'   => $th->getTraceAsString(),
                ]);
            return response()->json(['error' => 'Failed to generate payment token'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show( $id)
    {
        //
        try {
            $paymentToken = Payment_Tokens::findOrFail($id);
            return response()->json($paymentToken);
        } catch (\Throwable $th) {
                            Log::error('OrderController@store error', [
                    'message' => $th->getMessage(),
                    'trace'   => $th->getTraceAsString(),
                ]);
            return response()->json(['error' => 'Payment token not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  $id)
    {
        //
        try {
            $paymentToken = Payment_Tokens::findOrFail($id);
            $paymentToken->update($request->all());
            return response()->json($paymentToken);
        } catch (\Throwable $th) {
                            Log::error('OrderController@store error', [
                    'message' => $th->getMessage(),
                    'trace'   => $th->getTraceAsString(),
                ]);
            return response()->json(['error' => 'Failed to update payment token'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $id)
    {
        //
        try {
            $paymentToken = Payment_Tokens::findOrFail($id);
            $paymentToken->delete();
            return response()->json(['message' => 'Payment token deleted successfully'], 200);
        } catch (\Throwable $th) {
                            Log::error('OrderController@store error', [
                    'message' => $th->getMessage(),
                    'trace'   => $th->getTraceAsString(),
                ]);
            return response()->json(['error' => 'Failed to delete payment token'], 500);
        }
    }
}
