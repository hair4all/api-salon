<?php

namespace App\Http\Controllers\Product;

use App\Models\Branch;
use App\Models\Client;
use App\Models\Inventory;
use App\Models\Item_Sold;
use App\Models\Order;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\Service_Sold;
use App\Models\Payment_Tokens;
use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Service\Shipping\RajaOngkirService;
use App\Repositories\Shipping\ShippingRepositories;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    private RajaOngkirService $rajaongkirservice;
    private ShippingRepositories $shippingrepositories;

    /**
     * OrderController constructor.
     * Inisialisasi service dan repositories yang diperlukan.
     */
    public function __construct()
    {
        $this->rajaongkirservice   = new RajaOngkirService();
        $this->shippingrepositories = new ShippingRepositories();
    }

    /**
     * Display a listing of orders.
     *
     * @param  Request  $request  Parameter query: transaction_id (optional), limit (optional)
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        Log::info('OrderController@index called', $request->all());

        try {
            $query = Order::where('is_deleted', false);

            // Filter by transaction_id jika ada
            if ($request->filled('transaction_id')) {
                $query->where('transaction_id', $request->transaction_id);
            }

            $limit = $request->query('limit', 10);
            $paginated = $query->paginate($limit);

            Log::info('OrderController@index success', ['count' => $paginated->total()]);

            return response()->json($paginated);
        } catch (\Throwable $th) {
            Log::error('OrderController@index error', [
                'message' => $th->getMessage(),
                'trace'   => $th->getTraceAsString(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Gagal mengambil daftar order',
                'data'    => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created order.
     * 
     * Algoritma:
     * 1. Validasi input shipper, receiver, payment, cart.
     * 2. Proses pembuatan order via ShippingRepositories::processOrder().
     */

     public function store(Request $request)
        {
            try {
                Log::info('OrderController@store called', $request->all());

                // Validasi data utama
                $request->validate([
                    'order_number'   => 'nullable|string|max:255|unique:orders,order_number',
                    'transaction_id' => 'nullable|string|exists:transactions,id',
                    'client_id'      => 'required|integer|exists:clients,id',
                    'branch_id'      => 'required|integer|exists:branches,id',
                    'courier'        => 'required|string|max:255',
                    'payment'        => 'required|numeric|min:0',
                    'coins'          => 'nullable|numeric|min:0',
                    'shipping_cost'  => 'required|numeric|min:0',
                    'status'         => 'nullable|string|max:255',
                ]);

                // Panggil service untuk memproses order
                $order = Order::create([
                    'order_number'   => $request->order_number,
                    'transaction_id' => $request->transaction_id,
                    'client_id'      => $request->client_id,
                    'branch_id'      => $request->branch_id,
                    'courier'        => $request->courier,
                    'payment'        => $request->payment,
                    'coins'          => $request->coins ?? 0,
                    'shipping_cost'  => $request->shipping_cost,
                    'status'         => $request->status ?? 'pending',
                ]);

                Log::info('OrderController@store success', ['order' => $order]);

                return response()->json([
                    'status'  => true,
                    'message' => 'Order created successfully',
                    'data'    => $order,
                ], 201);
            } catch (\Illuminate\Validation\ValidationException $ve) {
                // Khusus validasi
                Log::warning('OrderController@store validation failed', [
                    'errors' => $ve->errors(),
                ]);

                return response()->json([
                    'status'  => false,
                    'message' => 'Validation error',
                    'data'    => $ve->errors(),
                ], 422);
            } catch (\Throwable $th) {
                Log::error('OrderController@store error', [
                    'message' => $th->getMessage(),
                    'trace'   => $th->getTraceAsString(),
                ]);

                return response()->json([
                    'status'  => false,
                    'message' => 'Gagal membuat order',
                    'data'    => $th
                        ->getMessage(),
                ], 500);
            }
        }

    /**
     * Store a newly created order in client-side
     *
     * 1. Validasi input shipper, receiver, payment, cart.
     * 2. Proses pembuatan order via ShippingRepositories::processOrder().
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkout(Request $request)
    {
        Log::info('OrderController@store called', $request->all());

        try {
            // Validasi data utama
            $request->validate([
                'branch_id'               => 'required|integer',
                'shipper_destination_id'  => 'required|integer',
                'client_id'               => 'required|integer',
                'receiver_destination_id' => 'required|integer',
                'shipping'                => 'required|string',
                'shipping_type'           => 'required|string',
                'payment_method'          => 'nullable|string',
                'additional_cost'         => 'nullable|numeric|min:0',
                'grand_total'             => 'required|numeric|min:0',
                // 'total_payment'           => 'required|numeric|min:0',
                'coins_payment'            => 'nullable|numeric|min:0',
                'cart'                    => 'required|array',
                'cart.*.product_id'       => 'required|integer|exists:products,id',
                'cart.*.name'             => 'required|string',
                'cart.*.price'            => 'required|numeric',
                'cart.*.quantity'         => 'required|integer|min:1',
            ]);

            // Panggil service untuk memproses order
            $order = $this->shippingrepositories->processOrder($request->all());
            Log::info('OrderController@store success', ['order' => $order]);
            if (!$order['status']) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Gagal membuat order',
                    'data'    => $order,
                ], 500);
            }
            return response()->json([
                'status'  => true,
                'message' => 'Order created successfully',
                'data'    => $order,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            // Khusus validasi
            Log::warning('OrderController@store validation failed', [
                'errors' => $ve->errors(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Validation error',
                'data'    => $ve->errors(),
            ], 422);
        } catch (\Throwable $th) {
            Log::error('OrderController@store error', [
                'message' => $th->getMessage(),
                'trace'   => $th->getTraceAsString(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Gagal membuat order',
                'data'    => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Checkout V2
     * 
     * 1. Masukan List Produk / Jasa
     * 2. Masukan ID Pelanggan
     * 3. Masukan Shipping Cost Jika ada
     * 4. Masukan Token Pembayaran 
     * 5. Lakukan proses checkout dengan menambahkan ke Item_Sold jika Product dan Service SOld jika Jasa
     * 
     */
    public function checkoutV2(Request $request){
        Log::info('OrderController@checkoutV2 called');
        
        try{
            /*
            {
                products:[
                    {
                        product_id: 1,
                        name: 'Product 1',
                        price: 10000,
                        quantity: 2
                    },
                    {
                        product_id: 2,
                        name: 'Product 2',
                        price: 20000,
                        quantity: 1
                    }
                ],
                service: [
                    {
                        service_id: 1,
                        name: 'Service 1',
                        price: 50000,
                        quantity: 1
                    }
                ],
                client_id: 1,
                shipping_cost: 5000,
                payment_token: 'token123',
                total_payment: 70000,
                payment_method: 'cash',
            }
            */
            $request->validate([
                'products' => 'nullable|array',
                'products.*.product_id' => 'nullable|integer|exists:products,id',
                'products.*.name' => 'nullable|string',
                'products.*.price' => 'nullable|numeric|min:0',
                'products.*.quantity' => 'nullable|integer|min:1',
                'service' => 'nullable|array',
                'service.*.service_id' => 'nullable|integer|exists:services,id',
                'service.*.name' => 'nullable|string',
                'service.*.price' => 'nullable|numeric|min:0',
                'service.*.quantity' => 'nullable|integer|min:1',
                'client_id' => 'nullable|integer|exists:clients,id',
                'shipping_cost' => 'nullable|numeric|min:0',
                'total_payment' => 'nullable|numeric|min:0',
                'payment_token' => 'nullable|string',
                'payment_method' => 'nullable|string|max:255',
            ]);

            $payload = $request->all();
            
            // Validasi token pembayaran
            if ($request->filled('payment_token')) {
                $token = Payment_Tokens::where('token', $request->payment_token)->first();
                if (!$token) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid payment token',
                    ], 400);
                }
                if ($token->expiry) {
                    // Gunakan Carbon untuk parsing dan perbandingan waktu dengan timezone
                    $expiry = \Carbon\Carbon::parse($token->expiry)->setTimezone(config('app.timezone', 'UTC'));
                    $now = \Carbon\Carbon::now(config('app.timezone', 'UTC'));
                    if ($expiry->lt($now)) {
                        return response()->json([
                            'status' => false,
                            'message' => 'Payment token has expired',
                        ], 400);
                    }
                }
                if(!$token->user_id || $token->user_id != $request->client_id) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Payment token does not match client',
                    ], 400);
                }
            }

            // Proses pembuatan order
            DB::beginTransaction();
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'client_id' => $request->client_id,
                'shipping_cost' => $request->shipping_cost ?? 0,
                'payment' => $request->total_payment ?? 0,
                'status' => 'pending',
            ]);

            
            if($payload['products'] ?? false){
                foreach ($payload['products'] as $product) {
                    $item_sold = Item_Sold::create([
                        'order_id' => $order->id,
                        'inventory_id' => $product['product_id'],
                        'quantity' => $product['quantity'],
                        'sold_date' => now(),
                    ]);
                }
            }

            if($payload['service'] ?? false){
                foreach ($payload['service'] as $service) {
                    $service_sold = Service_Sold::create([
                        'order_id' => $order->id,
                        'service_id' => $service['service_id'],
                        'quantity' => $service['quantity'],
                        'sold_date' => now(),
                    ]);
                }
            }

            // Hapus token pembayaran
            if ($request->filled('payment_token')) {
                $token->delete();
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Order created successfully',
                'data' => $order,
            ], 201);

        }
        catch (\Throwable $th) {
            Log::error('OrderController@checkoutV2 error', [
                'message'  => $th->getMessage(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Gagal melakukan checkout',
                'data'    => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified order.
     *
     * @param  int  $id Order ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        Log::info('OrderController@show called', ['order_id' => $id]);

        try {
            $order = Order::where('is_deleted', false)->findOrFail($id);

            Log::info('OrderController@show success', ['order_id' => $id]);

            return response()->json([
                'status'  => true,
                'message' => 'Order retrieved',
                'data'    => $order,
            ]);
        } catch (\Throwable $th) {
            Log::error('OrderController@show error', [
                'order_id' => $id,
                'message'  => $th->getMessage(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Gagal mengambil order',
                'data'    => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified order.
     *
     * @param  Request  $request
     * @param  int      $id      Order ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        Log::info('OrderController@update called', array_merge(['order_id' => $id], $request->all()));

        try {
            $request->validate([
                // 'order_number'   => "nullable|string|max:255|unique:orders,order_number,{$id}",
                'transaction_id' => 'nullable|string|exists:transactions,id',
                'courier'        => 'nullable|string|max:255',
                'shipping_cost'  => 'nullable|string',
                'status'         => 'nullable|string|max:255',
            ]);

            $order = Order::findOrFail($id);
            $order->update($request->only([
                'order_number',
                'transaction_id',
                'courier',
                'shipping_cost',
                'status',
            ]));

            Log::info('OrderController@update success', ['order_id' => $id]);

            return response()->json([
                'status'  => true,
                'message' => 'Order updated successfully',
                'data'    => $order,
            ]);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            Log::warning('OrderController@update validation failed', ['errors' => $ve->errors()]);

            return response()->json([
                'status'  => false,
                'message' => 'Validation error',
                'data'    => $ve->errors(),
            ], 422);
        } catch (\Throwable $th) {
            Log::error('OrderController@update error', [
                'order_id' => $id,
                'message'  => $th->getMessage(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Gagal memperbarui order',
                'data'    => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Soft delete the specified order.
     *
     * @param  int  $id Order ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(int $id)
    {
        Log::info('OrderController@destroy called', ['order_id' => $id]);

        try {
            $order = Order::where('is_deleted', false)->findOrFail($id);
            $order->update(['is_deleted' => true]);

            Log::info('OrderController@destroy success', ['order_id' => $id]);

            return response()->json([
                'status'  => true,
                'message' => 'Order deleted successfully',
            ]);
        } catch (\Throwable $th) {
            Log::error('OrderController@destroy error', [
                'order_id' => $id,
                'message'  => $th->getMessage(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Gagal menghapus order',
                'data'    => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Cancel the specified order.
     *
     * @param  int  $order_id Order ID
     * @return \Illuminate\Http\JsonResponse
     * 
     * Algoritma:
     * 1. Validasi order_id
     * 2. Ambil order dari database
     * 3. Panggil RajaOngkirService::cancelOrder()
     * 4. Jika berhasil, update status order ke canceled
     * 5. Ambil detail order dari RajaOngkir
     * 6. Update inventory dan sold items
     * 7. Update coins dan saldo client
     * 8. Update cash on branch
     * 9. Kembalikan response JSON
     */
    public function cancelOrder($order_id){
        Log::info('OrderController@cancelOrder called', ['order_id' => $order_id]);

        try {
            $order = Order::where('is_deleted', false)->where('id', $order_id)->first();

            // Check if order exists
            if (!$order) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Order not found',
                    'data'    => null,
                ], 404);
            }

            // Cancel order via RajaOngkir
            $rajaResponse = $this->rajaongkirservice->cancelOrder($order->order_number);

            // Check if RajaOngkir response is successful
            if ($rajaResponse['status'] == false) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Gagal membatalkan order',
                    'data'    => $rajaResponse['message'],
                ], 500);
            }

            // Update order status to canceled
            $order->update(['status' => 'canceled']);

            // Get order details from RajaOngkir
            $order_check = $this->rajaongkirservice->getOrderDetail($order->order_number);

            // Initialize before and after arrays
            $before = [];
            $after  = [];

            // Update inventory and sold items
            foreach ($order_check["order_details"] as $item) {
                // Get inventory item from product
                $product = Product::where('id', $item->product_variant_name)->first();
                
                if (!$product) {
                    return response()->json([
                        'status'  => false,
                        'message' => 'Gagal menemukan item di inventory',
                        'data'    => null,
                    ], 404);
                }

                // Store inventory data before update
                $before[] = [
                    'id'    => $product->inventory_id,
                    'stock' => $product->stock,
                ];

                $invetory_id = $product->inventory_id;

                // Increment inventory
                Inventory::where('id', $invetory_id)
                         ->increment('stock', $item->qty);

                // Delete sold item record
                Item_Sold::where('inventory_id', $invetory_id)
                         ->where('sold_date', now())
                         ->delete();

                // Store inventory data after update
                $after[] = [
                    'id'    => $invetory_id,
                    'stock' => Inventory::where('id', $invetory_id)->first()->stock,
                ];
            }

            // Update coins and saldo of client
            $client = Client::where('id', $order->client_id)->first();
            if ($client) {
                $client->increment('coins', $order->coins_payment);
                $client->increment('saldo', $order->grand_total);
            }

            // Update cash on branch
            $branch = Branch::where('id', $order->branch_id)->first();

            if ($branch) {
                $cash_deduction = $order->payment - ($order->coins ?? 0); 
                $branch->decrement('cash', $cash_deduction);
            }

            Log::info('OrderController@cancelOrder success', ['order_id' => $order_id]);

            return response()->json([
                'status'  => true,
                'message' => 'Order canceled successfully',
                'data'    => $rajaResponse['data'],
                'before'  => $before,
                'after'   => $after,
            ]);
        } catch (\Throwable $th) {
            Log::error('OrderController@cancelOrder error', [
                'order_id' => $order_id,
                'message'  => $th->getMessage(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Gagal membatalkan order',
                'data'    => $th->getMessage(),
            ], 500);
        }
    }

    /**
     * Calculate and show checkout data (ongkir, estimasi, dll).
     *
     * Algoritma:
     * 1. Validasi destinasi dan isi cart
     * 2. Hitung total berat: sum(item.weight * item.quantity)
     * 3. Hitung total harga: sum(item.price * item.quantity)
     * 4. Panggil RajaOngkirService::getShippingCost()
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function showCheckout(Request $request)
    {
        Log::info('OrderController@showCheckout called', $request->all());

        try {
            // Validasi destinasi & cart
            $request->validate([
                'shipper_destination_id'  => 'required|string',
                'receiver_destination_id' => 'required|string',
                'cart'                 => 'required|array',
                'cart.*.product_id'    => 'required|integer|exists:products,id',
                'cart.*.weight'        => 'required|numeric|min:0',
                'cart.*.price'         => 'required|numeric|min:0',
                'cart.*.quantity'      => 'required|integer|min:1',
            ]);

            // Hitung berat total
            $weight = collect($request->cart)
                ->reduce(fn($carry, $item) => $carry + ($item['weight'] * $item['quantity']), 0);

            // Hitung nilai barang total
            $itemValue = collect($request->cart)
                ->reduce(fn($carry, $item) => $carry + ($item['price'] * $item['quantity']), 0);
            
            // Hitung nilai limit coins
            $coinsLimit = collect($request->cart)
                ->reduce(fn($carry, $item) => $carry + ($item['coins'] * $item['quantity']), 0); 

            Log::info('OrderController@showCheckout calculations', [
                'weight'     => $weight,
                'item_value' => $itemValue,
            ]);

            // Panggil service ongkir
            $shippingData = $this->rajaongkirservice->getShippingCost([
                'shipper_destination_id'   => $request->shipper_destination,
                'receiver_destination_id'  => $request->receiver_destination,
                'weight'                   => $weight,
                'item_value'               => $itemValue,
            ]);


            Log::info('OrderController@showCheckout success');
            $shippingData = $shippingData['data'] ?? null;
            if (!$shippingData) {
                return response()->json([
                    'status'  => false,
                    'message' => 'Gagal menghitung ongkir',
                    'data'    => null,
                ], 500);
            }

            $shippingData['coins_limit'] = $coinsLimit;

            return response()->json([
                'status'  => true,
                'message' => 'Checkout data retrieved',
                'data'    => $shippingData,
            ]);
        } catch (\Illuminate\Validation\ValidationException $ve) {
            Log::warning('OrderController@showCheckout validation failed', ['errors' => $ve->errors()]);

            return response()->json([
                'status'  => false,
                'message' => 'Validation error',
                'data'    => $ve->errors(),
            ], 422);
        } catch (\Throwable $th) {
            Log::error('OrderController@showCheckout error', [
                'message' => $th->getMessage(),
                'trace'   => $th->getTraceAsString(),
            ]);

            return response()->json([
                'status'  => false,
                'message' => 'Gagal menghitung ongkir',
                'data'    => $th->getMessage(),
            ], 500);
        }
    }
}
