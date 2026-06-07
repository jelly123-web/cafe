@forelse ($menus as $menu)
    @php
        $isSoldOut = (bool) $menu->is_sold_out;
        $categoryName = $menu->category?->name ?? '-';
        $iconClass = match (strtolower($categoryName)) {
            'minuman' => 'fas fa-mug-hot',
            'dessert' => 'fas fa-ice-cream',
            'cemilan', 'snack' => 'fas fa-cookie-bite',
            default => 'fas fa-bowl-food',
        };
    @endphp
    <tr data-menu-id="{{ $menu->id }}" class="{{ $isSoldOut ? 'row-sold-out' : '' }}">
        <td>
            <div class="cell-menu">
                <div class="menu-icon {{ $isSoldOut ? 'sold-out' : '' }}"><i class="{{ $iconClass }}"></i></div>
                <span class="menu-name">{{ $menu->name }}</span>
            </div>
        </td>
        <td><span class="cell-category-pill">{{ $categoryName }}</span></td>
        <td>
            <span class="status-badge {{ $isSoldOut ? 'sold-out' : 'available' }}">
                <span class="status-dot"></span> {{ $isSoldOut ? 'Habis' : 'Tersedia' }}
            </span>
        </td>
        <td>
            <form method="POST" action="{{ route('kitchen.menus.toggle', $menu) }}" class="js-toggle-menu-form">
                @csrf
                @method('PUT')
                <button class="btn-action {{ $isSoldOut ? 'btn-available' : 'btn-sold-out' }}" type="submit">
                    <i class="fas {{ $isSoldOut ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                    {{ $isSoldOut ? 'Tandai Tersedia' : 'Tandai Habis' }}
                </button>
            </form>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="4">
            <div class="empty-state">
                <i class="fas fa-utensils"></i>
                <strong>Belum ada menu</strong>
                <span>Daftar menu akan tampil di sini untuk ditandai tersedia atau habis.</span>
            </div>
        </td>
    </tr>
@endforelse
