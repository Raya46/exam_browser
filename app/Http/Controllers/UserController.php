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

    public function index()
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
}
