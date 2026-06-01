<div class="grid">
    <div class="card">
        <span>Total penjualan semua cabang</span>
        <strong>Rp {{ number_format((float) $totalSales, 0, ',', '.') }}</strong>
        <small>Akumulasi seluruh transaksi pada semua cabang.</small>
    </div>
    <div class="card">
        <span>Transaksi hari ini</span>
        <strong>{{ number_format($todayTransactions, 0, ',', '.') }}</strong>
        <small>Jumlah transaksi yang masuk pada tanggal ini.</small>
    </div>
    <div class="card">
        <span>Laba / rugi</span>
        <strong class="{{ $profitLoss >= 0 ? 'profit' : 'loss' }}">
            {{ $profitLoss >= 0 ? 'Laba' : 'Rugi' }} Rp {{ number_format(abs((float) $profitLoss), 0, ',', '.') }}
        </strong>
        <small>Total penjualan dikurangi total modal barang terjual.</small>
    </div>
    <div class="card">
        <span>Menu terlaris</span>
        <strong>{{ $topMenu?->name ?? '-' }}</strong>
        <small>{{ number_format((int) ($topMenu?->sold_qty ?? 0), 0, ',', '.') }} porsi terjual</small>
    </div>
</div>

<div class="section">
    <div class="panel">
        <h2>Penjualan per cabang</h2>
        <table>
            <thead>
                <tr>
                    <th>Cabang</th>
                    <th class="muted">Kode</th>
                    <th>Total penjualan</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($branchSales as $branch)
                    <tr>
                        <td>{{ $branch->name }}</td>
                        <td class="muted">{{ $branch->code }}</td>
                        <td>Rp {{ number_format((float) $branch->total_sales, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="panel">
        <h2>Komposisi cabang</h2>
        <div class="bars">
            @php
                $maxSales = max(1, (float) $branchSales->max('total_sales'));
            @endphp
            @foreach ($branchSales as $branch)
                @php
                    $percent = $maxSales > 0 ? ((float) $branch->total_sales / $maxSales) * 100 : 0;
                @endphp
                <div class="bar-row">
                    <div class="bar-label">
                        <span>{{ $branch->name }}</span>
                        <span>Rp {{ number_format((float) $branch->total_sales, 0, ',', '.') }}</span>
                    </div>
                    <div class="bar-track">
                        <div class="bar-fill" style="width: {{ $percent }}%;"></div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div class="panel panel-wide">
    <h2>Transaksi terbaru semua akun</h2>
    <table>
        <thead>
            <tr>
                <th>Kode</th>
                <th>Cabang</th>
                <th>Meja</th>
                <th>Waktu</th>
                <th>Status</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($recentTransactions as $transaction)
                <tr>
                    <td><span class="order-code">{{ $transaction->code }}</span></td>
                    <td>{{ $transaction->branch?->name ?? '-' }}</td>
                    <td>{{ $transaction->table?->name ?? $transaction->table?->number ?? '-' }}</td>
                    <td>{{ optional($transaction->sold_at)->format('d M Y H:i') }}</td>
                    <td>
                        <span class="status-pill status-{{ $transaction->status ?? 'unknown' }}">
                            {{ $transaction->statusLabel() }}
                        </span>
                    </td>
                    <td>Rp {{ number_format((float) $transaction->total_amount, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="empty-state">Belum ada transaksi yang tercatat.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    {{ $recentTransactions->links('components.pagination') }}
</div>
