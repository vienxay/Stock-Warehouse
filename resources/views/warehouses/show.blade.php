@extends('layouts.app')
@section('title',$warehouse->name)
@section('page_title','ລາຍລະອຽດສາງ')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('warehouses.index') }}" class="text-gray-500 hover:text-gray-700">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <h2 class="text-xl font-bold text-gray-800">{{ $warehouse->name }}</h2>
    <span class="font-mono text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">{{ $warehouse->code }}</span>
    <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $warehouse->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
        {{ $warehouse->is_active ? 'ໃຊ້ງານ' : 'ປິດ' }}
    </span>
</div>

<div class="grid grid-cols-4 gap-5 mb-6">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-sm text-gray-500 mb-1">ສາຂາ</p>
        <p class="font-semibold text-gray-800">{{ $warehouse->branch?->name ?? '-' }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-sm text-gray-500 mb-1">ສິນຄ້າທັງໝົດ</p>
        <p class="text-2xl font-bold text-blue-600">{{ $warehouse->stocks->count() }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-sm text-gray-500 mb-1">ລາຍການໝົດສາງ</p>
        <p class="text-2xl font-bold text-red-500">{{ $warehouse->stocks->where('quantity', 0)->count() }}</p>
    </div>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
        <p class="text-sm text-gray-500 mb-1">ສ້າງວັນທີ</p>
        <p class="font-semibold text-gray-800">{{ $warehouse->created_at->format('d/m/Y') }}</p>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <h3 class="font-semibold text-gray-700">ສິນຄ້າໃນສາງ</h3>
        <a href="{{ route('stock.in') }}"
            class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            ຮັບເຂົ້າ
        </a>
    </div>
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">ສິນຄ້າ</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">ໝວດ</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">ຫົວໜ່ວຍ</th>
                <th class="text-right px-5 py-3 font-semibold text-gray-600">ຈຳນວນ</th>
                <th class="text-right px-5 py-3 font-semibold text-gray-600">ສະຖານະ</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($warehouse->stocks as $stock)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3">
                    <p class="font-medium text-gray-800">{{ $stock->product?->name }}</p>
                    <p class="text-xs font-mono text-gray-400">{{ $stock->product?->code }}</p>
                </td>
                <td class="px-5 py-3 text-gray-600">{{ $stock->product?->category?->name ?? '-' }}</td>
                <td class="px-5 py-3 text-gray-600">{{ $stock->product?->unit?->name ?? '-' }}</td>
                <td class="px-5 py-3 text-right font-bold text-gray-800">{{ number_format($stock->quantity) }}</td>
                <td class="px-5 py-3 text-right">
                    @if($stock->quantity == 0)
                        <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-red-100 text-red-700">ໝົດ</span>
                    @elseif($stock->product && $stock->quantity <= $stock->product->min_stock_alert)
                        <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-amber-100 text-amber-700">ໃກ້ໝົດ</span>
                    @else
                        <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-green-100 text-green-700">ປົກກະຕິ</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center py-12 text-gray-400">ຍັງບໍ່ມີສິນຄ້າໃນສາງ</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
