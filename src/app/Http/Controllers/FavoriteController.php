<?php

namespace App\Http\Controllers;

use App\Models\Item;

class FavoriteController extends Controller
{
    public function toggle(Item $item)
    {
        $user = auth()->user();
        if ($user->favorites()->where('item_id', $item->id)->exists()) {
            $user->favorites()->detach($item->id);
        } else {
            $user->favorites()->attach($item->id);
        }
        return back();
    }
}
