<?php

namespace App\Exports;

use App\Models\StockMovement;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StockMovementExport implements FromQuery, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function __construct(
        private string $type,
        private ?string $dateFrom,
        private ?string $dateTo,
        private ?int $warehouseId,
        private ?int $productId,
    ) {}

    public function query()
    {
        $q = StockMovement::with(['product', 'warehouse', 'user', 'supplier'])
            ->where('type', $this->type)
            ->orderBy('created_at', 'desc');

        if ($this->dateFrom) $q->whereDate('created_at', '>=', $this->dateFrom);
        if ($this->dateTo)   $q->whereDate('created_at', '<=', $this->dateTo);
        if ($this->warehouseId) $q->where('warehouse_id', $this->warehouseId);
        if ($this->productId)   $q->where('product_id', $this->productId);

        return $q;
    }

    public function headings(): array
    {
        return ['ເລກອ້າງອີງ', 'ສິນຄ້າ', 'ສາງ', 'ຈຳນວນ', 'ກ່ອນ', 'ຫຼັງ', 'ລາຄາ/ໜ່ວຍ', 'ຜູ້ດຳເນີນການ', 'ໝາຍເຫດ', 'ວັນທີ'];
    }

    public function map($row): array
    {
        return [
            $row->reference_no,
            $row->product?->name,
            $row->warehouse?->name,
            $row->quantity,
            $row->quantity_before,
            $row->quantity_after,
            $row->unit_price ?? '',
            $row->user?->name,
            $row->note ?? '',
            $row->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true]]];
    }
}
