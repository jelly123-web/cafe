@extends('cashier.layout')

@section('title', ($cafeBrand['name'] ?? config('app.name')) . ' - Struk Kasir')

@push('head')
    <style>
        .main-panel { padding: 2rem 2.5rem; overflow-y: auto; }
        .receipt-shell { max-width: 100%; }
        .panel { background: var(--bg-card); border: 1px solid var(--accent); border-radius: 20px; padding: 1.5rem 2rem; margin-bottom: 1.5rem; box-shadow: 0 4px 15px var(--shadow); }
        .page-title { font-family: 'Playfair Display', Georgia, serif; color: var(--primary); font-size: 1.8rem; margin: 0 0 0.5rem; }
        .page-desc { color: var(--text-muted); font-size: 0.95rem; margin: 0; }
        .alert { padding: 0.85rem 1.25rem; border-radius: 14px; margin-bottom: 1.25rem; font-weight: 500; font-size: 0.95rem; border: 1px solid transparent; }
        .ok { background: #E8F5E9; color: #558B2F; border-color: #C8E6C9; }
        .table-wrap { overflow-x: auto; margin: 0; }
        .receipt-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .receipt-table th, .receipt-table td { padding: 0.85rem 0.75rem; border-bottom: 1px solid var(--accent); vertical-align: top; text-align: left; font-size: 0.95rem; }
        .receipt-table th { background: var(--bg-main); font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); font-weight: 600; border-bottom: 2px solid var(--highlight); }
        .receipt-table tbody tr:hover { background-color: #FFFAF5; }
        .receipt-table th:nth-child(1), .receipt-table td:nth-child(1) { width: 16%; }
        .receipt-table th:nth-child(2), .receipt-table td:nth-child(2) { width: 9%; }
        .receipt-table th:nth-child(3), .receipt-table td:nth-child(3) { width: 15%; }
        .receipt-table th:nth-child(4), .receipt-table td:nth-child(4) { width: 11%; }
        .receipt-table th:nth-child(5), .receipt-table td:nth-child(5) { width: 49%; }
        .order-code { font-weight: 700; color: var(--primary); word-break: break-word; display: inline-block; }
        .order-total { font-weight: 600; white-space: nowrap; }
        .tag { display: inline-flex; align-items: center; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 700; letter-spacing: 0.5px; }
        .tag-paid { background: #E8F5E9; color: #558B2F; }
        .tag-unpaid { background: #FFF3E0; color: #E65100; }
        .tag-cancelled { background: #FFEBEE; color: #C62828; }
        .action-group { display: flex; align-items: center; gap: 0.65rem; flex-wrap: wrap; }
        .btn { border: 1px solid transparent; border-radius: 12px; padding: 0.65rem 1.2rem; cursor: pointer; font-weight: 600; font-family: inherit; font-size: 0.9rem; transition: all 0.2s ease; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; }
        .btn-sm { padding: 0.45rem 0.85rem; font-size: 0.85rem; border-radius: 8px; }
        .btn-secondary { background: transparent; color: var(--primary); border-color: var(--accent); }
        .btn-secondary:hover { border-color: var(--highlight); color: var(--highlight); background: #fffaf5; }
        .btn-primary { background: var(--highlight); color: #fff; border: none; box-shadow: 0 2px 8px rgba(212, 163, 115, 0.3); }
        .btn-primary:hover { background: #c68b59; transform: translateY(-2px); }
        .btn-danger { background: transparent; color: #C62828; border-color: #FFCDD2; }
        .btn-danger:hover { background: #FFEBEE; }
        .action-group form { display: inline-flex; align-items: center; }
        .inline-form { display: flex; gap: 0.45rem; align-items: center; flex-wrap: nowrap; }
        .input-field { border: 1px solid var(--accent); border-radius: 8px; padding: 0.45rem 0.75rem; background-color: var(--bg-card); color: var(--text-main); font-family: inherit; font-size: 0.85rem; outline: none; transition: border-color 0.2s ease, box-shadow 0.2s ease; width: 128px; }
        .input-field:focus { border-color: var(--highlight); box-shadow: 0 0 0 3px rgba(212, 163, 115, 0.15); }
        .pagination-wrap { margin-top: 1.5rem; }
        .pagination-meta { color: var(--text-muted); font-size: .9rem; margin-bottom: .75rem; text-align: center; }
        .pagination-links { display: flex; gap: .5rem; justify-content: center; flex-wrap: wrap; }
        .pagination-link, .pagination-dots { display: inline-flex; align-items: center; justify-content: center; min-width: 36px; height: 36px; border-radius: 10px; font-size: .9rem; font-weight: 600; text-decoration: none; border: 1px solid var(--accent); color: var(--primary); padding: 0 .65rem; }
        .pagination-link:hover { background: var(--highlight); color: #fff; border-color: var(--highlight); }
        .pagination-link.active { background: var(--highlight); color: #fff; border-color: var(--highlight); box-shadow: 0 2px 8px rgba(212, 163, 115, 0.3); }
        .pagination-link.disabled { color: var(--secondary); pointer-events: none; }
        @media (max-width: 768px) {
            .main-panel { padding: 1.5rem 1rem; }
            .page-title { font-size: 1.5rem; }
            .panel { padding: 1.25rem; }
            .receipt-table { table-layout: auto; }
            .receipt-table th:nth-child(5), .receipt-table td:nth-child(5) { width: auto; }
            .action-group { display: flex; flex-direction: column; align-items: flex-start; }
            .inline-form { width: 100%; flex-wrap: wrap; }
            .input-field { flex: 1; width: auto; min-width: 0; }
        }
    </style>
@endpush

@section('content')
    <div class="receipt-shell">
        <section class="panel">
            <h1 class="page-title">Struk</h1>
            <p class="page-desc">Cetak struk dan kirim struk digital.</p>
        </section>

        @if (session('success'))
            <div class="alert ok">{{ session('success') }}</div>
        @endif

        <section class="panel">
            <div class="table-wrap">
                <table class="receipt-table">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Meja</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td><span class="order-code">{{ $order->code }}</span></td>
                                <td>{{ $order->table?->number ?? '-' }}</td>
                                <td><span class="order-total">Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</span></td>
                                <td>
                                    @php
                                        $statusClass = $order->status === \App\Models\SaleTransaction::STATUS_PAID ? 'tag-paid' : ($order->status === \App\Models\SaleTransaction::STATUS_CANCELLED ? 'tag-cancelled' : 'tag-unpaid');
                                    @endphp
                                    <span class="tag {{ $statusClass }}">{{ $order->statusLabel() }}</span>
                                </td>
                                <td>
                                    <div class="action-group">
                                        <a class="btn btn-sm btn-secondary" href="{{ route('cashier.receipts.print', $order) }}" target="_blank">Cetak</a>
                                        <form method="POST" action="{{ route('cashier.receipts.send', $order) }}" class="inline-form">
                                            @csrf
                                            <input type="text" name="destination" placeholder="No WA" required class="input-field" maxlength="12">
                                            <button class="btn btn-sm btn-primary" type="submit">Kirim</button>
                                        </form>
                                        <form method="POST" action="{{ route('cashier.receipts.destroy', $order) }}" onsubmit="return confirm('Hapus data struk {{ $order->code }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" type="submit">Hapus Data</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{ $orders->links('components.pagination') }}
        </section>
    </div>
@endsection
