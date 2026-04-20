<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoodItem extends Model
{
    use HasFactory;

    protected $fillable = [
    'name',
    'description',
    'image',
    'price',
    'stock',
    'category',
    'is_available',
    'options',
    ];

    protected $casts = [
        'options' => 'array',
    ];

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_available', true)
                    ->where('stock', '>', 0);
    }
}
