<?php

namespace App\Http\Controllers\BookingService;

use App\Http\Controllers\Controller;

use App\Models\Booking_Service;
use Illuminate\Http\Request;

class BookingServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //
        try {
            $booking_services = Booking_Service::query()->where('is_deleted', 0);
            if ($request->has('search')) {
                $booking_services = $booking_services->where('name', 'like', '%' . $request->search . '%');
            }
            return response()->json( $booking_services->paginate($request->limit ?? 10),
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
        try{
            $request->validate([
                'client_id' => 'integer',
                'branch_id' => 'integer',
                'price' => 'numeric',
                'discount' => 'nullable|numeric',
                'expiry_discount_date' => 'nullable|date',
                'status' => 'string',
            ]);
            $booking_service = Booking_Service::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Booking Service created successfully',
                'data' => $booking_service,
            ]);
        }
        catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'data' => $th->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show( $id)
    {
        //
        try {
            $booking_service = Booking_Service::findOrFail($id)->where('is_deleted', 0);
            return response()->json([
                'status' => true,
                'message' => 'Booking Service',
                'data' => $booking_service,
            ]);

        } catch (\Throwable $th) {
            
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
    public function update(Request $request,  $id)
    {
        //
        try {
            $request->validate([
                'client_id' => 'integer',
                'branch_id' => 'integer',
                'price' => 'numeric',
                'discount' => 'nullable|numeric',
                'expiry_discount_date' => 'nullable|date',
                'status' => 'string',
            ]);
            $booking_service = Booking_Service::findOrFail($id);
            $booking_service->update($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Booking Service updated successfully',
                'data' => $booking_service,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Validation error',
                'data' => $th->getMessage(),
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $id)
    {
        //
        try {
            $booking_service = Booking_Service::findOrFail($id);
            $booking_service->update(['is_deleted' => 1]);
            return response()->json([
                'status' => true,
                'message' => 'Booking Service deleted successfully',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'data' => $th->getMessage(),
            ]);
        }
    }
}
