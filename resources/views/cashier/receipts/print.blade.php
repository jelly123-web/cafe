<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk {{ $order->code }}</title>
    <style>
        body { font-family: monospace; padding: 24px; }
        .wrap { max-width: 360px; margin: 0 auto; }
        .line { display: flex; justify-content: space-between; }
        @media print { .print-btn { display:none; } }
    </style>
</head>
<body>
    <div class="wrap">
        <button class="print-btn" onclick="window.print()">Cetak</button>
        <h3 style="text-align:center;margin-bottom:4px;">{{ $cafeBrand['name'] ?? config('app.name') }}</h3>
        <p style="text-align:center;margin-top:0;">Struk {{ $order->code }}</p>
        <hr>
        <div class="line"><span>Meja</span><span>{{ $order->table?->number ?? '-' }}</span></div>
        <div class="line"><span>Waktu</span><span>{{ $order->sold_at?->format('d/m/Y H:i') }}</span></div>
        <div class="line"><span>Status</span><span>{{ $order->statusLabel() }}</span></div>
        <hr>
        @foreach ($order->items as $item)
            <div class="line"><span>{{ $item->qty }}x {{ $item->menu?->name }}</span><span>{{ number_format((float) $item->line_total, 0, ',', '.') }}</span></div>
        @endforeach
        <hr>
        <div class="line"><strong>Total</strong><strong>Rp {{ number_format((float) $order->total_amount, 0, ',', '.') }}</strong></div>
    </div>
</body>
</html>
