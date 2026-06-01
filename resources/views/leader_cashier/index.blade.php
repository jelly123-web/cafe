@extends('leader_cashier.layout')

@section('title', 'Leader Kasir - Monitoring & Laporan')
@section('kicker', 'Halo, ' . (auth()->user()->name ?? 'User') . ' 👋')
@section('page_title', 'Leader Kasir')
@section('page_description', 'Monitoring transaksi, metode pembayaran, selisih kas, dan laporan kas masuk/keluar.')

@push('head')
    <style>
        .main-panel {
            padding-bottom: 2rem;
        }

        .leader-content {
            display: grid;
            gap: 1.5rem;
            padding-top: .25rem;
        }

        .leader-actions {
            display: flex;
            gap: .65rem;
            flex-wrap: wrap;
            margin-top: .85rem;
        }

        .leader-actions .btn {
            background: var(--highlight);
            color: #fff;
        }

        .leader-actions .btn:hover {
            background: #c68b59;
            transform: translateY(-1px);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(12, minmax(0, 1fr));
            gap: .65rem .75rem;
            align-items: start;
        }

        .form-grid input,
        .form-grid select {
            width: 100%;
            border: 1px solid var(--accent);
            border-radius: 10px;
            padding: .55rem .8rem;
            background: var(--bg-main);
            color: var(--text-main);
            min-width: 0;
        }

        .form-grid select {
            grid-column: span 2;
        }

        .form-grid input[name="amount"] {
            grid-column: span 2;
        }

        .form-grid input[name="description"] {
            grid-column: span 4;
        }

        .form-grid input[name="happened_at"] {
            grid-column: span 2;
            max-width: 220px;
            justify-self: start;
        }

        .form-grid button {
            grid-column: 12 / -1;
            min-height: 42px;
            max-width: 140px;
            justify-self: end;
        }

        .btn {
            border: 1px solid transparent;
            border-radius: 10px;
            padding: .6rem 1rem;
            cursor: pointer;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary {
            background: var(--highlight);
            color: #fff;
        }

        .btn-danger {
            background: transparent;
            color: var(--loss);
            border-color: #ffccd2;
        }

        .tag {
            display: inline-flex;
            padding: .2rem .65rem;
            border-radius: 999px;
            font-size: .75rem;
            font-weight: 700;
        }

        .tag-in { background: #e8f5e9; color: #558b2f; }
        .tag-out { background: #fff3e0; color: #e65100; }

        .table-wrap {
            overflow: hidden;
            border-radius: 16px;
            background: #fff;
        }

        .table-wrap table {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
        }

        .table-wrap th,
        .table-wrap td {
            vertical-align: top;
            white-space: normal;
            word-break: break-word;
        }

        .table-wrap th:nth-child(1),
        .table-wrap td:nth-child(1) { width: 18%; }

        .table-wrap th:nth-child(2),
        .table-wrap td:nth-child(2) { width: 12%; }

        .table-wrap th:nth-child(3),
        .table-wrap td:nth-child(3) { width: 16%; }

        .table-wrap th:nth-child(4),
        .table-wrap td:nth-child(4) { width: 32%; }

        .table-wrap th:nth-child(5),
        .table-wrap td:nth-child(5) { width: 12%; }

        .table-wrap th:nth-child(6),
        .table-wrap td:nth-child(6) { width: 10%; }

        .table-wrap td:last-child,
        .table-wrap th:last-child {
            white-space: nowrap;
        }

        .empty-state {
            text-align: center;
            color: var(--text-muted);
            font-style: italic;
            padding: 1rem;
        }

        @media (max-width: 1100px) {
            .form-grid select,
            .form-grid input[name="amount"],
            .form-grid input[name="description"],
            .form-grid button,
            .form-grid input[name="happened_at"] {
                grid-column: 1 / -1;
                max-width: none;
                justify-self: stretch;
            }
        }

        @media (max-width: 768px) {
            .leader-actions {
                flex-direction: column;
            }

            .leader-actions .btn {
                width: 100%;
            }
        }
    </style>
@endpush

@section('content')
    <div class="leader-content" data-leader-live data-live-url="{{ route('leader-cashier.live') }}">
        @if (session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif

        <section class="grid">
            <article class="card">
                <span>Total uang masuk hari ini</span>
                <strong id="metric-total-uang-masuk">Rp {{ number_format($totalUangMasuk, 0, ',', '.') }}</strong>
            </article>
            <article class="card">
                <span>Total transaksi hari ini</span>
                <strong id="metric-total-transaksi-hari-ini">{{ number_format($totalTransaksiHariIni, 0, ',', '.') }}</strong>
            </article>
            <article class="card">
                <span>Kas masuk manual hari ini</span>
                <strong id="metric-total-kas-masuk">Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</strong>
            </article>
            <article class="card">
                <span>Selisih kas (masuk - keluar)</span>
                <strong id="metric-selisih-kas">Rp {{ number_format($selisihKas, 0, ',', '.') }}</strong>
            </article>
        </section>

        <section class="panel">
            <h2>Laporan Kas Masuk/Keluar</h2>
            <form class="form-grid" id="cashFlowForm" method="POST" action="{{ route('leader-cashier.cash-flow.store') }}" data-turbo="false">
                @csrf
                <select name="type" required>
                    <option value="">Tipe</option>
                    <option value="in">Kas Masuk</option>
                    <option value="out">Kas Keluar</option>
                </select>
                <input type="number" name="amount" min="0.01" step="0.01" placeholder="Nominal" required>
                <input type="text" name="description" maxlength="255" placeholder="Keterangan" required>
                <input type="datetime-local" name="happened_at" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                <button class="btn btn-primary" type="submit">Simpan</button>
            </form>

            <div class="table-wrap" style="margin-top:1rem;">
                <table>
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Tipe</th>
                            <th>Nominal</th>
                            <th>Keterangan</th>
                            <th>User</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody id="cashFlowRows">
                        @include('leader_cashier.partials.cash-flow-rows', ['laporanKas' => $laporanKas])
                    </tbody>
                </table>
            </div>

            <div class="pagination" id="cashFlowPagination">
                {{ $laporanKas->links('components.pagination') }}
            </div>
        </section>

        <section class="panel">
            <h2>Riwayat Transaksi</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Waktu</th>
                            <th>Meja</th>
                            <th>Status</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody id="transactionRows">
                        @include('leader_cashier.partials.transaction-rows', ['riwayatTransaksi' => $riwayatTransaksi, 'hasStatus' => $hasStatus])
                    </tbody>
                </table>
            </div>

            <div class="pagination" id="transactionPagination">
                {{ $riwayatTransaksi->links('components.pagination') }}
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script>
        (function () {
            const root = document.querySelector('[data-leader-live]');
            if (!root) return;

            const liveUrl = root.dataset.liveUrl;
            const form = document.getElementById('cashFlowForm');
            const cashRows = document.getElementById('cashFlowRows');
            const trxRows = document.getElementById('transactionRows');
            const cashPagination = document.getElementById('cashFlowPagination');
            const trxPagination = document.getElementById('transactionPagination');
            const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

            const params = new URLSearchParams(window.location.search);
            let cashPage = Number(params.get('cash_page') || 1);
            let trxPage = Number(params.get('trx_page') || 1);
            let timer = null;
            let busy = false;

            const syncUrl = () => {
                const next = new URL(window.location.href);
                next.searchParams.delete('cash_page');
                next.searchParams.delete('trx_page');
                if (cashPage > 1) next.searchParams.set('cash_page', String(cashPage));
                if (trxPage > 1) next.searchParams.set('trx_page', String(trxPage));
                history.replaceState({}, '', next.toString());
            };

            const setText = (id, value) => {
                const el = document.getElementById(id);
                if (el) el.textContent = value;
            };

            const updateDom = (payload) => {
                setText('metric-total-uang-masuk', payload.metrics.total_uang_masuk);
                setText('metric-total-transaksi-hari-ini', payload.metrics.total_transaksi_hari_ini);
                setText('metric-total-kas-masuk', payload.metrics.total_kas_masuk);
                setText('metric-selisih-kas', payload.metrics.selisih_kas);

                if (cashRows) cashRows.innerHTML = payload.cash_flow.rows_html;
                if (trxRows) trxRows.innerHTML = payload.transactions.rows_html;
                if (cashPagination) cashPagination.innerHTML = payload.cash_flow.pagination_html;
                if (trxPagination) trxPagination.innerHTML = payload.transactions.pagination_html;

                syncUrl();
            };

            const fetchLive = async () => {
                if (busy) return;
                busy = true;
                try {
                    const url = new URL(liveUrl, window.location.origin);
                    url.searchParams.set('cash_page', String(cashPage));
                    url.searchParams.set('trx_page', String(trxPage));
                    const response = await fetch(url.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                    });
                    if (!response.ok) throw new Error('Live fetch failed');
                    const payload = await response.json();
                    updateDom(payload);
                } catch (error) {
                    console.error(error);
                } finally {
                    busy = false;
                }
            };

            const submitCashFlow = async (event) => {
                event.preventDefault();
                if (!form) return;

                const formData = new FormData(form);
                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });

                    if (!response.ok) {
                        throw new Error('Gagal menyimpan data kas');
                    }

                    form.reset();
                    const timeField = form.querySelector('input[name="happened_at"]');
                    if (timeField) {
                        const now = new Date();
                        const pad = (n) => String(n).padStart(2, '0');
                        timeField.value = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
                    }
                    await fetchLive();
                } catch (error) {
                    console.error(error);
                    alert('Gagal menyimpan data kas.');
                }
            };

            const handleDelete = async (event) => {
                const submitButton = event.target.closest('form[data-cash-delete]');
                if (!submitButton) return;

                event.preventDefault();
                const formEl = submitButton;
                const formData = new FormData(formEl);

                try {
                    const response = await fetch(formEl.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });

                    if (!response.ok) {
                        throw new Error('Gagal menghapus data kas');
                    }

                    await fetchLive();
                } catch (error) {
                    console.error(error);
                    alert('Gagal menghapus data kas.');
                }
            };

            const handlePaginationClick = async (event) => {
                const link = event.target.closest('.pagination a');
                if (!link || !root.contains(link)) return;

                event.preventDefault();
                const url = new URL(link.href, window.location.origin);
                if (link.closest('#cashFlowPagination')) {
                    cashPage = Number(url.searchParams.get('cash_page') || 1);
                }
                if (link.closest('#transactionPagination')) {
                    trxPage = Number(url.searchParams.get('trx_page') || 1);
                }
                await fetchLive();
            };

            form?.addEventListener('submit', submitCashFlow);
            document.addEventListener('submit', handleDelete);
            document.addEventListener('click', handlePaginationClick);

            fetchLive();
            timer = setInterval(() => {
                if (document.visibilityState === 'visible') {
                    fetchLive();
                }
            }, 15000);

            document.addEventListener('turbo:before-cache', () => {
                if (timer) clearInterval(timer);
            });
        })();
    </script>
@endpush
