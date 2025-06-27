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
            $order->order_details = $order->order_details ?? [];
            $totalPrice = 0;
            
            $order->order_details = collect($order->cart)
                ->map(function ($detail) use (&$totalPrice) {
                    // retrieve product & inventory
                    // dd($detail);
                    $product   = Product::find($detail['product_id']);
                    $inventory = $product
                        ? Inventory::find($product->inventory_id)
                        : null;

                    $price    = $detail['price']    ?? 0;
                    $qty      = $detail['qty']      ?? 1;
                    $subtotal = $detail['subtotal'] ?? ($price * $qty);

                    if ($product) {
                        // accumulate total price
                        $totalPrice += $price * $qty;
                    }

                    return [
                        'product_name'         => $product->name ?? $detail['product_name'] ?? 'Unknown Product',
                        'product_variant_name' => $product->name . "-" . $product->id   ?? null,
                        'product_price'        => $price,
                        'product_width'        => $product->width  ?? $inventory->width   ?? 1,
                        'product_height'       => $product->height ?? $inventory->height  ?? 1,
                        'product_weight'       => $product->weight ?? $inventory->weight  ?? 1,
                        'product_length'       => $product->length ?? $inventory->length  ?? 1,
                        'qty'                  => $qty,
                        'subtotal'             => $subtotal,
                    ];
                })
                ->values()
                ->toArray();

            $branch = Branch::find($order->branch_id) ?? new Branch();
            $client = Client::find($order->client_id) ?? new Client();
            $order->grand_total = $order->grand_total ?? $totalPrice;
            // dd(
            //     [
            //     'order_date'               => now()->toDateString(),
            //     'brand_name'               => config('app.name'),

            //     'shipper_name'           => $branch->name,
            //     'shipper_phone'          => $branch->phone,
            //     'shipper_destination_id' => $order->shipper_destination_id,
            //     'shipper_address'        => $branch->address,
            //     'shipper_email'          => $branch->email ?? "someone@mail.com",

            //     "receiver_name"            => $client->name,
            //     "receiver_phone"           => $client->phone,
            //     "receiver_destination_id"  => $order->receiver_destination_id,
            //     "receiver_address"         => $client->address,
            //     "shipping"                 => $order->shipping,
            //     "shipping_type"            => $order->shipping_type,
            //     "payment_method"           => $order->payment_method ? $order->payment_method : 'COD',
            //     "shipping_cost"            => $order->shipping_cost ?? 1,
            //     "shipping_cashback"        => $order->shipping_cashback ?? 1,
            //     "service_fee"              => $order->service_fee ?? 2500,
            //     "additional_cost"          => $order->additional_cost ?? 1,
            //     "grand_total"              => $order->grand_total ?? 1,
            //     "cod_value"                => $order->grand_total ?? 1,
            //     "insurance_value"          => $order->insurance_value ?? 1,
            //     "order_details"            => $order->order_details,
            // ]
            // );
            $service_fee = $order->service_fee ?? round($order->grand_total * 0.028); // Default service fee if not set
            return [
                'order_date'               => now()->toDateString(),
                'brand_name'               => config('app.name'),

                'shipper_name'           => $branch->name,
                'shipper_phone'          => $branch->phone,
                'shipper_destination_id' => $order->shipper_destination_id,
                'shipper_address'        => $branch->address,
                'shipper_email'          => $branch->email ?? "someone@mail.com",

                "receiver_name"            => $client->name,
                "receiver_phone"           => $client->phone,
                "receiver_destination_id"  => $order->receiver_destination_id,
                "receiver_address"         => $client->address,
                "shipping"                 => $order->shipping,
                "shipping_type"            => $order->shipping_type,
                "payment_method"           => $order->payment_method ? $order->payment_method : 'COD',
                "shipping_cost"            => $order->shipping_cost ?? 1,
                "shipping_cashback"        => $order->shipping_cashback ?? 1,
                "service_fee"              => $service_fee,
                "additional_cost"          => $order->additional_cost ?? 1,
                "grand_total"              => $order->grand_total ?? 1,
                "cod_value"                => $order->grand_total ?? 1,
                "insurance_value"          => $order->insurance_value ?? 1,
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
            $rajaOngkirService = new RajaOngkirService();
            // 1. Build the RajaOngkir payload
            $payload = $this->formatOrderDetails((object) $data);

            // 2. Send to RajaOngkir
            $rajaResponse = $rajaOngkirService->storeOrder($payload);
            Log::info('RajaOngkir response', ['response' => $rajaResponse]);
            if (! $rajaResponse['status']) {
                return $rajaResponse;
            }

            // 3. Persist locally and adjust stock
            $result = DB::transaction(function() use ($data, $rajaResponse) {
                $order = Order::create([
                    'order_number'   => $rajaResponse['data']['order_no'],
                    'transaction_id' => $data['transaction_id'] ?? null,
                    'client_id'      => $data['client_id'],
                    'courier'        => $data['shipping_type'],
                    'shipping_cost'  => $data['service_fee'] ?? 0,
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
                    
                    $client = Client::find($data['client_id']);
                    // Update points on client
                    if(isset($item['points']) && $item['points'] && $item['points'] > 0) {
                        if($client->points>=0){
                            Client::where('id', $data['client_id'])
                                    ->increment('points', $item['points']);
                        }
                        else{
                            $client->points = $item['points'] ?? 0;
                            $client->save();
                        }
                    }
                }

                $total_payment = $data['grand_total'] - ($data['coins_payment'] ?? 0);
                // Update saldo on client
                Client::where('id', $data['client_id'])
                        ->decrement('saldo', $total_payment);
                
                // Update coin on client
                if($data['coins_payment'] && $data['coins_payment'] > 0) {
                    Client::where('id', $data['client_id'])
                            ->decrement('coins', $data['coins_payment'] ?? 0);
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
            throw $e;
        }
    }
}