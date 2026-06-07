@extends('leader_cashier.layout')

@section('title', ($cafeBrand['name'] ?? config('app.name')) . ' - Laporan Kasir')
@section('page_icon', 'fas fa-file-invoice-dollar')
@section('page_title', 'Laporan Kasir')
@section('page_description', 'Total transaksi hari ini, jumlah transaksi, riwayat transaksi, dan rekap uang masuk.')

@push('head')
    <link rel="stylesheet" href="{{ asset('css/leader-cashier.css') }}">
@endpush

@section('content')
    <!-- ALERT WARN -->
    <div class="alert-warn fade-in">
        <div class="alert-icon"><i class="fas fa-triangle-exclamation"></i></div>
        <div class="alert-text">
            <strong>Catatan:</strong> Data laporan dihitung secara real-time dari transaksi hari ini.
        </div>
    </div>

    <!-- STATS GRID -->
    <section class="stats-grid">
        <article class="stat-card fade-in" style="--card-accent: var(--green);">
            <div class="card-icon" style="background:var(--green-light);color:var(--green);"><i class="fas fa-arrow-trend-up"></i></div>
            <strong class="value-green">Rp {{ number_format((float) $totalPenjualan, 0, ',', '.') }}</strong>
            <span>Total Penjualan</span>
        </article>
        <article class="stat-card fade-in" style="--card-accent: var(--blue);">
            <div class="card-icon" style="background:var(--blue-light);color:var(--blue);"><i class="fas fa-receipt"></i></div>
            <strong>{{ $totalTransaksi }}</strong>
            <span>Total Transaksi</span>
        </article>
        <article class="stat-card fade-in" style="--card-accent: var(--teal);">
            <div class="card-icon" style="background:var(--teal-light);color:var(--teal);"><i class="fas fa-boxes-stacked"></i></div>
            <strong>Rp {{ number_format((float) $totalModal, 0, ',', '.') }}</strong>
            <span>Total Modal (HPP)</span>
        </article>
        <article class="stat-card fade-in" style="--card-accent: var(--accent);">
            <div class="card-icon" style="background:var(--accent-light);color:var(--accent);"><i class="fas fa-chart-line"></i></div>
            <strong class="{{ ($labaRugi >= 0) ? 'value-green' : 'value-red' }}">
                {{ ($labaRugi >= 0 ? '+' : '-') }} Rp {{ number_format(abs((float) $labaRugi), 0, ',', '.') }}
            </strong>
            <span>Laba / Rugi Bersih</span>
        </article>
    </section>

    <!-- TRANSACTION HISTORY PANEL -->
    <section class="panel fade-in">
        <div class="panel-head">
            <div>
                <h2><i class="fas fa-table-list"></i> Riwayat Transaksi</h2>
            </div>
            <span class="live-indicator"><span class="live-dot"></span> Live</span>
        </div>

        <div class="table-wrap">
            <table class="report-table trx-report">
                <thead>
                    <tr>
                        <th>Kode TRX</th>
                        <th>Waktu</th>
                        <th>Meja</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th style="text-align: right;">Laba/Rugi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($riwayatTransaksi as $trx)
                        <tr>
                            <td><span class="trx-code">{{ $trx->code }}</span></td>
                            <td style="font-size:12px;color:var(--fg-secondary);">{{ optional($trx->sold_at)->format('d M Y, H:i') }}</td>
                            <td>
                                <div class="branch-cell">
                                    @if($trx->table_id)
                                        <div class="branch-dot" style="background:linear-gradient(135deg, var(--accent), #F59E0B);"><i class="fas fa-chair"></i></div>
                                        <span style="font-weight:600;font-size:13px;">Meja {{ $trx->table->number }}</span>
                                    @else
                                        <div class="branch-dot" style="background:linear-gradient(135deg, #2563EB, #60A5FA);"><i class="fas fa-bag-shopping"></i></div>
                                        <span style="font-weight:600;font-size:13px;">Bungkus</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                @php
                                    $isPaid = $trx->status === \App\Models\SaleTransaction::STATUS_PAID;
                                    $isCancelled = $trx->status === \App\Models\SaleTransaction::STATUS_CANCELLED;
                                    $tagClass = $isCancelled ? 'tag-cancelled' : ($isPaid ? 'tag-paid' : 'tag-pending');
                                @endphp
                                <span class="tag {{ $tagClass }}"><span class="tag-dot"></span> {{ $trx->statusLabel() }}</span>
                            </td>
                            <td class="amount-cell {{ $isCancelled ? 'text-muted' : '' }}" {{ $isCancelled ? 'style=text-decoration:line-through' : '' }}>
                                Rp {{ number_format((float) $trx->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="amount-cell {{ ($trx->profit >= 0) ? 'amount-profit' : 'amount-loss' }}">
                                {{ ($trx->profit >= 0 ? '+' : '-') }} Rp {{ number_format(abs((float) $trx->profit), 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty-state">Belum ada riwayat transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-area">
            {{ $riwayatTransaksi->links('components.pagination') }}
        </div>
    </section>
@endsection
