<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['buyer_id', 'total_price', 'status'];
    protected $table = 'orders';

    public function user()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
