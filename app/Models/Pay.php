<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pay extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'amount',
        'note',
        'status',
        'snap_token',
    ];

    public function subscription(){
        return $this->belongsTo(Subscription::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
