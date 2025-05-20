<?php

namespace App\Http\Controllers\Extension\Payment;

use App\Http\Controllers\Controller;

use App\Models\Topup_History;
use Illuminate\Http\Request;
use App\Service\Payment\MidtransService;
use Illuminate\Support\Str;
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
        $orderId = 'order-'.Str::random(8);
        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $request->input('amount'),
            ],
            'item_details' => $request->input('items', []),
            'customer_details' => $request->input('customer', []),
        ];

       
        
        $transaction = $this->midtrans->createTransaction($params);

        return response()->json([
            'order_id' => $orderId,
            'token' => $transaction->token,
            'redirect_url' => $transaction->redirect_url,
        ]);
    }

    /**
     * Handle Midtrans notifications (webhook)
     */
    public function handleNotification(Request $request)
    {
        $notification = $this->midtrans->handleNotification();

        $transactionStatus = $notification->transaction_status;
        $orderId = $notification->order_id;
        $fraudStatus = $notification->fraud_status;
        
        // Custom logic: update your database, send email, etc.
        switch ($transactionStatus) {
            case 'capture':
                if ($fraudStatus === 'challenge') {
                    // handle challenged
                    
                } elseif ($fraudStatus === 'accept') {
                    // payment successful
                }
                break;
            case 'settlement':
                // payment settled
                Topup_History::create([
                    'client_id' => $notification->custom_fields[0]->value,
                    'amount' => $notification->gross_amount,
                    'status' => 'settlement',
                    'topup_date' => now(),
                    'payment_type' => $notification->payment_type,
                    'transaction_id' => $notification->transaction_id,
                    'order_id' => $orderId,
                ]);
                break;
            case 'deny':
                // payment denied
                break;
            case 'cancel':
            case 'expire':
                // payment canceled or expired
                break;
            default:
                
                break;
        }

        return response()->json(['status' => 'ok'], Response::HTTP_OK);
    }
}