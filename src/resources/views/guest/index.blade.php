@extends('layouts.guest')
<link rel="stylesheet" href="{{ asset('css/guest/index.css') }}">
@section('content')
<main>
    <div class="item__list-page">
        <div class="tab__buttons">
            <a href="{{ route('items.showIndex', array_filter(['filter' => null, 'search' => request('search')])) }}"
                class="{{ $filter !== 'favorite' ? 'active' : '' }}">おすすめ</a>

            <a href="{{ route('items.showIndex', array_filter(['filter' => 'favorite', 'search' => request('search')])) }}"
                class="{{ $filter === 'favorite' ? 'active' : '' }}">マイリスト</a>
        </div>
        <div class="item__grid">
            @foreach ($items as $item)
            {{-- ログイン中のユーザーの出品商品は除外 --}}
            @auth
            @if ($item->user_id === auth()->id())
            @continue
            @endif
            @endauth
            <a href="{{ route('items.showItem', $item->id) }}" class="item__card">
                <div class="item__image" style="position: relative;">
                    @if(Str::startsWith($item->image, 'http'))
                    <img src="{{ $item->image }}" alt="商品画像">
                    @else
                    <img src="{{ asset('storage/' . $item->image) }}" alt="商品画像">
                    @endif
                    @if ($item->is_sold)
                    <span class="sold__badge">Sold</span>
                    @endif
                </div>
                <div class="item__name">{{ $item->name }}</div>
            </a>
            @endforeach
        </div>
    </div>
</main>
@endsection