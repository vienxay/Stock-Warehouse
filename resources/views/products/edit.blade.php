@extends('layouts.app')
@section('title','ແກ້ໄຂສິນຄ້າ')
@section('page_title','ແກ້ໄຂສິນຄ້າ')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('products.index') }}" class="text-gray-500 hover:text-gray-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </a>
        <h2 class="text-xl font-bold text-gray-800">ແກ້ໄຂ: {{ $product->name }}</h2>
    </div>

    <form method="POST" action="{{ route('products.update', $product) }}" enctype="multipart/form-data"
        class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 space-y-5">
        @csrf @method('PUT')

        <div class="grid grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ລະຫັດສິນຄ້າ <span class="text-red-500">*</span></label>
                <input type="text" name="code" value="{{ old('code', $product->code) }}"
                    class="w-full border @error('code') border-red-400 @else border-gray-300 @enderror rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                @error('code')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ບາໂຄດ</label>
                <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">ຊື່ສິນຄ້າ <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name', $product->name) }}"
                class="w-full border @error('name') border-red-400 @else border-gray-300 @enderror rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
        </div>

        <div class="grid grid-cols-3 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ໝວດໝູ່</label>
                <select name="category_id" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- ເລືອກ --</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" @selected(old('category_id', $product->category_id) == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ຍີ່ຫໍ້</label>
                <select name="brand_id" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- ເລືອກ --</option>
                    @foreach($brands as $b)
                        <option value="{{ $b->id }}" @selected(old('brand_id', $product->brand_id) == $b->id)>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ຫົວໜ່ວຍ</label>
                <select name="unit_id" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">-- ເລືອກ --</option>
                    @foreach($units as $u)
                        <option value="{{ $u->id }}" @selected(old('unit_id', $product->unit_id) == $u->id)>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">ຜູ້ສະໜອງ</label>
            <select name="supplier_id" class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- ເລືອກ --</option>
                @foreach($suppliers as $s)
                    <option value="{{ $s->id }}" @selected(old('supplier_id', $product->supplier_id) == $s->id)>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-3 gap-5">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ລາຄາທຶນ</label>
                <input type="number" name="cost_price" value="{{ old('cost_price', $product->cost_price) }}" min="0" step="0.01"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ລາຄາຂາຍ</label>
                <input type="number" name="selling_price" value="{{ old('selling_price', $product->selling_price) }}" min="0" step="0.01"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">ເຕືອນຕ່ຳສຸດ</label>
                <input type="number" name="min_stock_alert" value="{{ old('min_stock_alert', $product->min_stock_alert) }}" min="0"
                    class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">ລາຍລະອຽດ</label>
            <textarea name="description" rows="3"
                class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none">{{ old('description', $product->description) }}</textarea>
        </div>

        {{-- Image Upload --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">ຮູບພາບສິນຄ້າ</label>
            <div class="flex items-start gap-4">
                <div id="imgPreviewBox"
                     class="w-28 h-28 border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center bg-gray-50 overflow-hidden shrink-0">
                    @if($product->primaryImage)
                        <img id="imgPreview" src="{{ Storage::disk('public')->url($product->primaryImage->image_path) }}" alt=""
                             class="w-full h-full object-cover"/>
                        <svg id="imgPlaceholder" class="w-8 h-8 text-gray-300 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    @else
                        <svg id="imgPlaceholder" class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <img id="imgPreview" src="" alt="" class="w-full h-full object-cover hidden"/>
                    @endif
                </div>
                <div class="flex-1">
                    <label for="imageInput"
                        class="inline-flex items-center gap-2 cursor-pointer bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                        </svg>
                        ເລືອກຮູບໃໝ່
                    </label>
                    <input id="imageInput" type="file" name="image" accept="image/*" class="hidden"
                        onchange="previewImage(this)"/>
                    <p class="text-xs text-gray-400 mt-2">JPG, PNG, WEBP · ສູງສຸດ 2MB</p>
                    @error('image')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    @if($product->primaryImage)
                    <button type="button" class="mt-2 text-xs text-red-500 hover:text-red-700 underline"
                        onclick="appConfirm('ລົບຮູບນີ້?', function(){ document.getElementById('deleteImageForm').submit(); })">
                        ລົບຮູບເດີມ
                    </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl font-medium transition">
                ບັນທຶກການແກ້ໄຂ
            </button>
            <a href="{{ route('products.index') }}"
                class="px-6 border border-gray-300 text-gray-700 hover:bg-gray-50 py-3 rounded-xl font-medium transition text-center">
                ຍົກເລີກ
            </a>
        </div>
    </form>
</div>

@if($product->primaryImage)
<form id="deleteImageForm" method="POST"
      action="{{ route('products.images.destroy', [$product, $product->primaryImage]) }}"
      class="hidden">
    @csrf @method('DELETE')
</form>
@endif

@push('scripts')
<script>
function previewImage(input) {
    const preview = document.getElementById('imgPreview');
    const placeholder = document.getElementById('imgPlaceholder');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endpush
@endsection
