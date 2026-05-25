<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/dashboard.css', 'resources/js/dashboard.js'])
    @else
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
        <script defer src="{{ asset('js/dashboard.js') }}"></script>
    @endif
</head>
<body>
    <main class="dashboard-shell">
        <section class="hero">
            <span class="badge">Dashboard pengguna</span>
            <h1>Selamat datang, {{ $user->name }}</h1>
            <p>Halaman ini menampilkan hak akses yang diset oleh superadmin.</p>
        </section>

        <section class="cards">
            @foreach ($permissions as $permission)
                <article class="card {{ $permission['enabled'] ? 'on' : 'off' }}">
                    <span>{{ $permission['label'] }}</span>
                    <strong>{{ $permission['enabled'] ? 'Diizinkan' : 'Tidak diizinkan' }}</strong>
                    <small>{{ $permission['key'] }}</small>
                </article>
            @endforeach
        </section>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="logout" type="submit">Logout</button>
        </form>
    </main>
</body>
</html>
