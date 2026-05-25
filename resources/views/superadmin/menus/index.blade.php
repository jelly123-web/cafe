@extends('superadmin.layout')

@section('title', 'Manajemen Menu')
@section('page_title', 'Manajemen Menu')
@section('page_description', 'Tambah, edit, hapus menu, atur kategori, foto, dan harga.')

@section('content')
    <div class="content-toolbar">
        <form method="GET" action="{{ route('superadmin.menus.index') }}" class="search-box">
            <input type="text" name="search" placeholder="Cari menu atau kode" value="{{ request('search') }}">
            <button type="submit">Cari</button>
        </form>

        <a class="primary-link" href="{{ route('superadmin.menus.create') }}">+ Tambah Menu</a>
    </div>

    <div class="menu-layout">
        <div class="panel">
            <div class="panel-head">
                <h2>Daftar Menu</h2>
                <span>{{ $menus->count() }} menu</span>
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
        </div>

        <div class="panel">
            <div class="panel-head">
                <h2>Kategori Menu</h2>
                <span>{{ $categories->count() }} kategori</span>
            </div>

            <div class="category-list">
                @foreach ($categories as $category)
                    <div class="category-row">
                        <div>
                            <strong>{{ $category->name }}</strong>
                            <div class="muted">{{ $category->menus_count }} menu</div>
                        </div>
                        <div class="actions">
                            <a href="{{ route('superadmin.menu-categories.edit', $category) }}">Edit</a>
                            <form method="POST" action="{{ route('superadmin.menu-categories.destroy', $category) }}" onsubmit="return confirm('Hapus kategori ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Hapus</button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-16">
                <a class="secondary-link" href="{{ route('superadmin.menu-categories.create') }}">+ Tambah Kategori</a>
            </div>
        </div>
    </div>
@endsection
