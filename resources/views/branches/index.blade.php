@extends('layouts.app')
@section('title','ສາຂາ')
@section('page_title','ຈັດການສາຂາ')

@section('content')

{{-- Header --}}
<div class="flex flex-wrap items-center justify-between mb-6 gap-3">
    <div>
        <h2 class="text-xl font-bold text-gray-800">ສາຂາທັງໝົດ</h2>
        <p class="text-sm text-gray-500 mt-0.5">ຈັດການສາຂາ ແລະ ຂໍ້ມູນຕິດຕໍ່</p>
    </div>
    <button onclick="document.getElementById('addModal').style.display='flex'"
        class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        ເພີ່ມສາຂາ
    </button>
</div>

{{-- Flash messages --}}
@if(session('success'))
<div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">
    {{ session('success') }}
</div>
@endif
@if(session('error'))
<div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">
    {{ session('error') }}
</div>
@endif

{{-- Table --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">ລະຫັດ</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">ຊື່ສາຂາ</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">ໂທ / ອີເມວ</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">ທີ່ຢູ່</th>
                <th class="text-center px-5 py-3 font-semibold text-gray-600">ສາງ</th>
                <th class="text-center px-5 py-3 font-semibold text-gray-600">ຜູ້ໃຊ້</th>
                <th class="text-center px-5 py-3 font-semibold text-gray-600">ສະຖານະ</th>
                <th class="text-center px-5 py-3 font-semibold text-gray-600">ຈັດການ</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($branches as $branch)
            <tr class="hover:bg-gray-50"
                data-id="{{ $branch->id }}"
                data-name="{{ $branch->name }}"
                data-code="{{ $branch->code }}"
                data-phone="{{ $branch->phone ?? '' }}"
                data-email="{{ $branch->email ?? '' }}"
                data-address="{{ $branch->address ?? '' }}">
                <td class="px-5 py-3 font-mono text-xs text-gray-500">{{ $branch->code }}</td>
                <td class="px-5 py-3">
                    <p class="font-semibold text-gray-800">{{ $branch->name }}</p>
                </td>
                <td class="px-5 py-3 text-gray-600 text-xs space-y-0.5">
                    @if($branch->phone)<div>📞 {{ $branch->phone }}</div>@endif
                    @if($branch->email)<div>✉ {{ $branch->email }}</div>@endif
                    @if(!$branch->phone && !$branch->email)<span class="text-gray-400">-</span>@endif
                </td>
                <td class="px-5 py-3 text-gray-500 text-xs max-w-[160px] truncate">
                    {{ $branch->address ?? '-' }}
                </td>
                <td class="px-5 py-3 text-center">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-50 text-blue-700 font-bold text-sm">
                        {{ $branch->warehouses_count }}
                    </span>
                </td>
                <td class="px-5 py-3 text-center">
                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-purple-50 text-purple-700 font-bold text-sm">
                        {{ $branch->users_count }}
                    </span>
                </td>
                <td class="px-5 py-3 text-center">
                    <form method="POST" action="{{ route('branches.toggle', $branch) }}" class="inline">
                        @csrf
                        <button type="submit"
                            class="text-xs font-medium px-3 py-1 rounded-full transition
                            {{ $branch->is_active ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                            {{ $branch->is_active ? 'ໃຊ້ງານ' : 'ປິດ' }}
                        </button>
                    </form>
                </td>
                <td class="px-5 py-3">
                    <div class="flex items-center justify-center gap-1">
                        <button class="edit-btn p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg" title="ແກ້ໄຂ">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        <form method="POST" action="{{ route('branches.destroy', $branch) }}" class="inline"
                            data-confirm="ລຶບສາຂາ {{ $branch->name }}?">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg" title="ລຶບ">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center py-16 text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    ຍັງບໍ່ມີສາຂາ
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($branches->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">{{ $branches->links() }}</div>
    @endif
</div>

{{-- ===== ADD MODAL ===== --}}
<div id="addModal" style="display:none;position:fixed;inset:0;z-index:50;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,0.5)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-semibold text-lg text-gray-800">ເພີ່ມສາຂາໃໝ່</h3>
            <button onclick="document.getElementById('addModal').style.display='none'" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form method="POST" action="{{ route('branches.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ຊື່ສາຂາ <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="ສາຂານະຄອນຫຼວງ"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ລະຫັດ <span class="text-red-500">*</span></label>
                    <input type="text" name="code" required placeholder="VTE-01"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ເບີໂທ</label>
                    <input type="text" name="phone" placeholder="020 XXXX XXXX"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ອີເມວ</label>
                    <input type="email" name="email" placeholder="branch@example.com"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ທີ່ຢູ່</label>
                <input type="text" name="address" placeholder="ທີ່ຢູ່ສາຂາ..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
            @if($errors->any())
            <div class="bg-red-50 border border-red-200 rounded-lg px-4 py-3 text-sm text-red-700">
                @foreach($errors->all() as $e)<div>• {{ $e }}</div>@endforeach
            </div>
            @endif
            <div class="flex gap-3 pt-1">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl text-sm font-medium transition">
                    ບັນທຶກ
                </button>
                <button type="button" onclick="document.getElementById('addModal').style.display='none'"
                    class="flex-1 border border-gray-300 text-gray-700 hover:bg-gray-50 py-2.5 rounded-xl text-sm font-medium transition">
                    ຍົກເລີກ
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ===== EDIT MODAL ===== --}}
<div id="editModal" style="display:none;position:fixed;inset:0;z-index:50;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,0.5)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="font-semibold text-lg text-gray-800">ແກ້ໄຂສາຂາ</h3>
            <button onclick="document.getElementById('editModal').style.display='none'" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <form method="POST" id="editForm" class="space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ຊື່ສາຂາ <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="editName" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ລະຫັດ <span class="text-red-500">*</span></label>
                    <input type="text" name="code" id="editCode" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ເບີໂທ</label>
                    <input type="text" name="phone" id="editPhone"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ອີເມວ</label>
                    <input type="email" name="email" id="editEmail"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ທີ່ຢູ່</label>
                <input type="text" name="address" id="editAddress"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="submit" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white py-2.5 rounded-xl text-sm font-medium transition">
                    ບັນທຶກການແກ້ໄຂ
                </button>
                <button type="button" onclick="document.getElementById('editModal').style.display='none'"
                    class="flex-1 border border-gray-300 text-gray-700 hover:bg-gray-50 py-2.5 rounded-xl text-sm font-medium transition">
                    ຍົກເລີກ
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const branchBaseUrl = "{{ url('/branches') }}";

document.querySelectorAll('.edit-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const row = btn.closest('tr');
        document.getElementById('editForm').action = branchBaseUrl + '/' + row.dataset.id;
        document.getElementById('editName').value    = row.dataset.name;
        document.getElementById('editCode').value    = row.dataset.code;
        document.getElementById('editPhone').value   = row.dataset.phone;
        document.getElementById('editEmail').value   = row.dataset.email;
        document.getElementById('editAddress').value = row.dataset.address;
        document.getElementById('editModal').style.display = 'flex';
    });
});

@if($errors->any())
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('addModal').style.display = 'flex';
});
@endif
</script>
@endpush
@endsection
