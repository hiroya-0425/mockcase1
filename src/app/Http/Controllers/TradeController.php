<?php

namespace App\Http\Controllers;

use App\Http\Requests\TradeMessageRequest;
use Illuminate\Http\Request;
use App\Models\TradeMessage;
use App\Models\Order;

class TradeController extends Controller
{
    public function show(Order $order)
    {
        // 関連ロード
        $order->load(['item.user', 'user']);

        // メッセージ取得
        $messages = $order->tradeMessages()->with('user')->get();

        $order->tradeMessages()
            ->where('user_id', '!=', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // ✅ 自分が既に評価済みかチェック
        $hasReviewed = $order->ratings
            ->where('rater_id', auth()->id())
            ->isNotEmpty();

        // その他の取引（自分が関係する取引中の注文）
        $otherTrades = Order::where('id', '!=', $order->id)
            ->where(function ($query) {
                $query->where('user_id', auth()->id()) // 自分が購入者
                    ->orWhereHas('item', function ($q) {
                        $q->where('user_id', auth()->id()); // 自分が出品者
                    });
            })
            ->whereHas('item', function ($q) {
                $q->where('status', 'trading');
            })
            ->with('item')
            ->get();

        return view('trades.show', [
            'order' => $order,
            'messages' => $messages,
            'otherTrades' => $otherTrades,
            'hasReviewed' => $hasReviewed,
        ]);
    }

    // メッセージ送信
    public function storeMessage(TradeMessageRequest $request, Order $order)
    {
        // ✅ 入力値をセッションに保持（本文のみ）
        session(['trade_message_draft' => $request->message]);

        $data = [
            'order_id' => $order->id,
            'user_id'  => auth()->id(),
            'content'  => $request->message,
        ];

        // ✅ 画像がある場合は保存
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('trade_images', 'public');
        }

        // ✅ DB登録
        TradeMessage::create($data);

        // ✅ 送信成功後は下書きをクリア
        session()->forget('trade_message_draft');

        return back()->with('success', 'メッセージを送信しました。');
    }

    public function updateMessage(Request $request, Order $order, TradeMessage $message)
    {
        // 自分のメッセージ以外は編集禁止
        if ($message->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'content' => 'required|string|max:400',
        ]);

        $message->update([
            'content' => $request->content,
        ]);

        return back()->with('success', 'メッセージを更新しました');
    }

    /** 🟥 メッセージ削除 */
    public function destroyMessage(Order $order, TradeMessage $message)
    {
        // 自分のメッセージ以外は削除禁止
        if ($message->user_id !== auth()->id()) {
            abort(403);
        }

        $message->delete();

        return back()->with('success', 'メッセージを削除しました');
    }

}
