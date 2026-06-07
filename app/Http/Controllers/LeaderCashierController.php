<?php

namespace App\Http\Controllers;

use App\Models\CashFlowEntry;
use App\Models\DiningTable;
use App\Models\Menu;
use App\Models\CashierCart;
use App\Models\CashierCartItem;
use App\Models\SaleTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class LeaderCashierController extends Controller
{
    public function transactions(Request $request): View
    {
        $user = $request->user();
        $role = strtolower(trim((string) $user?->role));
        abort_unless($user && in_array($role, ['leader_cashier', 'kasir', 'superadmin'], true), 403);

        $catalog = Cache::remember('leader.cashier.transaction.catalog', now()->addMinutes(1), function () {
            return [
                'menus' => Menu::query()->where('code', '!=', 'A01')->orderBy('name')->get(),
                'addonMenus' => Menu::query()
                    ->whereIn('code', ['A01'])
                    ->orderBy('name')
                    ->get(),
                'tables' => DiningTable::query()->where('is_active', true)->orderByRaw('CAST(number AS UNSIGNED) ASC')->get(),
            ];
        });

        $orders = SaleTransaction::query()
            ->with(['table', 'items.menu'])
            ->whereDate('sold_at', Carbon::today())
            ->orderByDesc('sold_at')
            ->get();

        return view('leader_cashier.transactions.index', [
            'menus' => $catalog['menus'],
            'addonMenus' => $catalog['addonMenus'],
            'tables' => $catalog['tables'],
            'cart' => $this->cartRows((int) $user->id),
            'orders' => $orders,
            'canCancelOrders' => true,
        ]);
    }

    public function index(Request $request): View
    {
        return view('leader_cashier.index', $this->dashboardData($request));
    }

    public function live(Request $request): JsonResponse
    {
        $data = $this->dashboardData($request);

        return response()->json([
            'metrics' => [
                'total_uang_masuk' => 'Rp ' . number_format((float) $data['totalUangMasuk'], 0, ',', '.'),
                'total_transaksi_hari_ini' => number_format((int) $data['totalTransaksiHariIni'], 0, ',', '.'),
                'total_kas_masuk' => 'Rp ' . number_format((float) $data['totalKasMasuk'], 0, ',', '.'),
                'selisih_kas' => 'Rp ' . number_format((float) $data['selisihKas'], 0, ',', '.'),
            ],
            'cash_flow' => [
                'rows_html' => view('leader_cashier.partials.cash-flow-rows', [
                    'laporanKas' => $data['laporanKas'],
                ])->render(),
                'pagination_html' => $data['laporanKas']->links('components.pagination')->render(),
            ],
            'transactions' => [
                'rows_html' => view('leader_cashier.partials.transaction-rows', [
                    'riwayatTransaksi' => $data['riwayatTransaksi'],
                    'hasStatus' => $data['hasStatus'],
                ])->render(),
                'pagination_html' => $data['riwayatTransaksi']->links('components.pagination')->render(),
            ],
        ]);
    }

    public function storeCashFlow(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:in,out'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'description' => ['required', 'string', 'max:255'],
            'happened_at' => ['required', 'date'],
        ]);

        CashFlowEntry::query()->create([
            'type' => $data['type'],
            'amount' => $data['amount'],
            'description' => $data['description'],
            'happened_at' => $data['happened_at'],
            'created_by' => $request->user()?->id,
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['message' => 'Kas masuk/keluar berhasil dicatat.']);
        }

        return back()->with('success', 'Kas masuk/keluar berhasil dicatat.');
    }

    public function destroyCashFlow(Request $request, CashFlowEntry $entry): JsonResponse|RedirectResponse
    {
        $entry->delete();

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['message' => 'Data kas berhasil dihapus.']);
        }

        return back()->with('success', 'Data kas berhasil dihapus.');
    }

    private function dashboardData(Request $request): array
    {
        $today = Carbon::today();
        $cashPage = (int) $request->query('cash_page', 1);
        $trxPage = (int) $request->query('trx_page', 1);

        return Cache::remember(
            'leader.cashier.dashboard.' . $today->format('Y-m-d') . '.cash.' . $cashPage . '.trx.' . $trxPage,
            now()->addSeconds(10),
            function () use ($request, $today) {
                $hasStatus = Schema::hasColumn('sale_transactions', 'status');
                $hasMethod = Schema::hasColumn('sale_transactions', 'payment_method');

                $baseToday = SaleTransaction::query()->whereDate('sold_at', $today);
                $paidToday = SaleTransaction::query()->whereDate('sold_at', $today);
                if ($hasStatus) {
                    $paidToday->where('status', 'paid');
                }

                $totalUangMasuk = (float) $paidToday->sum('total_amount');
                $totalTransaksiHariIni = (int) $paidToday->count();

                $metodePembayaran = collect();
                if ($hasMethod) {
                    $metodePembayaran = (clone $paidToday)
                        ->selectRaw('payment_method, COUNT(*) as total')
                        ->groupBy('payment_method')
                        ->orderByDesc('total')
                        ->get();
                }

                $cashIn = CashFlowEntry::query()
                    ->where('type', 'in')
                    ->whereDate('happened_at', $today)
                    ->sum('amount');
                $cashOut = CashFlowEntry::query()
                    ->where('type', 'out')
                    ->whereDate('happened_at', $today)
                    ->sum('amount');

                $selisihKas = $totalUangMasuk + (float) $cashIn - (float) $cashOut;

                $riwayatTransaksi = SaleTransaction::query()
                    ->with('table')
                    ->orderByDesc('sold_at')
                    ->paginate(5, ['*'], 'trx_page')
                    ->appends($request->except('trx_page'));

                $laporanKas = CashFlowEntry::query()
                    ->with('user')
                    ->orderByDesc('happened_at')
                    ->paginate(10, ['*'], 'cash_page')
                    ->appends($request->except('cash_page'));

                return [
                    'today' => $today,
                    'hasStatus' => $hasStatus,
                    'hasMethod' => $hasMethod,
                    'totalUangMasuk' => $totalUangMasuk,
                    'totalTransaksiHariIni' => $totalTransaksiHariIni,
                    'metodePembayaran' => $metodePembayaran,
                    'selisihKas' => $selisihKas,
                    'riwayatTransaksi' => $riwayatTransaksi,
                    'laporanKas' => $laporanKas,
                    'totalKasMasuk' => (float) $cashIn,
                    'totalKasKeluar' => (float) $cashOut,
                    'totalTransaksiSemua' => (int) $baseToday->count(),
                ];
            }
        );
    }

    private function cartModel(int $userId): CashierCart
    {
        return CashierCart::query()->firstOrCreate(['user_id' => $userId]);
    }

    private function cartRows(int $userId): array
    {
        $cart = $this->cartModel($userId);
        $items = $cart->items()->with('menu')->get();

        $rows = [];
        /** @var CashierCartItem $item */
        foreach ($items as $item) {
            $lineTotal = (float) $item->unit_price * (int) $item->qty;
            $rows[(string) $item->menu_id] = [
                'menu_id' => $item->menu_id,
                'code' => $item->menu?->code,
                'name' => $item->menu?->name ?? 'Menu',
                'unit_price' => (float) $item->unit_price,
                'unit_cost' => (float) $item->unit_cost,
                'qty' => (int) $item->qty,
                'line_total' => $lineTotal,
                'is_addon' => strtolower((string) $item->menu?->code) === 'a01',
            ];
        }

        return $rows;
    }
}
