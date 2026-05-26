<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TableSheetExport implements FromCollection, WithHeadings, WithTitle, WithStyles, ShouldAutoSize
{
    public function __construct(private string $table) {}

    public function collection()
    {
        return DB::table($this->table)->get()->map(fn($r) => array_values((array) $r));
    }

    public function headings(): array
    {
        $first = DB::table($this->table)->first();
        return $first ? array_keys((array) $first) : [];
    }

    public function title(): string
    {
        // Sheet name max 31 chars, no special chars
        return substr($this->table, 0, 31);
    }

    public function styles(Worksheet $sheet): array
    {
        return [1 => ['font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                       'fill' => ['fillType' => 'solid', 'startColor' => ['argb' => 'FF1D4ED8']]]];
    }
}
