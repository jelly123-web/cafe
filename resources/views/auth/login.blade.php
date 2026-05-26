<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login {{ $cafeBrand['name'] ?? 'Superadmin' }}</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/auth/login.css', 'resources/js/auth/login.js'])
    @else
        <link rel="stylesheet" href="{{ asset('css/auth/login.css') }}">
        <script defer src="{{ asset('js/auth/login.js') }}"></script>
    @endif
</head>
<body>
    <main class="shell">
        <section class="hero">
            <div class="eyebrow">{{ $cafeBrand['name'] ?? 'Cafe Superadmin' }}</div>
            <h1>Dashboard penjualan lintas cabang.</h1>
            <p>
                Login memakai <strong>username</strong> dan <strong>password</strong>.
                Setelah masuk, superadmin bisa melihat total penjualan semua cabang, transaksi hari ini,
                laba/rugi, dan menu terlaris.
            </p>

            <div class="meta">
                <div>
                    <span>Credential demo</span>
                    <strong>superadmin / superadmin</strong>
                </div>
                <div>
                    <span>Area akses</span>
                    <strong>Superadmin only</strong>
                </div>
            </div>
        </section>

        <section class="card">
            <h2>Masuk</h2>
            <p>Gunakan akun superadmin untuk membuka dashboard.</p>

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
                    <label style="margin:0; display:flex; gap:10px; align-items:center;">
                        <input type="checkbox" name="remember" value="1" style="width:auto; accent-color:#f28b30;">
                        Ingat saya
                    </label>
                </div>
                <button type="submit">Masuk Dashboard</button>
            </form>

            <div class="hint">
                Jika akun belum muncul, jalankan `php artisan db:seed`.
            </div>
        </section>
    </main>
</body>
</html>
