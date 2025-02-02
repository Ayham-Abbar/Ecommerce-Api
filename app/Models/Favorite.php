<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $fillable = ['buyer_id', 'product_id'];
    protected $table = 'favorites';
    public $timestamps = false;

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
