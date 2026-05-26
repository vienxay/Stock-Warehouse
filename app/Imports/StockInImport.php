<?php

namespace App\Imports;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Models\WarehouseStock;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class StockInImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public array $errors  = [];
    public int   $success = 0;

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNum = $index + 2; // +2 because row 1 = header

            $productCode   = trim($row['product_code']   ?? $row['barcode'] ?? '');
            $warehouseCode = trim($row['warehouse_code'] ?? '');
            $quantity      = intval($row['quantity']     ?? 0);

            // Basic validation
            if (!$productCode || !$warehouseCode || $quantity < 1) {
                $this->errors[] = "ແຖວ {$rowNum}: ຂໍ້ມູນບໍ່ຄົບ (ລະຫັດສິນຄ້າ/ສາງ/ຈຳນວນ)";
                continue;
            }

            // Look up product
            $product = Product::where('code', $productCode)
                ->orWhere('barcode', $productCode)
                ->where('is_active', true)
                ->first();

            if (!$product) {
                $this->errors[] = "ແຖວ {$rowNum}: ບໍ່ພົບສິນຄ້າ \"{$productCode}\"";
                continue;
            }

            // Look up warehouse
            $warehouse = Warehouse::where('code', $warehouseCode)
                ->orWhere('name', $warehouseCode)
                ->where('is_active', true)
                ->first();

            if (!$warehouse) {
                $this->errors[] = "ແຖວ {$rowNum}: ບໍ່ພົບສາງ \"{$warehouseCode}\"";
                continue;
            }

            // Look up optional supplier
            $supplierCode = trim($row['supplier_code'] ?? '');
            $supplier     = $supplierCode
                ? Supplier::where('code', $supplierCode)->first()
                : null;

            $unitPrice = isset($row['unit_price']) && is_numeric($row['unit_price'])
                ? floatval($row['unit_price'])
                : null;

            $note = trim($row['note'] ?? '');

            try {
                DB::transaction(function () use ($product, $warehouse, $quantity, $unitPrice, $supplier, $note) {
                    $stock  = WarehouseStock::firstOrCreate(
                        ['warehouse_id' => $warehouse->id, 'product_id' => $product->id],
                        ['quantity' => 0]
                    );

                    $before = $stock->quantity;
                    $stock->increment('quantity', $quantity);

                    StockMovement::create([
                        'type'            => 'in',
                        'product_id'      => $product->id,
                        'warehouse_id'    => $warehouse->id,
                        'user_id'         => Auth::id(),
                        'quantity'        => $quantity,
                        'quantity_before' => $before,
                        'quantity_after'  => $before + $quantity,
                        'unit_price'      => $unitPrice,
                        'supplier_id'     => $supplier?->id,
                        'note'            => $note ?: 'ນຳເຂົ້າຈາກ Excel',
                        'movement_date'   => now(),
                        'reference_no'    => 'IMP-' . strtoupper(uniqid()),
                    ]);
                });

                $this->success++;
            } catch (\Exception $e) {
                $this->errors[] = "ແຖວ {$rowNum}: ເກີດຂໍ້ຜິດພາດ — " . $e->getMessage();
            }
        }
    }
}
