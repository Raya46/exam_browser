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
        $user = User::select('name')->with('kelasJurusan')->where('role', 'siswa')->where('sekolah_id', Auth::user()->sekolah_id)->get();
        return $user;
    }

    public function headings(): array
    {
        return [
            'Name',
            'Kelas & Jurusan',
        ];
    }

    public function map($user): array
    {
        return [
            $user->name,
            $user->kelasJurusan->name,
        ];
    }
}
