@extends('layouts.guest')
<link rel="stylesheet" href="{{ asset('css/guest/show.css') }}">
@section('content')
<main class="item__detail-page">
    <div class="item__detail-container">
        {{-- 左カラム：商品画像 --}}
        <div class="item__detail-image">
            <img src="{{ $item->image }}" alt="商品画像">
        </div>

        {{-- 右カラム：商品情報 --}}
        <div class="item__detail-info">
            {{-- 商品名とブランド --}}
            <h1 class="item__detail-name">{{ $item->name }}</h1>
            @if($item->brand)
            <div class="item__detail-brand">{{ $item->brand }}</div>
            @endif

            {{-- 価格 --}}
            <div class="item__detail-price">¥{{ number_format($item->price) }}（税込）</div>

            {{-- お気に入り数・コメント数 --}}
            <div class="item__detail-counts">
                <form action="{{ route('favorites.toggle', ['item' => $item->id]) }}" method="POST">
                    @csrf
                    <button type="submit" class="favorite-button">
                        ★ {{ $item->favorites->count() }}
                    </button>
                </form>

                <span class="comment__count">💬 {{ $item->messages->count() }}</span>
            </div>

            {{-- 購入ボタン --}}
            <form action="{{ route('orders.create', ['item' => $item->id]) }}" method="GET">
                @csrf
                <button type="submit" class="button button__primary item__detail-buy">購入手続きへ</button>
            </form>

            {{-- 商品説明 --}}
            <section class="item__detail-section">
                <h2 class="section__title">商品説明</h2>
                <p class="item__detail-description">{{ $item->description }}</p>
            </section>

            {{-- 商品の情報 --}}
            <section class="item__detail-section">
                <h2 class="section__title">商品の情報</h2>
                <ul class="item__detail-meta">
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