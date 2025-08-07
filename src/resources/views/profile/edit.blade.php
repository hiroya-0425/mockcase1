@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('css/profile/edit.css') }}">
@section('content')
<main class="profile__edit">
    <div class="profile__edit-header">
        <h2>プロフィール設定</h2>
    </div>
    <div class="profile__edit-form-area">
        <form class="form" action="" method="POST">
            @csrf
            @method('PATCH')

            <div class="form__image-section">
                <div class="form__image-preview">
                    <!-- ここに画像プレビューが入る -->
                    <img src="{{ $user->image_url ?? '/default-profile.png' }}" alt="プロフィール画像">
                </div>
                <div class="form__image-upload">
                    <label class="button is-outline">
                        画像を選択する
                        <input type="file" name="image" hidden>
                    </label>
                </div>
            </div>

            <div class="form__group">
                <label class="form__label">ユーザー名</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form__input">
                @error('name')
                <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <label class="form__label">郵便番号</label>
                <input type="text" name="zip_code" value="{{ old('zip_code', $user->zip_code) }}" class="form__input">
                @error('zip_code')
                <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <label class="form__label">住所</label>
                <input type="text" name="address" value="{{ old('address', $user->address) }}" class="form__input">
                @error('address')
                <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <label class="form__label">建物名</label>
                <input type="text" name="building" value="{{ old('building', $user->building) }}" class="form__input">
                @error('building')
                <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__button">
                <button type="submit" class="form__submit">更新する</button>
            </div>
        </form>
    </div>
</main>
@endsection