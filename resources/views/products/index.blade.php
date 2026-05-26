@extends('layouts.app')
@section('title','ສິນຄ້າ')
@section('page_title','ລາຍການສິນຄ້າ')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-xl font-bold text-gray-800">ສິນຄ້າທັງໝົດ</h2>
    <a href="{{ route('products.create') }}"
        class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        ເພີ່ມສິນຄ້າ
    </a>
</div>

{{-- Filters --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-5">
    <form method="GET" class="flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="ຄົ້ນຫາ ຊື່, ລະຫັດ, ບາໂຄດ..."
            class="flex-1 min-w-48 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
        <select name="category_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">ທຸກໝວດໝູ່</option>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}" @selected(request('category_id') == $cat->id)>{{ $cat->name }}</option>
            @endforeach
        </select>
        <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">ທຸກສະຖານະ</option>
            <option value="low" @selected(request('status')=='low')>ໃກ້ໝົດ</option>
            <option value="out" @selected(request('status')=='out')>ໝົດສາງ</option>
        </select>
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition">ຄົ້ນຫາ</button>
        @if(request()->hasAny(['search','category_id','status']))
            <a href="{{ route('products.index') }}" class="border border-gray-300 text-gray-600 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm transition">ລ້າງ</a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">#</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">ລະຫັດ</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">ຊື່ສິນຄ້າ</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">ໝວດໝູ່</th>
                    <th class="text-right px-5 py-3 font-semibold text-gray-600">ລາຄາທຶນ</th>
                    <th class="text-right px-5 py-3 font-semibold text-gray-600">ລາຄາຂາຍ</th>
                    <th class="text-center px-5 py-3 font-semibold text-gray-600">ຈຳນວນທັງໝົດ</th>
                    <th class="text-center px-5 py-3 font-semibold text-gray-600">ຮູບ</th>
                    <th class="text-center px-5 py-3 font-semibold text-gray-600">ສະຖານະ</th>
                    <th class="text-center px-5 py-3 font-semibold text-gray-600">ຈັດການ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($products as $i => $product)
                @php $total = $product->warehouseStocks->sum('quantity'); @endphp
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3 text-gray-400">{{ $products->firstItem() + $i }}</td>
                    <td class="px-5 py-3">
                        <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">{{ $product->code }}</span>
                    </td>
                    <td class="px-5 py-3">
                        <p class="font-medium text-gray-800">{{ $product->name }}</p>
                        @if($product->barcode)
                            <p class="text-xs text-gray-400 font-mono">{{ $product->barcode }}</p>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-gray-600">{{ $product->category?->name ?? '-' }}</td>
                    <td class="px-5 py-3 text-right font-medium text-gray-500">
                        {{ number_format($product->cost_price) }}
                    </td>
                    <td class="px-5 py-3 text-right font-medium text-gray-800">
                        {{ number_format($product->selling_price) }}
                    </td>
                    {{-- ຈຳນວນທັງໝົດ column --}}
                    <td class="px-5 py-3 text-center">
                        <span class="font-bold text-lg {{ $total <= 0 ? 'text-red-600' : ($product->isLowStock() ? 'text-orange-500' : 'text-gray-800') }}">
                            {{ number_format($total) }}
                        </span>
                    </td>
                    {{-- ຮູບ column --}}
                    <td class="px-5 py-3 text-center">
                        @if($product->primaryImage)
                            <img src="{{ Storage::disk('public')->url($product->primaryImage->image_path) }}" alt=""
                                 class="w-10 h-10 rounded-lg object-cover border border-gray-200 mx-auto"/>
                        @else
                            <div class="w-10 h-10 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center mx-auto">
                                <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                            </div>
                        @endif
                    </td>
                    {{-- ສະຖານະ column --}}
                    <td class="px-5 py-3 text-center">
                        @if($total <= 0)
                            <span class="text-xs font-medium bg-red-100 text-red-700 px-2.5 py-1 rounded-full">ໝົດ</span>
                        @elseif($product->isLowStock())
                            <span class="text-xs font-medium bg-orange-100 text-orange-700 px-2.5 py-1 rounded-full">ໃກ້ໝົດ</span>
                        @else
                            <span class="text-xs font-medium bg-green-100 text-green-700 px-2.5 py-1 rounded-full">ປົກກະຕິ</span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('products.show', $product) }}"
                                class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="ເບິ່ງ">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            <a href="{{ route('products.edit', $product) }}"
                                class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition" title="ແກ້ໄຂ">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('products.destroy', $product) }}"
                                data-confirm="ລົບສິນຄ້າ {{ $product->name }}?">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition" title="ລຶບ">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="px-5 py-16 text-center">
                        <svg class="w-16 h-16 text-gray-200 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        <p class="text-gray-400 font-medium">ບໍ່ພົບສິນຄ້າ</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($products->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">
        {{ $products->links() }}
    </div>
    @endif
</div>
@endsection
