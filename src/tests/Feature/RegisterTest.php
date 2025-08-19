<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 会員登録ページが表示できる()
    {
        $this->get('/register')->assertStatus(200);
    }

    /** @test */
    public function 名前未入力だとエラーになる_期待文言あり()
    {
        $payload = [
            'name' => '',
            'email' => 'testtest@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $res = $this->post('/register', $payload);

        // キーでの検証（安全）
        $res->assertSessionHasErrors(['name']);

        // 期待メッセージでの検証（messages() を合わせている場合）
        $res->assertSessionHasErrors(['name' => 'お名前を入力してください']);
    }

    /** @test */
    public function メール未入力だとエラーになる_期待文言あり()
    {
        $payload = [
            'name' => '太郎',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $res = $this->post('/register', $payload);

        $res->assertSessionHasErrors(['email']);
        $res->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    /** @test */
    public function パスワード未入力だとエラーになる_期待文言あり()
    {
        $payload = [
            'name' => '太郎',
            'email' => 'testtest@example.com',
            'password' => '',
            'password_confirmation' => '',
        ];

        $res = $this->post('/register', $payload);

        $res->assertSessionHasErrors(['password']);
        $res->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    /** @test */
    public function パスワードが7文字以下だとエラーになる_期待文言あり()
    {
        $payload = [
            'name' => '太郎',
            'email' => 'testtest@example.com',
            'password' => 'pass777', // 7文字
            'password_confirmation' => 'pass777',
        ];

        $res = $this->post('/register', $payload);

        $res->assertSessionHasErrors(['password']);
        $res->assertSessionHasErrors(['password' => 'パスワードは8文字以上で入力してください']);
    }

    /** @test */
    public function パスワードと確認用が一致しないとエラー_期待文言あり()
    {
        $payload = [
            'name' => '太郎',
            'email' => 'testtest@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different123',
        ];

        $res = $this->post('/register', $payload);

        $res->assertSessionHasErrors(['password']);
        $res->assertSessionHasErrors(['password' => 'パスワードと一致しません']);
    }

    /** @test */
    public function 全項目OKなら登録されプロフィール設定画面へ遷移する()
    {
        $payload = [
            'name' => '太郎',
            'email' => 'testtest@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $res = $this->post('/register', $payload);

        // 成功時のリダイレクト先（あなたの仕様：プロフィール編集）
        $res->assertRedirect(route('profile.edit'));

        $this->assertDatabaseHas('users', [
            'email' => 'testtest@example.com',
            'name'  => '太郎',
        ]);
    }
}
