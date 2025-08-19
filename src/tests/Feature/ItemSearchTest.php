<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemSearchTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 商品名の部分一致で検索できる()
    {
        // 準備：2商品（片方だけヒット）
        Item::factory()->create(['name' => 'Apple iPhone 15']);
        Item::factory()->create(['name' => 'Banana Case']);

        // 実行：guestで検索（/?search=phone）
        $res = $this->get('/?search=phone');

        // 検証：iPhone は表示、Banana は非表示
        $res->assertOk();
        $res->assertSee('Apple iPhone 15');
        $res->assertDontSee('Banana Case');
    }

    /** @test */
    public function 検索状態がマイリストでも保持される_かつ_マイリスト側でも検索が効く()
    {
        $user = User::factory()->create();

        // 検索対象データ（favorites を付けるのは Apple のみ）
        $apple  = Item::factory()->create(['name' => 'Apple Watch']);
        $banana = Item::factory()->create(['name' => 'Banana Stand']);

        // マイリスト（お気に入り）に Apple だけ追加
        $user->favorites()->attach($apple->id);

        // 1) ホームで検索（/?search=apple）
        $res1 = $this->actingAs($user)->get('/?search=apple');
        $res1->assertOk();
        // ヘッダーの検索入力の value にキーワードが保持されていること
        $res1->assertSee('value="apple"', false);
        // 通常一覧でも Apple は表示、Banana は非表示
        $res1->assertSee('Apple Watch');
        $res1->assertDontSee('Banana Stand');

        // 2) マイリストへ遷移（/?filter=favorite&search=apple）
        $res2 = $this->actingAs($user)->get('/?filter=favorite&search=apple');
        $res2->assertOk();
        // ここでも検索欄にキーワード保持
        $res2->assertSee('value="apple"', false);
        // マイリスト + 検索の結果として Apple のみ表示、Banana は非表示
        $res2->assertSee('Apple Watch');
        $res2->assertDontSee('Banana Stand');
    }
}
