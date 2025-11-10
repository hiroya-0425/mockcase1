<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\TradeController;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
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

// メール確認を促す画面（例：登録直後にここへ飛ぶ）
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// メール内のリンクをクリックしたとき（認証完了処理）
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill(); // 認証済みに更新
    return redirect('/mypage/profile'); // 認証後のリダイレクト先（マイページなどに変更可）
})->middleware(['auth', 'signed'])->name('verification.verify');

// 認証メールの再送ボタン
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();

    return back()->with('message', '確認メールを再送しました。');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


// 商品一覧画面
Route::get('/', [ItemController::class, 'showIndex'])->name('items.showIndex');
//商品詳細画面
Route::get('/item/{item}', [ItemController::class, 'showItem'])->name('items.showItem');

Route::middleware(['auth'])->group(function () {

    // プロフィール画面
    Route::get('/mypage', [ProfileController::class, 'showProfile'])->name('profile.show');

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

    Route::get('/trades/{order}', [TradeController::class, 'show'])->name('trades.show');
    Route::post('/trades/{order}/message', [TradeController::class, 'storeMessage'])->name('trades.message.store');

    // 取引メッセージ編集
    Route::patch('/trades/{order}/message/{message}', [TradeController::class, 'updateMessage'])
        ->middleware('auth')
        ->name('trades.message.update');

    // 取引メッセージ削除
    Route::delete('/trades/{order}/message/{message}', [TradeController::class, 'destroyMessage'])
        ->middleware('auth')
        ->name('trades.message.destroy');

    // 評価送信用
    Route::post('/orders/{order}/review', [OrdersController::class, 'storeReview'])
        ->name('orders.review.store');
});
