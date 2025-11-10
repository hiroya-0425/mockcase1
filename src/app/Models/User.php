<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'zip_code',
        'address',
        'building',
        'image',
    ];
    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(Item::class, 'favorites');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
    public function ratingsReceived()
    {
        return $this->hasMany(\App\Models\Rating::class, 'rated_user_id');
    }

    public function getAverageRatingAttribute()
    {
        return round($this->ratingsReceived()->avg('score'), 1);
    }
}
