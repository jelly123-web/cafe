@php
    $isEdit = isset($mode) && $mode === 'edit';
@endphp

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
