@extends('superadmin.layout')

@section('title', 'Kategori Menu')
@section('page_title', 'Kategori Menu')
@section('page_description', 'Kategori menu dikunci menjadi 3 pilihan tetap: makanan, minuman, dan paket.')

@push('head')
    <style>
        .locked-banner {
            background: #fffaf5;
            border: 1px solid var(--accent);
            border-radius: 18px;
            padding: 1rem 1.25rem;
            color: var(--text-main);
            margin-bottom: 1.25rem;
            box-shadow: 0 4px 15px var(--shadow);
        }

        .category-list {
            display: grid;
            gap: 0.85rem;
        }

        .category-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 1.25rem;
            background: #fffaf5;
            border: 1px solid var(--accent);
            border-radius: 16px;
        }

        .category-row strong {
            display: block;
            color: var(--primary);
            font-size: 1.05rem;
            margin-bottom: 0.15rem;
            text-transform: capitalize;
        }

        .category-row .muted {
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .category-pill {
            display: inline-flex;
            align-items: center;
            padding: 0.3rem 0.75rem;
            border-radius: 999px;
            background: #efebe9;
            color: var(--primary);
            font-weight: 700;
            font-size: 0.8rem;
            text-transform: capitalize;
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
            margin-bottom: 1.25rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--accent);
        }

        .panel-head h2 {
            margin: 0;
            color: var(--primary);
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 1.9rem;
        }

        .panel-head span {
            font-size: 0.9rem;
            color: var(--text-muted);
            font-weight: 500;
        }
    </style>
@endpush

@section('content')
    <div class="locked-banner">
        Kategori menu dikunci. Yang tersedia hanya: <strong>makanan</strong>, <strong>minuman</strong>, dan <strong>paket</strong>.
        Tidak bisa ditambah, diubah, atau dihapus.
    </div>

    <div class="panel">
        <div class="panel-head">
            <h2>Daftar Kategori</h2>
            <span>{{ $categories->count() }} kategori tetap</span>
        </div>

        <div class="category-list">
            @foreach ($categories as $category)
                <div class="category-row">
                    <div>
                        <strong>{{ $category->name }}</strong>
                        <div class="muted">{{ $category->menus_count }} menu</div>
                    </div>
                    <span class="category-pill">Tetap</span>
                </div>
            @endforeach
        </div>
    </div>
@endsection
