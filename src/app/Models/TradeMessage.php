<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'user_id',
        'content',
    ];

    // 🔹 メッセージは1つの注文(order)に属する
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // 🔹 メッセージは1人のユーザー(user)に属する
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnreadFromOther($query, $userId)
    {
        return $query->where('user_id', '!=', $userId)
            ->where('is_read', false);
    }
}
