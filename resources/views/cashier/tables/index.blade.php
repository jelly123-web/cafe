@extends('cashier.layout')

@section('title', ($cafeBrand['name'] ?? config('app.name')) . ' - Meja Kasir')

@push('head')
    <style>
        .main-panel { padding: 2rem 2.5rem; overflow-y: auto; }
        .table-shell { max-width: 100%; }
        .panel { background: var(--bg-card); border: 1px solid var(--accent); border-radius: 20px; padding: 1.5rem 2rem; margin-bottom: 1.5rem; box-shadow: 0 4px 15px var(--shadow); }
        .page-title { font-family: 'Playfair Display', Georgia, serif; color: var(--primary); font-size: 1.8rem; margin: 0 0 0.5rem; }
        .page-desc { color: var(--text-muted); font-size: 0.95rem; margin: 0; }
        .alert { padding: 0.85rem 1.25rem; border-radius: 14px; margin-bottom: 1.25rem; font-weight: 500; font-size: 0.95rem; border: 1px solid transparent; }
        .ok { background: #E8F5E9; color: #558B2F; border-color: #C8E6C9; }
        .table-wrap { overflow-x: auto; }
        .status-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .status-table th, .status-table td { padding: 0.95rem 0.75rem; border-bottom: 1px solid var(--accent); vertical-align: middle; text-align: left; font-size: 0.95rem; }
        .status-table th { background: var(--bg-main); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); font-weight: 600; border-bottom: 2px solid var(--highlight); }
        .status-table tbody tr:hover { background-color: #FFFAF5; }
        .status-table th:nth-child(1), .status-table td:nth-child(1) { width: 14%; }
        .status-table th:nth-child(2), .status-table td:nth-child(2) { width: 35%; }
        .status-table th:nth-child(3), .status-table td:nth-child(3) { width: 16%; }
        .status-table th:nth-child(4), .status-table td:nth-child(4) { width: 35%; }
        .table-number { font-weight: 700; color: var(--primary); font-size: 1.05rem; display: inline-block; }
        .table-name { color: var(--text-main); font-weight: 500; }
        .tag { display: inline-flex; align-items: center; padding: 0.3rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px; text-transform: uppercase; white-space: nowrap; }
        .tag-empty { background: #E8F5E9; color: #558B2F; }
        .tag-occupied { background: #FFF3E0; color: #E65100; }
        .action-group { display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center; }
        .btn { border: 1px solid transparent; border-radius: 10px; padding: 0.5rem 0.95rem; cursor: pointer; font-weight: 600; font-family: inherit; font-size: 0.84rem; transition: all 0.2s ease; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; white-space: nowrap; }
        .btn-primary { background: var(--highlight); color: #fff; border: none; box-shadow: 0 2px 8px rgba(212, 163, 115, 0.3); }
        .btn-primary:hover { background: #c68b59; transform: translateY(-2px); }
        .btn-secondary { background: transparent; color: var(--primary); border-color: var(--secondary); }
        .btn-secondary:hover { border-color: var(--highlight); color: var(--highlight); background: #fffaf5; }
        .btn-danger { background: transparent; color: var(--loss); border-color: #FFCDD2; }
        .btn-danger:hover { background: #FFF0F0; border-color: var(--loss); }
        .action-group form { display: inline-flex; }
        .action-group .btn { min-width: 96px; }
        @media (max-width: 768px) {
            .main-panel { padding: 1.5rem 1rem; }
            .page-title { font-size: 1.5rem; }
            .panel { padding: 1.25rem; }
            .status-table { table-layout: auto; }
            .status-table th:nth-child(4), .status-table td:nth-child(4) { width: auto; }
            .action-group { flex-direction: column; align-items: flex-start; }
            .action-group .btn { min-width: 0; width: 100%; }
        }
    </style>
@endpush

@section('content')
    <div class="table-shell">
        <section class="panel">
            <h1 class="page-title">Meja</h1>
            <p class="page-desc">Melihat status meja (kosong/terisi), membuka meja baru, dan menutup meja setelah selesai.</p>
        </section>

        @if (session('success'))
            <div class="alert ok">{{ session('success') }}</div>
        @endif

        <section class="panel">
            <div class="table-wrap">
                <table class="status-table">
                    <thead>
                        <tr>
                            <th>Nomor Meja</th>
                            <th>Nama Meja</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tables as $table)
                            <tr>
                                <td><span class="table-number">{{ $table->number }}</span></td>
                                <td><span class="table-name">{{ $table->name }}</span></td>
                                <td>
                                    <span class="tag {{ $table->service_status === \App\Models\DiningTable::STATUS_OCCUPIED ? 'tag-occupied' : 'tag-empty' }}">
                                        {{ $table->service_status === \App\Models\DiningTable::STATUS_OCCUPIED ? 'Terisi' : 'Kosong' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="action-group">
                                        @if ($table->service_status === \App\Models\DiningTable::STATUS_EMPTY)
                                            <form method="POST" action="{{ route('cashier.tables.open', $table) }}">
                                                @csrf
                                                <button class="btn btn-primary" type="submit">Buka Meja</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('cashier.tables.close', $table) }}">
                                                @csrf
                                                <button class="btn btn-secondary" type="submit">Tutup Meja</button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('cashier.tables.destroy', $table) }}" onsubmit="return confirm('Hapus meja {{ $table->number }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-danger" type="submit">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align:center;color:var(--text-muted);">Belum ada meja. Tambahkan meja dari superadmin atau modul meja Anda.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </div>
@endsection
