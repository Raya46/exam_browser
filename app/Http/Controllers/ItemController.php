<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    public function index(){
        $data = Item::all();

        return response()->json([
            'data' => $data
        ]);
    }

    public function store(Request $request)
    {
        Item::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'user_quantity' => $request->user_quantity,
        ]);

        return response()->json([
            'data' => 'success'
        ]);
    }

    public function destroy($id)
    {
        $data = Item::find($id);

        $data->delete();
        return response()->json([
            'data' => 'success'
        ]);
    }

    public function show($id)
    {
        $data = Item::find($id);

        return response()->json([
            'data' => $data
        ]);
    }

    public function putItem(Request $request, $id)
    {
        $data = Item::find($id);

        $data->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'user_quantity' => $request->user_quantity,
        ]);

        return response()->json([
            'data' => 'success'
        ]);
    }
}
