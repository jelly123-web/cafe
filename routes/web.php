<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\SuperadminAccessController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KitchenDashboardController;
use App\Http\Controllers\CashierOrderController;
use App\Http\Controllers\CashierTransactionController;
use App\Http\Controllers\CashierPaymentController;
use App\Http\Controllers\CashierReceiptController;
use App\Http\Controllers\CashierTableController;
use App\Http\Controllers\CashierReportController;
use App\Http\Controllers\SuperadminDashboardController;
use App\Http\Controllers\SuperadminMenuCategoryController;
use App\Http\Controllers\SuperadminMenuController;
use App\Http\Controllers\SuperadminEmployeeController;
use App\Http\Controllers\SuperadminPayrollController;
use App\Http\Controllers\SuperadminReportController;
use App\Http\Controllers\SuperadminSystemSettingController;
use App\Http\Controllers\SuperadminTableController;
use App\Http\Controllers\PublicTableMenuController;
use App\Http\Controllers\SuperadminUserController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LeaderCashierController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route(match (auth()->user()->role) {
            'superadmin' => 'superadmin.dashboard',
            'kitchen' => 'kitchen.dashboard',
            'inventory' => 'inventory.index',
            'leader_cashier' => 'leader-cashier.index',
            default => 'dashboard',
        })
        : redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'create'])->name('login');
Route::post('/login', [AuthController::class, 'store'])->name('login.store');
Route::get('/auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);
Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
Route::get('/brand-logo', function () {
    $path = \App\Models\SystemSetting::getValue('cafe_logo');

    abort_unless($path && \Illuminate\Support\Facades\Storage::disk('public')->exists($path), 404);

    return \Illuminate\Support\Facades\Storage::disk('public')
        ->response($path, null, ['Cache-Control' => 'public, max-age=300']);
})->name('brand.logo');
Route::get('/meja/{table:qr_token}', [PublicTableMenuController::class, 'show'])->name('tables.show');
Route::post('/meja/{table:qr_token}/pesan', [PublicTableMenuController::class, 'order'])->name('tables.order');
Route::get('/meja/{table:qr_token}/orders/live', [PublicTableMenuController::class, 'liveOrders'])->name('tables.orders.live');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});

Route::prefix('dapur')
    ->name('kitchen.')
    ->middleware(['auth', 'role:kitchen,superadmin,admin,leader_cashier'])
    ->group(function () {
        Route::get('/dashboard', [KitchenDashboardController::class, 'dashboard'])->name('dashboard');
        Route::get('/dashboard/live', [KitchenDashboardController::class, 'dashboardLive'])->name('dashboard.live');
        Route::get('/pesanan', [KitchenDashboardController::class, 'index'])->name('orders.index');
        Route::get('/pesanan/live', [KitchenDashboardController::class, 'liveOrders'])->name('orders.live');
        Route::put('/pesanan/{order}/status', [KitchenDashboardController::class, 'updateStatus'])->name('orders.status');
        Route::delete('/pesanan/{order}', [KitchenDashboardController::class, 'destroy'])->name('orders.destroy');
        Route::get('/menu-habis', [KitchenDashboardController::class, 'menus'])->name('menus.index');
        Route::get('/menu-habis/live', [KitchenDashboardController::class, 'menusLive'])->name('menus.live');
        Route::put('/menu-habis/{menu}', [KitchenDashboardController::class, 'toggleMenuStock'])->name('menus.toggle');
        Route::get('/riwayat', [KitchenDashboardController::class, 'history'])->name('history.index');
        Route::get('/riwayat/live', [KitchenDashboardController::class, 'historyLive'])->name('history.live');
    });

Route::prefix('kasir')
    ->name('cashier.')
    ->middleware(['auth', 'role:kasir,staff,admin,superadmin,leader_cashier'])
    ->group(function () {
        Route::get('/pesanan', [CashierOrderController::class, 'index'])->name('orders.index');
        Route::get('/pesanan/live', [CashierOrderController::class, 'live'])->name('orders.live');
        Route::post('/pesanan/{order}/cancel', [CashierOrderController::class, 'cancel'])->name('orders.cancel');
        Route::get('/transaksi', [CashierTransactionController::class, 'index'])->name('transactions.index');
        Route::post('/transaksi/items', [CashierTransactionController::class, 'addItem'])->name('transactions.items.store');
        Route::put('/transaksi/items/{menu}', [CashierTransactionController::class, 'updateItem'])->name('transactions.items.update');
        Route::delete('/transaksi/items/{menu}', [CashierTransactionController::class, 'removeItem'])->name('transactions.items.destroy');
        Route::post('/transaksi/checkout', [CashierTransactionController::class, 'checkout'])->name('transactions.checkout');
        Route::get('/pembayaran', [CashierPaymentController::class, 'index'])->name('payments.index');
        Route::get('/pembayaran/live', [CashierPaymentController::class, 'live'])->name('payments.live');
        Route::post('/pembayaran/{order}', [CashierPaymentController::class, 'pay'])->name('payments.pay');
        Route::delete('/pembayaran', [CashierPaymentController::class, 'destroyAll'])->name('payments.destroy-all');
        Route::get('/struk', [CashierReceiptController::class, 'index'])->name('receipts.index');
        Route::get('/struk/{order}/print', [CashierReceiptController::class, 'print'])->name('receipts.print');
        Route::post('/struk/{order}/send', [CashierReceiptController::class, 'sendDigital'])->name('receipts.send');
        Route::delete('/struk/{order}', [CashierReceiptController::class, 'destroy'])->name('receipts.destroy');
        Route::get('/meja', [CashierTableController::class, 'index'])->name('tables.index');
        Route::post('/meja/{table}/open', [CashierTableController::class, 'open'])->name('tables.open');
        Route::post('/meja/{table}/close', [CashierTableController::class, 'close'])->name('tables.close');
        Route::get('/meja/{table}', [CashierTableController::class, 'destroy'])->name('tables.destroy');
        Route::get('/laporan', [CashierReportController::class, 'index'])->name('reports.index');
        Route::get('/laporan/live', [CashierReportController::class, 'live'])->name('reports.live');
        Route::delete('/laporan/{order}', [CashierReportController::class, 'destroy'])->name('reports.destroy');
    });

