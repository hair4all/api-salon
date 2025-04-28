<?php

namespace App\Http\Controllers\Payment;

use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        try {
            $transactions = Transaction::query()->where('is_deleted', 0);
            if ($request->has('client_id')) {
                $transactions->where('client_id', $request->query('client_id'));
            }
            return response()->json(
                $transactions->paginate($request->query('limit') ?? 10)
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
                'worker_id' => 'nullable|integer',
                'transaction_date' => 'nullable|date',
                'total_price' => 'nullable|numeric',
                'payment_method_id' => 'nullable|integer',
            ]);
            $transaction = Transaction::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Transaction created successfully',
                'data' => $transaction,
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
            $transaction = Transaction::query()->where('is_deleted', 0)->findOrFail($id);
            if (!$transaction) {
                return response()->json([
                    'status' => false,
                    'message' => 'Transaction not found',
                    'data' => null,
                ]);
            }
            return response()->json([
                'status' => true,
                'message' => 'Transaction',
                'data' => $transaction
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
                'worker_id' => 'nullable|integer',
                'transaction_date' => 'nullable|date',
                'total_price' => 'nullable|numeric',
                'payment_method_id' => 'nullable|integer',
            ]);
            $transaction = Transaction::findOrFail($id);
            $transaction->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Transaction updated successfully',
                'data' => $transaction,
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
            $transaction = Transaction::findOrFail($id);
            $transaction->update(['is_deleted' => 1]);
            return response()->json([
                'status' => true,
                'message' => 'Transaction deleted successfully',
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
