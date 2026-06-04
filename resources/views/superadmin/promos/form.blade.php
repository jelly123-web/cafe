<div class="drawer-form-grid">
    <div class="drawer-field full">
        <label for="f_name">Nama Promo</label>
        <input id="f_name" type="text" name="name" value="{{ old('name', $promo->name) }}" required placeholder="Cth: Promo Ramadhan">
    </div>

    <div class="drawer-field full">
        <label for="f_description">Deskripsi</label>
        <textarea id="f_description" name="description" style="width:100%;padding:0.75rem;border:1.5px solid var(--accent);border-radius:14px;font-family:inherit;">{{ old('description', $promo->description) }}</textarea>
    </div>

    <div class="drawer-field">
        <label for="f_type">Tipe Promo</label>
        <select id="f_type" name="type" required onchange="togglePromoFields(this.value)">
            <option value="percentage" {{ old('type', $promo->type) === 'percentage' ? 'selected' : '' }}>Diskon Persentase (%)</option>
            <option value="fixed_discount" {{ old('type', $promo->type) === 'fixed_discount' ? 'selected' : '' }}>Potongan Harga Tetap (Rp)</option>
            <option value="buy_x_get_y" {{ old('type', $promo->type) === 'buy_x_get_y' ? 'selected' : '' }}>Beli X Gratis Y</option>
            <option value="free_shipping" {{ old('type', $promo->type) === 'free_shipping' ? 'selected' : '' }}>Gratis Ongkir</option>
        </select>
    </div>

    <div class="drawer-field" id="field_value">
        <label for="f_value">Nilai Promo</label>
        <input id="f_value" type="number" step="0.01" name="value" value="{{ old('value', $promo->value) }}" placeholder="Cth: 10 atau 5000">
    </div>

    <div class="drawer-field">
        <label for="f_min">Minimal Belanja (Opsional)</label>
        <input id="f_min" type="number" step="0.01" name="min_spend" value="{{ old('min_spend', $promo->min_spend) }}" placeholder="Cth: 50000">
    </div>

    <div class="drawer-field" id="field_buy" style="display:none;">
        <label for="f_buy">Jumlah Beli (X)</label>
        <input id="f_buy" type="number" name="buy_qty" min="0" value="{{ old('buy_qty', $promo->buy_qty) }}" placeholder="Cth: 2">
    </div>

    <div class="drawer-field" id="field_get" style="display:none;">
        <label for="f_get">Jumlah Gratis (Y)</label>
        <input id="f_get" type="number" name="get_qty" min="0" value="{{ old('get_qty', $promo->get_qty) }}" placeholder="Cth: 1">
    </div>

    <div class="drawer-field">
        <label for="f_applies">Berlaku Untuk</label>
        <select id="f_applies" name="applies_to" required onchange="toggleScopeFields(this.value)">
            @php $selectedAppliesTo = old('applies_to', $promo->applies_to ?: 'all'); @endphp
            <option value="all" {{ $selectedAppliesTo === 'all' ? 'selected' : '' }}>Semua Produk</option>
            <option value="specific" {{ $selectedAppliesTo === 'specific' ? 'selected' : '' }}>Produk Tertentu</option>
        </select>
    </div>

    <div class="drawer-field">
        <label for="f_status">Status</label>
        <select id="f_status" name="is_active">
            <option value="1" {{ old('is_active', $promo->is_active) ? 'selected' : '' }}>Aktif</option>
            <option value="0" {{ old('is_active', $promo->is_active) === false ? 'selected' : '' }}>Nonaktif</option>
        </select>
    </div>

    <div class="drawer-field full" id="field_scope" style="display:none;">
        <label>Pilih Menu/Paket Tertentu</label>
        <small id="scopeSelectionMeta" class="scope-help" style="margin-top:0;">0 item dipilih</small>
        <div class="scope-picker">
            <div class="scope-column">
                <strong class="scope-column-title">MENU</strong>
                <div class="scope-list">
                    @php $selectedMenus = old('menu_ids', $promo->menus->pluck('id')->all()); @endphp
                    @foreach ($menus as $menu)
                        <label class="scope-item">
                            <input type="checkbox" name="menu_ids[]" value="{{ $menu->id }}" {{ in_array($menu->id, $selectedMenus) ? 'checked' : '' }}>
                            <span class="scope-item-body">
                                <span class="scope-item-name">{{ $menu->name }}</span>
                                <span class="scope-item-meta">{{ $menu->code ?? 'Tanpa kode' }}</span>
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="scope-column">
                <strong class="scope-column-title">PAKET</strong>
                <div class="scope-list">
                    @php $selectedPackages = old('package_ids', $promo->foodPackages->pluck('id')->all()); @endphp
                    @foreach ($packages as $package)
                        <label class="scope-item">
                            <input type="checkbox" name="package_ids[]" value="{{ $package->id }}" {{ in_array($package->id, $selectedPackages) ? 'checked' : '' }}>
                            <span class="scope-item-body">
                                <span class="scope-item-name">{{ $package->name }}</span>
                                <span class="scope-item-meta">Paket promo</span>
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>
        <small class="scope-help">Pesanan yang mengandung salah satu menu atau paket terpilih akan mendapatkan promo ini.</small>
    </div>

    <div class="drawer-field">
        <label for="f_start">Tanggal Mulai</label>
        <input id="f_start" type="date" name="start_at" value="{{ old('start_at', $promo->start_at?->format('Y-m-d')) }}">
    </div>

    <div class="drawer-field">
        <label for="f_end">Tanggal Selesai</label>
        <input id="f_end" type="date" name="end_at" value="{{ old('end_at', $promo->end_at?->format('Y-m-d')) }}">
    </div>

    <div class="drawer-field full">
        <label for="f_banner">Banner Promo</label>
        <input id="f_banner" type="file" name="banner" accept="image/*">
        @if ($promo->banner_path)
            <small style="display:block;margin-top:0.5rem;color:var(--text-muted)">File saat ini: {{ basename($promo->banner_path) }}</small>
        @endif
    </div>
