<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $cafeBrand['name'] ?? config('app.name') }} - Dapur</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-main: #1f1612;
            --bg-card: #2b1f1a;
            --bg-soft: #362722;
            --primary: #f4e7dc;
            --secondary: #c9b6aa;
            --accent: #8b6b5c;
            --highlight: #d4a373;
            --text-main: #f7efe8;
            --text-muted: #ccb7aa;
            --loss: #f28c8c;
            --shadow: rgba(0, 0, 0, 0.25);
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background:
                radial-gradient(circle at top left, rgba(212, 163, 115, 0.22), transparent 32%),
                radial-gradient(circle at bottom right, rgba(139, 107, 92, 0.26), transparent 30%),
                var(--bg-main);
            color: var(--text-main);
        }

        .shell {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px;
        }

        .hero, .panel, .order-card, .stat-card {
            background: linear-gradient(180deg, rgba(43, 31, 26, 0.96), rgba(34, 25, 21, 0.98));
            border: 1px solid rgba(212, 163, 115, 0.22);
            border-radius: 20px;
            box-shadow: 0 12px 30px var(--shadow);
        }

        .hero {
            padding: 24px;
            display: grid;
            gap: 10px;
            margin-bottom: 18px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            background: rgba(212, 163, 115, 0.16);
            color: #f7e6d4;
            padding: 6px 12px;
            border-radius: 999px;
            font-weight: 700;
            font-size: 12px;
            letter-spacing: .5px;
            text-transform: uppercase;
        }

        h1, h2, h3 {
            font-family: 'Playfair Display', Georgia, serif;
            color: var(--primary);
            margin: 0;
        }

        .meta, .muted {
            color: var(--text-muted);
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 14px;
            margin-bottom: 18px;
        }

        .stat-card {
            padding: 18px;
        }

        .stat-card span {
            display: block;
            color: var(--text-muted);
            font-size: 13px;
            margin-bottom: 8px;
        }

        .stat-card strong {
            font-size: 2rem;
            color: #fff;
            font-family: 'Playfair Display', Georgia, serif;
        }

        .panel {
            padding: 20px;
        }

        .panel-head {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            align-items: center;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .orders {
            display: grid;
            gap: 14px;
        }

        .order-card {
            padding: 18px;
            display: grid;
            gap: 14px;
        }

        .order-top {
            display: flex;
            justify-content: space-between;
            gap: 14px;
            flex-wrap: wrap;
            align-items: start;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            padding: 0.28rem 0.75rem;
            border-radius: 999px;
            background: rgba(212, 163, 115, 0.15);
            color: #f8e7d7;
            font-size: 0.8rem;
            font-weight: 700;
        }

        .order-meta {
            display: grid;
            gap: 4px;
            color: var(--text-muted);
            font-size: 0.92rem;
        }

        .order-note {
            padding: 12px 14px;
            background: rgba(255, 255, 255, 0.03);
            border: 1px dashed rgba(212, 163, 115, 0.24);
            border-radius: 14px;
            color: #f7efe8;
        }

        .item-list {
            display: grid;
            gap: 8px;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 10px 12px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.04);
        }

        .logout {
            margin-top: 18px;
            background: var(--highlight);
            color: #fff;
            border: none;
            padding: 0.85rem 1.4rem;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s ease, background-color 0.2s ease;
        }

        .logout:hover {
            background: #c68b59;
            transform: translateY(-2px);
        }

        .empty {
            color: var(--text-muted);
            padding: 12px 2px;
        }

        @media (max-width: 900px) {
            .stats {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <main class="shell">
        <section class="hero">
            <span class="badge">Dapur</span>
            <h1>{{ $cafeBrand['name'] ?? config('app.name') }} - Pesanan Masuk</h1>
            <p class="muted" style="margin:0;">Melihat pesanan baru dari pelanggan, nomor meja, waktu pesanan masuk, dan catatan khusus.</p>
        </section>

        <section class="stats">
            <article class="stat-card">
                <span>Total pesanan hari ini</span>
                <strong>{{ $orderCount }}</strong>
            </article>
            <article class="stat-card">
                <span>Item masuk</span>
                <strong>{{ $itemCount }}</strong>
            </article>
            <article class="stat-card">
                <span>Login sebagai</span>
                <strong style="font-size:1.2rem;">{{ auth()->user()->name }}</strong>
            </article>
        </section>

        <section class="panel">
            <div class="panel-head">
                <div>
                    <h2>Pesanan Masuk</h2>
                    <p class="muted" style="margin:0;">Pesanan terbaru tampil paling atas.</p>
                </div>
            </div>

            <div class="orders">
                @forelse ($orders as $order)
                    <article class="order-card">
                        <div class="order-top">
                            <div>
                                <span class="pill">{{ $order->code }}</span>
                                <h3 style="margin-top:10px;">Meja {{ $order->table?->number ?? '-' }} - {{ $order->table?->name ?? 'Tanpa meja' }}</h3>
                            </div>
                            <div class="order-meta">
                                <span>Waktu masuk: <strong style="color:#fff;">{{ $order->sold_at?->format('d M Y, H:i') }}</strong></span>
                                <span>Total item: <strong style="color:#fff;">{{ $order->items_count }}</strong></span>
                            </div>
                        </div>

                        <div class="item-list">
                            @foreach ($order->items as $item)
                                <div class="item-row">
                                    <span>{{ $item->qty }}x {{ $item->menu?->name ?? 'Menu' }}</span>
                                    <span class="muted">Rp {{ number_format((float) $item->line_total, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>

                        <div class="order-note">
                            <strong style="display:block;margin-bottom:6px;color:#f8e7d7;">Catatan khusus</strong>
                            {{ $order->notes ?: 'Tidak ada catatan khusus.' }}
                        </div>
                    </article>
                @empty
                    <div class="empty">Belum ada pesanan masuk hari ini.</div>
                @endforelse
            </div>
        </section>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="logout" type="submit">Logout</button>
        </form>
    </main>
</body>
</html>
