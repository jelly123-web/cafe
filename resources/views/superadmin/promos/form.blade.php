@php
    $catalogItems = collect($menus)->map(fn ($menu) => [
        'kind' => 'menu',
        'id' => $menu->id,
        'name' => $menu->name,
        'meta' => $menu->code ?: 'Menu',
    ])->merge(
        collect($packages)->map(fn ($package) => [
            'kind' => 'package',
            'id' => $package->id,
            'name' => $package->name,
            'meta' => 'Paket',
        ])
    )->values();

    $oldBuyTargets = collect(old('buy_targets', $promo->buy_targets ?? []))
        ->filter(fn ($item) => is_array($item))
        ->mapWithKeys(fn ($item) => [($item['kind'] ?? 'menu') . '-' . ($item['id'] ?? 0) => (int) ($item['qty'] ?? 0)]);

    $oldGetTargets = collect(old('get_targets', $promo->get_targets ?? []))
        ->filter(fn ($item) => is_array($item))
        ->mapWithKeys(fn ($item) => [($item['kind'] ?? 'menu') . '-' . ($item['id'] ?? 0) => (int) ($item['qty'] ?? 0)]);
@endphp

<div class="drawer-form-grid">
    <div class="drawer-field full">
        <label for="f_name">Nama Promo</label>
        <input id="f_name" type="text" name="name" value="{{ old('name', $promo->name) }}" required placeholder="Cth: Promo">
    </div>

    <div class="drawer-field full">
        <label for="f_description">Deskripsi</label>
        <textarea id="f_description" name="description" rows="3" placeholder="Jelaskan detail promo...">{{ old('description', $promo->description) }}</textarea>
    </div>

    <div class="drawer-field">
        <label for="f_type">Tipe Promo</label>
        <select id="f_type" name="type" required onchange="togglePromoFields(this.value)">
            <option value="percentage" {{ old('type', $promo->type) === 'percentage' ? 'selected' : '' }}>Diskon Persentase (%)</option>
            <option value="fixed_discount" {{ old('type', $promo->type) === 'fixed_discount' ? 'selected' : '' }}>Potongan Harga (Rp)</option>
            <option value="buy_x_get_y" {{ old('type', $promo->type) === 'buy_x_get_y' ? 'selected' : '' }}>Beli X Gratis Y</option>
        </select>
    </div>

    <div class="drawer-field" id="field_value">
        <label for="f_value">Nilai Promo</label>
        <input id="f_value" type="number" step="0.01" name="value" value="{{ old('value', $promo->value) }}" placeholder="Cth: 10 atau 5000">
    </div>

    <div class="drawer-field" id="field_min">
        <label for="f_min">Minimal Belanja (Opsional)</label>
        <input id="f_min" type="number" step="0.01" name="min_spend" value="{{ old('min_spend', $promo->min_spend) }}" placeholder="Cth: 50000">
    </div>

    <div class="drawer-field full" id="field_percentage_preview" style="display:none;">
        <label>Preview Diskon Persentase</label>
        <div class="promo-preview-grid">
            <div class="drawer-field">
                <label for="f_before_price">Harga Sebelum Diskon</label>
                <input id="f_before_price" type="number" step="0.01" min="0" placeholder="Cth: 50000">
                <small class="promo-preview-note">Harga acuan untuk melihat hasil diskon.</small>
            </div>
            <div class="drawer-field">
                <label for="f_after_price">Harga Setelah Diskon</label>
                <input id="f_after_price" type="text" readonly placeholder="Otomatis dihitung">
                <small class="promo-preview-note">Nilai ini hanya preview, tidak disimpan ke database.</small>
            </div>
        </div>
    </div>

    <div class="drawer-field">
        <label for="f_applies">Berlaku Untuk</label>
        @php $selectedAppliesTo = old('applies_to', $promo->applies_to ?: 'all'); @endphp
        <select id="f_applies" name="applies_to" required onchange="toggleScopeFields(this.value)">
            <option value="all" {{ $selectedAppliesTo === 'all' ? 'selected' : '' }}>Semua Produk</option>
            <option value="specific" {{ $selectedAppliesTo === 'specific' ? 'selected' : '' }}>Produk Tertentu</option>
        </select>
    </div>

    <div class="drawer-field">
        <label for="f_status">Status</label>
        <select id="f_status" name="is_active">
            <option value="1" {{ old('is_active', $promo->is_active) !== false ? 'selected' : '' }}>Aktif</option>
            <option value="0" {{ old('is_active', $promo->is_active) === false ? 'selected' : '' }}>Nonaktif</option>
        </select>
    </div>

    <div class="drawer-field full" id="field_scope" style="display:none;">
        <label>Pilih Menu/Paket Tertentu</label>
        <small id="scopeSelectionMeta" class="scope-help" style="margin-top:0;">0 item dipilih</small>
        <div class="package-menu-selector-grid">
            @php 
                $selectedMenus = old('menu_ids', $promo->menus->pluck('id')->all());
                $menuQuantities = old('menu_quantities', $promo->menus->mapWithKeys(fn($m) => [$m->id => $m->pivot->quantity ?? 1])->all());
                $selectedPackages = old('package_ids', $promo->foodPackages->pluck('id')->all());
                $packageQuantities = old('package_quantities', $promo->foodPackages->mapWithKeys(fn($p) => [$p->id => $p->pivot->quantity ?? 1])->all());
            @endphp
            @foreach ($menus as $menu)
                @php 
                    $isSelected = in_array($menu->id, $selectedMenus); 
                    $qty = $menuQuantities[$menu->id] ?? 1;
                @endphp
                <div class="package-menu-option {{ $isSelected ? 'selected' : '' }}" data-menu-id="{{ $menu->id }}">
                    <div class="package-menu-row">
                        <input type="checkbox" name="menu_ids[]" value="{{ $menu->id }}" class="package-menu-checkbox" @checked($isSelected)>
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
            @foreach ($packages as $package)
                @php 
                    $isSelected = in_array($package->id, $selectedPackages); 
                    $qty = $packageQuantities[$package->id] ?? 1;
                @endphp
                <div class="package-menu-option {{ $isSelected ? 'selected' : '' }}" data-menu-id="{{ $package->id }}">
                    <div class="package-menu-row">
                        <input type="checkbox" name="package_ids[]" value="{{ $package->id }}" class="package-menu-checkbox" @checked($isSelected)>
                        <div class="package-menu-info">
                            <strong>{{ $package->name }}</strong>
                            <small>Paket</small>
                        </div>
                    </div>
                    <div class="qty-control" style="{{ $isSelected ? '' : 'display:none;' }}">
                        <button type="button" class="qty-btn minus">-</button>
                        <input type="number" name="package_quantities[{{ $package->id }}]" value="{{ $qty }}" min="1" class="qty-input">
                        <button type="button" class="qty-btn plus">+</button>
                    </div>
                </div>
            @endforeach
        </div>
        <small class="scope-help">Pesanan yang mengandung salah satu menu atau paket terpilih akan mendapatkan promo ini.</small>
    </div>

    <div class="drawer-field full" id="field_buyxgety" style="display:none;">
        <label>Atur Item Beli X dan Gratis Y</label>
        <div class="bxgy-builder">
            <div class="bxgy-column">
                <div class="bxgy-head">
                    <strong>Item yang harus dibeli</strong>
                    <span id="buyTargetSummary">0 item</span>
                </div>
                <div class="bxgy-list">
                    @foreach ($catalogItems as $index => $item)
                        @php
                            $itemKey = $item['kind'] . '-' . $item['id'];
                            $qty = $oldBuyTargets->get($itemKey, 0);
                        @endphp
                        <div class="bxgy-item" data-target-group="buy">
                            <div class="bxgy-item-info">
                                <span class="bxgy-item-name">{{ $item['name'] }}</span>
                                <span class="bxgy-item-meta">{{ $item['meta'] }}</span>
                            </div>
                            <div class="bxgy-counter">
                                <button type="button" class="bxgy-btn" data-counter-action="decrease">-</button>
                                <input type="number" class="bxgy-qty" value="{{ $qty }}" min="0" readonly>
                                <button type="button" class="bxgy-btn" data-counter-action="increase">+</button>
                            </div>
                            <input type="hidden" name="buy_targets[{{ $index }}][kind]" value="{{ $item['kind'] }}">
                            <input type="hidden" name="buy_targets[{{ $index }}][id]" value="{{ $item['id'] }}">
                            <input type="hidden" name="buy_targets[{{ $index }}][qty]" value="{{ $qty }}" class="bxgy-hidden-qty">
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="bxgy-column">
                <div class="bxgy-head">
                    <strong>Item gratis yang didapat</strong>
                    <span id="getTargetSummary">0 item</span>
                </div>
                <div class="bxgy-list">
                    @foreach ($catalogItems as $index => $item)
                        @php
                            $itemKey = $item['kind'] . '-' . $item['id'];
                            $qty = $oldGetTargets->get($itemKey, 0);
                        @endphp
                        <div class="bxgy-item" data-target-group="get">
                            <div class="bxgy-item-info">
                                <span class="bxgy-item-name">{{ $item['name'] }}</span>
                                <span class="bxgy-item-meta">{{ $item['meta'] }}</span>
                            </div>
                            <div class="bxgy-counter">
                                <button type="button" class="bxgy-btn" data-counter-action="decrease">-</button>
                                <input type="number" class="bxgy-qty" value="{{ $qty }}" min="0" readonly>
                                <button type="button" class="bxgy-btn" data-counter-action="increase">+</button>
                            </div>
                            <input type="hidden" name="get_targets[{{ $index }}][kind]" value="{{ $item['kind'] }}">
                            <input type="hidden" name="get_targets[{{ $index }}][id]" value="{{ $item['id'] }}">
                            <input type="hidden" name="get_targets[{{ $index }}][qty]" value="{{ $qty }}" class="bxgy-hidden-qty">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <small class="scope-help">Contoh: set `ayam` = 2 di kolom beli, lalu set `cocacola` = 1 di kolom gratis.</small>
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
    (function() {
        window.togglePromoFields = function(type) {
            const fieldVal = document.getElementById('field_value');
            const fieldMin = document.getElementById('field_min');
            const fieldScope = document.getElementById('field_scope');
            const fieldBxgy = document.getElementById('field_buyxgety');
            const fieldPercentagePreview = document.getElementById('field_percentage_preview');
            const applies = document.getElementById('f_applies');
            const valueLabel = document.querySelector('label[for="f_value"]');
            const valueInput = document.getElementById('f_value');

            const isBuyXGetY = type === 'buy_x_get_y';
            const isPercentage = type === 'percentage';
            const isFixedDiscount = type === 'fixed_discount';

            if (fieldVal) fieldVal.style.display = isBuyXGetY ? 'none' : 'block';
            if (fieldMin) fieldMin.style.display = (isBuyXGetY || isPercentage || isFixedDiscount) ? 'none' : 'block';
            if (fieldBxgy) fieldBxgy.style.display = isBuyXGetY ? 'block' : 'none';
            if (fieldPercentagePreview) fieldPercentagePreview.style.display = isPercentage ? 'block' : 'none';

            if (valueLabel) {
                valueLabel.textContent = isPercentage
                    ? 'Persentase Diskon (%)'
                    : (isFixedDiscount ? 'Nominal Potongan (Rp)' : 'Nilai Promo');
            }

            if (valueInput) {
                valueInput.placeholder = isPercentage
                    ? 'Cth: 10'
                    : (isFixedDiscount ? 'Cth: 5000' : 'Cth: 10 atau 5000');
            }

            if (isBuyXGetY) {
                if (applies) applies.value = 'specific';
                if (fieldScope) fieldScope.style.display = 'none';
                window.toggleScopeFields?.('specific', true);
            } else {
                const appliesTo = applies?.value || 'all';
                window.toggleScopeFields?.(appliesTo, false);
            }
        };

        window.toggleScopeFields = function(appliesTo, forceHidden = false) {
            const fieldScope = document.getElementById('field_scope');
            const type = document.getElementById('f_type')?.value;
            if (!fieldScope) return;

            const isSpecific = appliesTo === 'specific';
            const shouldShow = !forceHidden && type !== 'buy_x_get_y' && isSpecific;
            fieldScope.style.display = shouldShow ? 'block' : 'none';
            fieldScope.querySelectorAll('input[type="checkbox"]').forEach((checkbox) => {
                checkbox.disabled = !shouldShow;
            });
            window.updateScopeSelectionMeta?.();
        };

        window.updateScopeSelectionMeta = function() {
            const meta = document.getElementById('scopeSelectionMeta');
            const fieldScope = document.getElementById('field_scope');
            if (!meta || !fieldScope) return;

            const checkedCount = fieldScope.querySelectorAll('input[type="checkbox"]:checked').length;
            meta.textContent = checkedCount > 0
                ? `${checkedCount} item dipilih`
                : 'Belum ada menu atau paket dipilih';
        };

        const formatPromoMoney = (value) => {
            return 'Rp ' + new Intl.NumberFormat('id-ID').format(Number(value || 0));
        };

        const updatePercentagePreview = () => {
            const type = document.getElementById('f_type')?.value;
            const beforeInput = document.getElementById('f_before_price');
            const valueInput = document.getElementById('f_value');
            const afterInput = document.getElementById('f_after_price');
            if (!beforeInput || !valueInput || !afterInput) return;

            if (type !== 'percentage') {
                afterInput.value = '';
                return;
            }

            const before = Number(beforeInput.value || 0);
            const percent = Number(valueInput.value || 0);
            const safePercent = Math.max(0, Math.min(100, percent));
            const after = Math.max(0, before - (before * safePercent / 100));
            afterInput.value = before > 0 ? formatPromoMoney(after) : '';
        };

        const syncScopeRowState = (checkbox) => {
            const option = checkbox.closest('.package-menu-option');
            const qtyControl = option?.querySelector('.qty-control');
            const qtyInput = option?.querySelector('.qty-input');
            if (!option || !qtyControl || !qtyInput) return;

            option.classList.toggle('selected', checkbox.checked);
            qtyControl.style.display = checkbox.checked ? 'inline-flex' : 'none';

            if (!checkbox.checked) {
                qtyInput.value = '1';
            } else if (Number(qtyInput.value || 0) < 1) {
                qtyInput.value = '1';
            }
        };

        window.syncBxgySummary = function() {
            const updateSummary = (group, targetId) => {
                const summary = document.getElementById(targetId);
                if (!summary) return;

                const items = Array.from(document.querySelectorAll(`.bxgy-item[data-target-group="${group}"] .bxgy-hidden-qty`))
                    .map((input) => Number(input.value || 0))
                    .filter((qty) => qty > 0);

                const total = items.reduce((sum, qty) => sum + qty, 0);
                summary.textContent = total > 0 ? `${total} qty dipilih` : '0 item';
            };

            updateSummary('buy', 'buyTargetSummary');
            updateSummary('get', 'getTargetSummary');
        };

        document.querySelectorAll('.bxgy-item').forEach((item) => {
            const qtyInput = item.querySelector('.bxgy-qty');
            const hiddenQty = item.querySelector('.bxgy-hidden-qty');
            if (!qtyInput || !hiddenQty) return;

            item.querySelectorAll('.bxgy-btn').forEach((button) => {
                button.addEventListener('click', () => {
                    const action = button.dataset.counterAction;
                    const current = Number(hiddenQty.value || 0);
                    const next = action === 'increase' ? current + 1 : Math.max(0, current - 1);
                    hiddenQty.value = String(next);
                    qtyInput.value = String(next);
                    window.syncBxgySummary?.();
                });
            });
        });

        document.querySelectorAll('.package-menu-option').forEach((option) => {
            const checkbox = option.querySelector('.package-menu-checkbox');
            const qtyInput = option.querySelector('.qty-input');
            const minusBtn = option.querySelector('.qty-btn.minus');
            const plusBtn = option.querySelector('.qty-btn.plus');

            if (!checkbox || !qtyInput || !minusBtn || !plusBtn) return;

            syncScopeRowState(checkbox);

            checkbox.addEventListener('change', () => {
                syncScopeRowState(checkbox);
                window.updateScopeSelectionMeta?.();
            });

            minusBtn.addEventListener('click', () => {
                if (!checkbox.checked) return;
                const next = Math.max(1, Number(qtyInput.value || 1) - 1);
                qtyInput.value = String(next);
            });

            plusBtn.addEventListener('click', () => {
                if (!checkbox.checked) return;
                const next = Math.max(1, Number(qtyInput.value || 1) + 1);
                qtyInput.value = String(next);
            });

            qtyInput.addEventListener('input', () => {
                const next = Math.max(1, Number(qtyInput.value || 1));
                qtyInput.value = String(next);
            });
        });

        const typeEl = document.getElementById('f_type');
        const appliesEl = document.getElementById('f_applies');
        if (typeEl) window.togglePromoFields(typeEl.value);
        if (appliesEl) window.toggleScopeFields(appliesEl.value);
        document.getElementById('f_before_price')?.addEventListener('input', updatePercentagePreview);
        document.getElementById('f_value')?.addEventListener('input', updatePercentagePreview);

        document.querySelectorAll('#field_scope input[type="checkbox"]').forEach((checkbox) => {
            checkbox.addEventListener('change', window.updateScopeSelectionMeta);
        });

        window.updateScopeSelectionMeta();
        window.syncBxgySummary();
        updatePercentagePreview();
    })();
</script>
