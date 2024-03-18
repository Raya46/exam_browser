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
            $validateUser = Validator::make($request->all(),
            [
                'name' => 'required',
                'password' => 'required'
            ]);

            if($validateUser->fails()){
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if(!Auth::attempt($request->only(['name', 'password']))){
                return response()->json([
                    'status' => false,
                    'message' => 'name & Password does not match with our record.',
                ], 401);
            }

            $user = User::where('name', $request->name)->first();

            if(Auth::user()->role == 'super admin'){
                return response()->json([
                    'status' => true,
                    'message' => 'super admin',
                    'token' => $user->createToken("API TOKEN")->plainTextToken
                ], 200);
            }

            if(Auth::user()->role == 'admin sekolah'){
                return response()->json([
                    'status' => Auth::user()->id,
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

    public function checkExpiryDate()
    {
        $subscribedUsers = User::where('subscription_status', 'active')->get();

        foreach ($subscribedUsers as $user) {
            // Periksa apakah tanggal berlangganan sudah lebih dari 30 hari yang lalu
            $expiryDate = Carbon::parse($user->subscription_expiry_date);
            $thirtyDaysAgo = Carbon::now()->subDays(30);

            if ($expiryDate->lte($thirtyDaysAgo)) {
                // Perbarui status langganan menjadi expired
                $user->subscription_status = 'expired';
                $user->save();
            }
        }
    }
}
