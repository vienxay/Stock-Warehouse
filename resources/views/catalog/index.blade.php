@extends('layouts.app')
@section('title','ຂໍ້ມູນຫຼັກ')
@section('page_title','ຂໍ້ມູນຫຼັກ')

@section('content')

{{-- Flash --}}
@if(session('success'))
<div class="mb-4 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm">{{ session('error') }}</div>
@endif

{{-- Tabs --}}
<div class="flex gap-1 mb-6 bg-white rounded-2xl shadow-sm border border-gray-100 p-1.5 w-fit">
    @foreach([
        ['tab' => 'units',     'label' => 'ໜ່ວຍນັບ',   'count' => $units->count()],
        ['tab' => 'brands',    'label' => 'ຍີ່ຫໍ້',      'count' => $brands->count()],
        ['tab' => 'suppliers', 'label' => 'ຜູ້ສະໜອງ',  'count' => $suppliers->count()],
    ] as $t)
    <a href="{{ route('catalog.index', ['tab' => $t['tab']]) }}"
        class="flex items-center gap-2 px-5 py-2 rounded-xl text-sm font-medium transition
        {{ $tab === $t['tab'] ? 'bg-blue-600 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100' }}">
        {{ $t['label'] }}
        <span class="text-xs px-1.5 py-0.5 rounded-full {{ $tab === $t['tab'] ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-600' }}">
            {{ $t['count'] }}
        </span>
    </a>
    @endforeach
</div>

{{-- ================================================================ --}}
{{-- UNITS TAB --}}
{{-- ================================================================ --}}
@if($tab === 'units')
<div class="flex items-center justify-between mb-4">
    <div>
        <h2 class="text-lg font-bold text-gray-800">ໜ່ວຍນັບ</h2>
        <p class="text-xs text-gray-500 mt-0.5">ໜ່ວຍທີ່ໃຊ້ກັບສິນຄ້າ ເຊັ່ນ: ກ່ອງ, ອັນ, ກິໂລ...</p>
    </div>
    <button onclick="document.getElementById('addUnitModal').style.display='flex'"
        class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        ເພີ່ມໜ່ວຍ
    </button>
</div>
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">ຊື່ໜ່ວຍ</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">ຕົວຫຍໍ້</th>
                <th class="text-center px-5 py-3 font-semibold text-gray-600">ສິນຄ້າ</th>
                <th class="text-center px-5 py-3 font-semibold text-gray-600">ຈັດການ</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($units as $u)
            <tr class="hover:bg-gray-50" data-id="{{ $u->id }}" data-name="{{ $u->name }}" data-abbr="{{ $u->abbreviation ?? '' }}">
                <td class="px-5 py-3 font-medium text-gray-800">{{ $u->name }}</td>
                <td class="px-5 py-3"><span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">{{ $u->abbreviation ?? '-' }}</span></td>
                <td class="px-5 py-3 text-center">
                    <span class="text-xs font-semibold {{ $u->products_count > 0 ? 'text-blue-700' : 'text-gray-400' }}">{{ $u->products_count }}</span>
                </td>
                <td class="px-5 py-3">
                    <div class="flex items-center justify-center gap-2">
                        <button class="unit-edit-btn p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        @if($u->products_count == 0)
                        <form method="POST" action="{{ route('catalog.units.destroy', $u) }}"
                            data-confirm="ລຶບໜ່ວຍ {{ $u->name }}?">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg">
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
            <tr><td colspan="4" class="text-center py-12 text-gray-400">ຍັງບໍ່ມີໜ່ວຍ</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ================================================================ --}}
{{-- BRANDS TAB --}}
{{-- ================================================================ --}}
@elseif($tab === 'brands')
<div class="flex items-center justify-between mb-4">
    <div>
        <h2 class="text-lg font-bold text-gray-800">ຍີ່ຫໍ້ສິນຄ້າ</h2>
        <p class="text-xs text-gray-500 mt-0.5">ຍີ່ຫໍ້ຜູ້ຜະລິດ ທີ່ໃຊ້ໃນລະບົບ</p>
    </div>
    <button onclick="document.getElementById('addBrandModal').style.display='flex'"
        class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        ເພີ່ມຍີ່ຫໍ້
    </button>
