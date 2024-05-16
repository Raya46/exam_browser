<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    use HasFactory;
    protected $fillable = [
        'name'
    ];
    public function kelasJurusan()
    {
        return $this->hasMany(KelasJurusan::class);
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function links()
    {
        return $this->hasMany(Link::class);
    }
}
