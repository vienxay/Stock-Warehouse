@extends('layouts.app')
@section('title','ເບີກຈ່າຍ')
@section('page_title','ເບີກຈ່າຍ')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

    {{-- ===== FORM ===== --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- Barcode Scanner --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-800 mb-3 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 3.5V16m0 0v.5M20 16h.5M4 6h.01M4 20h.01M20 4h.01M4 4h.01"/>
                </svg>
                ສະແກນ Barcode
            </h3>
            <div class="relative">
                <input type="text" id="barcodeInput" placeholder="ສະແກນ ຫຼື ພິມ Barcode / ລະຫັດ..."
                    autocomplete="off"
                    class="w-full border-2 border-blue-300 focus:border-blue-500 rounded-xl px-4 py-3 pr-20 text-sm focus:outline-none focus:ring-2 focus:ring-blue-200 font-mono"/>
                <div class="absolute right-2 top-1/2 -translate-y-1/2 flex items-center gap-1">
                    <x-camera-scanner input-id="barcodeInput" callback="doBarcodeLookup"/>
                    <button type="button" onclick="doBarcodeLookup()"
                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </button>
                </div>
            </div>
            <div id="barcodeResult" class="hidden mt-3 p-3 rounded-xl border text-sm"></div>
        </div>

        {{-- Form --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-800 mb-4">ເບີກຈ່າຍສາງ</h3>
            <form method="POST" action="{{ route('stock.out.store') }}" class="space-y-4" id="stockOutForm">
                @csrf

                {{-- Product --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ສິນຄ້າ <span class="text-red-500">*</span></label>
                    <select name="product_id" id="productSelect" required onchange="onProductChange()"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- ເລືອກສິນຄ້າ --</option>
                        @foreach($products as $p)
                            <option value="{{ $p->id }}"
                                data-code="{{ $p->code }}"
                                data-barcode="{{ $p->barcode }}">
                                {{ $p->code }} - {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Warehouse --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ສາງ <span class="text-red-500">*</span></label>
                    <select name="warehouse_id" id="warehouseSelect" required onchange="loadStock()"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- ເລືອກສາງ --</option>
                        @foreach($warehouses as $w)
                            <option value="{{ $w->id }}">{{ $w->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Current Stock --}}
                <div id="currentStock" style="display:none" class="rounded-xl border px-4 py-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">ຈຳນວນໃນສາງ:</span>
                        <span id="stockQty" class="font-bold text-2xl text-blue-700">0</span>
                    </div>
                    <div id="stockAlert" style="display:none" class="mt-1 text-xs font-medium text-red-600">⚠ ໃກ້ໝົດ / ໝົດສາງ</div>
                </div>

                {{-- Quantity --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ຈຳນວນເບີກ <span class="text-red-500">*</span></label>
                    <input type="number" name="quantity" id="qtyInput" min="1" required placeholder="0"
                        oninput="checkQty(this)"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg font-bold"/>
                    <p id="qtyError" class="hidden mt-1 text-xs text-red-600">ຈຳນວນເກີນໃນສາງ</p>
                </div>

                {{-- Note --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ໝາຍເຫດ</label>
                    <textarea name="note" rows="2" placeholder="ໝາຍເຫດ..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                </div>

                <button type="submit" id="submitBtn"
                    class="w-full bg-red-600 hover:bg-red-700 text-white py-3 rounded-xl font-semibold transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                    ເບີກຈ່າຍ
                </button>
            </form>
        </div>
    </div>

    {{-- ===== HISTORY ===== --}}
    <div class="lg:col-span-3">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">ປະຫວັດເບີກຈ່າຍ</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="text-left px-4 py-3 text-gray-600 font-semibold">ສິນຄ້າ</th>
                            <th class="text-left px-4 py-3 text-gray-600 font-semibold">ສາງ</th>
                            <th class="text-right px-4 py-3 text-gray-600 font-semibold">ຈຳນວນ</th>
                            <th class="text-left px-4 py-3 text-gray-600 font-semibold">ຜູ້ດຳເນີນການ</th>
                            <th class="text-left px-4 py-3 text-gray-600 font-semibold">ວັນທີ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($movements as $m)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <p class="font-medium text-gray-800">{{ $m->product?->name }}</p>
                                <p class="text-xs text-gray-400 font-mono">{{ $m->reference_no }}</p>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $m->warehouse?->name }}</td>
                            <td class="px-4 py-3 text-right font-bold text-red-500">-{{ number_format($m->quantity) }}</td>
                            <td class="px-4 py-3 text-gray-600 text-xs">{{ $m->user?->name }}</td>
                            <td class="px-4 py-3 text-gray-500 text-xs">{{ $m->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-10 text-gray-400">ຍັງບໍ່ມີປະຫວັດ</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($movements->hasPages())
            <div class="px-5 py-4 border-t">{{ $movements->links() }}</div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
const barcodeUrl = "{{ route('stock.barcode') }}";
const stockUrl   = "{{ route('stock.quantity') }}";
let currentQtyInStock = 0;

// Auto-focus barcode field
document.getElementById('barcodeInput').focus();

// ======== Barcode Scanner ========
document.getElementById('barcodeInput').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        doBarcodeLookup();
    }
});

async function doBarcodeLookup() {
    const q = document.getElementById('barcodeInput').value.trim();
    if (!q) return;

    const resultBox = document.getElementById('barcodeResult');
    resultBox.className = 'mt-3 p-3 rounded-xl border text-sm';
    resultBox.textContent = 'ກຳລັງຄົ້ນຫາ...';
    resultBox.classList.remove('hidden');

    try {
        const res  = await fetch(`${barcodeUrl}?q=${encodeURIComponent(q)}`);
        const data = await res.json();

        if (!data.found) {
            resultBox.className = 'mt-3 p-3 rounded-xl border text-sm bg-red-50 border-red-200 text-red-700';
            resultBox.innerHTML = `<strong>ບໍ່ພົບສິນຄ້າ:</strong> "${q}"`;
            document.getElementById('barcodeInput').select();
            return;
        }

        document.getElementById('productSelect').value = data.id;
        onProductChange();

        resultBox.className = 'mt-3 p-3 rounded-xl border text-sm bg-green-50 border-green-200 text-green-700';
        resultBox.innerHTML = `✓ <strong>${data.code}</strong> — ${data.name} <span class="text-green-500">(${data.unit})</span>`;

        document.getElementById('barcodeInput').value = '';
        setTimeout(() => document.getElementById('qtyInput').focus(), 100);

    } catch (e) {
        resultBox.className = 'mt-3 p-3 rounded-xl border text-sm bg-red-50 border-red-200 text-red-700';
        resultBox.textContent = 'ເກີດຂໍ້ຜິດພາດ ກະລຸນາລອງໃໝ່';
    }
}

// ======== Stock Lookup ========
function onProductChange() { loadStock(); }

async function loadStock() {
    const pid = document.getElementById('productSelect').value;
    const wid = document.getElementById('warehouseSelect').value;
    const box = document.getElementById('currentStock');
    if (!pid || !wid) { box.style.display = 'none'; currentQtyInStock = 0; return; }

    const res = await fetch(`${stockUrl}?product_id=${pid}&warehouse_id=${wid}`);
    const d   = await res.json();
    currentQtyInStock = d.quantity;

    document.getElementById('stockQty').textContent = d.quantity.toLocaleString();
    document.getElementById('qtyInput').max = d.quantity;

    const stockAlert = document.getElementById('stockAlert');
    if (d.quantity <= 0) {
        box.className = 'rounded-xl border px-4 py-3 text-sm bg-red-50 border-red-200';
        stockAlert.style.display = 'block';
        stockAlert.textContent = '⚠ ໝົດສາງ — ບໍ່ສາມາດເບີກໄດ້';
    } else if (d.quantity <= 10) {
        box.className = 'rounded-xl border px-4 py-3 text-sm bg-amber-50 border-amber-200';
        stockAlert.style.display = 'block';
        stockAlert.textContent = '⚠ ຈຳນວນໃກ້ໝົດ';
    } else {
        box.className = 'rounded-xl border px-4 py-3 text-sm bg-blue-50 border-blue-200';
        stockAlert.style.display = 'none';
    }
    box.style.display = 'block';
}

// ======== Qty Validation ========
function checkQty(input) {
    const err = document.getElementById('qtyError');
    const btn = document.getElementById('submitBtn');
    if (currentQtyInStock > 0 && parseInt(input.value) > currentQtyInStock) {
        input.classList.add('border-red-400');
        err.classList.remove('hidden');
        btn.disabled = true;
        btn.classList.add('opacity-50', 'cursor-not-allowed');
    } else {
        input.classList.remove('border-red-400');
        err.classList.add('hidden');
        btn.disabled = false;
        btn.classList.remove('opacity-50', 'cursor-not-allowed');
    }
}
</script>
@endpush
@endsection
