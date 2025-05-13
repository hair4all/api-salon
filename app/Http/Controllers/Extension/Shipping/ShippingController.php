<?php

namespace App\Http\Controllers\Extension\Shipping;

use App\Http\Controllers\Controller;
use App\Service\Shipping\RajaOngkirService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

use App\Models\Cart;
use App\Models\Payment;
use App\Models\Orders;
use App\Models\OrderTracking;
use App\Models\ItemSold;

class ShippingController extends Controller
{
    protected $rajaOngkirService;
    /**
     * @var RajaOngkirService
     */
    public function __construct()
    {
        $this->rajaOngkirService = new RajaOngkirService();
    }

    public function pushShipping(Request $request){
        try {
            /*
            POST[Cart]
            Payment -> Push Item [Cart] -> RajaOngkir Post(Store) -> Orders -> Order Tracking 
            -> Loop Cart and Add Item Sold -> Remove Item [Cart]
            */
            // Payment Section




            // RajaOngkir Section
            $cost = $this->rajaOngkirService->getShippingCost(
                $request->input('shipper_destination_id'),
                $request->input('receiver_destination_id'),
                $request->input('weight'),
                $request->input('item_value')
            );

            if ($cost['status'] == 200) {
                $cost = $cost['data'];
            } else {
                return response()->json($cost, Response::HTTP_BAD_REQUEST);
            }

            // Orders Section
            



        } catch (\Throwable $th) {
            //throw $th;
        }
    }

}