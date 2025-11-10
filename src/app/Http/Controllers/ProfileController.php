<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class ProfileController extends Controller
{
    public function showEdit()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:20'],
            'zip_code' => ['required', 'string'],
            'address'  => ['required', 'string'],
            'building' => ['nullable', 'string'],
            'image'    => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'], // 5MB & webp許可
        ]);
        $user = Auth::user();
        if ($request->hasFile('image')) {
            if ($user->image && \Storage::disk('public')->exists($user->image)) {
                \Storage::disk('public')->delete($user->image);
            }
            $validated['image'] = $request->file('image')->store('profile', 'public');
        }
        $user->update($validated);
        return redirect()->route('profile.show');
    }

    public function showProfile()
    {
        $user = auth()->user();
        $page = request('page');
        $totalUnread = 0;

        // 🔹 取引中アイテムを取得（出品者・購入者両方）
        $tradingItems = \App\Models\Item::where('status', 'trading')
            ->where(function ($q) use ($user) {
                $q->where('user_id', $user->id) // 出品者側
                    ->orWhereHas('order', fn($q2) => $q2->where('user_id', $user->id)); // 購入者側
            })
            ->with(['order.tradeMessages'])
            ->get();

        // 🔹 相手からの未読のみカウント
        $totalUnread = $tradingItems->sum(function ($item) use ($user) {
            if (!$item->order) return 0;
            return $item->order->tradeMessages
                ->where('user_id', '!=', $user->id) // 自分以外
                ->where('is_read', false)           // 未読
                ->count();
        });

        // 🔹 ページごとのデータ
        if ($page === 'buy') {
            $items = $user->orders()->with('item')->get()->pluck('item');
        } elseif ($page === 'sell') {
            $items = $user->items()->latest()->get();
        } elseif ($page === 'trading') {
            $items = Item::where('status', 'trading')
                ->where(function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                        ->orWhereHas('order', fn($q2) => $q2->where('user_id', $user->id));
                })
                ->where(function ($q) use ($user) {
                    $q->whereHas('order', function ($query) use ($user) {
                        $query->whereDoesntHave('ratings', fn($q) => $q->where('rater_id', $user->id));
                    })
                        ->orWhereDoesntHave('order'); // ← Orderが無いitemも拾う
                })
                ->with(['order.tradeMessages'])
                ->get();
        }else {
            $items = $user->items()->latest()->get();
        }

        return view('profile.show', compact('user', 'items', 'totalUnread'));
    }
}
