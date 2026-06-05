@forelse ($orders as $order)
    @php
        $status = (string) $order->status;
        $activeStep = match ($status) {
            \App\Models\SaleTransaction::STATUS_READY => 4,
            \App\Models\SaleTransaction::STATUS_COMPLETED, \App\Models\SaleTransaction::STATUS_PAID => 5,
            default => 3,
        };
        $displayStatus = match ($status) {
            \App\Models\SaleTransaction::STATUS_READY => 'DIKIRIM',
            \App\Models\SaleTransaction::STATUS_COMPLETED, \App\Models\SaleTransaction::STATUS_PAID => 'SELESAI',
            \App\Models\SaleTransaction::STATUS_CANCELLED => 'DIBATALKAN',
            default => 'SEDANG DISIAPKAN',
        };
        $progress = match ($activeStep) {
            1 => 0,
            2 => 25,
            3 => 50,
            4 => 75,
            5 => 100,
            default => 0,
        };
        $steps = [
            ['label' => 'Dipesan', 'icon' => 'fa-check'],
            ['label' => 'Dikonfirmasi', 'icon' => 'fa-check'],
            ['label' => 'Disiapkan', 'icon' => 'fa-fire-burner'],
            ['label' => 'Dikirim', 'icon' => 'fa-motorcycle'],
            ['label' => 'Selesai', 'icon' => 'fa-house-chimney'],
        ];
    @endphp
    <article class="order-status-card status-card">
        <div class="status-order-info">
            <div>
                <div class="order-id">Pesanan <span>#{{ $order->code }}</span></div>
                <div class="order-time"><i class="far fa-clock"></i> {{ optional($order->created_at)->format('d M Y, H:i') }}</div>
            </div>
            <div class="status-badge preparing"><span class="dot"></span> {{ $displayStatus }}</div>
        </div>
        <div class="status-timeline" style="--progress: {{ $progress }}%;">
            @foreach ($steps as $index => $step)
                @php $stepNumber = $index + 1; @endphp
                <div class="step {{ $stepNumber < $activeStep ? 'completed' : ($stepNumber === $activeStep ? 'active' : '') }}">
                    <div class="step-circle"><i class="fas {{ $stepNumber < $activeStep ? 'fa-check' : $step['icon'] }}"></i></div>
                    <span class="step-label">{{ $step['label'] }}</span>
                </div>
            @endforeach
        </div>
        <div class="status-eta">
            <i class="fas fa-truck-fast"></i>
            <div class="status-eta-text">Estimasi tiba <strong>25-35 menit</strong> dari sekarang</div>
        </div>
    </article>
@empty
    <div class="empty">Belum ada pesanan dari meja ini.</div>
@endforelse
