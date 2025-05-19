<?php

namespace App\Http\Controllers\Extension;

use App\Service\Shipping\RajaOngkirService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RajaOngkirCallbackController extends Controller
{
    private RajaOngkirService $service;

    public function __construct(RajaOngkirService $service)
    {
        $this->service = $service;
    }

    public function getDestination(Request $request)
    {
        $request->validate([
            'search' => 'required|string',
        ]);

        $result = $this->service->getDestination($request->query('search'));

        return response()->json($result, $result['status'] ? 200 : 400);
    }

    public function getHistoryAirwayBill(Request $request)
    {
        $request->validate([
            'shipping'    => 'required|string',
            'airway_bill' => 'required|string',
        ]);

        $result = $this->service->getHistoryAirwayBill(
            $request->query('shipping'),
            $request->query('airway_bill')
        );

        return response()->json($result, $result['status'] ? 200 : 400);
    }

    public function getOrderDetail(Request $request)
    {
        $request->validate([
            'order_no' => 'required|string',
        ]);

        $result = $this->service->getOrderDetail($request->query('order_no'));

        return response()->json($result, $result['status'] ? 200 : 400);
    }

    public function getShippingCost(Request $request)
    {
        $request->validate([
            'shipper_destination_id'  => 'required|integer',
            'receiver_destination_id' => 'required|integer',
            'weight'                  => 'required|numeric',
            'item_value'              => 'required|numeric',
        ]);

        $params = $request->only([
            'shipper_destination_id',
            'receiver_destination_id',
            'weight',
            'item_value',
        ]);

        $result = $this->service->getShippingCost($params);

        return response()->json($result, $result['status'] ? 200 : 400);
    }

    public function storeOrder(Request $request)
    {
        $request->validate([
            'order_date'              => 'required',
            'brand_name'              => 'required|string',
            // … all your other rules …
            'order_details'           => 'required|array|min:1',
            'order_details.*.product_name'         => 'required|string',
            // … etc …
        ]);

        $result = $this->service->storeOrder($request->all());

        return response()->json($result, $result['status'] ? 200 : 500);
    }

    public function orderPickup(Request $request)
    {
        $request->validate([
            'pickup_date'    => 'required',
            'pickup_time'    => 'required|string',
            'pickup_vehicle' => 'required|string',
            'orders'         => 'required|array|min:1',
            'orders.*.order_no' => 'required|string',
        ]);

        $result = $this->service->orderPickup($request->only([
            'pickup_date',
            'pickup_time',
            'pickup_vehicle',
            'orders',
        ]));

        return response()->json($result, $result['status'] ? 200 : 500);
    }

    public function labelOrder(Request $request)
    {
        $request->validate([
            'order_no' => 'required|string',
            'page'     => 'nullable|string',
        ]);

        $result = $this->service->labelOrder(
            $request->input('order_no'),
            $request->input('page')
        );

        return response()->json($result, $result['status'] ? 200 : 400);
    }

    public function cancelOrder(Request $request)
    {
        $request->validate([
            'order_no' => 'required|string',
        ]);

        $result = $this->service->cancelOrder($request->input('order_no'));

        return response()->json($result, $result['status'] ? 200 : 400);
    }
}
