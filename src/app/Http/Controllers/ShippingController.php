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
        return view('shippings.edit', compact('user','item'));
    }

    public function update(Request $request, Item $item)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'zip_code' => 'required',
            'address' => 'required',
            'building' => 'nullable',
        ]);

        $user->update($validated);

        return redirect()->route('orders.create', ['item' => $item->id]);
            
    }
}
