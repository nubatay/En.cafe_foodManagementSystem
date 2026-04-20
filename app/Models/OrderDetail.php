<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'food_item_id',
        'qty',
        'price',
        'option',
        'status',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function food()
    {
        return $this->belongsTo(FoodItem::class, 'food_item_id');
    }

    public function getSubtotalAttribute(): float
    {
        return $this->price * $this->qty;
    }
}
