<?php

namespace App\Http\Controllers\Payment;

use App\Models\Withdraw_Histories;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WithdrawHistoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        try {
            $withdrawHistories = Withdraw_Histories::query()->where('is_deleted', 0);
            if ($request->has('client_id')) {
                $withdrawHistories->where('client_id', $request->query('client_id'));
            }
            return response()->json($withdrawHistories->paginate($request->query('limit') ?? 10)
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
                'withdraw_id' => 'nullable|integer',
                // 'inventory_id' => 'nullable|integer',
                'amount' => 'nullable|integer',
                'withdraw_date' => 'nullable|date',
                'payment_method_id' => 'nullable|integer',
                'status' => 'nullable|string',
            ]);
            $withdrawHistories = Withdraw_Histories::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Withdraw History created successfully',
                'data' => $withdrawHistories,
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
            $withdrawHistories = Withdraw_Histories::query()->where('id', $id)->where('is_deleted', 0)->first();
            if ($withdrawHistories) {
                return response()->json([
                    'status' => true,
                    'message' => 'Withdraw History',
                    'data' => $withdrawHistories
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Withdraw History not found',
                ]);
            }
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
                'withdraw_id' => 'nullable|integer',
                // 'inventory_id' => 'nullable|integer',
                'amount' => 'nullable|integer',
                'withdraw_date' => 'nullable|date',
                'payment_method_id' => 'nullable|integer',
                'status' => 'nullable|string',
            ]);
            $withdrawHistories = Withdraw_Histories::findOrFail($id);
            $withdrawHistories->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Withdraw History updated successfully',
                'data' => $withdrawHistories,
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
            $withdrawHistories = Withdraw_Histories::findOrFail($id);
            $withdrawHistories->update(['is_deleted' => 1]);
            return response()->json([
                'status' => true,
                'message' => 'Withdraw History deleted successfully',
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
