@php
    $categoryFilter = function (?string $name): string {
        $key = strtolower(trim((string) $name));

        if (str_contains($key, 'paket')) {
            return 'paket';
        }

        if (str_contains($key, 'minum') || str_contains($key, 'teh') || str_contains($key, 'kopi')) {
            return 'minuman';
        }

        return 'makanan';
    };

    $categoryIcon = function (?string $name): string {
        $key = strtolower(trim((string) $name));

        if (str_contains($key, 'nasi') || str_contains($key, 'makan')) {
            return 'fa-bowl-rice';
        }

        if (str_contains($key, 'mie') || str_contains($key, 'mi')) {
            return 'fa-wheat-awn';
        }

        if (str_contains($key, 'ayam')) {
            return 'fa-drumstick-bite';
        }

        if (str_contains($key, 'minum') || str_contains($key, 'teh') || str_contains($key, 'kopi')) {
            return 'fa-glass-water';
        }

        if (str_contains($key, 'snack') || str_contains($key, 'cemilan')) {
            return 'fa-cookie-bite';
        }

        if (str_contains($key, 'paket')) {
            return 'fa-bag-shopping';
        }

        return 'fa-utensils';
    };
@endphp

<button class="category-btn {{ ($activeFilter ?? 'all') === 'all' ? 'active' : '' }}" type="button" data-filter="all">
    <i class="fas fa-table-cells-large" aria-hidden="true"></i>
    <span>Semua</span>
</button>
@foreach ($categories as $category)
    <button class="category-btn {{ ($activeFilter ?? '') === $categoryFilter($category->name) ? 'active' : '' }}" type="button" data-filter="{{ $categoryFilter($category->name) }}">
        <i class="fas {{ $categoryIcon($category->name) }}" aria-hidden="true"></i>
        <span>{{ $category->name }}</span>
    </button>
@endforeach
