<?php

namespace App\Extension;

use App\Models\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class RajaOngkirController extends Controller{

    private $url = env('RAJA_ONGKIR_URL','https://api-sandbox.collaborator.komerce.id/');
    private $key = env('RAJA_ONGKIR_KEY','CBhUvjAne0dd4812baa711285pfaOpyN');
    
    public function getDestination(Request $request){
        try {
            $response = Http::get($this->url . 'tariff/api/v1/destination/', [
                
                'keyword' => $request->query('province'),
            ])->withHeader(
                'X-Api-Key', $this->key
            );
            $response = json_decode($response->body(), true);
            if ($response['status'] == 200) {
                return response()->json([
                    'status' => true,
                    'message' => 'Success',
                    'data' => $response['data'],
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed',
                    'data' => $response['message'],
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
            $response = Http::get($this->url . 'tariff/api/v1/orders/history-airway-bill', [
                'shipping'=> $request->query('shipping'),
                'airway_bill' => $request->query('airway_bill'),
            ])->withHeader(
                'X-Api-Key', $this->key
            );
            $response = json_decode($response->body(), true);
            if ($response['status'] == 200) {
                return response()->json([
                    'status' => true,
                    'message' => 'Success',
                    'data' => $response['data'],
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed',
                    'data' => $response['message'],
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
            $response = Http::get($this->url . 'tariff/api/v1/orders/detail', [
                'order_no'=> $request->query('order_no'),
            ])->withHeader(
                'X-Api-Key', $this->key
            );
            $response = json_decode($response->body(), true);
            if ($response['status'] == 200) {
                return response()->json([
                    'status' => true,
                    'message' => 'Success',
                    'data' => $response['data'],
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed',
                    'data' => $response['message'],
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
            $response = Http::get($this->url . 'tariff/api/v1/shipping-cost', [
                'shipper_destination_id'=> $request->query('shipper_destination_id'),
                'receiver_destination_id' => $request->query('receiver_destination_id'),
                'weight' => $request->query('weight'),
                'item_value' => $request->query('item_value'),
            ])->withHeader(
                'X-Api-Key', $this->key
            );
            $response = json_decode($response->body(), true);
            if ($response['status'] == 200) {
                return response()->json([
                    'status' => true,
                    'message' => 'Success',
                    'data' => $response['data'],
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed',
                    'data' => $response['message'],
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
                'order_date'               => 'required|date_format:Y-m-d H:i:s',
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

            $response = Http::post($this->url . 'order/api/v1/orders/store', [
            'brand_name'              => $request->input('brand_name'),
            'shipper_name'            => $request->input('shipper_name'),
            'shipper_phone'           => $request->input('shipper_phone'),
            'shipper_address'         => $request->input('shipper_address'),
            'shipper_email'           => $request->input('shipper_email'),
            'receiver_name'           => $request->input('receiver_name'),
            'receiver_phone'          => $request->input('receiver_phone'),
            'receiver_address'        => $request->input('receiver_address'),
            'shipping'                => $request->input('shipping'),
            'shipping_type'           => $request->input('shipping_type'),
            'payment_method'          => $request->input('payment_method'),
            'shipping_cost'           => $request->input('shipping_cost'),
            'shipping_cashback'       => $request->input('shipping_cashback'),
            'service_fee'             => $request->input('service_fee'),
            'additional_cost'         => $request->input('additional_cost'),
            'grand_total'             => $request->input('grand_total'),
            'cod_value'               => $request->input('cod_value'),
            'insurance_value'         => $request->input('insurance_value'),
            'order_details'           => $request->input('order_details'),
            ])->withHeader(
                'X-Api-Key', $this->key
            );
            $response = json_decode($response->body(), true);
            if ($response['status'] == 200) {
                return response()->json([
                    'status' => true,
                    'message' => 'Success',
                    'data' => $response['data'],
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed',
                    'data' => $response['message'],
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

    public function OrderPickup(Request $request){
        try {
            $request->validate([
                'pickup_date'     => 'required|date_format:Y-m-d',
                'pickup_time'     => 'required|string|max:255',
                'pickup_vehicle'  => 'required|string|max:255',
                'orders'          => 'required|array|min:1',
                'orders.*.order_no' => 'required|string|max:255',
            ]);

            $response = Http::post($this->url . 'order/api/v1/pickup/request', [
                'pickup_date'    => $request->input('pickup_date'),
                'pickup_time'    => $request->input('pickup_time'),
                'pickup_vehicle' => $request->input('pickup_vehicle'),
                'orders'         => $request->input('orders'),
            ])->withHeader(
                'X-Api-Key', $this->key
            );
            $response = json_decode($response->body(), true);
            if ($response['status'] == 200) {
                return response()->json([
                    'status' => true,
                    'message' => 'Success',
                    'data' => $response['data'],
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed',
                    'data' => $response['message'],
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
            $response = Http::post($url)->withHeader(
                'X-Api-Key', $this->key
            );
            
            $response = json_decode($response->body(), true);
            if ($response['status'] == 200) {
                return response()->json([
                    'status' => true,
                    'message' => 'Success',
                    'data' => $response['data'],
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed',
                    'data' => $response['message'],
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

            $response = Http::put($this->url . 'order/api/v1/orders/cancel', [
                'order_no' => $request->input('order_no'),
            ])->withHeader(
                'X-Api-Key', $this->key
            );
            $response = json_decode($response->body(), true);
            if ($response['status'] == 200) {
                return response()->json([
                    'status' => true,
                    'message' => 'Success',
                    'data' => $response['data'],
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Failed',
                    'data' => $response['message'],
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