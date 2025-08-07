<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\ItemRequest;

class ItemController extends Controller {
    public function showIndex(Request $request)
    {
        $user = auth()->user();
        $filter = $request->query('filter');
        $search = $request->query('search');

        // 認証済みユーザー向け
        if ($user) {
            if ($filter === 'favorite') {
                $items = $user->favorites()->with('orders')
                    ->when($search, function ($query, $search) {
                        $query->where('name', 'like', "%{$search}%");
                    })
                    ->latest()->get();
            } else {
                $items = Item::with('orders')
                    ->where('user_id', '!=', $user->id)
                    ->when($search, function ($query, $search) {
                        $query->where('name', 'like', "%{$search}%");
                    })
                    ->latest()->get();
            }

            return view('items.index', compact('items', 'filter', 'search'));
        } else {
            // ゲストユーザー向け
            if ($filter === 'favorite') {
                $items = collect(); // 空
            } else {
                $items = Item::with('orders')
                    ->when($search, function ($query, $search) {
                        $query->where('name', 'like', "%{$search}%");
                    })
                    ->latest()->get();
            }

            return view('guest.index', compact('items', 'filter', 'search'));
        }
    }
    public function showItem(Item $item)
    {
        $item->load(['favorites', 'messages.user', 'categories']);

        if (Auth::check()) {
            return view('items.show', compact('item'));
        } else {
            return view('guest.show', compact('item'));
        }
    }

    public function showCreate()
    {
        $categories = Category::all();
        return view('items.create', compact('categories'));
    }

    public function store(ItemRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('items', 'public');
        }

        $item = new Item($validated);
        $item->user_id = Auth::id();
        $item->save();

        // カテゴリーを中間テーブルに保存
        $item->categories()->attach($validated['category_id']);

        return redirect()->route('items.showIndex')->with('success', '商品を出品しました。');
    }

}
