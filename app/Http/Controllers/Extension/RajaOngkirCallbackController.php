<?php

namespace App\Http\Controllers\Extension;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class RajaOngkirCallbackController extends Controller{

    // private $url = env('RAJA_ONGKIR_URL','https://api-sandbox.collaborator.komerce.id/');
    // private $key = env('RAJA_ONGKIR_KEY','CBhUvjAne0dd4812baa711285pfaOpyN');
    private $url;
    private $key;
    
    public function __construct(){
        $this->url = env('RAJA_ONGKIR_URL','https://api-sandbox.collaborator.komerce.id/');
        $this->key = env('RAJA_ONGKIR_KEY','CBhUvjAne0dd4812baa711285pfaOpyN');
    }


    public function getDestination(Request $request){
        try {
            if(!$request->query('search')){
                return response()->json([
                    'status' => false,
                    'message' => 'search query is required',
                    'data' => null,
                ], 400);
            }
            // dd($request->query('search'),$this->url . 'tariff/api/v1/destination/',$this->key);
            $response = Http::withHeader(
                'x-api-key', $this->key
            )->get($this->url . 'tariff/api/v1/destination/search', [
                
                'keyword' => $request->query('search'),
            ]);
            // dd($response);
            if ($response->successful()) {
                $response = json_decode($response->body(), true);
                return response()->json([
                    'status' => true,
                    'message' => 'Success',
                    'data' => $response['data'],
                ]);
            } else {
                $response = json_decode($response->body(), true);
                return response()->json([
                    'status' => false,
                    'message' => 'Failed',
                    'data' =>$response,
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

    public function getHistoryAirwayBill(Request $request){
        try {
            if(!$request->query('shipping') || !$request->query('airway_bill')){
                return response()->json([
                    'status' => false,
                    'message' => 'shipping and airway_bill query is required',
                    'data' => null,
                ], 400);
            }
            $response = Http::withHeader(
                'x-api-key', $this->key
            )->get($this->url . 'order/api/v1/orders/history-airway-bill', [
                'shipping'=> $request->query('shipping'),
                'airway_bill' => $request->query('airway_bill'),
            ]);
            // dd($response);
            if ($response->successful()) {
                $response = json_decode($response->body(), true);
                return response()->json([
                    'status' => true,
                    'message' => 'Success',
                    'data' => $response['data'],
                ]);
            } else {
                $response = json_decode($response->body(), true);
                return response()->json([
                    'status' => false,
                    'message' => 'Failed',
                    'data' =>$response,
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

    public function getOrderDetail(Request $request){
        try {
            if(!$request->query('order_no')){
                return response()->json([
                    'status' => false,
                    'message' => 'order_no query is required',
                    'data' => null,
                ], 400);
            }
            $response = Http::withHeader(
                'x-api-key', $this->key
            )->get($this->url . 'order/api/v1/orders/detail', [
                'order_no'=> $request->query('order_no'),
            ]);
            if ($response->successful()) {
                $response = json_decode($response->body(), true);
                return response()->json([
                    'status' => true,
                    'message' => 'Success',
                    'data' => $response['data'],
                ]);
            } else {
                $response = json_decode($response->body(), true);
                return response()->json([
                    'status' => false,
                    'message' => 'Failed',
                    'data' =>$response,
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

    public function getShippingCost(Request $request){
        try {
            if(!$request->query('shipper_destination_id') || !$request->query('receiver_destination_id') || !$request->query('weight') || !$request->query('item_value')){
                return response()->json([
                    'status' => false,
                    'message' => 'shipper_destination_id, receiver_destination_id, weight and item_value query is required',
                    'data' => null,
                ], 400);
            }
            $response = Http::withHeader(
                'x-api-key', $this->key
            )->get($this->url . 'tariff/api/v1/calculate', [
                'shipper_destination_id'=> $request->query('shipper_destination_id'),
                'receiver_destination_id' => $request->query('receiver_destination_id'),
                'weight' => $request->query('weight'),
                'item_value' => $request->query('item_value'),
            ]);
            if ($response->successful()) {
                $response = json_decode($response->body(), true);
                return response()->json([
                    'status' => true,
                    'message' => 'Success',
                    'data' => $response['data'],
                ]);
            } else {
                $response = json_decode($response->body(), true);
                return response()->json([
                    'status' => false,
                    'message' => 'Failed',
                    'data' =>$response,
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

    public function storeOrder(Request $request){
        try {
            $request->validate([
                'order_date'               => 'required',
                'brand_name'               => 'required|string|max:255',
                'shipper_name'             => 'required|string|max:255',
                'shipper_phone'            => 'required|string|max:15',
                'shipper_destination_id'   => 'required|integer',
                'shipper_address'          => 'required|string|max:500',
                'shipper_email'            => 'required|email|max:255',
                'receiver_name'            => 'required|string|max:255',
                'receiver_phone'           => 'required|string|max:15',
                'receiver_destination_id'  => 'required|integer',
                'receiver_address'         => 'required|string|max:500',
                'shipping'                 => 'required|string|max:255',
                'shipping_type'            => 'required|string|max:255',
                'payment_method'           => 'required|string|max:255',
                'shipping_cost'            => 'required|numeric|min:0',
                'shipping_cashback'        => 'nullable|numeric|min:0',
                'service_fee'              => 'nullable|numeric|min:0',
                'additional_cost'          => 'nullable|numeric|min:0',
                'grand_total'              => 'required|numeric|min:0',
                'cod_value'                => 'nullable|numeric|min:0',
                'insurance_value'          => 'nullable|numeric|min:0',
                'order_details'            => 'required|array|min:1',
                'order_details.*.product_name'          => 'required|string|max:255',
                'order_details.*.product_variant_name'  => 'required|string|max:255',
                'order_details.*.product_price'         => 'required|numeric|min:0',
                'order_details.*.product_width'         => 'required|numeric|min:0',
                'order_details.*.product_height'        => 'required|numeric|min:0',
                'order_details.*.product_weight'        => 'required|numeric|min:0',
                'order_details.*.product_length'        => 'required|numeric|min:0',
                'order_details.*.qty'                   => 'required|integer|min:1',
                'order_details.*.subtotal'              => 'required|numeric|min:0',
            ]);

            $body = $request->all();

            $response = Http::withHeader(
                'x-api-key', $this->key
            )->post($this->url . 'order/api/v1/orders/store',$body );
            if ($response->successful()) {
                $response = json_decode($response->body(), true);
                return response()->json([
                    'status' => true,
                    'message' => 'Success',
                    'data' => $response['data'],
                ]);
            } else {
                $response = json_decode($response->body(), true);
                return response()->json([
                    'status' => false,
                    'message' => 'Failed',
                    'data' =>$response,
                ], 500);
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

    public function OrderPickup(Request $request){
        try {
            $request->validate([
                'pickup_date'     => 'required',
                'pickup_time'     => 'required|string|max:255',
                'pickup_vehicle'  => 'required|string|max:255',
                'orders'          => 'required|array|min:1',
                'orders.*.order_no' => 'required|string|max:255',
            ]);

            $response = Http::withHeader(
                'x-api-key', $this->key
            )->post($this->url . 'order/api/v1/pickup/request', [
                'pickup_date'    => $request->input('pickup_date'),
                'pickup_time'    => $request->input('pickup_time'),
                'pickup_vehicle' => $request->input('pickup_vehicle'),
                'orders'         => $request->input('orders'),
            ]);
            if ($response->successful()) {
                $response = json_decode($response->body(), true);
                return response()->json([
                    'status' => true,
                    'message' => 'Success',
                    'data' => $response['data'],
                ]);
            } else {
                $response = json_decode($response->body(), true);
                return response()->json([
                    'status' => false,
                    'message' => 'Failed',
                    'data' =>$response,
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

    public function LabelOrder(Request $request){
        try {
            $request->validate([
                'order_no' => 'required|string|max:255',
                'page' => 'nullable|string',
            ]);

            $url = $this->url . 'order/api/v1/orders/print-label';
            if ($request->input('order_no')) {
                $url .= '?order_no=' . $request->input('order_no');
            }
            if ($request->input('page')) {
                $url .= '&page=' . $request->input('page');
            }
            $response = Http::withHeader(
                'x-api-key', $this->key
            )->post($url);
            
            if ($response->successful()) {
                $response = json_decode($response->body(), true);
                return response()->json([
                    'status' => true,
                    'message' => 'Success',
                    'data' => $response['data'],
                ]);
            } else {
                $response = json_decode($response->body(), true);
                return response()->json([
                    'status' => false,
                    'message' => 'Failed',
                    'data' =>$response,
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

    public function CancelOrder(Request $request){
        try {
            $request->validate([
                'order_no' => 'required|string|max:255',
            ]);

            $response = Http::withHeader(
                'x-api-key', $this->key
            )->put($this->url . 'order/api/v1/orders/cancel', [
                'order_no' => $request->input('order_no'),
            ]);
            if ($response->successful()) {
                $response = json_decode($response->body(), true);
                return response()->json([
                    'status' => true,
                    'message' => 'Success',
                    'data' => $response['data'],
                ]);
            } else {
                $response = json_decode($response->body(), true);
                return response()->json([
                    'status' => false,
                    'message' => 'Failed',
                    'data' =>$response,
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