<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'brand',
        'description',
        'price',
        'image',
        'condition',
        'color',
    ];

    public function users()
    {
        return $this->belongsTo(User::class,  'user_id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function favorites()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function getIsSoldAttribute()
    {
        return $this->orders()->exists();
    }

}
