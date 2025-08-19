@extends('layouts.app')
@section('content')
<main class="order-pending">
    <h2>コンビニ支払い（テスト）</h2>
    <p>この方法は実店舗で支払う前提のため、Stripeから自動では戻ってきません。</p>
    <p>下の「支払い手続きへ」を別タブで開いて内容を確認したのち、デモ用の「完了にする」ボタンを押してください。</p>

    {{-- ★ store() で作った Checkout Session をそのまま開く --}}
    <p>
        <a href="{{ $sessionUrl }}" target="_blank" rel="noopener" class="btn">
            支払い手続きへ（Stripeを別タブで開く）
        </a>
    </p>

    {{-- ★ デモの「完了にする」ボタン：DB保存＆Sold化 --}}
    <form method="POST" action="{{ route('orders.mockComplete', $item) }}">
        @csrf
        <input type="hidden" name="payment_method" value="コンビニ払い">
        <button type="submit">（デモ）この注文を完了にする</button>
    </form>
</main>
@endsection