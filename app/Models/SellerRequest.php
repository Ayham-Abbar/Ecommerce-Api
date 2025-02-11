<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerRequest extends Model
{
    protected $fillable = ['buyer_id', 'status'];

    public function buyer()
    {
        return $this->belongsTo(User::class);
    }
}
