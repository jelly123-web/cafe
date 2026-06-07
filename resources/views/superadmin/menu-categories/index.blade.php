@extends('superadmin.layout')

@section('title', 'Kategori Menu')
@section('page_title', 'Kategori Menu')
@section('page_description', 'Kelola kategori menu untuk pengelompokan produk.')

@push('head')
    <style>
        .panel { background: var(--white); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 24px; margin-bottom: 20px; overflow: hidden; }
        .panel-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 20px; padding-bottom: 16px; border-bottom: 1px solid var(--border-light); flex-wrap: wrap; }
        .panel-head h2 { font-size: 16px; font-weight: 800; color: var(--fg); letter-spacing: -0.2px; margin: 0; display: flex; align-items: center; gap: 8px; }
        .panel-head h2 i { color: var(--accent); font-size: 18px; }
        .panel-meta { font-size: 13px; color: var(--muted); margin-top: 4px; font-weight: 500; }
        
        .category-list { display: grid; gap: 12px; }
        .category-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: 16px 20px; background: var(--white);
            border: 1px solid var(--border); border-radius: var(--radius-md);
            transition: all var(--transition);
        }
        .category-row:hover { box-shadow: var(--shadow-sm); border-color: var(--accent); transform: translateY(-1px); }
        .cat-left { display: flex; align-items: center; gap: 16px; min-width: 0; }
        .cat-info strong { display: block; color: var(--fg); font-size: 14px; font-weight: 800; }
        .cat-info .muted { color: var(--muted); font-size: 12px; font-weight: 500; margin-top: 2px; }
        .cat-right { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
        
        /* Buttons */
        .btn { border: 1.5px solid transparent; border-radius: var(--radius-sm); padding: 8px 16px; cursor: pointer; font-weight: 700; font-size: 13px; display: inline-flex; align-items: center; gap: 6px; transition: all var(--transition); }
        .btn-primary { background: var(--accent); color: white; }
        .btn-primary:hover { background: var(--accent-dark); }
        .btn-danger { background: transparent; color: var(--red); border-color: #FECACA; }
        .btn-danger:hover { background: var(--red-light); border-color: var(--red); }
        .btn-outline { background: var(--white); color: var(--fg-secondary); border-color: var(--border); }
        .btn-outline:hover { background: var(--accent-light); border-color: var(--accent); color: var(--accent-dark); }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        
        .menu-count-badge {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 4px 10px; border-radius: var(--radius-full);
            font-size: 11px; font-weight: 700;
            background: var(--bg); color: var(--fg-secondary);
        }
    </style>
@endpush

@section('content')
    <section class="panel fade-in">
        <div class="panel-head">
            <div>
                <h2><i class="fas fa-layer-group"></i> Daftar Kategori</h2>
                <div class="panel-meta">{{ $categories->count() }} kategori terdaftar</div>
            </div>
            <div style="display:flex; gap: 10px;">
                <a href="{{ route('superadmin.menu-categories.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Kategori</a>
                <form method="POST" action="{{ route('superadmin.menu-categories.destroy-all') }}" onsubmit="return confirm('Hapus semua?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash-can"></i> Hapus Semua</button>
                </form>
            </div>
        </div>

        <div class="category-list">
            @foreach($categories as $category)
                <div class="category-row">
                    <div class="cat-left">
                        <div class="cat-info">
                            <strong>{{ $category->name }}</strong>
                            <div class="muted">{{ $category->description ?? 'Tanpa deskripsi' }}</div>
                        </div>
                    </div>
                    <div class="cat-right">
                        <span class="menu-count-badge">{{ $category->display_count }} {{ $category->display_label }}</span>
                        <a href="{{ route('superadmin.menu-categories.edit', $category) }}" class="btn btn-outline btn-sm"><i class="fas fa-pen"></i></a>
                        <form method="POST" action="{{ route('superadmin.menu-categories.destroy', $category) }}" onsubmit="return confirm('Hapus?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection
