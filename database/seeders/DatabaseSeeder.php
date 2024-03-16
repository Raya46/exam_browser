<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Link;
use App\Models\Progress;
use App\Models\Subscription;
use App\Models\SubsList;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Link::create([
            'link_name' => 'https://docs.google.com/forms/d/e/1FAIpQLSfgXFzWuOo9sWAx3v4LfnNZWnTTLx6jwnmGnx4_qiOE75lV1w/viewform',
            'link_title' => 'ujian 2022/2023',
            'link_status' => 'active',
            'kelas_jurusan' => '12 RPL'
        ]);
        Subscription::create([
            'name' => '1 MONTH',
            'description' => 'free 1 month',
            'price' => 10000,
            'invoice_period' => 1,
            'invoice_interval' => 'month',
            'currency' => 'IDR',
        ]);
        Subscription::create([
            'name' => '1 YEAR',
            'description' => 'free 1 year',
            'price' => 100000,
            'invoice_period' => 1,
            'invoice_interval' => 'year',
            'currency' => 'IDR',
        ]);
        User::create([
            'name' => 'super admin',
            'role' => 'super admin',
            'password' => 'superadmin123'
        ]);
        User::create([
            'name' => 'admin@sekolah1.com',
            'role' => 'admin sekolah',
            'password' => 'adminsekolah123',
        ]);
        User::create([
            'name' => 'admin@sekolah2.com',
            'role' => 'admin sekolah',
            'password' => 'adminsekolah123',
        ]);
        User::create([
            'name' => 'siswa1',
            'role' => 'siswa',
            'password' => 'siswa123',
            'kelas_jurusan' => '12 RPL',
        ]);
        User::create([
            'name' => 'siswa2',
            'role' => 'siswa',
            'password' => 'siswa123',
            'kelas_jurusan' => '12 AKL',
        ]);
        User::create([
            'name' => 'siswa3',
            'role' => 'siswa',
            'password' => 'siswa123',
            'kelas_jurusan' => '11 RPL',
        ]);
        Progress::create([
            'user_id' => 2,
            'link_id' => 1,
            'status_progress' => 'dikerjakan'
        ]);
        Progress::create([
            'user_id' => 3,
            'link_id' => 1,
            'status_progress' => 'selesai'
        ]);
        Progress::create([
            'user_id' => 4,
            'link_id' => 1,
            'status_progress' => 'belum dikerjakan'
        ]);
        SubsList::create([
            'user_id' => 2,
            'subscription_id' => 1
        ]);
        SubsList::create([
            'user_id' => 3,
            'subscription_id' => 1
        ]);
    }
}
