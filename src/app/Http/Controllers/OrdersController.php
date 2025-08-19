<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Order;
use App\Models\Shipping;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;

use App\Http\Requests\OrdersRequest;
class OrdersController extends Controller {
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
        if ($item->is_sold) return back()->withErrors('この商品はすでに購入されています。');

        $user = auth()->user();
        $payload = $request->validated();

        $shipping = session('checkout.shipping') ?? [
            'zip_code' => $user->zip_code,
            'address'  => $user->address,
            'building' => $user->building,
        ];

        $order = \App\Models\Order::create([
            'user_id'        => $user->id,
            'item_id'        => $item->id,
            'payment_method' => $payload['payment_method'], // "カード払い" or "コンビニ払い"
            'status'         => 'pending',
        ]);

        \App\Models\Shipping::create([
            'order_id' => $order->id,
            'name'     => $user->name,
            'zip_code' => $shipping['zip_code'],
            'address'  => $shipping['address'],
            'building' => $shipping['building'] ?? null,
        ]);

        session()->forget('checkout.shipping');

        // ここで Checkout へ
        return redirect()->route('checkout.start', $order);
    }
    public function pending(Item $item, Request $request)
    {
        $user = auth()->user();

        $shipping = session('checkout.shipping') ?? [
            'zip_code' => $user->zip_code,
            'address'  => $user->address,
            'building' => $user->building,
        ];

        // ★ store() から渡ってくる値を受け取る
        $sessionId  = $request->query('session_id');
        $sessionUrl = $request->query('session_url');

        return view('orders.pending', compact('item', 'user', 'shipping', 'sessionId', 'sessionUrl'));
    }

    // 即時Sold（既存の mockComplete を使う）
    public function mockComplete(OrdersRequest $request, Item $item)
    {
        if ($item->is_sold) {
            return back()->withErrors('この商品はすでに購入されています。');
        }

        $user = auth()->user();
        $shipping = session('checkout.shipping') ?? [
            'zip_code' => $user->zip_code,
            'address'  => $user->address,
            'building' => $user->building,
        ];

        $order = Order::create([
            'user_id'        => $user->id,
            'item_id'        => $item->id,
            'payment_method' => $request->validated()['payment_method'] ?? 'デモ',
        ]);

        Shipping::create([
            'order_id' => $order->id,
            'name'     => $user->name,
            'zip_code' => $shipping['zip_code'],
            'address'  => $shipping['address'],
            'building' => $shipping['building'] ?? null,
        ]);

        $item->update(['is_sold' => true]);
        session()->forget('checkout.shipping');

        return redirect()->route('items.showItem', $item)->with('success', '（デモ）購入完了');
    }

    // ★Stripeへ進む（新規）
    public function checkout(Request $request, Item $item)
    {
        if ($item->is_sold) {
            return back()->withErrors('この商品はすでに購入されています。');
        }

        $method = $request->input('payment_method'); // "コンビニ払い" or "カード払い"
        $types  = $method === 'コンビニ払い' ? ['konbini'] : ['card'];

        Stripe::setApiKey(config('services.stripe.secret'));

        $successUrl = route('orders.success', ['item' => $item->id]) . '?session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl  = route('orders.cancel',  ['item' => $item->id]);

        // JPYはそのまま（×100しない）
        $amount = (int) $item->price;

        $session = StripeSession::create([
            'mode' => 'payment',
            'payment_method_types' => $types,
            'success_url' => $successUrl,
            'cancel_url'  => $cancelUrl,
            'metadata' => [
                'chosen_method' => $method,
                'item_id'       => (string)$item->id,
                'user_id'       => (string)auth()->id(),
            ],
            'line_items' => [[
                'quantity' => 1,
                'price_data' => [
                    'currency' => 'jpy',
                    'unit_amount' => $amount,
                    'product_data' => [
                        'name' => $item->name,
                    ],
                ],
            ]],
        ]);

        return redirect()->away($session->url); // URLは保存せず即リダイレクト
    }
    public function cancel(Item $item)
    {
        return redirect()->route('orders.create', ['item' => $item->id])
            ->with('error', '決済をキャンセルしました。');
    }

}
