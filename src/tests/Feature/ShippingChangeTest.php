<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use App\Models\Shipping;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShippingChangeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 送付先住所変更画面で登録した住所が商品購入画面に反映される
     */
    public function test_送付先変更が購入画面に反映される()
    {
        $seller = User::factory()->create();
        $buyer  = User::factory()->create();

        $item = Item::factory()->create(['user_id' => $seller->id]);

        $payload = [
            'zip_code' => '123-4567',
            'address'  => '東京都渋谷区道玄坂1-2-3',
            'building' => 'ABCビル101',
        ];

        // 住所をセッションに保存（ShippingController@update）
        $this->actingAs($buyer)
            ->patch(route('shippings.update', ['item' => $item->id]), $payload)
            ->assertRedirect(route('orders.create', ['item' => $item->id]));

        // 購入画面で反映確認
        $res = $this->actingAs($buyer)->get(route('orders.create', ['item' => $item->id]));
        $res->assertOk();
        $res->assertSee('〒123-4567');
        $res->assertSee('東京都渋谷区道玄坂1-2-3ABCビル101');
    }

    /**
     * 購入した商品に送付先住所が紐づいて登録される
     */
    public function test_購入時に送付先住所がshippingsに紐づいて保存される()
    {
        $seller = User::factory()->create();
        $buyer  = User::factory()->create();
        $item   = Item::factory()->create(['user_id' => $seller->id]);

        $payload = [
            'zip_code' => '987-6543',
            'address'  => '大阪府大阪市北区1-2-3',
            'building' => 'XYZマンション202',
        ];

        // 住所をセッションに保存
        $this->actingAs($buyer)
            ->patch(route('shippings.update', ['item' => $item->id]), $payload)
            ->assertRedirect(route('orders.create', ['item' => $item->id]));

        // 購入（OrdersController@store）
        $res = $this->actingAs($buyer)->post(
            route('orders.store', ['item' => $item->id]),
            [
                'payment_method' => 'カード払い', // バリデ必須想定
                'item_id'        => $item->id,
            ]
        );

        $res->assertRedirect(route('items.showIndex'));

        $order = Order::latest('id')->first();
        $this->assertNotNull($order);

        $this->assertDatabaseHas('shippings', [
            'order_id' => $order->id,
            'zip_code' => '987-6543',
            'address'  => '大阪府大阪市北区1-2-3',
            'building' => 'XYZマンション202',
        ]);

        // セッションの掃除を確認（forgetしている前提）
        $res->assertSessionMissing('checkout.shipping');
    }
}
