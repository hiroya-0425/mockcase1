@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile/show.css') }}">
@endsection

@section('content')
<main>
    <div class="profile__page">
        <div class="profile__header">
            <div class="profile__image">
                <img src="{{ $user->image ? asset('storage/' . $user->image) : asset('/default-profile.png') }}" alt="プロフィール画像">
            </div>

            <div class="profile__info">
                <div>
                    <div class="profile__username">{{ $user->name }}</div>
                    <div class="profile__rating">
                        @if($user->ratingsReceived->count())
                        @php $average = $user->average_rating; @endphp
                        <span class="stars">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <=floor($average))
                                ★
                                @else
                                ☆
                                @endif
                                @endfor
                                </span>
                                @else
                                <span class="no-rating">評価なし</span>
                                @endif
                    </div>
                </div>

                <a href="{{ route('profile.edit') }}" class="edit-profile-button">プロフィールを編集</a>
            </div>
        </div>
        <div class="profile__tabs">
            {{-- 出品した商品 --}}
            <a href="/mypage?page=sell" class="tab {{ request('page') === 'sell' || !request('page') ? 'active' : '' }}">
                出品した商品
            </a>

            {{-- 購入した商品 --}}
            <a href="/mypage?page=buy" class="tab {{ request('page') === 'buy' ? 'active' : '' }}">
                購入した商品
            </a>

            {{-- 取引中の商品 --}}
            <a href="/mypage?page=trading" class="tab {{ request('page') === 'trading' ? 'active' : '' }}">
                取引中の商品
                @if($totalUnread > 0)
                <span class="tab__badge">{{ $totalUnread }}</span>
                @endif
            </a>
        </div>
        <div class="item__grid">
            @foreach ($items as $item)
            <div class="item__card">
                <div class="item__image" style="position: relative;">
                    @if (request('page') === 'trading')
                    {{-- 取引中タブのときだけクリック可能 --}}
                    <a href="{{ route('trades.show', ['order' => $item->order->id ?? $item->id]) }}" class="item__link">

                        {{-- 🔴 相手からの未読メッセージ件数バッジ --}}
                        @php
                        $unreadCount = 0;
                        if (isset($item->order)) {
                        $unreadCount = $item->order->tradeMessages
                        ->where('user_id', '!=', auth()->id()) // 自分以外（相手）
                        ->where('is_read', false) // 未読のみ
                        ->count();
                        }
                        @endphp

                        @if ($unreadCount > 0)
                        <span class="message__badge">{{ $unreadCount }}</span>
                        @endif
                        @endif


                        @if(Str::startsWith($item->image, 'http'))
                        <img src="{{ $item->image }}" alt="商品画像">
                        @else
                        <img src="{{ asset('storage/' . $item->image) }}" alt="商品画像">
                        @endif
                        @if (request('page') === 'sell')
                        @if ($item->is_sold)
                        <span class="sold__badge">Sold</span>
                        @endif
                        @endif

                </div>
                <div class="item__name">{{ $item->name }}</div>

                @if (request('page') === 'trading')
                </a>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</main>
@endsection