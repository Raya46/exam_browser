<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Request;

class LinkController extends Controller
{
    public function index()
    {
        $links = Link::with('progress')->latest()->take(10)->get();

        return response()->json([
            'data' => $links
        ], 200);
    }

    public function store(Request $request)
    {
        Link::create([
            'link_name' => $request->link_name,
            'link_title' => $request->link_title,
            'link_status' => 'active'
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
            'link_name' => $request->link_name,
            'link_title' => $request->link_title,
            'link_status' => $request->link_status
        ]);

        return response()->json([
            'data' => 'berhasil'
        ]);
    }
}
