<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Item;
use App\Models\Link;
use App\Models\Progress;
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
            'sekolah' => 'sekolah1',
            'kelas_jurusan' => '12 RPL'
        ]);
        User::create([
            'name' => 'super admin',
            'role' => 'super admin',
            'password' => 'superadmin123'
        ]);
        User::create([
            'name' => 'admin@sekolah1.com',
            'role' => 'admin sekolah',
            'sekolah' => 'sekolah1',
            'password' => 'adminsekolah123',
        ]);
        Item::create([
            'name' => '1000 user',
            'description' => '1000 user for lifetime',
            'price' => 5000000,
            'user_quantity' => 1000
        ]);
        Item::create([
            'name' => '770 user',
            'description' => '770 user for lifetime',
            'price' => 3500000,
            'user_quantity' => 770
        ]);
        User::create([
            'name' => 'admin@sekolah2.com',
            'role' => 'admin sekolah',
            'sekolah' => 'sekolah2',
            'password' => 'adminsekolah123',
        ]);
        User::create([
            'name' => 'siswa1',
            'role' => 'siswa',
            'password' => 'siswa123',
            'sekolah' => 'sekolah1',
            'kelas_jurusan' => '12 RPL',
            'nilai' => 90
        ]);
        User::create([
            'name' => 'siswa2',
            'role' => 'siswa',
            'password' => 'siswa123',
            'sekolah' => 'sekolah1',
            'kelas_jurusan' => '12 AKL',
            'nilai' => 80
        ]);
        User::create([
            'name' => 'siswa3',
            'role' => 'siswa',
            'password' => 'siswa123',
            'sekolah' => 'sekolah2',
            'kelas_jurusan' => '11 RPL',
        ]);
        User::create([
            'name' => 'siswa4',
            'role' => 'siswa',
            'password' => 'siswa123',
            'sekolah' => 'sekolah2',
            'kelas_jurusan' => '10 RPL',
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
    }
}
