<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'table_number',
        'session_code',
        'total_price',
        'payment_amount',
        'change_amount',
        'payment_status',
        'status',
        'billing_status',
        'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function details()
    {
        return $this->hasMany(OrderDetail::class);
    }

    public function scopeActiveSession($query)
    {
        return $query->whereIn('billing_status', ['Ordering', 'Requested']);
    }

    public function scopeRequested($query)
    {
        return $query->where('billing_status', 'Requested');
    }
}
