<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class TradeCompletedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $buyer;
    public $item;

    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->buyer = $order->user; // 購入者
        $this->item = $order->item;  // 商品
    }

    public function build()
    {
        return $this->subject('【取引完了通知】' . $this->item->name)
            ->markdown('trades.completed');
    }
}
