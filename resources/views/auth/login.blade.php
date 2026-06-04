<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - {{ $cafeBrand['name'] ?? config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}?v={{ @filemtime(public_path('css/auth/login.css')) }}">
    <style>
        .shell { animation: fadeIn .3s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        button:disabled { opacity: 0.7; cursor: not-allowed; position: relative; }
        button:disabled::after { content: ""; position: absolute; width: 16px; height: 16px; top: 50%; right: 15px; margin-top: -8px; border: 2px solid #fff; border-radius: 50%; border-right-color: transparent; animation: rotate .6s linear infinite; }
        @keyframes rotate { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    </style>
    <script defer src="{{ asset('js/auth/login.js') }}"></script>
</head>
<body>
    <main class="shell">
        <section class="card">
            @if(isset($cafeBrand['logo']) && $cafeBrand['logo'])
                <div class="logo-wrap">
                    <img src="{{ asset('storage/' . $cafeBrand['logo']) }}" alt="Logo {{ $cafeBrand['name'] ?? 'Cafe' }}">
                </div>
            @endif
            <h2>Login</h2>
            <p>silahkan login!</p>

            @if (session('error'))
                <div class="error">{{ session('error') }}</div>
            @elseif ($errors->any())
                <div class="error">{{ $errors->first() }}</div>
            @endif

            <form id="loginForm" method="POST" action="{{ route('login.store') }}" data-turbo="false" autocomplete="off">
                @csrf
                <div class="field">
                    <label for="username">Username</label>
                    <input id="username" name="username" type="text" value="{{ old('username') }}" autocomplete="username" required autofocus>
                </div>
                <div class="field">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required>
                </div>
                <div class="row">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember" value="1">
                        Ingat saya
                    </label>
                </div>
                <button type="submit">Masuk Dashboard</button>
            </form>
        </section>
    </main>
</body>
</html>
