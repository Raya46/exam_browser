<?php

namespace App\Http\Controllers;

use App\Models\KelasJurusan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KelasJurusanController extends Controller
{
    // Menampilkan semua kelas jurusan
    public function index()
    {
        $kelasJurusans = KelasJurusan::where('sekolah_id', Auth::user()->sekolah_id)->get();
        return response()->json(['data' => $kelasJurusans]);
    }

    // Menyimpan kelas jurusan baru
    public function store(Request $request)
    {
        KelasJurusan::create([
            'name' => $request->name,
            'sekolah_id' => Auth::user()->sekolah_id
        ]);

        return response()->json([
            'data' => 'success'
        ]);
    }

    // Menampilkan satu kelas jurusan
    public function show($id)
    {
        $kelasJurusan = KelasJurusan::find($id);

        return response()->json([
            'data' => $kelasJurusan
        ]);
    }

    // Memperbarui kelas jurusan
    public function updateKelasJurusan(Request $request, $id)
    {
        $kelasJurusan = KelasJurusan::find($id);

        $kelasJurusan->update([
            'name' => $request->name
        ]);

        return response()->json([
            'data' => 'success'
        ]);
    }

    // Menghapus kelas jurusan
    public function destroy($id)
    {
        $kelasJurusan = KelasJurusan::find($id);

        $kelasJurusan->delete();
        return response()->json([
            'data' => 'success'
        ]);
    }
}
