<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;

class ImportSiswa implements ToCollection
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
        $index = 1;
        foreach ($rows as $row) {
            if ($index > 1) {
                $data['name'] = !empty($row[0]) ? $row[0] : '';
                $data['password'] = !empty($row[1]) ? bcrypt($row[1]) : '';
                $data['token'] = !empty($row[2]) ? $row[2] : '';
                $data['kelas_jurusan'] = !empty($row[3]) ? $row[3] : '';
                $data['role'] = 'siswa';
                $data['status'] = 'active';
                $data['sekolah'] = Auth::user()->sekolah;
                User::create($data);
            }
            $index++;
        }
    }
}
