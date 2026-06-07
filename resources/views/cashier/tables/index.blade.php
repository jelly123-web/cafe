@extends('cashier.layout')

@section('title', 'Meja Cafe')
@section('page_title', 'Meja Cafe')
@section('page_icon', 'fas fa-chair')
@section('page_description', 'Lihat, tambah, dan kelola meja yang dipakai pelanggan untuk scan QR.')

@push('head')
    <style>
    /* ===== TABLE TOOLBAR ===== */
    .table-toolbar {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      padding: 18px 24px;
      display: flex;
      justify-content: space-between;
      gap: 16px;
      align-items: center;
      flex-wrap: wrap;
      margin-bottom: 20px;
    }

    .toolbar-actions { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }

    /* ===== SEARCH BOX ===== */
    .search-box { display: flex; gap: 6px; }
    .search-box input {
      border: 1.5px solid var(--border);
      background: var(--white);
      padding: 10px 16px;
      border-radius: var(--radius-sm);
      min-width: 280px;
      font-family: var(--font);
      font-size: 13px;
      font-weight: 500;
      color: var(--fg);
      outline: none;
      transition: all var(--transition);
    }
    .search-box input::placeholder { color: var(--muted); }
    .search-box input:focus { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.1); }

    /* ===== BUTTONS ===== */
    .primary-link {
      display: inline-flex; align-items: center; gap: 6px;
      background: var(--accent); color: white; text-decoration: none;
      padding: 10px 20px; border-radius: var(--radius-sm);
      font-weight: 700; font-size: 13px; border: none; cursor: pointer;
      transition: all var(--transition); font-family: var(--font);
    }
    .primary-link:hover { background: var(--accent-dark); transform: translateY(-1px); box-shadow: 0 4px 14px rgba(217,119,6,0.25); }

    .secondary-link {
      display: inline-flex; align-items: center; gap: 5px;
      background: transparent; color: var(--fg-secondary); text-decoration: none;
      padding: 8px 14px; border-radius: var(--radius-sm);
      font-weight: 700; font-size: 12px; border: 1.5px solid var(--border); cursor: pointer;
      transition: all var(--transition); font-family: var(--font);
    }
    .secondary-link:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-light); }

    .danger-link {
      display: inline-flex; align-items: center; gap: 6px;
      background: transparent; color: var(--red); border: 1.5px solid #FECACA;
      padding: 9px 18px; border-radius: var(--radius-sm);
      font-weight: 700; font-size: 13px; cursor: pointer;
      transition: all var(--transition); font-family: var(--font);
    }
    .danger-link:hover { background: var(--red-light); border-color: var(--red); }

    /* ===== TABLE GRID ===== */
    .table-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
      gap: 16px;
    }

    /* ===== TABLE CARD ===== */
    .table-card {
      background: var(--white);
      border: 1px solid var(--border);
      border-radius: var(--radius-lg);
      padding: 20px;
      display: flex;
      flex-direction: column;
      gap: 16px;
      transition: all 0.25s ease;
      animation: cardIn 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    @keyframes cardIn {
      from { opacity: 0; transform: translateY(8px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .table-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: var(--accent);
      opacity: 0;
      transition: opacity var(--transition);
    }

    .table-card:hover {
      border-color: transparent;
      box-shadow: var(--shadow-lg);
      transform: translateY(-2px);
    }

    .table-card:hover::before {
      opacity: 1;
    }

    /* ===== TABLE CARD HEAD ===== */
    .table-card-head {
      display: flex;
      justify-content: space-between;
      gap: 12px;
      align-items: flex-start;
    }

    .table-card h3 {
      font-size: 15px;
      font-weight: 800;
      color: var(--fg);
      margin: 0;
      letter-spacing: -0.2px;
    }

    /* ===== TABLE PILL ===== */
    .table-pill {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 3px 10px;
      border-radius: var(--radius-full);
      font-size: 11px;
      font-weight: 700;
      letter-spacing: 0.3px;
      margin-bottom: 4px;
    }

    .table-pill.active {
      background: var(--green-light);
      color: var(--green);
    }

    .table-pill.inactive {
      background: #F3F4F6;
      color: var(--muted);
    }

    .table-pill .pill-dot {
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background: currentColor;
    }

    .table-pill.active .pill-dot {
      animation: dotPulse 2s infinite;
    }

    @keyframes dotPulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.3; }
    }

    /* ===== TABLE STATUS INFO ===== */
    .table-status-row {
      display: flex;
      align-items: center;
      gap: 12px;
      flex-wrap: wrap;
    }

    .table-stat {
      display: flex;
      align-items: center;
      gap: 5px;
      font-size: 12px;
      color: var(--muted);
      font-weight: 500;
    }

    .table-stat i { font-size: 12px; }

    /* ===== QR BOX ===== */
    .qr-box {
      display: flex;
      flex-direction: column;
      gap: 8px;
      align-items: flex-start;
      padding-top: 14px;
      border-top: 1px dashed var(--border);
    }

    .qr-preview-wrap {
      display: flex;
      align-items: flex-start;
      gap: 14px;
      width: 100%;
    }

    .qr-preview {
      width: 110px;
      height: 110px;
      padding: 6px;
      background: var(--white);
      border-radius: var(--radius-sm);
      border: 1.5px solid var(--border);
      display: block;
      object-fit: contain;
      flex-shrink: 0;
      transition: all var(--transition);
    }

    .qr-preview:hover {
      border-color: var(--accent);
      transform: scale(1.03);
    }

    .qr-info {
      display: flex;
      flex-direction: column;
      gap: 4px;
      min-width: 0;
    }

    .qr-info small {
      color: var(--muted);
      font-size: 12px;
      line-height: 1.5;
    }

    .qr-token-label {
      font-family: 'SF Mono', 'Fira Code', monospace;
      font-size: 11px;
      color: var(--fg-secondary);
      background: var(--bg);
      padding: 4px 8px;
      border-radius: 4px;
      word-break: break-all;
      border: 1px solid var(--border-light);
    }

    /* ===== TABLE ACTIONS ===== */
    .table-actions {
      display: flex;
      gap: 6px;
      flex-wrap: wrap;
      padding-top: 12px;
      border-top: 1px solid var(--border-light);
    }

    .table-actions form { margin: 0; display: inline-flex; }

    /* ===== EMPTY STATE ===== */
    .table-empty {
      grid-column: 1 / -1;
      text-align: center;
      padding: 48px 24px;
      color: var(--muted);
      font-size: 14px;
      background: #fff;
      border-radius: var(--radius-lg);
    }

    .table-empty::before {
      content: '\f0fc';
      font-family: 'Font Awesome 6 Free';
      font-weight: 900;
      display: block;
      font-size: 36px;
      margin-bottom: 10px;
      color: var(--border);
    }

    .table-empty em { font-style: normal; font-weight: 700; color: var(--fg-secondary); }

    /* ===== PAGINATION ===== */
    .pagination-area { margin-top: 20px; }
    .pagination-wrap { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; }
    .pagination-meta { font-size: 12px; color: var(--muted); font-weight: 500; }
    .pagination-links { display: flex; gap: 4px; flex-wrap: wrap; }
    .pagination-link, .pagination-dots {
      display: inline-flex; align-items: center; justify-content: center;
      min-width: 34px; height: 34px; border-radius: var(--radius-sm);
      font-size: 12px; font-weight: 600; text-decoration: none;
      border: 1px solid var(--border); color: var(--fg-secondary);
      padding: 0 8px; background: var(--white); transition: all var(--transition);
      font-family: var(--font); cursor: pointer;
    }
    .pagination-link:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-light); }
    .pagination-link.active { background: var(--accent); border-color: var(--accent); color: white; }
    .pagination-link.disabled { opacity: 0.35; pointer-events: none; }
    </style>
