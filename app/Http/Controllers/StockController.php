<?php

namespace App\Http\Controllers;

use App\Imports\StockInImport;
use App\Models\AuditLog;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class StockController extends Controller
{
    private function authorizeStock(): void
    {
        if (!Auth::user()->canManageStock()) {
            abort(403, 'ທ່ານບໍ່ມີສິດເຂົ້າເຖິງໜ້ານີ້');
        }
    }

    public function inIndex()
    {
        $this->authorizeStock();
        $movements = StockMovement::with(['product', 'warehouse', 'user', 'supplier'])
            ->where('type', 'in')
            ->latest()
            ->paginate(20);
        $products   = Product::where('is_active', true)->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        $suppliers  = Supplier::where('is_active', true)->get();
        return view('stock.in', compact('movements', 'products', 'warehouses', 'suppliers'));
    }

    public function inStore(Request $request)
    {
        $this->authorizeStock();
        $data = $request->validate([
            'product_id'    => 'required|exists:products,id',
            'warehouse_id'  => 'required|exists:warehouses,id',
            'quantity'      => 'required|integer|min:1',
            'unit_price'    => 'nullable|numeric|min:0',
            'supplier_id'   => 'nullable|exists:suppliers,id',
            'note'          => 'nullable|string',
            'movement_date' => 'nullable|date',
        ]);

        DB::transaction(function () use ($data) {
            $stock = WarehouseStock::firstOrCreate(
                ['warehouse_id' => $data['warehouse_id'], 'product_id' => $data['product_id']],
                ['quantity' => 0]
            );

            $before = $stock->quantity;
            $stock->increment('quantity', $data['quantity']);

            StockMovement::create([
                'type'           => 'in',
                'product_id'     => $data['product_id'],
                'warehouse_id'   => $data['warehouse_id'],
                'user_id'        => Auth::id(),
                'quantity'       => $data['quantity'],
                'quantity_before'=> $before,
                'quantity_after' => $before + $data['quantity'],
                'unit_price'     => $data['unit_price'] ?? null,
                'supplier_id'    => $data['supplier_id'] ?? null,
                'note'           => $data['note'] ?? null,
                'movement_date'  => $data['movement_date'] ?? now(),
                'reference_no'   => 'IN-' . strtoupper(uniqid()),
            ]);

            AuditLog::log('stock_in', "ນຳເຂົ້າສາງ {$data['quantity']} ໜ່ວຍ (Product #{$data['product_id']}, Warehouse #{$data['warehouse_id']})");
        });

        return back()->with('success', 'ນຳເຂົ້າສາງສຳເລັດ');
    }

    public function outIndex()
    {
        $this->authorizeStock();
        $movements = StockMovement::with(['product', 'warehouse', 'user'])
            ->where('type', 'out')
            ->latest()
            ->paginate(20);
        $products   = Product::where('is_active', true)->get();
        $warehouses = Warehouse::where('is_active', true)->get();
        return view('stock.out', compact('movements', 'products', 'warehouses'));
    }

    public function outStore(Request $request)
    {
        $this->authorizeStock();
        $data = $request->validate([
            'product_id'   => 'required|exists:products,id',
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity'     => 'required|integer|min:1',
            'note'         => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($data) {
                $stock = WarehouseStock::where([
                    'warehouse_id' => $data['warehouse_id'],
                    'product_id'   => $data['product_id'],
                ])->lockForUpdate()->first();

                if (!$stock || $stock->quantity < $data['quantity']) {
                    throw new \Exception('ສິນຄ້າໃນສາງບໍ່ພຽງພໍ (ມີ: ' . ($stock?->quantity ?? 0) . ')');
                }

                $before = $stock->quantity;
                $stock->decrement('quantity', $data['quantity']);

                StockMovement::create([
                    'type'            => 'out',
                    'product_id'      => $data['product_id'],
                    'warehouse_id'    => $data['warehouse_id'],
                    'user_id'         => Auth::id(),
                    'quantity'        => $data['quantity'],
                    'quantity_before' => $before,
                    'quantity_after'  => $before - $data['quantity'],
                    'note'            => $data['note'] ?? null,
                    'movement_date'   => now(),
                    'reference_no'    => 'OUT-' . strtoupper(uniqid()),
                ]);

                AuditLog::log('stock_out', "ຈ່າຍອອກ {$data['quantity']} ໜ່ວຍ (Product #{$data['product_id']}, Warehouse #{$data['warehouse_id']})");
            });
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }

        return back()->with('success', 'ຈ່າຍອອກສາງສຳເລັດ');
    }

    public function getStock(Request $request)
    {
        $stock = WarehouseStock::where([
            'product_id'   => $request->product_id,
            'warehouse_id' => $request->warehouse_id,
        ])->value('quantity') ?? 0;

        return response()->json(['quantity' => $stock]);
    }

    public function importExcel(Request $request)
    {
        $this->authorizeStock();
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'excel_file.required' => 'ກະລຸນາເລືອກໄຟລ໌',
            'excel_file.mimes'    => 'ຕ້ອງເປັນໄຟລ໌ .xlsx, .xls ຫຼື .csv',
            'excel_file.max'      => 'ໄຟລ໌ຂະໜາດໃຫຍ່ເກີນ 5MB',
        ]);

        $import = new StockInImport();
        Excel::import($import, $request->file('excel_file'));

        $msg = "ນຳເຂົ້າສຳເລັດ {$import->success} ລາຍການ";

        AuditLog::log('stock_import', "Import Excel: {$import->success} ລາຍການ" . (!empty($import->errors) ? ' (ມີຂໍ້ຜິດພາດ ' . count($import->errors) . ' ລາຍການ)' : ''));

        if (!empty($import->errors)) {
            $errList = implode(' | ', $import->errors);
            return back()->with('warning', $msg . ' | ຜິດພາດ: ' . $errList);
        }

        return back()->with('success', $msg);
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="stock_in_template.csv"',
        ];

        $warehouses = Warehouse::where('is_active', true)->pluck('code')->implode(', ');
        $suppliers  = Supplier::where('is_active', true)->pluck('code')->implode(', ');

        $rows = [
            ['product_code', 'warehouse_code', 'quantity', 'unit_price', 'supplier_code', 'note'],
            ['PRD001', 'WH001', '100', '50000', 'SUP001', 'ນຳເຂົ້າຕາມໃບສັ່ງ'],
            ['PRD002', 'WH001', '50',  '',      '',       ''],
        ];

        $callback = function () use ($rows) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // UTF-8 BOM for Excel
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function lookupBarcode(Request $request)
    {
        $q = trim($request->q ?? '');
        if (!$q) {
            return response()->json(['found' => false]);
        }

        $product = Product::where('is_active', true)
            ->where(function ($query) use ($q) {
                $query->where('barcode', $q)->orWhere('code', $q);
            })
            ->with('unit')
            ->first();

        if (!$product) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found' => true,
            'id'    => $product->id,
            'name'  => $product->name,
            'code'  => $product->code,
            'unit'  => $product->unit?->abbreviation ?? '',
        ]);
    }
}
