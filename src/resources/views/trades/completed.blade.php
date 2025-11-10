@component('mail::message')
# 取引完了のお知らせ

{{ $buyer->name }} さんが、以下の商品について取引を完了しました。

---

**商品名：** {{ $item->name }}
**価格：** ¥{{ number_format($item->price) }}

---

取引画面から、{{ $buyer->name }} さんへの評価をお願いします。

今後ともご利用をお待ちしております。
{{ config('app.name') }}
@endcomponent