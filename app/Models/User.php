<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $guard_name = 'api';
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    public function getRoleAttribute()
    {
        return $this->getRoleNames()->first();
    }
    public function products()
    {
        return $this->hasMany(Product::class, 'seller_id');
    }
    public function orders()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }
    public function payments()
    {
        return $this->hasMany(Payment::class, 'buyer_id');
    }
    public function favorite_products()
    {
        return $this->hasMany(Favorite::class,'buyer_id');
    }
    public function seller_payments()
    {
        return $this->hasMany(SellerPayment::class, 'seller_id');
    }
}
