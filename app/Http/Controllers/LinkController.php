<?php

namespace App\Http\Controllers;

use App\Models\KelasJurusan;
use App\Models\Link;
use App\Models\Sekolah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LinkController extends Controller
{
    public function index()
    {
        $links = Link::with('progress')
            ->where('sekolah_id', Auth::user()->sekolah_id)
            ->where('kelas_jurusan_id', Auth::user()->kelas_jurusan_id)
            ->whereDoesntHave('progress')
            ->orWhereHas('progress', function ($query) {
                $query->where('status_progress', 'belum dikerjakan');
            })
            ->get();

        return response()->json([
            'data' => $links,
        ], 200);
    }

    public function indexLinkAdmin()
    {
        $links = Link::with('kelasJurusan')->where('sekolah_id', Auth::user()->sekolah_id)->get();
        $sekolah = Sekolah::where('id', Auth::user()->sekolah_id)->first();
        return response()->json([
            'data' => $links,
            'sekolah' => $sekolah
        ], 200);
    }


    public function storeLink(Request $request)
    {
        $kelasJurusan = KelasJurusan::firstOrCreate(
            ['name' => $request->kelas_jurusan, 'sekolah_id' => Auth::user()->sekolah_id],
            ['name' => $request->kelas_jurusan, 'sekolah_id' => Auth::user()->sekolah_id]
        );
        Link::create([
            'link_name' => $request->link_name,
            'link_title' => $request->link_title,
            'sekolah_id' => Auth::user()->sekolah_id,
            'kelas_jurusan_id' => $kelasJurusan->id,
            'link_status' => $request->link_status,
            'waktu_pengerjaan' => $request->waktu_pengerjaan,
            'waktu_pengerjaan_mulai' => $request->waktu_pengerjaan_mulai,
            'waktu_pengerjaan_selesai' => $request->waktu_pengerjaan_selesai
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

        $kelasJurusan = KelasJurusan::firstOrCreate(
            ['name' => $request->kelas_jurusan, 'sekolah_id' => Auth::user()->sekolah_id],
            ['name' => $request->kelas_jurusan, 'sekolah_id' => Auth::user()->sekolah_id]
        );

        $link->update([
            'link_name' => $request->link_name,
            'link_title' => $request->link_title,
            'link_status' => $request->link_status,
            'kelas_jurusan_id' => $kelasJurusan->id,
            'waktu_pengerjaan' => $request->waktu_pengerjaan,
            'waktu_pengerjaan_mulai' => $request->waktu_pengerjaan_mulai,
            'waktu_pengerjaan_selesai' => $request->waktu_pengerjaan_selesai
        ]);

        return response()->json([
            'data' => 'success'
        ]);
    }
}
