<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Item;
use App\Models\KelasJurusan;
use App\Models\Link;
use App\Models\Progress;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Sekolah::create([
            'name' => 'SUPER ADMIN'
        ]);
        KelasJurusan::create([
            'name' => 'SUPER ADMIN',
            'sekolah_id' => 1
        ]);
        User::create([
            'name' => 'super admin',
            'role' => 'super admin',
            'password' => 'superadmin123',
            'sekolah_id' => 1,
            'kelas_jurusan_id' => 1
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
    }
}
