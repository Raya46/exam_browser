<?php

namespace App\Http\Controllers;

use App\Models\Progress;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
    public function progressUser(Request $request, $id){
        $progress = Progress::find($id);
        $user = User::where('role', 'siswa')->where('id', Auth::user()->id)->first();

        $progress->update([
            'user_id' => $user->id,
            'link_id' => $request->link_id,
            'status_progress' => $request->status_progress
        ]);
    }

    public function updateProgress(Request $request, $id){
        $progress = Progress::find($id);

        $progress->update([
            'user_id' => $request->user_id,
            'link_id' => $request->link_id,
            'status_progress' => $request->status_progress
        ]);

        return response()->json([
            'data' => 'success'
        ]);
    }

    public function userProgress(){
        $data = Progress::whereHas('user', function ($query) {
            $query->where('role', 'siswa')->where('sekolah', Auth::user()->sekolah);
        })->with('link', 'user')->get();

        return response()->json([
            'data' => $data
        ]);
    }

    public function show($id){
        $progress = Progress::with('link','user')->where('id',$id)->first();

        return response()->json([
            'data' => $progress
        ]);
    }

    public function createProgress(Request $request){
        $user = User::where('role', 'siswa')->where('id', Auth::user()->id)->first();

        Progress::create([
            'user_id' => $user->id,
            'link_id' => $request->link_id,
            'status_progress' => 'dikerjakan'
        ]);

        return response()->json([
            'data' => 'success'
        ]);
    }
}
