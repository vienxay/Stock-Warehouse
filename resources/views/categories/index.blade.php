@extends('layouts.app')
@section('title','ໝວດໝູ່')
@section('page_title','ໝວດໝູ່ສິນຄ້າ')

@section('content')
<div class="flex flex-wrap items-center justify-between mb-6 gap-3">
    <h2 class="text-xl font-bold text-gray-800">ໝວດໝູ່ທັງໝົດ</h2>
    <button id="openAddModal"
        class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        ເພີ່ມໝວດໝູ່
    </button>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">#</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">ລະຫັດ</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">ຊື່ໝວດໝູ່</th>
                <th class="text-center px-5 py-3 font-semibold text-gray-600">ສິນຄ້າ</th>
                <th class="text-center px-5 py-3 font-semibold text-gray-600">ສະຖານະ</th>
                <th class="text-center px-5 py-3 font-semibold text-gray-600">ຈັດການ</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($categories as $i => $cat)
            <tr class="hover:bg-gray-50">
                <td class="px-5 py-3 text-gray-400">{{ $categories->firstItem() + $i }}</td>
                <td class="px-5 py-3"><span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">{{ $cat->code ?? '-' }}</span></td>
                <td class="px-5 py-3 font-medium text-gray-800">{{ $cat->name }}</td>
                <td class="px-5 py-3 text-center text-gray-600">{{ $cat->products_count }}</td>
                <td class="px-5 py-3 text-center">
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $cat->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                        {{ $cat->is_active ? 'ໃຊ້ງານ' : 'ປິດ' }}
                    </span>
                </td>
                <td class="px-5 py-3">
                    <div class="flex items-center justify-center gap-2">
                        <button class="edit-cat-btn p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition"
                            data-id="{{ $cat->id }}"
                            data-name="{{ $cat->name }}"
                            data-code="{{ $cat->code ?? '' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        @if($cat->products_count == 0)
                        <form method="POST" action="{{ route('categories.destroy', $cat) }}"
                            data-confirm="ລຶບໝວດໝູ່ {{ addslashes($cat->name) }}?">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center py-12 text-gray-400">ຍັງບໍ່ມີໝວດໝູ່</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($categories->hasPages())
    <div class="px-5 py-4 border-t">{{ $categories->links() }}</div>
    @endif
</div>

{{-- Add Modal --}}
<div id="addModal" style="display:none" class="fixed inset-0 bg-black/50 items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <h3 class="font-semibold text-lg mb-4">ເພີ່ມໝວດໝູ່ໃໝ່</h3>
        <form method="POST" action="{{ route('categories.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ຊື່ໝວດໝູ່ <span class="text-red-500">*</span></label>
                <input type="text" name="name" required placeholder="ໃສ່ຊື່ໝວດໝູ່"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ລະຫັດ</label>
                <input type="text" name="code" placeholder="ELEC"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl text-sm font-medium transition">ບັນທຶກ</button>
                <button type="button" id="closeAddModal"
                    class="flex-1 border border-gray-300 text-gray-700 hover:bg-gray-50 py-2.5 rounded-xl text-sm font-medium transition">ຍົກເລີກ</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div id="editModal" style="display:none" class="fixed inset-0 bg-black/50 items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <h3 class="font-semibold text-lg mb-4">ແກ້ໄຂໝວດໝູ່</h3>
        <form method="POST" id="editForm" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ຊື່ໝວດໝູ່</label>
                <input type="text" name="name" id="editName" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ລະຫັດ</label>
                <input type="text" name="code" id="editCode"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white py-2.5 rounded-xl text-sm font-medium transition">ບັນທຶກ</button>
                <button type="button" id="closeEditModal"
                    class="flex-1 border border-gray-300 text-gray-700 hover:bg-gray-50 py-2.5 rounded-xl text-sm font-medium transition">ຍົກເລີກ</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const categoryBaseUrl = "{{ url('/categories') }}";

document.getElementById('openAddModal').addEventListener('click', function() {
    document.getElementById('addModal').style.display = 'flex';
});
document.getElementById('closeAddModal').addEventListener('click', function() {
    document.getElementById('addModal').style.display = 'none';
});
document.getElementById('closeEditModal').addEventListener('click', function() {
    document.getElementById('editModal').style.display = 'none';
});

document.querySelectorAll('.edit-cat-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        document.getElementById('editForm').action = categoryBaseUrl + '/' + this.dataset.id;
        document.getElementById('editName').value = this.dataset.name;
        document.getElementById('editCode').value = this.dataset.code;
        document.getElementById('editModal').style.display = 'flex';
    });
});
</script>
@endpush
@endsection
