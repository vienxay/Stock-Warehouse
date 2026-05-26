<!DOCTYPE html>
<html lang="lo">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1f2937; margin: 0; padding: 20px; }
    h1 { font-size: 16px; margin: 0 0 4px; color: #1e40af; }
    .meta { font-size: 10px; color: #6b7280; margin-bottom: 16px; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    thead th { background: #1e40af; color: #fff; padding: 7px 8px; text-align: left; font-size: 10px; }
    tbody tr:nth-child(even) { background: #f0f4ff; }
    tbody td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .badge-in  { color: #16a34a; font-weight: 700; }
    .badge-out { color: #dc2626; font-weight: 700; }
    .footer { margin-top: 20px; font-size: 9px; color: #9ca3af; text-align: right; }
    .summary { display: flex; gap: 20px; margin-bottom: 12px; }
    .summary-box { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 6px; padding: 8px 14px; }
    .summary-box .num { font-size: 18px; font-weight: 700; color: #1e40af; }
    .summary-box .lbl { font-size: 9px; color: #6b7280; }
</style>
</head>
<body>
    <h1>{{ $title }}</h1>
    <div class="meta">
        ວັນທີອອກລາຍງານ: {{ now()->format('d/m/Y H:i') }}
        @if($dateFrom || $dateTo)
            | ໄລຍະ: {{ $dateFrom ?? '...' }} ຫາ {{ $dateTo ?? '...' }}
        @endif
        @if($warehouse) | ສາງ: {{ $warehouse->name }} @endif
    </div>

    <table>
        <table style="width:auto;margin-bottom:12px;border:none;">
            <tr>
                <td style="padding:4px 12px 4px 0;border:none;">
                    <div style="background:#eff6ff;border:1px solid #bfdbfe;border-radius:6px;padding:8px 16px;text-align:center;">
                        <div style="font-size:18px;font-weight:700;color:#1e40af;">{{ $movements->count() }}</div>
                        <div style="font-size:9px;color:#6b7280;">ທັງໝົດ</div>
                    </div>
                </td>
                <td style="padding:4px 0;border:none;">
                    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:6px;padding:8px 16px;text-align:center;">
                        <div style="font-size:18px;font-weight:700;color:#16a34a;">{{ number_format($movements->sum('quantity')) }}</div>
                        <div style="font-size:9px;color:#6b7280;">ຈຳນວນລວມ</div>
                    </div>
                </td>
            </tr>
        </table>
    </table>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>ເລກອ້າງອີງ</th>
                <th>ສິນຄ້າ</th>
                <th>ສາງ</th>
                <th class="text-right">ຈຳນວນ</th>
                <th class="text-right">ກ່ອນ</th>
                <th class="text-right">ຫຼັງ</th>
                <th>ຜູ້ດຳເນີນການ</th>
                <th>ວັນທີ</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movements as $i => $m)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td style="font-family:monospace;font-size:9px;">{{ $m->reference_no }}</td>
                <td>{{ $m->product?->name }}</td>
                <td>{{ $m->warehouse?->name }}</td>
                <td class="text-right {{ $m->type === 'in' ? 'badge-in' : 'badge-out' }}">
                    {{ $m->type === 'in' ? '+' : '-' }}{{ number_format($m->quantity) }}
                </td>
                <td class="text-right">{{ number_format($m->quantity_before) }}</td>
                <td class="text-right">{{ number_format($m->quantity_after) }}</td>
                <td>{{ $m->user?->name }}</td>
                <td style="font-size:9px;">{{ $m->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center">ບໍ່ມີຂໍ້ມູນ</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="footer">ລະບົບຈັດການສາງ — ອອກໂດຍ {{ auth()->user()?->name }}</div>
</body>
</html>
