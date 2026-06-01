<section class="summary-grid">
    <article class="summary-card">
        <span>Total transaksi hari ini</span>
        <strong>Rp {{ number_format($totalToday, 0, ',', '.') }}</strong>
    </article>
    <article class="summary-card">
        <span>Jumlah transaksi hari ini</span>
        <strong>{{ $countToday }}</strong>
    </article>
    <article class="summary-card">
        <span>Rekap uang masuk</span>
        <strong>Rp {{ number_format($totalIncome, 0, ',', '.') }}</strong>
    </article>
    <article class="summary-card">
        <span>Total riwayat (halaman ini)</span>
        <strong>{{ $history->count() }}</strong>
    </article>
</section>

<section class="panel">
    <h2 class="section-title">Riwayat Transaksi</h2>
    <div class="table-wrap">
        <table class="report-table">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Waktu</th>
                    <th>Meja</th>
                    <th>Status</th>
                    <th>Total</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($history as $trx)
                    @php
                        $statusClass = $trx->status === \App\Models\SaleTransaction::STATUS_PAID ? 'tag-paid' : ($trx->status === \App\Models\SaleTransaction::STATUS_CANCELLED ? 'tag-cancelled' : 'tag-pending');
                    @endphp
                    <tr>
                        <td><span class="order-code">{{ $trx->code }}</span></td>
                        <td>{{ $trx->sold_at?->format('d M Y H:i') }}</td>
                        <td>{{ $trx->table?->number ?? '-' }}</td>
                        <td><span class="tag {{ $statusClass }}">{{ $trx->statusLabel() }}</span></td>
                        <td>Rp {{ number_format((float) $trx->total_amount, 0, ',', '.') }}</td>
                        <td>
                            <form method="POST" action="{{ route('cashier.reports.destroy', $trx) }}" onsubmit="return confirm('Hapus transaksi {{ $trx->code }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination-wrap">
        <div class="pagination-meta">
            Menampilkan {{ $history->firstItem() ?? 0 }} - {{ $history->lastItem() ?? 0 }} dari {{ $history->total() }} data
        </div>
        {{ $history->links('components.pagination') }}
    </div>
</section>