</div>
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">ຊື່ຍີ່ຫໍ້</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">ລະຫັດ</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">ລາຍລະອຽດ</th>
                <th class="text-center px-5 py-3 font-semibold text-gray-600">ສິນຄ້າ</th>
                <th class="text-center px-5 py-3 font-semibold text-gray-600">ຈັດການ</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($brands as $b)
            <tr class="hover:bg-gray-50"
                data-id="{{ $b->id }}" data-name="{{ $b->name }}"
                data-code="{{ $b->code ?? '' }}" data-desc="{{ $b->description ?? '' }}">
                <td class="px-5 py-3 font-medium text-gray-800">{{ $b->name }}</td>
                <td class="px-5 py-3"><span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">{{ $b->code ?? '-' }}</span></td>
                <td class="px-5 py-3 text-gray-500 text-xs max-w-[200px] truncate">{{ $b->description ?? '-' }}</td>
                <td class="px-5 py-3 text-center">
                    <span class="text-xs font-semibold {{ $b->products_count > 0 ? 'text-blue-700' : 'text-gray-400' }}">{{ $b->products_count }}</span>
                </td>
                <td class="px-5 py-3">
                    <div class="flex items-center justify-center gap-2">
                        <button class="brand-edit-btn p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        @if($b->products_count == 0)
                        <form method="POST" action="{{ route('catalog.brands.destroy', $b) }}"
                            data-confirm="ລຶບຍີ່ຫໍ້ {{ $b->name }}?">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg">
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
            <tr><td colspan="5" class="text-center py-12 text-gray-400">ຍັງບໍ່ມີຍີ່ຫໍ້</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- ================================================================ --}}
{{-- SUPPLIERS TAB --}}
{{-- ================================================================ --}}
@elseif($tab === 'suppliers')
<div class="flex items-center justify-between mb-4">
    <div>
        <h2 class="text-lg font-bold text-gray-800">ຜູ້ສະໜອງ</h2>
        <p class="text-xs text-gray-500 mt-0.5">ບໍລິສັດ ຫຼື ບຸກຄົນທີ່ສະໜອງສິນຄ້າ</p>
    </div>
    <button onclick="document.getElementById('addSupplierModal').style.display='flex'"
        class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        ເພີ່ມຜູ້ສະໜອງ
    </button>
</div>
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">ຊື່ຜູ້ສະໜອງ</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">ລະຫັດ</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">ຜູ້ຕິດຕໍ່</th>
                <th class="text-left px-5 py-3 font-semibold text-gray-600">ໂທ / ອີເມວ</th>
                <th class="text-center px-5 py-3 font-semibold text-gray-600">ສິນຄ້າ</th>
                <th class="text-center px-5 py-3 font-semibold text-gray-600">ຈັດການ</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($suppliers as $s)
            <tr class="hover:bg-gray-50"
                data-id="{{ $s->id }}" data-name="{{ $s->name }}"
                data-code="{{ $s->code ?? '' }}" data-contact="{{ $s->contact_person ?? '' }}"
                data-phone="{{ $s->phone ?? '' }}" data-email="{{ $s->email ?? '' }}"
                data-address="{{ $s->address ?? '' }}">
                <td class="px-5 py-3 font-medium text-gray-800">{{ $s->name }}</td>
                <td class="px-5 py-3"><span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">{{ $s->code ?? '-' }}</span></td>
                <td class="px-5 py-3 text-gray-600 text-xs">{{ $s->contact_person ?? '-' }}</td>
                <td class="px-5 py-3 text-gray-500 text-xs space-y-0.5">
                    @if($s->phone)<div>📞 {{ $s->phone }}</div>@endif
                    @if($s->email)<div>✉ {{ $s->email }}</div>@endif
                    @if(!$s->phone && !$s->email)-@endif
                </td>
                <td class="px-5 py-3 text-center">
                    <span class="text-xs font-semibold {{ $s->products_count > 0 ? 'text-blue-700' : 'text-gray-400' }}">{{ $s->products_count }}</span>
                </td>
                <td class="px-5 py-3">
                    <div class="flex items-center justify-center gap-2">
                        <button class="supplier-edit-btn p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        @if($s->products_count == 0)
                        <form method="POST" action="{{ route('catalog.suppliers.destroy', $s) }}"
                            data-confirm="ລຶບຜູ້ສະໜອງ {{ $s->name }}?">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg">
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
            <tr><td colspan="6" class="text-center py-12 text-gray-400">ຍັງບໍ່ມີຜູ້ສະໜອງ</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endif

