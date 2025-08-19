<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function プロフィールページに必要な情報が表示される()
    {
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'image' => 'profile/test.jpg',
        ]);

        // 出品商品
        $myItem = Item::factory()->for($user, 'users')->create([
            'name' => '出品商品',
        ]);

        // 購入商品
        $purchasedItem = Item::factory()->create(['name' => '購入商品']);
        Order::factory()->create([
            'user_id' => $user->id,
            'item_id' => $purchasedItem->id,
        ]);

        // プロフィールページ表示
        $res = $this->actingAs($user)->get('/mypage');

        $res->assertOk();
        $res->assertSee('profile/test.jpg');   // プロフィール画像
        $res->assertSee('テストユーザー');     // ユーザー名
        $res->assertSee('出品した商品');           // 出品した商品一覧
        $res->assertSee('購入した商品');           // 購入した商品一覧
    }
}
