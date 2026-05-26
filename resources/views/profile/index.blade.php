@extends('layouts.app')
@section('title', 'ຂໍ້ມູນສ່ວນຕົວ')
@section('page_title', 'ຂໍ້ມູນສ່ວນຕົວ')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    {{-- ===== Profile Info Card ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <h2 class="font-semibold text-gray-800">ຂໍ້ມູນສ່ວນຕົວ</h2>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            @if(session('success'))
            <div class="p-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('success') }}
            </div>
            @endif
            @if($errors->any())
            <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">{{ $errors->first() }}</div>
            @endif

            {{-- Avatar --}}
            <div class="flex items-center gap-5">
                <div class="relative shrink-0">
                    <div class="w-20 h-20 rounded-2xl overflow-hidden bg-blue-600 flex items-center justify-center">
                        @if($user->avatar)
                            <img src="{{ Storage::disk('public')->url($user->avatar) }}"
                                 alt="{{ $user->name }}" class="w-full h-full object-cover" id="avatarPreview"/>
                        @else
                            <span class="text-white text-3xl font-bold" id="avatarInitial">{{ substr($user->name, 0, 1) }}</span>
                            <img src="" alt="" class="w-full h-full object-cover hidden" id="avatarPreview"/>
                        @endif
                    </div>
                </div>
                <div class="flex-1">
                    <p class="text-sm font-medium text-gray-700 mb-2">ຮູບໂປຣໄຟລ໌</p>
                    <label class="cursor-pointer inline-flex items-center gap-2 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        ເລືອກຮູບ
                        <input type="file" name="avatar" class="hidden" accept="image/*" id="avatarInput"/>
                    </label>
                    @if($user->avatar)
                    <form method="POST" action="{{ route('profile.avatar.remove') }}" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="ml-2 text-xs text-red-500 hover:text-red-700 hover:underline">ລຶບຮູບ</button>
                    </form>
                    @endif
                    <p class="text-xs text-gray-400 mt-1">JPG, PNG, WebP · ສູງສຸດ 2MB</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ຊື່ - ນາມສະກຸນ <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                </div>

                {{-- Username (readonly) --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ຊື່ຜູ້ໃຊ້ (Username)</label>
                    <input type="text" value="{{ $user->username }}" readonly
                        class="w-full border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 text-gray-500 cursor-not-allowed"/>
                    <p class="text-xs text-gray-400 mt-1">ບໍ່ສາມາດປ່ຽນ username ໄດ້</p>
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ອີເມວ</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                        placeholder="example@email.com"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                </div>

                {{-- Phone --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ເບີໂທ</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                        placeholder="020 XXXX XXXX"
                        class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
                </div>
            </div>

            {{-- Role + Branch (readonly) --}}
            <div class="grid grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ສິດ (Role)</label>
                    <div class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 text-gray-600">
                        @switch($user->role)
                            @case('super_admin') Super Admin @break
                            @case('admin') Admin @break
                            @case('manager') Manager @break
                            @case('warehouse_staff') Warehouse Staff @break
                            @default Staff
                        @endswitch
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ສາຂາ</label>
                    <div class="border border-gray-200 rounded-xl px-3 py-2.5 text-sm bg-gray-50 text-gray-600">
                        {{ $user->branch?->name ?? '—' }}
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-medium transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    ບັນທຶກຂໍ້ມູນ
                </button>
            </div>
        </form>
    </div>

    {{-- ===== Change Password Card ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center gap-3">
            <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h2 class="font-semibold text-gray-800">ປ່ຽນລະຫັດຜ່ານ</h2>
        </div>

        <form method="POST" action="{{ route('profile.password') }}" class="p-6 space-y-5">
            @csrf
            @method('PUT')

            @if(session('pwd_success'))
            <div class="p-3 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                {{ session('pwd_success') }}
            </div>
            @endif
            @if(session('pwd_error'))
            <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">{{ session('pwd_error') }}</div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                {{-- Current password --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ລະຫັດຜ່ານປັດຈຸບັນ <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="password" name="current_password" id="curPwd" required
                            placeholder="ລະຫັດຜ່ານເກົ່າ"
                            class="w-full border border-gray-300 rounded-xl px-3 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"/>
                        <button type="button" onclick="toggleField('curPwd')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- New password --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ລະຫັດຜ່ານໃໝ່ <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="password" name="password" id="newPwd" required minlength="6"
                            placeholder="ຢ່າງໜ້ອຍ 6 ຕົວ"
                            class="w-full border border-gray-300 rounded-xl px-3 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"/>
                        <button type="button" onclick="toggleField('newPwd')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Confirm --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ຢືນຢັນລະຫັດຜ່ານ <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="confPwd" required
                            placeholder="ໃສ່ຄືນໃໝ່"
                            class="w-full border border-gray-300 rounded-xl px-3 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-orange-400"/>
                        <button type="button" onclick="toggleField('confPwd')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="flex items-center gap-2 bg-orange-500 hover:bg-orange-600 text-white px-6 py-2.5 rounded-xl text-sm font-medium transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    ປ່ຽນລະຫັດຜ່ານ
                </button>
            </div>
        </form>
    </div>

    {{-- ===== Account Info Card ===== --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-semibold text-gray-700 mb-4 text-sm">ຂໍ້ມູນບັນຊີ</h3>
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-gray-500">ສ້າງບັນຊີ</p>
                <p class="font-medium text-gray-800">{{ $user->created_at->format('d/m/Y H:i') }}</p>
            </div>
            <div>
                <p class="text-gray-500">Login ລ່າສຸດ</p>
                <p class="font-medium text-gray-800">
                    {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : '—' }}
                </p>
            </div>
            <div>
                <p class="text-gray-500">ສະຖານະ</p>
                <span class="inline-block text-xs font-medium px-2.5 py-1 rounded-full {{ $user->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                    {{ $user->is_active ? 'ໃຊ້ງານ' : 'ລະງັບ' }}
                </span>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function toggleField(id) {
    var el = document.getElementById(id);
    el.type = el.type === 'password' ? 'text' : 'password';
}

// Avatar preview
document.getElementById('avatarInput').addEventListener('change', function() {
    var file = this.files[0];
    if (!file) return;
    var reader = new FileReader();
    reader.onload = function(e) {
        var preview = document.getElementById('avatarPreview');
        var initial = document.getElementById('avatarInitial');
        preview.src = e.target.result;
        preview.classList.remove('hidden');
        if (initial) initial.classList.add('hidden');
    };
    reader.readAsDataURL(file);
});
</script>
@endpush
