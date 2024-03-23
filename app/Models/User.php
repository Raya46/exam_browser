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
        'kelas_jurusan',
        'sekolah',
        'serial_number',
        'status'
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
}
