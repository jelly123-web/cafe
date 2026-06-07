<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>cafecaf - Meja {{ $table->number }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=Playfair+Display:wght@700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif
    <style>
        :root {
            --bg: #FAFAFA;
            --white: #FFFFFF;
            --card: #FFFFFF;
            --border: #F0EDE8;
            --fg: #1A1A1A;
            --fg-secondary: #6B6560;
            --muted: #A8A29E;
            --accent: #E85D2C;
            --accent-light: #FFF0EB;
            --accent-dark: #C94A1E;
            --green: #16A34A;
            --green-light: #DCFCE7;
            --yellow: #EAB308;
            --yellow-light: #FEF9C3;
            --blue: #2563EB;
            --blue-light: #DBEAFE;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.04), 0 1px 2px rgba(0,0,0,0.02);
            --shadow-md: 0 4px 16px rgba(0,0,0,0.06), 0 1px 4px rgba(0,0,0,0.03);
            --shadow-lg: 0 12px 40px rgba(0,0,0,0.08), 0 4px 12px rgba(0,0,0,0.03);
            --shadow-xl: 0 20px 60px rgba(0,0,0,0.1), 0 8px 20px rgba(0,0,0,0.04);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 20px;
            --radius-full: 999px;
            --font-main: 'Plus Jakarta Sans', sans-serif;
            --font-display: 'Playfair Display', serif;

            --bg-main: var(--bg);
            --bg-card: var(--card);
            --primary: var(--fg);
            --secondary: var(--fg-secondary);
            --accent-soft: var(--accent-light);
            --highlight: var(--accent);
            --text-main: var(--fg);
            --text-muted: var(--muted);
            --profit: var(--green);
            --loss: #DC2626;
            --shadow: var(--shadow-sm);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: var(--font-main);
            background: var(--bg);
            color: var(--fg);
            line-height: 1.6;
            min-height: 100vh;
            overflow-x: hidden;
        }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: var(--border); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--muted); }

        body::before {
            content: '';
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-image:
                radial-gradient(circle at 20% 20%, rgba(232,93,44,0.03) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(232,93,44,0.02) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(0,0,0,0.008) 0%, transparent 70%);
            pointer-events: none;
            z-index: 0;
        }

        .shell { position: relative; z-index: 1; max-width: 1200px; margin: 0 auto; padding: 24px; }

        .navbar {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            padding: 0 24px;
            transition: box-shadow 0.3s ease;
        }
        .navbar.scrolled { box-shadow: var(--shadow-md); }
        .navbar-inner {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 64px;
        }
        .brand { display: flex; align-items: center; gap: 10px; text-decoration: none; }
        .brand-icon {
            width: 36px; height: 36px;
            background: var(--accent);
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
            transform: rotate(-3deg);
        }
        .brand-name {
            font-family: var(--font-display);
            font-size: 22px;
            font-weight: 800;
            color: var(--fg);
            letter-spacing: -0.5px;
        }
        .nav-actions { display: flex; align-items: center; gap: 8px; }
        .nav-btn {
            position: relative;
            width: 40px; height: 40px;
            border: none;
            background: transparent;
            border-radius: var(--radius-sm);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            color: var(--fg-secondary);
            font-size: 18px;
            transition: all 0.2s ease;
        }
        .nav-btn:hover { background: var(--accent-light); color: var(--accent); }
        .nav-btn .badge {
            position: absolute;
            top: 4px;
            right: 4px;
            width: 16px;
            height: 16px;
            background: var(--accent);
            color: white;
            font-size: 9px;
            font-weight: 700;
            border-radius: var(--radius-full);
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid white;
        }

        .hero, .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 20px;
            box-shadow: var(--shadow-md);
        }
        .hero-content { max-width: 58%; text-align: left; margin-right: auto; }

        .hero {
            padding: 20px 30px;
            margin-bottom: 24px;
            text-align: left;
            border-bottom: 4px solid var(--accent);
            overflow: hidden;
            position: relative;
            background: linear-gradient(135deg, #E85D2C 0%, #F97316 50%, #FB923C 100%);
            background-size: cover;
            background-position: center;
            color: white;
        }

        .hero::before {
            content: '';
            position: absolute;
            top: -30px;
            right: -10px;
            width: 180px;
            height: 180px;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }

        .hero::after {
            content: '';
            position: absolute;
            bottom: -40px;
            right: 60px;
            width: 130px;
            height: 130px;
            background: rgba(255,255,255,0.06);
            border-radius: 50%;
        }

        .hero.has-image::before {
            inset: 0;
            width: auto;
            height: auto;
            top: 0;
            right: 0;
            border-radius: 0;
            background: linear-gradient(135deg, rgba(232,93,44,0.92) 0%, rgba(249,115,22,0.84) 55%, rgba(251,146,60,0.76) 100%);
        }

        .hero > * { position: relative; z-index: 1; }

        .hero-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            padding: 4px 12px;
            border-radius: var(--radius-full);
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 8px;
        }

        .hero-title {
            font-family: var(--font-display);
            font-size: 26px;
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 6px;
            color: white;
        }

        .hero-desc {
            font-size: 13px;
            opacity: 0.9;
            line-height: 1.4;
            margin-bottom: 14px;
            color: rgba(255,255,255,0.92);
        }

        .hero-cta {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: white;
            color: var(--accent);
            border: none;
            padding: 8px 20px;
            border-radius: var(--radius-full);
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s ease;
            font-family: var(--font-main);
        }

        .section {
            padding: 0;
            margin-bottom: 2rem;
            background: transparent;
            border: 0;
            border-radius: 0;
            box-shadow: none;
        }
        .section h2 { font-size: 1.5rem; margin-bottom: 1rem; }
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
            gap: 12px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 800;
            color: var(--fg);
            letter-spacing: -0.3px;
            margin: 0;
        }

        .section-title span {
            color: var(--accent);
        }

        .section-link {
            font-size: 13px;
            font-weight: 600;
            color: var(--accent);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 4px;
            transition: gap 0.2s ease;
            white-space: nowrap;
        }

        .section-link:hover {
            gap: 8px;
        }
        .section h3 {
            font-size: 1.08rem;
            margin-bottom: 0.9rem;
            color: var(--primary);
            font-family: 'Playfair Display', Georgia, serif;
        }
        #packageGrid { display: contents; }

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
            display: flex;
            gap: 14px;
            overflow-x: auto;
            padding-bottom: 8px;
            margin-bottom: 36px;
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .promo-grid {
            display: flex;
            gap: 14px;
            align-items: stretch;
        }

        .promo-card {
            border: 1px solid var(--accent);
            border-radius: 18px;
            background: linear-gradient(135deg, #fffaf5 0%, #fff 75%);
            padding: 0;
            overflow: hidden;
            box-shadow: 0 4px 15px var(--shadow);
        }

        .promo-card-inner {
            display: grid;
            grid-template-columns: 96px minmax(0, 1fr);
            gap: 0.8rem;
            padding: 0.85rem;
            align-items: start;
        }

        .promo-card-media {
            background: transparent;
            padding: 0;
        }

        .promo-card-banner {
            width: 96px;
            height: 96px;
            object-fit: cover;
            border-radius: 16px;
            border: 1px solid rgba(215, 204, 200, 0.95);
            background: #f7eee7;
            display: block;
        }

        .promo-card-copy {
            min-width: 0;
            padding: 0;
            display: grid;
            gap: 0.34rem;
        }

        .promo-card-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.35rem;
            flex-wrap: wrap;
        }

        .promo-card-kicker,
        .promo-card-status {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 0.16rem 0.4rem;
            font-size: 0.55rem;
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
            font-size: 0.98rem;
            line-height: 1.15;
            letter-spacing: 0.1px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .promo-card-desc {
            color: var(--text-muted);
            font-size: 0.65rem;
            line-height: 1.32;
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .promo-featured-box {
            display: grid;
            gap: 0.12rem;
            padding: 0.38rem 0.48rem;
            border-radius: 10px;
            background: linear-gradient(180deg, #fff7ef 0%, #fffdfb 100%);
            border: 1px solid rgba(212, 163, 115, 0.25);
        }

        .promo-featured-title {
            color: var(--primary);
            font-size: 0.6rem;
            font-weight: 800;
            line-height: 1.1;
        }

        .promo-featured-price {
            display: grid;
            gap: 0.05rem;
            color: var(--text-main);
        }

        .promo-featured-price-label {
            color: var(--text-muted);
            font-size: 0.54rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.25px;
        }

        .promo-featured-price strong {
            color: var(--primary);
            font-size: 0.68rem;
        }

        .promo-featured-price--promo strong {
            color: #d16d2a;
            font-size: 0.72rem;
        }

        .promo-card-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.22rem;
        }

        .promo-card-meta span {
            display: inline-flex;
            align-items: center;
            padding: 0.14rem 0.34rem;
            border-radius: 999px;
            background: #fff;
            color: var(--text-main);
            border: 1px solid var(--accent);
            font-size: 0.54rem;
            font-weight: 600;
        }

        .promo-card-actions {
            display: flex;
            align-items: center;
            gap: 0.32rem;
            flex-wrap: wrap;
        }

        .promo-action-btn {
            appearance: none;
            border: none;
            border-radius: 999px;
            padding: 0.42rem 0.75rem;
            background: linear-gradient(135deg, var(--highlight), #c98a50);
            color: #fff;
            font-weight: 800;
            font-size: 0.68rem;
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
            font-size: 0.56rem;
        }

        .promo-card--all .promo-card-media {
            background: linear-gradient(135deg, rgba(212, 163, 115, 0.18), rgba(255, 248, 241, 0.92));
        }

        .promo-order-group {
            display: grid;
            gap: 0.25rem;
            padding-top: 0.05rem;
            border-top: 1px dashed rgba(212, 163, 115, 0.25);
        }

        .promo-order-label {
            color: var(--primary);
            font-size: 0.58rem;
            font-weight: 700;
            letter-spacing: 0.2px;
        }

        .promo-order-list {
            display: flex;
            gap: 0.24rem;
            overflow-x: auto;
            padding-bottom: 0.05rem;
        }

        .promo-order-chip {
            display: inline-flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: center;
            gap: 0.03rem;
            padding: 0.22rem 0.34rem;
            border-radius: 8px;
            border: 1px solid rgba(212, 163, 115, 0.7);
            background: #fff;
            color: var(--primary);
            font-size: 0.52rem;
            font-weight: 700;
            width: 92px;
            min-width: 92px;
            min-height: 32px;
            transition: all 0.18s ease;
        }

        .promo-order-chip--static {
            cursor: default;
            pointer-events: none;
        }

        .promo-order-chip-name {
            display: block;
            font-size: 0.5rem;
            font-weight: 800;
            letter-spacing: 0.2px;
            text-transform: uppercase;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .promo-order-chip-price {
            display: block;
            font-size: 0.5rem;
            line-height: 1.2;
            font-weight: 700;
            color: var(--text-main);
        }

        .promo-order-chip-price small {
            display: inline-block;
            font-size: 0.48rem;
            font-weight: 800;
            color: var(--highlight);
        }

        .promo-order-chip:hover {
            background: #fff;
            border-color: rgba(212, 163, 115, 0.7);
            color: var(--primary);
            transform: none;
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
            gap: 0.9rem;
            flex-wrap: wrap;
            margin: 0 0 2rem;
            padding: 0.1rem 0 0.35rem;
        }
        .category-nav .category-btn { box-shadow: var(--shadow-sm); }
        .package-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
        }
        .category-btn {
            border: 1px solid #e8e2da;
            background: #fff;
            color: #6f675f;
            padding: 0.95rem 1.55rem;
            border-radius: 999px;
            font-weight: 700;
            cursor: pointer;
            font-family: inherit;
            font-size: 1rem;
            line-height: 1;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.65rem;
            box-shadow: 0 6px 18px rgba(53, 33, 12, 0.06);
        }
        .category-btn i { font-size: 1rem; }
        .category-btn:hover { background: var(--bg-main); border-color: var(--accent); color: var(--accent-dark); }
        .category-btn.active {
            background: #ef6428;
            color: #fff;
            border-color: #ef6428;
            box-shadow: 0 10px 24px rgba(239, 100, 40, 0.24);
        }

        .menu-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 1rem; }
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
            grid-template-columns: 92px minmax(0, 1fr);
            grid-template-areas:
                "image info"
                "badge badge";
            gap: 0.9rem 1rem;
            padding: 1.15rem;
        }

        .public-package-card .menu-info {
            gap: 0.24rem;
        }

        .public-package-card .menu-img {
            height: 92px;
            border-radius: 18px;
        }

        .public-package-card .menu-title {
            font-size: 1.15rem;
        }

        .public-package-card .menu-category {
            font-size: 0.8rem;
        }

        .public-package-card .price {
            font-size: 1.1rem;
        }

        .public-package-card .package-free-badge {
            font-size: 0.68rem;
            padding: 0.28rem 0.65rem;
        }

        .public-package-card .package-chips {
            gap: 0.35rem;
        }

        .public-package-card .package-chip {
            font-size: 0.68rem;
            padding: 0.25rem 0.55rem;
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
            width: 100%;
            justify-content: center;
            justify-self: stretch;
            padding: 0.65rem 0.8rem;
            font-size: 0.78rem;
        }
        .in-cart .qty-badge { background: rgba(212, 163, 115, 0.15); color: var(--highlight); border-color: var(--highlight); }

        .empty { color: var(--text-muted); font-style: italic; padding: 2rem 0; text-align: center; }

        .cart-summary {
            display: grid;
            gap: 0.75rem;
        }

        .cart-note-summary {
            display: none;
            border: 1px dashed var(--accent);
            border-radius: 16px;
            background: #fffaf5;
            padding: 0.85rem 0.95rem;
            color: var(--text-main);
            line-height: 1.5;
        }

        .cart-note-summary strong {
            display: block;
            color: var(--primary);
            margin-bottom: 0.2rem;
            font-family: 'Playfair Display', Georgia, serif;
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
            width: min(440px, 92vw); background: #fff;
            border: 1px solid var(--border); border-radius: 24px;
            box-shadow: var(--shadow-xl);
            opacity: 0; visibility: hidden; transition: .3s ease; z-index: 1001;
            display: flex; flex-direction: column;
            overflow: hidden;
        }
        .order-modal.open { opacity: 1; visibility: visible; transform: translate(-50%, -50%); }

        .modal-head { 
            padding: 24px 24px 16px;
            border-bottom: 1px solid var(--bg);
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .modal-head strong { 
            font-size: 20px;
            font-weight: 900;
            color: var(--fg);
            text-transform: capitalize;
            margin-bottom: 4px;
            font-family: inherit;
        }
        .price#modalMenuPrice { 
            font-size: 18px;
            font-weight: 800;
            color: var(--highlight);
        }

        .modal-body { 
            padding: 24px;
            display: flex;
            flex-direction: column; 
            gap: 24px; 
            overflow-y: auto; 
            flex: 1;
        }
        .modal-section {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .modal-body label { 
            font-size: 11px;
            font-weight: 800;
            color: var(--muted);
            text-transform: uppercase;
            letter-spacing: 0.8px;
            margin: 0;
            display: block;
        }
        .modal-close { display: none; }

        .qty-control-modal { 
            display: flex; 
            align-items: center; 
            gap: 16px; 
        }
        .qty-btn-modal { 
            width: 40px; 
            height: 40px; 
            border-radius: var(--radius-md); 
            border: 1.5px solid var(--border); 
            background: #fff; 
            color: var(--fg); 
            cursor: pointer; 
            font-size: 18px; 
            font-weight: 800; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            transition: all 0.2s ease; 
        }
        .qty-btn-modal:hover { border-color: var(--highlight); color: var(--highlight); background: var(--accent-light); }
        .qty-val-modal { font-size: 20px; font-weight: 900; color: var(--fg); min-width: 30px; text-align: center; }

        .qty-input {
            width: 100%; border: 1.5px solid var(--border); border-radius: 12px;
            padding: 0.75rem 1rem; font-family: inherit; font-size: 1rem; color: var(--fg);
            background: var(--bg); outline: none; transition: all 0.2s ease; text-align: center;
        }

        .btn-light#removeFromCartBtn { 
            border: 1.5px solid #FECACA; 
            background: #fff; 
            color: #DC2626; 
            border-radius: var(--radius-md); 
            padding: 14px; 
            font-weight: 800; 
            cursor: pointer; 
            transition: all 0.2s ease; 
            flex: 0.4;
        }
        .btn-light#removeFromCartBtn:hover { background: #FEE2E2; border-color: #DC2626; }
        
        .total-line { 
            padding: 20px 24px;
            background: var(--bg);
            border-top: 1px solid var(--bg);
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
        }
        .total-line span:first-child { font-size: 14px; font-weight: 700; color: var(--muted); }
        .total-line span:last-child { font-size: 20px; font-weight: 900; color: var(--fg); }

        .modal-promo-box {
            padding: 12px 16px;
            border: 1.5px solid var(--highlight);
            border-radius: 12px;
            background: var(--accent-light);
            color: var(--fg);
            font-size: 13px;
            line-height: 1.5;
            display: none;
            margin-bottom: 8px;
        }
        .modal-promo-box strong { color: var(--highlight); display:block; margin-bottom:0.2rem; }
        .notes-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .notes-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 4px;
        }
        .voice-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: var(--radius-full);
            border: 1.5px solid var(--border);
            background: #fff;
            color: var(--fg-secondary);
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s ease;
            width: fit-content;
        }
        .voice-btn:hover { border-color: var(--highlight); color: var(--highlight); background: var(--accent-light); }
        .voice-btn i { color: var(--highlight); }
        .voice-btn.is-recording {
            background: #FEE2E2;
            border-color: #DC2626;
            color: #DC2626;
        }
        .voice-btn.is-recording i { color: #DC2626; animation: pulse 1s infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }

        .voice-state {
            font-size: 11px;
            color: var(--muted);
        }
        .notes-textarea {
            width: 100%; border: 1.5px solid var(--border); border-radius: var(--radius-md);
            padding: 14px 16px; font-family: inherit; font-size: 14px; color: var(--fg);
            background: var(--bg); outline: none; transition: all 0.2s ease;
            min-height: 80px;
            resize: vertical;
        }
        .notes-textarea:focus {
            border-color: var(--highlight);
            background: #fff;
            box-shadow: 0 0 0 4px rgba(217, 119, 6, 0.1);
        }

        .modal-foot {
            padding: 0 24px 24px;
            background: var(--bg);
            display: flex;
            gap: 12px;
        }
        .modal-foot .btn {
            background: var(--highlight);
            color: white;
            border: none;
            border-radius: var(--radius-md);
            padding: 14px;
            font-size: 14px;
            font-weight: 800;
            cursor: pointer;
            transition: all 0.2s ease;
            flex: 1;
            box-shadow: 0 4px 12px rgba(217, 119, 6, 0.25);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-foot .btn:hover {
            background: var(--accent-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(217, 119, 6, 0.35);
        }
        .modal-foot .btn:active { transform: translateY(0); }

        .footer {
            text-align: center;
            padding: 24px;
            font-size: 12px;
            color: var(--muted);
            border-top: 1px solid var(--border);
            margin-top: 20px;
            position: relative;
            z-index: 1;
        }

        @media (max-width: 700px) {
            .order-modal { width: min(94vw, 480px); }
            .modal-head, .modal-foot { padding: 1rem 1.1rem; }
            .modal-body { padding: 1rem 1.1rem; gap: 0.85rem; }
            .shell { padding: 0.75rem; }
            .hero, .section { padding: 1rem; border-radius: 16px; margin-bottom: 1rem; }
            .menu-grid { grid-template-columns: 1fr; gap: 0.75rem; }
            .package-grid { grid-template-columns: 1fr; gap: 0.75rem; }
            .promo-grid { grid-template-columns: 1fr; gap: 0.75rem; }
            .promo-card-inner {
                grid-template-columns: 82px minmax(0, 1fr);
                gap: 0.65rem;
                padding: 0.75rem;
            }
            .promo-card-banner {
                width: 82px;
                height: 82px;
                border-radius: 14px;
            }
            .promo-order-list { gap: 0.22rem; }
            .promo-order-chip {
                width: 84px;
                min-width: 84px;
            }
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
            .promo-card-title { font-size: 0.88rem; }
            .promo-card-copy { padding: 0; gap: 0.3rem; }
        }
        /* ===== REFERENCE LAYOUT OVERRIDES ===== */
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
            gap: 12px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 800;
            color: var(--fg);
            letter-spacing: -0.3px;
            margin: 0;
        }

        .section-title span {
            color: var(--accent);
        }

        .section-link {
            font-size: 13px;
            font-weight: 600;
            color: var(--accent);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 4px;
            transition: gap 0.2s ease;
            white-space: nowrap;
        }

        .section-link:hover { gap: 8px; }

        .promo-wrap {
            display: flex;
            gap: 14px;
            overflow-x: auto;
            padding-bottom: 8px;
            margin-bottom: 36px;
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .promo-wrap::-webkit-scrollbar { display: none; }

        .promo-grid {
            display: flex;
            gap: 14px;
            align-items: stretch;
        }

        .promo-card {
            min-width: 280px;
            background: var(--white);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            transition: all 0.3s ease;
            cursor: pointer;
            flex-shrink: 0;
        }

        .promo-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--accent);
        }

        .promo-card-inner {
            display: block;
            padding: 0;
        }

        .promo-card-media {
            padding: 0;
            background: transparent;
        }

        .promo-card-banner {
            width: 100%;
            height: 140px;
            object-fit: cover;
            border-radius: 0;
            border: 0;
            background: #f7eee7;
            display: block;
        }

        .promo-card-copy {
            min-width: 0;
            padding: 14px 16px 16px;
            display: grid;
            gap: 0.35rem;
        }

        .promo-card-title {
            color: var(--primary);
            font-family: var(--font-display);
            font-size: 15px;
            font-weight: 700;
            line-height: 1.15;
        }

        .promo-card-desc {
            font-size: 12px;
            color: var(--muted);
            line-height: 1.4;
            margin: 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .promo-card-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.35rem;
            flex-wrap: wrap;
        }

        .promo-card-kicker,
        .promo-card-status {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: 0.16rem 0.4rem;
            font-size: 0.55rem;
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

        .promo-featured-box {
            display: grid;
            gap: 0.12rem;
            padding: 0.38rem 0.48rem;
            border-radius: 10px;
            background: linear-gradient(180deg, #fff7ef 0%, #fffdfb 100%);
            border: 1px solid rgba(212, 163, 115, 0.25);
        }

        .promo-featured-title {
            color: var(--primary);
            font-size: 0.6rem;
            font-weight: 800;
            line-height: 1.1;
        }

        .promo-featured-price-label {
            color: var(--text-muted);
            font-size: 0.54rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.25px;
        }

        .promo-featured-price strong {
            font-size: 0.86rem;
            color: var(--text-main);
        }

        .promo-card-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem;
            font-size: 0.68rem;
            color: var(--text-muted);
        }

        .promo-card-meta span {
            display: inline-flex;
            align-items: center;
            padding: 0.28rem 0.65rem;
            border-radius: 999px;
            border: 1px solid var(--accent);
            background: #fff;
            color: var(--text-main);
            font-weight: 600;
        }

        .promo-card-actions {
            display: flex;
            align-items: center;
            gap: 0.55rem;
            flex-wrap: wrap;
        }

        .promo-action-btn {
            border: none;
            background: var(--accent);
            color: #fff;
            padding: 0.55rem 0.95rem;
            border-radius: 999px;
            font-weight: 700;
            font-family: inherit;
            font-size: 0.84rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .promo-action-btn:hover {
            background: var(--accent-dark);
            transform: translateY(-1px);
        }

        .promo-card-hint {
            font-size: 0.72rem;
            color: var(--text-muted);
        }

        .promo-order-group {
            display: grid;
            gap: 0.4rem;
            padding-top: 0.25rem;
        }

        .promo-order-label {
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--primary);
        }

        .promo-order-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.35rem;
        }

        .promo-order-chip {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            padding: 0.28rem 0.55rem;
            border-radius: 999px;
            border: 1px solid var(--accent);
            background: #fff;
            font-size: 0.68rem;
            font-weight: 600;
        }

        .promo-order-chip-name { color: var(--text-main); }
        .promo-order-chip-price { color: var(--accent); }
        .promo-order-chip-price small { color: var(--text-muted); }

        .package-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 40px;
        }

        .public-package-card {
            display: flex;
            flex-direction: column;
            background: var(--white);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            cursor: pointer;
            position: relative;
            padding: 0;
            gap: 0;
        }

        .public-package-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
            border-color: transparent;
        }

        .public-package-card .menu-img {
            width: 100%;
            height: 160px;
            border-radius: 0;
            border: 0;
            padding: 0;
            object-fit: cover;
            background: #f7eee7;
        }

        .public-package-card .menu-info {
            padding: 14px 16px 16px;
            gap: 0.35rem;
        }

        .public-package-card .menu-title {
            font-size: 15px;
        }

        .public-package-card .menu-category {
            font-size: 12px;
        }

        .public-package-card .price {
            font-size: 16px;
        }

        .public-package-card .package-free-badge {
            font-size: 0.68rem;
            padding: 0.28rem 0.65rem;
        }

        .public-package-card .package-chips {
            gap: 0.35rem;
        }

        .public-package-card .package-chip {
            font-size: 0.68rem;
            padding: 0.25rem 0.55rem;
        }

        .public-package-card .qty-badge,
        .menu-card .qty-badge,
        .qty-badge {
            display: none;
        }

        .menu-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 40px;
        }

        .menu-card {
            display: flex;
            flex-direction: column;
            background: var(--white);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            cursor: pointer;
            transition: all .2s ease;
            min-height: 100%;
            padding: 0;
        }

        .menu-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
            border-color: transparent;
        }

        .menu-card > .menu-img {
            width: 100%;
            height: 170px;
            border-radius: 0;
            border: 0;
            padding: 0;
            object-fit: cover;
            background: #f7eee7;
        }

        .menu-card > .menu-info {
            padding: 14px 16px 16px;
            gap: 0.35rem;
        }

        .menu-title {
            font-size: 15px;
        }

        .menu-category {
            font-size: 12px;
        }

        .menu-price {
            font-size: 16px;
        }

        .btn-add-cart {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            background: var(--accent-light);
            color: var(--accent);
        }

        .btn-add-cart:hover {
            background: var(--accent);
            color: white;
        }

        .menu-label {
            top: 10px;
            left: 10px;
        }

        @media (max-width: 768px) {
            .shell {
                padding: 16px;
            }

            .hero {
                padding: 28px 22px;
            }

            .hero h1 {
                font-size: 24px;
            }

            .section-title {
                font-size: 17px;
            }

            .package-grid {
                grid-template-columns: 1fr;
            }

            .menu-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }

            .promo-grid {
                gap: 0.75rem;
            }

            .promo-card {
                min-width: 260px;
            }

            .promo-card-banner {
                height: 120px;
            }

            .public-package-card .menu-img {
                height: 120px;
            }

            .menu-card > .menu-img {
                height: 120px;
            }
        }

        @media (max-width: 480px) {
            .menu-grid {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }

            .menu-title {
                font-size: 13px;
            }

            .menu-price {
                font-size: 14px;
            }
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 60%;
        }

        .hero-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            padding: 5px 14px;
            border-radius: var(--radius-full);
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-bottom: 14px;
        }

        .hero-title {
            font-family: var(--font-display);
            font-size: 32px;
            font-weight: 900;
            line-height: 1.15;
            margin-bottom: 8px;
            color: white;
        }

        .hero-desc {
            font-size: 14px;
            opacity: 0.9;
            line-height: 1.5;
            margin-bottom: 20px;
            color: rgba(255,255,255,0.92);
        }

        .hero-cta {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: white;
            color: var(--accent);
            border: none;
            padding: 10px 24px;
            border-radius: var(--radius-full);
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s ease;
            font-family: var(--font-main);
        }

        .hero-cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.15);
        }


        /* Exact reference controls: navbar brand and category tabs. */
        .navbar {
            background: rgba(255,255,255,0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            padding: 0 24px;
        }

        .navbar-inner {
            max-width: 1200px;
            height: 64px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .brand-icon {
            width: 36px;
            height: 36px;
            background: var(--accent);
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
            transform: rotate(-3deg);
            transition: transform 0.3s ease;
            box-shadow: none;
        }

        .brand:hover .brand-icon {
            transform: rotate(3deg) scale(1.05);
        }

        .brand-name {
            font-family: var(--font-display);
            font-size: 22px;
            font-weight: 800;
            color: var(--fg);
            letter-spacing: -0.5px;
            line-height: 1;
        }

        .category-nav {
            display: flex;
            gap: 8px;
            margin: 0 0 24px;
            overflow-x: auto;
            padding: 0 0 4px;
            flex-wrap: nowrap;
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .category-nav::-webkit-scrollbar { display: none; }

        .category-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-full);
            background: var(--white);
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            color: var(--fg-secondary);
            white-space: nowrap;
            transition: all 0.25s ease;
            font-family: var(--font-main);
            box-shadow: var(--shadow-sm);
            line-height: 1.6;
            min-height: 52px;
        }

        .category-btn i {
            font-size: 16px;
            color: currentColor;
            width: 16px;
            text-align: center;
        }

        .category-btn:hover {
            border-color: var(--accent);
            color: var(--accent);
            background: var(--accent-light);
        }

        .category-btn.active {
            background: var(--accent);
            border-color: var(--accent);
            color: white;
            box-shadow: var(--shadow-md);
        }


        /* Latest customer-page reference overrides: compact promo, combined menu, order stepper. */
        .promo-wrap {
            display: block;
            margin-bottom: 40px;
            overflow: visible;
            padding-bottom: 0;
        }

        .promo-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 0;
            overflow: visible;
            padding-bottom: 0;
        }

        .promo-card {
            min-width: 0;
            width: 100%;
            background: var(--white);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: none;
            transition: all 0.3s ease;
        }

        .promo-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--accent);
        }

        .promo-img {
            width: 100%;
            height: 140px;
            object-fit: cover;
            display: block;
        }

        .promo-body {
            padding: 14px 16px;
        }

        .promo-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: var(--radius-full);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            margin-bottom: 8px;
        }

        .promo-badge.diskon { background: var(--accent-light); color: var(--accent); }
        .promo-badge.bogofree { background: var(--green-light); color: var(--green); }
        .promo-badge.cashback { background: var(--blue-light); color: var(--blue); }
        .promo-name { font-size: 15px; font-weight: 700; margin-bottom: 4px; color: var(--fg); }
        .promo-detail { font-size: 12px; color: var(--muted); line-height: 1.45; }
        .promo-expire { display: flex; align-items: center; gap: 4px; margin-top: 8px; font-size: 11px; color: var(--accent); font-weight: 600; }

        .combined-menu-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 1rem;
            margin-bottom: 40px;
        }

        .combined-menu-grid .package-grid,
        .combined-menu-grid .menu-grid {
            display: contents;
        }

        .status-wrap {
            display: grid;
            gap: 1rem;
        }

        .order-status-card.status-card {
            background: var(--white);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 24px;
            box-shadow: var(--shadow-md);
        }

        .section:has(#tableOrderStatusWrap) h2 {
            font-size: 20px;
            font-weight: 800;
            letter-spacing: -0.3px;
            margin-bottom: 18px;
        }

        .section:has(#tableOrderStatusWrap) h2::first-letter {
            color: var(--fg);
        }

        @media (max-width: 1024px) {
            .promo-grid,
            .combined-menu-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 480px) {
            .promo-grid,
            .combined-menu-grid {
                grid-template-columns: 1fr 1fr;
                gap: 10px;
            }

            .promo-img { height: 120px; }
        }


        /* Force exact order status reference layout. */
        #statusSection.status-section {
            width: min(1440px, calc(100vw - 76px));
            max-width: none;
            margin: 52px 0 64px 50%;
            transform: translateX(-50%);
        }

        #statusSection .section-header {
            margin-bottom: 26px;
        }

        #statusSection .section-title {
            font-family: var(--font-main);
            font-size: 26px;
            line-height: 1.2;
            font-weight: 800;
            letter-spacing: -0.55px;
            color: var(--fg);
            margin: 0;
        }

        #statusSection .section-title span {
            color: var(--accent);
        }

        #tableOrderStatusWrap.status-wrap {
            display: grid;
            gap: 16px;
            width: 100%;
        }

        #tableOrderStatusWrap .order-status-card.status-card {
            width: 100%;
            background: var(--white);
            border: 1.5px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 32px 30px 30px;
            box-shadow: var(--shadow-md);
            transition: all 0.3s ease;
        }

        #tableOrderStatusWrap .status-order-info {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 30px;
        }

        #tableOrderStatusWrap .order-id {
            font-family: var(--font-main);
            font-size: 18px;
            line-height: 1.25;
            font-weight: 800;
            color: var(--fg);
            margin: 0 0 4px;
        }

        #tableOrderStatusWrap .order-id span {
            color: var(--accent);
        }

        #tableOrderStatusWrap .order-time {
            display: flex;
            align-items: center;
            gap: 4px;
            font-family: var(--font-main);
            font-size: 14px;
            line-height: 1.35;
            color: var(--muted);
        }

        #tableOrderStatusWrap .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 18px;
            border-radius: var(--radius-full);
            font-family: var(--font-main);
            font-size: 13px;
            line-height: 1;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0;
            white-space: nowrap;
            margin-top: 8px;
        }

        #tableOrderStatusWrap .status-badge.preparing {
            background: var(--yellow-light);
            color: #A16207;
        }

        #tableOrderStatusWrap .status-badge .dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: currentColor;
            flex: 0 0 auto;
            animation: dotBlink 1.2s infinite;
        }

        #tableOrderStatusWrap .status-timeline {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            position: relative;
            padding: 0 50px;
            margin: 0 0 28px;
            width: 100%;
        }

        #tableOrderStatusWrap .status-timeline::before {
            content: '';
            position: absolute;
            top: 21px;
            left: 80px;
            right: 80px;
            height: 4px;
            background: var(--border);
            border-radius: 999px;
            z-index: 0;
        }

        #tableOrderStatusWrap .status-timeline::after {
            content: '';
            position: absolute;
            top: 21px;
            left: 80px;
            width: var(--progress, 0%);
            max-width: calc(100% - 160px);
            height: 4px;
            background: var(--accent);
            border-radius: 999px;
            z-index: 1;
            transition: width 0.8s ease;
        }

        #tableOrderStatusWrap .step {
            display: flex;
            flex: 1 1 0;
            flex-direction: column;
            align-items: center;
            gap: 12px;
            position: relative;
            z-index: 2;
            min-width: 0;
        }

        #tableOrderStatusWrap .step-circle {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 17px;
            border: 4px solid var(--border);
            background: var(--white);
            color: var(--muted);
            transition: all 0.3s ease;
        }

        #tableOrderStatusWrap .step.completed .step-circle {
            background: var(--accent);
            border-color: var(--accent);
            color: white;
        }

        #tableOrderStatusWrap .step.active .step-circle {
            border-color: var(--accent);
            color: var(--accent);
            background: var(--white);
            animation: stepPulse 2s infinite;
        }

        #tableOrderStatusWrap .step-label {
            font-family: var(--font-main);
            font-size: 14px;
            line-height: 1.2;
            font-weight: 700;
            color: var(--muted);
            text-align: center;
            max-width: 92px;
        }

        #tableOrderStatusWrap .step.completed .step-label,
        #tableOrderStatusWrap .step.active .step-label {
            color: var(--fg);
        }

        #tableOrderStatusWrap .status-eta {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 0;
            padding: 17px 20px;
            background: var(--accent-light);
            border-radius: var(--radius-md);
            width: 100%;
        }

        #tableOrderStatusWrap .status-eta i {
            color: var(--accent);
            font-size: 24px;
            line-height: 1;
        }

        #tableOrderStatusWrap .status-eta-text {
            font-family: var(--font-main);
            font-size: 16px;
            line-height: 1.4;
            color: var(--fg);
        }

        #tableOrderStatusWrap .status-eta-text strong {
            color: var(--accent);
            font-weight: 800;
        }

        @media (max-width: 768px) {
            #statusSection.status-section {
                width: calc(100vw - 32px);
                margin-top: 36px;
            }

            #tableOrderStatusWrap .order-status-card.status-card {
                padding: 22px 18px;
            }

            #tableOrderStatusWrap .status-order-info {
                align-items: flex-start;
                margin-bottom: 24px;
            }

            #tableOrderStatusWrap .status-timeline {
                padding: 0 4px;
            }

            #tableOrderStatusWrap .status-timeline::before,
            #tableOrderStatusWrap .status-timeline::after {
                left: 28px;
                right: 28px;
                max-width: calc(100% - 56px);
            }

            #tableOrderStatusWrap .step-circle {
                width: 34px;
                height: 34px;
                font-size: 13px;
                border-width: 3px;
            }

            #tableOrderStatusWrap .step-label {
                font-size: 10px;
                max-width: 58px;
            }
        }


        /* Final exact fixes: hero badge and side cart modal. */
        .cart-drawer-backdrop {
            position: fixed !important;
            inset: 0 !important;
            background: rgba(62, 39, 35, 0.34) !important;
            backdrop-filter: blur(4px) !important;
            opacity: 0 !important;
            visibility: hidden !important;
            transition: .25s ease !important;
            z-index: 1298 !important;
        }

        .cart-drawer-backdrop.open {
            opacity: 1 !important;
            visibility: visible !important;
        }

        .cart-drawer {
            position: fixed !important;
            top: 0 !important;
            right: 0 !important;
            left: auto !important;
            bottom: 0 !important;
            width: min(460px, 30vw) !important;
            max-width: calc(100vw - 24px) !important;
            height: 100vh !important;
            max-height: 100vh !important;
            background: #fff !important;
            border-left: 1px solid rgba(232, 93, 44, 0.18) !important;
            border-top: 0 !important;
            border-right: 0 !important;
            border-bottom: 0 !important;
            border-radius: 0 !important;
            box-shadow: -20px 0 48px rgba(62, 39, 35, 0.18) !important;
            overflow: hidden !important;
            display: grid !important;
            grid-template-rows: auto 1fr auto !important;
            transform: translateX(100%) !important;
            opacity: 0 !important;
            visibility: hidden !important;
            pointer-events: none !important;
            transition: transform .26s ease, opacity .26s ease, visibility .26s ease !important;
            z-index: 1299 !important;
        }

        .cart-drawer.open {
            transform: translateX(0) !important;
            opacity: 1 !important;
            visibility: visible !important;
            pointer-events: auto !important;
        }

        .cart-drawer-head {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 12px !important;
            min-height: 92px !important;
            padding: 28px 34px !important;
            border-bottom: 1px solid rgba(232, 93, 44, 0.14) !important;
            background: #fff !important;
        }

        .cart-drawer-head h2 {
            margin: 0 !important;
            font-family: var(--font-display) !important;
            font-size: 2.2rem !important;
            line-height: 1 !important;
            font-weight: 900 !important;
            color: var(--fg) !important;
            letter-spacing: -0.5px !important;
        }

        .cart-drawer-close {
            width: 54px !important;
            height: 54px !important;
            border: 1px solid rgba(232, 93, 44, 0.34) !important;
            background: #fff !important;
            color: var(--fg) !important;
            border-radius: 18px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            cursor: pointer !important;
            font-size: 2rem !important;
            font-weight: 800 !important;
            line-height: 1 !important;
            transition: all .2s ease !important;
        }

        .cart-drawer-close:hover {
            background: var(--accent-light) !important;
            color: var(--accent-dark) !important;
            border-color: var(--accent) !important;
        }

        .cart-drawer-body {
            padding: 28px 34px !important;
            overflow-y: auto !important;
            background: #fff !important;
        }

        .cart-summary {
            display: grid !important;
            gap: 18px !important;
        }

        .cart-empty {
            padding: 20px 22px !important;
            border: 1px dashed rgba(232, 93, 44, 0.32) !important;
            border-radius: 20px !important;
            background: #fff8f5 !important;
            color: var(--muted) !important;
            font-size: 1rem !important;
            line-height: 1.75 !important;
            text-align: left !important;
            font-style: italic !important;
        }

        .cart-note-summary {
            display: none;
            border: 1px solid rgba(232, 93, 44, 0.18) !important;
            border-radius: 20px !important;
            background: #fff8f5 !important;
            padding: 16px 18px !important;
            color: var(--fg-secondary) !important;
            font-size: 0.95rem !important;
            line-height: 1.8 !important;
        }

        .cart-note-summary strong {
            display: block !important;
            margin-bottom: 8px !important;
            color: var(--fg) !important;
            font-size: 0.78rem !important;
            font-weight: 800 !important;
            font-family: var(--font-main) !important;
            text-transform: uppercase !important;
            letter-spacing: 0.5px !important;
        }

        .cart-list {
            display: grid !important;
            gap: 14px !important;
        }

        .cart-row {
            display: grid !important;
            grid-template-columns: minmax(0, 1fr) auto !important;
            gap: 14px !important;
            align-items: start !important;
            padding: 16px 18px !important;
            border: 1px solid var(--border) !important;
            border-radius: 20px !important;
            background: #fff !important;
        }

        .cart-row-main {
            min-width: 0 !important;
        }

        .cart-row-main strong {
            display: block !important;
            color: var(--fg) !important;
            font-size: 1rem !important;
            line-height: 1.4 !important;
            font-weight: 800 !important;
            margin-bottom: 3px !important;
        }

        .cart-row-main small {
            display: block !important;
            color: var(--muted) !important;
            font-size: 0.9rem !important;
            line-height: 1.5 !important;
        }

        .cart-row-promo {
            margin-top: 6px !important;
            color: var(--accent) !important;
            font-size: 0.78rem !important;
            font-weight: 700 !important;
        }

        .cart-row-actions {
            display: grid !important;
            justify-items: end !important;
            gap: 8px !important;
            min-width: 110px !important;
        }

        .cart-row-price {
            color: var(--fg) !important;
            font-size: 1rem !important;
            line-height: 1.2 !important;
            font-weight: 800 !important;
            white-space: nowrap !important;
        }

        .cart-remove-btn {
            border: 1px solid rgba(232, 93, 44, 0.24) !important;
            background: #fff !important;
            color: var(--accent-dark) !important;
            border-radius: 12px !important;
            padding: 7px 11px !important;
            font-size: 0.75rem !important;
            font-weight: 700 !important;
            line-height: 1 !important;
            cursor: pointer !important;
            transition: all 0.2s ease !important;
        }

        .cart-remove-btn:hover {
            background: var(--red-light) !important;
            border-color: rgba(220, 38, 38, 0.25) !important;
            color: var(--red) !important;
        }

        .cart-pricing {
            display: grid !important;
            gap: 12px !important;
            padding-top: 18px !important;
            border-top: 1px dashed var(--border) !important;
        }

        .cart-pricing-row {
            display: flex !important;
            align-items: center !important;
            justify-content: space-between !important;
            gap: 12px !important;
            color: var(--fg-secondary) !important;
            font-size: 0.98rem !important;
            line-height: 1.4 !important;
        }

        .cart-pricing-row strong {
            color: var(--fg) !important;
            font-size: 1rem !important;
            line-height: 1.2 !important;
            font-weight: 800 !important;
            white-space: nowrap !important;
            text-align: right !important;
        }

        .cart-pricing-row.discount,
        .cart-pricing-row.discount strong {
            color: var(--red) !important;
        }

        .cart-pricing-row.total {
            margin-top: 2px !important;
            padding-top: 14px !important;
            border-top: 1px solid var(--border-light) !important;
            color: var(--fg) !important;
            font-size: 1.15rem !important;
            font-weight: 800 !important;
        }

        .cart-pricing-row.total strong {
            color: var(--accent) !important;
            font-size: 1.45rem !important;
            font-family: var(--font-main) !important;
        }

        .cart-drawer-foot {
            padding: 22px 34px 28px !important;
            border-top: 1px solid var(--border-light) !important;
            background: #fff !important;
        }

        .cart-drawer .btn,
        #submitOrderBtn {
            width: 100% !important;
            min-height: 58px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 16px 20px !important;
            border: 0 !important;
            border-radius: 18px !important;
            font-family: var(--font-main) !important;
            font-size: 1rem !important;
            font-weight: 800 !important;
            box-shadow: none !important;
        }

        #submitOrderBtn:disabled {
            background: #bcb7b1 !important;
            color: #fff !important;
            opacity: 1 !important;
        }

        @media (max-width: 1024px) {
            .cart-drawer {
                width: min(420px, 44vw) !important;
            }
        }

        @media (max-width: 768px) {
            .cart-drawer {
                width: min(92vw, 460px) !important;
                max-width: 92vw !important;
                right: 0 !important;
            }

            .cart-drawer-head {
                min-height: auto !important;
                padding: 18px 20px !important;
            }

            .cart-drawer-head h2 {
                font-size: 1.7rem !important;
            }

            .cart-drawer-body {
                padding: 18px 20px !important;
            }

            .cart-drawer-foot {
                padding: 16px 20px 20px !important;
            }
        }

    </style>
  @include('components.page-transition-guard')
