@extends('layouts.app')
@section('title','ສາງ')
@section('page_title','ຈັດການສາງ')

@section('content')
<div class="flex flex-wrap items-center justify-between mb-6 gap-3">
    <h2 class="text-xl font-bold text-gray-800">ສາງທັງໝົດ</h2>
    <button onclick="document.getElementById('addModal').style.display='flex'"
        class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        ເພີ່ມສາງ
    </button>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">ລະຫັດ</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">ຊື່ສາງ</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">ສາຂາ</th>
                <th class="text-center px-5 py-3 font-semibold text-gray-600">ສິນຄ້າ</th>
                <th class="text-center px-5 py-3 font-semibold text-gray-600">ສະຖານະ</th>
                <th class="text-center px-5 py-3 font-semibold text-gray-600">ຈັດການ</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($warehouses as $wh)
            <tr class="hover:bg-gray-50"
                data-id="{{ $wh->id }}"
                data-name="{{ $wh->name }}"
                data-code="{{ $wh->code }}"
                data-branch="{{ $wh->branch?->name ?? '' }}">
                <td class="px-5 py-3 font-mono text-xs text-gray-500">{{ $wh->code }}</td>
                <td class="px-5 py-3 font-medium text-gray-800">
                    <a href="{{ route('warehouses.show', $wh) }}" class="hover:text-blue-600 transition">{{ $wh->name }}</a>
                </td>
                <td class="px-5 py-3 text-gray-600">{{ $wh->branch?->name ?? '-' }}</td>
                <td class="px-5 py-3 text-center font-bold text-gray-700">{{ $wh->stocks_count }}</td>
                <td class="px-5 py-3 text-center">
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $wh->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $wh->is_active ? 'ໃຊ້ງານ' : 'ປິດ' }}
                    </span>
                </td>
                <td class="px-5 py-3">
                    <div class="flex items-center justify-center gap-2">
                        <a href="{{ route('warehouses.show', $wh) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </a>
                        <button class="edit-btn p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-12 text-gray-400">ຍັງບໍ່ມີສາງ</td></tr>
            @endforelse
        </tbody>
    </table>
    </div>
    @if($warehouses->hasPages())
    <div class="px-5 py-4 border-t">{{ $warehouses->links() }}</div>
    @endif
</div>

{{-- Add Modal --}}
<div id="addModal" style="display:none;position:fixed;inset:0;z-index:50;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,0.5)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <h3 class="font-semibold text-lg mb-4">ເພີ່ມສາງໃໝ່</h3>
        <form method="POST" action="{{ route('warehouses.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ຊື່ສາງ <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ລະຫັດ <span class="text-red-500">*</span></label>
                    <input type="text" name="code" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ສາຂາ <span class="text-red-500">*</span></label>
                <input type="text" name="branch_name" required list="branchList"
                    placeholder="ພິມຊື່ສາຂາ ຫຼື ເລືອກ..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                <datalist id="branchList">
                    @foreach($branches as $b)
                        <option value="{{ $b->name }}">
                    @endforeach
                </datalist>
                <p class="text-xs text-gray-400 mt-1">ພິມຊື່ໃໝ່ ລະບົບຈະສ້າງສາຂາໃຫ້ອັດຕະໂນມັດ</p>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl text-sm font-medium transition">ບັນທຶກ</button>
                <button type="button" onclick="document.getElementById('addModal').style.display='none'"
                    class="flex-1 border border-gray-300 text-gray-700 hover:bg-gray-50 py-2.5 rounded-xl text-sm font-medium transition">ຍົກເລີກ</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div id="editModal" style="display:none;position:fixed;inset:0;z-index:50;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,0.5)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <h3 class="font-semibold text-lg mb-4">ແກ້ໄຂສາງ</h3>
        <form method="POST" id="editForm" class="space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ຊື່ສາງ</label>
                    <input type="text" name="name" id="editName" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ລະຫັດ</label>
                    <input type="text" name="code" id="editCode" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ສາຂາ</label>
                <input type="text" name="branch_name" id="editBranch" required list="branchList"
                    placeholder="ພິມຊື່ສາຂາ ຫຼື ເລືອກ..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                <p class="text-xs text-gray-400 mt-1">ພິມຊື່ໃໝ່ ລະບົບຈະສ້າງສາຂາໃຫ້ອັດຕະໂນມັດ</p>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white py-2.5 rounded-xl text-sm font-medium transition">ບັນທຶກ</button>
                <button type="button" onclick="document.getElementById('editModal').style.display='none'"
                    class="flex-1 border border-gray-300 text-gray-700 hover:bg-gray-50 py-2.5 rounded-xl text-sm font-medium transition">ຍົກເລີກ</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const warehouseBaseUrl = "{{ url('/warehouses') }}";

document.querySelectorAll('.edit-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const row = btn.closest('tr');
        document.getElementById('editForm').action = warehouseBaseUrl + '/' + row.dataset.id;
        document.getElementById('editName').value = row.dataset.name;
        document.getElementById('editCode').value = row.dataset.code;
        document.getElementById('editBranch').value = row.dataset.branch;
        document.getElementById('editModal').style.display = 'flex';
    });
});
</script>
@endpush
@endsection
