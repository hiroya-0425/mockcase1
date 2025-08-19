<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserUpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function プロフィール変更ページに初期値が表示される()
    {
        // Arrange: ユーザー作成
        $user = User::factory()->create([
            'name' => 'テストユーザー',
            'image' => 'profile/test.jpg',
            'zip_code' => '123-4567',
            'address' => '東京都渋谷区テスト1-2-3',
        ]);

        // Act: ログインしてプロフィール編集ページにアクセス
        $res = $this->actingAs($user)->get('/mypage/profile');

        // Assert: 初期値が表示されているか確認
        $res->assertOk();
        $res->assertSee('テストユーザー');
        $res->assertSee('profile/test.jpg');
        $res->assertSee('123-4567');
        $res->assertSee('東京都渋谷区テスト1-2-3');
    }
}
