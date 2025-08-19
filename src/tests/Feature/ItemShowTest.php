<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemShowTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 商品詳細ページに必要な情報が表示される()
    {
        $item = Item::factory()->create([
            'name' => 'テスト商品',
            'brand' => 'テストブランド',
            'price' => 9999,
            'description' => 'これは説明です',
            'condition' => '新品',
        ]);

        // カテゴリを2つ付与
        $categories = Category::factory()->count(2)->create();
        $item->categories()->attach($categories);

        $res = $this->get('/item/' . $item->id);

        $res->assertOk();
        $res->assertSee('テスト商品');
        $res->assertSee('テストブランド');
        $res->assertSee('¥9,999');
        $res->assertSee('（税込）');
        $res->assertSee('これは説明です');
        $res->assertSee('新品');

        // 複数カテゴリが表示されているか
        foreach ($categories as $category) {
            $res->assertSee($category->name);
        }

        
    }
    /** @test */
    public function 商品詳細ページにいいね数が表示される()
    {
        $item = Item::factory()->create();
        $user = User::factory()->create();

        // ユーザーがいいねする
        $item->favorites()->attach($user->id);

        $res = $this->get("/item/{$item->id}");

        $res->assertOk();
        $res->assertSee('1'); // いいね数が1になっているか
    }

    /** @test */
    /** @test */
    public function 商品詳細ページにコメントが表示される()
    {
        $item = \App\Models\Item::factory()->create();
        $user = \App\Models\User::factory()->create();

        // messages リレーションを使用してコメントを1件作成
        $item->messages()->create([
            'user_id'    => $user->id,
            'body'       => 'テストコメントです',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $res = $this->get("/item/{$item->id}");

        $res->assertOk();
        // コメント本文
        $res->assertSee('テストコメントです');
        // コメントしたユーザー名
        $res->assertSee($user->name);
        // コメント件数（ビューは「コメント（N件）」の表記）
        $res->assertSee('コメント（1件）');
    }
}
