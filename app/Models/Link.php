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
        'sekolah',
        'kelas_jurusan',
        'link_status',
    ];

    public function progress()
    {
        return $this->hasMany(Progress::class);
    }
}