{{-- ================================================================ --}}
{{-- MODALS — UNITS --}}
{{-- ================================================================ --}}
<div id="addUnitModal" style="display:none;position:fixed;inset:0;z-index:50;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,0.5)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <h3 class="font-semibold text-lg mb-4">ເພີ່ມໜ່ວຍໃໝ່</h3>
        <form method="POST" action="{{ route('catalog.units.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ຊື່ໜ່ວຍ <span class="text-red-500">*</span></label>
                <input type="text" name="name" required placeholder="ກ່ອງ, ອັນ, ກິໂລ..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ຕົວຫຍໍ້</label>
                <input type="text" name="abbreviation" placeholder="kg, pcs, box..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl text-sm font-medium transition">ບັນທຶກ</button>
                <button type="button" onclick="document.getElementById('addUnitModal').style.display='none'"
                    class="flex-1 border border-gray-300 text-gray-700 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 transition">ຍົກເລີກ</button>
            </div>
        </form>
    </div>
</div>
<div id="editUnitModal" style="display:none;position:fixed;inset:0;z-index:50;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,0.5)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <h3 class="font-semibold text-lg mb-4">ແກ້ໄຂໜ່ວຍ</h3>
        <form method="POST" id="editUnitForm" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ຊື່ໜ່ວຍ <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="euName" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ຕົວຫຍໍ້</label>
                <input type="text" name="abbreviation" id="euAbbr"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="submit" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white py-2.5 rounded-xl text-sm font-medium transition">ບັນທຶກ</button>
                <button type="button" onclick="document.getElementById('editUnitModal').style.display='none'"
                    class="flex-1 border border-gray-300 text-gray-700 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 transition">ຍົກເລີກ</button>
            </div>
        </form>
    </div>
</div>

{{-- ================================================================ --}}
{{-- MODALS — BRANDS --}}
{{-- ================================================================ --}}
<div id="addBrandModal" style="display:none;position:fixed;inset:0;z-index:50;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,0.5)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <h3 class="font-semibold text-lg mb-4">ເພີ່ມຍີ່ຫໍ້ໃໝ່</h3>
        <form method="POST" action="{{ route('catalog.brands.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ຊື່ຍີ່ຫໍ້ <span class="text-red-500">*</span></label>
                <input type="text" name="name" required placeholder="Samsung, Toyota..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ລະຫັດ</label>
                <input type="text" name="code" placeholder="SAM, TOY..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ລາຍລະອຽດ</label>
                <input type="text" name="description" placeholder="ອະທິບາຍຍີ່ຫໍ້..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl text-sm font-medium transition">ບັນທຶກ</button>
                <button type="button" onclick="document.getElementById('addBrandModal').style.display='none'"
                    class="flex-1 border border-gray-300 text-gray-700 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 transition">ຍົກເລີກ</button>
            </div>
        </form>
    </div>
</div>
<div id="editBrandModal" style="display:none;position:fixed;inset:0;z-index:50;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,0.5)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6">
        <h3 class="font-semibold text-lg mb-4">ແກ້ໄຂຍີ່ຫໍ້</h3>
        <form method="POST" id="editBrandForm" class="space-y-4">
            @csrf @method('PUT')
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ຊື່ຍີ່ຫໍ້ <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="ebName" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ລະຫັດ</label>
                <input type="text" name="code" id="ebCode"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ລາຍລະອຽດ</label>
                <input type="text" name="description" id="ebDesc"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="submit" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white py-2.5 rounded-xl text-sm font-medium transition">ບັນທຶກ</button>
                <button type="button" onclick="document.getElementById('editBrandModal').style.display='none'"
                    class="flex-1 border border-gray-300 text-gray-700 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 transition">ຍົກເລີກ</button>
            </div>
        </form>
    </div>
