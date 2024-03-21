<?php

namespace App\Http\Controllers;

use App\Models\Progress;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

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
        $data = User::where('role', 'siswa')->where('sekolah', Auth::user()->sekolah)->get();

        return response()->json([
            'data' => $data
        ]);
    }

    public function createAdminSekolah(Request $request)
    {
        User::create([
            'name' => $request->name,
            'role' => 'admin sekolah',
            'password' => $request->password,
            'sekolah' => $request->sekolah
        ]);

        return response()->json([
            'data' => 'success'
        ]);
    }

    public function deleteAdminSekolah($id)
    {
        $data = User::find($id);

        $data->delete();

        return response()->json([
            "data" => "success delete $data"
        ]);
    }

    public function updateAdminSekolah(Request $request, $id)
    {
        $user = User::find($id);
        $checkUsername = User::where('name', $request->name)->first();
        if ($checkUsername && $request->name != $user->name) return response()->json([
            'data' => 'nama sudah digunakan'
        ]);

        if ($request->password == null || $request->password == "") {
            $user->update([
                'name' => $request->name,
                'role' => $request->role
            ]);
            return response()->json([
                'data' => 'success'
            ]);
        } else {
            $user->update([
                'name' => $request->name,
                'password' => $request->password,
                'role' => $request->role
            ]);
            return response()->json([
                'data' => 'success'
            ]);
        }
    }

    public function createSiswa(Request $request)
    {
        $user = User::where('role', 'admin sekolah')->where('id', Auth::user()->id)->first();
        User::create([
            'name' => $request->name,
            'role' => 'siswa',
            'password' => $request->password,
            'token' => $user->token,
            'sekolah' => $user->sekolah,
        ]);

        return response()->json([
            'data' => 'success'
        ]);
    }

    public function deleteSiswa($id)
    {
        $data = User::where('role', 'siswa')->find($id);

        $data->delete();

        return response()->json([
            "data" => "success delete $data"
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
                'token' => $request->token,
                'role' => $request->role
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
            'password' => $request->password,
            'role' => 'admin sekolah',
            'sekolah' => $request->sekolah
        ]);

        return response()->json([
            'data' => 'success'
        ]);
    }
}
