<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ຕັ້ງລະຫັດຜ່ານໃໝ່ - Stock Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Noto Sans Lao', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-900 flex items-center justify-center p-4">

    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-500 rounded-full opacity-10 blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-indigo-500 rounded-full opacity-10 blur-3xl"></div>
    </div>

    <div class="relative w-full max-w-md">

        {{-- Logo --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-2xl shadow-2xl mb-4">
                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-white">ຕັ້ງລະຫັດຜ່ານໃໝ່</h1>
            <p class="text-blue-200 mt-1 text-sm">ໃສ່ລະຫັດຜ່ານໃໝ່ຂອງທ່ານ</p>
        </div>

        {{-- Card --}}
        <div class="bg-white rounded-2xl shadow-2xl p-8">

            @if($errors->any())
            <div class="mb-5 p-3 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700">
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('password.reset.update') }}" class="space-y-5" id="resetForm">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                {{-- New password --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ລະຫັດຜ່ານໃໝ່ <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input type="password" name="password" id="newPassword" required
                            placeholder="ຢ່າງໜ້ອຍ 6 ຕົວອັກສອນ" minlength="6" autofocus
                            class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500 @error('password') border-red-400 bg-red-50 @enderror"/>
                        <button type="button" onclick="togglePwd('newPassword', 'eye1')"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <svg id="eye1" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    {{-- Strength bar --}}
                    <div class="mt-2 h-1.5 bg-gray-200 rounded-full overflow-hidden">
                        <div id="strengthBar" class="h-full rounded-full transition-all duration-300" style="width:0"></div>
                    </div>
                    <p id="strengthText" class="text-xs text-gray-400 mt-1"></p>
                </div>

                {{-- Confirm password --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">ຢືນຢັນລະຫັດຜ່ານ <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                        </div>
                        <input type="password" name="password_confirmation" id="confirmPassword" required
                            placeholder="ໃສ່ລະຫັດຜ່ານຄືນໃໝ່"
                            class="w-full pl-10 pr-12 py-3 border border-gray-300 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500"/>
                        <button type="button" onclick="togglePwd('confirmPassword', 'eye2')"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600">
                            <svg id="eye2" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    <p id="matchMsg" class="text-xs mt-1 hidden"></p>
                </div>

                <button type="submit"
                    class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-3 px-4 rounded-xl transition flex items-center justify-center gap-2 shadow-lg shadow-green-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    ຕັ້ງລະຫັດຜ່ານໃໝ່
                </button>
            </form>

            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="text-sm text-blue-600 hover:text-blue-800 hover:underline flex items-center justify-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    ກັບໄປໜ້າ Login
                </a>
            </div>
        </div>
    </div>

    <script>
    function togglePwd(inputId, eyeId) {
        var input = document.getElementById(inputId);
        input.type = input.type === 'password' ? 'text' : 'password';
    }

    // Password strength
    document.getElementById('newPassword').addEventListener('input', function() {
        var v = this.value;
        var score = 0;
        if (v.length >= 6) score++;
        if (v.length >= 10) score++;
        if (/[A-Z]/.test(v)) score++;
        if (/[0-9]/.test(v)) score++;
        if (/[^A-Za-z0-9]/.test(v)) score++;

        var bar = document.getElementById('strengthBar');
        var txt = document.getElementById('strengthText');
        var colors = ['', 'bg-red-400', 'bg-orange-400', 'bg-yellow-400', 'bg-lime-500', 'bg-green-500'];
        var labels = ['', 'ອ່ອນຫຼາຍ', 'ອ່ອນ', 'ປານກາງ', 'ແຂງ', 'ແຂງຫຼາຍ'];
        var textColors = ['', 'text-red-500', 'text-orange-500', 'text-yellow-600', 'text-lime-600', 'text-green-600'];

        bar.className = 'h-full rounded-full transition-all duration-300 ' + (colors[score] || '');
        bar.style.width = (score * 20) + '%';
        txt.textContent = score > 0 ? labels[score] : '';
        txt.className = 'text-xs mt-1 ' + (textColors[score] || 'text-gray-400');

        checkMatch();
    });

    document.getElementById('confirmPassword').addEventListener('input', checkMatch);

    function checkMatch() {
        var p1 = document.getElementById('newPassword').value;
        var p2 = document.getElementById('confirmPassword').value;
        var msg = document.getElementById('matchMsg');
        if (!p2) { msg.classList.add('hidden'); return; }
        msg.classList.remove('hidden');
        if (p1 === p2) {
            msg.textContent = 'ລະຫັດຜ່ານກົງກັນ';
            msg.className = 'text-xs mt-1 text-green-600';
        } else {
            msg.textContent = 'ລະຫັດຜ່ານບໍ່ກົງກັນ';
            msg.className = 'text-xs mt-1 text-red-500';
        }
    }
    </script>
</body>
</html>
