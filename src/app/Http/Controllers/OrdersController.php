<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Order;
use App\Models\Shipping;
use App\Http\Requests\OrdersRequest;

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

        // 配送先（セッションがなければユーザー住所）
        $shipping = session('checkout.shipping') ?? [
            'zip_code' => $user->zip_code,
            'address'  => $user->address,
            'building' => $user->building,
        ];

        // 注文保存（この時点で確定）
        $order = Order::create([
            'user_id'        => $user->id,
            'item_id'        => $item->id,
            'payment_method' => $validated['payment_method'], // 文字保存だけ（課題用）
            // 'status' を使っていたら 'paid' 固定でもOK
        ]);

        Shipping::create([
            'order_id' => $order->id,
            'name'     => $user->name,
            'zip_code' => $shipping['zip_code'],
            'address'  => $shipping['address'],
            'building' => $shipping['building'] ?? null,
        ]);

        // SOLD にする仕様ならここで反映
        $item->update(['is_sold' => true]);

        session()->forget('checkout.shipping');

        return redirect()->route('items.showIndex')->with('success', '購入が完了しました');
    }
}
