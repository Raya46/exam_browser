<?php

namespace App\Http\Controllers;

use App\Exports\ExportSiswa;
use App\Imports\ImportSiswa;
use App\Models\KelasJurusan;
use App\Models\Pay;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function loginAdmin(Request $request)
    {
        $credentials = $request->validate([
            'name' => 'required',
            'password' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = User::where('name', $request->name)->first();
        $token = $user->createToken("API TOKEN")->plainTextToken;

        return response()->json([
            'message' => $user->role === 'super admin' ? 'super admin' : 'admin sekolah',
            'token' => $token
        ]);
    }

    public function updateOrVerifySerialNumber(Request $request)
    {
        try {
            $role = ['admin sekolah', 'siswa'];
            $user = User::where('id', Auth::user()->id)->whereIn('role', $role)->first();

            if (empty($user->serial_number)) {
                $user->serial_number = $request->serial_number;
                $user->save();
                return response()->json([
                    'data' => 'Serial number updated successfully'
                ]);
            }

            if ($user->serial_number == $request->serial_number) {
                return response()->json([
                    'data' => 'Serial number valid'
                ]);
            } else {
                return response()->json([
                    'data' => 'Serial number not valid'
                ]);
            }
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'serial number used'
            ], 500);
        }
    }



    public function loginSiswaAdmin(Request $request)
    {
        $credentials = $request->validate([
            'name' => 'required',
            'password' => 'required',
            'token' => 'required',
        ]);

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = User::where('name', $request->name)->first();
        $token = $user->createToken("API TOKEN")->plainTextToken;

        return response()->json([
            'message' => $user->role === 'admin sekolah' ? 'admin sekolah' : 'siswa',
            'token' => $token
        ]);
    }


    public function logout(): JsonResponse
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout success',
        ], 200);
    }

    public function getDataLoggedIn(){
        $user = User::where('id', Auth::user()->id)->first();
        return response()->json([
            'name' => Auth::user()->name,
            'kelas_jurusan' => $user->kelasJurusan->name,
            'sekolah' => $user->sekolah->name,
            'role' => Auth::user()->role,
        ]);
    }

    public function indexSuperAdmin()
    {
        $data = User::where('role', 'admin sekolah')->get();

        return response()->json([
            'data' => $data
        ]);
    }

    public function indexAdminSekolah(Request $request)
    {
        $role = ['siswa', 'admin sekolah'];
        $data = User::with('kelasJurusan')->whereIn('role', $role)
            ->where('sekolah_id', Auth::user()->sekolah_id)
            ->where('kelas_jurusan_id', $request->kelas_jurusan_id)
            ->paginate(3)->appends([
                'kelas_jurusan_id' => $request->kelas_jurusan_id
            ]);
        $token = Auth::user()->token;

        if (empty($token)) {
            return response()->json([
                'data' => []
            ]);
        }

        return response()->json([
            'token' => $token,
            'data' => $data
        ]);
    }

    public function export_siswa_excel()
    {
        $sekolah = Auth::user()->sekolah;
        return Excel::download(new ExportSiswa, "$sekolah.xlsx");
    }

    public function import_siswa_excel(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls', // Pastikan file berformat Excel
        ]);

        try {
            Excel::import(new ImportSiswa(), $request->file('file'));

            return response()->json([
                'message' => 'Data siswa berhasil diimpor.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengimpor data siswa. Silakan cek kembali format file Anda.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function createSiswaAdminSekolah(Request $request)
    {
        $kelasJurusan = KelasJurusan::firstOrCreate(
            ['name' => $request->kelas_jurusan, 'sekolah_id' => Auth::user()->sekolah_id],
            ['name' => $request->kelas_jurusan, 'sekolah_id' => Auth::user()->sekolah_id]
        );
        $user = User::where('role', 'admin sekolah')->where('id', Auth::user()->id)->first();
        $status_pay = ['settlement', 'capture'];
        $schoolUsersCount = User::where('sekolah_id', Auth::user()->sekolah_id)->count();
        $paid = Pay::whereHas('user', function ($query) use ($user) {
            $query->where('sekolah_id', $user->sekolah_id);
        })->whereIn('status', $status_pay)->with('item')->latest()->first();

        if ($schoolUsersCount >= $paid->item->user_quantity) {
            return response()->json([
                'message' => 'Maaf, tidak bisa menambahkan pengguna baru karena jumlah pengguna dengan sekolah yang sama sudah mencapai batas maksimum.'
            ], 403); // 403: Forbidden
        }
        User::create([
            'name' => $request->name,
            'role' => $request->role,
            'password' => $request->password,
            'token' => $request->token1 . '-' . $request->token2 . '-' . $user->sekolah->name,
            'sekolah_id' => $user->sekolah_id,
            'kelas_jurusan_id' => $kelasJurusan->id
        ]);

        return response()->json([
            'data' => 'success'
        ]);
    }

    public function deleteSiswaAdminSekolah($id)
    {
        $role = ['siswa', 'admin sekolah'];
        $data = User::whereIn('role', $role)->find($id);

        $data->delete();

        return response()->json([
            "data" => "success"
        ]);
    }

    public function updateSiswaAdminSekolah(Request $request, $id)
    {
        $role = ['siswa', 'admin sekolah'];
        $user = User::whereIn('role', $role)->find($id);
        $kelasJurusan = KelasJurusan::firstOrCreate(
            ['name' => $request->kelas_jurusan, 'sekolah_id' => Auth::user()->sekolah_id],
            ['name' => $request->kelas_jurusan, 'sekolah_id' => Auth::user()->sekolah_id]
        );
        if ($request->password == null || $request->password == "") {
            $user->update([
                'name' => $request->name,
                'token' => $request->token. '-' . $user->sekolah->name,
                'role' => $request->role,
                'kelas_jurusan_id' => $kelasJurusan->id
            ]);
            return response()->json([
                'data' => 'success'
            ]);
        } else {
            $user->update([
                'name' => $request->name,
                'password' => $request->password,
                'token' => $request->token . '-' . $user->sekolah->name,
                'role' => $request->role,
                'kelas_jurusan_id' => $kelasJurusan->id
            ]);
            return response()->json([
                'data' => 'success'
            ]);
        }
    }

    public function showUser($id)
    {
        $data = User::find($id);

        return response()->json([
            'data' => $data
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|email|max:255|unique:users,name',
            'role' => 'required|string|max:255',
            'password' => 'required|string',
            'sekolah' => 'required|string|max:255',
            'kelas_jurusan' => 'required|string|max:255',
        ]);

        $sekolah = Sekolah::firstOrCreate(
            ['name' => $request->sekolah],
            ['name' => $request->sekolah]
        );

        $kelasJurusan = KelasJurusan::firstOrCreate(
            ['name' => $request->kelas_jurusan, 'sekolah_id' => $sekolah->id],
            ['name' => $request->kelas_jurusan, 'sekolah_id' => $sekolah->id]
        );

        User::create([
            'name' => $request->name,
            'role' => $request->role,
            'password' => bcrypt($request->password), // Pastikan password di-hash
            'sekolah_id' => $sekolah->id,
            'kelas_jurusan_id' => $kelasJurusan->id
        ]);

        return response()->json(['data' => 'success']);
    }

    public function getKelasJurusan()
    {
        $kelasJurusan = KelasJurusan::distinct()->where('name', '!=', 'SUPER ADMIN')->get();

        return response()->json([
            'data' => $kelasJurusan
        ]);
    }
    public function getKelasJurusanLog()
    {
        $kelasJurusan = KelasJurusan::distinct()->where('name', '!=', 'SUPER ADMIN')->where('sekolah_id', Auth::user()->sekolah_id)->get();
        return response()->json([
            'data' => $kelasJurusan
        ]);
    }
    public function getSekolah()
    {
        $sekolah = Sekolah::where('name', '!=', 'SUPER ADMIN')->get();

        return response()->json([
            'data' => $sekolah
        ]);
    }
}
