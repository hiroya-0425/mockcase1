<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ItemStoreTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 出品フォームから必要項目を保存できる()
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        // カテゴリを複数作成（配列で送る必要があるため）
        $cats = \App\Models\Category::factory()->count(2)->create();
        $catIds = $cats->pluck('id')->all();

        $payload = [
            'category_id' => $catIds,          // ← 配列で送る
            'name'        => 'テストTシャツ',
            'brand'       => 'TEST BRAND',
            'description' => '着心地の良いTシャツです',
            'price'       => 1999,             // 文字列ではなく整数
            'condition'   => '新品',
            // 画像は任意
        ];

        $res = $this->post('/items', $payload);
        $res->assertSessionHasNoErrors();

        // items に保存確認
        $this->assertDatabaseHas('items', [
            'user_id'    => $user->id,
            'name'       => 'テストTシャツ',
            'brand'      => 'TEST BRAND',
            'description' => '着心地の良いTシャツです',
            'price'      => 1999,
            'condition'  => '新品',
        ]);

        // ピボット（複数カテゴリが紐付いたか）
        $itemId = \App\Models\Item::latest('id')->value('id');
        foreach ($catIds as $cid) {
            $this->assertDatabaseHas('category_item', [
                'item_id'     => $itemId,
                'category_id' => $cid,
            ]);
        }
    }

    /** @test */
    public function 画像も送った場合は_storage_public_に保存され_パスが登録される_任意テスト()
    {
        Storage::fake('public');

        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $cats = \App\Models\Category::factory()->count(1)->create();
        $catIds = $cats->pluck('id')->all();

        // 最小PNG（1x1）を作ってアップロード（GD拡張不要）
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO3y2fQAAAAASUVORK5CYII=');
        $tmpPath = sys_get_temp_dir() . '/mini.png';
        file_put_contents($tmpPath, $png);
        $file = new UploadedFile($tmpPath, 'mini.png', 'image/png', null, true);

        $payload = [
            'category_id' => $catIds,      // 配列で送る
            'name'        => '画像付き商品',
            'brand'       => 'IMG BRAND',
            'description' => '画像あり',
            'price'       => 3000,
            'condition'   => '新品',
            'image'       => $file,
        ];

        $res = $this->post('/items', $payload);
        $res->assertSessionHasNoErrors();

        $item = \App\Models\Item::latest('id')->first();
        $this->assertNotNull($item->image, 'image パスが保存されていること');
        $this->assertTrue(str_starts_with($item->image, 'items/'), 'items/ 配下に保存されること');

        Storage::disk('public')->assertExists($item->image);
    }
}
