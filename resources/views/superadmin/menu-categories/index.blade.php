@extends('superadmin.layout')

@section('title', 'Kategori Menu')
@section('page_title', 'Kategori Menu')
@section('page_description', 'Tambah, edit, dan hapus kategori menu yang dipakai di form menu.')

@section('content')
    <div class="content-toolbar">
        <div></div>
        <a class="primary-link" href="{{ route('superadmin.menu-categories.create') }}">+ Tambah Kategori</a>
    </div>

    <div class="panel">
        <div class="panel-head">
            <h2>Daftar Kategori</h2>
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
    </div>
@endsection
