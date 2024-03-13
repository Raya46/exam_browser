<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Link;
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
            'name' => 'tes links',
            'description' => 'ujian 2022/2023'
        ]);
        User::create([
            'name' => 'admin',
            'role' => 'admin',
            'password' => 'admin123'
        ]);
        User::create([
            'name' => 'siswa1',
            'role' => 'siswa',
            'password' => 'siswa123',
            'kelas_jurusan' => '12 RPL'
        ]);
        User::create([
            'name' => 'siswa2',
            'role' => 'siswa',
            'password' => 'siswa123',
            'kelas_jurusan' => '12 AKL'
        ]);
        User::create([
            'name' => 'siswa3',
            'role' => 'siswa',
            'password' => 'siswa123',
            'kelas_jurusan' => '11 RPL'
        ]);
    }
}
