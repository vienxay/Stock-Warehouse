@extends('layouts.app')
@section('title','ຄຳຮ້ອງຂໍ')
@section('page_title','ຄຳຮ້ອງຂໍເບີກສິນຄ້າ')

@section('content')
<div class="flex items-center justify-between mb-6">
    <h2 class="text-xl font-bold text-gray-800">ຄຳຮ້ອງທັງໝົດ</h2>
    <a href="{{ route('requests.create') }}"
        class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        ສ້າງຄຳຮ້ອງ
    </a>
</div>

{{-- Status filter --}}
<div class="flex gap-2 mb-5 flex-wrap">
    @foreach([''=>'ທັງໝົດ','pending'=>'ລໍຖ້າ','approved'=>'ອະນຸມັດ','issued'=>'ຈ່າຍແລ້ວ','rejected'=>'ປະຕິເສດ','cancelled'=>'ຍົກເລີກ'] as $val=>$label)
    <a href="{{ route('requests.index', $val ? ['status'=>$val] : []) }}"
        class="px-4 py-2 rounded-xl text-sm font-medium transition border
        {{ request('status') === $val ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-300 hover:bg-gray-50' }}">
        {{ $label }}
    </a>
    @endforeach
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">ເລກທີ</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">ຜູ້ຂໍ</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">ຈຸດປະສົງ</th>
                    <th class="text-center px-5 py-3 font-semibold text-gray-600">ລາຍການ</th>
                    <th class="text-center px-5 py-3 font-semibold text-gray-600">ສະຖານະ</th>
                    <th class="text-left px-5 py-3 font-semibold text-gray-600">ວັນທີ</th>
                    <th class="text-center px-5 py-3 font-semibold text-gray-600">ຈັດການ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($requests as $req)
                <tr class="hover:bg-gray-50">
                    <td class="px-5 py-3 font-mono font-medium text-blue-700">{{ $req->request_no }}</td>
                    <td class="px-5 py-3 text-gray-700">{{ $req->requester_name ?? $req->requester?->name }}</td>
                    <td class="px-5 py-3 text-gray-600 max-w-xs truncate">{{ $req->purpose ?? '-' }}</td>
                    <td class="px-5 py-3 text-center text-gray-700">{{ $req->items->count() }}</td>
                    <td class="px-5 py-3 text-center">
                        <span class="text-xs font-medium px-2.5 py-1 rounded-full
                            @if($req->status==='pending') bg-amber-100 text-amber-700
                            @elseif($req->status==='approved') bg-blue-100 text-blue-700
                            @elseif($req->status==='issued') bg-green-100 text-green-700
                            @elseif($req->status==='rejected') bg-red-100 text-red-700
                            @else bg-gray-100 text-gray-500
                            @endif">
                            @switch($req->status)
                                @case('pending') ລໍຖ້າ @break
                                @case('approved') ອະນຸມັດ @break
                                @case('issued') ຈ່າຍແລ້ວ @break
                                @case('rejected') ປະຕິເສດ @break
                                @default ຍົກເລີກ
                            @endswitch
                        </span>
                    </td>
                    <td class="px-5 py-3 text-gray-500 text-xs">{{ $req->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-5 py-3">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('requests.show', $req) }}"
                                class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            @if($req->isPending())
                            <form method="POST" action="{{ route('requests.destroy', $req) }}"
                                data-confirm="ຍົກເລີກຄຳຮ້ອງ?">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition">
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
                <tr><td colspan="7" class="text-center py-12 text-gray-400">ຍັງບໍ່ມີຄຳຮ້ອງ</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($requests->hasPages())
    <div class="px-5 py-4 border-t">{{ $requests->links() }}</div>
    @endif
</div>
@endsection
