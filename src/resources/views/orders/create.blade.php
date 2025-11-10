@extends('layouts.app')
<link rel="stylesheet" href="{{ asset('css/orders/create.css') }}">
@section('content')
<form action="{{ route('orders.store', ['item' => $item->id]) }}" method="POST">
    @csrf
    <main class="order__confirm">
        <div class="order__confirm-container">

            {{-- 左カラム --}}
            <div class="order__confirm-main">
                {{-- 商品情報 --}}
                <div class="order__confirm-product">
                    <div class="order__confirm-image">
                        <img src="{{ $item->image }}" alt="商品画像">
                    </div>
                    <div class="order__confirm-name">
                        <h2>{{ $item->name }}</h2>
                        <p>¥{{ number_format($item->price) }}</p>
                    </div>
                </div>
                {{-- 支払い方法 --}}
                <div class="payment_method__section">
                    <label for="payment_method">支払い方法</label>
                    <select name="payment_method" id="payment_method" onchange="updatePaymentMethod()">
                        <option value="" disabled selected>選択してください</option>
                        <option value="コンビニ払い">コンビニ払い</option>
                        <option value="カード払い">カード払い</option>
                    </select>
                </div>
                {{-- 配送先 --}}
                <div class="order__confirm-address">
                    <h3>配送先</h3>
                    @php
                    $shippingData = session('checkout.shipping', [
                    'zip_code' => $user->zip_code,
                    'address' => $user->address,
                    'building' => $user->building,
                    ]);
                    @endphp
                    <p>〒{{ $shippingData['zip_code'] }}</p>
                    <p>{{ $shippingData['address'] }}{{ $shippingData['building'] }}</p>
                    <a href="{{ route('shippings.edit', ['item' => $item->id]) }}">変更する</a>
                </div>
            </div>
            {{-- 右カラム：サマリーと購入ボタン --}}
            <div class="order__confirm-right">
                <div class="order__confirm-summary">
                    <table>
                        <tr>
                            <th>商品代金</th>
                            <td>¥{{ number_format($item->price) }}</td>
                        </tr>
                        <tr>
                            <th>支払い方法</th>
                            <td id="selected_payment_method">未選択</td>
                        </tr>
                    </table>
                </div>
                <input type="hidden" name="item_id" value="{{ $item->id }}">
                <button type="submit" class="order__confirm-submit">購入する</button>
            </div>
        </div>
</form>
<script>
    function updatePaymentMethod() {
        const select = document.getElementById('payment_method');
        const selectedText = select.options[select.selectedIndex].text;
        const display = document.getElementById('selected_payment_method');

        display.textContent = selectedText ? selectedText : '未選択';
    }
</script>
</main>
@endsection