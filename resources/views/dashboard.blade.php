<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $cafeBrand['name'] ?? config('app.name') }} - Dashboard</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/dashboard.css', 'resources/js/dashboard.js'])
    @else
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
        <script defer src="{{ asset('js/dashboard.js') }}"></script>
    @endif
  @include('components.page-transition-guard')
</head>
<body>
    <main class="dashboard-shell">
        <section class="hero">
            <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 4px solid #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 1rem;">
            <span class="badge">Dashboard pengguna</span>
            <h1>Selamat datang di {{ $cafeBrand['name'] ?? config('app.name') }}, {{ $user->name }}</h1>
            <p>Halaman ini menampilkan hak akses yang diset oleh superadmin.</p>
            <p style="margin-top: 1rem;">
                <a href="{{ route('profile.edit') }}" style="color: #795548; font-weight: 600; text-decoration: none;">Edit Profil &rarr;</a>
            </p>
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

        @if (auth()->user()?->hasPermission('manage_orders') || auth()->user()?->hasPermission('view_all_orders'))
            <p style="text-align:center; margin-top: 1rem;">
                <a href="{{ route('cashier.orders.index') }}">Buka Halaman Pesanan Kasir</a>
            </p>
        @endif

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="logout" type="submit">Logout</button>
        </form>
    </main>
</body>
</html>
