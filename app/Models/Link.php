<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    use HasFactory;

    protected $fillable =[
        'link_name',
        'link_title',
        'sekolah_id',
        'kelas_jurusan_id',
        'link_status',
        'waktu_pengerjaan'
    ];

    public function progress()
    {
        return $this->hasMany(Progress::class);
    }
    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }
    public function kelasJurusan()
    {
        return $this->belongsTo(KelasJurusan::class);
    }
}
