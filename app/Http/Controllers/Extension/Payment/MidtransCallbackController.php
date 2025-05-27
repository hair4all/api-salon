<?php

namespace App\Http\Controllers\Extension\Payment;

use App\Http\Controllers\Controller;

use App\Models\Client;
use App\Models\Topup_History;
use Illuminate\Http\Request;
use App\Service\Payment\MidtransService;
use Illuminate\Support\Str;
use Log;
use Symfony\Component\HttpFoundation\Response;

class MidtransCallbackController extends Controller
{
    protected MidtransService $midtrans;

    public function __construct(MidtransService $midtrans)
    {
        $this->midtrans = $midtrans;
           }


    /**
     * Generate a Snap payment token
     */
    public function getSnapToken(Request $request)
    {   
        try{
            $request->validate([
                'user_id' => 'required|integer|exists:clients,id',
                'amount' => 'required|numeric|min:1000',
            ]);
            $client = Client::find($request->input('user_id'));
            if (!$client) {
                return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
            }
            $orderId = 'topup-'.$request->input('amount')."-user".$client['id']."-".Str::random(8);
            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $request->input('amount'),
                ],
                'item_details' => [
                    [
                        'id' => $orderId,
                        'price' => $request->input('amount'),
                        'quantity' => 1,
                        'name' => 'Topup Saldo',
                    ],
                ],
                'customer_details' => [
                    'first_name' => $client->name,
                    'email' => $client->email,
                    'phone' => $client->phone,
                ],
            ];
    
            $transaction = $this->midtrans->createTransaction($params);

            if (!$transaction || !isset($transaction->token)) {
                Log::error('Failed to create Midtrans transaction for order: ' . $orderId);
                return response()->json(['message' => 'Failed to create payment token'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            // Simpan histori topup
            Topup_History::updateOrCreate(
                ['order_id' => $orderId],
                ['client_id' => $client->id, 'amount' => $request->input('amount'), 'status' => 'pending']
            );
            Log::info('Snap token generated successfully for order: ' . $orderId);
    
            return response()->json([
                'order_id' => $orderId,
                'token' => $transaction->token,
                'redirect_url' => $transaction->redirect_url,
            ]);

        }
        catch (\Throwable $th) {
            Log::error('Error generating Snap token: ' . $th->getMessage());
            return response()->json(['message' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Handle Midtrans notifications (webhook)
     */
    public function handleNotification(Request $request)
    {
        // Tangkap data dari Midtrans
        $orderId        = $request->input('order_id');
        $statusCode     = $request->input('status_code');
        $grossAmount    = $request->input('gross_amount');
        $receivedSign   = $request->input('signature_key');
        $transactionStatus = $request->input('transaction_status');

        // Validasi Signature
        $serverKey = config('services.midtrans.server_key');
        $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

        if ($receivedSign !== $expectedSignature) {
            Log::warning('Midtrans signature invalid for order: ' . $orderId);
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        // Jika pembayaran sukses
        if ($transactionStatus === 'settlement') {
            // Ekstrak user ID dari order_id: contoh "topup-10000-user5"
            if (preg_match('/user(\d+)/', $orderId, $matches)) {
                $userId = $matches[1];
                $user = Client::find($userId);

                if ($user) {
                    // Cek apakah sudah pernah diproses
                    $existing = Topup_History::where('order_id', $orderId)->first();
                    if (!$existing || $existing->status !== 'success') {

                        // Simpan histori topup (optional)
                        Topup_History::updateOrCreate(
                            ['order_id' => $orderId],
                            ['user_id' => $user->id, 'amount' => $grossAmount, 'status' => 'success']
                        );

                        // Update saldo user
                        $user->saldo += floatval($grossAmount);
                        $user->save();

                        Log::info('Topup berhasil untuk user ID ' . $user->id . ' sebesar ' . $grossAmount);
                    }
                }
            }
        }

        return response()->json(['message' => 'Webhook received'], 200);
    }

}