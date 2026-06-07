@forelse($laporanKas as $row)
    <tr>
        <td style="font-size:12px;color:var(--fg-secondary);">{{ $row->happened_at?->format('d M Y, H:i') }}</td>
        <td>
            @if($row->type === 'in')
                <span class="tag tag-in"><i class="fas fa-arrow-down" style="font-size:9px;"></i> Masuk</span>
            @else
                <span class="tag tag-out"><i class="fas fa-arrow-up" style="font-size:9px;"></i> Keluar</span>
            @endif
        </td>
        <td class="amount-cell {{ $row->type === 'in' ? 'amount-in' : 'amount-out' }}">
            {{ $row->type === 'in' ? '+' : '-' }} Rp {{ number_format((float) $row->amount, 0, ',', '.') }}
        </td>
        <td>{{ $row->description }}</td>
        <td style="font-size:12px;color:var(--fg-secondary);">{{ $row->user?->name ?? $row->user?->username ?? '-' }}</td>
        <td style="text-align:right;">
            <form method="POST" action="{{ route('leader-cashier.cash-flow.destroy', $row) }}" data-cash-delete data-turbo="false">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger btn-sm" type="submit" title="Hapus"><i class="fas fa-trash"></i></button>
            </form>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="empty-state">Belum ada data kas masuk/keluar.</td>
    </tr>
@endforelse
