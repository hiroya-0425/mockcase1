<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COACHTECHフリマ</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />

    <link rel="stylesheet" href="{{ asset('css/auth/verify-email.css') }}" />
</head>

<body>
    <header class="header">
        <div class="header__inner">
            <h1 class="header__inner-logo"><img src="{{ asset('images/logo.svg') }}" alt="COACHTECHロゴ" class="header__inner-image"></h1>
        </div>
    </header>
    <main>
        <div class="verify__inner">
            <p class="verify__text">登録していただいたメールアドレスに認証メールを送付いました。</p>
            <p class="verify__text">メール認証を完了してください。</p>
            <a href="#" class="verify__button">認証はこちらから</a>
            <form method="POST" action="{{ route('verification.send') }}" class="verify__form">
                @csrf
                <button type="submit">認証メールを再送する</button>
            </form>
        </div>
    </main>
</body>

</html>