@extends('superadmin.layout')

@section('title', 'Meja')
@section('page_title', 'Meja Cafe')
@section('page_description', 'Lihat, tambah, dan kelola meja yang dipakai pelanggan untuk scan QR.')

@push('head')
    <style>
        .table-toolbar {
            background: var(--bg-card);
            border: 1px solid var(--accent);
            border-radius: 20px;
            box-shadow: 0 4px 15px var(--shadow);
            padding: 1.25rem 1.5rem;
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }

        .table-toolbar h2 {
            font-family: 'Playfair Display', Georgia, serif;
            color: var(--primary);
            margin: 0 0 0.25rem;
            font-size: 1.2rem;
        }

        .table-toolbar p {
            margin: 0;
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .table-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1.5rem;
        }

        .table-card {
            background: var(--bg-card);
            border: 1px solid var(--accent);
            border-radius: 20px;
            box-shadow: 0 4px 15px var(--shadow);
            padding: 1.25rem;
            display: grid;
            gap: 1.25rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
        }

        .table-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px var(--shadow);
            border-color: rgba(212, 163, 115, 0.3);
        }

        .table-card-head {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: start;
        }

        .table-card h3 {
            font-family: 'Playfair Display', Georgia, serif;
            color: var(--primary);
            margin-bottom: 0.25rem;
            font-size: 1.2rem;
        }

        .table-pill {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 999px;
            background: #efebe9;
            color: var(--primary);
            font-size: 0.8rem;
            font-weight: 600;
        }

        .qr-box {
            display: grid;
            gap: 0.75rem;
            justify-items: start;
            padding-top: 0.75rem;
            border-top: 1px dashed var(--accent);
        }

        .qr-box svg {
            width: 140px;
            height: 140px;
            padding: 8px;
            background: #fff;
            border-radius: 12px;
            border: 2px dashed var(--accent);
            box-shadow: 0 2px 8px var(--shadow);
        }

        .qr-box small {
            color: var(--text-muted);
            font-size: 0.8rem;
        }

        .table-actions {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .primary-link,
        .secondary-link,
        .danger-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            padding: 0.5rem 1rem;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            font-size: 0.85rem;
            font-family: inherit;
            transition: all 0.2s ease;
            border: 1px solid transparent;
        }

        .primary-link {
            background: var(--highlight);
            color: #fff;
            box-shadow: 0 2px 8px rgba(212, 163, 115, 0.3);
            border: none;
        }

        .primary-link:hover {
            background: #c68b59;
            transform: translateY(-2px);
        }

        .secondary-link {
            background: transparent;
            color: var(--primary);
            border-color: var(--accent);
        }

        .secondary-link:hover {
            border-color: var(--highlight);
            color: var(--highlight);
            background: #fffaf5;
        }

        .danger-link {
            background: transparent;
            color: var(--loss);
            border-color: #f8d7da;
        }

        .danger-link:hover {
            background: #fff0f0;
            border-color: var(--loss);
        }

        .pagination-area {
            margin-top: 1.5rem;
        }

        .table-empty {
            grid-column: 1 / -1;
            color: var(--text-muted);
            padding: 1.25rem;
        }

        .table-actions form {
            margin: 0;
        }

        @media (max-width: 768px) {
            .table-grid {
                grid-template-columns: 1fr;
            }

            .table-toolbar {
                padding: 1rem 1.1rem;
            }

            .table-card {
                padding: 1rem;
            }
        }
    </style>
@endpush

@section('content')
    <div class="table-toolbar">
        <div>
            <h2>Daftar Meja</h2>
            <p>Setiap meja punya QR unik untuk scan pelanggan.</p>
        </div>
        <div style="display:flex;gap:0.75rem;flex-wrap:wrap;">
            <a class="primary-link" href="{{ route('superadmin.tables.create') }}">+ Tambah Meja</a>
            <form method="POST" action="{{ route('superadmin.tables.destroy-all') }}" onsubmit="return confirm('Hapus semua meja? Aksi ini tidak bisa dibatalkan.')">
                @csrf
                @method('DELETE')
                <button type="submit" class="danger-link">Hapus Semua Meja</button>
            </form>
        </div>
    </div>

    <div class="table-grid">
        @forelse ($tables as $table)
            <article class="table-card">
                <div class="table-card-head">
                    <div>
                        <span class="table-pill">Meja {{ $table->number }}</span>
                        <h3>{{ $table->name }}</h3>
                        <p style="margin:0;color:var(--text-muted);font-size:0.9rem;">Status: {{ $table->is_active ? 'Aktif' : 'Nonaktif' }}</p>
                        <p style="margin:0;color:var(--text-muted);font-size:0.9rem;">Total transaksi: {{ $table->sales_count }}</p>
                    </div>
                    <div class="table-pill">{{ $table->qr_token }}</div>
                </div>

                <div class="qr-box">
                    {!! app('qrcode')->format('svg')->size(180)->margin(1)->generate(route('tables.show', $table)) !!}
                    <small>Scan QR untuk membuka halaman pelanggan meja ini.</small>
                </div>

                <div class="table-actions">
                    <a class="secondary-link" href="{{ route('tables.show', $table) }}" target="_blank" rel="noopener">Buka Halaman</a>
                    <a class="secondary-link" href="{{ route('superadmin.tables.edit', $table) }}">Edit</a>
                    <form method="POST" action="{{ route('superadmin.tables.destroy', $table) }}" onsubmit="return confirm('Hapus meja ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="danger-link">Hapus</button>
                    </form>
                </div>
            </article>
        @empty
            <div class="table-card table-empty">
                <div>Belum ada meja.</div>
            </div>
        @endforelse
    </div>

    <div class="pagination-area">
        {{ $tables->links('components.pagination') }}
    </div>
@endsection
