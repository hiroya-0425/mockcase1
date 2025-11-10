<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest;
use App\Models\Item;
class CommentController extends Controller
{
    public function store(CommentRequest $request, Item $item)
    {
        $item->messages()->create([
            'user_id' => auth()->id(),
            'body' => $request->body,
        ]);
        return back()->with('success', 'コメントを投稿しました');
    }
}