</div>

{{-- ================================================================ --}}
{{-- MODALS — SUPPLIERS --}}
{{-- ================================================================ --}}
<div id="addSupplierModal" style="display:none;position:fixed;inset:0;z-index:50;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,0.5)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6">
        <h3 class="font-semibold text-lg mb-4">ເພີ່ມຜູ້ສະໜອງໃໝ່</h3>
        <form method="POST" action="{{ route('catalog.suppliers.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ຊື່ຜູ້ສະໜອງ <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ລະຫັດ</label>
                    <input type="text" name="code"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ຜູ້ຕິດຕໍ່</label>
                <input type="text" name="contact_person" placeholder="ຊື່ຜູ້ຕິດຕໍ່..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ເບີໂທ</label>
                    <input type="text" name="phone" placeholder="020 XXXX XXXX"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ອີເມວ</label>
                    <input type="email" name="email"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ທີ່ຢູ່</label>
                <input type="text" name="address"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-2.5 rounded-xl text-sm font-medium transition">ບັນທຶກ</button>
                <button type="button" onclick="document.getElementById('addSupplierModal').style.display='none'"
                    class="flex-1 border border-gray-300 text-gray-700 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 transition">ຍົກເລີກ</button>
            </div>
        </form>
    </div>
</div>
<div id="editSupplierModal" style="display:none;position:fixed;inset:0;z-index:50;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,0.5)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg p-6">
        <h3 class="font-semibold text-lg mb-4">ແກ້ໄຂຜູ້ສະໜອງ</h3>
        <form method="POST" id="editSupplierForm" class="space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ຊື່ຜູ້ສະໜອງ <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="esName" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ລະຫັດ</label>
                    <input type="text" name="code" id="esCode"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ຜູ້ຕິດຕໍ່</label>
                <input type="text" name="contact_person" id="esContact"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ເບີໂທ</label>
                    <input type="text" name="phone" id="esPhone"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ອີເມວ</label>
                    <input type="email" name="email" id="esEmail"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ທີ່ຢູ່</label>
                <input type="text" name="address" id="esAddress"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="submit" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white py-2.5 rounded-xl text-sm font-medium transition">ບັນທຶກ</button>
                <button type="button" onclick="document.getElementById('editSupplierModal').style.display='none'"
                    class="flex-1 border border-gray-300 text-gray-700 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-50 transition">ຍົກເລີກ</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const catalogUrl = "{{ url('/catalog') }}";

// Units
document.querySelectorAll('.unit-edit-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const r = btn.closest('tr');
        document.getElementById('editUnitForm').action = catalogUrl + '/units/' + r.dataset.id;
        document.getElementById('euName').value = r.dataset.name;
        document.getElementById('euAbbr').value = r.dataset.abbr;
        document.getElementById('editUnitModal').style.display = 'flex';
    });
});

// Brands
document.querySelectorAll('.brand-edit-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const r = btn.closest('tr');
        document.getElementById('editBrandForm').action = catalogUrl + '/brands/' + r.dataset.id;
        document.getElementById('ebName').value = r.dataset.name;
        document.getElementById('ebCode').value = r.dataset.code;
        document.getElementById('ebDesc').value = r.dataset.desc;
        document.getElementById('editBrandModal').style.display = 'flex';
    });
});

// Suppliers
document.querySelectorAll('.supplier-edit-btn').forEach(function(btn) {
    btn.addEventListener('click', function() {
        const r = btn.closest('tr');
        document.getElementById('editSupplierForm').action = catalogUrl + '/suppliers/' + r.dataset.id;
        document.getElementById('esName').value    = r.dataset.name;
        document.getElementById('esCode').value    = r.dataset.code;
        document.getElementById('esContact').value = r.dataset.contact;
        document.getElementById('esPhone').value   = r.dataset.phone;
        document.getElementById('esEmail').value   = r.dataset.email;
        document.getElementById('esAddress').value = r.dataset.address;
        document.getElementById('editSupplierModal').style.display = 'flex';
    });
});
</script>
@endpush
@endsection
