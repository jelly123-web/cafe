@extends('leader_cashier.layout')

@section('title', 'Struk Kasir')
@section('page_title', 'Struk Kasir')
@section('page_icon', 'fas fa-receipt')
@section('page_description', 'Cetak struk dan kirim struk digital via WhatsApp.')

@push('head')
<style>
    /* ===== STATS STRIP ===== */
    .stats-strip {
        display: grid; grid-template-columns: repeat(4, 1fr);
        gap: 12px; margin-bottom: 24px;
    }
    .strip-card {
        background: var(--white); border: 1px solid var(--border);
        border-radius: var(--radius-md); padding: 14px 18px;
        display: flex; align-items: center; gap: 14px;
        transition: all 0.25s ease; position: relative; overflow: hidden;
    }
    .strip-card .strip-icon {
        width: 40px; height: 40px; border-radius: var(--radius-sm);
        display: flex; align-items: center; justify-content: center;
        font-size: 16px; flex-shrink: 0;
    }
    .strip-info strong {
        font-size: 18px; font-weight: 900; color: var(--fg);
        letter-spacing: -0.3px; line-height: 1.1; display: block;
    }
    .strip-info span {
        font-size: 11px; color: var(--muted); font-weight: 600;
        text-transform: uppercase; letter-spacing: 0.4px;
    }
</style>
@endpush

@section('content')
  <!-- STATS STRIP -->
  <section class="stats-strip fade-in">
    <div class="strip-card" style="--card-accent: var(--green);">
      <div class="strip-icon" style="background:var(--green-light);color:var(--green);"><i class="fas fa-check-circle"></i></div>
      <div class="strip-info">
        <strong>{{ $orders->where('status', 'paid')->count() }}</strong>
        <span>Lunas</span>
      </div>
    </div>
    <div class="strip-card" style="--card-accent: var(--accent);">
      <div class="strip-icon" style="background:var(--accent-light);color:var(--accent);"><i class="fas fa-hourglass-half"></i></div>
      <div class="strip-info">
        <strong>{{ $orders->where('status', 'unpaid')->count() }}</strong>
        <span>Belum Bayar</span>
      </div>
    </div>
    <div class="strip-card" style="--card-accent: var(--blue);">
      <div class="strip-icon" style="background:var(--blue-light);color:var(--blue);"><i class="fas fa-paper-plane"></i></div>
      <div class="strip-info">
        <strong>{{ $orders->where('status', 'sent')->count() }}</strong>
        <span>Struk Terkirim</span>
      </div>
    </div>
    <div class="strip-card" style="--card-accent: var(--red);">
      <div class="strip-icon" style="background:var(--red-light);color:var(--red);"><i class="fas fa-ban"></i></div>
      <div class="strip-info">
        <strong>{{ $orders->where('status', 'cancelled')->count() }}</strong>
        <span>Dibatalkan</span>
      </div>
    </div>
  </section>

  <!-- MAIN PANEL -->
  <section class="panel fade-in">
    <div class="panel-head">
      <div>
        <h2><i class="fas fa-table-list"></i> Daftar Struk</h2>
      </div>
    </div>

    <!-- FILTER BAR -->
    <div class="filter-bar">
      <div class="search-mini">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Cari kode struk..." id="searchInput">
      </div>
      <button class="filter-tab active" data-filter="all">Semua <span class="tab-count">{{ $orders->count() }}</span></button>
      <button class="filter-tab" data-filter="paid">Lunas <span class="tab-count">{{ $orders->where('status', 'paid')->count() }}</span></button>
      <button class="filter-tab" data-filter="unpaid">Belum Bayar <span class="tab-count">{{ $orders->where('status', 'unpaid')->count() }}</span></button>
      <button class="filter-tab" data-filter="cancelled">Batal <span class="tab-count">{{ $orders->where('status', 'cancelled')->count() }}</span></button>
    </div>

    <!-- TABLE -->
    <div class="table-wrap">
      <table class="report-table receipt-table">
        <thead>
          <tr>
            <th>Kode TRX</th>
            <th>Meja</th>
            <th>Total</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody id="receiptBody">
          @foreach($orders as $order)
          <tr data-status="{{ $order->status }}">
            <td><span class="trx-code">{{ $order->code }}</span></td>
            <td>
              <div class="branch-cell">
                <div class="branch-dot" style="background:linear-gradient(135deg, var(--accent), #F59E0B);"><i class="fas fa-chair"></i></div>
                <span style="font-weight:600;font-size:13px;">{{ $order->table ? 'Meja ' . $order->table->number : 'Bungkus' }}</span>
              </div>
            </td>
            <td class="amount-cell">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
            <td>
                @php
                    $statusClass = $order->status === 'paid' ? 'status-paid' : ($order->status === 'cancelled' ? 'status-cancelled' : 'status-unpaid');
                    $statusLabel = $order->status === 'paid' ? 'Lunas' : ($order->status === 'cancelled' ? 'Batal' : 'Belum Bayar');
                @endphp
                <span class="status-pill {{ $statusClass }}"><span class="status-dot"></span> {{ $statusLabel }}</span>
            </td>
            <td>
              <div class="action-group" style="display: flex; align-items: center; gap: 8px;">
                <a class="btn btn-print btn-sm" href="{{ route('cashier.receipts.print', $order) }}" target="_blank" title="Cetak Struk">
                  <i class="fas fa-print"></i> Cetak
                </a>
                
                <form action="{{ route('cashier.receipts.send', $order) }}" method="POST" data-send-form style="display: flex; gap: 8px; align-items: center;">
                  @csrf
                  <input type="text" name="destination" placeholder="No WA" class="input-field" maxlength="15" required>
                  <button class="btn btn-send btn-sm" type="submit">
                    <i class="fab fa-whatsapp"></i> Kirim
                  </button>
                </form>
                
                @if($order->status !== 'paid')
                <form action="{{ route('cashier.receipts.destroy', $order) }}" method="POST" onsubmit="return confirm('Hapus data struk ini?')" data-delete-form style="display: inline-flex;">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger btn-sm" type="submit">
                      <i class="fas fa-trash"></i> Hapus
                    </button>
                </form>
                @endif
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <!-- PAGINATION -->
    <div class="pagination-area">
      {{ $orders->links() }}
    </div>
  </section>
@endsection

@push('scripts')
<script>
  (() => {
    // ===== FILTER TABS =====
    const filterTabs = document.querySelectorAll('.filter-tab');
    const rows = document.querySelectorAll('#receiptBody tr[data-status]');
    filterTabs.forEach(tab => {
      tab.addEventListener('click', () => {
        filterTabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');
        const filter = tab.dataset.filter;
        rows.forEach(row => {
          row.style.display = (filter === 'all' || row.dataset.status === filter) ? '' : 'none';
        });
      });
    });

    // ===== SEARCH =====
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
      searchInput.addEventListener('input', () => {
        const q = searchInput.value.toLowerCase().trim();
        rows.forEach(row => {
          const text = row.textContent.toLowerCase();
          row.style.display = text.includes(q) ? '' : 'none';
        });
      });
    }
  })();
</script>
@endpush
