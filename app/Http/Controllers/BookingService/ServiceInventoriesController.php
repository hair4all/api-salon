<?php

namespace App\Http\Controllers\BookingService;

use App\Models\Service_Inventories;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

class ServiceInventoriesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        try {
            $serviceInventories = Service_Inventories::query()->where('is_deleted', 0);
            if ($request->has('service_id')) {
                $serviceInventories->where('service_id', $request->query('service_id'));
            }
            return response()->json( $serviceInventories->paginate($request->query('limit') ?? 10)
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
                'service_id' => 'nullable|integer',
                'inventory_id' => 'nullable|integer',
                'quantity' => 'nullable|integer',
            ]);
            $serviceInventory = Service_Inventories::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Service Inventory created successfully',
                'data' => $serviceInventory,
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
            $serviceInventory = Service_Inventories::query()->where('id', $id)->where('is_deleted', 0)->first();
            if ($serviceInventory) {
                return response()->json([
                    'status' => true,
                    'message' => 'Get service inventory',
                    'data' => $serviceInventory,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Service Inventory not found',
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
                'service_id' => 'nullable|integer',
                'inventory_id' => 'nullable|integer',
                'quantity' => 'nullable|integer',
            ]);
            $serviceInventory = Service_Inventories::query()->where('id', $id)->where('is_deleted', 0)->first();
            if ($serviceInventory) {
                $serviceInventory->update($request->all());
                return response()->json([
                    'status' => true,
                    'message' => 'Service Inventory updated successfully',
                    'data' => $serviceInventory,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Service Inventory not found',
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
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
        try {
            $serviceInventory = Service_Inventories::query()->where('id', $id)->where('is_deleted', 0)->first();
            if ($serviceInventory) {
                $serviceInventory->update(['is_deleted' => 1]);
                return response()->json([
                    'status' => true,
                    'message' => 'Service Inventory deleted successfully',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Service Inventory not found',
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
}
