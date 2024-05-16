<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'password',
        'token',
        'role',
        'kelas_jurusan_id',
        'sekolah_id',
        'serial_number',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function progress()
    {
        return $this->hasMany(Progress::class);
    }

    public function pay()
    {
        return $this->hasMany(Pay::class);
    }
    public function sekolah()
    {
        return $this->belongsTo(Sekolah::class);
    }
    public function kelasJurusan()
    {
        return $this->belongsTo(kelasJurusan::class);
    }
}
