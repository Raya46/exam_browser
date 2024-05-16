<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KelasJurusan extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'sekolah_id',
    ];
    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
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
