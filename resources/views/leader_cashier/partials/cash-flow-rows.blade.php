@forelse($laporanKas as $row)
    <tr>
        <td>{{ $row->happened_at?->format('d M Y H:i') }}</td>
        <td>
            {!! $row->type === 'in' ? '<span class="tag tag-in">Masuk</span>' : '<span class="tag tag-out">Keluar</span>' !!}
        </td>
        <td>Rp {{ number_format((float) $row->amount, 0, ',', '.') }}</td>
        <td>{{ $row->description }}</td>
        <td>{{ $row->user?->username ?? '-' }}</td>
        <td>
            <form method="POST" action="{{ route('leader-cashier.cash-flow.destroy', $row) }}" data-cash-delete data-turbo="false" onsubmit="return confirm('Hapus data kas ini?')">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger" type="submit">Hapus</button>
            </form>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="empty-state">Belum ada data kas masuk/keluar.</td>
    </tr>
@endforelse
