@extends('layouts.app')
@section('title',$product->name)
@section('page_title','ລາຍລະອຽດສິນຄ້າ')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('products.index') }}" class="text-gray-500 hover:text-gray-700">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <h2 class="text-xl font-bold text-gray-800">{{ $product->name }}</h2>
    <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">{{ $product->code }}</span>
    <a href="{{ route('products.edit', $product) }}"
        class="ml-auto flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-xl text-sm font-medium transition">
        ແກ້ໄຂ
    </a>
</div>

<div class="grid grid-cols-3 gap-5">
    {{-- Info --}}
    <div class="col-span-2 space-y-5">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-700 mb-4">ຂໍ້ມູນສິນຄ້າ</h3>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div><p class="text-gray-500">ລະຫັດ</p><p class="font-medium font-mono">{{ $product->code }}</p></div>
                <div><p class="text-gray-500">ບາໂຄດ</p><p class="font-medium font-mono">{{ $product->barcode ?? '-' }}</p></div>
                <div><p class="text-gray-500">ໝວດໝູ່</p><p class="font-medium">{{ $product->category?->name ?? '-' }}</p></div>
                <div><p class="text-gray-500">ຍີ່ຫໍ້</p><p class="font-medium">{{ $product->brand?->name ?? '-' }}</p></div>
                <div><p class="text-gray-500">ຫົວໜ່ວຍ</p><p class="font-medium">{{ $product->unit?->name ?? '-' }}</p></div>
                <div><p class="text-gray-500">ຜູ້ສະໜອງ</p><p class="font-medium">{{ $product->supplier?->name ?? '-' }}</p></div>
                <div><p class="text-gray-500">ລາຄາທຶນ</p><p class="font-medium text-gray-800">{{ number_format($product->cost_price) }} ກີບ</p></div>
                <div><p class="text-gray-500">ລາຄາຂາຍ</p><p class="font-medium text-blue-600">{{ number_format($product->selling_price) }} ກີບ</p></div>
                <div><p class="text-gray-500">ເຕືອນຕ່ຳສຸດ</p><p class="font-medium">{{ $product->min_stock_alert }}</p></div>
            </div>
            @if($product->description)
            <div class="mt-4 pt-4 border-t border-gray-100">
                <p class="text-gray-500 text-sm">ລາຍລະອຽດ</p>
                <p class="text-gray-700 text-sm mt-1">{{ $product->description }}</p>
            </div>
            @endif
        </div>

        {{-- Stock by warehouse --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-700 mb-4">ຈຳນວນໃນສາງ</h3>
            @forelse($product->warehouseStocks as $ws)
            <div class="flex items-center justify-between py-3 border-b border-gray-50 last:border-0">
                <div>
                    <p class="font-medium text-sm">{{ $ws->warehouse->name }}</p>
                    <p class="text-xs text-gray-500">{{ $ws->warehouse->branch->name ?? '' }}</p>
                </div>
                <span class="text-lg font-bold {{ $ws->quantity <= 0 ? 'text-red-500' : 'text-gray-800' }}">
                    {{ number_format($ws->quantity) }}
                </span>
            </div>
            @empty
            <p class="text-gray-400 text-sm text-center py-4">ບໍ່ມີຂໍ້ມູນສາງ</p>
            @endforelse
            <div class="mt-3 pt-3 border-t border-gray-200 flex justify-between">
                <span class="font-semibold text-gray-700">ລວມທັງໝົດ</span>
                <span class="font-bold text-blue-600 text-lg">{{ number_format($product->total_stock) }}</span>
            </div>
        </div>
    </div>

    {{-- Right panel --}}
    <div class="space-y-5">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-700 mb-3">ສະຖານະ</h3>
            @php $total = $product->total_stock; @endphp
            <div class="text-center py-4">
                <p class="text-5xl font-bold {{ $total <= 0 ? 'text-red-500' : ($product->isLowStock() ? 'text-orange-500' : 'text-green-600') }}">
                    {{ number_format($total) }}
                </p>
                <p class="text-gray-500 text-sm mt-1">ຈຳນວນລວມ</p>
                <div class="mt-3">
                    @if($total <= 0)
                        <span class="bg-red-100 text-red-700 text-sm font-medium px-3 py-1 rounded-full">ໝົດສາງ</span>
                    @elseif($product->isLowStock())
                        <span class="bg-orange-100 text-orange-700 text-sm font-medium px-3 py-1 rounded-full">ໃກ້ໝົດ</span>
                    @else
                        <span class="bg-green-100 text-green-700 text-sm font-medium px-3 py-1 rounded-full">ປົກກະຕິ</span>
                    @endif
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3 mt-3">
                <a href="{{ route('stock.in') }}?product_id={{ $product->id }}"
                    class="text-center bg-green-50 text-green-700 hover:bg-green-100 py-2 rounded-lg text-sm font-medium transition">
                    ນຳເຂົ້າ
                </a>
                <a href="{{ route('stock.out') }}?product_id={{ $product->id }}"
                    class="text-center bg-blue-50 text-blue-700 hover:bg-blue-100 py-2 rounded-lg text-sm font-medium transition">
                    ຈ່າຍອອກ
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Movement history --}}
<div class="mt-5 bg-white rounded-2xl shadow-sm border border-gray-100">
    <div class="px-5 py-4 border-b border-gray-100">
        <h3 class="font-semibold text-gray-700">ປະຫວັດການເຄື່ອນໄຫວ</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-5 py-3 text-gray-600 font-semibold">ປະເພດ</th>
                    <th class="text-left px-5 py-3 text-gray-600 font-semibold">ສາງ</th>
                    <th class="text-right px-5 py-3 text-gray-600 font-semibold">ຈຳນວນ</th>
                    <th class="text-right px-5 py-3 text-gray-600 font-semibold">ກ່ອນ → ຫຼັງ</th>
                    <th class="text-left px-5 py-3 text-gray-600 font-semibold">ຜູ້ດຳເນີນການ</th>
                    <th class="text-left px-5 py-3 text-gray-600 font-semibold">ວັນທີ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($movements as $m)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3">
                        @php
                            $typeLabel = match($m->type) {
                                'in'         => ['label' => 'ນຳເຂົ້າ',  'class' => 'bg-green-100 text-green-700'],
                                'out'        => ['label' => 'ຈ່າຍອອກ',  'class' => 'bg-red-100 text-red-700'],
                                'transfer'   => ['label' => 'ໂອນສາງ',   'class' => 'bg-blue-100 text-blue-700'],
                                'adjustment' => ['label' => 'ປັບໃໝ່',   'class' => 'bg-purple-100 text-purple-700'],
                                default      => ['label' => $m->type,   'class' => 'bg-gray-100 text-gray-600'],
                            };
                        @endphp
                        <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $typeLabel['class'] }}">
                            {{ $typeLabel['label'] }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-gray-700">{{ $m->warehouse?->name }}</td>
                    <td class="px-5 py-3 text-right font-bold {{ $m->type === 'in' || $m->type === 'transfer' ? 'text-green-600' : 'text-red-500' }}">
                        {{ in_array($m->type, ['in','transfer']) ? '+' : '-' }}{{ number_format($m->quantity) }}
                    </td>
                    <td class="px-5 py-3 text-right text-gray-500 font-mono text-xs">
                        {{ $m->quantity_before }} → {{ $m->quantity_after }}
                    </td>
                    <td class="px-5 py-3 text-gray-600">{{ $m->user?->name }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-8 text-gray-400">ບໍ່ມີປະຫວັດ</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
