<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $cafeBrand['name'] ?? config('app.name') }} - Meja {{ $table->number }}</title>
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <style>
        :root {
            --bg-main: #f9f5f0;
            --bg-card: #ffffff;
            --primary: #795548;
            --accent: #d7ccc8;
            --highlight: #d4a373;
            --text-main: #6d4c41;
            --text-muted: #a1887f;
            --shadow: rgba(121, 85, 72, 0.08);
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: var(--bg-main);
            color: var(--text-main);
            background-image: radial-gradient(var(--accent) 1px, transparent 1px);
            background-size: 24px 24px;
        }
        .shell { max-width: 1100px; margin: 0 auto; padding: 24px; }
        .hero, .section, .card {
            background: var(--bg-card);
            border: 1px solid var(--accent);
            border-radius: 20px;
            box-shadow: 0 4px 15px var(--shadow);
        }
        .hero { padding: 24px; display: grid; gap: 8px; margin-bottom: 20px; }
        .badge {
            display: inline-flex; align-items: center; width: fit-content;
            background: var(--highlight); color: #fff; padding: 6px 12px; border-radius: 999px;
            font-weight: 700; font-size: 12px; letter-spacing: .5px; text-transform: uppercase;
        }
        h1, h2 { font-family: 'Playfair Display', Georgia, serif; color: var(--primary); margin: 0; }
        .meta { color: var(--text-muted); }
        .section { padding: 20px; margin-bottom: 20px; }
        .category-nav { display:flex; gap:10px; flex-wrap:wrap; margin-bottom: 18px; }
        .category-nav a {
            text-decoration:none; color: var(--primary); background:#efebe9; padding:8px 14px; border-radius:999px;
            font-weight:600;
        }
        .menu-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap: 14px; }
        .card { padding: 16px; }
        .card strong { display:block; color: var(--primary); margin-bottom: 4px; }
        .price { font-weight: 700; }
        .empty { color: var(--text-muted); font-style: italic; }
        .category-block { margin-top: 22px; }
        .category-title { margin-bottom: 12px; }
        @media (max-width: 700px) {
            .shell { padding: 16px; }
            .menu-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <main class="shell">
        <section class="hero">
            <span class="badge">Scan dari Meja {{ $table->number }}</span>
            <h1>{{ $cafeBrand['name'] ?? config('app.name') }}</h1>
            <div class="meta">Meja: <strong>{{ $table->name }}</strong> | Nomor meja: <strong>{{ $table->number }}</strong></div>
            <p style="margin:0;color:var(--text-muted);">Halaman ini muncul setelah QR meja dipindai. Pelanggan harus melihat nomor meja ini sebelum memesan.</p>
        </section>

        <section class="section">
            <h2>Menu</h2>
            <div class="category-nav">
                <a href="#semua">Semua</a>
                @foreach ($categories as $category)
                    <a href="#category-{{ $category->id }}">{{ $category->name }}</a>
                @endforeach
            </div>

            <div id="semua" class="category-block">
                <h3 class="category-title">Semua Menu</h3>
                <div class="menu-grid">
                    @forelse ($menus as $menu)
                        <article class="card" id="menu-{{ $menu->id }}">
                            <strong>{{ $menu->name }}</strong>
                            <div style="color:var(--text-muted);margin-bottom:6px;">{{ $menu->category?->name ?? 'Tanpa kategori' }}</div>
                            <div class="price">Rp {{ number_format((float) $menu->selling_price, 0, ',', '.') }}</div>
                        </article>
                    @empty
                        <div class="empty">Belum ada menu.</div>
                    @endforelse
                </div>
            </div>

            @foreach ($categories as $category)
                <div id="category-{{ $category->id }}" class="category-block">
                    <h3 class="category-title">{{ $category->name }}</h3>
                    <div class="menu-grid">
                        @forelse ($category->menus as $menu)
                            <article class="card">
                                <strong>{{ $menu->name }}</strong>
                                <div style="color:var(--text-muted);margin-bottom:6px;">{{ $menu->code }}</div>
                                <div class="price">Rp {{ number_format((float) $menu->selling_price, 0, ',', '.') }}</div>
                            </article>
                        @empty
                            <div class="empty">Belum ada menu di kategori ini.</div>
                        @endforelse
                    </div>
                </div>
            @endforeach
        </section>
    </main>
</body>
</html>
