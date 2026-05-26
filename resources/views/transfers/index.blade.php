@extends('layouts.app')
@section('title','ໂອນສາງ')
@section('page_title','ໂອນສາງລະຫວ່າງ Warehouse')

@section('content')

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold text-gray-800">ການໂອນສາງ</h2>
        <p class="text-sm text-gray-500 mt-0.5">ໂອນສິນຄ້າລະຫວ່າງ warehouse</p>
    </div>
    @if(Auth::user()->canManageStock())
    <button onclick="document.getElementById('createModal').style.display='flex'"
        class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
        </svg>
        ສ້າງການໂອນໃໝ່
    </button>
    @endif
</div>

{{-- Flash --}}
@if(session('success'))
<div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">{{ session('error') }}</div>
@endif

{{-- Filters --}}
<form method="GET" class="flex flex-wrap gap-3 mb-5">
    <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="">ທຸກສະຖານະ</option>
        <option value="pending"   {{ request('status')==='pending'   ? 'selected' : '' }}>ລໍຖ້າ</option>
        <option value="completed" {{ request('status')==='completed' ? 'selected' : '' }}>ສຳເລັດ</option>
        <option value="cancelled" {{ request('status')==='cancelled' ? 'selected' : '' }}>ຍົກເລີກ</option>
    </select>
    <select name="from_warehouse_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        <option value="">ທຸກ Warehouse ຕົ້ນທາງ</option>
        @foreach($warehouses as $wh)
        <option value="{{ $wh->id }}" {{ request('from_warehouse_id')==$wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
        @endforeach
    </select>
    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition">ຄົ້ນຫາ</button>
    <a href="{{ route('transfers.index') }}" class="border border-gray-300 text-gray-600 px-4 py-2 rounded-lg text-sm hover:bg-gray-50 transition">ລ້າງ</a>
</form>

{{-- Table --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">ເລກທີ</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">ສິນຄ້າ</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">ຕົ້ນທາງ → ປາຍທາງ</th>
                <th class="text-right px-5 py-3 font-semibold text-gray-600">ຈຳນວນ</th>
                <th class="text-center px-5 py-3 font-semibold text-gray-600">ສະຖານະ</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">ໂດຍ / ວັນທີ</th>
                <th class="text-center px-5 py-3 font-semibold text-gray-600">ດຳເນີນການ</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($transfers as $t)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3 font-mono text-xs text-blue-700 font-semibold">{{ $t->transfer_no }}</td>
                <td class="px-5 py-3">
                    <p class="font-medium text-gray-800">{{ $t->product?->name }}</p>
                    <p class="text-xs text-gray-400">{{ $t->product?->unit?->name }}</p>
                </td>
                <td class="px-5 py-3">
                    <div class="flex items-center gap-2 text-sm">
                        <span class="bg-orange-50 text-orange-700 px-2 py-0.5 rounded font-medium text-xs">{{ $t->fromWarehouse?->name }}</span>
                        <svg class="w-3.5 h-3.5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
                        </svg>
                        <span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded font-medium text-xs">{{ $t->toWarehouse?->name }}</span>
                    </div>
                </td>
                <td class="px-5 py-3 text-right font-bold text-gray-800">{{ number_format($t->quantity) }}</td>
                <td class="px-5 py-3 text-center">
                    @if($t->status === 'pending')
                        <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-amber-100 text-amber-700">ລໍຖ້າຮັບ</span>
                    @elseif($t->status === 'completed')
                        <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-green-100 text-green-700">ສຳເລັດ</span>
                    @else
                        <span class="text-xs font-medium px-2.5 py-1 rounded-full bg-red-100 text-red-700">ຍົກເລີກ</span>
                    @endif
                </td>
                <td class="px-5 py-3 text-xs text-gray-500">
                    <p>{{ $t->creator?->name }}</p>
                    <p class="text-gray-400">{{ $t->created_at->format('d/m/Y H:i') }}</p>
                    @if($t->status === 'completed')
                    <p class="text-green-600 mt-0.5">ຮັບ: {{ $t->receiver?->name }}</p>
                    @endif
                </td>
                <td class="px-5 py-3">
                    <div class="flex items-center justify-center gap-2">
                        {{-- View note --}}
                        @if($t->note)
                        <button title="{{ $t->note }}"
                            class="p-1.5 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                            </svg>
                        </button>
                        @endif

                        @if($t->status === 'pending')
                        {{-- Receive --}}
                        <button class="receive-btn p-1.5 text-green-600 hover:bg-green-50 rounded-lg" title="ຢືນຢັນຮັບ"
                            data-id="{{ $t->id }}" data-no="{{ $t->transfer_no }}"
                            data-product="{{ $t->product?->name }}"
                            data-qty="{{ number_format($t->quantity) }}"
                            data-unit="{{ $t->product?->unit?->name }}"
                            data-to="{{ $t->toWarehouse?->name }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </button>
                        {{-- Cancel --}}
                        <form method="POST" action="{{ route('transfers.cancel', $t) }}" class="inline"
                            onsubmit="return confirm('ຍົກເລີກການໂອນ {{ $t->transfer_no }}?\nສາງຈະຖືກຄືນໄປ {{ $t->fromWarehouse?->name }}')">
                            @csrf
                            <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg" title="ຍົກເລີກ">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center py-16 text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                    </svg>
                    ຍັງບໍ່ມີການໂອນສາງ
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($transfers->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">{{ $transfers->links() }}</div>
    @endif
</div>

{{-- ===== CREATE MODAL ===== --}}
<div id="createModal" style="display:none;position:fixed;inset:0;z-index:50;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,0.5)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6">
        <div class="flex items-center justify-between mb-5">
            <div>
                <h3 class="font-semibold text-lg text-gray-800">ສ້າງການໂອນໃໝ່</h3>
                <p class="text-xs text-gray-500 mt-0.5">ໂອນສິນຄ້າລະຫວ່າງ Warehouse</p>
            </div>
            <button onclick="document.getElementById('createModal').style.display='none'" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form method="POST" action="{{ route('transfers.store') }}" class="space-y-4" id="createForm">
            @csrf
            {{-- From / To --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ຕົ້ນທາງ <span class="text-red-500">*</span></label>
                    <select name="from_warehouse_id" id="fromWh" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- ເລືອກ --</option>
                        @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ປາຍທາງ <span class="text-red-500">*</span></label>
                    <select name="to_warehouse_id" id="toWh" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- ເລືອກ --</option>
                        @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Product --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ສິນຄ້າ <span class="text-red-500">*</span></label>
                <select name="product_id" id="productSel" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- ເລືອກສິນຄ້າ --</option>
                    @foreach($products as $p)
                    <option value="{{ $p->id }}">{{ $p->name }} @if($p->unit)({{ $p->unit->name }})@endif</option>
                    @endforeach
                </select>
            </div>

            {{-- Stock info --}}
            <div id="stockInfo" class="hidden bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 text-sm text-blue-700">
                ສາງຕົ້ນທາງ: <span id="stockQty" class="font-bold">-</span> ໜ່ວຍ
            </div>

            {{-- Quantity --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ຈຳນວນ <span class="text-red-500">*</span></label>
                <input type="number" name="quantity" id="qtyInput" required min="1"
                    placeholder="0"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>

            {{-- Note --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ໝາຍເຫດ</label>
                <textarea name="note" rows="2" placeholder="ເຫດຜົນທີ່ໂອນ..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
            </div>

            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-3 text-sm text-red-700">
                @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
            </div>
            @endif

            <div class="flex gap-3 pt-1">
                <button type="submit"
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl text-sm font-medium transition">
                    ຢືນຢັນໂອນ
                </button>
                <button type="button" onclick="document.getElementById('createModal').style.display='none'"
                    class="flex-1 border border-gray-300 text-gray-700 hover:bg-gray-50 py-2.5 rounded-xl text-sm font-medium transition">
                    ຍົກເລີກ
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===== RECEIVE MODAL ===== --}}
<div id="receiveModal" style="display:none;position:fixed;inset:0;z-index:50;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,0.5)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-lg text-green-700">ຢືນຢັນຮັບສິນຄ້າ</h3>
            <button onclick="document.getElementById('receiveModal').style.display='none'" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-4 text-sm space-y-1.5">
            <div class="flex justify-between">
                <span class="text-gray-600">ເລກທີໂອນ</span>
                <span class="font-mono font-bold text-blue-700" id="rcvNo">-</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">ສິນຄ້າ</span>
                <span class="font-semibold" id="rcvProduct">-</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">ຈຳນວນ</span>
                <span class="font-bold text-green-700" id="rcvQty">-</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">ເຂົ້າສາງ</span>
                <span class="font-semibold text-blue-700" id="rcvTo">-</span>
            </div>
        </div>
        <form method="POST" id="receiveForm" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ໝາຍເຫດ <span class="text-gray-400 text-xs">(ທາງເລືອກ)</span></label>
                <textarea name="note" rows="2"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 resize-none"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit"
                    class="flex-1 bg-green-600 hover:bg-green-700 text-white py-2.5 rounded-xl text-sm font-medium transition">
                    ຢືນຢັນຮັບ
                </button>
                <button type="button" onclick="document.getElementById('receiveModal').style.display='none'"
                    class="flex-1 border border-gray-300 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                    ຍົກເລີກ
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const baseUrl = "{{ url('/transfers') }}";
const stockUrl = "{{ route('stock.quantity') }}";

// ===== Stock quantity lookup =====
function checkStock() {
    const fromId = document.getElementById('fromWh').value;
    const productId = document.getElementById('productSel').value;
    const info = document.getElementById('stockInfo');
    const qty = document.getElementById('stockQty');

    if (!fromId || !productId) { info.classList.add('hidden'); return; }

    fetch(stockUrl + '?warehouse_id=' + fromId + '&product_id=' + productId)
        .then(r => r.json())
        .then(d => {
            qty.textContent = (d.quantity ?? 0).toLocaleString();
            document.getElementById('qtyInput').max = d.quantity ?? 0;
            info.classList.remove('hidden');
        })
        .catch(() => info.classList.add('hidden'));
}

document.getElementById('fromWh').addEventListener('change', checkStock);
document.getElementById('productSel').addEventListener('change', checkStock);

// ===== Validate same warehouse =====
document.getElementById('createForm').addEventListener('submit', function(e) {
    const from = document.getElementById('fromWh').value;
    const to   = document.getElementById('toWh').value;
    if (from && to && from === to) {
        e.preventDefault();
        alert('ຕົ້ນທາງ ແລະ ປາຍທາງ ຕ້ອງຕ່າງກັນ');
    }
});

// ===== Receive modal =====
document.querySelectorAll('.receive-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const id = btn.dataset.id;
        document.getElementById('receiveForm').action = baseUrl + '/' + id + '/receive';
        document.getElementById('rcvNo').textContent      = btn.dataset.no;
        document.getElementById('rcvProduct').textContent = btn.dataset.product;
        document.getElementById('rcvQty').textContent     = btn.dataset.qty + ' ' + btn.dataset.unit;
        document.getElementById('rcvTo').textContent      = btn.dataset.to;
        document.getElementById('receiveModal').style.display = 'flex';
    });
});

@if($errors->any())
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('createModal').style.display = 'flex';
});
@endif
</script>
@endpush
@endsection
