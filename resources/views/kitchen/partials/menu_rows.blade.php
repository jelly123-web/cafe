@forelse ($menus as $menu)
    <tr data-menu-id="{{ $menu->id }}">
        <td><span class="menu-name">{{ $menu->name }}</span></td>
        <td><span class="menu-category">{{ $menu->category?->name ?? '-' }}</span></td>
        <td>
            <span class="tag {{ $menu->is_sold_out ? 'tag-sold-out' : 'tag-available' }}">
                {{ $menu->is_sold_out ? 'Habis' : 'Tersedia' }}
            </span>
        </td>
        <td>
            <form method="POST" action="{{ route('kitchen.menus.toggle', $menu) }}" class="js-toggle-menu-form">
                @csrf
                @method('PUT')
                <button class="btn {{ $menu->is_sold_out ? 'btn-success' : 'btn-danger' }}" type="submit">
                    {{ $menu->is_sold_out ? 'Tandai Tersedia' : 'Tandai Habis' }}
                </button>
            </form>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="4" class="empty-state">Belum ada menu.</td>
    </tr>
@endforelse