@endpush

@section('content')
    <!-- TOOLBAR -->
    <div class="table-toolbar fade-in">
        <div class="toolbar-actions">
            <div class="search-box">
                <input type="text" id="tableSearchInput" placeholder="Cari meja (cth: Meja 1)..." autocomplete="off">
            </div>
        </div>
        <div class="toolbar-actions">
            <form method="POST" action="{{ route('cashier.tables.destroy', 0) }}" onsubmit="return confirm('Hapus semua meja? Aksi ini tidak bisa dibatalkan.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="danger-link"><i class="fas fa-trash-can"></i> Hapus Semua Meja</button>
            </form>
        </div>
    </div>

    <!-- TABLE GRID -->
    <div class="table-grid fade-in">
        @forelse ($tables as $table)
            <article class="table-card" data-table-id="{{ $table->id }}">
                <div class="table-card-head">
                    <div>
                        <span class="table-pill {{ $table->service_status === 'occupied' ? 'inactive' : 'active' }}">
                            <span class="pill-dot"></span> Meja {{ $table->number }}
                        </span>
                        <h3>{{ $table->name }}</h3>
                    </div>
                </div>
                <div class="table-status-row">
                    <span class="table-stat">
                        <i class="fas {{ $table->service_status === 'empty' ? 'fa-circle-check' : 'fa-circle-xmark' }}" style="color: {{ $table->service_status === 'empty' ? 'var(--green)' : 'var(--muted)' }};"></i> 
                        {{ $table->service_status === 'empty' ? 'Kosong' : 'Terisi' }}
                    </span>
                </div>
                
                <div class="table-actions">
                    @if ($table->service_status === 'empty')
                        <form method="POST" action="{{ route('cashier.tables.open', $table) }}">
                            @csrf
                            <button class="primary-link" type="submit"><i class="fas fa-door-open"></i> Buka</button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('cashier.tables.close', $table) }}">
                            @csrf
                            <button class="secondary-link" type="submit"><i class="fas fa-door-closed"></i> Tutup</button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('cashier.tables.destroy', $table) }}" onsubmit="return confirm('Hapus meja {{ $table->number }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="danger-link"><i class="fas fa-trash"></i> Hapus</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="table-empty" id="emptyState">
                <em>Belum ada meja.</em>
            </div>
        @endforelse
    </div>

    <!-- PAGINATION -->
    <div class="pagination-area fade-in">
        {{ $tables->links('components.pagination') }}
    </div>
@endsection
