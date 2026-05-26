@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')

{{-- Welcome --}}
<div class="mb-6">
    <h2 class="text-2xl font-bold text-gray-800">
        ຍິນດີຕ້ອນຮັບ, {{ $user->name }} 👋
    </h2>
    <p class="text-gray-500 text-sm mt-1">{{ now()->locale('lo')->isoFormat('dddd, D MMMM YYYY') }}</p>
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </div>
            <span class="text-xs text-blue-500 bg-blue-50 px-2 py-1 rounded-full font-medium">ທັງໝົດ</span>
        </div>
        <p class="text-3xl font-bold text-gray-800">{{ number_format($totalProducts) }}</p>
        <p class="text-gray-500 text-sm mt-1">ສິນຄ້າທັງໝົດ</p>
    </div>

    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
            </div>
            <span class="text-xs text-purple-500 bg-purple-50 px-2 py-1 rounded-full font-medium">ທັງໝົດ</span>
        </div>
        <p class="text-3xl font-bold text-gray-800">{{ number_format($totalWarehouses) }}</p>
        <p class="text-gray-500 text-sm mt-1">ສາງທັງໝົດ</p>
    </div>

    <div class="bg-white rounded-2xl p-5 shadow-sm border border-orange-100">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-orange-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <span class="text-xs text-orange-500 bg-orange-50 px-2 py-1 rounded-full font-medium">ເຕືອນ</span>
        </div>
        <p class="text-3xl font-bold text-orange-600">{{ number_format($lowStockCount) }}</p>
        <p class="text-gray-500 text-sm mt-1">ສິນຄ້າໃກ້ໝົດ</p>
    </div>

    <div class="bg-white rounded-2xl p-5 shadow-sm border border-red-100">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center">
                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </div>
            <span class="text-xs text-red-500 bg-red-50 px-2 py-1 rounded-full font-medium">ໝົດ</span>
        </div>
        <p class="text-3xl font-bold text-red-600">{{ number_format($outOfStockCount) }}</p>
        <p class="text-gray-500 text-sm mt-1">ສິນຄ້າໝົດສາງ</p>
    </div>

</div>

