<?php

namespace App\Http\Controllers\BookingService;

use App\Http\Controllers\Controller;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        try {
            $services = Service::query()->where('is_delete', 0);
            if ($request->has('search')) {
                $services->where('name', 'like', '%' . $request->search . '%');
            }
            return response()->json(
                $services->paginate($request->limit ?? 10),
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
                'branch_id' => 'nullable|integer',
                'name' => 'nullable|string',
                'description' => 'nullable|string',
                'price' => 'nullable|numeric',
                'discount' => 'nullable|numeric',
                'expiry_discount_date' => 'nullable|date',
            ]);
            $service = Service::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Service created successfully',
                'data' => $service,
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
            $service = Service::query()->where('id', $id)->where('is_delete', 0)->first();
            if ($service) {
                return response()->json([
                    'status' => true,
                    'message' => 'Get service',
                    'data' => $service,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Service not found',
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
                'branch_id' => 'nullable|integer',
                'name' => 'nullable|string',
                'description' => 'nullable|string',
                'price' => 'nullable|numeric',
                'discount' => 'nullable|numeric',
                'expiry_discount_date' => 'nullable|date',
            ]);
            $service = Service::findOrFail($id);
            $service->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Service updated successfully',
                'data' => $service,
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
            $service = Service::findOrFail($id);
            $service->update(['is_delete' => 1]);
            return response()->json([
                'status' => true,
                'message' => 'Service deleted successfully',
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
