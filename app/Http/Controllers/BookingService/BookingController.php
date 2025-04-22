<?php

namespace App\Http\Controllers\BookingService;

use App\Models\Booking;
use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

/**
 * Class BookingController
 *
 * Manages CRUD operations for bookings, including filtering, pagination, and soft deletion.
 *
 * @package App\Http\Controllers\BookingService
 */


class BookingController extends Controller
{
/**
 * Display a listing of the resource.
 *
 * Retrieves bookings with optional filters:
 * - search by client name
 * - filter by creation date
 * - filter by status
 * Results are paginated (default limit: 10).
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\JsonResponse
 */
    public function index(Request $request)
        {
            //
            try {
                $bookings = Booking::query()->where('is_deleted',0);
                if($request->has('search')) {
                    $bookings->where('name', 'like', '%' . $request->input('search') . '%');
                }
                if($request->has('date')) {
                    $bookings->whereDate('created_at', $request->input('date'));
                }
                if($request->has('status')) {
                    $bookings->where('status', $request->input('status'));
                }
                return response()->json([
                    'status' => true,
                    'message' => 'Booking list',
                    'data' => $bookings->paginate($request->limit ?? 10),
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
 * Store a newly created resource in storage.
 *
 * Validates the input for:
 * - client_id (integer)
 * - booking_date (date)
 * - notes (nullable string)
 * - status (string)
 * Creates and returns the new booking record.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\JsonResponse
 */
    public function store(Request $request)
    {
        //
        try {

            $request->validate([
                'client_id' => 'integer',
                'booking_date' => 'date',
                'notes' => 'nullable|string',
                'status' => 'string',
            ]);

            $booking = Booking::create($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Booking created successfully',
                'data' => $booking,
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
 *
 * Retrieves a single booking by ID if not marked as deleted.
 * Returns an error response if the booking is not found.
 *
 * @param  int  $id
 * @return \Illuminate\Http\JsonResponse
 */
    public function show( $id)
    {
        //
        try {
            $booking = Booking::find($id)->where('is_deleted',0)->first();
            if (!$booking) {
                return response()->json([
                    'status' => false,
                    'message' => 'Booking not found',
                ]);
            }
            return response()->json([
                'status' => true,
                'message' => 'Booking details',
                'data' => $booking,
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
 *
 * Validates the input fields (same rules as store),
 * then updates the existing booking by ID.
 * Returns the updated booking or an error if not found.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  int  $id
 * @return \Illuminate\Http\JsonResponse
 */

    public function update(Request $request,  $id)
    {
        //
        try {
            $request->validate([
                'client_id' => 'integer',
                'booking_date' => 'date',
                'notes' => 'nullable|string',
                'status' => 'string',
            ]);

            $booking = Booking::find($id);
            if (!$booking) {
                return response()->json([
                    'status' => false,
                    'message' => 'Booking not found',
                ]);
            }
            $booking->update($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Booking updated successfully',
                'data' => $booking,
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
 *
 * Soft-deletes the booking by setting 'is_deleted' to true.
 * Returns a success message or error if not found.
 *
 * @param  int  $id
 * @return \Illuminate\Http\JsonResponse
 */
    public function destroy( $id)
    {
        //
        try {
            $booking = Booking::find($id);
            if (!$booking) {
                return response()->json([
                    'status' => false,
                    'message' => 'Booking not found',
                ]);
            }
            $booking->update(['is_deleted' => 1]);

            return response()->json([
                'status' => true,
                'message' => 'Booking deleted successfully',
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
