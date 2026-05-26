@extends('superadmin.layout')

@section('title', 'Kategori Menu')
@section('page_title', 'Kategori Menu')
@section('page_description', 'Tambah, edit, dan hapus kategori menu yang dipakai di form menu.')

@push('head')
    <style>
        .category-pagination {
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

        @media (max-width: 768px) {
            .pagination-wrap {
                justify-content: center;
            }
        }
    </style>
@endpush

@section('content')
    <div class="content-toolbar">
        <div></div>
        <a class="primary-link" href="{{ route('superadmin.menu-categories.create') }}">+ Tambah Kategori</a>
    </div>

    <div class="panel">
        <div class="panel-head">
            <h2>Daftar Kategori</h2>
            <span>{{ $categories->total() }} kategori</span>
        </div>

        <div class="category-list">
            @forelse ($categories as $category)
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
            @empty
                <div class="alert">Belum ada kategori.</div>
            @endforelse
        </div>

        <div class="category-pagination">
            {{ $categories->links('components.pagination') }}
        </div>
    </div>
@endsection
