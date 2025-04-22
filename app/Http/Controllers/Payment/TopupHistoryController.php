<?php

namespace App\Http\Controllers\Payment;

use App\Models\Topup_History;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TopupHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        try {
            $topupHistory = Topup_History::query()->where('is_deleted', 0);
            if ($request->has('client_id')) {
                $topupHistory->where('client_id', $request->query('client_id'));
            }
            return response()->json($topupHistory->paginate($request->query('limit') ?? 10)
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
                'client_id' => 'nullable|integer',
                'amount' => 'nullable|integer',
                'topup_date' => 'nullable|date',
                'payment_method_id' => 'nullable|integer',
                'status' => 'nullable|string',
            ]);
            $topupHistory = Topup_History::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Topup History created successfully',
                'data' => $topupHistory,
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
            $topupHistory = Topup_History::query()->where('is_deleted', 0)->where('id', $id)->first();
            if (!$topupHistory) {
                return response()->json([
                    'status' => false,
                    'message' => 'Topup History not found',
                ]);
            }
            return response()->json([
                'status' => true,
                'message' => 'Topup History',
                'data' => $topupHistory
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
                'client_id' => 'nullable|integer',
                'amount' => 'nullable|integer',
                'topup_date' => 'nullable|date',
                'payment_method_id' => 'nullable|integer',
                'status' => 'nullable|string',
            ]);
            $topupHistory = Topup_History::findOrFail($id);
            $topupHistory->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Topup History updated successfully',
                'data' => $topupHistory,
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
            $topupHistory = Topup_History::findOrFail($id);
            $topupHistory->update(['is_deleted' => 1]);
            return response()->json([
                'status' => true,
                'message' => 'Topup History deleted successfully',
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
