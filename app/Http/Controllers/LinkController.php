<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function index()
    {
        $links = Link::all();

        return response()->json([
            'data' => $links
        ], 200);
    }

    public function store(Request $request)
    {
        Link::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'data' => 'berhasil'
        ]);
    }

    public function destroy($id)
    {
        $link = Link::find($id);

        $link->delete();
        return response()->json([
            'data' => 'berhasil'
        ]);
    }

    public function show($id)
    {
        $link = Link::find($id);

        return response()->json([
            'data' => $link
        ]);
    }

    public function putLink(Request $request, $id)
    {
        $link = Link::find($id);

        $link->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'data' => 'berhasil'
        ]);
    }
}
