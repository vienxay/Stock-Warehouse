@extends('layouts.app')
@section('title','ລາຍງານ')
@section('page_title','ລາຍງານ')

@section('content')

{{-- ===== TAB NAV ===== --}}
<div class="flex gap-2 mb-5 flex-wrap">
    @foreach([
        'in'        => ['ນຳເຂົ້າສາງ',    'text-green-700 bg-green-600'],
        'out'       => ['ເບີກຈ່າຍ',      'text-red-700 bg-red-600'],
        'low'       => ['ສິນຄ້າໃກ້ໝົດ',  'text-amber-700 bg-amber-500'],
        'warehouse' => ['ສາງ (Stock)',    'text-blue-700 bg-blue-600'],
    ] as $key => [$label, $colors])
    <a href="{{ route('reports.index', array_merge(request()->except('tab','page'), ['tab'=>$key])) }}"
        class="px-5 py-2.5 rounded-xl text-sm font-medium border transition
            {{ $tab === $key
                ? explode(' ', $colors)[1] . ' text-white border-transparent shadow-sm'
                : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
        {{ $label }}
    </a>
    @endforeach
</div>

{{-- ===== FILTER BAR ===== --}}
@if($tab !== 'warehouse')
<form method="GET" action="{{ route('reports.index') }}" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-5">
    <input type="hidden" name="tab" value="{{ $tab }}"/>
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 items-end">
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">ຈາກວັນທີ</label>
            <input type="date" name="date_from" value="{{ $dateFrom }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">ຫາວັນທີ</label>
            <input type="date" name="date_to" value="{{ $dateTo }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
        </div>
        @if($tab !== 'low')
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">ສາງ</label>
            <select name="warehouse_id" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- ທຸກສາງ --</option>
                @foreach($warehouses as $w)
                    <option value="{{ $w->id }}" {{ $warehouseId == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-600 mb-1">ສິນຄ້າ</label>
            <select name="product_id" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- ທຸກສິນຄ້າ --</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}" {{ $productId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        @endif
        <div class="{{ $tab === 'low' ? 'md:col-span-3' : '' }} flex gap-2">
            <button type="submit"
                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl text-sm font-medium transition">
                ຄົ້ນຫາ
            </button>
            <a href="{{ route('reports.index', ['tab' => $tab]) }}"
                class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-medium transition">
                ລ້າງ
            </a>
        </div>
    </div>
</form>
@endif

{{-- ===== EXPORT BUTTONS ===== --}}
@if($tab !== 'warehouse')
<div class="flex gap-2 mb-4 justify-end">
    <a href="{{ route('reports.excel', array_merge(request()->all(), ['tab' => $tab])) }}"
        class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Export Excel
    </a>
    <a href="{{ route('reports.pdf', array_merge(request()->all(), ['tab' => $tab])) }}"
        class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
        </svg>
        Export PDF
    </a>
</div>
@endif

{{-- ===== TAB: STOCK IN / OUT ===== --}}
@if(in_array($tab, ['in', 'out']))
@php
    $isIn      = $tab === 'in';
    $totalQty  = $movements->sum('quantity');
    $color     = $isIn ? 'green' : 'red';
    $sign      = $isIn ? '+' : '-';
@endphp

{{-- Summary cards --}}
<div class="grid grid-cols-3 gap-4 mb-5">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-sm text-gray-500 mb-1">ທັງໝົດ</p>
        <p class="text-2xl font-bold text-gray-800">{{ number_format($movements->total()) }} ລາຍການ</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-sm text-gray-500 mb-1">ຈຳນວນລວມ (ໜ້ານີ້)</p>
        <p class="text-2xl font-bold {{ $isIn ? 'text-green-600' : 'text-red-500' }}">
            {{ $sign }}{{ number_format($totalQty) }}
        </p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-sm text-gray-500 mb-1">ໄລຍະ</p>
        <p class="text-sm font-medium text-gray-700">{{ $dateFrom }} ຫາ {{ $dateTo }}</p>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">ເລກອ້າງອີງ</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">ສິນຄ້າ</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">ສາງ</th>
                <th class="text-right px-5 py-3 font-semibold text-gray-600">ຈຳນວນ</th>
                <th class="text-right px-5 py-3 font-semibold text-gray-600">ກ່ອນ</th>
                <th class="text-right px-5 py-3 font-semibold text-gray-600">ຫຼັງ</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">ຜູ້ດຳເນີນການ</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">ໝາຍເຫດ</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">ວັນທີ</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($movements as $m)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3 font-mono text-xs text-gray-500">{{ $m->reference_no }}</td>
                <td class="px-5 py-3 font-medium text-gray-800">{{ $m->product?->name }}</td>
                <td class="px-5 py-3 text-gray-600">{{ $m->warehouse?->name }}</td>
                <td class="px-5 py-3 text-right font-bold {{ $isIn ? 'text-green-600' : 'text-red-500' }}">
                    {{ $sign }}{{ number_format($m->quantity) }}
                </td>
                <td class="px-5 py-3 text-right text-gray-500">{{ number_format($m->quantity_before) }}</td>
                <td class="px-5 py-3 text-right text-gray-700 font-medium">{{ number_format($m->quantity_after) }}</td>
                <td class="px-5 py-3 text-gray-600 text-xs">{{ $m->user?->name }}</td>
                <td class="px-5 py-3 text-gray-500 text-xs max-w-xs truncate">{{ $m->note ?? '-' }}</td>
                <td class="px-5 py-3 text-gray-500 text-xs">{{ $m->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center py-12 text-gray-400">ບໍ່ມີຂໍ້ມູນ</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($movements->hasPages())
    <div class="px-5 py-4 border-t">{{ $movements->links() }}</div>
    @endif
</div>
@endif

{{-- ===== TAB: LOW STOCK ===== --}}
@if($tab === 'low')
<div class="grid grid-cols-3 gap-4 mb-5">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-sm text-gray-500 mb-1">ທັງໝົດ</p>
        <p class="text-2xl font-bold text-amber-600">{{ $lowStock->count() }} ລາຍການ</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-sm text-gray-500 mb-1">ໝົດສາງ</p>
        <p class="text-2xl font-bold text-red-600">{{ $lowStock->filter(fn($p) => $p->total_stock <= 0)->count() }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-sm text-gray-500 mb-1">ໃກ້ໝົດ</p>
        <p class="text-2xl font-bold text-amber-500">{{ $lowStock->filter(fn($p) => $p->total_stock > 0)->count() }}</p>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">ສິນຄ້າ</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">ໝວດ</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">ຫົວໜ່ວຍ</th>
                <th class="text-right px-5 py-3 font-semibold text-gray-600">ຕ່ຳສຸດ</th>
                <th class="text-right px-5 py-3 font-semibold text-gray-600">ໃນສາງ</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">ສາງ</th>
                <th class="text-center px-5 py-3 font-semibold text-gray-600">ສະຖານະ</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($lowStock as $p)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3">
                    <p class="font-medium text-gray-800">{{ $p->name }}</p>
                    <p class="text-xs font-mono text-gray-400">{{ $p->code }}</p>
                </td>
                <td class="px-5 py-3 text-gray-600 text-xs">{{ $p->category?->name ?? '-' }}</td>
                <td class="px-5 py-3 text-gray-600 text-xs">{{ $p->unit?->abbreviation ?? '-' }}</td>
                <td class="px-5 py-3 text-right text-gray-500">{{ number_format($p->min_stock_alert) }}</td>
                <td class="px-5 py-3 text-right font-bold text-lg {{ $p->total_stock <= 0 ? 'text-red-600' : 'text-amber-500' }}">
                    {{ number_format($p->total_stock) }}
                </td>
                <td class="px-5 py-3 text-xs text-gray-500">
                    @foreach($p->warehouseStocks->where('quantity','<=',$p->min_stock_alert) as $s)
                        <span>{{ $s->warehouse?->name }}: <b>{{ $s->quantity }}</b></span>@if(!$loop->last), @endif
                    @endforeach
                </td>
                <td class="px-5 py-3 text-center">
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full
                        {{ $p->total_stock <= 0 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }}">
                        {{ $p->total_stock <= 0 ? 'ໝົດສາງ' : 'ໃກ້ໝົດ' }}
                    </span>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="text-center py-12 text-gray-400">ສິນຄ້າທຸກລາຍການມີພຽງພໍ</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

{{-- ===== TAB: WAREHOUSE SUMMARY ===== --}}
@if($tab === 'warehouse')
<div class="space-y-5">
    @forelse($warehouseSummary as $wh)
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <div>
                <h3 class="font-semibold text-gray-800">{{ $wh->name }}
                    <span class="font-mono text-xs text-gray-400 ml-2">{{ $wh->code }}</span>
                </h3>
                <p class="text-xs text-gray-500">{{ $wh->branch?->name }} | {{ $wh->stocks_count }} ລາຍການ</p>
            </div>
            <a href="{{ route('warehouses.show', $wh) }}"
                class="text-xs text-blue-600 hover:underline">ລາຍລະອຽດ →</a>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-5 py-2 text-gray-500 font-medium text-xs">ສິນຄ້າ</th>
                    <th class="text-left px-5 py-2 text-gray-500 font-medium text-xs">ໝວດ</th>
                    <th class="text-left px-5 py-2 text-gray-500 font-medium text-xs">ຫົວໜ່ວຍ</th>
                    <th class="text-right px-5 py-2 text-gray-500 font-medium text-xs">ຈຳນວນ</th>
                    <th class="text-center px-5 py-2 text-gray-500 font-medium text-xs">ສະຖານະ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($wh->stocks->take(10) as $s)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-2.5 font-medium text-gray-800">{{ $s->product?->name }}</td>
                    <td class="px-5 py-2.5 text-gray-500 text-xs">{{ $s->product?->category?->name ?? '-' }}</td>
                    <td class="px-5 py-2.5 text-gray-500 text-xs">{{ $s->product?->unit?->abbreviation ?? '-' }}</td>
                    <td class="px-5 py-2.5 text-right font-bold {{ $s->quantity <= 0 ? 'text-red-500' : 'text-gray-800' }}">
                        {{ number_format($s->quantity) }}
                    </td>
                    <td class="px-5 py-2.5 text-center">
                        @if($s->quantity <= 0)
                            <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full">ໝົດ</span>
                        @elseif($s->product && $s->quantity <= $s->product->min_stock_alert)
                            <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">ໃກ້ໝົດ</span>
                        @else
                            <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">ປົກກະຕິ</span>
                        @endif
                    </td>
                </tr>
                @endforeach
                @if($wh->stocks->count() > 10)
                <tr>
                    <td colspan="5" class="px-5 py-2.5 text-center text-xs text-gray-400">
                        ... ແລະ {{ $wh->stocks->count() - 10 }} ລາຍການ
                        <a href="{{ route('warehouses.show', $wh) }}" class="text-blue-600 hover:underline ml-1">ເບິ່ງທັງໝົດ</a>
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
    @empty
    <div class="bg-white rounded-2xl p-12 text-center text-gray-400">ບໍ່ມີຂໍ້ມູນສາງ</div>
    @endforelse
</div>
@endif

@endsection
