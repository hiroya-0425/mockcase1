<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>COACHTECH フリマ</title>
    <link rel="stylesheet" href="{{ asset('css/common.css') }}">
    @yield('css')
</head>

<body>
    {{-- ヘッダー --}}
    <header>
        <div class="header__logo">
            <a href="{{ route('items.showIndex') }}">
                <img src="{{ asset('images/logo.svg') }}" alt="COACHTECHロゴ" class="logo__image">
            </a>
        </div>
        <form class="header__search-form" action="{{ route('items.showIndex') }}" method="GET">
            @csrf
            <input type="text" value="{{ request('search') }}" name="search" class="header__search-input" placeholder="なにをお探しですか？">
        </form>
        <div class="header__nav-buttons">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav__button">ログアウト</button>
            </form>
            <a href="/mypage" class="nav__button">マイページ</a>
            <a href="/sell" class="nav__button sell">出品</a>
        </div>
    </header>

    {{-- 各ページごとの中身 --}}
    <main>
        @yield('content')
    </main>
</body>

</html>