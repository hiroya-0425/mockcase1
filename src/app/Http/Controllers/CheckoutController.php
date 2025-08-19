<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Checkout\Session as CheckoutSession;
use App\Models\Order;

class CheckoutController extends Controller
{
    public function start(Order $order)
    {
        if (config('services.stripe.mode') !== 'test') {
            abort(403, 'Stripe test mode only.');
        }

        $order->load('item', 'user');
        if (!$order->item || !$order->user) abort(404);

        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        $types = $order->payment_method === 'コンビニ払い' ? ['card', 'konbini'] : ['card'];

        // ★ ここを追加：APP_URL からベースURLを作り、ポート込みの絶対URLを自前で生成
        $base = rtrim(config('app.url'), '/'); // 例: http://localhost:8080
        $successUrl = $base . route('checkout.success', [], false) . '?session_id={CHECKOUT_SESSION_ID}&order=' . $order->id;
        $cancelUrl  = $base . route('checkout.cancel',  [], false) . '?order=' . $order->id;

        $session = \Stripe\Checkout\Session::create([
            'mode' => 'payment',
            'payment_method_types' => $types,
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => ['name' => $order->item->name],
                    'unit_amount' => (int) $order->item->price, // 円なのでそのまま
                ],
                'quantity' => 1,
            ]],
            'customer_email' => $order->user->email,
            'success_url' => $successUrl,
            'cancel_url'  => $cancelUrl,
            'locale' => 'ja',

            // ★ 保険：成功画面で order が欠けても復元できるよう埋めておく
            'metadata' => [
                'order_id' => (string)$order->id,
            ],
        ]);

        return redirect()->away($session->url);
    }
    public function success(Request $request)
    {
        $orderId = $request->query('order');

        if (!$orderId) {
            $sessionId = $request->query('session_id');
            if ($sessionId) {
                \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
                $session = \Stripe\Checkout\Session::retrieve($sessionId);
                $orderId = $session->metadata->order_id ?? null;
            }
        }

        $order = \App\Models\Order::with('item')->findOrFail($orderId);

        $order->status = 'paid';
        $order->save();

        if ($order->item && !$order->item->is_sold) {
            $order->item->update(['is_sold' => true]);
        }

        return redirect()->route('items.showIndex')->with('success', '決済（テスト）が完了しました。');
    }
}
