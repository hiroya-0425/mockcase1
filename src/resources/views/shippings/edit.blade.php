@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('css/shipping/edit.css') }}">
@section('content')
<main class="shipping__edit">
    <h2 class="shipping__edit-title">住所の変更</h2>

    <form action="{{ route('shippings.update', ['item' => $item->id]) }}" method="POST" class="form">
        @csrf
        @method('PATCH')

        <div class="form__group">
            <label for="zip_code">郵便番号</label>
            <input type="text" name="zip_code" value="{{ old('zip_code', $user->zip_code) }}">
        </div>

        <div class="form__group">
            <label for="address">住所</label>
            <input type="text" name="address" value="{{ old('address', $user->address) }}">
        </div>

        <div class="form__group">
            <label for="building">建物名</label>
            <input type="text" name="building" value="{{ old('building', $user->building) }}">
        </div>

        <button type="submit" class="form__submit">更新する</button>
    </form>
</main>
@endsection