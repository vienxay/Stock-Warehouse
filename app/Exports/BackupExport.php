<?php

namespace App\Exports;

use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BackupExport implements WithMultipleSheets
{
    const TABLES = [
        'settings', 'branches', 'users',
        'categories', 'brands', 'units', 'suppliers',
        'warehouses', 'products', 'product_images', 'warehouse_stocks',
        'stock_requests', 'stock_request_items', 'stock_movements',
        'warehouse_transfers', 'audit_logs',
    ];

    public function sheets(): array
    {
        return collect(self::TABLES)
            ->filter(fn($t) => Schema::hasTable($t))
            ->map(fn($t) => new TableSheetExport($t))
            ->toArray();
    }
}
