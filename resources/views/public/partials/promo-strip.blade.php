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
    <div class="promo-grid" id="promoScroll">
        @foreach ($promos as $promo)
            @php
                $periodLabel = $promo->starts_at && $promo->ends_at
                    ? $promo->starts_at->format('d/m/y') . ' s/d ' . $promo->ends_at->format('d/m/y')
                    : ($promo->ends_at
                        ? 'Sampai ' . $promo->ends_at->format('d/m/y')
                        : ($promo->starts_at ? 'Mulai ' . $promo->starts_at->format('d/m/y') : 'Promo aktif'));

                $promoBadge = match ($promo->type) {
                    'percentage' => 'Diskon',
                    'fixed_discount' => 'Diskon',
                    'buy_x_get_y' => 'B1G1',
                    default => 'Cashback',
                };

                $promoSubtext = match ($promo->type) {
                    'percentage' => 'Potongan ' . rtrim(rtrim(number_format((float) $promo->discount_value, 2, ',', '.'), '0'), ',') . '%',
                    'fixed_discount' => 'Potongan Rp ' . number_format((float) $promo->discount_value, 0, ',', '.'),
                    'buy_x_get_y' => 'Beli ' . ($promo->buy_qty ?? 1) . ' Gratis ' . ($promo->get_qty ?? 1),
                    default => $promo->description ?: 'Promo spesial hari ini',
                };

                $badgeClass = match ($promo->type) {
                    'buy_x_get_y' => 'bogofree',
                    'percentage', 'fixed_discount' => 'diskon',
                    default => 'cashback',
                };

                $featuredItem = $promo->menus->first() ?: $promo->foodPackages->first();
                $featuredName = $featuredItem?->name ?? $promo->name;
                $featuredType = $promo->menus->first() ? 'menu' : ($promo->foodPackages->first() ? 'package' : null);
                $featuredPrice = (float) ($featuredItem?->selling_price ?? 0);
                $promoPrice = null;
                $priceLabel = null;

                if ($featuredItem && in_array($promo->type, ['percentage', 'fixed_discount'], true)) {
                    $promoPrice = $promo->type === 'percentage'
                        ? max(0, $featuredPrice - ($featuredPrice * ((float) $promo->value / 100)))
                        : max(0, $featuredPrice - (float) $promo->value);

                    $priceLabel = 'Rp ' . number_format($promoPrice, 0, ',', '.');
                } elseif ($featuredItem && $promo->type === 'buy_x_get_y') {
                    $priceLabel = 'Beli ' . ((int) ($promo->buy_qty ?? 1)) . ' Gratis ' . ((int) ($promo->get_qty ?? 1));
                }

                if (! empty($promo->banner_path) && \Illuminate\Support\Facades\Storage::disk('public')->exists($promo->banner_path)) {
                    $featuredImagePath = \Illuminate\Support\Facades\Storage::disk('public')->url($promo->banner_path);
                } elseif (! empty($promo->banner_path)) {
                    $featuredImagePath = asset('storage/' . ltrim($promo->banner_path, '/'));
                } elseif ($featuredItem && ! empty($featuredItem->image_path) && \Illuminate\Support\Facades\Storage::disk('public')->exists($featuredItem->image_path)) {
                    $featuredImagePath = asset('storage/' . $featuredItem->image_path);
                } elseif ($featuredItem && ! empty($featuredItem->image_path)) {
                    $featuredImagePath = $featuredItem->image_path;
                } else {
                    $featuredImagePath = asset('images/menu-placeholder.svg');
                }

                $promoMetaJson = json_encode([
                    'id' => $promo->id,
                    'name' => $promo->name,
                    'type' => $promo->type,
                    'value' => (float) $promo->value,
                    'buy_qty' => (int) ($promo->buy_qty ?? 0),
                    'get_qty' => (int) ($promo->get_qty ?? 0),
                    'unit_price' => $promoPrice,
                    'period_label' => $periodLabel,
                ], JSON_HEX_APOS | JSON_HEX_QUOT);
            @endphp
            <article
                class="promo-card"
                @if($featuredItem && $featuredType)
                    data-promo-action="quick-add"
                    data-item-type="{{ $featuredType }}"
                    data-id="{{ $featuredItem->id }}"
                    data-name="{{ $featuredItem->name }}"
                    data-price="{{ $promoPrice ?? $featuredPrice }}"
                    data-original-price="{{ $featuredPrice }}"
                    data-promo-meta="{{ $promoMetaJson }}"
                    style="cursor:pointer"
                @endif
            >
                <img class="promo-img" src="{{ $featuredImagePath }}" alt="{{ $featuredName }}" loading="lazy">
                <div class="promo-body">
                    <span class="promo-badge {{ $badgeClass }}">{{ $promoBadge }}</span>
                    <div class="promo-name">{{ $promo->name }}</div>
                    <div class="promo-detail">{{ $promo->description ?: $promoSubtext }}</div>
                    @if ($priceLabel)
                        <div class="promo-detail" style="margin-top:4px;">
                            @if($promoPrice !== null)
                                <strong style="color:var(--accent);">{{ $priceLabel }}</strong>
                                @if($featuredPrice > ($promoPrice ?? 0))
                                    <span style="margin-left:6px;color:var(--muted);text-decoration:line-through;">Rp {{ number_format($featuredPrice, 0, ',', '.') }}</span>
                                @endif
                            @else
                                <strong style="color:var(--accent);">{{ $priceLabel }}</strong>
                            @endif
                        </div>
                    @endif
                    <div class="promo-expire"><i class="far fa-clock"></i> {{ $periodLabel }}</div>
                </div>
            </article>
        @endforeach
    </div>
@endif
