<?php

namespace App\Http\Controllers;

use App\Models\Progress;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function postLogin(Request $request)
    {
        $validate = $request->validate([
            "name" => "required",
            "password" => "required",
        ]);
        $token = User::where("name", $request->name)->first()->createToken('auth')->plainTextToken;

        if (!Auth::attempt($validate)) return response()->json([
            'message' => 'wrong username or password',
            'data' => $validate
        ], 404);

        if (Auth::user()->role == 'admin') return response()->json([
            'message' => 'admin',
            'data' => $validate,
            'token' => $token
        ], 200);

        return response()->json([
            'message' => 'siswa',
            'data' => $validate,
            'token' => $token
        ], 200);
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

    public function indexAdminSekolah()
    {
        $data = User::where('role', 'siswa')->get();

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

    public function createAdminSekolah(Request $request)
    {
        User::create([
            'name' => $request->name,
            'role' => 'admin sekolah',
            'password' => $request->password
        ]);

        return response()->json([
            'data' => 'berhasil'
        ]);
    }

    public function deleteAdminSekolah($id)
    {
        $data = User::where('role', 'admin sekolah')->find($id);

        $data->delete();

        return response()->json([
            "data" => "berhasil delete $data"
        ]);
    }

    public function updateAdminSekolah(Request $request, $id)
    {
        $user = User::where('role', 'admin sekolah')->find($id);
        $checkUsername = User::where('name', $request->name)->first();
        if ($checkUsername && $request->name != $user->name) return response()->json([
            'data' => 'nama sudah digunakan'
        ]);

        if ($request->password == null || $request->password == "") {
            $user->update([
                'name' => $request->name,
            ]);
            return response()->json([
                'data' => 'success'
            ]);
        } else {
            $user->update([
                'name' => $request->name,
                'password' => $request->password,
            ]);
            return response()->json([
                'data' => 'success'
            ]);
        }
    }

    public function createSiswa(Request $request)
    {
        User::create([
            'name' => $request->name,
            'role' => 'siswa',
            'password' => $request->password
        ]);

        return response()->json([
            'data' => 'berhasil'
        ]);
    }

    public function deleteSiswa($id)
    {
        $data = User::where('role', 'siswa')->find($id);

        $data->delete();

        return response()->json([
            "data" => "berhasil delete $data"
        ]);
    }

    public function updateSiswa(Request $request, $id)
    {
        $user = User::where('role', 'siswa')->find($id);
        $checkUsername = User::where('name', $request->name)->first();
        if ($checkUsername && $request->name != $user->name) return response()->json([
            'data' => 'nama sudah digunakan'
        ]);

        if ($request->password == null || $request->password == "") {
            $user->update([
                'name' => $request->name,
            ]);
            return response()->json([
                'data' => 'success'
            ]);
        } else {
            $user->update([
                'name' => $request->name,
                'password' => $request->password,
            ]);
            return response()->json([
                'data' => 'success'
            ]);
        }
    }

    public function showUser($id){
        $data = User::find($id);

        return response()->json([
            'data' => $data
        ]);
    }
}
