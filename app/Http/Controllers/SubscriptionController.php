<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function index(){
        $data = Subscription::all();

        return response()->json([
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        Subscription::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'invoice_period' => $request->invoice_period,
            'invoice_interval' => $request->invoice_interval,
            'currency' => $request->currency,
        ]);

        return response()->json([
            'data' => 'berhasil'
        ]);
    }

    public function destroy($id)
    {
        $data = Subscription::find($id);

        $data->delete();
        return response()->json([
            'data' => 'berhasil'
        ]);
    }

    public function show($id)
    {
        $data = Subscription::find($id);

        return response()->json([
            'data' => $data
        ]);
    }

    public function putSubscription(Request $request, $id)
    {
        $data = Subscription::find($id);

        $data->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'invoice_period' => $request->invoice_period,
            'invoice_interval' => $request->invoice_interval,
            'currency' => $request->currency,
        ]);

        return response()->json([
            'data' => 'berhasil'
        ]);
    }
}
