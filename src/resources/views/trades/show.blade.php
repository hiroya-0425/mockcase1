@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/trades/show.css') }}">
@endsection

@section('content')
<main class="trade">
    <aside class="trade__sidebar">
        <p class="trade__other">その他の取引</p>
        <div class="trade__sidebar-list">
            @foreach ($otherTrades as $trade)
            <a href="{{ route('trades.show', ['order' => $trade->id]) }}" class="trade__sidebar-item">
                <div class="trade__sidebar-name">
                    {{ $trade->item->name }}
                </div>
            </a>
            @endforeach
        </div>
    </aside>

    <section class="trade__main">
        <div class="trade__header">
            <div class="trade__user-info">
                @if (auth()->id() === $order->user_id)
                <img src="{{ $order->item->user->image ? asset('storage/' . $order->item->user->image) : asset('/default-profile.png') }}"
                    alt="相手のアイコン"
                    class="trade__user-icon">
                <h2 class="trade__user-name">「{{ $order->item->user->name }}」さんとの取引画面</h2>
                @else
                <img src="{{ $order->user->image ? asset('storage/' . $order->user->image) : asset('/default-profile.png') }}"
                    alt="相手のアイコン"
                    class="trade__user-icon">
                <h2 class="trade__user-name">「{{ $order->user->name }}」さんとの取引画面</h2>
                @endif
            </div>
            {{-- 取引完了ボタン --}}
            @if($order->item->status === 'trading')
            @if(auth()->id() === $order->user_id)
            <button id="complete-btn" class="trade__complete">取引を完了する</button>
            @endif
            @endif
        </div>

        {{-- 評価フォーム（最初は非表示） --}}
        <div id="rating-modal" class="rating__modal" style="display:none;">
            <div class="rating__content">
                <h3 class="rating__header">取引が完了しました。</h3>
                <p class="rating__text">今回の取引相手はどうでしたか？</p>

                <form action="{{ route('orders.review.store', $order->id) }}" method="POST" class="form">
                    @csrf
                    <div class="rating__stars">
                        @for($i = 5; $i >= 1; $i--)
                        <input type="radio" name="score" value="{{ $i }}" id="star{{ $i }}">
                        <label for="star{{ $i }}">★</label>
                        @endfor
                    </div>
                    <button type="submit" class="rating__submit">送信する</button>
                </form>
            </div>
        </div>

        <div class="trade__item">
            @if (\Illuminate\Support\Str::startsWith($order->item->image, 'http'))
            <img class="trade__item-image" src="{{ $order->item->image }}" alt="商品画像">
            @else
            <img class="trade__item-image" src="{{ asset('storage/' . $order->item->image) }}" alt="商品画像">
            @endif
            <div class="trade__item-detail">
                <h3 class="trade__item-text">{{ $order->item->name }}</h3>
                <p class="trade__item-text">¥{{ number_format($order->item->price) }}</p>
            </div>
        </div>

        <div class="trade__messages">
            @foreach ($messages as $message)
            {{-- 👇 各メッセージ全体を包むブロック（これが必要） --}}
            <div class="message__block {{ $message->user_id === auth()->id() ? 'mine' : '' }}">

                {{-- アイコン＋名前 --}}
                <div class="message__user {{ $message->user_id === auth()->id() ? 'mine' : '' }}">
                    <img src="{{ $message->user->image ? asset('storage/' . $message->user->image) : asset('/default-profile.png') }}"
                        alt=""
                        class="message__user-icon">
                    <small class="message__user-name">{{ $message->user->name }}</small>
                </div>

                {{-- 吹き出し本体 --}}
                <div class="message {{ $message->user_id === auth()->id() ? 'mine' : 'other' }}">
                    @if ($message->content)
                    <p class="message__text">{{ $message->content }}</p>
                    @endif

                    @if ($message->image)
                    <img src="{{ asset('storage/' . $message->image) }}"
                        alt="送信画像"
                        class="message__image">
                    @endif
                </div>

                {{-- 自分の投稿だけ編集・削除ボタン --}}
                @if ($message->user_id === auth()->id())
                <div class="message__actions">
                    <button type="button" class="message__actions-btn"
                        data-id="{{ $message->id }}"
                        data-content="{{ $message->content }}">編集</button>

                    <form action="{{ route('trades.message.destroy', ['order' => $order->id, 'message' => $message->id]) }}"
                        method="POST" class="inline-form">
                        @csrf
                        @method('DELETE')
                        <button class="message__actions-btn"
                            type="submit"
                            onclick="return confirm('このメッセージを削除しますか？')">削除</button>
                    </form>

                    {{-- 編集フォーム --}}
                    <form id="edit-form-{{ $message->id }}"
                        action="{{ route('trades.message.update', ['order' => $order->id, 'message' => $message->id]) }}"
                        method="POST"
                        style="display:none;"
                        class="inline-form">
                        @csrf
                        @method('PATCH')
                        <input type="text" name="content" value="{{ $message->content }}" maxlength="400">
                        <button type="submit">更新</button>
                    </form>
                </div>
                @endif
            </div>
            @endforeach
        </div>

        <form action="{{ route('trades.message.store', $order->id) }}"
            method="POST"
            class="trade__form"
            enctype="multipart/form-data">
            @csrf
            <div class="trade__input-group">
                <input type="text" name="message" placeholder="取引メッセージを入力してください" value="{{ old('message', session('trade_message_draft')) }}">

                {{-- 画像ファイル選択 --}}
                <label for="image" class="trade__image-button">画像を追加</label>
                <input type="file" name="image" id="image" accept="image/*" style="display:none;">

                <button type="submit"><svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" fill="none" stroke="#7a7a7a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="send-icon">
                        <path d="M22 2L11 13"></path>
                        <path d="M22 2L15 22L11 13L2 9L22 2Z"></path>
                    </svg></button>
            </div>
            {{-- 🔹 バリデーションエラー --}}
            @error('message')
            <p class="error-text">{{ $message }}</p>
            @enderror
            @error('image')
            <p class="error-text">{{ $message }}</p>
            @enderror
        </form>
    </section>
