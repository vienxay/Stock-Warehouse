@extends('layouts.app')
@section('title','ຕັ້ງຄ່າລະບົບ')
@section('page_title','ຕັ້ງຄ່າລະບົບ')

@section('content')
<form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
    @csrf

    {{-- Tabs --}}
    <div class="flex gap-1 mb-6 bg-white rounded-xl shadow-sm border border-gray-100 p-1 w-fit">
        <button type="button" onclick="switchTab('general')" id="tab-btn-general"
            class="tab-btn px-4 py-2 rounded-lg text-sm font-medium transition bg-blue-600 text-white shadow-sm">
            ຂໍ້ມູນທົ່ວໄປ
        </button>
        <button type="button" onclick="switchTab('system')" id="tab-btn-system"
            class="tab-btn px-4 py-2 rounded-lg text-sm font-medium transition text-gray-500 hover:text-gray-700">
            ລະບົບ
        </button>
        <button type="button" onclick="switchTab('stock')" id="tab-btn-stock"
            class="tab-btn px-4 py-2 rounded-lg text-sm font-medium transition text-gray-500 hover:text-gray-700">
            ສາງ
        </button>
    </div>

    {{-- ===== General ===== --}}
    <div id="tab-general" class="tab-panel">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-5 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                ຂໍ້ມູນກິດຈະການ
            </h3>
            <div class="space-y-4">
                {{-- Logo Upload --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ໂລໂກ້ບໍລິສັດ</label>
                    <div class="flex items-start gap-5">
                        {{-- Preview --}}
                        <div id="logoPreviewBox"
                            class="w-24 h-24 rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 flex items-center justify-center overflow-hidden shrink-0">
                            @if(!empty($general['company_logo']))
                                <img id="logoPreview"
                                    src="{{ Storage::disk('public')->url($general['company_logo']) }}"
                                    alt="Logo" class="w-full h-full object-contain p-1"/>
                            @else
                                <svg id="logoPlaceholder" class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <img id="logoPreview" src="" alt="" class="w-full h-full object-contain p-1 hidden"/>
                            @endif
                        </div>
                        {{-- Actions --}}
                        <div class="flex-1 pt-1">
                            <label for="logoInput"
                                class="inline-flex items-center gap-2 cursor-pointer bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                ເລືອກຮູບ
                            </label>
                            <input id="logoInput" type="file" name="company_logo" accept="image/*"
                                class="hidden" onchange="previewLogo(this)"/>
                            <p class="text-xs text-gray-400 mt-2">PNG, JPG, SVG · ສູງສຸດ 2MB</p>
                            <p class="text-xs text-gray-400">ແນະນຳ: ຮູບສີ່ຫຼ່ຽມ, ພື້ນຫຼັງໂປ່ງໃສ (PNG/SVG)</p>
                            @error('company_logo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                            @if(!empty($general['company_logo']))
                            <label class="flex items-center gap-1.5 mt-2 cursor-pointer">
                                <input type="checkbox" name="remove_logo" value="1" id="removeLogo"
                                    class="w-3.5 h-3.5 rounded text-red-500 border-gray-300"/>
                                <span class="text-xs text-red-500">ລຶບໂລໂກ້ເດີມ</span>
                            </label>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100 pt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        ຊື່ບໍລິສັດ / ຮ້ານ <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="company_name"
                        value="{{ old('company_name', $general['company_name'] ?? '') }}"
                        class="w-full border {{ $errors->has('company_name') ? 'border-red-400' : 'border-gray-300' }} rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                    @error('company_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ທີ່ຢູ່</label>
                    <textarea name="company_address" rows="2"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                        placeholder="ທີ່ຢູ່ຂອງບໍລິສັດ...">{{ old('company_address', $general['company_address'] ?? '') }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">ເບີໂທ</label>
                        <input type="text" name="company_phone"
                            value="{{ old('company_phone', $general['company_phone'] ?? '') }}"
                            placeholder="020 xx xxx xxx"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">ອີເມວ</label>
                        <input type="email" name="company_email"
                            value="{{ old('company_email', $general['company_email'] ?? '') }}"
                            placeholder="info@example.com"
                            class="w-full border {{ $errors->has('company_email') ? 'border-red-400' : 'border-gray-300' }} rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                        @error('company_email')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== System ===== --}}
    <div id="tab-system" class="tab-panel hidden">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-5 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                ການຕັ້ງຄ່າລະບົບ
            </h3>
            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            ຊື່ສະກຸນເງິນ <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="currency"
                            value="{{ old('currency', $system['currency'] ?? 'ກີບ') }}"
                            placeholder="ກີບ"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            ສັນຍາລັກ <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="currency_symbol"
                            value="{{ old('currency_symbol', $system['currency_symbol'] ?? '₭') }}"
                            placeholder="₭"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            ຮູບແບບວັນທີ <span class="text-red-500">*</span>
                        </label>
                        <select name="date_format"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach(['d/m/Y' => 'DD/MM/YYYY', 'Y-m-d' => 'YYYY-MM-DD', 'd-m-Y' => 'DD-MM-YYYY', 'm/d/Y' => 'MM/DD/YYYY'] as $fmt => $label)
                                <option value="{{ $fmt }}"
                                    @selected(old('date_format', $system['date_format'] ?? 'd/m/Y') === $fmt)>
                                    {{ $label }} &nbsp;({{ now()->format($fmt) }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1.5">
                            ຈຳນວນລາຍການ / ໜ້າ <span class="text-red-500">*</span>
                        </label>
                        <select name="items_per_page"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach([10, 15, 25, 50, 100] as $n)
                                <option value="{{ $n }}"
                                    @selected(old('items_per_page', $system['items_per_page'] ?? '15') == $n)>
                                    {{ $n }} ລາຍການ
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Timezone <span class="text-red-500">*</span>
                    </label>
                    <select name="timezone"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach([
                            'Asia/Vientiane'   => 'Asia/Vientiane (UTC+7) — ລາວ',
                            'Asia/Bangkok'     => 'Asia/Bangkok (UTC+7) — ໄທ',
                            'Asia/Ho_Chi_Minh' => 'Asia/Ho_Chi_Minh (UTC+7) — ຫວຽດນາມ',
                            'Asia/Phnom_Penh'  => 'Asia/Phnom_Penh (UTC+7) — ກຳປູເຈຍ',
                            'Asia/Yangon'      => 'Asia/Yangon (UTC+6:30) — ມຽນມາ',
                            'Asia/Singapore'   => 'Asia/Singapore (UTC+8) — ສິງກະໂປ',
                            'UTC'              => 'UTC',
                        ] as $tz => $tzLabel)
                            <option value="{{ $tz }}"
                                @selected(old('timezone', $system['timezone'] ?? 'Asia/Vientiane') === $tz)>
                                {{ $tzLabel }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Stock ===== --}}
    <div id="tab-stock" class="tab-panel hidden">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-base font-semibold text-gray-800 mb-5 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
                ການຕັ້ງຄ່າສາງ
            </h3>
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        ຈຳນວນເຕືອນຕ່ຳສຸດ (ຄ່າ default ສຳລັບສິນຄ້າໃໝ່) <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="number" name="default_min_stock_alert"
                            value="{{ old('default_min_stock_alert', $stock['default_min_stock_alert'] ?? '10') }}"
                            min="0" max="9999"
                            class="w-40 border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                        <span class="text-sm text-gray-500">ໜ່ວຍ</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1.5">ຄ່ານີ້ຈະຖືກໃຊ້ອັດຕະໂນມັດເວລາເພີ່ມສິນຄ້າໃໝ່</p>
                </div>

                <div class="border-t border-gray-100 pt-5">
                    <p class="text-sm font-medium text-gray-700 mb-3">ການແຈ້ງເຕືອນ</p>
                    <div class="space-y-4">
                        {{-- Toggle: Low stock --}}
                        <label class="flex items-center gap-3 cursor-pointer">
                            <div class="relative shrink-0">
                                <input type="checkbox" name="low_stock_notify" value="1" id="toggle-low"
                                    {{ old('low_stock_notify', $stock['low_stock_notify'] ?? '1') == '1' ? 'checked' : '' }}
                                    class="sr-only" onchange="syncToggle('toggle-low','track-low','dot-low')"/>
                                <div id="track-low"
                                    class="w-10 h-5 rounded-full transition {{ ($stock['low_stock_notify'] ?? '1') == '1' ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
                                <div id="dot-low"
                                    class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform {{ ($stock['low_stock_notify'] ?? '1') == '1' ? 'translate-x-5' : '' }}"></div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">ແຈ້ງເຕືອນສາງໃກ້ໝົດ</p>
                                <p class="text-xs text-gray-400">ເວລາສິນຄ້າຮອດລະດັບ min_stock_alert</p>
                            </div>
                        </label>

                        {{-- Toggle: Out stock --}}
                        <label class="flex items-center gap-3 cursor-pointer">
                            <div class="relative shrink-0">
                                <input type="checkbox" name="out_stock_notify" value="1" id="toggle-out"
                                    {{ old('out_stock_notify', $stock['out_stock_notify'] ?? '1') == '1' ? 'checked' : '' }}
                                    class="sr-only" onchange="syncToggle('toggle-out','track-out','dot-out')"/>
                                <div id="track-out"
                                    class="w-10 h-5 rounded-full transition {{ ($stock['out_stock_notify'] ?? '1') == '1' ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
                                <div id="dot-out"
                                    class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform {{ ($stock['out_stock_notify'] ?? '1') == '1' ? 'translate-x-5' : '' }}"></div>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">ແຈ້ງເຕືອນສາງໝົດ</p>
                                <p class="text-xs text-gray-400">ເວລາສິນຄ້າໝົດ (ຈຳນວນ = 0)</p>
                            </div>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- System Info --}}
    <div class="mt-5 bg-blue-50 border border-blue-100 rounded-2xl p-5">
        <h3 class="text-sm font-semibold text-blue-700 mb-3 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            ຂໍ້ມູນລະບົບ
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-xs">
            <div>
                <p class="text-blue-500">PHP Version</p>
                <p class="font-medium text-blue-800">{{ phpversion() }}</p>
            </div>
            <div>
                <p class="text-blue-500">Laravel Version</p>
                <p class="font-medium text-blue-800">{{ app()->version() }}</p>
            </div>
            <div>
                <p class="text-blue-500">Environment</p>
                <p class="font-medium text-blue-800">{{ app()->environment() }}</p>
            </div>
            <div>
                <p class="text-blue-500">ເວລາລະບົບ</p>
                <p class="font-medium text-blue-800">{{ now()->format('d/m/Y H:i') }}</p>
            </div>
        </div>
    </div>

    {{-- Save Button --}}
    <div class="mt-5 flex justify-end">
        <button type="submit"
            class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl font-medium transition shadow-sm text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            ບັນທຶກການຕັ້ງຄ່າ
        </button>
    </div>

</form>
@endsection

@php
$activeTab = '';
if ($errors->hasAny(['company_name','company_address','company_phone','company_email','company_logo'])) {
    $activeTab = 'general';
} elseif ($errors->hasAny(['currency','currency_symbol','date_format','items_per_page','timezone'])) {
    $activeTab = 'system';
} elseif ($errors->has('default_min_stock_alert')) {
    $activeTab = 'stock';
}
@endphp

@push('scripts')
<script>
function switchTab(name) {
    document.querySelectorAll('.tab-panel').forEach(el => el.classList.add('hidden'));
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('bg-blue-600', 'text-white', 'shadow-sm');
        btn.classList.add('text-gray-500');
    });
    document.getElementById('tab-' + name).classList.remove('hidden');
    const activeBtn = document.getElementById('tab-btn-' + name);
    activeBtn.classList.add('bg-blue-600', 'text-white', 'shadow-sm');
    activeBtn.classList.remove('text-gray-500');
}

function syncToggle(checkId, trackId, dotId) {
    const checked = document.getElementById(checkId).checked;
    const track = document.getElementById(trackId);
    const dot = document.getElementById(dotId);
    track.classList.toggle('bg-blue-600', checked);
    track.classList.toggle('bg-gray-200', !checked);
    dot.classList.toggle('translate-x-5', checked);
}

function previewLogo(input) {
    if (!input.files || !input.files[0]) return;
    const preview = document.getElementById('logoPreview');
    const placeholder = document.getElementById('logoPlaceholder');
    const reader = new FileReader();
    reader.onload = e => {
        preview.src = e.target.result;
        preview.classList.remove('hidden');
        if (placeholder) placeholder.classList.add('hidden');
    };
    reader.readAsDataURL(input.files[0]);
}

// Keep active tab after validation error
const _errorTab = '{{ $activeTab }}';
if (_errorTab) switchTab(_errorTab);
</script>
@endpush
