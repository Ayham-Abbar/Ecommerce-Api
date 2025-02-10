<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Payment extends Model
{
    use HasFactory;
    protected $table = 'payments';
    protected $fillable = [
        'buyer_id',
        'payment_id',
        'amount',
        'currency',
        'payment_status',
        'payment_details'
    ];

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
}
