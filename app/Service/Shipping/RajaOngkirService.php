<?php

namespace App\Service\Shipping;

use Illuminate\Support\Facades\Http;


class RajaOngkirService
{
    protected $url;
    protected $key;

    public function __construct()
    {
        $this->url = env('RAJA_ONGKIR_URL', 'https://api-sandbox.collaborator.komerce.id/');
        $this->key = env('RAJA_ONGKIR_KEY', 'CBhUvjAne0dd4812baa711285pfaOpyN');
    }

    public function getDestination(string $keyword): array
    {
        $response = Http::withHeader('x-api-key', $this->key)
            ->get($this->url . 'tariff/api/v1/destination/search', [
                'keyword' => $keyword,
            ]);

        return $this->formatResponse($response);
    }

    public function getHistoryAirwayBill(string $shipping, string $airwayBill): array
    {
        $response = Http::withHeader('x-api-key', $this->key)
            ->get($this->url . 'order/api/v1/orders/history-airway-bill', [
                'shipping' => $shipping,
                'airway_bill' => $airwayBill,
            ]);

        return $this->formatResponse($response);
    }

    public function getOrderDetail(string $orderNo): array
    {
        $response = Http::withHeader('x-api-key', $this->key)
            ->get($this->url . 'order/api/v1/orders/detail', [
                'order_no' => $orderNo,
            ]);

        return $this->formatResponse($response);
    }

    public function getShippingCost(int $shipperDestId, int $receiverDestId, float $weight, float $itemValue): array
    {
        $response = Http::withHeader('x-api-key', $this->key)
            ->get($this->url . 'tariff/api/v1/calculate', [
                'shipper_destination_id' => $shipperDestId,
                'receiver_destination_id' => $receiverDestId,
                'weight' => $weight,
                'item_value' => $itemValue,
            ]);

        return $this->formatResponse($response);
    }

    public function storeOrder(array $payload): array
    {
        $response = Http::withHeader('x-api-key', $this->key)
            ->post($this->url . 'order/api/v1/orders/store', $payload);

        return $this->formatResponse($response);
    }

    public function orderPickup(array $payload): array
    {
        $response = Http::withHeader('x-api-key', $this->key)
            ->post($this->url . 'order/api/v1/pickup/request', $payload);

        return $this->formatResponse($response);
    }

    public function labelOrder(string $orderNo, ?string $page = null): array
    {
        $query = http_build_query(array_filter([
            'order_no' => $orderNo,
            'page' => $page,
        ]));

        $response = Http::withHeader('x-api-key', $this->key)
            ->post($this->url . 'order/api/v1/orders/print-label?' . $query);

        return $this->formatResponse($response);
    }

    public function cancelOrder(string $orderNo): array
    {
        $response = Http::withHeader('x-api-key', $this->key)
            ->put($this->url . 'order/api/v1/orders/cancel', [
                'order_no' => $orderNo,
            ]);

        return $this->formatResponse($response);
    }

    protected function formatResponse(\Illuminate\Http\Client\Response $response): array
    {
        $body = $response->json();

        if ($response->successful() && isset($body['data'])) {
            return [
                'status' => true,
                'message' => 'Success',
                'data' => $body['data'],
            ];
        }

        return [
            'status' => false,
            'message' => $body['message'] ?? 'Failed',
            'data' => $body,
        ];
    }
}
