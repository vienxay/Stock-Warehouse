@extends('layouts.app')
@section('title',$request->request_no)
@section('page_title','ລາຍລະອຽດຄຳຮ້ອງ')

@section('content')
<div class="flex items-center gap-3 mb-6">
    <a href="{{ route('requests.index') }}" class="text-gray-500 hover:text-gray-700">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
    </a>
    <h2 class="text-xl font-bold text-gray-800">{{ $request->request_no }}</h2>
    <a href="{{ route('requests.pdf', $request) }}" target="_blank"
        class="ml-auto flex items-center gap-1.5 text-sm text-red-600 hover:text-red-700 border border-red-200 hover:bg-red-50 px-3 py-1.5 rounded-lg transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        ພິມໃບເບີກ PDF
    </a>
    <span class="text-xs font-medium px-3 py-1 rounded-full
        @if($request->status==='pending') bg-amber-100 text-amber-700
        @elseif($request->status==='approved') bg-blue-100 text-blue-700
        @elseif($request->status==='issued') bg-green-100 text-green-700
        @elseif($request->status==='rejected') bg-red-100 text-red-700
        @else bg-gray-100 text-gray-500 @endif">
        @switch($request->status)
            @case('pending') ລໍຖ້າອະນຸມັດ @break
            @case('approved') ອະນຸມັດແລ້ວ @break
            @case('issued') ຈ່າຍສຳເລັດ @break
            @case('rejected') ປະຕິເສດ @break
            @default ຍົກເລີກ
        @endswitch
    </span>
</div>

<div class="grid grid-cols-3 gap-5">
    <div class="col-span-2 space-y-5">
        {{-- Items table --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="px-5 py-4 border-b border-gray-100">
                <h3 class="font-semibold text-gray-700">ລາຍການສິນຄ້າ</h3>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-5 py-3 text-gray-600 font-semibold">ສິນຄ້າ</th>
                        <th class="text-right px-5 py-3 text-gray-600 font-semibold">ຂໍ</th>
                        <th class="text-right px-5 py-3 text-gray-600 font-semibold">ຈ່າຍ</th>
                        <th class="text-left px-5 py-3 text-gray-600 font-semibold">ໝາຍເຫດ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($request->items as $item)
                    <tr>
                        <td class="px-5 py-3">
                            <p class="font-medium">{{ $item->product?->name }}</p>
                            <p class="text-xs text-gray-400">{{ $item->product?->unit?->name }}</p>
                        </td>
                        <td class="px-5 py-3 text-right font-bold text-gray-800">{{ number_format($item->quantity_requested) }}</td>
                        <td class="px-5 py-3 text-right font-bold {{ $item->quantity_issued > 0 ? 'text-green-600' : 'text-gray-400' }}">
                            {{ number_format($item->quantity_issued) }}
                        </td>
                        <td class="px-5 py-3 text-gray-500 text-xs">{{ $item->note ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Actions for manager/admin --}}
        @if(Auth::user()->isManager())
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-700 mb-4">ດຳເນີນການ</h3>
            <div class="flex flex-wrap gap-3">
                @if($request->isPending())
                <form method="POST" action="{{ route('requests.approve', $request) }}">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-sm font-medium transition">
                        ✓ ອະນຸມັດ
                    </button>
                </form>
                <button onclick="document.getElementById('rejectModal').style.display='flex'"
                    class="flex items-center gap-2 bg-red-500 hover:bg-red-600 text-white px-5 py-2.5 rounded-xl text-sm font-medium transition">
                    ✕ ປະຕິເສດ
                </button>
                @endif

                @if($request->isApproved())
                <form method="POST" action="{{ route('requests.issue', $request) }}">
                    @csrf
                    <button type="submit"
                        class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-xl text-sm font-medium transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8"/>
                        </svg>
                        ຈ່າຍສິນຄ້າ
                    </button>
                </form>
                @endif

                @if($request->isIssued() && !$request->isReceived())
                <button onclick="document.getElementById('receiveModal').style.display='flex'"
                    class="flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white px-5 py-2.5 rounded-xl text-sm font-medium transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    ຢືນຢັນ Staff ມາຮັບແລ້ວ
                </button>
                @endif

                @if($request->isReceived())
                <div class="flex items-center gap-2 bg-green-50 border border-green-200 text-green-700 px-5 py-2.5 rounded-xl text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Staff ຮັບສິນຄ້າແລ້ວ
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- Info panel --}}
    <div class="space-y-5">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
            <h3 class="font-semibold text-gray-700 mb-4">ຂໍ້ມູນຄຳຮ້ອງ</h3>
            <div class="space-y-3 text-sm">
                {{-- Requester block --}}
                <div class="bg-blue-50 rounded-xl p-3 space-y-2">
                    <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">ຜູ້ຮ້ອງຂໍ</p>
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-sm shrink-0">
                            {{ substr($request->requester_name ?? $request->requester?->name ?? '?', 0, 1) }}
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">{{ $request->requester_name ?? $request->requester?->name ?? '—' }}</p>
                            <p class="text-xs text-gray-500">
                                @switch($request->requester?->role)
                                    @case('super_admin') Super Admin @break
                                    @case('admin') Admin @break
                                    @case('manager') ຜູ້ຈັດການ @break
                                    @case('warehouse_staff') ພະນັກງານສາງ @break
                                    @default ພະນັກງານ
                                @endswitch
                                @if($request->branch?->name)
                                    · {{ $request->branch->name }}
                                @endif
                            </p>
                            @if($request->requester?->phone)
                                <p class="text-xs text-gray-400">📞 {{ $request->requester->phone }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div><p class="text-gray-500">ສາງ</p><p class="font-medium">{{ $request->warehouse?->name ?? '-' }}</p></div>
                <div><p class="text-gray-500">ຈຸດປະສົງ</p><p class="font-medium">{{ $request->purpose ?? '-' }}</p></div>
                <div><p class="text-gray-500">ວັນທີຂໍ</p><p class="font-medium">{{ $request->created_at->format('d/m/Y H:i') }}</p></div>
                @if($request->approved_by)
                <div class="pt-2 border-t border-gray-100">
                    <p class="text-gray-500">ຜູ້ອະນຸມັດ</p><p class="font-medium">{{ $request->approver?->name }}</p>
                    <p class="text-xs text-gray-400">{{ $request->approved_at?->format('d/m/Y H:i') }}</p>
                </div>
                @endif
                @if($request->rejection_reason)
                <div class="bg-red-50 rounded-lg p-3">
                    <p class="text-red-600 text-xs font-medium">ເຫດຜົນປະຕິເສດ:</p>
                    <p class="text-red-700 text-sm mt-1">{{ $request->rejection_reason }}</p>
                </div>
                @endif
                @if($request->issued_by)
                <div class="pt-2 border-t border-gray-100">
                    <p class="text-gray-500">ຜູ້ຈ່າຍ</p><p class="font-medium">{{ $request->issuer?->name }}</p>
                    <p class="text-xs text-gray-400">{{ $request->issued_at?->format('d/m/Y H:i') }}</p>
                </div>
                @endif
                @if($request->received_at)
                <div class="pt-2 border-t border-gray-100 bg-green-50 rounded-lg p-3">
                    <p class="text-xs font-semibold text-green-600 uppercase tracking-wider mb-1">ຮັບສິນຄ້າແລ້ວ</p>
                    <p class="text-gray-500 text-xs">ຢືນຢັນໂດຍ</p>
                    <p class="font-medium text-sm">{{ $request->receiver?->name }}</p>
                    <p class="text-xs text-gray-400">{{ $request->received_at->format('d/m/Y H:i') }}</p>
                    @if($request->received_note)
                    <p class="text-xs text-gray-500 mt-1 italic">{{ $request->received_note }}</p>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Receive Modal --}}
