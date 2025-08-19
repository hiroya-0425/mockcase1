@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('css/items/create.css') }}">
@section('content')
<main class="item__create">
    <h2 class="item__create-title">商品の出品</h2>

    <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data" class="item-create__form">
        @csrf

        {{-- 商品画像 --}}
        <div class="form__group">
            <label class="form__label">商品画像</label>
            <div class="form__image-upload">
                <label class="image__button">
                    画像を選択する
                    <input type="file" name="image" id="imageInput" hidden>
                </label>
            </div>
            {{-- プレビュー表示用 --}}
            <div id="imagePreview" style="margin-top: 10px;">
                <img id="previewImg" src="" alt="" style="max-width: 200px; display: none; border: 1px solid #ccc; padding: 5px;">
            </div>
        </div>

        {{-- 商品の詳細 --}}
        <div class="form__section">
            <h3 class="form__section-title">商品の詳細</h3>

            {{-- カテゴリー --}}
            <div class="form__group">
                <label class="form__label">カテゴリー</label>
                <div class="form__category-buttons">
                    @foreach ($categories as $category)
                    <label class="category__button">
                        <input type="checkbox" name="category_id[]" value="{{ $category->id }}"
                            {{ is_array(old('category_id')) && in_array($category->id, old('category_id')) ? 'checked' : '' }}>
                        <span>{{ $category->name }}</span>
                    </label>
                    @endforeach
                </div>
                @error('category_id')
                <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            {{-- 商品の状態 --}}
            <div class="form__group">
                <label class="form__label">商品の状態</label>
                <select name="condition" class="form__select">
                    <option value="">選択してください</option>
                    <option value="新品">新品</option>
                    <option value="目立った傷や汚れなし">目立った傷や汚れなし</option>
                    <option value="やや傷や汚れあり">やや傷や汚れあり</option>
                    <option value="状態が悪い">状態が悪い</option>
                </select>
                @error('condition')
                <div class="form__error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- 商品名と説明 --}}
        <div class="form__section">
            <h3 class="form__section-title">商品名と説明</h3>
            <div class="form__group">
                <label class="form__label">商品名</label>
                <input type="text" name="name" class="form__input" value="{{ old('name') }}">
                @error('name')
                <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <label class="form__label">ブランド名</label>
                <input type="text" name="brand" class="form__input" value="{{ old('brand') }}">
                @error('brand')
                <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <label class="form__label">商品の説明</label>
                <textarea name="description" class="form__textarea">{{ old('description') }}</textarea>
                @error('description')
                <div class="form__error">{{ $message }}</div>
                @enderror
            </div>

            <div class="form__group">
                <label class="form__label">販売価格</label>
                <div class="form__price-wrapper">
                    <span class="yen-symbol">¥</span>
                    <input type="number" name="price" class="form__input price-input" value="{{ old('price') }}">
                </div>
                @error('price')
                <div class="form__error">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- 出品ボタン --}}
        <div class="form__button">
            <button type="submit" class="form__submit">出品する</button>
        </div>
    </form>
</main>
<script>
    document.getElementById('imageInput').addEventListener('change', function(event) {
        const file = event.target.files[0];
        const previewImg = document.getElementById('previewImg');

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            previewImg.src = '';
            previewImg.style.display = 'none';
        }
    });
</script>
@endsection