Route::prefix('gudang')
    ->name('inventory.')
    ->middleware(['auth', 'role:inventory,superadmin'])
    ->group(function () {
        Route::get('/', [InventoryController::class, 'index'])->name('index');
        Route::get('/barang-masuk', [InventoryController::class, 'stockInPage'])->name('in.page');
        Route::get('/barang-keluar', [InventoryController::class, 'stockOutPage'])->name('out.page');
        Route::post('/categories', [InventoryController::class, 'storeCategory'])->name('categories.store');
        Route::post('/items', [InventoryController::class, 'storeItem'])->name('items.store');
        Route::post('/stock-in', [InventoryController::class, 'stockIn'])->name('stock.in');
        Route::post('/stock-out', [InventoryController::class, 'stockOut'])->name('stock.out');
        Route::post('/stock-opname', [InventoryController::class, 'stockOpname'])->name('stock.opname');
    });

Route::prefix('leader-kasir')
    ->name('leader-cashier.')
    ->middleware(['auth', 'role:leader_cashier,superadmin'])
    ->group(function () {
        Route::get('/', [LeaderCashierController::class, 'index'])->name('index');
        Route::get('/transaksi', [LeaderCashierController::class, 'transactions'])->name('transactions.index');
        Route::get('/live', [LeaderCashierController::class, 'live'])->name('live');
        Route::post('/cash-flow', [LeaderCashierController::class, 'storeCashFlow'])->name('cash-flow.store');
        Route::delete('/cash-flow/{entry}', [LeaderCashierController::class, 'destroyCashFlow'])->name('cash-flow.destroy');
    });

Route::prefix('superadmin')
    ->name('superadmin.')
    ->middleware(['auth', 'superadmin'])
    ->group(function () {
        Route::get('/dashboard', [SuperadminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/live', [SuperadminDashboardController::class, 'live'])->name('dashboard.live');
        Route::get('/dashboard/live/fragment', [SuperadminDashboardController::class, 'fragment'])->name('dashboard.live.fragment');
        Route::resource('users', SuperadminUserController::class)->except(['show']);
        Route::get('/access', [SuperadminAccessController::class, 'index'])->name('access.index');
        Route::get('/access/{user}/edit', [SuperadminAccessController::class, 'edit'])->name('access.edit');
        Route::put('/access/{user}', [SuperadminAccessController::class, 'update'])->name('access.update');
        Route::post('/access/matrix', [SuperadminAccessController::class, 'updateMatrix'])->name('access.matrix.update');
        Route::delete('/menus/all', [SuperadminMenuController::class, 'destroyAll'])->name('menus.destroy-all');
        Route::resource('menus', SuperadminMenuController::class)->except(['show']);
        Route::get('/employees', [SuperadminEmployeeController::class, 'index'])->name('employees.index');
        Route::post('/employees', [SuperadminEmployeeController::class, 'store'])->name('employees.store');
        Route::delete('/employees/all', [SuperadminEmployeeController::class, 'destroyAll'])->name('employees.destroy-all');
        Route::delete('/employees/{employee}', [SuperadminEmployeeController::class, 'destroy'])->name('employees.destroy');
        Route::get('/payrolls', [SuperadminPayrollController::class, 'index'])->name('payrolls.index');
        Route::post('/payrolls', [SuperadminPayrollController::class, 'store'])->name('payrolls.store');
        Route::delete('/payrolls/all', [SuperadminPayrollController::class, 'destroyAll'])->name('payrolls.destroy-all');
        Route::delete('/payrolls/{payroll}', [SuperadminPayrollController::class, 'destroy'])->name('payrolls.destroy');
        Route::delete('/menu-categories/all', [SuperadminMenuCategoryController::class, 'destroyAll'])->name('menu-categories.destroy-all');
        Route::resource('menu-categories', SuperadminMenuCategoryController::class)
            ->parameters(['menu-categories' => 'menuCategory'])
            ->except(['show']);
        Route::delete('tables/all', [SuperadminTableController::class, 'destroyAll'])->name('tables.destroy-all');
        Route::get('tables/{table}/qr', [SuperadminTableController::class, 'qr'])->name('tables.qr');
        Route::resource('tables', SuperadminTableController::class)->except(['show']);
        Route::get('/settings', [SuperadminSystemSettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SuperadminSystemSettingController::class, 'update'])->name('settings.update');
        Route::get('/reports', [SuperadminReportController::class, 'index'])->name('reports.index');
        Route::delete('/reports/all', [SuperadminReportController::class, 'destroyAll'])->name('reports.destroy-all');
        Route::delete('/reports/{transaction}', [SuperadminReportController::class, 'destroy'])->name('reports.destroy');
        Route::get('/reports/live', [SuperadminReportController::class, 'live'])->name('reports.live');
        Route::get('/reports/pdf', [SuperadminReportController::class, 'exportPdf'])->name('reports.pdf');
        Route::get('/reports/excel', [SuperadminReportController::class, 'exportExcel'])->name('reports.excel');
    });
