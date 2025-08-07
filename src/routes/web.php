<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// 会員登録
Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);

// 商品一覧画面
Route::get('/', [ItemController::class, 'showIndex'])->name('items.showIndex');
//商品詳細画面
Route::get('/item/{item}', [ItemController::class, 'showItem'])->name('items.showItem');

Route::middleware(['auth'])->group(function () {

    // プロフィール画面
    Route::get('/mypage', [ProfileController::class, 'showProfile']);

    // プロフィール編集画面
    Route::get('/mypage/profile', [ProfileController::class, 'showEdit'])->name('profile.edit');
    // プロフィール変更
    Route::patch('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');

    
    // 商品購入画面
    Route::get('/purchase/{item}', [OrdersController::class, 'create'])->name('orders.create');
    // 購入
    Route::post('/purchase/{item}', [OrdersController::class, 'store'])->name('orders.store');

    // 送付先住所変更画面
    Route::get('/purchase/address/{item}', [ShippingController::class, 'edit'])->name('shippings.edit');
    // 送付先住所変更
    Route::patch('/purchase/{item}', [ShippingController::class, 'update'])->name('shippings.update');

    // 商品出品画面
    Route::get('/sell',[ItemController::class,'showCreate'])->name('items.showCreate');
    // 商品出品
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');

    // お気に入り
    Route::post('/items/{item}/favorite', [FavoriteController::class, 'toggle'])->name('favorites.toggle');
    // コメント
    Route::post('/items/{item}/comment', [CommentController::class, 'store'])->name('comments.store');
    
});
