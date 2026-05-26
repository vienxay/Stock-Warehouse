<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StockRequest;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // ── Core stats ──────────────────────────────────────────────
        $totalProducts   = Product::count();
        $totalWarehouses = Warehouse::count();

        $lowStockCount  = Product::where('is_active', true)
            ->whereRaw('(SELECT COALESCE(SUM(quantity),0) FROM warehouse_stocks WHERE warehouse_stocks.product_id = products.id) > 0')
            ->whereRaw('(SELECT COALESCE(SUM(quantity),0) FROM warehouse_stocks WHERE warehouse_stocks.product_id = products.id) <= products.min_stock_alert')
            ->count();

        $outOfStockCount = Product::where('is_active', true)
            ->whereRaw('(SELECT COALESCE(SUM(quantity),0) FROM warehouse_stocks WHERE warehouse_stocks.product_id = products.id) = 0')
            ->count();

        $stockInToday  = StockMovement::where('type', 'in')->whereDate('created_at', today());
        $stockOutToday = StockMovement::where('type', 'out')->whereDate('created_at', today());

        $stockInCount  = (clone $stockInToday)->count();
        $stockInQty    = (clone $stockInToday)->sum('quantity');
        $stockOutCount = (clone $stockOutToday)->count();
        $stockOutQty   = (clone $stockOutToday)->sum('quantity');

        $pendingRequests = StockRequest::where('status', 'pending')->count();

        // ── 7-day chart data ─────────────────────────────────────────
        $chartLabels  = [];
        $chartIn      = [];
        $chartOut     = [];

        for ($i = 6; $i >= 0; $i--) {
            $date          = now()->subDays($i)->toDateString();
            $chartLabels[] = now()->subDays($i)->format('d/m');
            $chartIn[]     = StockMovement::where('type', 'in')->whereDate('created_at', $date)->sum('quantity');
            $chartOut[]    = StockMovement::where('type', 'out')->whereDate('created_at', $date)->sum('quantity');
        }

        // ── Low stock products (top 6) ───────────────────────────────
        $lowStockProducts = Product::with(['category', 'unit'])
            ->where('is_active', true)
            ->whereRaw('(SELECT COALESCE(SUM(quantity),0) FROM warehouse_stocks WHERE warehouse_stocks.product_id = products.id) <= products.min_stock_alert')
            ->orderByRaw('(SELECT COALESCE(SUM(quantity),0) FROM warehouse_stocks WHERE warehouse_stocks.product_id = products.id) ASC')
            ->limit(6)
            ->get()
            ->map(function ($p) {
                $p->current_stock = DB::table('warehouse_stocks')->where('product_id', $p->id)->sum('quantity');
                return $p;
            });

        // ── Recent requests ──────────────────────────────────────────
        $requestQuery = StockRequest::with(['requester', 'branch']);
        if (!$user->isAdmin()) {
            $requestQuery->where('requester_id', $user->id);
        }
        $recentRequests = $requestQuery->latest()->limit(6)->get();

        // ── Recent movements (today) ──────────────────────────────────
        $recentMovements = StockMovement::with(['product', 'warehouse', 'user'])
            ->whereDate('created_at', today())
            ->latest()
            ->limit(8)
            ->get();

        // ── Warehouse stock summary ───────────────────────────────────
        $warehouseSummary = Warehouse::where('is_active', true)
            ->withCount('stocks')
            ->with(['stocks' => fn($q) => $q->select('warehouse_id', DB::raw('SUM(quantity) as total_qty'))->groupBy('warehouse_id')])
            ->get()
            ->map(function ($wh) {
                $wh->total_stock = $wh->stocks->sum('total_qty');
                return $wh;
            });

        return view('dashboard', compact(
            'user',
            'totalProducts', 'totalWarehouses',
            'lowStockCount', 'outOfStockCount',
            'stockInCount', 'stockInQty',
            'stockOutCount', 'stockOutQty',
            'pendingRequests',
            'chartLabels', 'chartIn', 'chartOut',
            'lowStockProducts', 'recentRequests',
            'recentMovements', 'warehouseSummary'
        ));
    }
}
