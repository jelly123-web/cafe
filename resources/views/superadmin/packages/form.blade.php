@php
    $isEdit = isset($mode) && $mode === 'edit';
    $selectedMenus = $selectedMenus ?? old('menus', []);
    $selectedMenus = is_array($selectedMenus) ? $selectedMenus : [$selectedMenus];
    $menuQuantities = $menuQuantities ?? old('menu_quantities', []);
@endphp

@push('head')
    <style>
        .package-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
        .package-field { display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 1.25rem; }
        .package-field.full { grid-column: 1 / -1; }
        .package-field label { font-size: 0.9rem; font-weight: 600; color: var(--text-muted); }
        .package-field input, .package-field textarea { width: 100%; padding: 0.75rem 1rem; border-radius: 12px; border: 1.5px solid var(--accent); background: #fff; font-size: 0.95rem; }
        .package-field input:focus { border-color: var(--highlight); outline: none; }
        
        .package-image-box { display: grid; grid-template-columns: 1fr 150px; gap: 1.5rem; align-items: start; }
        .package-file { position: relative; }
        .package-file input { display: none; }
        .package-file label { display: inline-flex; background: var(--secondary); color: #fff; padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer; font-weight: 600; margin-bottom: 0.5rem; }
        .package-file span { display: block; font-size: 0.8rem; color: var(--text-muted); font-style: italic; }
        .package-image { width: 150px; height: 150px; border-radius: 16px; object-fit: cover; border: 2px dashed var(--accent); background: var(--bg-main); }

        .package-menu-selector { background: #fffaf5; border: 1.5px solid var(--accent); border-radius: 20px; padding: 1.5rem; margin-top: 1rem; }
        .package-menu-selector-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; }
        .package-menu-selector-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1rem; }
        
        .package-menu-option { 
            display: flex; 
            align-items: center; 
            gap: 0.75rem; 
            padding: 1rem; 
            background: #fff; 
            border: 1.5px solid var(--accent); 
            border-radius: 16px; 
            cursor: pointer; 
            transition: all 0.2s;
        }
        .package-menu-option:hover { border-color: var(--highlight); transform: translateY(-2px); }
        .package-menu-option.selected { border-color: var(--highlight); background: #fffdfb; box-shadow: 0 4px 12px var(--shadow); }
        
        .package-menu-checkbox { width: 20px; height: 20px; accent-color: var(--highlight); flex-shrink: 0; }
        .package-menu-info { flex: 1; min-width: 0; }
        .package-menu-info strong { display: block; color: var(--primary); font-size: 0.95rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .package-menu-info small { color: var(--text-muted); font-size: 0.8rem; }

        .qty-control { 
            display: flex; 
            align-items: center; 
            gap: 0.5rem; 
            background: #fdfaf8; 
            padding: 0.25rem; 
            border-radius: 10px; 
            border: 1px solid var(--accent); 
            flex-shrink: 0;
            margin-left: auto;
        }
        .qty-btn { width: 28px; height: 28px; border-radius: 6px; border: none; background: #fff; color: var(--primary); cursor: pointer; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.1rem; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .qty-btn:hover { background: var(--highlight); color: #fff; }
        .qty-input { width: 35px; text-align: center; border: none; background: transparent; font-weight: 700; color: var(--primary); -moz-appearance: textfield; }
        .qty-input::-webkit-outer-spin-button, .qty-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        
        .package-actions-row { display: flex; justify-content: flex-end; gap: 1rem; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--accent); }
    </style>
@endpush

<div class="panel">
    <form method="POST" action="{{ $isEdit ? route('superadmin.packages.update', $package) : route('superadmin.packages.store') }}" enctype="multipart/form-data" class="package-form">
        @csrf
        @if ($isEdit)
            @method('PUT')
        @endif

        <div class="package-form-grid">
            <div class="package-field">
                <label for="name">Nama Paket</label>
                <input id="name" type="text" name="name" value="{{ old('name', $package->name) }}" required placeholder="Contoh: Paket Keluarga A">
                @error('name')<small class="form-error">{{ $message }}</small>@enderror
            </div>

            <div class="package-field">
                <label for="selling_price">Harga Jual Paket</label>
                <input id="selling_price" type="number" step="0.01" min="0" name="selling_price" value="{{ old('selling_price', $package->selling_price) }}" required placeholder="0">
                @error('selling_price')<small class="form-error">{{ $message }}</small>@enderror
            </div>

            <div class="package-field full">
                <label for="notes">Catatan (Opsional)</label>
                <textarea id="notes" name="notes" rows="3" placeholder="Contoh: Paket hemat, cocok untuk 2 orang.">{{ old('notes', $package->notes ?? '') }}</textarea>
                @error('notes')<small class="form-error">{{ $message }}</small>@enderror
            </div>

            <div class="package-field full">
                <label for="free_item">Barang Gratis (Opsional)</label>
                <select id="free_item" name="free_item">
                    <option value="">Tidak ada barang gratis</option>
                    @foreach ($menus as $menu)
                        <option value="{{ $menu->name }}" {{ old('free_item', $package->free_item ?? '') == $menu->name ? 'selected' : '' }}>
                            {{ $menu->name }}
                        </option>
                    @endforeach
                </select>
                @error('free_item')<small class="form-error">{{ $message }}</small>@enderror
            </div>

            <div class="package-field full package-image-box">
                <div class="package-file">
                    <label for="image">Pilih Foto Paket</label>
                    <input id="image" data-package-image-input type="file" name="image" accept="image/*">
                    <span data-package-image-name>{{ $package->image_path ? basename($package->image_path) : 'Belum ada file dipilih' }}</span>
                </div>
                <img
                    class="package-image"
                    data-package-image-preview
                    src="{{ $package->image_path ? (Storage::disk('public')->exists($package->image_path) ? Storage::disk('public')->url($package->image_path) : asset('images/menu-placeholder.svg')) : asset('images/menu-placeholder.svg') }}"
                    alt="Preview paket"
                >
            </div>
        </div>

        <div class="package-menu-selector">
            <div class="package-menu-selector-header">
                <div>
                    <h3 style="margin:0;color:var(--primary);font-family:'Playfair Display', Georgia, serif;font-size:1.4rem;">Isi Paket & Jumlah</h3>
                    <p style="color:var(--text-muted);font-size:0.85rem;margin-top:2px;">Pilih menu dan tentukan jumlah per porsinya.</p>
                </div>
                <span data-package-menu-count style="font-weight:700;color:var(--highlight);background:#fff;padding:0.4rem 1rem;border-radius:10px;border:1px solid var(--accent);">{{ count($selectedMenus) }} item terpilih</span>
            </div>

            @error('menus')
                <small class="form-error" style="display:block;margin-bottom:1rem;">{{ $message }}</small>
            @enderror

            <div class="package-menu-selector-grid">
                @foreach ($menus as $menu)
                    @php
                        $isSelected = in_array($menu->id, $selectedMenus);
                        $qty = $menuQuantities[$menu->id] ?? 1;
                    @endphp
                    <div class="package-menu-option {{ $isSelected ? 'selected' : '' }}" data-menu-id="{{ $menu->id }}">
                        <div style="display: flex; align-items: center; gap: 0.75rem; flex: 1; min-width: 0;">
                            <input
                                type="checkbox"
                                name="menus[]"
                                value="{{ $menu->id }}"
                                class="package-menu-checkbox"
                                @checked($isSelected)
                            >
                            <div class="package-menu-info">
                                <strong>{{ $menu->name }}</strong>
                                <small>{{ $menu->code }} - Rp {{ number_format((float) $menu->selling_price, 0, ',', '.') }}</small>
                            </div>
                        </div>
                        
                        <div class="qty-control" style="{{ $isSelected ? '' : 'display:none;' }}">
                            <button type="button" class="qty-btn minus">-</button>
                            <input type="number" name="menu_quantities[{{ $menu->id }}]" value="{{ $qty }}" min="1" class="qty-input">
                            <button type="button" class="qty-btn plus">+</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="package-actions-row">
            <a href="{{ route('superadmin.packages.index') }}" class="secondary-link">Batal</a>
            <button type="submit" class="primary-link">{{ $isEdit ? 'Simpan Perubahan' : 'Buat Paket' }}</button>
        </div>
    </form>
</div>

<script>
    (function() {
        const fileInput = document.querySelector('[data-package-image-input]');
        const fileNameDisplay = document.querySelector('[data-package-image-name]');
        const imagePreview = document.querySelector('[data-package-image-preview]');
        const countDisplay = document.querySelector('[data-package-menu-count]');
        
        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    fileNameDisplay.textContent = file.name;
                    const reader = new FileReader();
                    reader.onload = (event) => imagePreview.src = event.target.result;
                    reader.readAsDataURL(file);
                }
            });
        }

        const updateCount = () => {
            const count = document.querySelectorAll('.package-menu-checkbox:checked').length;
            countDisplay.textContent = count + ' item terpilih';
        };

        document.querySelectorAll('.package-menu-option').forEach(option => {
            const checkbox = option.querySelector('.package-menu-checkbox');
            const qtyControl = option.querySelector('.qty-control');
            const qtyInput = option.querySelector('.qty-input');
            const plusBtn = option.querySelector('.plus');
            const minusBtn = option.querySelector('.minus');

            const toggleSelected = (force) => {
                const isChecked = force !== undefined ? force : checkbox.checked;
                checkbox.checked = isChecked;
                option.classList.toggle('selected', isChecked);
                qtyControl.style.display = isChecked ? 'flex' : 'none';
                updateCount();
            };

            // Click on card (excluding qty control)
            option.addEventListener('click', (e) => {
                if (!e.target.closest('.qty-control') && e.target !== checkbox) {
                    toggleSelected(!checkbox.checked);
                }
            });

            checkbox.addEventListener('change', () => toggleSelected());

            plusBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                qtyInput.value = parseInt(qtyInput.value) + 1;
            });

            minusBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                if (parseInt(qtyInput.value) > 1) {
                    qtyInput.value = parseInt(qtyInput.value) - 1;
                }
            });

            qtyInput.addEventListener('click', e => e.stopPropagation());
        });
    })();
</script>