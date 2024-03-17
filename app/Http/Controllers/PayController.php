<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PayController extends Controller
{
    public function __construct()
    {
        \Midtrans\Config::$serverKey    = config('services.midtrans.serverKey');
        \Midtrans\Config::$isProduction = config('services.midtrans.isProduction');
        \Midtrans\Config::$isSanitized  = config('services.midtrans.isSanitized');
        \Midtrans\Config::$is3ds        = config('services.midtrans.is3ds');
    }

    public function pay(Request $request)
    {
        $snapToken = null;

        DB::transaction(function () use ($request, &$snapToken) {
            $subscription = Subscription::where('id', $request->subs_id)->first();
            $donation = \App\Models\Pay::create([
                'code'   => 'ACT-' . mt_rand(100000, 999999),
                'name'   => Auth::user()->name,
                'amount' => $request->amount,
                'note'   => $request->note,
                'user_id' => Auth::user()->id,
                'subscription_id' => $request->subscription_id
            ]);
            $payload = [
                'transaction_details' => [
                    'order_id'     => $donation->code,
                    'gross_amount' => $donation->amount,
                ],
                'customer_details' => [
                    'first_name' => $donation->name,
                    'email'      => $donation->email,
                ],
                'item_details' => [
                    [
                        'id'            => $donation->code,
                        'price'         => $donation->amount,
                        'quantity'      => 1,
                        'name'          => $subscription->name,
                        'brand'         => 'Donation',
                        'category'      => 'Donation',
                        'merchant_name' => config('app.name'),
                    ],
                ],
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($payload);
            $donation->snap_token = $snapToken;
            $donation->save();
            if ($snapToken) {
                Auth::user()->code->update([
                    'token' => $donation->code
                ]);
                $donation->update([
                    'status' => 'success'
                ]);
            } else {
                $donation->update([
                    'status' => 'failed'
                ]);
            }
        });

        if ($snapToken) {
            return response()->json([
                'status'     => 'success',
                'snap_token' => $snapToken,
            ]);
        } else {
            return response()->json([
                'status'  => 'error',
                'message' => 'Failed to generate Snap token.',
            ], 500);
        }
    }
}
