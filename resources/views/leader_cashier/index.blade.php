@extends('leader_cashier.layout')

@section('title', 'Leader Kasir - Monitoring & Laporan')
@section('page_icon', 'fas fa-chart-line')
@section('page_title', 'Monitoring & Laporan')
@section('page_description', 'Monitoring transaksi, metode pembayaran, selisih kas, dan laporan kas masuk/keluar.')

@section('content')
<div class="leader-content" data-leader-live data-live-url="{{ route('leader-cashier.live') }}">
    
    <!-- STATS GRID -->
    <section class="stats-grid">
      <article class="stat-card fade-in" style="--card-accent: var(--green);">
        <div class="card-icon" style="background:var(--green-light);color:var(--green);"><i class="fas fa-arrow-trend-up"></i></div>
        <strong class="value-green" id="metric-total-uang-masuk">Rp {{ number_format($totalUangMasuk, 0, ',', '.') }}</strong>
        <span>Total Uang Masuk</span>
      </article>
      <article class="stat-card fade-in" style="--card-accent: var(--blue);">
        <div class="card-icon" style="background:var(--blue-light);color:var(--blue);"><i class="fas fa-receipt"></i></div>
        <strong id="metric-total-transaksi-hari-ini">{{ number_format($totalTransaksiHariIni, 0, ',', '.') }}</strong>
        <span>Total Transaksi Hari Ini</span>
      </article>
      <article class="stat-card fade-in" style="--card-accent: var(--teal);">
        <div class="card-icon" style="background:var(--teal-light);color:var(--teal);"><i class="fas fa-hand-holding-dollar"></i></div>
        <strong id="metric-total-kas-masuk">Rp {{ number_format($totalKasMasuk, 0, ',', '.') }}</strong>
        <span>Kas Masuk Manual</span>
      </article>
      <article class="stat-card fade-in" style="--card-accent: var(--accent);">
        <div class="card-icon" style="background:var(--accent-light);color:var(--accent);"><i class="fas fa-scale-balanced"></i></div>
        <strong id="metric-selisih-kas">Rp {{ number_format($selisihKas, 0, ',', '.') }}</strong>
        <span>Selisih Kas (Masuk - Keluar)</span>
      </article>
    </section>

    <!-- CASH FLOW PANEL -->
    <section class="panel fade-in" id="cash-flow-section">
      <div class="panel-head">
        <div>
          <h2><i class="fas fa-wallet"></i> Laporan Kas Masuk/Keluar</h2>
          <div class="panel-meta">Catat pengeluaran operasional atau penambahan modal manual.</div>
        </div>
      </div>

      <form class="form-grid" id="cashFlowForm" method="POST" action="{{ route('leader-cashier.cash-flow.store') }}" data-turbo="false">
        @csrf
        <div class="field" style="grid-column: span 2;">
          <label>Tipe</label>
          <select name="type" required>
            <option value="">Pilih Tipe</option>
            <option value="in">Kas Masuk</option>
            <option value="out">Kas Keluar</option>
          </select>
        </div>
        <div class="field" style="grid-column: span 2;">
          <label>Nominal</label>
          <input type="number" name="amount" min="0.01" step="0.01" placeholder="0" required>
        </div>
        <div class="field" style="grid-column: span 4;">
          <label>Keterangan</label>
          <input type="text" name="description" maxlength="255" placeholder="Deskripsi transaksi..." required>
        </div>
        <div class="field" style="grid-column: span 3;">
          <label>Waktu</label>
          <input type="datetime-local" name="happened_at" value="{{ now()->format('Y-m-d\TH:i') }}" required>
        </div>
        <button class="btn btn-primary" type="submit" style="grid-column: span 1; align-self: end; height: 40px;">
          <i class="fas fa-plus"></i> Simpan
        </button>
      </form>

      <div class="table-wrap">
        <table class="report-table cash-table">
          <thead>
            <tr>
              <th>Waktu</th>
              <th>Tipe</th>
              <th>Nominal</th>
              <th>Keterangan</th>
              <th>User</th>
              <th style="text-align: right;">Aksi</th>
            </tr>
          </thead>
          <tbody id="cashFlowRows">
            @include('leader_cashier.partials.cash-flow-rows', ['laporanKas' => $laporanKas])
          </tbody>
        </table>
      </div>

      <div class="pagination-area" id="cashFlowPagination">
        {{ $laporanKas->links('components.pagination') }}
      </div>
    </section>

    <!-- TRANSACTION HISTORY PANEL -->
    <section class="panel fade-in">
      <div class="panel-head">
        <div>
          <h2><i class="fas fa-table-list"></i> Riwayat Transaksi</h2>
          <div class="panel-meta">Daftar transaksi terbaru hari ini.</div>
        </div>
        <span class="live-indicator"><span class="live-dot"></span> Live</span>
      </div>

      <div class="table-wrap">
        <table class="report-table trx-table">
          <thead>
            <tr>
              <th>Kode</th>
              <th>Waktu</th>
              <th>Meja</th>
              <th>Status</th>
              <th style="text-align: right;">Total</th>
            </tr>
          </thead>
          <tbody id="transactionRows">
            @include('leader_cashier.partials.transaction-rows', ['riwayatTransaksi' => $riwayatTransaksi, 'hasStatus' => $hasStatus])
          </tbody>
        </table>
      </div>

      <div class="pagination-area" id="transactionPagination">
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

            const submitBtn = form.querySelector('button[type="submit"]');
            const originalContent = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

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
                    const data = await response.json();
                    throw new Error(data.message || 'Gagal menyimpan data kas');
                }

                form.reset();
                const timeField = form.querySelector('input[name="happened_at"]');
                if (timeField) {
                    const now = new Date();
                    const pad = (n) => String(n).padStart(2, '0');
                    timeField.value = `${now.getFullYear()}-${pad(now.getMonth() + 1)}-${pad(now.getDate())}T${pad(now.getHours())}:${pad(now.getMinutes())}`;
                }
                
                if (window.showToast) window.showToast('Kas berhasil dicatat.', 'success');
                await fetchLive();
            } catch (error) {
                console.error(error);
                if (window.showToast) window.showToast(error.message, 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalContent;
            }
        };

        const handleDelete = async (event) => {
            const formEl = event.target.closest('form[data-cash-delete]');
            if (!formEl) return;

            event.preventDefault();
            if (!confirm('Hapus data kas ini?')) return;

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

                if (window.showToast) window.showToast('Data kas dihapus.', 'success');
                await fetchLive();
            } catch (error) {
                console.error(error);
                if (window.showToast) window.showToast('Gagal menghapus.', 'error');
            }
        };

        const handlePaginationClick = async (event) => {
            const link = event.target.closest('.pagination-link');
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
