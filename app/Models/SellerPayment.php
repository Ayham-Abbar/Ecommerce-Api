<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerPayment extends Model
{
    protected $fillable = ['seller_id', 'order_id', 'amount', 'status'];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
