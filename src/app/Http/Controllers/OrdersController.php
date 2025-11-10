<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Models\Shipping;
use App\Http\Requests\OrdersRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TradeCompletedNotification;

class OrdersController extends Controller
{
    public function create(Item $item)
    {
        $user = auth()->user();

        $shipping = session('checkout.shipping') ?? [
            'zip_code' => $user->zip_code,
            'address'  => $user->address,
            'building' => $user->building,
        ];
        return view('orders.create', compact('item', 'user', 'shipping'));
    }

    public function store(OrdersRequest $request, Item $item)
    {
        if ($item->is_sold) {
            return back()->withErrors('この商品はすでに購入されています。');
        }
        $validated = $request->validated();
        $user = auth()->user();
        $shipping = session('checkout.shipping') ?? [
            'zip_code' => $user->zip_code,
            'address'  => $user->address,
            'building' => $user->building,
        ];
        $order = Order::create([
            'user_id'        => $user->id,
            'item_id'        => $item->id,
            'payment_method' => $validated['payment_method'],
        ]);
        Shipping::create([
            'order_id' => $order->id,
            'name'     => $user->name,
            'zip_code' => $shipping['zip_code'],
            'address'  => $shipping['address'],
            'building' => $shipping['building'] ?? null,
        ]);
        session()->forget('checkout.shipping');
        $item->update(['status' => 'trading']);
        return redirect()->route('items.showIndex')->with('success', '購入が完了しました');
    }

    public function storeReview(Request $request, Order $order)
    {
        $user = auth()->user();

        $request->validate([
            'score' => 'required|integer|min:1|max:5',
        ]);

        // すでに評価済みならリダイレクト
        $alreadyReviewed = \App\Models\Rating::where('order_id', $order->id)
            ->where('rater_id', $user->id)
            ->exists();

        if ($alreadyReviewed) {
            return back()->with('error', 'すでにこの取引を評価済みです。');
        }

        // 評価登録
        \App\Models\Rating::create([
            'order_id' => $order->id,
            'rater_id' => $user->id,
            'rated_user_id' => $order->user_id === $user->id
                ? $order->item->user_id
                : $order->user_id,
            'score' => $request->score,
        ]);

        // 両者の評価状況を取得
        $buyerReviewed = \App\Models\Rating::where('order_id', $order->id)
            ->where('rater_id', $order->user_id)
            ->exists();

        $sellerReviewed = \App\Models\Rating::where('order_id', $order->id)
            ->where('rater_id', $order->item->user_id)
            ->exists();
            
        if ($user->id === $order->user_id) { // ログイン中が購入者の場合
            $seller = $order->item->user; // 出品者
            Mail::to($seller->email)->send(new TradeCompletedNotification($order));
        }

        // ✅ 出品者が評価したら取引完了
        if ($buyerReviewed && $sellerReviewed) {
            $order->item->update(['status' => 'completed']);
        }

        return redirect()->route('items.showIndex')->with('message', '評価を送信しました。');
    }
}
