@php
    $isEdit = isset($mode) && $mode === 'edit';
@endphp

@push('head')
    <style>
        .menu-form { display: flex; flex-direction: column; gap: 1.5rem; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; }
        .field { display: flex; flex-direction: column; gap: 0.4rem; }
        .field label { font-size: 0.85rem; font-weight: 600; color: var(--text-muted); }
        .field input, .field select { width: 100%; padding: 0.65rem 1rem; border: 1px solid var(--accent); border-radius: 12px; background: #fff; color: var(--text-main); font-size: 0.95rem; outline: none; }
        .field input:focus, .field select:focus { border-color: var(--highlight); box-shadow: 0 0 0 3px rgba(212, 163, 115, 0.15); }
        
        .image-box { grid-column: span 2; display: grid; grid-template-columns: 1fr 150px; gap: 1.5rem; align-items: start; }
        .file-input-wrapper { margin-top: 0.15rem; }
        .file-custom-btn { display: inline-flex; background: var(--secondary); color: #fff; padding: 0.45rem 0.85rem; border-radius: 8px; cursor: pointer; font-weight: 600; font-size: 0.85rem; margin-bottom: 0.5rem; }
        .file-name { font-size: 0.8rem; color: var(--text-muted); font-style: italic; }
        .image-preview { width: 150px; height: 150px; object-fit: cover; border-radius: 16px; border: 2px dashed var(--accent); padding: 4px; background-color: var(--bg-main); cursor: pointer; }

        .cropper-modal { position:fixed; inset:0; z-index:1700; display:grid; place-items:center; background:rgba(56,37,30,.34); backdrop-filter:blur(3px); }
        .cropper-modal[hidden] { display:none !important; }
        .cropper-dialog { width:min(560px, calc(100vw - 2rem)); background:#fff; border:1px solid var(--accent); border-radius:18px; box-shadow:0 18px 45px rgba(62,39,35,.18); overflow:hidden; }
        .cropper-head, .cropper-foot { display:flex; align-items:center; justify-content:space-between; gap:1rem; padding:1rem 1.15rem; border-bottom:1px solid var(--accent); }
        .cropper-foot { border-bottom:0; border-top:1px solid var(--accent); justify-content:flex-end; }
        .cropper-head strong { color:var(--primary); font-family:'Playfair Display', Georgia, serif; font-size:1.2rem; }
        .cropper-body { display:grid; place-items:center; gap:.85rem; padding:1.15rem; }
        .cropper-canvas { width:min(360px, 78vw); height:min(360px, 78vw); border:1px dashed var(--accent); border-radius:18px; background:#fffaf5; cursor:move; touch-action:none; }
        .cropper-control { display:grid; gap:0.35rem; width:min(360px, 78vw); color:var(--text-muted); font-size:0.85rem; font-weight:600; }
        .cropper-control input { accent-color:var(--highlight); }
        .cropper-close, .cropper-done { border:1px solid var(--accent); background:#fff; color:var(--primary); border-radius:12px; padding:.55rem 1rem; cursor:pointer; font-weight:700; }
        .cropper-done { background:var(--highlight); color:#fff; border-color:var(--highlight); }

        .form-actions { display: flex; justify-content: flex-end; gap: 1rem; margin-top: 0.5rem; padding-top: 1.5rem; border-top: 1px solid var(--accent); }
        .secondary-link { display: inline-flex; align-items: center; color: var(--primary); text-decoration: none; padding: 0.6rem 1.2rem; border-radius: 12px; font-weight: 600; border: 1px solid var(--accent); }
        .primary-link { display: inline-flex; align-items: center; justify-content: center; background: var(--highlight); color: #fff; text-decoration: none; padding: 0.6rem 1.2rem; border-radius: 12px; font-weight: 600; border: none; cursor: pointer; }
        
        .form-error { font-size: 0.8rem; color: var(--loss); margin-top: 0.15rem; font-weight: 500; }

        @media (max-width: 768px) {
            .form-grid { grid-template-columns: 1fr; }
            .image-box { grid-column: span 1; grid-template-columns: 1fr; }
            .image-preview { width: 100%; height: 180px; }
            .form-actions { flex-direction: column; }
            .form-actions a, .form-actions button { width: 100%; justify-content: center; }
        }
    </style>
@endpush

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
                <input id="name" type="text" name="name" value="{{ old('name', $menu->name) }}" required placeholder="Cth: Caramel Macchiato">
                @error('name')<small class="form-error">{{ $message }}</small>@enderror
            </div>

            <div class="field">
                <label for="selling_price">Harga Jual</label>
                <input id="selling_price" type="number" step="0.01" name="selling_price" value="{{ old('selling_price', (float) $menu->selling_price) }}" required placeholder="35000">
                @error('selling_price')<small class="form-error">{{ $message }}</small>@enderror
            </div>

            <div class="field">
                <label for="cost_price">Harga Modal</label>
                <input id="cost_price" type="number" step="0.01" name="cost_price" value="{{ old('cost_price', (float) $menu->cost_price) }}" required placeholder="12000">
                @error('cost_price')<small class="form-error">{{ $message }}</small>@enderror
            </div>

            <div class="field image-box" data-cropper data-cropper-size="600">
                <div class="file-input-wrapper">
                    <label for="image">Foto Menu</label>
                    <br>
                    <input id="image" type="file" name="image" accept="image/*" data-cropper-input style="display:none;">
                    <input type="hidden" name="cropped_image" id="cropped_image" data-cropper-output>
                    <label for="image" class="file-custom-btn">Pilih Foto</label>
                    <div class="file-name" id="file-name-display" data-cropper-filename>
                        {{ $menu->image_path ? basename($menu->image_path) : 'Belum ada file dipilih' }}
                    </div>
                    @error('image')<small class="form-error">{{ $message }}</small>@enderror
                    @error('cropped_image')<small class="form-error">{{ $message }}</small>@enderror
                </div>
                
                <div class="selected-photo" data-cropper-preview-wrap {{ $menu->image_path ? '' : 'hidden' }}>
                    <img
                        class="image-preview"
                        data-cropper-preview
                        src="{{ $menu->image_path ? asset('storage/' . $menu->image_path) : asset('images/menu-placeholder.svg') }}"
                        alt="Preview menu"
                        title="Klik untuk crop ulang"
                    >
                </div>

                <div class="cropper-modal" data-cropper-panel hidden>
                    <div class="cropper-dialog">
                        <div class="cropper-head">
                            <strong>Atur Crop Foto Menu</strong>
                            <button type="button" class="cropper-close" data-cropper-close>Tutup</button>
                        </div>
                        <div class="cropper-body">
                            <canvas class="cropper-canvas" data-cropper-canvas></canvas>
                            <label class="cropper-control">
                                Zoom crop
                                <input type="range" min="1" max="3" step="0.05" value="1" data-cropper-zoom>
                            </label>
                        </div>
                        <div class="cropper-foot">
                            <button type="button" class="cropper-done" data-cropper-close>Pakai Crop Ini</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('superadmin.menus.index') }}" class="secondary-link">Batal</a>
            <button type="submit" class="primary-link">{{ $isEdit ? 'Simpan Perubahan' : 'Buat Menu' }}</button>
        </div>
    </form>
</div>

<script src="{{ asset('js/cafe-image-cropper.js') }}?v=4"></script>
