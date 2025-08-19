<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログアウトができる()
    {
        // 1. ユーザーを作成してログイン
        $user = User::factory()->create();
        $this->actingAs($user);

        // 2. ログアウトを実行
        $res = $this->post('/logout');

        // 3. 期待挙動: セッションからログアウトされる
        $res->assertRedirect('/'); // ← ログアウト後にトップへ戻す仕様の場合
        $this->assertGuest(); // ユーザーが未ログイン状態になっているか確認
    }
}
