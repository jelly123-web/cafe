@php
    $isEdit = isset($mode) && $mode === 'edit';
@endphp

<div class="panel">
    <form method="POST" action="{{ $isEdit ? route('superadmin.menus.update', $menu) : route('superadmin.menus.store') }}" enctype="multipart/form-data" class="menu-form">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="form-grid">
            <div class="field">
                <label for="code">Kode Menu</label>
                <input id="code" type="text" name="code" value="{{ old('code', $menu->code) }}" required>
                @error('code')<small class="form-error">{{ $message }}</small>@enderror
            </div>

            <div class="field">
                <label for="menu_category_id">Kategori</label>
                <select id="menu_category_id" name="menu_category_id">
                    <option value="">Tanpa kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('menu_category_id', $menu->menu_category_id) == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('menu_category_id')<small class="form-error">{{ $message }}</small>@enderror
            </div>

            <div class="field">
                <label for="name">Nama Menu</label>
                <input id="name" type="text" name="name" value="{{ old('name', $menu->name) }}" required>
                @error('name')<small class="form-error">{{ $message }}</small>@enderror
            </div>

            <div class="field">
                <label for="selling_price">Harga Jual</label>
                <input id="selling_price" type="number" step="0.01" name="selling_price" value="{{ old('selling_price', $menu->selling_price) }}" required>
                @error('selling_price')<small class="form-error">{{ $message }}</small>@enderror
            </div>

            <div class="field">
                <label for="cost_price">Harga Modal</label>
                <input id="cost_price" type="number" step="0.01" name="cost_price" value="{{ old('cost_price', $menu->cost_price) }}" required>
                @error('cost_price')<small class="form-error">{{ $message }}</small>@enderror
            </div>

            <div class="field image-box">
                <label for="image">Foto Menu</label>
                <input id="image" data-menu-image-input type="file" name="image" accept="image/*">
                @error('image')<small class="form-error">{{ $message }}</small>@enderror
                <img
                    class="image-preview"
                    data-menu-image-preview
                    src="{{ $menu->image_path ? asset('storage/' . $menu->image_path) : asset('images/menu-placeholder.svg') }}"
                    alt="Preview menu"
                >
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('superadmin.menus.index') }}" class="secondary-link">Batal</a>
            <button type="submit" class="primary-link">{{ $isEdit ? 'Simpan Perubahan' : 'Buat Menu' }}</button>
        </div>
    </form>
</div>
