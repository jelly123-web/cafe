<div class="category-filter-bar fade-in visible" id="menuCategoryFilterBar" data-current-category-id="{{ $current_category_id ?? 'all' }}">
    <a href="{{ route('superadmin.menus.index', ['search' => request('search')]) }}"
       class="filter-pill {{ ($current_category_id ?? 'all') === 'all' ? 'active' : '' }}"
       data-category-id="all"
       data-category-slug="all"
       data-turbo="false">
       <i class="fas fa-th-large"></i> Semua <span>({{ $total_menus }})</span>
    </a>
    @foreach ($categories as $category)
        @php
            $icon = 'fa-utensils';
            $cname = strtolower($category->name);
            if (str_contains($cname, 'makanan')) {
                $icon = 'fa-bowl-rice';
            } elseif (str_contains($cname, 'minuman')) {
                $icon = 'fa-glass-water';
            } elseif (str_contains($cname, 'paket')) {
                $icon = 'fa-shopping-bag';
            }
        @endphp
        <a href="{{ route('superadmin.menus.index', ['category_id' => $category->id, 'search' => request('search')]) }}"
           class="filter-pill {{ (string) ($current_category_id ?? 'all') === (string) $category->id ? 'active' : '' }}"
           data-category-id="{{ $category->id }}"
           data-category-slug="{{ $cname }}"
           data-turbo="false">
           <i class="fas {{ $icon }}"></i> {{ strtolower($category->name) }} <span>({{ $category->display_count ?? $category->menus_count }})</span>
        </a>
    @endforeach
</div>

<div class="panel fade-in visible">
    <div class="panel-head">
        <h2>
            <i class="fas fa-list" id="menuListIcon"></i>
            <span id="menuListTitle">Daftar Menu</span>
        </h2>
        <span id="menuCount">{{ $menus->count() + $packages->count() }} item</span>
    </div>

    <div class="menu-card-list" id="menusList" data-list-mode="mixed">
        @php $hasAnyItem = $menus->count() > 0 || $packages->count() > 0; @endphp
        @foreach ($menus as $menu)
            @php
                $menuImage = $menu->image_path
                    ? (Storage::disk('public')->exists($menu->image_path) ? Storage::disk('public')->url($menu->image_path) : asset('images/menu-placeholder.svg'))
                    : asset('images/menu-placeholder.svg');
                $menuCategorySlug = strtolower($menu->category?->name ?? 'tanpa-kategori');
            @endphp
            <div class="menu-card"
                 data-menu-id="{{ $menu->id }}"
                 data-item-type="menu"
                 data-category-slug="{{ $menuCategorySlug }}"
                 data-category-id-val="{{ $menu->menu_category_id ?? 'none' }}">
                <img class="menu-thumb" src="{{ $menuImage }}" alt="{{ $menu->name }}">
                <div class="menu-meta">
                    <h3>{{ $menu->name }}</h3>
                    <p>{{ $menu->code }}</p>
                    <div class="menu-pricing">
                        <span class="tag tag-category">{{ $menu->category?->name ?? 'Tanpa kategori' }}</span>
                        <span class="tag tag-success">Rp {{ number_format((float) $menu->selling_price, 0, ',', '.') }}</span>
                        <span class="tag tag-muted">Modal Rp {{ number_format((float) $menu->cost_price, 0, ',', '.') }}</span>
                    </div>
                </div>
                <div class="actions">
                    <button type="button" class="btn-open-edit"
                        data-id="{{ $menu->id }}"
                        data-code="{{ $menu->code }}"
                        data-name="{{ $menu->name }}"
                        data-category-id="{{ $menu->menu_category_id }}"
                        data-selling-price="{{ (float) $menu->selling_price }}"
                        data-cost-price="{{ (float) $menu->cost_price }}"
                        data-image-url="{{ $menuImage }}"
                    ><i class="fas fa-pen"></i> Edit</button>
                    <button type="button" class="btn-delete-menu" data-id="{{ $menu->id }}"><i class="fas fa-trash"></i> Hapus</button>
                </div>
            </div>
        @endforeach
        @foreach ($packages as $package)
            @php
                $packageImage = $package->image_path
                    ? (Storage::disk('public')->exists($package->image_path) ? Storage::disk('public')->url($package->image_path) : asset('images/menu-placeholder.svg'))
                    : asset('images/menu-placeholder.svg');
                $packageItems = $package->menus->map(fn ($item) => '(' . (int) ($item->pivot->quantity ?? 1) . 'x) ' . $item->name)->implode(', ');
            @endphp
            <div class="menu-card package-menu-card"
                 data-package-id="{{ $package->id }}"
                 data-item-type="package"
                 data-category-slug="paket"
                 data-category-id-val="paket">
                <img class="menu-thumb" src="{{ $packageImage }}" alt="{{ $package->name }}">
                <div class="menu-meta">
                    <h3>{{ $package->name }}</h3>
                    <p>{{ $package->code ?: 'Paket' }}</p>
                    <div class="menu-pricing">
                        <span class="tag tag-category">paket</span>
                        <span class="tag tag-success">Rp {{ number_format((float) $package->selling_price, 0, ',', '.') }}</span>
                        @if ($package->free_item)
                            <span class="tag tag-category"><i class="fas fa-gift"></i>&nbsp; Free: {{ $package->free_item }}</span>
                        @endif
                        @if ($packageItems)
                            <span class="tag tag-muted">{{ $packageItems }}</span>
                        @else
                            <span class="tag tag-muted">Belum ada menu</span>
                        @endif
                    </div>
                </div>
                <div class="actions">
                    <a class="package-manage-link" href="{{ route('superadmin.packages.index') }}"><i class="fas fa-box-open"></i> Kelola Paket</a>
                </div>
            </div>
        @endforeach
        @if (! $hasAnyItem)
            <div class="alert" id="emptyState"><em>Belum ada menu atau paket.</em></div>
        @endif
    </div>

    <div class="menu-pagination">
        {{-- Pagination dimatikan supaya pindah kategori instan tanpa reload --}}
    </div>
</div>
