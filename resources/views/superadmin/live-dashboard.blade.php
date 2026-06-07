<!-- STAT CARDS -->
<div class="stats-grid">
  <div class="stat-card fade-in" style="--stat-color: var(--accent);">
    <div class="stat-header">
      <div class="stat-icon amber"><i class="fas fa-coins"></i></div>
      <div class="stat-trend up"><i class="fas fa-arrow-up"></i> 12.5%</div>
    </div>
    <div class="stat-value">Rp {{ number_format($totalSales, 0, ',', '.') }}</div>
    <div class="stat-label">Total Penjualan {{ $periodLabel ?? 'Hari Ini' }}</div>
  </div>
  <div class="stat-card fade-in" style="--stat-color: var(--blue);">
    <div class="stat-header">
      <div class="stat-icon blue"><i class="fas fa-receipt"></i></div>
      <div class="stat-trend up"><i class="fas fa-arrow-up"></i> 8.3%</div>
    </div>
    <div class="stat-value">{{ number_format($todayTransactions, 0, ',', '.') }}</div>
    <div class="stat-label">Transaksi {{ $periodLabel ?? 'Hari Ini' }}</div>
  </div>
  <div class="stat-card fade-in" style="--stat-color: var(--green);">
    <div class="stat-header">
      <div class="stat-icon green"><i class="fas fa-chart-line"></i></div>
      <div class="stat-trend up"><i class="fas fa-arrow-up"></i> 5.1%</div>
    </div>
    <div class="stat-value">Rp {{ number_format(abs($profitLoss), 0, ',', '.') }}</div>
    <div class="stat-label">Laba Bersih {{ $periodLabel ?? 'Hari Ini' }}</div>
  </div>
  <div class="stat-card fade-in" style="--stat-color: var(--teal);">
    <div class="stat-header">
      <div class="stat-icon teal"><i class="fas fa-store"></i></div>
      @if($totalBranchesCount - $activeBranchesCount > 0)
        <div class="stat-trend down"><i class="fas fa-arrow-down"></i> {{ $totalBranchesCount - $activeBranchesCount }} cabang</div>
      @else
        <div class="stat-trend up"><i class="fas fa-check"></i> Live</div>
      @endif
    </div>
    <div class="stat-value">{{ $activeBranchesCount }} / {{ $totalBranchesCount }}</div>
    <div class="stat-label">Cabang Aktif</div>
  </div>
</div>

<!-- TWO COLUMN: TABLE + BESTSELLER -->
<div class="two-col">

  <!-- TABEL PENJUALAN PER CABANG -->
  <section class="section-card fade-in">
    <div class="section-card-header">
      <div class="section-card-title">
        <i class="fas fa-ranking-star" style="color: var(--accent);"></i> Penjualan Per Cabang
        <span class="live-indicator"><span class="live-dot"></span> LIVE</span>
      </div>
      <div class="section-card-actions">
        <div class="filter-pills" id="dashboardPeriodPills">
          <a
            href="{{ route('superadmin.dashboard', array_merge(request()->except('page'), ['period' => 'today'])) }}"
            class="filter-pill {{ ($currentPeriod ?? 'today') === 'today' ? 'active' : '' }}"
            data-period="today"
          >Hari Ini</a>
          <a
            href="{{ route('superadmin.dashboard', array_merge(request()->except('page'), ['period' => 'week'])) }}"
            class="filter-pill {{ ($currentPeriod ?? 'today') === 'week' ? 'active' : '' }}"
            data-period="week"
          >Minggu</a>
          <a
            href="{{ route('superadmin.dashboard', array_merge(request()->except('page'), ['period' => 'month'])) }}"
            class="filter-pill {{ ($currentPeriod ?? 'today') === 'month' ? 'active' : '' }}"
            data-period="month"
          >Bulan</a>
        </div>
      </div>
    </div>
    <div class="section-card-body" style="position:relative;">
      <table class="data-table">
        <thead>
          <tr>
            <th>CABANG</th>
            <th>PENJUALAN</th>
            <th>TARGET</th>
            <th>STATUS</th>
          </tr>
        </thead>
        <tbody>
            @php
                $maxSales = max(1, (float) $branchSales->max('total_sales'));
                $branchColors = ['#D97706', '#2563EB', '#7C3AED', '#DC2626', '#6B7280'];
            @endphp
            @foreach ($branchSales as $index => $branch)
                @php
                    $percent = $maxSales > 0 ? ((float) $branch->total_sales / $maxSales) * 100 : 0;
                    $color = $branchColors[$index % count($branchColors)];
                @endphp
                <tr>
                    <td>
                        <div class="cell-branch">
                            <div class="cell-branch-dot" style="background: {{ $color }};">{{ strtoupper(substr($branch->name, 0, 2)) }}</div>
                            <div>
                                <div class="cell-branch-name">{{ $branch->name }}</div>
                                <div class="cell-branch-loc">Kode: {{ $branch->code }}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="cell-money positive">Rp {{ number_format($branch->total_sales, 0, ',', '.') }}</span></td>
                    <td>
                        <div class="cell-bar">
                            <div class="cell-bar-track">
                                <div class="cell-bar-fill" style="width:{{ $percent }}%; background: {{ $percent > 80 ? 'var(--green)' : ($percent > 50 ? 'var(--accent)' : 'var(--red)') }};"></div>
                            </div>
                            <span class="cell-bar-value" style="color: {{ $percent > 80 ? 'var(--green)' : ($percent > 50 ? 'var(--accent-dark)' : 'var(--red)') }};">{{ round($percent) }}%</span>
                        </div>
                    </td>
                    <td>
                        <span class="status-badge {{ $branch->total_sales > 0 ? 'active' : 'inactive' }}">
                            <span class="status-dot"></span> {{ $branch->total_sales > 0 ? 'Aktif' : 'Tutup' }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
      </table>
    </div>
  </section>

  <!-- MENU TERLARIS -->
  <section class="section-card fade-in">
    <div class="section-card-header">
      <div class="section-card-title"><i class="fas fa-fire" style="color: var(--accent);"></i> Menu Terlaris</div>
      <a href="{{ route('superadmin.menus.index') }}" class="btn-sm"><i class="fas fa-external-link-alt"></i> Detail</a>
    </div>
    <div class="section-card-body">
      <ul class="bestseller-list">
        @forelse($topMenus as $index => $menu)
            <li class="bestseller-item">
                <div class="bestseller-rank {{ $index == 0 ? 'gold' : ($index == 1 ? 'silver' : ($index == 2 ? 'bronze' : 'normal')) }}">
                    {{ $index + 1 }}
                </div>
                <div class="bestseller-info">
                    <div class="bestseller-name">{{ $menu->name }}</div>
                    <div class="bestseller-meta">Rp {{ number_format($menu->price, 0, ',', '.') }}</div>
                </div>
                <div class="bestseller-qty">
                    <div class="bestseller-qty-num">{{ number_format($menu->sold_qty, 0, ',', '.') }}</div>
                    <div class="bestseller-qty-label">terjual</div>
                </div>
            </li>
        @empty
            <li class="empty-state">
                <i class="fas fa-utensils"></i>
                <p>Belum ada data menu terlaris.</p>
            </li>
        @endforelse
      </ul>
    </div>
  </section>
</div>
