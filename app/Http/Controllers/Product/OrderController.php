<?php

namespace App\Http\Controllers\Product;

use App\Models\Inventory;
use App\Models\Item_Sold;
use App\Models\Order;
use App\Models\Product;
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
     * 1. Validasi input shipper, receiver, payment, cart.
     * 2. Proses pembuatan order via ShippingRepositories::processOrder().
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
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
                'payment_method'          => 'nullable|string|in:prepaid,cod',
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

            Log::info('OrderController@store success', ['order_id' => $order->id]);

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
