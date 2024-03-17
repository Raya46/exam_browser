<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'invoice_period',
        'invoice_interval',
        'currency',
    ];

    public function pays(){
        return $this->hasMany(Pay::class);
    }
}
