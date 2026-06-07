@extends('cashier.layout')

@section('title', 'Struk Kasir')
@section('page_title', 'Struk Kasir')
@section('page_icon', '<i class="fas fa-receipt"></i>')
@section('page_description', 'Cetak struk dan kirim struk digital via WhatsApp.')

@section('content')
  <!-- STATS STRIP -->
  <section class="stats-strip fade-in">
    <div class="strip-card" style="--card-accent: var(--green);">
      <div class="strip-icon" style="background:var(--green-light);color:var(--green);"><i class="fas fa-check-circle"></i></div>
      <div class="strip-info">
        <strong>5</strong>
        <span>Lunas</span>
      </div>
    </div>
    <div class="strip-card" style="--card-accent: var(--accent);">
      <div class="strip-icon" style="background:var(--accent-light);color:var(--accent);"><i class="fas fa-hourglass-half"></i></div>
      <div class="strip-info">
        <strong>1</strong>
        <span>Belum Bayar</span>
      </div>
    </div>
    <div class="strip-card" style="--card-accent: var(--blue);">
      <div class="strip-icon" style="background:var(--blue-light);color:var(--blue);"><i class="fas fa-paper-plane"></i></div>
      <div class="strip-info">
        <strong>3</strong>
        <span>Struk Terkirim</span>
      </div>
    </div>
    <div class="strip-card" style="--card-accent: var(--red);">
      <div class="strip-icon" style="background:var(--red-light);color:var(--red);"><i class="fas fa-ban"></i></div>
      <div class="strip-info">
        <strong>1</strong>
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
      <button class="filter-tab active" data-filter="all">Semua <span class="tab-count">7</span></button>
      <button class="filter-tab" data-filter="paid">Lunas <span class="tab-count">5</span></button>
      <button class="filter-tab" data-filter="unpaid">Belum Bayar <span class="tab-count">1</span></button>
      <button class="filter-tab" data-filter="cancelled">Batal <span class="tab-count">1</span></button>
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
          <!-- Sample Rows (Would ideally come from Laravel controller) -->
          <tr data-status="paid">
            <td><span class="trx-code">TRX-0148</span></td>
            <td>
              <div class="branch-cell">
                <div class="branch-dot" style="background:linear-gradient(135deg, var(--accent), #F59E0B);"><i class="fas fa-chair"></i></div>
                <span style="font-weight:600;font-size:13px;">Meja 3</span>
              </div>
            </td>
            <td class="amount-cell">Rp 68.000</td>
            <td><span class="status-pill status-paid"><span class="status-dot"></span> Lunas</span></td>
            <td>
              <div class="action-group">
                <a class="btn btn-print btn-sm" href="#" title="Cetak Struk"><i class="fas fa-print"></i> Cetak</a>
                <form class="inline-form" data-send-form>
                  <input type="text" name="destination" placeholder="No WA" class="input-field" maxlength="15">
                  <button class="btn btn-send btn-sm" type="submit"><i class="fab fa-whatsapp"></i> Kirim</button>
                </form>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- PAGINATION (Simplified) -->
    <div class="pagination-area">
      <div class="pagination-wrap">
        <span class="pagination-meta">Menampilkan 1–7 dari 7 struk</span>
        <div class="pagination-links">
          <a class="pagination-link disabled"><i class="fas fa-chevron-left" style="font-size:10px;"></i></a>
          <a class="pagination-link active">1</a>
          <a class="pagination-link" href="#">2</a>
        </div>
      </div>
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

    // ===== SEND WHATSAPP =====
    document.querySelectorAll('[data-send-form]').forEach(form => {
      form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const input = form.querySelector('.input-field');
        const btn = form.querySelector('button');
        const phone = input.value.trim();
        if (!phone) { showToast('Masukkan nomor WhatsApp.', 'error'); input.focus(); return; }

        const origHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
        btn.disabled = true;

        await new Promise(r => setTimeout(r, 1000));

        btn.innerHTML = '<i class="fas fa-check"></i>';
        showToast(`Struk terkirim ke ${phone}`, 'success');
        input.value = '';

        setTimeout(() => { btn.innerHTML = origHTML; btn.disabled = false; }, 1500);
      });
    });

    // ===== DELETE =====
    document.querySelectorAll('[data-delete-form]').forEach(form => {
      form.addEventListener('submit', (e) => {
        e.preventDefault();
        if (!confirm('Hapus data struk ini?')) return;
        const row = form.closest('tr');
        if (row) {
          row.style.transition = 'all 0.3s ease';
          row.style.opacity = '0';
          row.style.transform = 'translateX(20px)';
          setTimeout(() => row.remove(), 300);
        }
        showToast('Data struk dihapus.', 'error');
      });
    });
  })();
</script>
@endpush
