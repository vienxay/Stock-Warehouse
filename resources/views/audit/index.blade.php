@extends('layouts.app')

@section('title', 'Audit Log')
@section('page_title', 'Audit Log')

@section('content')

{{-- Stats --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-blue-100 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-500">ກິດຈະກຳວັນນີ້</p>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($todayCount) }}</p>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-indigo-100 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-500">ກິດຈະກຳອາທິດນີ້</p>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($weekCount) }}</p>
        </div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-5 flex items-center gap-4">
        <div class="w-11 h-11 rounded-xl bg-green-100 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
            </svg>
        </div>
        <div>
            <p class="text-xs text-gray-500">ຜູ້ໃຊ້ Active ວັນນີ້</p>
            <p class="text-2xl font-bold text-gray-800">{{ number_format($activeUsers) }}</p>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="bg-white rounded-xl shadow-sm p-4 mb-4">
    <form method="GET" action="{{ route('audit.index') }}" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs text-gray-500 mb-1">ຄົ້ນຫາ</label>
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="ຄົ້ນຫາລາຍລະອຽດ..."
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="min-w-[150px]">
            <label class="block text-xs text-gray-500 mb-1">ຜູ້ໃຊ້</label>
            <select name="user_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">ທຸກຜູ້ໃຊ້</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                        {{ $u->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="min-w-[160px]">
            <label class="block text-xs text-gray-500 mb-1">ປະເພດກິດຈະກຳ</label>
            <select name="action" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">ທຸກປະເພດ</option>
                @foreach($actions as $action)
                    <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>
                        {{ \App\Models\AuditLog::ACTION_CONFIG[$action]['label'] ?? $action }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">ຈາກວັນທີ</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-xs text-gray-500 mb-1">ຫາວັນທີ</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}"
                class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="flex gap-2">
            <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition">
                ຄົ້ນຫາ
            </button>
            <a href="{{ route('audit.index') }}"
                class="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg text-sm hover:bg-gray-200 transition">
                ລ້າງ
            </a>
        </div>
    </form>
</div>

{{-- Table + Clear --}}
<div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
        <div>
            <h2 class="font-semibold text-gray-800">ລາຍການ Audit Log</h2>
            <p class="text-xs text-gray-400 mt-0.5">ທັງໝົດ {{ number_format($logs->total()) }} ລາຍການ</p>
        </div>
        <button onclick="openClearModal()"
            class="flex items-center gap-2 px-3 py-2 bg-red-50 text-red-600 rounded-lg text-sm hover:bg-red-100 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            ລ້າງ Log ເກົ່າ
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider">
                <tr>
                    <th class="px-4 py-3 text-left w-10">#</th>
                    <th class="px-4 py-3 text-left">ກິດຈະກຳ</th>
                    <th class="px-4 py-3 text-left">ລາຍລະອຽດ</th>
                    <th class="px-4 py-3 text-left">ຜູ້ໃຊ້</th>
                    <th class="px-4 py-3 text-left">IP Address</th>
                    <th class="px-4 py-3 text-left">ວັນທີ-ເວລາ</th>
                    <th class="px-4 py-3 text-center w-16">ລຶບ</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 text-gray-400 text-xs">
                            {{ $logs->firstItem() + $loop->index }}
                        </td>
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $log->action_color }}">
                                {{ $log->action_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-700 max-w-xs">
                            <span class="truncate block" title="{{ $log->description }}">
                                {{ $log->description }}
                            </span>
                            @if($log->model_type)
                                <span class="text-xs text-gray-400">{{ $log->model_type }} #{{ $log->model_id }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($log->user)
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 bg-blue-600 rounded-full flex items-center justify-center text-white text-xs font-bold shrink-0">
                                        {{ substr($log->user->name, 0, 1) }}
                                    </div>
                                    <span class="text-gray-700 text-xs">{{ $log->user->name }}</span>
                                </div>
                            @else
                                <span class="text-gray-400 text-xs">System</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs font-mono">
                            {{ $log->ip_address ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-gray-500 text-xs whitespace-nowrap">
                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <form method="POST" action="{{ route('audit.destroy', $log) }}"
                                data-confirm="ລຶບ Log ນີ້?">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="text-red-400 hover:text-red-600 transition p-1 rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-gray-400">
                            <svg class="w-10 h-10 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            ບໍ່ພົບລາຍການ Audit Log
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($logs->hasPages())
        <div class="px-5 py-4 border-t border-gray-100">
            {{ $logs->withQueryString()->links() }}
        </div>
    @endif
</div>

{{-- Clear Modal (hidden via display:none to avoid Tailwind hidden/flex conflict) --}}
<div id="clearModal" style="display:none;position:fixed;inset:0;z-index:50;align-items:center;justify-content:center;background:rgba(15,23,42,0.5)">
    <div class="bg-white rounded-2xl shadow-2xl" style="width:340px">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center shrink-0">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-800">ລ້າງ Log ເກົ່າ</p>
                    <p class="text-xs text-gray-400">ເລືອກຊ່ວງເວລາທີ່ຕ້ອງການລ້າງ</p>
                </div>
            </div>
            <button onclick="closeClearModal()"
                class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg p-1.5 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('audit.clear') }}">
            @csrf
            <input type="hidden" name="days" id="clearDaysInput" value="30">

            <div class="px-5 py-4">
                <p class="text-xs text-gray-500 mb-2">ລົບ Log ທີ່ເກົ່າກວ່າ:</p>

                {{-- Day pill buttons — all start grey, JS sets active on load --}}
                <div class="flex gap-2 mb-4">
                    @foreach([7, 14, 30, 60, 90] as $d)
                        <button type="button" data-days="{{ $d }}"
                            id="pill{{ $d }}"
                            class="day-pill flex-1 py-1.5 rounded-xl text-xs font-semibold border border-gray-200 bg-white text-gray-500 transition-colors">
                            {{ $d }}ວັນ
                        </button>
                    @endforeach
                </div>

                {{-- Warning --}}
                <div class="flex items-center gap-2 bg-orange-50 border border-orange-200 rounded-xl px-3 py-2.5">
                    <svg class="w-3.5 h-3.5 text-orange-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-xs text-orange-700">ການດຳເນີນການນີ້ <strong>ບໍ່ສາມາດຍ້ອນກັບ</strong> ໄດ້</span>
                </div>
            </div>

            <div style="display:flex;gap:10px;padding:0 20px 20px">
                <button type="button" onclick="closeClearModal()"
                    style="flex:1;padding:9px 0;font-size:13px;font-weight:600;color:#4b5563;background:#f3f4f6;border:none;border-radius:12px;cursor:pointer">
                    ຍົກເລີກ
                </button>
                <button type="submit"
                    style="flex:1;padding:9px 0;font-size:13px;font-weight:600;color:#fff;background:#dc2626;border:none;border-radius:12px;cursor:pointer">
                    ລ້າງ Log
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function openClearModal() {
        document.getElementById('clearModal').style.display = 'flex';
    }
    function closeClearModal() {
        document.getElementById('clearModal').style.display = 'none';
    }
    function selectDays(days) {
        document.getElementById('clearDaysInput').value = days;
        document.querySelectorAll('.day-pill').forEach(function(btn) {
            var d = parseInt(btn.getAttribute('data-days'));
            if (d === days) {
                btn.style.background = '#dc2626';
                btn.style.color = '#ffffff';
                btn.style.borderColor = '#dc2626';
            } else {
                btn.style.background = '#ffffff';
                btn.style.color = '#6b7280';
                btn.style.borderColor = '#e5e7eb';
            }
        });
    }
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.day-pill').forEach(function(btn) {
            btn.addEventListener('click', function() {
                selectDays(parseInt(this.getAttribute('data-days')));
            });
        });
        document.getElementById('clearModal').addEventListener('click', function(e) {
            if (e.target === this) closeClearModal();
        });
        selectDays(30);
    });
</script>
@endpush

@endsection
