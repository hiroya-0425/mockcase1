<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // id=1 のユーザー
        User::create([
            'id'       => 1,
            'name'     => '代居大哉',
            'email'    => 'hiroya-ydh@example.jp',
            'password' => Hash::make('hirohiroya'),
            'zip_code' => '100-0001',
            'address'  => '東京都千代田区千代田1-1',
            'building' => 'テストビル101',
            'image'    => null,
        ]);

        // id=2 のユーザー
        User::create([
            'id'       => 2,
            'name'     => '須藤 巧',
            'email'    => 'sudou@example.jp',
            'password' => Hash::make('12345678'),
            'zip_code' => '150-0001',
            'address'  => '東京都渋谷区神宮前1-1-1',
            'building' => 'テストマンション202',
            'image'    => null,
        ]);


        User::factory()->count(10)->create();
    }

}
