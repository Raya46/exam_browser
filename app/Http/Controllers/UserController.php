<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
