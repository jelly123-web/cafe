@php
    $formatIdDate = function ($date) {
        if (! $date) {
            return null;
        }

        $monthMap = [
            'January' => 'Januari',
            'February' => 'Februari',
            'March' => 'Maret',
            'April' => 'April',
            'May' => 'Mei',
            'June' => 'Juni',
            'July' => 'Juli',
            'August' => 'Agustus',
            'September' => 'September',
            'October' => 'Oktober',
            'November' => 'November',
            'December' => 'Desember',
        ];

        return strtr($date->format('d F Y'), $monthMap);
    };
@endphp

@if ($promos->isEmpty())
    <div class="promo-empty">Belum ada promo aktif saat ini.</div>
@else
    <div class="promo-grid">
        @foreach ($promos as $promo)
            @php
                $isSpecific = $promo->applies_to === 'specific';
                $periodLabel = $promo->end_at
                    ? 'Berlaku sampai ' . $formatIdDate($promo->end_at)
                    : ($promo->start_at
                        ? 'Mulai ' . $formatIdDate($promo->start_at)
                        : 'Tanpa batas tanggal');

                $promoBadge = match ($promo->type) {
                    'percentage' => 'DISKON ' . number_format((float) $promo->value, 0) . '%',
                    'fixed_discount' => 'DISKON TETAP',
                    'buy_x_get_y' => 'PROMO SPESIAL',
                    default => 'GRATIS ONGKIR',
                };

                $promoSubtext = match ($promo->type) {
                    'percentage' => 'Harga promo lebih hemat untuk item tertentu atau semua pesanan.',
                    'fixed_discount' => 'Potongan harga langsung untuk produk yang dipilih.',
                    'buy_x_get_y' => 'Beli lebih hemat, cocok untuk paket dan menu combo.',
                    default => 'Gratis ongkir untuk semua pesanan sesuai syarat minimum belanja.',
                };

                $featuredItem = $promo->menus->first() ?: $promo->foodPackages->first();
                $featuredType = $promo->menus->first() ? 'menu' : ($promo->foodPackages->first() ? 'package' : null);
                $featuredBasePrice = $featuredItem ? (float) $featuredItem->selling_price : 0;
                $featuredPromoPrice = $featuredBasePrice;

                if ($featuredItem && in_array($promo->type, ['percentage', 'fixed_discount'], true) && (float) $promo->min_spend <= 0) {
                    $featuredPromoPrice = $promo->type === 'percentage'
                        ? max(0, $featuredBasePrice - ($featuredBasePrice * ((float) $promo->value / 100)))
                        : max(0, $featuredBasePrice - (float) $promo->value);
                }

                $featuredImagePath = null;
                if ($featuredItem && ! empty($featuredItem->image_path) && \Illuminate\Support\Facades\Storage::disk('public')->exists($featuredItem->image_path)) {
                    $featuredImagePath = \Illuminate\Support\Facades\Storage::disk('public')->url($featuredItem->image_path);
                } elseif ($promo->banner_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($promo->banner_path)) {
                    $featuredImagePath = \Illuminate\Support\Facades\Storage::disk('public')->url($promo->banner_path);
                } else {
                    $featuredImagePath = asset('images/menu-placeholder.svg');
                }

                $featuredName = $featuredItem?->name ?? $promo->name;
                $featuredMeta = $featuredItem ? [
                    'id' => $promo->id,
                    'name' => $featuredItem->name,
                    'type' => $featuredType,
                    'price' => $featuredPromoPrice,
                    'originalPrice' => $featuredBasePrice,
                    'promoMeta' => [
                        'id' => $promo->id,
                        'name' => $promo->name,
                        'type' => $promo->type,
                        'value' => (float) $promo->value,
                        'buy_qty' => (int) $promo->buy_qty,
                        'get_qty' => (int) $promo->get_qty,
                        'unit_price' => $featuredPromoPrice,
                        'period_label' => $periodLabel,
                    ],
                ] : null;

                $buttonLabel = $isSpecific ? 'Pesan Sekarang' : 'Gunakan Promo';
                $buttonAction = $isSpecific && $featuredItem ? 'open-item' : 'scroll-menu';
            @endphp
            <article class="promo-card {{ $isSpecific ? 'promo-card--specific' : 'promo-card--all' }}">
                <div class="promo-card-inner">
                    <div class="promo-card-media">
                        <img
                            src="{{ $featuredImagePath }}"
                            alt="{{ $featuredName }}"
                            class="promo-card-banner"
                        >
                    </div>

                    <div class="promo-card-copy">
                        <div class="promo-card-head">
                            <span class="promo-card-kicker">{{ $isSpecific ? 'PROMO SPESIAL' : $promoBadge }}</span>
                            <span class="promo-card-status">Aktif</span>
                        </div>

                        <strong class="promo-card-title">{{ $promo->name }}</strong>

                        @if ($promo->description)
                            <p class="promo-card-desc">{{ $promo->description }}</p>
                        @else
                            <p class="promo-card-desc">{{ $promoSubtext }}</p>
                        @endif

                        @if ($featuredItem && in_array($promo->type, ['percentage', 'fixed_discount'], true) && (float) $promo->min_spend <= 0)
                            <div class="promo-featured-box">
                                <div class="promo-featured-title">{{ strtoupper($featuredName) }}</div>
                                <div class="promo-featured-price">
                                    <span class="promo-featured-price-label">Harga Normal</span>
                                    <strong>Rp {{ number_format($featuredBasePrice, 0, ',', '.') }}</strong>
                                </div>
                                <div class="promo-featured-price promo-featured-price--promo">
                                    <span class="promo-featured-price-label">Harga Promo</span>
                                    <strong>Rp {{ number_format($featuredPromoPrice, 0, ',', '.') }}</strong>
                                </div>
                            </div>
                        @elseif ($promo->type === 'buy_x_get_y')
                            <div class="promo-featured-box">
                                <div class="promo-featured-title">{{ strtoupper($featuredName) }}</div>
                                <div class="promo-featured-price">
                                    <span class="promo-featured-price-label">Promo</span>
                                    <strong>Beli {{ $promo->buy_qty }} Gratis {{ $promo->get_qty }}</strong>
                                </div>
                            </div>
                        @else
                            <div class="promo-featured-box">
                                <div class="promo-featured-title">{{ strtoupper($promo->name) }}</div>
                                <div class="promo-featured-price">
                                    <span class="promo-featured-price-label">Promo</span>
                                    <strong>{{ $promoBadge }}</strong>
                                </div>
                            </div>
                        @endif

                        <div class="promo-card-meta">
                            <span>{{ $periodLabel }}</span>
                            @if ($promo->min_spend > 0)
                                <span>Min. belanja Rp {{ number_format((float) $promo->min_spend, 0, ',', '.') }}</span>
                            @endif
                            @if ($isSpecific)
                                <span>{{ $promo->menus->count() }} menu, {{ $promo->foodPackages->count() }} paket</span>
                            @else
                                <span>Berlaku untuk semua pesanan</span>
                            @endif
                        </div>

                        <div class="promo-card-actions">
                            <button
                                type="button"
                                class="promo-action-btn"
                                data-promo-action="{{ $buttonAction }}"
                                @if ($buttonAction === 'open-item' && $featuredMeta)
                                    data-item-type="{{ $featuredMeta['type'] }}"
                                    data-id="{{ $featuredMeta['id'] }}"
                                    data-name="{{ $featuredMeta['name'] }}"
                                    data-price="{{ $featuredMeta['price'] }}"
                                    data-original-price="{{ $featuredMeta['originalPrice'] }}"
                                    data-promo-meta='@json($featuredMeta['promoMeta'])'
                                @endif
                            >
                                {{ $buttonLabel }}
                            </button>

                            @if ($isSpecific && $promo->menus->count() + $promo->foodPackages->count() > 1)
                                <span class="promo-card-hint">Pilihan item tersedia di bawah</span>
                            @endif
                        </div>

                        @if ($isSpecific && ($promo->menus->isNotEmpty() || $promo->foodPackages->isNotEmpty()))
                            <div class="promo-order-group">
                                <span class="promo-order-label">Item promo</span>
                                <div class="promo-order-list">
                                    @foreach ($promo->menus as $menu)
                                        @php
                                            $menuBasePrice = (float) $menu->selling_price;
                                            $menuPromoPrice = $menuBasePrice;
                                            if ($promo->type === 'percentage' && (float) $promo->min_spend <= 0) {
                                                $menuPromoPrice = max(0, $menuBasePrice - ($menuBasePrice * ((float) $promo->value / 100)));
                                            } elseif ($promo->type === 'fixed_discount' && (float) $promo->min_spend <= 0) {
                                                $menuPromoPrice = max(0, $menuBasePrice - (float) $promo->value);
                                            }

                                            $menuPromoMeta = [
                                                'id' => $promo->id,
                                                'name' => $promo->name,
                                                'type' => $promo->type,
                                                'value' => (float) $promo->value,
                                                'buy_qty' => (int) $promo->buy_qty,
                                                'get_qty' => (int) $promo->get_qty,
                                                'unit_price' => $menuPromoPrice,
                                                'period_label' => $periodLabel,
                                            ];
                                        @endphp
                                        <button
                                            type="button"
                                            class="promo-order-chip"
                                            data-promo-order="menu"
                                            data-id="{{ $menu->id }}"
                                            data-name="{{ $menu->name }}"
                                            data-price="{{ $menuPromoPrice }}"
                                            data-original-price="{{ $menuBasePrice }}"
                                            data-promo-meta='@json($menuPromoMeta)'
                                        >
                                            {{ $menu->name }}
                                        </button>
                                    @endforeach

                                    @foreach ($promo->foodPackages as $package)
                                        @php
                                            $packageBasePrice = (float) $package->selling_price;
                                            $packagePromoPrice = $packageBasePrice;
                                            if ($promo->type === 'percentage' && (float) $promo->min_spend <= 0) {
                                                $packagePromoPrice = max(0, $packageBasePrice - ($packageBasePrice * ((float) $promo->value / 100)));
                                            } elseif ($promo->type === 'fixed_discount' && (float) $promo->min_spend <= 0) {
                                                $packagePromoPrice = max(0, $packageBasePrice - (float) $promo->value);
                                            }

                                            $packagePromoMeta = [
                                                'id' => $promo->id,
                                                'name' => $promo->name,
                                                'type' => $promo->type,
                                                'value' => (float) $promo->value,
                                                'buy_qty' => (int) $promo->buy_qty,
                                                'get_qty' => (int) $promo->get_qty,
                                                'unit_price' => $packagePromoPrice,
                                                'period_label' => $periodLabel,
                                            ];
                                        @endphp
                                        <button
                                            type="button"
                                            class="promo-order-chip"
                                            data-promo-order="package"
                                            data-id="{{ $package->id }}"
                                            data-name="{{ $package->name }}"
                                            data-price="{{ $packagePromoPrice }}"
                                            data-original-price="{{ $packageBasePrice }}"
                                            data-promo-meta='@json($packagePromoMeta)'
                                        >
                                            {{ $package->name }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </article>
        @endforeach
    </div>
@endif
