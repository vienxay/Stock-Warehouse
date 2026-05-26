<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\WarehouseTransferController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockRequestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WarehouseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('login'));

// Language switcher (no auth required)
Route::get('/lang/{locale}', function (string $locale) {
    if (in_array($locale, ['lo', 'en', 'zh'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('lang.switch');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Forgot / Reset Password
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'verify'])->name('password.verify');
    Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showReset'])->name('password.reset.show');
    Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.reset.update');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'changePassword'])->name('profile.password');
    Route::delete('/profile/avatar', [ProfileController::class, 'removeAvatar'])->name('profile.avatar.remove');

    // Notifications
    Route::post('/notifications/read-all', function () {
        /** @var \App\Models\User $u */ $u = Auth::user();
        $u->unreadNotifications->markAsRead();
        cache()->forget("notif_count_{$u->id}"); // clear cache
        return back();
    })->name('notifications.read-all');

    Route::post('/notifications/{id}/read', function ($id) {
        /** @var \App\Models\User $u */ $u = Auth::user();
        $n = $u->notifications()->find($id);
        if ($n) {
            $n->markAsRead();
            cache()->forget("notif_count_{$u->id}"); // clear cache
        }
        return response()->json(['ok' => true]);
    })->name('notifications.read');

    Route::get('/notifications/unread-count', function () {
        /** @var \App\Models\User $u */ $u = Auth::user();
        $count = cache()->remember("notif_count_{$u->id}", 30, function () use ($u) {
            return $u->unreadNotifications()->count();
        });
        return response()->json(['count' => $count]);
    })->name('notifications.count');

    // Products
    Route::resource('products', ProductController::class);
    Route::delete('/products/{product}/images/{image}', [ProductController::class, 'destroyImage'])->name('products.images.destroy');

    // Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');

    // Catalog (Units / Brands / Suppliers)
    Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
    Route::post('/catalog/units', [CatalogController::class, 'storeUnit'])->name('catalog.units.store');
    Route::put('/catalog/units/{unit}', [CatalogController::class, 'updateUnit'])->name('catalog.units.update');
    Route::delete('/catalog/units/{unit}', [CatalogController::class, 'destroyUnit'])->name('catalog.units.destroy');
    Route::post('/catalog/brands', [CatalogController::class, 'storeBrand'])->name('catalog.brands.store');
    Route::put('/catalog/brands/{brand}', [CatalogController::class, 'updateBrand'])->name('catalog.brands.update');
    Route::delete('/catalog/brands/{brand}', [CatalogController::class, 'destroyBrand'])->name('catalog.brands.destroy');
    Route::post('/catalog/suppliers', [CatalogController::class, 'storeSupplier'])->name('catalog.suppliers.store');
    Route::put('/catalog/suppliers/{supplier}', [CatalogController::class, 'updateSupplier'])->name('catalog.suppliers.update');
    Route::delete('/catalog/suppliers/{supplier}', [CatalogController::class, 'destroySupplier'])->name('catalog.suppliers.destroy');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // Stock In / Out
    Route::get('/stock/in', [StockController::class, 'inIndex'])->name('stock.in');
    Route::post('/stock/in', [StockController::class, 'inStore'])->name('stock.in.store');
    Route::get('/stock/out', [StockController::class, 'outIndex'])->name('stock.out');
    Route::post('/stock/out', [StockController::class, 'outStore'])->name('stock.out.store');
    Route::get('/stock/quantity', [StockController::class, 'getStock'])->name('stock.quantity');
    Route::get('/stock/barcode', [StockController::class, 'lookupBarcode'])->name('stock.barcode');
    Route::post('/stock/import', [StockController::class, 'importExcel'])->name('stock.import');
    Route::get('/stock/template', [StockController::class, 'downloadTemplate'])->name('stock.template');

    // Stock Requests
    Route::resource('requests', StockRequestController::class)->except(['edit', 'update']);
    Route::post('/requests/{stockRequest}/approve', [StockRequestController::class, 'approve'])->name('requests.approve');
    Route::post('/requests/{stockRequest}/reject', [StockRequestController::class, 'reject'])->name('requests.reject');
    Route::post('/requests/{stockRequest}/issue', [StockRequestController::class, 'issue'])->name('requests.issue');
    Route::post('/requests/{stockRequest}/receive', [StockRequestController::class, 'receive'])->name('requests.receive');
    Route::get('/requests/{stockRequest}/pdf', [StockRequestController::class, 'pdf'])->name('requests.pdf');

    // Warehouses
    Route::resource('warehouses', WarehouseController::class)->except(['edit', 'create']);

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.excel');
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPdf'])->name('reports.pdf');

    // Warehouse Transfers
    Route::get('/transfers', [WarehouseTransferController::class, 'index'])->name('transfers.index');
    Route::post('/transfers', [WarehouseTransferController::class, 'store'])->name('transfers.store');
    Route::post('/transfers/{transfer}/receive', [WarehouseTransferController::class, 'receive'])->name('transfers.receive');
    Route::post('/transfers/{transfer}/cancel', [WarehouseTransferController::class, 'cancel'])->name('transfers.cancel');

    // Branches (admin only)
    Route::middleware('admin')->group(function () {
        Route::get('/branches', [BranchController::class, 'index'])->name('branches.index');
        Route::post('/branches', [BranchController::class, 'store'])->name('branches.store');
        Route::put('/branches/{branch}', [BranchController::class, 'update'])->name('branches.update');
        Route::post('/branches/{branch}/toggle', [BranchController::class, 'toggleActive'])->name('branches.toggle');
        Route::delete('/branches/{branch}', [BranchController::class, 'destroy'])->name('branches.destroy');
    });

    // Users & Settings (admin only)
    Route::middleware('admin')->group(function () {
        Route::resource('users', UserController::class)->except(['show', 'edit', 'create']);
        Route::post('/users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle-active');
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

        // Audit Logs
        Route::get('/audit', [AuditLogController::class, 'index'])->name('audit.index');
        Route::delete('/audit/{auditLog}', [AuditLogController::class, 'destroy'])->name('audit.destroy');
        Route::post('/audit/clear', [AuditLogController::class, 'clear'])->name('audit.clear');

        // Backups
        Route::get('/backups', [BackupController::class, 'index'])->name('backups.index');
        Route::post('/backups', [BackupController::class, 'store'])->name('backups.store');
        Route::get('/backups/{backup}/download', [BackupController::class, 'download'])->name('backups.download');
        Route::delete('/backups/{backup}', [BackupController::class, 'destroy'])->name('backups.destroy');
    });
});
