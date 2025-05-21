<?php

namespace App\Repositories\Shipping;

use App\Models\Branch;
use App\Models\Client;
use App\Models\Product;
use App\Service\Shipping\RajaOngkirService;
use App\Models\Order;
use App\Models\Inventory;
use App\Models\Item_Sold;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ShippingRepositories
{
    public function formatOrderDetails($order)
    {
        try {
            // Get the order details from the order object
            // and format them into the desired structure
            $order->order_details = array_map(function ($detail) {
                $product = Product::find($detail->product_id);
                $inventory = $product
                    ? Inventory::find($product->inventory_id)
                    : null;

                if ($product) {
                    $detail->product_name         = $product->name;
                    $detail->product_variant_name = $product->id;
                    $detail->product_price        = $detail->price;
                    $detail->product_width        = $product->width   ?? $inventory->width   ?? 0;
                    $detail->product_height       = $product->height  ?? $inventory->height  ?? 0;
                    $detail->product_weight       = $product->weight  ?? $inventory->weight  ?? 0;
                    $detail->product_length       = $product->length  ?? $inventory->length  ?? 0;
                }

                return [
                    "product_name"         => $detail->product_name,
                    "product_variant_name" => $detail->product_variant_name,
                    "product_price"        => $detail->product_price,
                    "product_width"        => $detail->product_width,
                    "product_height"       => $detail->product_height,
                    "product_weight"       => $detail->product_weight,
                    "product_length"       => $detail->product_length,
                    "qty"                  => $detail->qty,
                    "subtotal"             => $detail->subtotal,
                ];
            }, $order->order_details);

            $branch = Branch::find($order->branch_id);
            $client = Client::find($order->client_id);

            return [
                'order_date'               => now()->toDateString(),
                'brand_name'               => config('app.name'),

                'shipper_name'           => $branch->name,
                'shipper_phone'          => $branch->phone,
                'shipper_destination_id' => $order->destination_id,
                'shipper_address'        => $branch->address,
                'shipper_email'          => $branch->email,

                "receiver_name"            => $client->receiver_name,
                "receiver_phone"           => $client->receiver_phone,
                "receiver_destination_id"  => $order->receiver_destination_id,
                "receiver_address"         => $client->receiver_address,
                "shipping"                 => $order->shipping,
                "shipping_type"            => $order->shipping_type,
                "payment_method"           => $order->payment_method ? $order->payment_method : 'prepaid',
                "shipping_cost"            => $order->shipping_cost,
                "shipping_cashback"        => $order->shipping_cashback,
                "service_fee"              => $order->service_fee,
                "additional_cost"          => $order->additional_cost,
                "grand_total"              => $order->grand_total,
                "cod_value"                => $order->cod_value ?? 0,
                "insurance_value"          => $order->insurance_value ?? 0,
                "order_details"            => $order->order_details,
            ];
        } catch (\Exception $e) {
            Log::error("Error formatting order details: " . $e->getMessage());
            throw $e;
        }
    }

    public function processOrder(array $data)
    {
        try {
            // 1. Build the RajaOngkir payload
            $payload = $this->formatOrderDetails((object) $data);

            // 2. Send to RajaOngkir
            $rajaResponse = (new RajaOngkirService())->storeOrder($payload);
            if (! $rajaResponse['status']) {
                return $rajaResponse;
            }

            // 3. Persist locally and adjust stock
            $result = DB::transaction(function() use ($data, $rajaResponse) {
                $order = Order::create([
                    'order_number'   => $rajaResponse['data']['order_no'],
                    'transaction_id' => $data['transaction_id'] ?? null,
                    'client_id'      => $data['client_id'],
                    'courier'        => $data['shipping_name'],
                    'shipping_cost'  => $data['shipping_cost'],
                    'payment'        => $data['grand_total'] ?? 0,
                    'coins'          => $data['coins_payment'] ?? 0,
                    'status'         => 'pending',
                ]);

                foreach ($data['cart'] as $item) {
                    // decrement inventory
                    Inventory::where('id', $item['product_id'])
                             ->decrement('stock', $item['quantity']);

                    // record sold item
                    Item_Sold::create([
                        'inventory_id' => $item['product_id'],
                        'quantity'     => $item['quantity'],
                        'sold_date'    => Carbon::now(),
                        'is_deleted'   => false,
                    ]);

                    // Update points on client
                    if($item['points'] && $item['points'] > 0) {
                        Client::where('id', $data['client_id'])
                                ->increment('points', $item['points']);
                    }
                }

                $total_payment = $item['grand_total'] - ($data['coins_payment'] ?? 0);
                // Update saldo on client
                Client::where('id', $data['client_id'])
                        ->decrement('saldo', $total_payment);
                
                // Update coin on client
                if($data['coins_payment'] && $data['coins_payment'] > 0) {
                    Client::where('id', $data['client_id'])
                            ->decrement('coins', $data['coins_payment']);
                }

                

                // Update Cash on Branch
                Branch::where('id', $data['branch_id'])
                        ->increment('cash', $total_payment);

                return [
                    'status'           => true,
                    'message'          => 'Order created and stock updated',
                    'data'             => $order,
                    'shipping_details' => $rajaResponse['data'],
                ];
            });

            return $result;
        } catch (\Exception $e) {
            Log::error("Error processing order: " . $e->getMessage());
            return [
                'status'  => false,
                'message' => 'Failed to process order: ' . $e->getMessage(),
            ];
        }
    }
}