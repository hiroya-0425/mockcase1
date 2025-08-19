<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
class ProfileController extends Controller
{
    //
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

        // 編集画面に戻って “保存後の画像” をそのまま表示したいなら edit に戻る
        return redirect()->route('profile.show');
        // マイページに飛ばしたいなら:
        // return redirect('/mypage')->with('success','プロフィールを更新しました');
    }

    public function showProfile()
    {
        $user = auth()->user();

        // クエリパラメータによって表示商品を切り替え
        if (request('page') === 'buy') {
            $items = $user->orders()->with('item')->get()->pluck('item');
        } else {
            $items = $user->items()->latest()->get();
        }

        return view('profile.show', compact('user', 'items'));
    }
}
