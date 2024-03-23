<?php

namespace App\Http\Controllers;

use App\Exports\ExportSiswa;
use App\Imports\ImportSiswa;
use App\Models\Pay;
use App\Models\Progress;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class UserController extends Controller
{
    public function postLogin(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'name' => 'required',
                    'password' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if (!Auth::attempt($request->only(['name', 'password']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'name & Password does not match with our record.',
                ], 401);
            }

            $user = User::where('name', $request->name)->first();

            if (Auth::user()->role == 'super admin') {
                return response()->json([
                    'status' => true,
                    'message' => 'super admin',
                    'token' => $user->createToken("API TOKEN")->plainTextToken
                ], 200);
            }

            if (Auth::user()->role == 'admin sekolah') {
                return response()->json([
                    'status' => true,
                    'message' => 'admin sekolah',
                    'token' => $user->createToken("API TOKEN")->plainTextToken
                ], 200);
            }

            return response()->json([
                'status' => true,
                'message' => 'siswa',
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function logout(): JsonResponse
    {
        auth()->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logout success',
        ], 200);
    }

    public function indexSiswa()
    {
        $data = Progress::with('user', 'link')
            ->where('user_id', Auth::user()->id)
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            'data' => $data
        ]);
    }

    public function indexSuperAdmin()
    {
        $data = User::where('role', 'admin sekolah')->get();

        return response()->json([
            'data' => $data
        ]);
    }

    public function indexAdminSekolah()
    {
        $role = ['siswa', 'admin sekolah'];
        $status_pay = ['settlement', 'capture'];
        $data = User::whereIn('role', $role)->where('sekolah', Auth::user()->sekolah)->get();
        $paid = Pay::whereHas('user', function($query){
            $query->where('sekolah', Auth::user()->sekolah);
        })->whereIn('status', $status_pay)->with('item')->latest()->first();

        return response()->json([
            'paid' => $paid,
            'data' => $data
        ]);
    }

    public function export_siswa_excel(){
        $sekolah = Auth::user()->sekolah;
        return Excel::download(new ExportSiswa, "$sekolah.xlsx");
    }

    public function import_siswa_excel(Request $request){
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
        $user = User::where('role', 'admin sekolah')->where('id', Auth::user()->id)->first();
        $status_pay = ['settlement', 'capture'];
        $schoolUsersCount = User::where('sekolah', Auth::user()->sekolah)->count();
        $paid = Pay::whereHas('user', function($query){
            $query->where('sekolah', Auth::user()->sekolah);
        })->whereIn('status', $status_pay)->with('item')->latest()->first();

        if ($schoolUsersCount >= $paid->item->user_quantity) {
            return response()->json([
                'message' => 'Maaf, tidak bisa menambahkan pengguna baru karena jumlah pengguna dengan sekolah yang sama sudah mencapai batas maksimum.'
            ], 403); // 403: Forbidden
        }
        if($request->role == "admin sekolah"){
            User::create([
                'name' => $request->name,
                'role' => 'admin sekolah',
                'password' => $request->password,
                'token' => $request->token . strtoupper($user->sekolah),
                'sekolah' => strtoupper($user->sekolah),
                'kelas_jurusan' => strtoupper($request->kelas_jurusan)
            ]);
        }

        User::create([
            'name' => $request->name,
            'role' => 'siswa',
            'password' => $request->password,
            'token' => $request->token . strtoupper($user->sekolah),
            'sekolah' => strtoupper($user->sekolah),
            'kelas_jurusan' => strtoupper($request->kelas_jurusan)
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
            "data" => "success delete $data"
        ]);
    }

    public function updateSiswaAdminSekolah(Request $request, $id)
    {
        $role = ['siswa', 'admin sekolah'];
        $user = User::whereIn('role', $role)->find($id);

        if ($request->password == null || $request->password == "") {
            $user->update([
                'name' => $request->name,
                'token' => $request->token,
                'role' => $request->role,
                'kelas_jurusan' => $request->kelas_jurusan
            ]);
            return response()->json([
                'data' => 'success'
            ]);
        } else {
            $user->update([
                'name' => $request->name,
                'password' => $request->password,
                'token' => $request->token,
                'role' => $request->role,
                'kelas_jurusan' => $request->kelas_jurusan
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

    public function registerAdminSekolah(Request $request)
    {
        User::create([
            'name' => $request->name,
            'role' => $request->role,
            'password' => $request->password,
            'sekolah' => strtoupper($request->sekolah),
            'kelas_jurusan' => strtoupper($request->kelas_jurusan)
        ]);

        return response()->json([
            'data' => 'success'
        ]);
    }
}
