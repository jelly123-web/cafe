    @forelse ($packages as $package)
        @php
            $packageImage = $package->image_path
                ? (Storage::disk('public')->exists($package->image_path) ? Storage::disk('public')->url($package->image_path) : asset('images/menu-placeholder.svg'))
                : asset('images/menu-placeholder.svg');
        @endphp
        <article class="card menu-card public-package-card"
            data-package-id="{{ $package->id }}"
            data-package-name="{{ $package->name }}"
            data-package-price="{{ (float) ($package->display_price ?? $package->selling_price) }}"
            data-package-original-price="{{ (float) ($package->original_price ?? $package->selling_price) }}"
            data-package-promo-meta='@json($package->promo_meta)'
            data-menu-category-key="{{ $package->menu_category_id ? 'cat-'.$package->menu_category_id : 'all' }}"
        >
            <img src="{{ $packageImage }}" alt="{{ $package->name }}" class="menu-img">
            <div class="menu-info">
                <strong class="menu-title">{{ $package->name }}</strong>
                <div class="menu-category">paket</div>
                @if ($package->has_promo_price ?? false)
                    <div class="promo-price-wrap">
                        <div class="price">Rp {{ number_format((float) ($package->display_price ?? $package->selling_price), 0, ',', '.') }}</div>
                        <div class="price-original">Rp {{ number_format((float) ($package->original_price ?? $package->selling_price), 0, ',', '.') }}</div>
                        @if (!empty($package->promo_label))
                            <span class="price-promo-badge">{{ $package->promo_label }}</span>
                        @endif
                    </div>
                @else
                    <div class="price">Rp {{ number_format((float) $package->selling_price, 0, ',', '.') }}</div>
                @endif
                @if ($package->free_item)
                    <div>
                        <span class="package-free-badge">Free: {{ $package->free_item }}</span>
                    </div>
                @endif
                <div class="package-chips">
                    @forelse ($package->menus as $menu)
                        <span class="package-chip">({{ $menu->pivot->quantity }}x) {{ $menu->name }}</span>
                    @empty
                        <span class="package-chip package-chip-muted">Belum ada menu</span>
                    @endforelse
                </div>
            </div>
            <div class="qty-badge">
                Qty dipilih: <span class="package-qty-selected" data-package-qty-for="{{ $package->id }}">0</span>
            </div>
        </article>
    @empty
    @endforelse
