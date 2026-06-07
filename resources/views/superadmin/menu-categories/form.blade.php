@php
    $isEdit = isset($menuCategory);
    $category = $menuCategory ?? new \App\Models\MenuCategory();
@endphp

@push('head')
    <style>
        .category-form .form-actions .secondary-link {
            background: #f3f3f3;
            color: #8d8d8d !important;
            border: 1px solid #e0e0e0;
            font-weight: 700;
        }

        .category-form .form-actions .secondary-link:hover {
            background: #ececec;
            color: #7d7d7d !important;
            border-color: #d6d6d6;
        }

        .category-form .form-actions .primary-link {
            background: var(--highlight);
            color: #fff !important;
        }

        .category-form .form-actions .primary-link:hover {
            background: #c68b59;
            color: #fff !important;
        }
    </style>
@endpush

<div class="panel">
    <form method="POST" action="{{ $isEdit ? route('superadmin.menu-categories.update', $category) : route('superadmin.menu-categories.store') }}" class="category-form">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="field">
            <label for="name">Nama Kategori</label>
            <input id="name" type="text" name="name" value="{{ old('name', $category->name) }}" required>
            @error('name')<small class="form-error">{{ $message }}</small>@enderror
        </div>

        <div class="form-actions">
            <a href="{{ route('superadmin.menu-categories.index') }}" class="secondary-link">Batal</a>
            <button type="submit" class="primary-link">{{ $isEdit ? 'Simpan Perubahan' : 'Buat Kategori' }}</button>
        </div>
    </form>
</div>
