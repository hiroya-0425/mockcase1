<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function ログインページが表示できる()
    {
        $this->get('/login')->assertStatus(200);
    }
    protected function setUp(): void
    {
        parent::setUp();
        // テストでは CSRF を無効化
        $this->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
    }

    /** @test */
    public function メール未入力だとエラーになる_期待文言あり()
    {
        $payload = [
            'email' => '',
            'password' => 'password123',
        ];

        $res = $this->post('/login', $payload);

        // キーで検証（Fortifyのデフォは 'email' にエラーを付ける）
        $res->assertSessionHasErrors(['email']);

        // 日本語メッセージを厳密に見る場合（カスタム時のみ有効化）
        $res->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    /** @test */
    public function パスワード未入力だとエラーになる_期待文言あり()
    {
        $payload = [
            'email' => 'test@example.com',
            'password' => '',
        ];

        $res = $this->post('/login', $payload);

        $res->assertSessionHasErrors(['password']);
        $res->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    /** @test */
    public function 入力情報が誤っているとエラー_期待文言あり()
    {
        // 存在しないユーザー or 誤パスワード
        $payload = [
            'email' => 'not-exist@example.com',
            'password' => 'wrongpass',
        ];

        $res = $this->post('/login', $payload);

        // Fortifyデフォは 'email' に auth.failed を付ける
        $res->assertSessionHasErrors(['email']);

        // メッセージをカスタムしているならこちらを有効化
        // $res->assertSessionHasErrors(['email' => 'ログイン情報が登録されていません']);?
    }

    /** @test */
    public function 正しい情報ならログインできトップへ遷移する()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $payload = [
            'email' => 'test@example.com',
            'password' => 'password123',
        ];

        $res = $this->post('/login', $payload);

        // 期待リダイレクト（トップ＝items.showIndex ルートに紐づく '/'）
        // プロジェクトの仕様が別なら、assertRedirect('/') に変えてOK
        $res->assertRedirect(route('items.showIndex'));

        $this->assertAuthenticatedAs($user);
    }
}
