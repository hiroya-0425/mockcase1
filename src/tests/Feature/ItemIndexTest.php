<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Item;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemIndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 全商品を取得できる()
    {
        $items = Item::factory()->count(3)->create();

        $res = $this->get('/');

        $res->assertStatus(200);
        foreach ($items as $item) {
            $res->assertSee($item->name);
        }
    }

    /** @test */
    public function 購入済み商品は_sold_と表示される()
    {
        $item = Item::factory()->create();
        Order::factory()->create([
            'item_id' => $item->id,
        ]);

        $res = $this->get('/');

        $res->assertStatus(200);
        $res->assertSee('Sold');
    }

    /** @test */
    public function 自分が出品した商品は一覧に表示されない()
    {
        $user = User::factory()->create();
        $myItem = Item::factory()->create([
            'user_id' => $user->id,
        ]);
        $otherItem = Item::factory()->create();

        $res = $this->actingAs($user)->get('/');

        $res->assertStatus(200);
        $res->assertDontSee($myItem->name);
        $res->assertSee($otherItem->name);
    }
}
