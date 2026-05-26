@extends('layouts.app')
@section('title','ນຳເຂົ້າສາງ')
@section('page_title','ນຳເຂົ້າສາງ')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

    {{-- ===== FORM ===== --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- Import Excel Button --}}
        <div class="flex gap-2">
            <button onclick="document.getElementById('importModal').style.display='flex'"
                class="flex-1 flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                ນຳເຂົ້າ Excel
            </button>
            <a href="{{ route('stock.template') }}"
                class="flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2.5 rounded-xl text-sm font-medium transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Template
            </a>
        </div>

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
            <h3 class="font-semibold text-gray-800 mb-4">ນຳເຂົ້າສາງໃໝ່</h3>
            <form method="POST" action="{{ route('stock.in.store') }}" class="space-y-4" id="stockInForm">
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
                                data-barcode="{{ $p->barcode }}"
                                data-unit="{{ $p->unit?->abbreviation }}">
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
                <div id="currentStock" style="display:none" class="bg-blue-50 border border-blue-200 rounded-lg px-4 py-2.5 text-sm flex items-center justify-between">
                    <span class="text-blue-700">ຈຳນວນໃນສາງ:</span>
                    <span id="stockQty" class="font-bold text-blue-700 text-lg">0</span>
                </div>

                {{-- Quantity --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ຈຳນວນນຳເຂົ້າ <span class="text-red-500">*</span></label>
                    <input type="number" name="quantity" id="qtyInput" min="1" required placeholder="0"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg font-bold"/>
                </div>

                {{-- Unit Price --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ລາຄາຕໍ່ໜ່ວຍ</label>
                    <input type="number" name="unit_price" min="0" step="0.01" placeholder="0"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                </div>

                {{-- Supplier --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ຜູ້ສະໜອງ</label>
                    <select name="supplier_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- ເລືອກ --</option>
                        @foreach($suppliers as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Note --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ໝາຍເຫດ</label>
                    <textarea name="note" rows="2" placeholder="ໝາຍເຫດ..."
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
                </div>

                <button type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white py-3 rounded-xl font-semibold transition flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4M17 8l4 4m0 0l-4 4m4-4H3"/>
                    </svg>
                    ນຳເຂົ້າສາງ
                </button>
            </form>
        </div>
    </div>

    {{-- ===== HISTORY ===== --}}
    <div class="lg:col-span-3">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-800">ປະຫວັດນຳເຂົ້າ</h3>
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
                            <td class="px-4 py-3 text-right font-bold text-green-600">+{{ number_format($m->quantity) }}</td>
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

{{-- Import Modal --}}
<div id="importModal" style="display:none" class="fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-semibold text-lg">ນຳເຂົ້າຂໍ້ມູນ Stock ຈາກ Excel</h3>
            <button onclick="document.getElementById('importModal').style.display='none'"
                class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Format guide --}}
        <div class="bg-gray-50 rounded-xl p-4 mb-5 text-xs text-gray-600">
            <p class="font-semibold text-gray-700 mb-2">ຮູບແບບຄໍລຳ Excel/CSV:</p>
            <div class="overflow-x-auto">
                <table class="w-full text-xs border-collapse">
                    <thead>
                        <tr class="bg-gray-200">
                            <th class="border border-gray-300 px-2 py-1 text-left font-semibold">product_code *</th>
                            <th class="border border-gray-300 px-2 py-1 text-left font-semibold">warehouse_code *</th>
                            <th class="border border-gray-300 px-2 py-1 text-left font-semibold">quantity *</th>
                            <th class="border border-gray-300 px-2 py-1 text-left font-semibold">unit_price</th>
                            <th class="border border-gray-300 px-2 py-1 text-left font-semibold">supplier_code</th>
                            <th class="border border-gray-300 px-2 py-1 text-left font-semibold">note</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="bg-white">
                            <td class="border border-gray-300 px-2 py-1 font-mono">PRD001</td>
                            <td class="border border-gray-300 px-2 py-1 font-mono">WH001</td>
                            <td class="border border-gray-300 px-2 py-1 font-mono">100</td>
                            <td class="border border-gray-300 px-2 py-1 font-mono">50000</td>
                            <td class="border border-gray-300 px-2 py-1 font-mono">SUP001</td>
                            <td class="border border-gray-300 px-2 py-1">ຊຸດທຳອິດ</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p class="mt-2 text-gray-500">* = ບັງຄັບ | ສາມາດໃຊ້ barcode ແທນ product_code ໄດ້</p>
        </div>

        <form method="POST" action="{{ route('stock.import') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">ເລືອກໄຟລ໌ <span class="text-red-500">*</span></label>
                <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:border-emerald-400 transition cursor-pointer"
                    onclick="document.getElementById('excelFile').click()">
                    <svg class="w-10 h-10 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <p id="fileLabel" class="text-sm text-gray-500">ກົດເພື່ອເລືອກໄຟລ໌ .xlsx / .xls / .csv</p>
                    <p class="text-xs text-gray-400 mt-1">ຂະໜາດສູງສຸດ 5MB</p>
                </div>
                <input type="file" id="excelFile" name="excel_file"
                    accept=".xlsx,.xls,.csv" class="hidden"
                    onchange="document.getElementById('fileLabel').textContent = this.files[0]?.name ?? 'ເລືອກໄຟລ໌'"/>
            </div>
            <div class="flex gap-3">
                <button type="submit"
                    class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white py-2.5 rounded-xl text-sm font-medium transition">
                    ນຳເຂົ້າ
                </button>
                <button type="button" onclick="document.getElementById('importModal').style.display='none'"
                    class="flex-1 border border-gray-300 text-gray-700 hover:bg-gray-50 py-2.5 rounded-xl text-sm font-medium transition">
                    ຍົກເລີກ
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const barcodeUrl  = "{{ route('stock.barcode') }}";
const stockUrl    = "{{ route('stock.quantity') }}";

// ======== Barcode Scanner ========
const barcodeInput = document.getElementById('barcodeInput');
let barcodeBuffer = '';
let barcodeTimer  = null;

// Auto-focus barcode field on load
barcodeInput.focus();

// Detect fast scanner input (< 50ms between keystrokes = scanner)
barcodeInput.addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        doBarcodeLookup();
        return;
    }
});

async function doBarcodeLookup() {
    const q = barcodeInput.value.trim();
    if (!q) return;

    const resultBox = document.getElementById('barcodeResult');
    resultBox.className = 'mt-3 p-3 rounded-xl border text-sm';
    resultBox.textContent = 'ກຳລັງຄົ້ນຫາ...';
    resultBox.classList.remove('hidden');

    try {
        const res = await fetch(`${barcodeUrl}?q=${encodeURIComponent(q)}`);
        const data = await res.json();

        if (!data.found) {
            resultBox.className = 'mt-3 p-3 rounded-xl border text-sm bg-red-50 border-red-200 text-red-700';
            resultBox.innerHTML = `<strong>ບໍ່ພົບສິນຄ້າ:</strong> "${q}"`;
            barcodeInput.select();
            return;
        }

        // Select product in dropdown
        const sel = document.getElementById('productSelect');
        sel.value = data.id;
        onProductChange();

        resultBox.className = 'mt-3 p-3 rounded-xl border text-sm bg-green-50 border-green-200 text-green-700';
        resultBox.innerHTML = `✓ <strong>${data.code}</strong> — ${data.name} <span class="text-green-500">(${data.unit})</span>`;

        barcodeInput.value = '';
        // Focus quantity
        setTimeout(() => document.getElementById('qtyInput').focus(), 100);

    } catch (e) {
        resultBox.className = 'mt-3 p-3 rounded-xl border text-sm bg-red-50 border-red-200 text-red-700';
        resultBox.textContent = 'ເກີດຂໍ້ຜິດພາດ ກະລຸນາລອງໃໝ່';
    }
}

// ======== Stock Lookup ========
function onProductChange() {
    loadStock();
}

async function loadStock() {
    const pid = document.getElementById('productSelect').value;
    const wid = document.getElementById('warehouseSelect').value;
    const box = document.getElementById('currentStock');
    if (!pid || !wid) { box.style.display = 'none'; return; }

    const res = await fetch(`${stockUrl}?product_id=${pid}&warehouse_id=${wid}`);
    const d   = await res.json();
    document.getElementById('stockQty').textContent = d.quantity.toLocaleString();
    box.style.display = 'flex';
}
</script>
@endpush
@endsection
