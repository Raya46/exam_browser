<?php

namespace App\Http\Controllers;

use App\Models\Pay;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

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
        $order_id = Str::uuid()->toString();
        $snapToken = null;
        $donation = Pay::create([
            'order_id'   => 'ACT-' .$order_id,
            'status'   => 'pending',
            'item_name'   => $request->item_name,
            'price' => $request->price,
            'user_id' => Auth::user()->id,
            'item_id' => $request->item_id,
        ]);
        $payload = [
            'transaction_details' => [
                'order_id'     => $donation->order_id,
                'gross_amount' => $donation->price,
            ],
            'customer_details' => [
                'first_name' => Auth::user()->name,
                'email'      => Auth::user()->name,
            ],
            'item_details' => [
                [
                    'id'            => $donation->order_id,
                    'price'         => $donation->price,
                    'quantity'      => 1,
                    'name'          => $donation->item_name,
                    'brand'         => 'Donation',
                    'category'      => 'Donation',
                    'merchant_name' => config('app.name'),
                ],
            ],
        ];

        $snapToken = \Midtrans\Snap::getSnapToken($payload);


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

    public function webhook(Request $request)
    {
        $auth = base64_encode(env('MIDTRANS_SERVER_KEY'));
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => "Basic $auth"
        ])->get("https://api.sandbox.midtrans.com/v2/$request->order_id/status");
        $response = json_decode($response->body());
        $pay = Pay::where('order_id', $response->order_id)->first();
        $admin_sekolah = User::where('role', 'admin sekolah')->where('id', $pay->user_id)->first();
        $students = User::where('role', 'siswa')->where('sekolah', $admin_sekolah->sekolah)->get();
        $lastOrderNumber = Pay::where('user_id', $admin_sekolah->id)->max('id');
        $lastOrderNumber = (int) substr($lastOrderNumber, 4, 3);
        $newOrderNumber = $lastOrderNumber + 1;
        $newOrderNumber = str_pad($newOrderNumber, 3, '0', STR_PAD_LEFT);

        if ($response->transaction_status == 'capture') {
            $pay->status = 'capture';
            User::where('role', 'admin sekolah')->where('id', $pay->user_id)->update([
                'status' => 'active',
                'token' => $pay->order_id,
            ]);

            foreach ($students as $index => $student) {
                $studentToken = 'USR' . '-' . sprintf('%03d', $newOrderNumber + $index) . '-' . strtoupper($admin_sekolah->sekolah);
                User::where('id', $student->id)->update([
                    'status' => 'active',
                    'token' => $studentToken,
                ]);
            }
        }
        if ($response->transaction_status == 'settlement') {
            $pay->status = 'settlement';
            User::where('role', 'admin sekolah')->where('id', $pay->user_id)->update([
                'status' => 'active',
                'token' => $pay->order_id,
            ]);

            foreach ($students as $index => $student) {
                $studentToken = 'USR' . '-' . sprintf('%03d', $newOrderNumber + $index) . '-' . strtoupper($admin_sekolah->sekolah);
                User::where('id', $student->id)->update([
                    'status' => 'active',
                    'token' => $studentToken,
                ]);
            }
        }
        if ($response->transaction_status == 'pending') {
            $pay->status = 'pending';
        }
        if ($response->transaction_status == 'deny') {
            $pay->status = 'deny';
        }
        if ($response->transaction_status == 'expire') {
            $pay->status = 'expire';
        }
        if ($response->transaction_status == 'cancel') {
            $pay->status = 'cancel';
        }

        $pay->save();
        return response()->json('success');
    }

    public function getUserSubs()
    {
        $pays = Pay::with('item', 'user')->get();
        return response()->json([
            'data' => $pays
        ]);
    }
}
