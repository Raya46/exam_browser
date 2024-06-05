<?php

namespace App\Listeners;

use App\Events\ProgressUpdated;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SendProgressUpdate
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ProgressUpdated $event)
    {
        $data = $event->data;
        $socket_url = 'http://192.168.1.3:6001';

        // Sending data to Socket.IO server
        Http::post($socket_url, [
            'event' => 'progress-channel:App\Events\ProgressUpdated',
            'data' => $data
        ]);

        Log::info('Progress updated event sent');
    }
}
