<?php

namespace App\Http\Controllers\Extension\Payment;

use App\Http\Controllers\Controller;

use App\Models\Client;
use App\Models\Topup_History;
use App\Models\Transaction;
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
        Log::channel('transaction')->info(sprintf(
            '[getSnapToken] Request start: user_id=%s, amount=%s',
            $request->input('user_id'),
            $request->input('amount')
        ));

        try {
            $request->validate([
                'user_id' => 'required|integer|exists:clients,id',
                'amount'  => 'required|numeric|min:1000',
            ]);

            $client = Client::find($request->input('user_id'));
            if (!$client) {
                Log::channel('transaction')->warning('[getSnapToken] User not found: ' . $request->input('user_id'));
                return response()->json(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
            }

            $orderId = 'topup-'.$request->input('amount')."-user{$client->id}-".Str::random(8);
            // add into transaction log
            Log::channel('transaction')->info('New transaction initiated', [
                'order_id'  => $orderId,
                'client_id' => $client->id,
                'amount'    => $request->input('amount'),
            ]);

            $params = [
                'transaction_details' => [
                    'order_id'     => $orderId,
                    'gross_amount' => $request->input('amount'),
                ],
                'item_details' => [[
                    'id'       => $orderId,
                    'price'    => $request->input('amount'),
                    'quantity' => 1,
                    'name'     => 'Topup Saldo',
                ]],
                'customer_details' => [
                    'first_name' => $client->name,
                    'email'      => $client->email,
                    'phone'      => $client->phone,
                ],
            ];

            $transaction = $this->midtrans->createTransaction($params);
            if (!$transaction || !isset($transaction->token)) {
                Log::channel('transaction')->error("[getSnapToken] Failed to create Midtrans transaction for order {$orderId}");
                return response()->json(['message' => 'Failed to create payment token'], Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            // Simpan histori topup
            Topup_History::updateOrCreate(
                ['order_id' => $orderId],
                ['client_id' => $client->id, 'amount' => $request->input('amount'), 'status' => 'pending']
            );
            Transaction::updateOrCreate(
                ['name' => $orderId],
                [
                    'description'      => 'Topup Saldo',
                    'client_id'        => $client->id,
                    'total_price'      => $request->input('amount'),
                    'transaction_date' => now(),
                    'status'           => 'pending',
                ]
            );

            Log::channel('transaction')->info("[getSnapToken] Snap token generated: token={$transaction->token}, redirect_url={$transaction->redirect_url}");

            return response()->json([
                'order_id'     => $orderId,
                'token'        => $transaction->token,
                'redirect_url' => $transaction->redirect_url,
            ], Response::HTTP_OK);

        } catch (\Throwable $th) {
            Log::channel('transaction')->error('[getSnapToken] Error: ' . $th->getMessage());
            return response()->json(['message' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Handle Midtrans notifications (webhook)
     */
    public function handleNotification(Request $request)
    {
        Log::channel('transaction')->info(sprintf(
            '[handleNotification] Received webhook: order_id=%s, status=%s, amount=%s',
            $request->input('order_id'),
            $request->input('transaction_status'),
            $request->input('gross_amount')
        ));

        try {
            $orderId           = $request->input('order_id');
            $statusCode        = $request->input('status_code');
            $grossAmount       = $request->input('gross_amount');
            $receivedSign      = $request->input('signature_key');
            $transactionStatus = $request->input('transaction_status');

            $serverKey         = config('services.midtrans.server_key');
            $expectedSignature = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);

            if ($receivedSign !== $expectedSignature) {
                Log::channel('transaction')->warning("[handleNotification] Invalid signature for order {$orderId}");
                return response()->json(['message' => 'Invalid signature'], Response::HTTP_FORBIDDEN);
            }

            if ($transactionStatus === 'settlement') {
                if (preg_match('/user(\d+)/', $orderId, $matches)) {
                    $userId      = $matches[1];
                    $user        = Client::find($userId);
                    $transaction = Topup_History::where('order_id', $orderId)->first();

                    if ($user && (!$transaction || $transaction->status !== 'success')) {
                        Topup_History::updateOrCreate(
                            ['order_id' => $orderId],
                            ['client_id' => $user->id, 'amount' => $grossAmount, 'status' => 'success']
                        );
                        Transaction::updateOrCreate(
                            ['name' => $orderId],
                            [
                                'description'      => 'Topup Saldo',
                                'client_id'        => $user->id,
                                'total_price'      => $grossAmount,
                                'transaction_date' => now(),
                                'status'           => 'success',
                            ]
                        );
                        $user->saldo += floatval($grossAmount);
                        $user->save();

                        Log::channel('transaction')->info("[handleNotification] Topup success for user {$user->id}, amount={$grossAmount}");
                    } else {
                        Log::channel('transaction')->info("[handleNotification] Transaction already processed or user not found for order {$orderId}");
                    }
                } else {
                    Log::channel('transaction')->warning("[handleNotification] Cannot extract user ID from order_id {$orderId}");
                }
            }

            Log::channel('transaction')->info("[handleNotification] Webhook processed for order {$orderId}");
            return response()->json(['message' => 'Webhook received'], Response::HTTP_OK);

        } catch (\Throwable $th) {
            Log::channel('transaction')->error('[handleNotification] Error: ' . $th->getMessage());
            return response()->json(['message' => 'Internal server error'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}