</main>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const completeBtn = document.getElementById('complete-btn');
        const modal = document.getElementById('rating-modal');

        if (completeBtn) {
            completeBtn.addEventListener('click', (e) => {
                e.preventDefault(); // すぐ送信しない
                modal.style.display = 'block'; // 評価フォームを表示
            });
        }
        const input = document.querySelector('input[name="message"]');
        if (!input) return;
        const key = 'trade_message_draft_{{ $order->id }}';

        // ページ読み込み時：保存済み内容を復元
        const saved = localStorage.getItem(key);
        if (saved) input.value = saved;

        // 入力するたびに保存
        input.addEventListener('input', () => {
            localStorage.setItem(key, input.value);
        });

        // 送信時に削除
        document.querySelector('form.trade__form').addEventListener('submit', () => {
            localStorage.removeItem(key);
        });

        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.id;
                const form = document.getElementById(`edit-form-${id}`);
                const text = document.getElementById(`text-${id}`);

                // 表示切り替え
                if (form.style.display === 'none') {
                    form.style.display = 'block';
                    text.style.display = 'none';
                    btn.textContent = 'キャンセル';
                } else {
                    form.style.display = 'none';
                    text.style.display = 'block';
                    btn.textContent = '編集';
                }
            });
        });
    });
</script>
@if (
$order->item->status === 'trading' &&
auth()->id() === $order->item->user_id && {{-- 出品者本人 --}}
!$hasReviewed && {{-- 自分はまだ評価していない --}}
$order->ratings->where('rater_id', $order->user_id)->isNotEmpty() {{-- 購入者が評価済み --}}
)
<script>
    document.addEventListener('DOMContentLoaded', () => {
        document.getElementById('rating-modal').style.display = 'block';
    });
</script>
@endif