<div id="receiveModal" style="display:none;position:fixed;inset:0;z-index:50;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,0.5)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <h3 class="font-semibold text-lg mb-1 text-purple-700">ຢືນຢັນ Staff ມາຮັບສິນຄ້າ</h3>
        <p class="text-sm text-gray-500 mb-4">ຢືນຢັນວ່າ <strong>{{ $request->requester_name ?? $request->requester?->name }}</strong> ໄດ້ມາຮັບສິນຄ້າ #{{ $request->request_no }} ແລ້ວ</p>
        <form method="POST" action="{{ route('requests.receive', $request) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ໝາຍເຫດ <span class="text-gray-400 text-xs">(ທາງເລືອກ)</span></label>
                <textarea name="received_note" rows="2" placeholder="ໝາຍເຫດການຮັບ..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 resize-none"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white py-2.5 rounded-xl text-sm font-medium transition">ຢືນຢັນຮັບສິນຄ້າ</button>
                <button type="button" onclick="document.getElementById('receiveModal').style.display='none'"
                    class="flex-1 border border-gray-300 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition">ຍົກເລີກ</button>
            </div>
        </form>
    </div>
</div>

{{-- Reject Modal --}}
<div id="rejectModal" style="display:none;position:fixed;inset:0;z-index:50;align-items:center;justify-content:center;padding:16px;background:rgba(0,0,0,0.5)">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
        <h3 class="font-semibold text-lg mb-4 text-red-600">ປະຕິເສດຄຳຮ້ອງ</h3>
        <form method="POST" action="{{ route('requests.reject', $request) }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ເຫດຜົນ <span class="text-red-500">*</span></label>
                <textarea name="rejection_reason" required rows="3" placeholder="ລະບຸເຫດຜົນ..."
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 resize-none"></textarea>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-red-500 hover:bg-red-600 text-white py-2.5 rounded-xl text-sm font-medium transition">ຢືນຢັນປະຕິເສດ</button>
                <button type="button" onclick="document.getElementById('rejectModal').style.display='none'"
                    class="flex-1 border border-gray-300 py-2.5 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 transition">ຍົກເລີກ</button>
            </div>
        </form>
    </div>
</div>
@endsection