{{-- Second row stats --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

    <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl p-5 text-white shadow-sm">
        <div class="flex items-center gap-3 mb-2">
            <svg class="w-6 h-6 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4"/>
            </svg>
            <span class="text-sm opacity-80">ນຳເຂົ້າວັນນີ້</span>
        </div>
        <p class="text-4xl font-bold">{{ number_format($stockInCount) }}</p>
        <p class="text-green-100 text-xs mt-1">ລາຍການ · {{ number_format($stockInQty) }} ໜ່ວຍ</p>
    </div>

    <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl p-5 text-white shadow-sm">
        <div class="flex items-center gap-3 mb-2">
            <svg class="w-6 h-6 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
            <span class="text-sm opacity-80">ຈ່າຍອອກວັນນີ້</span>
        </div>
        <p class="text-4xl font-bold">{{ number_format($stockOutCount) }}</p>
        <p class="text-blue-100 text-xs mt-1">ລາຍການ · {{ number_format($stockOutQty) }} ໜ່ວຍ</p>
    </div>

    <div class="bg-gradient-to-br from-amber-500 to-orange-500 rounded-2xl p-5 text-white shadow-sm">
        <div class="flex items-center gap-3 mb-2">
            <svg class="w-6 h-6 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <span class="text-sm opacity-80">ຄຳຮ້ອງລໍຖ້າ</span>
        </div>
        <p class="text-4xl font-bold">{{ number_format($pendingRequests) }}</p>
        <p class="text-amber-100 text-xs mt-1">ລາຍການ</p>
    </div>

</div>

{{-- Chart --}}
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 mb-6">
    <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-gray-800">ການເຄື່ອນໄຫວ 7 ວັນ</h3>
        <div class="flex items-center gap-4 text-xs">
            <span class="flex items-center gap-1"><span class="inline-block w-3 h-3 rounded-full bg-green-500"></span>ນຳເຂົ້າ</span>
            <span class="flex items-center gap-1"><span class="inline-block w-3 h-3 rounded-full bg-blue-500"></span>ຈ່າຍອອກ</span>
        </div>
    </div>
    <canvas id="stockChart" height="80"></canvas>
</div>

{{-- Low Stock + Warehouse Summary --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    {{-- Low Stock Products --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <span class="w-2 h-2 bg-orange-400 rounded-full"></span>
                ສິນຄ້າໃກ້ໝົດ
            </h3>
            <a href="{{ route('reports.index') }}" class="text-blue-600 text-sm hover:underline">ລາຍງານ →</a>
        </div>
        @forelse($lowStockProducts as $lp)
        <div class="px-5 py-3 flex items-center gap-3 border-b border-gray-50 last:border-0">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-800 truncate">{{ $lp->name }}</p>
                <p class="text-xs text-gray-500 font-mono">{{ $lp->code }} · {{ $lp->category?->name }}</p>
            </div>
            <div class="text-right shrink-0">
                <p class="text-lg font-bold {{ $lp->current_stock <= 0 ? 'text-red-600' : 'text-orange-500' }}">
                    {{ number_format($lp->current_stock) }}
                </p>
                <p class="text-xs text-gray-400">ຕ່ຳສຸດ {{ $lp->min_stock_alert }}</p>
            </div>
            <a href="{{ route('stock.in') }}?product_id={{ $lp->id }}"
                class="shrink-0 text-xs bg-green-50 text-green-700 hover:bg-green-100 px-3 py-1.5 rounded-lg font-medium transition">
                ເຕີມ
            </a>
        </div>
        @empty
        <div class="px-5 py-8 text-center">
            <svg class="w-10 h-10 text-green-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <p class="text-gray-400 text-sm">ສິນຄ້າທຸກລາຍການປົກກະຕິ</p>
        </div>
        @endforelse
    </div>

    {{-- Warehouse Summary --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800 flex items-center gap-2">
                <span class="w-2 h-2 bg-blue-400 rounded-full"></span>
                ສະຫຼຸບສາງ
            </h3>
        </div>
        @forelse($warehouseSummary as $wh)
        <div class="px-5 py-3 flex items-center justify-between border-b border-gray-50 last:border-0 hover:bg-gray-50 transition">
            <div>
                <p class="text-sm font-medium text-gray-800">{{ $wh->name }}</p>
                <p class="text-xs text-gray-400">{{ $wh->stocks_count }} ລາຍການສິນຄ້າ</p>
            </div>
            <div class="text-right">
                <p class="text-lg font-bold text-blue-600">{{ number_format($wh->total_stock) }}</p>
                <p class="text-xs text-gray-400">ໜ່ວຍລວມ</p>
            </div>
        </div>
        @empty
        <div class="px-5 py-8 text-center text-gray-400 text-sm">ຍັງບໍ່ມີຂໍ້ມູນສາງ</div>
        @endforelse
    </div>

</div>

{{-- Bottom section: Recent Requests + Recent Movements --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Recent Requests --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">ຄຳຮ້ອງຂໍລ່າສຸດ</h3>
            <a href="{{ route('requests.index') }}" class="text-blue-600 text-sm hover:underline">ເບິ່ງທັງໝົດ →</a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($recentRequests as $req)
            <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50 transition">
                <div class="min-w-0">
                    <p class="text-sm font-medium text-gray-800 truncate">{{ $req->request_no }}</p>
                    <p class="text-xs text-gray-500">{{ $req->requester->name ?? '-' }} · {{ $req->created_at->diffForHumans() }}</p>
                </div>
                <span class="ml-3 shrink-0 text-xs font-medium px-2.5 py-1 rounded-full
                    @if($req->status === 'pending') bg-amber-100 text-amber-700
                    @elseif($req->status === 'approved') bg-blue-100 text-blue-700
                    @elseif($req->status === 'issued') bg-green-100 text-green-700
                    @elseif($req->status === 'rejected') bg-red-100 text-red-700
                    @else bg-gray-100 text-gray-600
                    @endif">
                    @if($req->status === 'pending') ລໍຖ້າ
                    @elseif($req->status === 'approved') ອະນຸມັດ
                    @elseif($req->status === 'issued') ຈ່າຍແລ້ວ
                    @elseif($req->status === 'rejected') ປະຕິເສດ
                    @else {{ $req->status }}
                    @endif
                </span>
            </div>
            @empty
            <div class="px-5 py-8 text-center">
                <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-gray-400 text-sm">ຍັງບໍ່ມີຄຳຮ້ອງຂໍ</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Recent Movements --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-800">ການເຄື່ອນໄຫວລ່າສຸດ</h3>
            <a href="{{ route('reports.index') }}" class="text-blue-600 text-sm hover:underline">ລາຍງານ →</a>
        </div>
        <div class="px-5 py-4 space-y-3 max-h-80 overflow-y-auto">
            @forelse($recentMovements as $mv)
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-full shrink-0 flex items-center justify-center text-xs font-bold
                    {{ $mv->type === 'in' ? 'bg-green-100 text-green-600' : 'bg-blue-100 text-blue-600' }}">
                    {{ $mv->type === 'in' ? '+' : '-' }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-sm text-gray-700">
                        <span class="font-medium">{{ $mv->product->name ?? '-' }}</span>
                        ·
                        {{ $mv->type === 'in' ? 'ນຳເຂົ້າ' : 'ຈ່າຍອອກ' }}
                        <span class="font-semibold {{ $mv->type === 'in' ? 'text-green-600' : 'text-blue-600' }}">
                            {{ number_format($mv->quantity) }}
                        </span>
                        ໜ່ວຍ
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $mv->warehouse->name ?? '-' }} · {{ $mv->created_at->diffForHumans() }}</p>
                </div>
            </div>
            @empty
            <div class="text-center py-8">
                <p class="text-gray-400 text-sm">ຍັງບໍ່ມີກິດຈະກຳວັນນີ້</p>
            </div>
            @endforelse
        </div>
    </div>

</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
new Chart(document.getElementById('stockChart'), {
    type: 'bar',
    data: {
        labels: @json($chartLabels),
        datasets: [
            {
                label: 'ນຳເຂົ້າ',
                data: @json($chartIn),
                backgroundColor: 'rgba(34,197,94,0.7)',
                borderRadius: 6,
            },
            {
                label: 'ຈ່າຍອອກ',
                data: @json($chartOut),
                backgroundColor: 'rgba(59,130,246,0.7)',
                borderRadius: 6,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, grid: { color: '#f3f4f6' } },
            x: { grid: { display: false } }
        }
    }
});
</script>
@endpush

@endsection
