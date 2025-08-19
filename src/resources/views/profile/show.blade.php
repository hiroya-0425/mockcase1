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
                <div class="profile__username">{{ $user->name }}</div>
                <a href="{{ route('profile.edit') }}" class="edit-profile-button">プロフィールを編集</a>
            </div>
        </div>

        <div class="profile__tabs">
            <a href="/mypage?page=sell" class="tab {{ request('page') !== 'buy' ? 'active' : '' }}">出品した商品</a>
            <a href="/mypage?page=buy" class="tab {{ request('page') === 'buy' ? 'active' : '' }}">購入した商品</a>
        </div>

        <div class="item__grid">
            @foreach ($items as $item)
            <div class="item__card">
                <div class="item__image">
                    @if(Str::startsWith($item->image, 'http'))
                    <img src="{{ $item->image }}" alt="商品画像">
                    @else
                    <img src="{{ asset('storage/' . $item->image) }}" alt="商品画像">
                    @endif
                </div>
                <div class="item__name">{{ $item->name }}</div>
            </div>
            @endforeach
        </div>
    </div>
</main>
@endsection