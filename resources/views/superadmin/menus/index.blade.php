@extends('superadmin.layout')

@push('head')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap');

        :root {
            --bg-main: #f9f5f0;
            --bg-card: #ffffff;
            --primary: #795548;
            --secondary: #bcaaa4;
            --accent: #d7ccc8;
            --highlight: #d4a373;
            --text-main: #6d4c41;
            --text-muted: #a1887f;
            --profit: #81c784;
            --loss: #e57373;
            --shadow: rgba(121, 85, 72, 0.08);
            --line: rgba(121, 85, 72, 0.14);
        }

        .main-panel {
            padding: 2rem 2.5rem;
            background-color: var(--bg-main);
            color: var(--text-main);
            line-height: 1.6;
            background-image: radial-gradient(var(--accent) 1px, transparent 1px);
            background-size: 24px 24px;
        }

        .page-header {
            margin-bottom: 1.5rem;
        }

        .page-kicker {
            display: inline-flex;
            align-items: center;
            background-color: var(--highlight);
            color: #fff;
            font-size: 0.75rem;
            padding: 0.35rem 0.75rem;
            border-radius: 50px;
            font-weight: 600;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .page-header h1 {
            font-family: 'Playfair Display', Georgia, serif;
            color: var(--primary);
            font-size: 1.8rem;
            margin: 0.5rem 0 0.25rem;
        }

        .page-header p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        .content-toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .search-box {
            display: flex;
            gap: 0.5rem;
        }

        .search-box input {
            border: 1px solid var(--accent);
            background: var(--bg-card);
            padding: 0.6rem 1rem;
            border-radius: 12px;
            font-family: inherit;
            color: var(--text-main);
            outline: none;
            transition: border-color 0.2s ease;
            min-width: 250px;
        }

        .search-box input:focus {
            border-color: var(--highlight);
            box-shadow: 0 0 0 3px rgba(212, 163, 115, 0.15);
        }

        .search-box button {
            background: var(--secondary);
            color: #fff;
            border: none;
            padding: 0.6rem 1.2rem;
            border-radius: 12px;
            cursor: pointer;
            font-weight: 600;
            transition: background 0.2s ease;
        }

        .search-box button:hover {
            background: var(--primary);
        }

        .primary-link {
            display: inline-flex;
            align-items: center;
            background: var(--highlight);
            color: #fff;
            text-decoration: none;
            padding: 0.6rem 1.2rem;
            border-radius: 12px;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(212, 163, 115, 0.3);
            transition: all 0.3s ease;
        }

        .primary-link:hover {
            background-color: #c68b59;
            transform: translateY(-2px);
        }

        .panel {
            background: var(--bg-card);
            padding: 2rem;
            border-radius: 20px;
            box-shadow: 0 4px 15px var(--shadow);
        }

        .panel-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--accent);
        }

        .panel-head h2 {
            font-family: 'Playfair Display', Georgia, serif;
            color: var(--primary);
            font-size: 1.4rem;
            margin: 0;
            border: none;
            padding: 0;
        }

        .panel-head span {
            font-size: 0.9rem;
            color: var(--text-muted);
            font-weight: 500;
        }

        .tag {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            background-color: #efebe9;
            color: var(--primary);
        }

        .tag-success {
            background-color: #e8f5e9;
            color: #558b2f;
        }

        .tag-muted {
            background-color: #f5f5f5;
            color: #9e9e9e;
        }

        .actions {
            display: flex;
            gap: 0.75rem;
            align-items: center;
            margin-top: 0;
            line-height: 1;
        }

        .actions a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: var(--highlight);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.875rem;
            transition: color 0.2s ease;
            margin: 0;
        }

        .actions a:hover {
            color: var(--primary);
            text-decoration: underline;
        }

        .actions form {
            display: flex;
            align-items: center;
            margin: 0;
        }

        .actions button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: none;
            border: none;
            color: var(--loss);
            cursor: pointer;
            font-weight: 600;
            font-size: 0.875rem;
            padding: 0;
            margin: 0;
            transition: color 0.2s ease;
        }

        .actions button:hover {
            color: #b71c1c;
            text-decoration: underline;
        }

        .menu-layout {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 1.5rem;
            align-items: flex-start;
        }

        .menu-card-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .menu-card {
            display: flex;
            gap: 1.25rem;
            padding: 1rem;
            background: #fffaf5;
            border: 1px solid var(--accent);
            border-radius: 16px;
            transition: all 0.2s ease;
        }

        .menu-card:hover {
            border-color: var(--highlight);
            box-shadow: 0 4px 12px var(--shadow);
        }

        .menu-thumb {
            width: 90px;
            height: 90px;
            border-radius: 12px;
            object-fit: cover;
            background-color: var(--bg-main);
            flex-shrink: 0;
        }

        .menu-meta {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .menu-meta h3 {
            font-family: 'Playfair Display', Georgia, serif;
            color: var(--primary);
            font-size: 1.15rem;
            margin-bottom: 0.1rem;
        }

        .menu-meta p {
            color: var(--text-muted);
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
        }

        .menu-pricing {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 0.25rem;
        }

        .category-list {
            display: flex;
            flex-direction: column;
        }

        .category-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.85rem 0.25rem;
            border-bottom: 1px solid var(--accent);
        }

        .category-row:last-child {
            border-bottom: none;
        }

        .category-row strong {
            color: var(--primary);
            font-size: 0.95rem;
        }

        .category-row .muted {
            color: var(--text-muted);
            font-size: 0.8rem;
        }

        .category-row .actions {
            margin-top: 0;
        }

        .mt-16 {
            margin-top: 1.5rem;
        }

        .secondary-link {
            display: inline-flex;
            align-items: center;
            color: var(--primary);
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            border: 1px solid var(--accent);
            transition: all 0.2s ease;
        }

        .secondary-link:hover {
            background-color: var(--bg-main);
            border-color: var(--highlight);
            color: var(--highlight);
        }

        .alert {
            background: var(--bg-card);
            border-radius: 12px;
            padding: 1rem 1.1rem;
            box-shadow: 0 4px 15px var(--shadow);
            margin-bottom: 1rem;
            color: var(--text-muted);
            font-style: italic;
        }

        .menu-pagination {
            margin-top: 1.25rem;
        }

        .pagination-wrap {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
            margin-top: 1.25rem;
            padding-top: 1rem;
            border-top: 1px solid var(--accent);
        }

        .pagination-meta {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .pagination-links {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .pagination-link,
        .pagination-dots {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            padding: 0 0.85rem;
            border-radius: 12px;
            border: 1px solid var(--accent);
            background: var(--bg-card);
            color: var(--primary);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .pagination-link.active {
            background: var(--highlight);
            color: #fff;
            border-color: var(--highlight);
        }

        .pagination-link.disabled {
            color: var(--text-muted);
            background: #f9f5f0;
            cursor: not-allowed;
        }

        .pagination-dots {
            border-color: transparent;
            background: transparent;
        }

        @media (max-width: 1100px) {
            .app-shell {
                grid-template-columns: 1fr;
            }

            .sidebar {
                border-right: 0;
                border-bottom: 1px solid rgba(121, 85, 72, 0.08);
            }
        }

        @media (max-width: 900px) {
            .menu-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .main-panel {
                padding: 1.5rem 1rem;
            }

            .content-toolbar {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box input {
                min-width: auto;
                width: 100%;
            }

            .page-header h1 {
                font-size: 1.5rem;
            }

            .menu-card {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }

            .menu-thumb {
                width: 100%;
                height: 160px;
            }

            .menu-meta {
                align-items: center;
            }

            .menu-pricing {
                justify-content: center;
            }

            .actions {
                justify-content: center;
            }
        }
    </style>
@endpush

@section('title', 'Manajemen Menu')
@section('page_title', 'Manajemen Menu')
@section('page_description', 'Tambah, edit, hapus menu, foto, dan harga.')

@section('content')
    <div class="content-toolbar">
        <form method="GET" action="{{ route('superadmin.menus.index') }}" class="search-box">
            <input type="text" name="search" placeholder="Cari menu atau kode" value="{{ request('search') }}">
            <button type="submit">Cari</button>
        </form>

        <a class="primary-link" href="{{ route('superadmin.menus.create') }}">+ Tambah Menu</a>
    </div>

    <div class="panel">
        <div class="panel-head">
            <h2>Daftar Menu</h2>
            <span>{{ $menus->total() }} menu</span>
        </div>

        <div class="menu-card-list">
            @forelse ($menus as $menu)
                <div class="menu-card">
                    <img
                        class="menu-thumb"
                        src="{{ $menu->image_path ? asset('storage/' . $menu->image_path) : asset('images/menu-placeholder.svg') }}"
                        alt="{{ $menu->name }}"
                    >
                    <div class="menu-meta">
                        <h3>{{ $menu->name }}</h3>
                        <p>{{ $menu->code }}</p>
                        <div class="menu-pricing">
                            <span class="tag">{{ $menu->category?->name ?? 'Tanpa kategori' }}</span>
                            <span class="tag tag-success">Rp {{ number_format((float) $menu->selling_price, 0, ',', '.') }}</span>
                            <span class="tag tag-muted">Modal Rp {{ number_format((float) $menu->cost_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="actions">
                            <a href="{{ route('superadmin.menus.edit', $menu) }}">Edit</a>
                            <form method="POST" action="{{ route('superadmin.menus.destroy', $menu) }}" onsubmit="return confirm('Hapus menu ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert">Belum ada menu.</div>
            @endforelse
        </div>

        <div class="menu-pagination">
            {{ $menus->links('components.pagination') }}
        </div>
    </div>
@endsection
