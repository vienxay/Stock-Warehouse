@extends('layouts.app')
@section('title','ສ້າງຄຳຮ້ອງ')
@section('page_title','ສ້າງຄຳຮ້ອງຂໍເບີກສິນຄ້າ')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('requests.index') }}" class="text-gray-500 hover:text-gray-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h2 class="text-xl font-bold text-gray-800">ສ້າງຄຳຮ້ອງໃໝ່</h2>
    </div>

    <form method="POST" action="{{ route('requests.store') }}"
        class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
        @csrf

        {{-- ຂໍ້ມູນຜູ້ຮ້ອງຂໍ --}}
        <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 space-y-3">
            <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">ຂໍ້ມູນຜູ້ຮ້ອງຂໍ</p>
            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-2">
                    <label class="block text-xs text-gray-500 mb-1">ຊື່ຜູ້ຮ້ອງຂໍ <span class="text-red-500">*</span></label>
                    <input type="text" name="requester_name"
                        value="{{ old('requester_name', Auth::user()->name) }}"
                        required maxlength="100"
                        class="w-full border {{ $errors->has('requester_name') ? 'border-red-400' : 'border-gray-300' }} rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 bg-white"/>
                    @error('requester_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">ຕຳແໜ່ງ / ສາຂາ</p>
                    <p class="text-sm font-medium text-gray-700 pt-2">
                        @switch(Auth::user()->role)
                            @case('super_admin') Super Admin @break
                            @case('admin') Admin @break
                            @case('manager') ຜູ້ຈັດການ @break
                            @case('warehouse_staff') ພະນັກງານສາງ @break
                            @default ພະນັກງານ
                        @endswitch
                        @if(Auth::user()->branch?->name)
                            · {{ Auth::user()->branch->name }}
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ສາງ</label>
                <select name="warehouse_id" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- ເລືອກສາງ --</option>
                    @foreach($warehouses as $w)
                        <option value="{{ $w->id }}">{{ $w->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ຈຸດປະສົງ</label>
                <input type="text" name="purpose" placeholder="ໃຊ້ສຳລັບ..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
        </div>

        {{-- Items --}}
        <div>
            <div class="flex items-center justify-between mb-3">
                <label class="text-sm font-semibold text-gray-700">ລາຍການສິນຄ້າ <span class="text-red-500">*</span></label>
                <button type="button" onclick="addRow()"
                    class="flex items-center gap-1.5 text-blue-600 hover:text-blue-800 text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    ເພີ່ມລາຍການ
                </button>
            </div>

            <div id="items" class="space-y-3">
                <div class="item-row grid grid-cols-12 gap-3 items-start bg-gray-50 rounded-xl p-3">
                    <div class="col-span-6">
                        <select name="items[0][product_id]" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">-- ເລືອກສິນຄ້າ --</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }} ({{ $p->unit?->abbreviation }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-span-2">
                        <input type="number" name="items[0][quantity]" min="1" required placeholder="ຈຳນວນ"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                    </div>
                    <div class="col-span-3">
                        <input type="text" name="items[0][note]" placeholder="ໝາຍເຫດ"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                    </div>
                    <div class="col-span-1 flex justify-center pt-2">
                        <button type="button" onclick="removeRow(this)" class="text-gray-400 hover:text-red-500 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">ໝາຍເຫດ</label>
            <textarea name="note" rows="2" placeholder="ໝາຍເຫດເພີ່ມເຕີມ..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-medium transition">
                ສົ່ງຄຳຮ້ອງ
            </button>
            <a href="{{ route('requests.index') }}"
                class="px-6 border border-gray-300 text-gray-700 hover:bg-gray-50 py-3 rounded-xl font-medium transition text-center">
                ຍົກເລີກ
            </a>
        </div>
    </form>
</div>

@php
$productsJson = $products->map(fn($p) => [
    'id'   => $p->id,
    'text' => $p->code . ' - ' . $p->name . ' (' . ($p->unit?->abbreviation ?? '') . ')',
])->toJson(JSON_UNESCAPED_UNICODE);
@endphp

@push('scripts')
<script>
const products = {!! $productsJson !!};
let rowIndex = 1;

function addRow() {
    const opts = products.map(p => `<option value="${p.id}">${p.text}</option>`).join('');
    const row = document.createElement('div');
    row.className = 'item-row grid grid-cols-12 gap-3 items-start bg-gray-50 rounded-xl p-3';
    row.innerHTML = `
        <div class="col-span-6">
            <select name="items[${rowIndex}][product_id]" required class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- ເລືອກສິນຄ້າ --</option>${opts}
            </select>
        </div>
        <div class="col-span-2">
            <input type="number" name="items[${rowIndex}][quantity]" min="1" required placeholder="ຈຳນວນ"
                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
        </div>
        <div class="col-span-3">
            <input type="text" name="items[${rowIndex}][note]" placeholder="ໝາຍເຫດ"
                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
        </div>
        <div class="col-span-1 flex justify-center pt-2">
            <button type="button" onclick="removeRow(this)" class="text-gray-400 hover:text-red-500 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>`;
    document.getElementById('items').appendChild(row);
    rowIndex++;
}

function removeRow(btn) {
    const rows = document.querySelectorAll('.item-row');
    if (rows.length > 1) btn.closest('.item-row').remove();
}
</script>
@endpush
@endsection
