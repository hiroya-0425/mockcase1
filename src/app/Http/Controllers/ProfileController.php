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
        $request->validate([
            'name' => ['required', 'string', 'max:20'],
            'zip_code' => ['required', 'string'],
            'address' => ['required', 'string'],
            'building' => ['nullable', 'string'],
        ]);

        $user = Auth::user();

        $user->update([
            'name' => $request->name,
            'zip_code' => $request->zip_code,
            'address' => $request->address,
            'building' => $request->building,
        ]);

        return redirect('/mypage');
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
