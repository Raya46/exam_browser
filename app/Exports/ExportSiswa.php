<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportSiswa implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::select('name', 'kelas_jurusan', 'nilai')->where('role', 'siswa')->where('sekolah', Auth::user()->sekolah)->get();
    }

    public function headings(): array
    {
        return [
            'Name',
            'Kelas & Jurusan',
            'Nilai'
        ];
    }

    public function map($user): array
    {
        return [
            $user->name,
            $user->kelas_jurusan,
            $user->nilai
        ];
    }
}
