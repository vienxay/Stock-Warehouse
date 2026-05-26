<!DOCTYPE html>
<html lang="lo">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1f2937; margin: 0; padding: 20px; }
    h1 { font-size: 16px; margin: 0 0 4px; color: #b45309; }
    .meta { font-size: 10px; color: #6b7280; margin-bottom: 16px; }
    table { width: 100%; border-collapse: collapse; }
    thead th { background: #b45309; color: #fff; padding: 7px 8px; text-align: left; font-size: 10px; }
    tbody tr:nth-child(even) { background: #fffbeb; }
    tbody td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; }
    .text-right { text-align: right; }
    .text-center { text-align: center; }
    .out { color: #dc2626; font-weight: 700; }
    .low { color: #d97706; font-weight: 700; }
    .footer { margin-top: 20px; font-size: 9px; color: #9ca3af; text-align: right; }
</style>
</head>
<body>
    <h1>ລາຍງານສິນຄ້າໃກ້ໝົດ / ໝົດສາງ</h1>
    <div class="meta">ວັນທີ: {{ now()->format('d/m/Y H:i') }} | ທັງໝົດ: {{ $products->count() }} ລາຍການ</div>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>ລະຫັດ</th>
                <th>ຊື່ສິນຄ້າ</th>
                <th>ໝວດ</th>
                <th>ຫົວໜ່ວຍ</th>
                <th class="text-right">ຕ່ຳສຸດ</th>
                <th class="text-right">ໃນສາງ</th>
                <th>ສາງ</th>
                <th class="text-center">ສະຖານະ</th>
            </tr>
        </thead>
        <tbody>
            @forelse($products as $i => $p)
            <tr>
                <td class="text-center">{{ $i + 1 }}</td>
                <td style="font-family:monospace;">{{ $p->code }}</td>
                <td>{{ $p->name }}</td>
                <td>{{ $p->category?->name ?? '-' }}</td>
                <td>{{ $p->unit?->abbreviation ?? '-' }}</td>
                <td class="text-right">{{ number_format($p->min_stock_alert) }}</td>
                <td class="text-right {{ $p->total_stock <= 0 ? 'out' : 'low' }}">
                    {{ number_format($p->total_stock) }}
                </td>
                <td style="font-size:9px;">
                    @foreach($p->warehouseStocks as $s)
                        {{ $s->warehouse?->name }}: {{ $s->quantity }}@if(!$loop->last), @endif
                    @endforeach
                </td>
                <td class="text-center {{ $p->total_stock <= 0 ? 'out' : 'low' }}">
                    {{ $p->total_stock <= 0 ? 'ໝົດສາງ' : 'ໃກ້ໝົດ' }}
                </td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center">ບໍ່ມີຂໍ້ມູນ</td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="footer">ລະບົບຈັດການສາງ — ອອກໂດຍ {{ auth()->user()?->name }}</div>
</body>
</html>
