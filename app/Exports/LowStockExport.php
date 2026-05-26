<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LowStockExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return Product::with(['category', 'unit', 'warehouseStocks.warehouse'])
            ->where('is_active', true)
            ->get()
            ->filter(fn($p) => $p->total_stock <= $p->min_stock_alert);
    }

    public function headings(): array
    {
        return ['ລະຫັດ', 'ຊື່ສິນຄ້າ', 'ໝວດ', 'ຫົວໜ່ວຍ', 'ຈຳນວນຕ່ຳສຸດ', 'ຈຳນວນໃນສາງ', 'ສາງ', 'ສະຖານະ'];
    }

    public function map($p): array
    {
        $warehouseList = $p->warehouseStocks
            ->map(fn($s) => $s->warehouse?->name . '(' . $s->quantity . ')')
            ->implode(', ');

        return [
            $p->code,
            $p->name,
            $p->category?->name ?? '-',
            $p->unit?->abbreviation ?? '-',
            $p->min_stock_alert,
            $p->total_stock,
            $warehouseList,
            $p->total_stock <= 0 ? 'ໝົດສາງ' : 'ໃກ້ໝົດ',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
