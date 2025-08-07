@extends('layouts.app')

<link rel="stylesheet" href="{{ asset('css/items/show.css') }}">

@section('content')
<main class="item-detail-page">
    <div class="item-detail__container">
        {{-- 左カラム：商品画像 --}}
        <div class="item-detail__image">
            <img src="{{ $item->image }}" alt="商品画像">
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
                <form action="{{ route('favorites.toggle', ['item' => $item->id]) }}" method="POST">
                    @csrf
                    @php
                    $isFavorited = $item->favorites->contains(auth()->id());
                    @endphp
                    <button type="submit" class="favorite-button {{ $isFavorited ? 'favorited' : '' }}">
                        <span class="favorite-icon">★</span>
                        <span class="favorite-count">{{ $item->favorites->count() }}</span>
                    </button>
                </form>

                <span class="comment-count">💬 {{ $item->messages->count() }}</span>
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
                <p class="item-detail__description">{{ $item->description }}</p>
            </section>

            {{-- 商品の情報 --}}
            <section class="item-detail__section">
                <h2 class="section-title">商品の情報</h2>
                <ul class="item-detail__meta">
                    @if ($item->categories)
                    <li><strong>カテゴリー：</strong>
                        @foreach ($item->categories as $category)
                        <span class="category">{{ $category->name }}</span>
                        @endforeach
                    </li>
                    @endif

                    <li><strong>商品の状態：</strong><span class="condition">{{ $item->condition }}</span></li>
                    @if($item->color)
                    <li><strong>カラー：</strong><span class="color">{{ $item->color }}</span></li>
                    @endif
                </ul>
            </section>

            {{-- コメント表示 --}}
            <h3>コメント（{{ $item->messages->count() }}件）</h3>

            <ul>
                @foreach ($item->messages as $message)
                <li>{{ $message->user->name }}:{{ $message->body }}</li>
                @endforeach
            </ul>

            {{-- コメント投稿フォーム --}}
            @auth
            <form action="{{ route('comments.store', $item->id) }}" method="POST">
                @csrf
                <textarea name="body" rows="3" placeholder="コメントを入力">{{ old('body') }}</textarea>
                @error('body')
                <div class="form__error">{{ $message }}</div>
                @enderror
                <button type="submit">送信</button>
            </form>
            @endauth
        </div>
    </div>
</main>
@endsection