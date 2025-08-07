<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Order;
use App\Http\Requests\OrdersRequest;
class OrdersController extends Controller {
    public function create(Item $item)
    {
        $user = auth()->user();
        return view('orders.create', compact('item', 'user'));
    }

    public function store(OrdersRequest $request, Item $item)
    {
        // 商品がすでに購入されていないか確認
        if ($item->is_sold) {
            return redirect()->back()->withErrors('この商品はすでに購入されています。');
        }

        $validated = $request->validated();

        Order::create([
            'user_id' => auth()->id(),
            'item_id' => $item->id,
            'payment_method' => $validated['payment_method'],
        ]);

        return redirect()->route('items.showIndex')->with('success', '購入が完了しました');
    }
}
