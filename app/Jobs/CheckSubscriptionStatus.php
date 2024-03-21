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
        $activeUsers = User::whereNotNull('token')
            ->whereIn('role', ['admin sekolah', 'siswa'])
            ->where('subscription_status', 'active')
            ->get();

        $activeSchools = $activeUsers->pluck('sekolah')->unique();

        foreach ($activeUsers as $user) {
            $expiryDate = Carbon::parse($user->subscription_expiry_date);
            $thirtyDaysAgo = Carbon::now()->subDays(30);

            if ($expiryDate->lte($thirtyDaysAgo)) {
                if ($user->role === 'admin sekolah') {
                    $user->subscription_status = 'expire';
                    $user->token = '';
                } else {
                    if ($activeSchools->contains($user->sekolah)) {
                        $user->subscription_status = 'expire';
                        $user->token = '';
                    }
                }
                $user->save();
            } else {
                continue;
            }
        }
    }

}
