<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubsList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

    public function getUserSubs(){
        $data = SubsList::with('subscription', 'user')->get();

        return response()->json([
            'data' => $data
        ]);
    }

    public function postUserSubs(Request $request){
        SubsList::create([
            'user_id' => Auth::user()->id,
            'subscription_id' => $request->subscription_id
        ]);

        return response()->json([
            'data' => 'berhasil langganan'
        ]);
    }
}
