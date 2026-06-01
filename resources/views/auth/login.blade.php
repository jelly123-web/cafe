<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - {{ $cafeBrand['name'] ?? config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}?v={{ @filemtime(public_path('css/auth/login.css')) }}">
    
    {{-- Turbo for faster transitions --}}
    <script src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@7.3.0/dist/turbo.es2017-umd.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/nprogress@0.2.0/nprogress.css">
    <script src="https://unpkg.com/nprogress@0.2.0/nprogress.js"></script>
    <style>
        #nprogress .bar { background: #795548 !important; height: 3px !important; }
        .shell { animation: fadeIn .3s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        button:disabled { opacity: 0.7; cursor: not-allowed; position: relative; }
        button:disabled::after { content: ""; position: absolute; width: 16px; height: 16px; top: 50%; right: 15px; margin-top: -8px; border: 2px solid #fff; border-radius: 50%; border-right-color: transparent; animation: rotate .6s linear infinite; }
        @keyframes rotate { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    </style>
    <script defer src="{{ asset('js/auth/login.js') }}"></script>
</head>
<body>
    <script>
        document.addEventListener('turbo:click', () => NProgress.start());
        document.addEventListener('turbo:load', () => NProgress.done());
        document.addEventListener('turbo:submit-start', (e) => {
            const btn = e.target.querySelector('button[type="submit"]');
            if (btn) btn.disabled = true;
            NProgress.start();
        });
    </script>
    <main class="shell">
        <section class="card">
            @if(isset($cafeBrand['logo']) && $cafeBrand['logo'])
                <div class="logo-wrap">
                    <img src="{{ asset('storage/' . $cafeBrand['logo']) }}" alt="Logo {{ $cafeBrand['name'] ?? 'Cafe' }}">
                </div>
            @endif
            <h2>Login</h2>
            <p>silahkan login!</p>

            @if ($errors->any())
                <div class="error">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login.store') }}">
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
