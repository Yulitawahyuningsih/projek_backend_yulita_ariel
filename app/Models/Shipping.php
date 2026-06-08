<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'courier',
        'tracking_number',
        'service',
        'cost',
        'estimated_arrival',
        'status',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}