</head>
<body>
    <nav class="navbar" id="navbar">
        <div class="navbar-inner">
            <a href="#" class="brand" id="brandLink">
                <div class="brand-icon" id="brandIcon">
                    @if(!empty($cafeBrand['logo_url']))
                        <img src="{{ $cafeBrand['logo_url'] }}" alt="{{ $cafeBrand['name'] ?? 'cafecaf' }}" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;">
                    @else
                        <i class="fas fa-utensils"></i>
                    @endif
                </div>
                <span class="brand-name" id="brandName">{{ $cafeBrand['name'] ?? 'cafecaf' }}</span>
            </a>
            <div class="nav-actions">
                <button class="nav-btn" type="button" title="Riwayat pesanan" onclick="showToast('Riwayat pesanan belum tersedia', 'fa-clock-rotate-left')">
                    <i class="fas fa-clock-rotate-left"></i>
                </button>
                <button class="nav-btn" type="button" title="Keranjang" onclick="toggleCart()">
                    <i class="fas fa-bag-shopping"></i>
                    <span class="badge" id="cartBadgeDesktop" style="display:none;">0</span>
                </button>
            </div>
        </div>
    </nav>

    <main class="shell">
        <section
            id="heroSection"
            class="hero {{ !empty($publicHero['image_url']) ? 'has-image' : '' }}"
            @if(!empty($publicHero['image_url']))
                style="background-image:url('{{ $publicHero['image_url'] }}')"
            @endif
        >
            <div class="hero-content">
                <div class="hero-tag" id="heroTag"><i class="fas fa-fire"></i> <span>{{ $publicHero['tag'] ?? 'PROMO SPESIAL HARI INI' }}</span></div>
                <h1 class="hero-title" id="heroTitle">{{ $publicHero['title'] ?? 'Diskon 50% Untuk Semua Paket Nasi Goreng' }}</h1>
                <p class="hero-desc" id="heroDesc">{{ $publicHero['desc'] ?? 'Nikmati paket lengkap dengan harga setengah. Berlaku sampai pukul 23:59 malam ini.' }}</p>
                <button class="hero-cta" id="heroButton" type="button" onclick="scrollToSection('promoSection')"><i class="fas fa-tag"></i> <span>{{ $publicHero['button_text'] ?? 'Lihat Promo' }}</span></button>
            </div>
        </section>

        <section class="section" id="promoSection">
            <div class="section-header">
                <h2 class="section-title">Promo <span>Terkini</span></h2>
                <a href="#" class="section-link">Lihat semua <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="promo-wrap" id="promoWrap">
                @include('public.partials.promo-strip', ['promos' => $promos])
            </div>
        </section>

        <div class="category-nav" id="categoryNav">
            @include('public.partials.menu-categories', ['categories' => $categories, 'activeFilter' => 'all'])
        </div>

        <section class="section" id="menuSection">
            <div class="section-header">
                <h2 class="section-title">Menu <span>Makanan</span></h2>
                <a href="#" class="section-link">Lihat semua <i class="fas fa-arrow-right"></i></a>
            </div>
            <div class="combined-menu-grid">
                <div class="package-grid" id="packageGrid">
                    @include('public.partials.package-grid', ['packages' => $packages])
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

        <section class="section status-section" id="statusSection">
            <div class="section-header">
                <h2 class="section-title">Status <span>Pesanan</span></h2>
            </div>
            <div class="status-wrap" id="tableOrderStatusWrap">
                @include('public.partials.table-order-status', ['orders' => $orders])
            </div>
        </section>
    </main>

    <footer class="footer">
        cafecaf &copy; 2026 — Semua hak dilindungi.
    </footer>

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
                <div class="cart-note-summary" id="cartNoteSummary"></div>
                <div class="cart-pricing" id="cartTotalPrice"></div>
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
            <div class="price" id="modalMenuPrice"></div>
            <button type="button" class="modal-close" id="closeModalBtn" style="display:none;">Tutup</button>
        </div>
        <div class="modal-body">
            <div class="modal-promo-box" id="modalPromoNote"></div>

            <div class="modal-section">
                <label>Jumlah Pesanan</label>
                <div class="qty-control-modal">
                    <button type="button" class="qty-btn-modal" id="modalMinusBtn">-</button>
                    <span class="qty-val-modal" id="modalQtyVal">1</span>
                    <button type="button" class="qty-btn-modal" id="modalPlusBtn">+</button>
                </div>
            </div>

            <div class="modal-section">
                <label for="orderNotes">Catatan Pesanan</label>
                <textarea id="orderNotes" name="notes" class="notes-textarea" placeholder="Tulis catatan di sini..."></textarea>
                <div class="notes-actions">
                    <button type="button" class="voice-btn" id="voiceNoteBtn">
                        <i class="fas fa-microphone"></i>
                        <span>Mulai rekam</span>
                    </button>
                    <span class="voice-state" id="voiceNoteState">Siap ngomong</span>
                </div>
            </div>
        </div>
        <div class="total-line">
            <span>Total</span>
            <span id="modalSubtotal">Rp 0</span>
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
            const cartNoteSummary = document.getElementById('cartNoteSummary');
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
            const notesTextarea = document.getElementById('orderNotes');
            const voiceNoteBtn = document.getElementById('voiceNoteBtn');
            const voiceNoteState = document.getElementById('voiceNoteState');
            const tableOrderStatusWrap = document.getElementById('tableOrderStatusWrap');
            const orderForm = document.getElementById('orderForm');
            const csrfToken = orderForm?.querySelector('input[name="_token"]')?.value || '';
            const menuCatalogLiveUrl = @json(route('tables.menus.live', $table->qr_token));
            const statusLiveUrl = @json(route('tables.orders.live', $table->qr_token));
            const orderSyncChannel = window.BroadcastChannel ? new BroadcastChannel('cafe-order-sync') : null;
            const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition || null;
            let speechRecognition = null;
            let isRecordingVoice = false;
            let voiceNotesBaseText = '';
            let voiceTranscriptCommitted = false;
            let voiceSessionFinalTranscript = '';
            let voiceSessionInterimTranscript = '';

            const formatRupiah = (n) => 'Rp ' + Number(n || 0).toLocaleString('id-ID');
            const normalizeTranscript = (text) => {
                const value = String(text || '').trim().replace(/\s+/g, ' ');
                if (!value) return '';
                return value.charAt(0).toUpperCase() + value.slice(1);
            };
            const appendTranscriptSegment = (existing, segment) => {
                const next = normalizeTranscript(segment);
                if (!next) return normalizeTranscript(existing);
                const current = normalizeTranscript(existing);
                if (!current) return next;
                if (current === next || current.endsWith(` ${next}`) || current.endsWith(next)) return current;
                return `${current} ${next}`;
            };
            const toPositiveInt = (value) => {
                const n = Number(value);
                return Number.isInteger(n) && n > 0 ? n : null;
            };
            const syncNotesScroll = () => {
                if (!notesTextarea) return;
                requestAnimationFrame(() => {
                    notesTextarea.scrollTop = notesTextarea.scrollHeight;
                });
            };
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

            const snapshotValidCart = () => {
                const menuItems = Array.from(cart.entries())
                    .map(([id, row]) => ({ id: toPositiveInt(id), row }))
                    .filter((entry) => entry.id && Number(entry.row?.qty || 0) > 0);
                const packageItems = Array.from(packageCart.entries())
                    .map(([id, row]) => ({ id: toPositiveInt(id), row }))
                    .filter((entry) => entry.id && Number(entry.row?.qty || 0) > 0);
                return { menuItems, packageItems };
            };

        window.scrollToSection = (id) => {
            const el = document.getElementById(id);
            if (el) {
                const navHeight = document.getElementById('navbar')?.offsetHeight || 64;
                const offset = el.offsetTop - navHeight - 20;
                window.scrollTo({ top: offset, behavior: 'smooth' });
            }
        };

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
                notesTextarea?.focus({ preventScroll: true });
            };
            const closeModal = () => {
                modal.classList.remove('open');
                modalBackdrop.classList.remove('open');
                modal.setAttribute('aria-hidden', 'true');
                stopVoiceRecognition(false);
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
                snapshotValidCart().menuItems.forEach(({ id, row }) => {
                    const mInp = document.createElement('input'); mInp.type='hidden'; mInp.name=`items[${i}][menu_id]`; mInp.value=String(id); orderItemsContainer.appendChild(mInp);
                    const qInp = document.createElement('input'); qInp.type='hidden'; qInp.name=`items[${i}][qty]`; qInp.value=String(row.qty); orderItemsContainer.appendChild(qInp);
                    i++;
                });
                let j = 0;
                snapshotValidCart().packageItems.forEach(({ id, row }) => {
                    const pInp = document.createElement('input'); pInp.type='hidden'; pInp.name=`packages[${j}][package_id]`; pInp.value=String(id); orderItemsContainer.appendChild(pInp);
                    const pqInp = document.createElement('input'); pqInp.type='hidden'; pqInp.name=`packages[${j}][qty]`; pqInp.value=String(row.qty); orderItemsContainer.appendChild(pqInp);
                    j++;
                });
            };

            const refreshOrderView = () => {
                updateQtyBadges();
                rebuildHiddenInputs();
                renderCartSummary();
            };

            const broadcastOrderSync = (payload = {}) => {
                const message = {
                    type: 'order-created',
                    ts: Date.now(),
                    table_id: @json($table->id),
                    table_label: @json($table->name),
                    ...payload,
                };

                try {
                    orderSyncChannel?.postMessage(message);
                } catch (_) {}

                try {
                    localStorage.setItem('cafe-order-sync-last', JSON.stringify(message));
                } catch (_) {}

                window.dispatchEvent(new CustomEvent('cafe:order-sync', { detail: message }));
            };

            const updateCartNoteSummary = () => {
                if (!cartNoteSummary) return;
                const note = String(notesTextarea?.value || '').trim();
                if (!note) {
                    cartNoteSummary.style.display = 'none';
                    cartNoteSummary.innerHTML = '';
                    return;
                }

                cartNoteSummary.style.display = 'block';
                cartNoteSummary.innerHTML = `<strong>Catatan Pesanan</strong>${note.replace(/\n/g, '<br>')}`;
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
                updateCartNoteSummary();

                if (cartList) {
                    cartList.innerHTML = entries.map((entry) => `
                        <div class="cart-row">
                            <div class="cart-row-main">
                                <strong>${entry.title}</strong>
                                <small>${entry.subtitle}</small>
                                ${entry.promoText ? `<div class="cart-row-promo">${entry.promoText}</div>` : ''}
                            </div>
                            <div class="cart-row-actions">
                                <strong class="cart-row-price">${formatRupiah(entry.total)}</strong>
                                <button type="button" class="cart-remove-btn" data-cart-kind="${entry.kind}" data-cart-id="${entry.id}">Hapus</button>
                            </div>
                        </div>
                    `).join('');
                }

                if (cartTotalPrice) {
                    const pricingRows = [
                        `<div class="cart-pricing-row"><span>Subtotal</span><strong>${formatRupiah(subtotal)}</strong></div>`
                    ];
                    if (specificDiscount > 0) {
                        pricingRows.push(`<div class="cart-pricing-row discount"><span>Diskon promo item</span><strong>-${formatRupiah(specificDiscount)}</strong></div>`);
                    }
                    if (discount > 0 && activePromo) {
                        pricingRows.push(`<div class="cart-pricing-row discount"><span>${activePromo.name}</span><strong>-${formatRupiah(discount)}</strong></div>`);
                    }
                    pricingRows.push(`<div class="cart-pricing-row total"><span>Total</span><strong>${formatRupiah(total)}</strong></div>`);
                    cartTotalPrice.innerHTML = pricingRows.join('');
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

            const setVoiceState = (text, active = false) => {
                if (voiceNoteState) voiceNoteState.textContent = text;
                if (voiceNoteBtn) {
                    voiceNoteBtn.classList.toggle('is-recording', active);
                    voiceNoteBtn.innerHTML = active 
                        ? '<i class="fas fa-stop"></i> Berhenti...' 
                        : '<i class="fas fa-microphone"></i> Mulai rekam';
                }
            };
            const stopVoiceRecognition = (resetState = true) => {
                if (speechRecognition && isRecordingVoice) {
                    try { speechRecognition.stop(); } catch (_) {}
                }
                isRecordingVoice = false;
                if (resetState) setVoiceState('Siap ngomong', false);
            };

            const startVoiceRecognition = () => {
                if (!SpeechRecognition) {
                    window.showToast('Browser ini belum mendukung input suara.', 'error');
                    return;
                }

                if (!speechRecognition) {
                    speechRecognition = new SpeechRecognition();
                    speechRecognition.lang = 'id-ID';
                    speechRecognition.continuous = true;
                    speechRecognition.interimResults = true;
                    speechRecognition.maxAlternatives = 1;

                    speechRecognition.onstart = () => {
                        isRecordingVoice = true;
                        voiceTranscriptCommitted = false;
                        voiceNotesBaseText = String(notesTextarea?.value || '').trim();
                        voiceSessionFinalTranscript = '';
                        voiceSessionInterimTranscript = '';
                        setVoiceState('Mendengarkan...', true);
                    };

                    speechRecognition.onresult = (event) => {
                        if (!notesTextarea) return;

                        let updatedFinal = voiceSessionFinalTranscript;
                        let updatedInterim = '';

                        for (let i = event.resultIndex; i < event.results.length; i++) {
                            const result = event.results[i];
                            const transcript = normalizeTranscript(result?.[0]?.transcript || '');
                            if (!transcript) continue;

                            if (result.isFinal) {
                                updatedFinal = appendTranscriptSegment(updatedFinal, transcript);
                            } else {
                                updatedInterim = appendTranscriptSegment(updatedInterim, transcript);
                            }
                        }

                        voiceSessionFinalTranscript = updatedFinal;
                        voiceSessionInterimTranscript = updatedInterim;

                        const combinedVoiceText = [voiceNotesBaseText, voiceSessionFinalTranscript, voiceSessionInterimTranscript]
                            .map((part) => normalizeTranscript(part))
                            .filter(Boolean)
                            .join(' ')
                            .trim();

                        if (combinedVoiceText) {
                            notesTextarea.value = combinedVoiceText;
                            notesTextarea.dispatchEvent(new Event('input', { bubbles: true }));
                            syncNotesScroll();
                        }

                        if (voiceSessionInterimTranscript) {
                            setVoiceState('Mendengarkan...', true);
                        } else if (voiceSessionFinalTranscript) {
                            setVoiceState('Tersimpan', true);
                        }
                    };

                    speechRecognition.onerror = (event) => {
                        const errorType = event?.error || 'unknown';
                        isRecordingVoice = false;
                        setVoiceState('Siap', false);
                        if (errorType !== 'no-speech' && errorType !== 'aborted') {
                            window.showToast('Gagal menangkap suara. Coba lagi.', 'error');
                        }
                    };

                    speechRecognition.onend = () => {
                        isRecordingVoice = false;
                        voiceTranscriptCommitted = Boolean(voiceSessionFinalTranscript);
                        if (!voiceTranscriptCommitted) {
                            setVoiceState('Siap ngomong', false);
                        } else {
                            setVoiceState('Tersimpan', false);
                        }
                    };
                }

                try {
                    speechRecognition.start();
                } catch (_) {
                    window.showToast('Input suara sedang sibuk. Coba lagi sebentar.', 'error');
                }
            };

            voiceNoteBtn?.addEventListener('click', () => {
                if (isRecordingVoice) {
                    stopVoiceRecognition();
                    return;
                }
                startVoiceRecognition();
            });

            notesTextarea?.addEventListener('input', () => {
                updateCartNoteSummary();
                syncNotesScroll();
            });

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
                document.querySelectorAll('#packageGrid .public-package-card').forEach(card => {
                    const key = card.getAttribute('data-menu-category-key') || 'paket';
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
                const promoCard = e.target.closest('.promo-card[data-promo-action="quick-add"]');
                if (promoCard) {
                    const itemType = promoCard.dataset.itemType === 'package' ? 'package' : 'menu';
                    const itemId = parseItemNumber(promoCard.dataset.id);
                    const itemName = promoCard.dataset.name || 'Promo item';
                    const itemPrice = parseItemNumber(promoCard.dataset.price);
                    const originalPrice = parseItemNumber(promoCard.dataset.originalPrice) || itemPrice;
                    const promoMeta = parsePromoMeta(promoCard.dataset.promoMeta);

                    if (itemType === 'package') {
                        packageCart.set(itemId, {
                            name: itemName,
                            qty: (packageCart.get(itemId)?.qty || 0) + 1,
                            price: itemPrice,
                            originalPrice,
                            promoMeta: promoMeta || null,
                        });
                    } else {
                        cart.set(itemId, {
                            name: itemName,
                            qty: (cart.get(itemId)?.qty || 0) + 1,
                            price: itemPrice,
                            originalPrice,
                            promoMeta: promoMeta || null,
                        });
                    }

                    refreshOrderView();
                    openCartDrawer();
                    window.showToast('Promo ditambahkan ke keranjang.', 'success');
                    return;
                }

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

                const { menuItems, packageItems } = snapshotValidCart();

                if (menuItems.length === 0 && packageItems.length === 0) {
                    window.showToast('Keranjang masih kosong. Pilih menu dulu.', 'error');
                    return;
                }

                submitOrderBtn?.setAttribute('disabled', 'disabled');
                const originalSubmitText = submitOrderBtn?.textContent || 'Kirim Pesanan';
                if (submitOrderBtn) {
                    submitOrderBtn.textContent = 'Mengirim...';
                }
                try {
                    const formData = new FormData();
                    formData.set('_token', csrfToken);
                    formData.set('notes', String(notesTextarea?.value || ''));
                    menuItems.forEach(({ id, row }, index) => {
                        formData.set(`items[${index}][menu_id]`, String(id));
                        formData.set(`items[${index}][qty]`, String(row.qty));
                    });
                    packageItems.forEach(({ id, row }, index) => {
                        formData.set(`packages[${index}][package_id]`, String(id));
                        formData.set(`packages[${index}][qty]`, String(row.qty));
                    });

                    const res = await fetch(orderForm.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: formData,
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
                    broadcastOrderSync({
                        order_id: payload.order_id || null,
                        latest_ts: payload.latest_ts || Date.now(),
                        count: payload.count || 0,
                    });
                    window.showToast(payload.message || 'Pesanan berhasil dikirim.', 'success');
                } catch (err) {
                    window.showToast(err.message || 'Gagal mengirim pesanan.', 'error');
                } finally {
                    if (submitOrderBtn) {
                        submitOrderBtn.textContent = originalSubmitText;
                    }
                    if (menuItems.length > 0 || packageItems.length > 0) {
                        submitOrderBtn?.removeAttribute('disabled');
                    } else {
                        submitOrderBtn?.setAttribute('disabled', 'disabled');
                    }
                }
            });

            refreshOrderView(); applyMenuFilter('all');

            let isPolling = false;
            const brandIcon = document.getElementById('brandIcon');
            const brandName = document.getElementById('brandName');
            const heroSection = document.getElementById('heroSection');
            const heroTag = document.getElementById('heroTag');
            const heroTitle = document.getElementById('heroTitle');
            const heroDesc = document.getElementById('heroDesc');
            const heroButton = document.getElementById('heroButton');
            let lastSettingsTs = Number(@json($initial_settings_ts ?? 0));

            const renderBrandAndHero = (brandData = {}) => {
                if (brandName && typeof brandData.name === 'string' && brandData.name.trim() !== '') {
                    brandName.textContent = brandData.name;
                }

                if (brandIcon) {
                    if (brandData.logo_url) {
                        brandIcon.innerHTML = `<img src="${brandData.logo_url}" alt="${(brandData.name || 'cafecaf').replace(/"/g, '&quot;')}" style="width:100%;height:100%;object-fit:cover;border-radius:inherit;">`;
                    } else {
                        brandIcon.innerHTML = '<i class="fas fa-utensils"></i>';
                    }
                }

                const heroData = brandData.hero || {};
                if (heroTag) {
                    const heroTagText = heroTag.querySelector('span');
                    if (heroTagText && typeof heroData.tag === 'string' && heroData.tag.trim() !== '') {
                        heroTagText.textContent = heroData.tag;
                    }
                }
                if (heroTitle && typeof heroData.title === 'string' && heroData.title.trim() !== '') {
                    heroTitle.textContent = heroData.title;
                }
                if (heroDesc && typeof heroData.desc === 'string' && heroData.desc.trim() !== '') {
                    heroDesc.textContent = heroData.desc;
                }
                if (heroButton) {
                    const heroButtonText = heroButton.querySelector('span');
                    if (heroButtonText && typeof heroData.button_text === 'string' && heroData.button_text.trim() !== '') {
                        heroButtonText.textContent = heroData.button_text;
                    }
                }
                if (heroSection) {
                    if (heroData.image_url) {
                        heroSection.classList.add('has-image');
                        heroSection.style.backgroundImage = `url("${heroData.image_url}")`;
                    } else {
                        heroSection.classList.remove('has-image');
                        heroSection.style.backgroundImage = '';
                    }
                }
            };

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
                        const nextSettingsTs = Number(mData.settings_ts || 0);

                        if (nextSettingsTs !== lastSettingsTs && mData.brand) {
                            renderBrandAndHero(mData.brand);
                            lastSettingsTs = nextSettingsTs;
                        }

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

            window.addEventListener('scroll', () => {
                document.getElementById('navbar')?.classList.toggle('scrolled', window.scrollY > 10);
            });

            setInterval(() => { if (document.visibilityState === 'visible') pollData(); }, 1000);
        })();
    </script>
</body>
</html>
