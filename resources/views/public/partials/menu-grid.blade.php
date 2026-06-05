@forelse ($menus as $menu)
    @php
        $menuImage = $menu->image_path
            ? (Storage::disk('public')->exists($menu->image_path) ? Storage::disk('public')->url($menu->image_path) : asset('images/menu-placeholder.svg'))
            : asset('images/menu-placeholder.svg');
    @endphp
    <article class="card menu-card"
        data-menu-id="{{ $menu->id }}"
        data-menu-name="{{ $menu->name }}"
        data-menu-category="{{ $menu->category?->name ?? 'Tanpa kategori' }}"
        data-menu-category-key="{{ str_contains(strtolower($menu->category?->name ?? ''), 'minum') || str_contains(strtolower($menu->category?->name ?? ''), 'teh') || str_contains(strtolower($menu->category?->name ?? ''), 'kopi') ? 'minuman' : 'makanan' }}"
        data-menu-price="{{ (float) ($menu->display_price ?? $menu->selling_price) }}"
        data-menu-original-price="{{ (float) ($menu->original_price ?? $menu->selling_price) }}"
        data-menu-promo-meta='@json($menu->promo_meta)'
    >
        <img src="{{ $menuImage }}" alt="{{ $menu->name }}" class="menu-img">
        <div class="menu-info">
            <strong class="menu-title">{{ $menu->name }}</strong>
            <div class="menu-category">{{ $menu->category?->name ?? 'Tanpa kategori' }}</div>
            @if ($menu->has_promo_price ?? false)
                <div class="promo-price-wrap">
                    <div class="price">Rp {{ number_format((float) ($menu->display_price ?? $menu->selling_price), 0, ',', '.') }}</div>
                    <div class="price-original">Rp {{ number_format((float) ($menu->original_price ?? $menu->selling_price), 0, ',', '.') }}</div>
                    @if (!empty($menu->promo_label))
                        <span class="price-promo-badge">{{ $menu->promo_label }}</span>
                    @endif
                </div>
            @else
                <div class="price">Rp {{ number_format((float) $menu->selling_price, 0, ',', '.') }}</div>
            @endif
            <div class="qty-badge">Qty dipilih: <span class="qty-selected" data-qty-for="{{ $menu->id }}">0</span></div>
        </div>
    </article>
@empty
    <div class="empty">Belum ada menu.</div>
@endforelse
