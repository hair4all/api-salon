<?php

namespace App\Service\Shipping;

use Illuminate\Support\Facades\Http;

class RajaOngkirService
{
    private $url;
    private $key;

    public function __construct()
    {
        $this->url = env('RAJA_ONGKIR_URL', 'https://api-sandbox.collaborator.komerce.id/');
        $this->key = env('RAJA_ONGKIR_KEY', 'CBhUvjAne0dd4812baa711285pfaOpyN');
    }

    public function getDestination($search)
    {
        $response = Http::withHeader('x-api-key', $this->key)->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->get($this->url . 'tariff/api/v1/destination/search', [
                'keyword' => $search,
            ]);

        return $this->handleResponse($response);
    }

    public function getHistoryAirwayBill($shipping, $airwayBill)
    {
        $response = Http::withHeader('x-api-key', $this->key)->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->get($this->url . 'order/api/v1/orders/history-airway-bill', [
                'shipping' => $shipping,
                'airway_bill' => $airwayBill,
            ]);

        return $this->handleResponse($response);
    }

    public function getOrderDetail($orderNo)
    {
        $response = Http::withHeader('x-api-key', $this->key)->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->get($this->url . 'order/api/v1/orders/detail', [
                'order_no' => $orderNo,
            ]);

        return $this->handleResponse($response);
    }

    public function getShippingCost($params)
    {
        $response = Http::withHeader('x-api-key', $this->key)->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->get($this->url . 'tariff/api/v1/calculate', $params);

        return $this->handleResponse($response);
    }

    public function storeOrder($body)
    {
        $response = Http::withHeader('x-api-key', $this->key)->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->post($this->url . 'order/api/v1/orders/store', $body);

        return $this->handleResponse($response);
    }

    public function orderPickup($body)
    {
        $response = Http::withHeader('x-api-key', $this->key)->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->post($this->url . 'order/api/v1/pickup/request', $body);

        return $this->handleResponse($response);
    }

    public function labelOrder($orderNo, $page = null)
    {
        $url = $this->url . 'order/api/v1/orders/print-label?order_no=' . $orderNo;
        if ($page) {
            $url .= '&page=' . $page;
        }

        $response = Http::withHeader('x-api-key', $this->key)->withHeader('Content-Type', 'application/x-www-form-urlencoded')->post($url);

        return $this->handleResponse($response);
    }

    public function cancelOrder($orderNo)
    {
        $response = Http::withHeader('x-api-key', $this->key)->withHeader('Content-Type', 'application/x-www-form-urlencoded')
            ->put($this->url . 'order/api/v1/orders/cancel', [
                'order_no' => $orderNo,
            ]);

        return $this->handleResponse($response);
    }

    private function handleResponse($response)
    {
        if ($response->successful()) {
            return [
                'status' => true,
                'message' => 'Success',
                'data' => json_decode($response->body(), true)['data'],
            ];
        }

        return [
            'status' => false,
            'message' => 'Failed',
            'data' => json_decode($response->body(), true),
        ];
    }
}
