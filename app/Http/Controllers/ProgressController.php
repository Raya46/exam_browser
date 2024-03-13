<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Models\Progress;
use Illuminate\Http\Request;

class ProgressController extends Controller
{
    public function progressOnDo(Request $request, $id){
        $progress = Progress::find($id);

        $progress->update([
            'user_id' => $request->user_id,
            'link_id' => $request->link_id,
            'status_progress' => 'dikerjakan'
        ]);
    }

    public function progressDone(Request $request, $id){
        $progress = Progress::find($id);

        $progress->update([
            'user_id' => $request->user_id,
            'link_id' => $request->link_id,
            'status_progress' => 'selesai'
        ]);
    }
}
