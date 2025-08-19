<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteIndexTest extends TestCase
{
    use RefreshDatabase;

    /**
     * いいねした商品だけが表示される
     *
     * 手順:
     * 1. ログイン
     * 2. /?filter=favorite を開く
     * 期待: いいね済み商品は表示、未いいね商品は非表示
     */
    public function test_いいねした商品だけが表示される()
    {
        $user = User::factory()->create();

        // 衝突しない固有名で作成する（HTMLにまず出てこないワード）
        $liked   = Item::factory()->create(['name' => 'LIKED_ITEM_12345']);
        $unliked = Item::factory()->create(['name' => 'UNLIKED_ITEM_67890']);

        $user->favorites()->attach($liked->id);

        $res = $this->actingAs($user)->get('/?filter=favorite');

        $res->assertOk();
        $res->assertSee('LIKED_ITEM_12345');
        $res->assertDontSee('UNLIKED_ITEM_67890');
    }

    /**
     * 購入済み商品は「Sold」と表示される
     *
     * 手順:
     * 1. ログイン
     * 2. お気に入り済み & 注文済みのアイテムを用意
     * 3. /?filter=favorite を開く
     * 期待: 「Sold」が表示される
     */
    public function test_購入済み商品は_Sold_と表示される()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // お気に入り登録
        $user->favorites()->attach($item->id);

        // 購入済みにする（orders にレコードを作る）
        Order::factory()->create([
            'user_id'        => $user->id,
            'item_id'        => $item->id,
            'payment_method' => 'カード払い', // 追加済みカラムを満たす
        ]);

        $res = $this->actingAs($user)->get('/?filter=favorite');

        $res->assertOk();
        $res->assertSee('Sold');
    }

    /**
     * 未認証の場合は何も表示されない
     *
     * 手順:
     * 1. 未ログイン状態で /?filter=favorite を開く
     * 期待: 何も表示されない（コレクション空）
     */
    public function test_未認証の場合は何も表示されない()
    {
        // いいね済みの商品があっても、ゲストで開くと空表示になる仕様
        $someone = User::factory()->create();
        $liked   = Item::factory()->create();
        $someone->favorites()->attach($liked->id);

        $res = $this->get('/?filter=favorite');

        $res->assertOk();
        // 少なくとも該当アイテム名が出てこないことを確認
        $res->assertDontSee($liked->name);
        // 必要なら「空表示のUI文言」を入れていればそれの確認でもOK（入れてないならこのままでOK）
    }
}
