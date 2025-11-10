@extends('layouts.guest')

<link rel="stylesheet" href="{{ asset('css/items/show.css') }}">

@section('content')
<main class="item-detail-page">
    <div class="item-detail__container">
        {{-- 左カラム：商品画像 --}}
        @php $hasImage = !empty($item->image); @endphp
        <div class="item-detail__image {{ $hasImage ? 'has-image' : 'no-image' }}">
            @if(Str::startsWith($item->image, 'http'))
            <img src="{{ $item->image }}" alt="商品画像">
            @else
            <img src="{{ asset('storage/' . $item->image) }}" alt="商品画像">
            @endif

            @if ($item->is_sold)
            <span class="sold-badge">Sold</span>
            @endif
        </div>

        {{-- 右カラム：商品情報 --}}
        <div class="item-detail__info">
            {{-- 商品名とブランド --}}
            <h1 class="item-detail__name">{{ $item->name }}</h1>
            @if($item->brand)
            <div class="item-detail__brand">{{ $item->brand }}</div>
            @endif
            {{-- 価格 --}}
            <div class="item-detail__price">¥{{ number_format($item->price) }}（税込）</div>
            {{-- お気に入り数・コメント数 --}}
            <div class="item-detail__counts">
                {{-- いいねボタン --}}
                <form action="{{ route('favorites.toggle', ['item' => $item->id]) }}" method="POST">
                    @csrf
                    @php $isFavorited = $item->favorites->contains(auth()->id()); @endphp
                    <button type="submit" class="favorite-button {{ $isFavorited ? 'favorited' : '' }}">
                        <span class="favorite-icon">{{ $isFavorited ? '★' : '☆' }}</span>
                        <span class="favorite-count">{{ $item->favorites->count() }}</span>
                    </button>
                </form>
                {{-- コメント表示 --}}
                <div class="comment-count">
                    <span class="comment-icon">💬</span>
                    <span class="comment-number">{{ $item->messages->count() }}</span>
                </div>
            </div>
            {{-- 購入ボタン --}}
            <form action="{{ route('orders.create', ['item' => $item->id]) }}" method="GET">
                @csrf
                @if ($item->is_sold)
                <button type="submit" class="order-confirm__submit" disabled style="opacity: 0.5; cursor: not-allowed;">
                    この商品は購入済みです
                </button>
                @else
                <button type="submit" class="order-confirm__submit">購入する</button>
                @endif
            </form>
            {{-- 商品説明 --}}
            <section class="item-detail__section">
                <h2 class="section-title">商品説明</h2>
                @if($item->color)
                <div class="item-detail__color">
                    <strong>カラー:</strong>
                    <span class="color">{{ $item->color }}</span>
                </div>
                @endif
                <p class="item-detail__description">{{ $item->description }}</p>
            </section>
            {{-- 商品の情報 --}}
            <section class="item-detail__section">
                <h2 class="section-title">商品の情報</h2>
                <ul class="item-detail__meta">
                    @if ($item->categories)
                    <li><strong>カテゴリー</strong>
                        @foreach ($item->categories as $category)
                        <span class="category">{{ $category->name }}</span>
                        @endforeach
                    </li>
                    @endif
                    <li><strong>商品の状態</strong><span class="condition">{{ $item->condition }}</span></li>
                    {{-- カラーは商品説明へ移動済み --}}
                </ul>
            </section>
            {{-- コメント表示 --}}
            <h3>コメント（{{ $item->messages->count() }}件）</h3>
            <ul class="comment-list">
                @foreach ($item->messages as $message)
                <li class="comment__item">
                    <div class="comment__header">
                        <div class="comment__avatar"></div>
                        <div class="comment__username">{{ $message->user->name }}</div>
                    </div>
                    <div class="comment__text">
                        {{ $message->body }}
                    </div>
                </li>
                @endforeach
            </ul>
            {{-- コメント投稿フォーム --}}
            <h3>商品へのコメント</h3>
            <form action="{{ route('comments.store', $item->id) }}" method="POST" class="comment-form">
                @csrf
                <textarea name="body" rows="3" placeholder="コメントを入力" class="comment-textarea">{{ old('body') }}</textarea>
                @error('body')
                <div class="form__error">{{ $message }}</div>
                @enderror
                <button type="submit" class="comment-submit">コメントを送信する</button>
            </form>
        </div>
    </div>
</main>
@endsection