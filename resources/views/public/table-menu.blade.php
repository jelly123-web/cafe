<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $cafeBrand['name'] ?? config('app.name') }} - Meja {{ $table->number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <style>
        :root {
            --bg-main: #F9F5F0;
            --bg-card: #FFFFFF;
            --primary: #795548;
            --secondary: #BCAAA4;
            --accent: #D7CCC8;
            --highlight: #D4A373;
            --text-main: #6D4C41;
            --text-muted: #A1887F;
            --profit: #81C784;
            --loss: #E57373;
            --shadow: rgba(121, 85, 72, 0.08);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-main);
            color: var(--text-main);
            line-height: 1.6;
            background-image: radial-gradient(var(--accent) 1px, transparent 1px);
            background-size: 24px 24px;
            padding-bottom: 24px;
        }

        .shell { max-width: 900px; margin: 0 auto; padding: 2rem 1.5rem; }

        .hero, .section, .card {
            background: var(--bg-card);
            border: 1px solid var(--accent);
            border-radius: 20px;
            box-shadow: 0 4px 15px var(--shadow);
        }

        .hero {
            padding: 2rem;
            margin-bottom: 1.5rem;
            text-align: center;
            border-bottom: 4px solid var(--highlight);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(212, 163, 115, 0.15);
            color: var(--highlight);
            padding: 0.4rem 1rem;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.8rem;
            letter-spacing: .5px;
            text-transform: uppercase;
            margin-bottom: 1rem;
        }

        h1, h2 { font-family: 'Playfair Display', Georgia, serif; color: var(--primary); margin: 0; }
        .hero h1 { font-size: 2.5rem; margin-bottom: 0.5rem; }
        .meta { color: var(--text-muted); margin-bottom: 0.5rem; font-size: 1.1rem; }
        .meta strong { color: var(--primary); }
        .hero-desc { color: var(--text-muted); font-size: 0.95rem; margin: 0; }

        .section { padding: 2rem; margin-bottom: 2rem; }
        .section h2 { font-size: 1.5rem; margin-bottom: 1rem; }
        .section h3 {
            font-size: 1.08rem;
            margin-bottom: 0.9rem;
            color: var(--primary);
            font-family: 'Playfair Display', Georgia, serif;
        }
        .section-block + .section-block {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px dashed var(--accent);
        }
        .section-block h3 {
            font-size: 1.05rem;
            margin-bottom: 0.85rem;
            color: var(--primary);
            font-family: 'Playfair Display', Georgia, serif;
        }

        .package-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 0.85rem;
        }

        .package-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem;
        }

        .package-chip {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.55rem;
            border-radius: 999px;
            border: 1px solid var(--accent);
            background: #fff;
            color: var(--text-main);
            font-size: 0.68rem;
            font-weight: 600;
            line-height: 1.15;
        }

        .package-chip-muted {
            color: var(--text-muted);
            background: rgba(255, 255, 255, 0.55);
        }

        .package-free-badge {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            padding: 0.28rem 0.65rem;
            border-radius: 999px;
            background: #fff3e0;
            color: #ef6c00;
            font-size: 0.68rem;
            font-weight: 700;
            border: 1px solid #ffd59e;
        }

        .status-wrap {
            display: grid;
            gap: 0.9rem;
        }

        .promo-wrap {
            display: grid;
            gap: 0.9rem;
        }

        .promo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 0.75rem;
            align-items: stretch;
        }

        .promo-card {
            border: 1px solid var(--accent);
            border-radius: 16px;
            background: linear-gradient(180deg, #fffaf5 0%, #fff 100%);
            padding: 0;
            overflow: hidden;
            box-shadow: 0 4px 15px var(--shadow);
        }

        .promo-card-inner {
            display: grid;
            gap: 0;
        }

        .promo-card-media {
            background: linear-gradient(135deg, rgba(212, 163, 115, 0.12), rgba(255, 248, 241, 0.9));
            padding: 0.65rem;
        }

        .promo-card-banner {
            width: 100%;
            height: 112px;
            object-fit: cover;
            border-radius: 12px;
            border: 1px solid rgba(215, 204, 200, 0.95);
            background: #f7eee7;
            display: block;
        }

        .promo-card-copy {
            padding: 0.7rem 0.8rem 0.8rem;
            display: grid;
            gap: 0.55rem;
        }

        .promo-card-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .promo-card-kicker,
        .promo-card-status {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 0.22rem 0.58rem;
            font-size: 0.64rem;
            font-weight: 700;
            letter-spacing: 0.2px;
        }

        .promo-card-kicker {
            background: rgba(212, 163, 115, 0.18);
            color: var(--highlight);
        }

        .promo-card-status {
            background: #e8f5e9;
            color: #2e7d32;
        }

        .promo-card-title {
            color: var(--primary);
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 1rem;
            line-height: 1.15;
            letter-spacing: 0.1px;
        }

        .promo-card-desc {
            color: var(--text-muted);
            font-size: 0.76rem;
            line-height: 1.4;
        }

        .promo-featured-box {
            display: grid;
            gap: 0.28rem;
            padding: 0.65rem 0.75rem;
            border-radius: 14px;
            background: linear-gradient(180deg, #fff7ef 0%, #fffdfb 100%);
            border: 1px solid rgba(212, 163, 115, 0.25);
        }

        .promo-featured-title {
            color: var(--primary);
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 0.9rem;
            line-height: 1.15;
        }

        .promo-featured-price {
            display: grid;
            gap: 0.14rem;
            color: var(--text-main);
        }

        .promo-featured-price-label {
            color: var(--text-muted);
            font-size: 0.66rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.25px;
        }

        .promo-featured-price strong {
            color: var(--primary);
            font-size: 0.92rem;
        }

        .promo-featured-price--promo strong {
            color: #d16d2a;
            font-size: 1rem;
        }

        .promo-card-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.38rem;
        }

        .promo-card-meta span {
            display: inline-flex;
            align-items: center;
            padding: 0.22rem 0.46rem;
            border-radius: 999px;
            background: #fff;
            color: var(--text-main);
            border: 1px solid var(--accent);
            font-size: 0.63rem;
            font-weight: 600;
        }

        .promo-card-actions {
            display: flex;
            align-items: center;
            gap: 0.45rem;
            flex-wrap: wrap;
        }

        .promo-action-btn {
            appearance: none;
            border: none;
            border-radius: 999px;
            padding: 0.58rem 0.9rem;
            background: linear-gradient(135deg, var(--highlight), #c98a50);
            color: #fff;
            font-weight: 800;
            font-size: 0.78rem;
            cursor: pointer;
            box-shadow: 0 8px 18px rgba(212, 163, 115, 0.25);
            transition: transform 0.18s ease, box-shadow 0.18s ease, opacity 0.18s ease;
        }

        .promo-action-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 10px 22px rgba(212, 163, 115, 0.32);
        }

        .promo-card-hint {
            color: var(--text-muted);
            font-size: 0.7rem;
        }

        .promo-card--all .promo-card-media {
            background: linear-gradient(135deg, rgba(212, 163, 115, 0.18), rgba(255, 248, 241, 0.92));
        }

        .promo-order-group {
            display: grid;
            gap: 0.42rem;
            padding-top: 0.05rem;
            border-top: 1px dashed rgba(212, 163, 115, 0.25);
        }

        .promo-order-label {
            color: var(--primary);
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.2px;
        }

        .promo-order-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem;
        }

        .promo-order-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.28rem 0.55rem;
            border-radius: 999px;
            border: 1px solid rgba(212, 163, 115, 0.7);
            background: #fff;
            color: var(--primary);
            font-size: 0.63rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.18s ease;
        }

        .promo-order-chip:hover {
            background: #fff2e3;
            border-color: var(--highlight);
            color: var(--highlight);
            transform: translateY(-1px);
        }

        .promo-empty {
            color: var(--text-muted);
            font-style: italic;
            padding: 1rem 1.1rem;
            border: 1px dashed var(--accent);
            border-radius: 14px;
            background: #fffaf5;
        }

        .order-status-card {
            border: 1px solid var(--accent);
            border-radius: 14px;
            background: #fff;
            padding: 0.75rem 1rem;
            transition: all 0.2s ease;
        }

        .order-status-card:hover {
            border-color: var(--highlight);
            background: #FFFAF5;
        }

        .order-status-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 0.2rem;
        }

        .order-status-head strong {
            color: var(--primary);
            font-size: 0.95rem;
            font-family: 'Playfair Display', Georgia, serif;
        }

        .order-status-meta {
            color: var(--text-muted);
            font-size: 0.8rem;
            margin-bottom: 0.4rem;
            border-bottom: 1px dashed var(--accent);
            padding-bottom: 0.4rem;
        }

        .order-status-items {
            color: var(--text-main);
            font-size: 0.85rem;
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .order-status-items div {
            background: var(--bg-main);
            padding: 0.15rem 0.5rem;
            border-radius: 6px;
            border: 1px solid var(--accent);
        }

        /* Toast System */
        .toast-wrap {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: min(340px, 90vw);
            pointer-events: none;
        }
        .toast-item {
            background: #38251e;
            color: #fff;
            padding: 12px 20px;
            border-radius: 14px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            font-size: 0.9rem;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            pointer-events: auto;
            animation: toast-in 0.3s ease forwards;
            transition: opacity 0.2s, transform 0.2s;
        }
        .toast-item.success { border-left: 4px solid var(--profit); }
        .toast-item.error { border-left: 4px solid var(--loss); }
        .toast-item button {
            background: transparent;
            border: none;
            color: rgba(255,255,255,0.6);
            cursor: pointer;
            font-size: 1.1rem;
            padding: 0 4px;
        }
        @keyframes toast-in {
            from { opacity: 0; transform: translateY(-15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.7rem;
            border-radius: 999px;
            font-weight: 700;
            font-size: 0.78rem;
            letter-spacing: 0.3px;
        }
        .status-pending { background: #FFF3E0; color: #ef6c00; }
        .status-processing { background: #E3F2FD; color: #1565c0; }
        .status-ready { background: #E8F5E9; color: #2e7d32; }
        .status-completed { background: #E8F5E9; color: #1b5e20; }
        .status-cancelled { background: #FFEBEE; color: #c62828; }

        .alert-ok, .alert-err {
            border-radius: 12px;
            padding: 0.85rem 1.25rem;
            margin-bottom: 1rem;
            font-weight: 500;
            font-size: 0.95rem;
            border: 1px solid transparent;
        }
        .alert-ok { background: #E8F5E9; border-color: #C8E6C9; color: #558B2F; }
        .alert-err { background: #FFEBEE; border-color: #FFCDD2; color: #C62828; }

        .category-nav {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid var(--accent);
            padding-bottom: 1rem;
        }
        .category-btn {
            border: 1px solid var(--accent);
            background: #fff;
            color: var(--primary);
            padding: 0.5rem 1.25rem;
            border-radius: 999px;
            font-weight: 600;
            cursor: pointer;
            font-family: inherit;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }
        .category-btn:hover { background: var(--bg-main); }
        .category-btn.active {
            background: var(--highlight);
            color: #fff;
            border-color: var(--highlight);
            box-shadow: 0 2px 8px rgba(212, 163, 115, 0.3);
        }

        .menu-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem; }
        .card {
            border-radius: 20px;
            position: relative;
            overflow: hidden;
            background:
                linear-gradient(180deg, rgba(255,255,255,0.96), rgba(255,250,245,0.98)),
                var(--bg-card);
        }
        .menu-card { 
            display: grid;
            grid-template-columns: 92px minmax(0, 1fr);
            grid-template-areas:
                "image info"
                "badge badge";
            align-items: start;
            gap: 0.9rem 1rem;
            padding: 1.15rem;
            cursor: pointer; 
            transition: all .2s ease;
            min-height: 100%;
            animation: fadeIn .4s ease;
            border: 1px solid transparent;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .menu-card:hover {
            transform: translateY(-2px);
            border-color: var(--highlight);
            box-shadow: 0 8px 25px rgba(121, 85, 72, 0.12);
        }
        .menu-card.in-cart {
            border-color: var(--highlight);
            background: #FFFAF5;
            box-shadow: 0 4px 12px rgba(212, 163, 115, 0.1);
        }

        .public-package-card {
            background:
                linear-gradient(180deg, rgba(255,248,238,0.98), rgba(255,255,255,0.98));
            grid-template-columns: 72px minmax(0, 1fr);
            grid-template-areas:
                "image info"
                "badge badge";
            gap: 0.7rem 0.85rem;
            padding: 0.9rem;
        }

        .public-package-card .menu-info {
            gap: 0.22rem;
        }

        .public-package-card .menu-img {
            height: 72px;
            border-radius: 16px;
        }

        .public-package-card .menu-title {
            font-size: 1rem;
        }

        .public-package-card .menu-category {
            font-size: 0.74rem;
        }

        .public-package-card .price {
            font-size: 0.98rem;
        }

        .public-package-card .package-free-badge {
            font-size: 0.64rem;
            padding: 0.24rem 0.55rem;
        }

        .public-package-card .package-chips {
            gap: 0.28rem;
        }

        .public-package-card .package-chip {
            font-size: 0.64rem;
            padding: 0.22rem 0.5rem;
        }

        .menu-img {
            grid-area: image;
            width: 100%;
            height: 92px;
            border-radius: 18px;
            object-fit: cover;
            background: linear-gradient(180deg, #f8f0ea, #fff);
            flex-shrink: 0;
            border: 1px solid var(--accent);
            padding: 0.25rem;
        }

        .menu-info {
            grid-area: info;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            gap: 0.3rem;
            min-width: 0;
        }

        .menu-title { 
            display: block; 
            color: var(--primary); 
            font-family: 'Playfair Display', Georgia, serif; 
            font-size: 1.15rem; 
            font-weight: 700; 
            line-height: 1.2;
        }
        .menu-category {
            color: var(--text-muted);
            font-size: 0.8rem;
            text-transform: lowercase;
            letter-spacing: 0.2px;
        }
        .price {
            font-weight: 800;
            color: var(--highlight);
            font-size: 1.1rem;
            margin-top: 0.05rem;
        }

        .promo-price-wrap {
            display: grid;
            gap: 0.18rem;
            align-items: start;
        }

        .price-original {
            color: var(--text-muted);
            font-size: 0.82rem;
            text-decoration: line-through;
            text-decoration-thickness: 1.5px;
        }

        .price-promo-badge {
            display: inline-flex;
            align-items: center;
            width: fit-content;
            padding: 0.2rem 0.55rem;
            border-radius: 999px;
            background: #fff3e0;
            color: #ef6c00;
            border: 1px solid #ffd59e;
            font-size: 0.68rem;
            font-weight: 700;
        }

        .qty-badge {
            grid-area: badge;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 4px;
            border: 1px solid var(--accent);
            background: rgba(255,255,255,0.92);
            padding: 0.65rem 0.8rem;
            border-radius: 14px;
            font-size: 0.78rem;
            color: var(--text-muted);
            font-weight: 700;
            width: 100%;
        }

        .public-package-card .qty-badge {
            grid-area: badge;
            width: fit-content;
            justify-content: flex-start;
            justify-self: start;
            padding: 0.42rem 0.7rem;
            font-size: 0.7rem;
        }
        .in-cart .qty-badge { background: rgba(212, 163, 115, 0.15); color: var(--highlight); border-color: var(--highlight); }

        .empty { color: var(--text-muted); font-style: italic; padding: 2rem 0; text-align: center; }

        .cart-summary {
            display: grid;
            gap: 0.75rem;
        }

        .cart-fab {
            position: fixed;
            right: 1.1rem;
            bottom: 1.1rem;
            z-index: 1300;
            width: 58px;
            height: 58px;
            border: 0;
            border-radius: 999px;
            background: var(--highlight);
            color: #fff;
            box-shadow: 0 16px 30px rgba(212, 163, 115, 0.35);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.18s ease, box-shadow 0.18s ease, background 0.18s ease;
        }

        .cart-fab:hover {
            transform: translateY(-2px);
            background: #c78d5d;
            box-shadow: 0 18px 34px rgba(212, 163, 115, 0.42);
        }

        .cart-fab:focus-visible {
            outline: 3px solid rgba(212, 163, 115, 0.35);
            outline-offset: 3px;
        }

        .cart-fab-icon {
            font-size: 1.35rem;
            line-height: 1;
        }

        .cart-fab-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            min-width: 22px;
            height: 22px;
            padding: 0 6px;
            border-radius: 999px;
            background: #fff;
            color: var(--highlight);
            border: 1px solid rgba(212, 163, 115, 0.35);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.72rem;
            font-weight: 700;
            box-shadow: 0 8px 16px rgba(48, 28, 21, 0.12);
        }

        .cart-drawer-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(56, 37, 30, 0.3);
            backdrop-filter: blur(2px);
            z-index: 1298;
            opacity: 0;
            visibility: hidden;
            transition: 0.2s ease;
        }

        .cart-drawer-backdrop.open {
            opacity: 1;
            visibility: visible;
        }

        .cart-drawer {
            position: fixed;
            right: 1rem;
            bottom: 5.4rem;
            z-index: 1299;
            width: min(380px, calc(100vw - 2rem));
            max-height: min(70vh, 560px);
            background: linear-gradient(180deg, #fffdfb 0%, #fff 100%);
            border: 1px solid var(--accent);
            border-radius: 20px;
            box-shadow: 0 22px 40px rgba(79, 53, 43, 0.22);
            display: grid;
            grid-template-rows: auto 1fr auto;
            overflow: hidden;
            transform: translateY(18px) scale(0.96);
            opacity: 0;
            pointer-events: none;
            transition: transform 0.2s ease, opacity 0.2s ease;
        }

        .cart-drawer.open {
            transform: translateY(0) scale(1);
            opacity: 1;
            pointer-events: auto;
        }

        .cart-drawer-head {
            padding: 1rem 1.1rem;
            border-bottom: 1px solid var(--accent);
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.75rem;
            background: #fff;
        }

        .cart-drawer-head h2 {
            margin: 0;
            font-family: 'Playfair Display', Georgia, serif;
            color: var(--primary);
            font-size: 1.2rem;
        }

        .cart-drawer-close {
            border: 1px solid var(--accent);
            background: #fff;
            color: var(--primary);
            border-radius: 12px;
            width: 36px;
            height: 36px;
            cursor: pointer;
            font-weight: 700;
        }

        .cart-drawer-body {
            padding: 1rem 1.1rem;
            overflow-y: auto;
            display: grid;
            gap: 0.75rem;
        }

        .cart-drawer-foot {
            padding: 0.95rem 1.1rem 1.1rem;
            border-top: 1px solid var(--accent);
            background: #fff;
            display: grid;
            gap: 0.85rem;
        }

        .cart-empty {
            color: var(--text-muted);
            font-style: italic;
            padding: 0.85rem 1rem;
            border: 1px dashed var(--accent);
            border-radius: 14px;
            background: #fffaf5;
        }

        .cart-list {
            display: grid;
            gap: 0.65rem;
        }

        .cart-row {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 1rem;
            padding: 0.85rem 1rem;
            border: 1px solid var(--accent);
            border-radius: 14px;
            background: #fff;
        }

        .cart-row-main { min-width: 0; }

        .cart-row strong {
            display: block;
            color: var(--primary);
            margin-bottom: 0.1rem;
        }

        .cart-row small { color: var(--text-muted); display:block; }
        .cart-row-promo { margin-top: 0.25rem; color: var(--loss); font-size: 0.78rem; font-weight: 700; }
        .cart-row-actions { display:grid; justify-items:end; gap:0.45rem; }
        .cart-remove-btn {
            border: 1px solid rgba(229, 115, 115, 0.55);
            background: #fff;
            color: var(--loss);
            border-radius: 10px;
            padding: 0.32rem 0.75rem;
            font-size: 0.78rem;
            font-weight: 700;
            cursor: pointer;
        }
        .cart-remove-btn:hover { background:#ffebee; }

        .cart-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            border-top: 1px dashed var(--accent);
            padding-top: 0.85rem;
            margin-top: 0.25rem;
        }

        .cart-total strong {
            color: var(--primary);
            font-size: 1.2rem;
            font-family: 'Playfair Display', Georgia, serif;
        }

        .btn {
            border: 0; border-radius: 12px; padding: 0.65rem 1.5rem;
            background: var(--highlight); color: #fff; font-weight: 700;
            cursor: pointer; font-family: inherit; font-size: 0.95rem;
            text-decoration: none; transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(212, 163, 115, 0.3);
        }
        .btn:hover { background: #c68b59; transform: translateY(-1px); }
        .btn:disabled { opacity: .5; cursor: not-allowed; transform: none; background: var(--secondary); box-shadow: none; }
        .cart-drawer .btn { width: 100%; justify-content: center; }

        .modal-backdrop {
            position: fixed; inset: 0; background: rgba(62, 39, 35, 0.5);
            backdrop-filter: blur(4px); opacity: 0; visibility: hidden; transition: .25s ease; z-index: 1000;
        }
        .modal-backdrop.open { opacity: 1; visibility: visible; }
        .order-modal {
            position: fixed; left: 50%; top: 50%;
            transform: translate(-50%, -45%);
            width: min(480px, 92vw); background: #fff;
            border: 1px solid var(--accent); border-radius: 24px;
            box-shadow: 0 20px 50px rgba(62, 39, 35, 0.25);
            opacity: 0; visibility: hidden; transition: .25s ease; z-index: 1001;
            display: grid; grid-template-rows: auto 1fr auto;
        }
        .order-modal.open { opacity: 1; visibility: visible; transform: translate(-50%, -50%); }

        .modal-head, .modal-foot { padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; gap: 10px; }
        .modal-head { border-bottom: 1px solid var(--accent); }
        .modal-head strong { font-family: 'Playfair Display', Georgia, serif; font-size: 1.3rem; color: var(--primary); text-transform: lowercase; }
        .modal-foot { border-top: 1px solid var(--accent); justify-content: flex-end; gap: 0.75rem; }
        .modal-body { padding: 1.5rem; display: flex; flex-direction: column; gap: 1rem; overflow-y: auto; }
        .modal-body label { font-size: 0.85rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        .modal-close { border: 1px solid var(--accent); background: #fff; color: var(--primary); border-radius: 8px; padding: 0.4rem 0.75rem; font-weight: 600; cursor: pointer; transition: all 0.2s ease; }
        .modal-close:hover { background: var(--bg-main); }

        .qty-control-modal { display: flex; align-items: center; gap: 1.25rem; background: var(--bg-main); padding: 0.5rem; border-radius: 16px; border: 1px solid var(--accent); width: fit-content; margin: 0 auto; }
        .qty-btn-modal { width: 44px; height: 44px; border-radius: 12px; border: none; background: #fff; color: var(--primary); cursor: pointer; font-size: 1.5rem; font-weight: 700; display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 10px rgba(0,0,0,0.05); transition: all 0.2s; }
        .qty-btn-modal:hover { background: var(--highlight); color: #fff; }
        .qty-val-modal { font-size: 1.5rem; font-weight: 800; color: var(--primary); min-width: 40px; text-align: center; }

        .qty-input {
            width: 100%; border: 1px solid var(--accent); border-radius: 12px;
            padding: 0.75rem 1rem; font-family: inherit; font-size: 1rem; color: var(--text-main);
            background: var(--bg-main); outline: none; transition: all 0.2s ease; text-align: center;
        }

        .btn-light { border: 1px solid var(--loss); background: #fff; color: var(--loss); border-radius: 12px; padding: 0.65rem 1.5rem; font-weight: 700; cursor: pointer; transition: all 0.2s ease; }
        .btn-light:hover { background: #FFEBEE; }
        .total-line { display: flex; justify-content: space-between; font-weight: 700; color: var(--primary); font-size: 1.1rem; margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed var(--accent); }
        .modal-promo-box {
            display:none;
            padding: 0.85rem 1rem;
            border: 1px dashed var(--accent);
            border-radius: 14px;
            background: #fffaf5;
            color: var(--text-main);
            font-size: 0.88rem;
            line-height: 1.5;
        }
        .modal-promo-box strong { color: var(--primary); display:block; margin-bottom:0.2rem; }

        @media (max-width: 700px) {
            .order-modal { width: min(94vw, 480px); }
            .modal-head, .modal-foot { padding: 1rem 1.1rem; }
            .modal-body { padding: 1rem 1.1rem; gap: 0.85rem; }
            .shell { padding: 0.75rem; }
            .hero, .section { padding: 1rem; border-radius: 16px; margin-bottom: 1rem; }
            .menu-grid { grid-template-columns: 1fr; gap: 0.75rem; }
            .package-grid { grid-template-columns: 1fr; gap: 0.75rem; }
            .promo-grid { grid-template-columns: 1fr; gap: 0.75rem; }
            .hero h1 { font-size: 1.8rem; }
            .menu-card,
            .public-package-card {
                grid-template-columns: 78px minmax(0, 1fr);
                gap: 0.85rem;
                padding: 0.95rem;
            }
            .menu-img { height: 78px; border-radius: 16px; }
            .menu-title { font-size: 1rem; }
            .price { font-size: 0.95rem; }
            .badge { font-size: 0.7rem; padding: 0.3rem 0.8rem; }
            .package-chips { gap: 0.35rem; }
            .package-chip { font-size: 0.7rem; padding: 0.28rem 0.55rem; }
            .promo-card-title { font-size: 0.92rem; }
            .promo-card-banner { height: 92px; }
            .promo-card-copy { padding: 0.65rem; }
        }
    </style>
</head>
<body>
    <main class="shell">
        <section class="hero">
            <span class="badge">Scan dari Meja {{ $table->number }}</span>
            <h1>{{ $cafeBrand['name'] ?? config('app.name') }}</h1>
            <div class="meta">Meja: <strong>{{ $table->name }}</strong> | Nomor meja: <strong>{{ $table->number }}</strong></div>
            <p class="hero-desc">Pilih menu lalu klik kartu menu untuk isi jumlah pesanan.</p>
        </section>

        <section class="section" id="promoSection">
            <h2>Promo Aktif</h2>
            <div class="promo-wrap" id="promoWrap">
                @include('public.partials.promo-strip', ['promos' => $promos])
            </div>
        </section>

        <section class="section">
            <h2>Status Pesanan Meja Ini</h2>
            <div class="status-wrap" id="tableOrderStatusWrap">
                @include('public.partials.table-order-status', ['orders' => $orders])
            </div>
        </section>

        <section class="section" id="menuSection">
            <h2>Menu dan Paket</h2>

            <div class="section-block">
                <h3>Paket Tersedia</h3>
                <div class="package-grid" id="packageGrid">
                    @include('public.partials.package-grid', ['packages' => $packages])
                </div>
            </div>

            <div class="section-block">
                <h3>Menu</h3>
                <div class="category-nav" id="categoryNav">
                    @include('public.partials.menu-categories', ['categories' => $categories, 'activeFilter' => 'all'])
                </div>

                <div class="menu-grid" id="menuGrid">
                    @include('public.partials.menu-grid', ['menus' => $menus])
                </div>
            </div>

            <form method="POST" action="{{ route('tables.order', $table->qr_token) }}" id="orderForm">
                @csrf
                <div id="orderItemsContainer"></div>
            </form>
        </section>
    </main>

    <button type="button" class="cart-fab" id="cartFab" aria-controls="cartDrawer" aria-expanded="false" aria-label="Buka keranjang pesanan">
        <span class="cart-fab-icon">🛒</span>
        <span class="cart-fab-badge" id="cartFabBadge">0</span>
    </button>

    <div class="cart-drawer-backdrop" id="cartDrawerBackdrop"></div>
    <section class="cart-drawer" id="cartDrawer" aria-hidden="true">
        <div class="cart-drawer-head">
            <h2>Keranjang</h2>
            <button type="button" class="cart-drawer-close" id="closeCartDrawerBtn" aria-label="Tutup keranjang">×</button>
        </div>
        <div class="cart-drawer-body">
            <div class="cart-summary" id="cartSummary">
                <div class="cart-empty" id="cartEmptyState">Belum ada item dipilih. Ketuk menu atau paket untuk menambah ke keranjang.</div>
                <div class="cart-list" id="cartList"></div>
                <div class="cart-total">
                    <span>Total Pesanan</span>
                    <strong id="cartTotalPrice">Rp 0</strong>
                </div>
            </div>
        </div>
        <div class="cart-drawer-foot">
            <button type="submit" form="orderForm" class="btn" id="submitOrderBtn" disabled>Kirim Pesanan</button>
        </div>
    </section>

    <div class="modal-backdrop" id="modalBackdrop"></div>
    <section class="order-modal" id="orderModal" aria-hidden="true">
        <div class="modal-head">
            <strong id="modalMenuName">Menu</strong>
            <button type="button" class="modal-close" id="closeModalBtn">Tutup</button>
        </div>
        <div class="modal-body">
            <div class="price" id="modalMenuPrice" style="font-size: 1.4rem; text-align: center; margin-bottom: 0.5rem;"></div>
            <div class="modal-promo-box" id="modalPromoNote"></div>
            
            <label style="text-align: center; display: block; margin-bottom: 0.5rem;">Jumlah Pesanan</label>
            <div class="qty-control-modal">
                <button type="button" class="qty-btn-modal" id="modalMinusBtn">-</button>
                <div class="qty-val-modal" id="modalQtyVal">1</div>
                <button type="button" class="qty-btn-modal" id="modalPlusBtn">+</button>
            </div>

            <div class="total-line">
                <span>Total</span>
                <span id="modalSubtotal">Rp 0</span>
            </div>
        </div>
        <div class="modal-foot">
            <button type="button" class="btn-light" id="removeFromCartBtn">Hapus</button>
            <button type="button" class="btn" id="saveToCartBtn">Tambah ke Keranjang</button>
        </div>
    </section>

    <div id="toastWrap" class="toast-wrap"></div>

    <script>
        (function () {
            const packageGrid = document.getElementById('packageGrid');
            const categoryNav = document.getElementById('categoryNav');
            const menuGrid = document.getElementById('menuGrid');
            const menuSection = document.getElementById('menuSection');
            const orderItemsContainer = document.getElementById('orderItemsContainer');
            const promoWrap = document.getElementById('promoWrap');
            const cartList = document.getElementById('cartList');
            const cartEmptyState = document.getElementById('cartEmptyState');
            const cartTotalPrice = document.getElementById('cartTotalPrice');
            const submitOrderBtn = document.getElementById('submitOrderBtn');
            const cartFab = document.getElementById('cartFab');
            const cartFabBadge = document.getElementById('cartFabBadge');
            const cartDrawer = document.getElementById('cartDrawer');
            const cartDrawerBackdrop = document.getElementById('cartDrawerBackdrop');
            const closeCartDrawerBtn = document.getElementById('closeCartDrawerBtn');
            const modal = document.getElementById('orderModal');
            const modalBackdrop = document.getElementById('modalBackdrop');
            const closeModalBtn = document.getElementById('closeModalBtn');
            const saveBtn = document.getElementById('saveToCartBtn');
            const removeBtn = document.getElementById('removeFromCartBtn');
            const modalQtyVal = document.getElementById('modalQtyVal');
            const modalPlusBtn = document.getElementById('modalPlusBtn');
            const modalMinusBtn = document.getElementById('modalMinusBtn');
            const modalSubtotal = document.getElementById('modalSubtotal');
            const modalMenuName = document.getElementById('modalMenuName');
            const modalMenuPrice = document.getElementById('modalMenuPrice');
            const modalPromoNote = document.getElementById('modalPromoNote');
            const tableOrderStatusWrap = document.getElementById('tableOrderStatusWrap');
            const orderForm = document.getElementById('orderForm');
            const csrfToken = orderForm?.querySelector('input[name="_token"]')?.value || '';
            const menuCatalogLiveUrl = @json(route('tables.menus.live', $table->qr_token));
            const statusLiveUrl = @json(route('tables.orders.live', $table->qr_token));

            const formatRupiah = (n) => 'Rp ' + Number(n || 0).toLocaleString('id-ID');
            const parsePromoMeta = (value) => {
                if (!value) return null;
                try { return JSON.parse(value); } catch (_) { return null; }
            };
            const computeItemPromoDiscount = (row) => {
                const promoMeta = row?.promoMeta;
                if (!promoMeta) return 0;
                const qty = Number(row.qty || 0);
                const basePrice = Number(row.originalPrice || row.price || 0);
                if (promoMeta.type === 'percentage' || promoMeta.type === 'fixed_discount') {
                    return Math.max(0, basePrice - Number(row.price || basePrice)) * qty;
                }
                if (promoMeta.type === 'buy_x_get_y') {
                    const buyQty = Math.max(0, Number(promoMeta.buy_qty || 0));
                    const getQty = Math.max(0, Number(promoMeta.get_qty || 0));
                    if (buyQty > 0 && getQty > 0) {
                        const bundleSize = buyQty + getQty;
                        const freeUnits = Math.floor(qty / bundleSize) * getQty;
                        return freeUnits * basePrice;
                    }
                }
                return 0;
            };
            const cart = new Map();
            const packageCart = new Map();
            const activePromos = @json($promos);
            
            const calculateTotals = () => {
                let subtotal = 0;
                let specificDiscount = 0;
                for (const item of cart.values()) {
                    subtotal += Number(item.originalPrice || item.price || 0) * item.qty;
                    specificDiscount += computeItemPromoDiscount(item);
                }
                for (const item of packageCart.values()) {
                    subtotal += Number(item.originalPrice || item.price || 0) * item.qty;
                    specificDiscount += computeItemPromoDiscount(item);
                }

                const subtotalAfterSpecific = Math.max(0, subtotal - specificDiscount);
                let discount = 0;
                let activePromo = null;

                for (const promo of activePromos) {
                    if (promo.applies_to !== 'all') continue;
                    if (!['percentage', 'fixed_discount'].includes(promo.type)) continue;
                    const minSpend = Number(promo.min_spend || 0);
                    if (subtotalAfterSpecific < minSpend) continue;
                    const candidateDiscount = promo.type === 'percentage'
                        ? subtotalAfterSpecific * (Number(promo.value || 0) / 100)
                        : Number(promo.value || 0);
                    if (!activePromo || candidateDiscount > discount) {
                        activePromo = promo;
                        discount = candidateDiscount;
                    }
                }

                discount = Math.min(discount, subtotalAfterSpecific);

                return { subtotal, specificDiscount, subtotalAfterSpecific, discount, total: Math.max(0, subtotalAfterSpecific - discount), activePromo };
            };

            let activeItem = null;
            let currentFilter = 'all';
            let lastMenuTs = {{ $initial_menu_ts ?? 0 }};
            let lastMenuCount = {{ $initial_menu_count ?? 0 }};
            let lastCategoryCount = {{ $initial_category_count ?? 0 }};
            let lastPackageCount = {{ $initial_package_count ?? 0 }};
            let lastPromoCount = {{ $initial_promo_count ?? 0 }};
            let currentModalQty = 1;
            let cartDrawerOpen = false;

            const toastWrap = document.getElementById('toastWrap');
            window.showToast = function (message, type = 'success', timeout = 3500) {
                if (!toastWrap || !message) return;
                const el = document.createElement('div');
                el.className = 'toast-item ' + type;
                el.innerHTML = '<span>' + String(message) + '</span><button type="button">x</button>';
                const close = () => {
                    el.style.opacity = '0';
                    el.style.transform = 'translateY(-8px)';
                    setTimeout(() => el.remove(), 180);
                };
                el.querySelector('button')?.addEventListener('click', close);
                toastWrap.appendChild(el);
                setTimeout(close, timeout);
            };
            const showClientAlert = (message, type = 'error') => window.showToast(message, type);

            const openModal = () => {
                modal.classList.add('open');
                modalBackdrop.classList.add('open');
                modal.setAttribute('aria-hidden', 'false');
            };
            const closeModal = () => {
                modal.classList.remove('open');
                modalBackdrop.classList.remove('open');
                modal.setAttribute('aria-hidden', 'true');
            };

            const openCartDrawer = () => {
                cartDrawerOpen = true;
                cartDrawer?.classList.add('open');
                cartDrawerBackdrop?.classList.add('open');
                cartDrawer?.setAttribute('aria-hidden', 'false');
                cartFab?.setAttribute('aria-expanded', 'true');
            };

            const closeCartDrawer = () => {
                cartDrawerOpen = false;
                cartDrawer?.classList.remove('open');
                cartDrawerBackdrop?.classList.remove('open');
                cartDrawer?.setAttribute('aria-hidden', 'true');
                cartFab?.setAttribute('aria-expanded', 'false');
            };

            cartFab?.addEventListener('click', () => {
                if (cartDrawerOpen) closeCartDrawer();
                else openCartDrawer();
            });
            [cartDrawerBackdrop, closeCartDrawerBtn].forEach((el) => el?.addEventListener('click', closeCartDrawer));

            const updateQtyBadges = () => {
                document.querySelectorAll('.qty-selected').forEach((el) => {
                    const id = Number(el.getAttribute('data-qty-for'));
                    const qty = cart.get(id)?.qty || 0;
                    el.textContent = qty;
                    const card = el.closest('.menu-card');
                    if (card) card.classList.toggle('in-cart', qty > 0);
                });
                document.querySelectorAll('.package-qty-selected').forEach((el) => {
                    const id = Number(el.getAttribute('data-package-qty-for'));
                    const qty = packageCart.get(id)?.qty || 0;
                    el.textContent = qty;
                    const card = el.closest('.public-package-card');
                    if (card) card.classList.toggle('in-cart', qty > 0);
                });
            };

            const rebuildHiddenInputs = () => {
                orderItemsContainer.innerHTML = '';
                let i = 0;
                cart.forEach((row, menuId) => {
                    const mInp = document.createElement('input'); mInp.type='hidden'; mInp.name=`items[${i}][menu_id]`; mInp.value=String(menuId); orderItemsContainer.appendChild(mInp);
                    const qInp = document.createElement('input'); qInp.type='hidden'; qInp.name=`items[${i}][qty]`; qInp.value=String(row.qty); orderItemsContainer.appendChild(qInp);
                    i++;
                });
                let j = 0;
                packageCart.forEach((row, pkgId) => {
                    const pInp = document.createElement('input'); pInp.type='hidden'; pInp.name=`packages[${j}][package_id]`; pInp.value=String(pkgId); orderItemsContainer.appendChild(pInp);
                    const pqInp = document.createElement('input'); pqInp.type='hidden'; pqInp.name=`packages[${j}][qty]`; pqInp.value=String(row.qty); orderItemsContainer.appendChild(pqInp);
                    j++;
                });
            };

            const refreshOrderView = () => {
                updateQtyBadges();
                rebuildHiddenInputs();
                renderCartSummary();
            };

            const renderCartSummary = () => {
                const { subtotal, specificDiscount, discount, total, activePromo } = calculateTotals();
                const entries = [];
                let itemCount = 0;

                cart.forEach((row, id) => {
                    const promoDiscount = computeItemPromoDiscount(row);
                    const baseTotal = Number(row.originalPrice || row.price || 0) * row.qty;
                    entries.push({
                        kind: 'menu',
                        id,
                        title: row.name,
                        subtitle: `Menu x${row.qty}`,
                        total: Math.max(0, baseTotal - promoDiscount),
                        promoText: promoDiscount > 0 && row.promoMeta ? `${row.promoMeta.name} -${formatRupiah(promoDiscount)}` : '',
                    });
                    itemCount += row.qty;
                });

                packageCart.forEach((row, id) => {
                    const promoDiscount = computeItemPromoDiscount(row);
                    const baseTotal = Number(row.originalPrice || row.price || 0) * row.qty;
                    entries.push({
                        kind: 'package',
                        id,
                        title: row.name,
                        subtitle: `Paket x${row.qty}`,
                        total: Math.max(0, baseTotal - promoDiscount),
                        promoText: promoDiscount > 0 && row.promoMeta ? `${row.promoMeta.name} -${formatRupiah(promoDiscount)}` : '',
                    });
                    itemCount += row.qty;
                });

                if (cartEmptyState) cartEmptyState.style.display = entries.length ? 'none' : 'block';

                if (cartList) {
                    cartList.innerHTML = entries.map((entry) => `
                        <div class="cart-row">
                            <div class="cart-row-main">
                                <strong>${entry.title}</strong>
                                <small>${entry.subtitle}</small>
                                ${entry.promoText ? `<div class="cart-row-promo">${entry.promoText}</div>` : ''}
                            </div>
                            <div class="cart-row-actions">
                                <strong>${formatRupiah(entry.total)}</strong>
                                <button type="button" class="cart-remove-btn" data-cart-kind="${entry.kind}" data-cart-id="${entry.id}">Hapus</button>
                            </div>
                        </div>
                    `).join('');
                }

                if (cartTotalPrice) {
                    let html = `<div style="display:flex;justify-content:space-between;"><span>Subtotal</span><span>${formatRupiah(subtotal)}</span></div>`;
                    if (specificDiscount > 0) {
                        html += `<div style="display:flex;justify-content:space-between;color:var(--loss);margin-top:0.25rem;">
                                    <span>Diskon promo item</span>
                                    <span>-${formatRupiah(specificDiscount)}</span>
                                </div>`;
                    }
                    if (discount > 0) {
                        html += `<div style="display:flex;justify-content:space-between;color:var(--loss);margin-top:0.25rem;">
                                    <span>${activePromo.name}</span>
                                    <span>-${formatRupiah(discount)}</span>
                                </div>`;
                    }
                    html += `<div style="display:flex;justify-content:space-between;margin-top:0.5rem;padding-top:0.5rem;border-top:1px solid var(--accent);font-weight:700;">
                                <span>Total</span>
                                <span>${formatRupiah(total)}</span>
                            </div>`;
                    cartTotalPrice.innerHTML = html;
                }

                if (cartFabBadge) {
                    cartFabBadge.textContent = itemCount;
                    cartFabBadge.style.display = itemCount > 0 ? 'inline-flex' : 'none';
                }

                if (submitOrderBtn) {
                    submitOrderBtn.disabled = entries.length === 0;
                }
            };

            const updateModalDisplay = () => {
                if (!activeItem) return;
                modalQtyVal.textContent = currentModalQty;
                const tempRow = {
                    qty: currentModalQty,
                    price: activeItem.price,
                    originalPrice: activeItem.originalPrice,
                    promoMeta: activeItem.promoMeta,
                };
                const baseTotal = Number(activeItem.originalPrice || activeItem.price || 0) * currentModalQty;
                const promoDiscount = computeItemPromoDiscount(tempRow);
                modalSubtotal.textContent = formatRupiah(Math.max(0, baseTotal - promoDiscount));

                if (activeItem.promoMeta) {
                    let promoText = '';
                    if (activeItem.promoMeta.type === 'buy_x_get_y') {
                        promoText = `${activeItem.promoMeta.name}: beli ${activeItem.promoMeta.buy_qty} gratis ${activeItem.promoMeta.get_qty}`;
                    } else if (promoDiscount > 0) {
                        promoText = `${activeItem.promoMeta.name}: diskon ${formatRupiah(promoDiscount)} untuk jumlah ini`;
                    }

                    if (promoText) {
                        modalPromoNote.style.display = 'block';
                        modalPromoNote.innerHTML = `<strong>Promo aktif</strong>${promoText}<br><small>${activeItem.promoMeta.period_label || ''}</small>`;
                    } else {
                        modalPromoNote.style.display = 'none';
                        modalPromoNote.innerHTML = '';
                    }
                } else {
                    modalPromoNote.style.display = 'none';
                    modalPromoNote.innerHTML = '';
                }
            };

            const parseItemNumber = (value) => {
                const parsed = Number(value);
                return Number.isFinite(parsed) ? parsed : 0;
            };

            modalPlusBtn?.addEventListener('click', () => { currentModalQty++; updateModalDisplay(); });
            modalMinusBtn?.addEventListener('click', () => { if (currentModalQty > 0) currentModalQty--; updateModalDisplay(); });

            const applyMenuFilter = (filter = currentFilter) => {
                currentFilter = filter || 'all';
                document.querySelectorAll('#categoryNav .category-btn').forEach(btn => btn.classList.toggle('active', btn.getAttribute('data-filter') === currentFilter));
                document.querySelectorAll('#menuGrid .menu-card').forEach(card => {
                    const key = card.getAttribute('data-menu-category-key');
                    card.style.display = (currentFilter === 'all' || key === currentFilter) ? '' : 'none';
                });
            };

            categoryNav?.addEventListener('click', (e) => {
                const btn = e.target.closest('.category-btn');
                if (btn) applyMenuFilter(btn.getAttribute('data-filter') || 'all');
            });

            const initItemModal = (item) => {
                if (!item || !item.id || !item.name || item.price < 0) {
                    showClientAlert('Item ini belum siap dipesan. Refresh halaman lalu coba lagi.', 'err');
                    return;
                }
                activeItem = item;
                const existing = (item.type === 'menu' ? cart.get(item.id) : packageCart.get(item.id));
                currentModalQty = existing ? existing.qty : 1;
                modalMenuName.textContent = item.name;
                if (Number(item.originalPrice || item.price) > Number(item.price || 0)) {
                    modalMenuPrice.innerHTML = `<span style="display:block;">${formatRupiah(item.price)}</span><small style="color:var(--text-muted);text-decoration:line-through;">${formatRupiah(item.originalPrice)}</small>`;
                } else {
                    modalMenuPrice.textContent = formatRupiah(item.price);
                }
                updateModalDisplay();
                openModal();
            };

            menuGrid?.addEventListener('click', (e) => {
                const card = e.target.closest('.menu-card');
                if (!card || card.classList.contains('public-package-card')) return;
                initItemModal({
                    type: 'menu',
                    id: parseItemNumber(card.dataset.menuId),
                    name: card.dataset.menuName,
                    price: parseItemNumber(card.dataset.menuPrice),
                    originalPrice: parseItemNumber(card.dataset.menuOriginalPrice),
                    promoMeta: parsePromoMeta(card.dataset.menuPromoMeta),
                });
            });

            packageGrid?.addEventListener('click', (e) => {
                const card = e.target.closest('.public-package-card');
                if (!card) return;
                e.stopPropagation();
                initItemModal({
                    type: 'package',
                    id: parseItemNumber(card.dataset.packageId),
                    name: card.dataset.packageName,
                    price: parseItemNumber(card.dataset.packagePrice),
                    originalPrice: parseItemNumber(card.dataset.packageOriginalPrice),
                    promoMeta: parsePromoMeta(card.dataset.packagePromoMeta),
                });
            });

            promoWrap?.addEventListener('click', (e) => {
                const actionBtn = e.target.closest('[data-promo-action]');
                if (actionBtn) {
                    const action = actionBtn.getAttribute('data-promo-action');
                    if (action === 'scroll-menu') {
                        menuSection?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        return;
                    }

                    if (action === 'open-item') {
                        initItemModal({
                            type: actionBtn.dataset.itemType === 'package' ? 'package' : 'menu',
                            id: parseItemNumber(actionBtn.dataset.id),
                            name: actionBtn.dataset.name,
                            price: parseItemNumber(actionBtn.dataset.price),
                            originalPrice: parseItemNumber(actionBtn.dataset.originalPrice),
                            promoMeta: parsePromoMeta(actionBtn.dataset.promoMeta),
                        });
                        return;
                    }
                }

                const chip = e.target.closest('.promo-order-chip');
                if (!chip) return;

                initItemModal({
                    type: chip.dataset.promoOrder === 'package' ? 'package' : 'menu',
                    id: parseItemNumber(chip.dataset.id),
                    name: chip.dataset.name,
                    price: parseItemNumber(chip.dataset.price),
                    originalPrice: parseItemNumber(chip.dataset.originalPrice),
                    promoMeta: parsePromoMeta(chip.dataset.promoMeta),
                });
            });

            saveBtn?.addEventListener('click', () => {
                if (!activeItem) return;
                const qty = currentModalQty;
                if (activeItem.type === 'menu') {
                    if (qty <= 0) cart.delete(activeItem.id);
                    else cart.set(activeItem.id, { name: activeItem.name, qty, price: activeItem.price, originalPrice: activeItem.originalPrice || activeItem.price, promoMeta: activeItem.promoMeta || null });
                } else {
                    if (qty <= 0) packageCart.delete(activeItem.id);
                    else packageCart.set(activeItem.id, { name: activeItem.name, qty, price: activeItem.price, originalPrice: activeItem.originalPrice || activeItem.price, promoMeta: activeItem.promoMeta || null });
                }
                refreshOrderView();
                closeModal();
                openCartDrawer();
                window.showToast('Item berhasil ditambahkan ke keranjang.', 'success');
            });

            removeBtn?.addEventListener('click', () => {
                if (!activeItem) return;
                if (activeItem.type === 'menu') cart.delete(activeItem.id); else packageCart.delete(activeItem.id);
                refreshOrderView(); closeModal();
            });

            cartList?.addEventListener('click', (e) => {
                const btn = e.target.closest('.cart-remove-btn');
                if (!btn) return;
                const kind = btn.dataset.cartKind;
                const id = parseItemNumber(btn.dataset.cartId);
                if (kind === 'package') packageCart.delete(id);
                else cart.delete(id);
                refreshOrderView();
                if (cart.size === 0 && packageCart.size === 0) closeCartDrawer();
            });

            [modalBackdrop, closeModalBtn].forEach(el => el?.addEventListener('click', closeModal));
            orderForm?.addEventListener('submit', async (e) => {
                e.preventDefault();

                if (cart.size === 0 && packageCart.size === 0) {
                    window.showToast('Keranjang masih kosong. Pilih menu dulu.', 'error');
                    return;
                }

                submitOrderBtn?.setAttribute('disabled', 'disabled');
                const originalSubmitText = submitOrderBtn?.textContent || 'Kirim Pesanan';
                if (submitOrderBtn) {
                    submitOrderBtn.textContent = 'Mengirim...';
                }
                try {
                    const res = await fetch(orderForm.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: new FormData(orderForm),
                        credentials: 'same-origin',
                    });
                    
                    let payload;
                    try {
                        payload = await res.json();
                    } catch (jsonErr) {
                        throw new Error('Terjadi kesalahan sistem (Server Error).');
                    }

                    if (!res.ok || payload.ok === false) {
                        throw new Error(payload.message || 'Gagal mengirim pesanan.');
                    }

                    cart.clear();
                    packageCart.clear();
                    refreshOrderView();
                    if (payload.html) tableOrderStatusWrap.innerHTML = payload.html;
                    closeCartDrawer();
                    window.showToast(payload.message || 'Pesanan berhasil dikirim.', 'success');
                } catch (err) {
                    window.showToast(err.message || 'Gagal mengirim pesanan.', 'error');
                } finally {
                    if (submitOrderBtn) {
                        submitOrderBtn.textContent = originalSubmitText;
                    }
                    if (cart.size > 0 || packageCart.size > 0) {
                        submitOrderBtn?.removeAttribute('disabled');
                    } else {
                        submitOrderBtn?.setAttribute('disabled', 'disabled');
                    }
                }
            });

            refreshOrderView(); applyMenuFilter('all');

            let isPolling = false;
            const pollData = async () => {
                if (isPolling) return;
                isPolling = true;
                try {
                    const [menuRes, orderRes] = await Promise.all([
                        fetch(menuCatalogLiveUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' }),
                        fetch(statusLiveUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
                    ]);
                    if (menuRes.ok) {
                        const mData = await menuRes.json();
                        const nextLatestTs = Number(mData.latest_ts || 0);
                        const nextMenuCount = Number(mData.menu_count || 0);
                        const nextCategoryCount = Number(mData.category_count || 0);
                        const nextPackageCount = Number(mData.package_count || 0);
                        const nextPromoCount = Number(mData.promo_count || 0);

                        if (
                            nextLatestTs !== lastMenuTs ||
                            nextMenuCount !== lastMenuCount ||
                            nextCategoryCount !== lastCategoryCount ||
                            nextPackageCount !== lastPackageCount ||
                            nextPromoCount !== lastPromoCount
                        ) {
                            if (promoWrap && typeof mData.promos_html === 'string') promoWrap.innerHTML = mData.promos_html;
                            if (packageGrid) packageGrid.innerHTML = mData.packages_html;
                            if (categoryNav) categoryNav.innerHTML = mData.categories_html;
                            if (menuGrid) menuGrid.innerHTML = mData.menus_html;
                            applyMenuFilter(currentFilter); updateQtyBadges();
                            lastMenuTs = nextLatestTs;
                            lastMenuCount = nextMenuCount;
                            lastCategoryCount = nextCategoryCount;
                            lastPackageCount = nextPackageCount;
                            lastPromoCount = nextPromoCount;
                        }
                    }
                    if (orderRes.ok) {
                        const oData = await orderRes.json();
                        tableOrderStatusWrap.innerHTML = oData.html;
                    }
                } catch (e) {
                    console.error('Polling error:', e);
                } finally {
                    isPolling = false;
                }
            };

            setInterval(() => { if (document.visibilityState === 'visible') pollData(); }, 1000);
        })();
    </script>
</body>
</html>