</div>

<script>
    function togglePromoFields(type) {
        const fieldVal = document.getElementById('field_value');
        const fieldBuy = document.getElementById('field_buy');
        const fieldGet = document.getElementById('field_get');

        if (type === 'buy_x_get_y') {
            fieldVal.style.display = 'none';
            fieldBuy.style.display = 'block';
            fieldGet.style.display = 'block';
        } else if (type === 'free_shipping') {
            fieldVal.style.display = 'none';
            fieldBuy.style.display = 'none';
            fieldGet.style.display = 'none';
        } else {
            fieldVal.style.display = 'block';
            fieldBuy.style.display = 'none';
            fieldGet.style.display = 'none';
        }
    }

    function toggleScopeFields(appliesTo) {
        const fieldScope = document.getElementById('field_scope');
        const isSpecific = appliesTo === 'specific';
        fieldScope.style.display = isSpecific ? 'block' : 'none';
        fieldScope.querySelectorAll('input[type="checkbox"]').forEach((checkbox) => {
            checkbox.disabled = !isSpecific;
        });
        updateScopeSelectionMeta();
    }

    function updateScopeSelectionMeta() {
        const meta = document.getElementById('scopeSelectionMeta');
        const fieldScope = document.getElementById('field_scope');
        if (!meta || !fieldScope) return;

        const checkedCount = fieldScope.querySelectorAll('input[type="checkbox"]:checked').length;
        meta.textContent = checkedCount > 0
            ? `${checkedCount} item dipilih`
            : 'Belum ada menu atau paket dipilih';
    }

    // Initialize on load
    document.addEventListener('DOMContentLoaded', function() {
        togglePromoFields(document.getElementById('f_type').value);
        toggleScopeFields(document.getElementById('f_applies').value);
        document.querySelectorAll('#field_scope input[type="checkbox"]').forEach((checkbox) => {
            checkbox.addEventListener('change', updateScopeSelectionMeta);
        });
        updateScopeSelectionMeta();
    });
</script>
