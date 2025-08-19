<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class ShippingController extends Controller
{
    public function edit(Item $item)
    {
        $user = Auth::user();

        // セッションに一時保存があればそれを優先、なければユーザー情報で初期化
        $shipping = session('checkout.shipping') ?? [
            'zip_code' => $user->zip_code,
            'address'  => $user->address,
            'building' => $user->building,
        ];

        return view('shippings.edit', compact('user', 'item', 'shipping'));
    }

    public function update(Request $request, Item $item)
    {
        $validated = $request->validate([
            'zip_code' => 'required|string',
            'address'  => 'required|string',
            'building' => 'nullable|string',
        ]);

        // ユーザーは更新せず、セッションにだけ保存
        session(['checkout.shipping' => $validated]);

        return redirect()->route('orders.create', ['item' => $item->id])
            ->with('success', '配送先を更新しました（マイページ住所は変更していません）');
    }
}
