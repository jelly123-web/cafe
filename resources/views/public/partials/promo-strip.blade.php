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
                $periodLabel = $promo->ends_at
                    ? 'Berlaku sampai ' . $formatIdDate($promo->ends_at)
                    : ($promo->starts_at ? 'Mulai ' . $formatIdDate($promo->starts_at) : 'Promo aktif');

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

                if ($featuredItem && ! empty($featuredItem->image_path) && \Illuminate\Support\Facades\Storage::disk('public')->exists($featuredItem->image_path)) {
                    $featuredImagePath = asset('storage/' . $featuredItem->image_path);
                } elseif ($featuredItem && ! empty($featuredItem->image_path)) {
                    $featuredImagePath = $featuredItem->image_path;
                } else {
                    $featuredImagePath = 'https://picsum.photos/seed/promo-' . $promo->id . '/400/200.jpg';
                }
            @endphp
            <article class="promo-card">
                <img class="promo-img" src="{{ $featuredImagePath }}" alt="{{ $featuredName }}" loading="lazy">
                <div class="promo-body">
                    <span class="promo-badge {{ $badgeClass }}">{{ $promoBadge }}</span>
                    <div class="promo-name">{{ $promo->name }}</div>
                    <div class="promo-detail">{{ $promo->description ?: $promoSubtext }}</div>
                    <div class="promo-expire"><i class="far fa-clock"></i> {{ $periodLabel }}</div>
                </div>
            </article>
        @endforeach
    </div>
@endif
