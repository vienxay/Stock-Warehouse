<?php

namespace App\Http\Controllers;

use App\Exports\LowStockExport;
use App\Exports\StockMovementExport;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    // ─── Main report page ────────────────────────────────────────────
    public function index(Request $request)
    {
        $tab        = $request->tab ?? 'movements';
        $dateFrom   = $request->date_from ?? now()->startOfMonth()->toDateString();
        $dateTo     = $request->date_to   ?? now()->toDateString();
        $warehouseId = $request->warehouse_id;
        $productId   = $request->product_id;
        $warehouses  = Warehouse::where('is_active', true)->get();
        $products    = Product::where('is_active', true)->orderBy('name')->get();

        // Stock In / Out
        $movements = collect();
        if (in_array($tab, ['in', 'out'])) {
            $movements = $this->movementQuery($tab, $dateFrom, $dateTo, $warehouseId, $productId)
                ->paginate(20)->withQueryString();
        }

        // Low stock
        $lowStock = collect();
        if ($tab === 'low') {
            $lowStock = Product::with(['category', 'unit', 'warehouseStocks.warehouse'])
                ->where('is_active', true)
                ->get()
                ->filter(fn($p) => $p->total_stock <= $p->min_stock_alert)
                ->values();
        }

        // Warehouse summary
        $warehouseSummary = collect();
        if ($tab === 'warehouse') {
            $warehouseSummary = Warehouse::with(['stocks.product.unit', 'branch'])
                ->where('is_active', true)
                ->withCount('stocks')
                ->get();
        }

        return view('reports.index', compact(
            'tab', 'dateFrom', 'dateTo', 'warehouses', 'products',
            'warehouseId', 'productId', 'movements', 'lowStock', 'warehouseSummary'
        ));
    }

    // ─── Export Excel ─────────────────────────────────────────────────
    public function exportExcel(Request $request)
    {
        $tab      = $request->tab;
        $dateFrom = $request->date_from;
        $dateTo   = $request->date_to;

        if ($tab === 'low') {
            return Excel::download(new LowStockExport(), 'low_stock_' . now()->format('Ymd') . '.xlsx');
        }

        $type = $tab === 'out' ? 'out' : 'in';
        $filename = 'stock_' . $type . '_' . now()->format('Ymd') . '.xlsx';

        return Excel::download(
            new StockMovementExport($type, $dateFrom, $dateTo, $request->warehouse_id, $request->product_id),
            $filename
        );
    }

    // ─── Export PDF ───────────────────────────────────────────────────
    public function exportPdf(Request $request)
    {
        $tab      = $request->tab;
        $dateFrom = $request->date_from;
        $dateTo   = $request->date_to;
        $warehouse = $request->warehouse_id
            ? Warehouse::find($request->warehouse_id)
            : null;

        if ($tab === 'low') {
            $products = Product::with(['category', 'unit', 'warehouseStocks.warehouse'])
                ->where('is_active', true)
                ->get()
                ->filter(fn($p) => $p->total_stock <= $p->min_stock_alert)
                ->values();

            $pdf = Pdf::loadView('reports.pdf.low_stock', compact('products'))
                ->setPaper('a4', 'landscape');

            return $pdf->download('low_stock_' . now()->format('Ymd') . '.pdf');
        }

        $type      = $tab === 'out' ? 'out' : 'in';
        $title     = $type === 'in' ? 'ລາຍງານນຳເຂົ້າສາງ' : 'ລາຍງານເບີກຈ່າຍ';
        $movements = $this->movementQuery($type, $dateFrom, $dateTo, $request->warehouse_id, $request->product_id)->get();

        $pdf = Pdf::loadView('reports.pdf.movements', compact('title', 'movements', 'dateFrom', 'dateTo', 'warehouse'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('stock_' . $type . '_' . now()->format('Ymd') . '.pdf');
    }

    // ─── Helper ───────────────────────────────────────────────────────
    private function movementQuery(string $type, ?string $from, ?string $to, ?int $whId, ?int $pId)
    {
        $q = StockMovement::with(['product', 'warehouse', 'user'])
            ->where('type', $type)
            ->orderBy('created_at', 'desc');

        if ($from)  $q->whereDate('created_at', '>=', $from);
        if ($to)    $q->whereDate('created_at', '<=', $to);
        if ($whId)  $q->where('warehouse_id', $whId);
        if ($pId)   $q->where('product_id', $pId);

        return $q;
    }
}
