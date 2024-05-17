<?php

namespace App\Http\Controllers;

use App\Models\Progress;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ProgressController extends Controller
{
    public function updateProgressStatus()
    {
        $currentTime = Carbon::now('Asia/Jakarta');
        $sekolahId = Auth::user()->sekolah_id;

        Progress::whereHas('link', function ($query) use ($currentTime) {
            $query->where('waktu_pengerjaan_selesai', '<=', $currentTime);
        })
            ->where('status_progress', '!=', 'selesai')
            ->whereHas('user', function ($query) use ($sekolahId) {
                $query->where('sekolah_id', $sekolahId);
            })
            ->update(['status_progress' => 'selesai']);

        return response()->json([
            'message' => 'Progress status updated successfully'
        ]);
    }
    public function createOrUpdateProgress(Request $request)
    {
        $user = User::where('role', 'siswa')->where('id', Auth::user()->id)->first();
        $progress = Progress::where('user_id', Auth::user()->id)->where('link_id', $request->link_id)->first();

        if (empty($progress)) {
            $progressCreated = Progress::create([
                'user_id' => $user->id,
                'link_id' => $request->link_id,
                'status_progress' => 'dikerjakan',
            ]);
            return response()->json([
                'data' => $progressCreated
            ]);
        } else {
            $progressUpdated = $progress->update([
                'link_id' => $request->link_id,
                'status_progress' => $request->status_progress
            ]);
            return response()->json([
                'data' => $progressUpdated
            ]);
        }
    }

    public function progressUser(Request $request)
    {
        $progress = Progress::where('user_id', Auth::user()->id)->where('link_id', $request->link_id)->first();
        $user = User::where('role', 'siswa')->where('id', Auth::user()->id)->first();

        $progress->update([
            'user_id' => $user->id,
            'link_id' => $request->link_id,
            'status_progress' => $request->status_progress
        ]);

        return response()->json([
            'data' => $request->status_progress
        ]);
    }

    public function updateProgress(Request $request, $id)
    {
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

    public function userProgress()
    {
        $data = Progress::whereHas('user', function ($query) {
            $query->where('role', 'siswa')
                ->where('sekolah_id', Auth::user()->sekolah_id)
                ->where('id', Auth::user()->id)
                ->where('kelas_jurusan_id', Auth::user()->kelas_jurusan_id);
        })->where('status_progress', '!=', 'belum dikerjakan')
            ->with('link', 'user')
            ->get();

        return response()->json([
            'data' => $data
        ]);
    }

    public function monitoringUserProgress(Request $request)
    {
        $query = Progress::whereHas('user', function ($query) use ($request) {
            $query->where('role', 'siswa')
                ->where('sekolah_id', Auth::user()->sekolah_id);

            if ($request->filled('kelas_jurusan_id')) {
                $query->where('kelas_jurusan_id', $request->kelas_jurusan_id);
            }
        })->with('link', 'user');

        // Tambahkan filter untuk status_progress
        if ($request->filled('status_progress')) {
            $query->where('status_progress', $request->status_progress);
        }

        $data = $query->paginate(3)->appends([
            'kelas_jurusan_id' => $request->kelas_jurusan_id,
            'status_progress' => $request->status_progress
        ]);

        return response()->json([
            'data' => $data
        ]);
    }

    public function show($id)
    {
        $progress = Progress::with('link', 'user')->where('id', $id)->first();

        return response()->json([
            'data' => $progress
        ]);
    }
}
