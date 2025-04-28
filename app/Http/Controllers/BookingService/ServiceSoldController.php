<?php


namespace App\Http\Controllers\BookingService;

use App\Models\Service_Sold;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class ServiceSoldController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        try {
            $serviceSold = Service_Sold::query()->where('is_deleted', 0);
            if ($request->has('service_id')) {
                $serviceSold->where('service_id', $request->query('service_id'));
            }
            return response()->json(
                $serviceSold->paginate($request->query('limit') ?? 10)
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
                'service_id' => 'nullable|integer',
                // 'inventory_id' => 'nullable|integer',
                // 'quantity' => 'nullable|integer',
                'client_id' => 'nullable|integer',
                'branch_id' => 'nullable|integer',
                'worker_id' => 'nullable|integer',
                'sold_date' => 'nullable|date',
            ]);
            $serviceSold = Service_Sold::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Service Sold created successfully',
                'data' => $serviceSold,
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
            $serviceSold = Service_Sold::query()->where('id', $id)->where('is_deleted', 0)->first();
            if ($serviceSold) {
                return response()->json([
                    'status' => true,
                    'message' => 'Service Sold',
                    'data' => $serviceSold
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Service Sold not found',
                    'data' => null
                ]);
            }
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
                'service_id' => 'nullable|integer',
                // 'inventory_id' => 'nullable|integer',
                // 'quantity' => 'nullable|integer',
                'client_id' => 'nullable|integer',
                'branch_id' => 'nullable|integer',
                'worker_id' => 'nullable|integer',
                'sold_date' => 'nullable|date',
            ]);
            $serviceSold = Service_Sold::query()->where('id', $id)->where('is_deleted', 0)->first();
            if ($serviceSold) {
                $serviceSold->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Service Sold updated successfully',
                    'data' => $serviceSold,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Service Sold not found',
                    'data' => null
                ]);
            }
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
            $serviceSold = Service_Sold::query()->where('id', $id)->where('is_deleted', 0)->first();
            if ($serviceSold) {
                $serviceSold->update(['is_deleted' => 1]);
                return response()->json([
                    'status' => true,
                    'message' => 'Service Sold deleted successfully',
                    'data' => null
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Service Sold not found',
                    'data' => null
                ]);
            }
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
