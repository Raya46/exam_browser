<?php

namespace App\Jobs;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CheckSubscriptionStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function handle()
    {
        // Ambil semua pengguna yang berlangganan aktif
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
