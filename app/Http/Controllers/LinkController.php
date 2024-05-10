<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LinkController extends Controller
{
    public function index()
    {
        $links = Link::with('progress')
            ->where('sekolah', Auth::user()->sekolah)
            ->where('kelas_jurusan', Auth::user()->kelas_jurusan)
            ->whereDoesntHave('progress')
            ->get();

        return response()->json([
            'data' => $links,
            'user' => Auth::user()->sekolah
        ], 200);
    }

    public function indexLinkAdmin()
    {
        $links = Link::where('sekolah', Auth::user()->sekolah)->get();

        return response()->json([
            'data' => $links,
            'user' => Auth::user()->sekolah
        ], 200);
    }


    public function storeLink(Request $request)
    {

        Link::create([
            'link_name' => $request->link_name,
            'link_title' => $request->link_title,
            'sekolah' => Auth::user()->sekolah,
            'kelas_jurusan' => $request->kelas_jurusan,
            'link_status' => 'active',
        ]);

        return response()->json([
            'data' => 'success'
        ]);
    }


    public function destroy($id)
    {
        $link = Link::find($id);

        $link->delete();
        return response()->json([
            'data' => 'success'
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
            'link_status' => $request->link_status,
            'kelas_jurusan' => $request->kelas_jurusan
        ]);

        return response()->json([
            'data' => 'success'
        ]);
    }
}
