<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\Progress;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProgressController extends Controller
{
    public function updateStatusByTime()
    {
        $currentTime = Carbon::now();
        Link::where('waktu_pengerjaan_mulai', '<=', $currentTime)->where('link_status', 'inactive')->update(['link_status' => 'active']);
        Progress::whereHas('link', function ($query) use ($currentTime) {
            $query->where('waktu_pengerjaan_selesai', '<=', $currentTime);
        })->where('status_progress', 'dikerjakan')->update(['status_progress' => 'selesai']);
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

    public function progressUser(Request $request, $id)
    {
        $progress = Progress::where('user_id', Auth::user()->id)->where('link_id', $id)->first();
        $progress->update([
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

        $data = $query->paginate(5)->appends([
            'kelas_jurusan_id' => $request->kelas_jurusan_id,
            'status_progress' => $request->status_progress
        ]);

        return response()->json([
            'data' => $data
        ]);
    }

    public function show($id)
    {
        $progress = Progress::where('user_id', Auth::user()->id)->where('link_id', $id)->first();

        return response()->json([
            'data' => $progress
        ]);
    }

    public function streamMonitoringUserProgress(Request $request)
    {
        $response = new StreamedResponse(function () use ($request) {
            $lastEventId = $request->header('Last-Event-ID', 0);

            while (true) {
                $query = Progress::whereHas('user', function ($query) use ($request) {
                    $query->where('role', 'siswa')
                        ->where('sekolah_id', Auth::user()->sekolah_id);

                    if ($request->filled('kelas_jurusan_id')) {
                        $query->where('kelas_jurusan_id', $request->kelas_jurusan_id);
                    }
                })->with('link', 'user');

                if ($request->filled('status_progress')) {
                    $query->where('status_progress', $request->status_progress);
                }

                $data = $query->get();

                echo "id: " . uniqid() . "\n";
                echo "data: " . json_encode(['data' => $data]) . "\n\n";

                ob_flush();
                flush();

                sleep(5); // Wait for 5 seconds before sending the next update

                if (connection_aborted()) {
                    break;
                }
            }
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');

        return $response;
    }

    public function streamUserProgress()
    {
        $response = new StreamedResponse(function () {
            $userId = Auth::user()->id;

            while (true) {
                $data = Progress::whereHas('user', function ($query) use ($userId) {
                    $query->where('role', 'siswa')
                        ->where('sekolah_id', Auth::user()->sekolah_id)
                        ->where('id', $userId)
                        ->where('kelas_jurusan_id', Auth::user()->kelas_jurusan_id);
                })->where('status_progress', '!=', 'belum dikerjakan')
                    ->with('link', 'user')
                    ->get();

                echo "data: " . json_encode(['data' => $data]) . "\n\n";
                ob_flush();
                flush();
                sleep(5); // Wait for 5 seconds before sending the next update

                if (connection_aborted()) {
                    break;
                }
            }
        });

        $response->headers->set('Content-Type', 'text/event-stream');
        $response->headers->set('Cache-Control', 'no-cache');
        $response->headers->set('Connection', 'keep-alive');

        return $response;
    }
}
