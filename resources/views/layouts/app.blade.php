<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Stock Management</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Noto Sans Lao', sans-serif; }
        .sidebar-link.active { background: rgba(255,255,255,0.15); }
        .sidebar-link:hover { background: rgba(255,255,255,0.1); }
    </style>
    @if(app()->getLocale() === 'zh')
    <style>
        body { font-family: 'PingFang SC', 'Noto Sans SC', 'Microsoft YaHei', sans-serif; }
    </style>
    @endif
</head>
<body class="bg-gray-100 min-h-screen">

    <div class="flex h-screen overflow-hidden">

        {{-- ========== SIDEBAR ========== --}}
        <aside id="sidebar"
            class="w-64 bg-linear-to-b from-blue-900 to-blue-800 text-white flex flex-col shrink-0 transition-all duration-300 z-30">

            {{-- Logo --}}
            @php
                $companyLogo = \App\Models\Setting::get('company_logo');
                $companyName = \App\Models\Setting::get('company_name', 'ລະບົບສາງ');
            @endphp
            <div class="flex items-center gap-3 px-5 py-5 border-b border-blue-700">
                <div class="w-9 h-9 bg-white rounded-lg flex items-center justify-center shrink-0 overflow-hidden">
                    @if($companyLogo)
                        <img src="{{ Storage::disk('public')->url($companyLogo) }}"
                             alt="{{ $companyName }}"
                             class="w-full h-full object-contain p-0.5"/>
                    @else
                        <svg class="w-5 h-5 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    @endif
                </div>
                <div>
                    <h1 class="font-bold text-sm leading-tight truncate">{{ $companyName }}</h1>
                    <p class="text-blue-300 text-xs">Stock Management</p>
                </div>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1">

                <x-sidebar-link route="dashboard" icon="dashboard">
                    {{ __('nav.dashboard') }}
                </x-sidebar-link>

                <div class="pt-3 pb-1">
                    <p class="text-blue-400 text-xs font-semibold uppercase tracking-wider px-3">{{ __('nav.products_section') }}</p>
                </div>
                <x-sidebar-link route="products.index" icon="product">
                    {{ __('nav.products') }}
                </x-sidebar-link>
                <x-sidebar-link route="categories.index" icon="category">
                    {{ __('nav.categories') }}
                </x-sidebar-link>
                <x-sidebar-link route="catalog.index" icon="catalog">
                    {{ __('nav.catalog') }}
                </x-sidebar-link>

                <div class="pt-3 pb-1">
                    <p class="text-blue-400 text-xs font-semibold uppercase tracking-wider px-3">{{ __('nav.warehouse_section') }}</p>
                </div>
                @if(Auth::user()->canManageStock())
                <x-sidebar-link route="stock.in" icon="stock-in">
                    {{ __('nav.stock_in') }}
                </x-sidebar-link>
                <x-sidebar-link route="stock.out" icon="stock-out">
                    {{ __('nav.stock_out') }}
                </x-sidebar-link>
                <x-sidebar-link route="transfers.index" icon="transfer">
                    {{ __('nav.transfers') }}
                </x-sidebar-link>
                @endif
                <x-sidebar-link route="requests.index" icon="request">
                    {{ __('nav.requests') }}
                </x-sidebar-link>
                <x-sidebar-link route="reports.index" icon="report">
                    {{ __('nav.reports') }}
                </x-sidebar-link>

                @if(Auth::user()->isAdmin())
                <div class="pt-3 pb-1">
                    <p class="text-blue-400 text-xs font-semibold uppercase tracking-wider px-3">{{ __('nav.settings_section') }}</p>
                </div>
                <x-sidebar-link route="warehouses.index" icon="warehouse">
                    {{ __('nav.warehouses') }}
                </x-sidebar-link>
                <x-sidebar-link route="branches.index" icon="branch">
                    {{ __('nav.branches') }}
                </x-sidebar-link>
                <x-sidebar-link route="users.index" icon="users">
                    {{ __('nav.users') }}
                </x-sidebar-link>
                <x-sidebar-link route="settings.index" icon="settings">
                    {{ __('nav.settings') }}
                </x-sidebar-link>
                <x-sidebar-link route="backups.index" icon="backup">
                    {{ __('nav.backups') }}
                </x-sidebar-link>
                <x-sidebar-link route="audit.index" icon="audit">
                    {{ __('nav.audit') }}
                </x-sidebar-link>
                @endif
            </nav>

            {{-- Logout --}}
            <div class="px-3 py-4 border-t border-blue-700">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full flex items-center gap-3 px-3 py-2.5 text-blue-200 hover:text-white hover:bg-red-600/30 rounded-lg transition text-sm">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        {{ __('nav.logout') }}
                    </button>
                </form>
            </div>
        </aside>

        {{-- ========== MAIN CONTENT ========== --}}
        <div class="flex-1 flex flex-col overflow-hidden">

            {{-- Top Navbar --}}
            <header class="bg-white shadow-sm z-20 shrink-0">
                <div class="flex items-center justify-between px-6 py-3">
                    <div class="flex items-center gap-4">
                        <button onclick="toggleSidebar()" class="text-gray-500 hover:text-gray-700 lg:hidden">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        <nav class="text-sm text-gray-500">
                            <span class="font-medium text-gray-800">@yield('page_title', 'Dashboard')</span>
                        </nav>
                    </div>
                    <div class="flex items-center gap-3">

                        {{-- ===== Language Switcher ===== --}}
                        <div class="flex items-center gap-0.5 bg-gray-100 rounded-lg p-1">
                            @foreach(['lo' => 'ລາວ', 'en' => 'EN', 'zh' => '中文'] as $code => $label)
                                <a href="{{ route('lang.switch', $code) }}"
                                    class="px-2.5 py-1 rounded-md text-xs font-medium transition
                                        {{ app()->getLocale() === $code
                                            ? 'bg-white text-blue-700 shadow-sm font-semibold'
                                            : 'text-gray-500 hover:text-gray-700' }}">
                                    {{ $label }}
                                </a>
                            @endforeach
                        </div>

                        {{-- ===== Notifications Bell ===== --}}
                        @php
                            /** @var \App\Models\User $authUser */
                            $authUser     = Auth::user();
                            $unreadCount  = $authUser->unreadNotifications()->count();
                        @endphp
                        <div class="relative" id="notifWrapper">
                            <button onclick="toggleNotif()" id="notifBtn"
                                class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                </svg>
                                <span id="notifBadge"
                                    style="display:none"
                                    class="absolute -top-0.5 -right-0.5 min-w-[1.1rem] h-[1.1rem] bg-red-500 text-white text-[10px] font-bold rounded-full items-center justify-center px-1">
                                </span>
                            </button>

                            {{-- Dropdown --}}
                            <div id="notifDropdown"
                                class="hidden absolute right-0 top-full mt-2 w-80 bg-white rounded-xl shadow-xl border border-gray-100 z-50 overflow-hidden">

                                <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                                    <span class="text-sm font-semibold text-gray-800">{{ __('nav.notifications') }}</span>
                                    @if($unreadCount > 0)
                                        <form method="POST" action="{{ route('notifications.read-all') }}">
                                            @csrf
                                            <button type="submit" class="text-xs text-blue-600 hover:underline">{{ __('nav.mark_all_read') }}</button>
                                        </form>
                                    @endif
                                </div>

                                <div class="max-h-72 overflow-y-auto divide-y divide-gray-50">
                                    @php
                                        /** @var \App\Models\User $authUser */
                                        $notifList = $authUser->notifications()->latest()->limit(10)->get();
                                    @endphp
                                    @forelse($notifList as $notif)
                                        @php
                                            $d    = $notif->data;
                                            $type = $d['type'] ?? 'new_request';
                                            $iconColor = match($type) {
                                                'request_approved' => 'bg-green-100 text-green-600',
                                                'request_rejected' => 'bg-red-100 text-red-500',
                                                'request_issued'   => 'bg-purple-100 text-purple-600',
                                                default            => 'bg-blue-100 text-blue-600',
                                            };
                                        @endphp
                                        <a href="{{ $d['url'] ?? route('requests.index') }}"
                                            onclick="markRead('{{ $notif->id }}')"
                                            class="flex gap-3 px-4 py-3 hover:bg-gray-50 transition {{ $notif->read_at ? '' : 'bg-blue-50' }}">
                                            <div class="w-8 h-8 rounded-full {{ $iconColor }} flex items-center justify-center shrink-0 mt-0.5">
                                                @if($type === 'request_approved')
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                @elseif($type === 'request_rejected')
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                @elseif($type === 'request_issued')
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8"/></svg>
                                                @else
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                @if($type === 'new_request')
                                                    <p class="text-xs font-semibold text-gray-800">ຄຳຮ້ອງໃໝ່ #{{ $d['request_no'] ?? '' }}</p>
                                                    <p class="text-xs text-gray-500 truncate">ຈາກ {{ $d['requester'] ?? '' }} · {{ $d['item_count'] ?? 0 }} ລາຍການ</p>
                                                @else
                                                    <p class="text-xs font-semibold text-gray-800">{{ $d['message'] ?? '' }}</p>
                                                    <p class="text-xs text-gray-500 font-mono">#{{ $d['request_no'] ?? '' }}</p>
                                                    @if(!empty($d['rejection_reason']))
                                                        <p class="text-xs text-red-500 truncate">{{ $d['rejection_reason'] }}</p>
                                                    @endif
                                                @endif
                                                <p class="text-[10px] text-gray-400 mt-0.5">{{ $notif->created_at->diffForHumans() }}</p>
                                            </div>
                                            @if(!$notif->read_at)
                                                <span class="w-2 h-2 bg-blue-500 rounded-full shrink-0 mt-2"></span>
                                            @endif
                                        </a>
                                    @empty
                                        <div class="px-4 py-8 text-center text-xs text-gray-400">{{ __('nav.no_notifications') }}</div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        {{-- User menu --}}
                        <div class="relative" id="userMenuWrapper">
                            <button onclick="toggleUserMenu()"
                                class="flex items-center gap-2 text-sm hover:bg-gray-100 rounded-xl px-2 py-1.5 transition">
                                <div class="w-8 h-8 rounded-full overflow-hidden bg-blue-600 flex items-center justify-center shrink-0">
                                    @if(Auth::user()->avatar)
                                        <img src="{{ Storage::disk('public')->url(Auth::user()->avatar) }}"
                                             alt="{{ Auth::user()->name }}" class="w-full h-full object-cover"/>
                                    @else
                                        <span class="text-white font-bold text-xs">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                    @endif
                                </div>
                                <span class="text-gray-700 font-medium hidden md:block">{{ Auth::user()->name }}</span>
                                <svg class="w-4 h-4 text-gray-400 hidden md:block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            {{-- Dropdown --}}
                            <div id="userMenuDropdown"
                                class="hidden absolute right-0 top-full mt-2 w-52 bg-white rounded-xl shadow-xl border border-gray-100 z-50 overflow-hidden">
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->username }}</p>
                                </div>
                                <div class="py-1">
                                    <a href="{{ route('profile.show') }}"
                                        class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                        </svg>
                                        ຂໍ້ມູນສ່ວນຕົວ
                                    </a>
                                </div>
                                <div class="py-1 border-t border-gray-100">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit"
                                            class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                            </svg>
                                            ອອກຈາກລະບົບ
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="flex-1 overflow-y-auto p-6">
                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-xl flex items-center gap-3">
                        <svg class="w-5 h-5 text-green-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-green-700 text-sm">{{ session('success') }}</p>
                    </div>
                @endif
                @if(session('error'))
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-xl flex items-center gap-3">
                        <svg class="w-5 h-5 text-red-500 shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-red-700 text-sm">{{ session('error') }}</p>
                    </div>
                @endif
                @if(session('warning'))
                    <div class="mb-4 p-4 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-3">
                        <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <p class="text-amber-700 text-sm">{{ session('warning') }}</p>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    {{-- ===== Notification Toast Container ===== --}}
    <div id="toastContainer" class="fixed bottom-5 right-5 z-50 flex flex-col gap-2"></div>

    <script>
        // Sidebar toggle
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('-translate-x-full');
        }

        // Notification dropdown
        function toggleNotif() {
            document.getElementById('notifDropdown').classList.toggle('hidden');
        }
        var _notifBase = '{{ url("notifications") }}';
        function markRead(id) {
            fetch(_notifBase + '/' + id + '/read', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            });
        }
        // User menu dropdown
        function toggleUserMenu() {
            document.getElementById('userMenuDropdown').classList.toggle('hidden');
        }

        document.addEventListener('click', function(e) {
            var notifWrapper = document.getElementById('notifWrapper');
            if (notifWrapper && !notifWrapper.contains(e.target)) {
                document.getElementById('notifDropdown').classList.add('hidden');
            }
            var userWrapper = document.getElementById('userMenuWrapper');
            if (userWrapper && !userWrapper.contains(e.target)) {
                document.getElementById('userMenuDropdown').classList.add('hidden');
            }
        });

        // Real-time notification polling (every 30s)
        var _notifCount = parseInt('{{ $unreadCount }}', 10);
        var _notifCountUrl = '{{ route("notifications.count") }}';
        var _toastLabel = {
            toast: '{{ __("nav.new_notif_toast") }}',
            items: '{{ __("nav.items") }}'
        };

        function _updateBadge(count) {
            var badge = document.getElementById('notifBadge');
            if (!badge) return;
            badge.textContent = count > 9 ? '9+' : count;
            badge.style.display = count > 0 ? 'flex' : 'none';
        }

        function _showToast(text) {
            var toast = document.createElement('div');
            toast.className = 'bg-blue-600 text-white px-4 py-3 rounded-xl shadow-lg flex items-center gap-2 text-sm animate-pulse';
            toast.innerHTML =
                '<svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
                '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>' +
                '</svg><span>' + text + '</span>';
            document.getElementById('toastContainer').appendChild(toast);
            setTimeout(function() { toast.remove(); }, 4000);
        }

        function _pollNotifications() {
            fetch(_notifCountUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    var newCount = data.count || 0;
                    if (newCount > _notifCount) {
                        var diff = newCount - _notifCount;
                        _showToast(_toastLabel.toast + ' ' + diff + ' ' + _toastLabel.items);
                    }
                    _notifCount = newCount;
                    _updateBadge(newCount);
                })
                .catch(function() {});
        }

        _updateBadge(_notifCount);
        setInterval(_pollNotifications, 30000);
    </script>
    @stack('scripts')

    {{-- ===== JS DOM Translator (non-Lao locales only) ===== --}}
    @if(app()->getLocale() !== 'lo')
    <script id="ui-t" type="application/json">@json(\Illuminate\Support\Facades\Lang::get('ui'))</script>
    <script>
    (function() {
        var _T = JSON.parse(document.getElementById('ui-t').textContent);

        function translateTextNode(node) {
            var orig = node.nodeValue;
            var key  = orig.trim();
            if (key && _T[key]) {
                node.nodeValue = orig.replace(key, _T[key]);
            }
        }

        function translateEl(el) {
            var ph = el.getAttribute('placeholder');
            if (ph && _T[ph.trim()]) el.placeholder = _T[ph.trim()];
            var ti = el.getAttribute('title');
            if (ti && _T[ti.trim()]) el.title = _T[ti.trim()];
        }

        function walkAndTranslate(root) {
            var walker = document.createTreeWalker(
                root,
                NodeFilter.SHOW_TEXT,
                {
                    acceptNode: function(n) {
                        var tag = n.parentNode && n.parentNode.tagName;
                        if (tag === 'SCRIPT' || tag === 'STYLE') return NodeFilter.FILTER_REJECT;
                        return n.nodeValue.trim() ? NodeFilter.FILTER_ACCEPT : NodeFilter.FILTER_REJECT;
                    }
                }
            );
            var nodes = [];
            while (walker.nextNode()) nodes.push(walker.currentNode);
            nodes.forEach(translateTextNode);

            root.querySelectorAll('[placeholder],[title]').forEach(translateEl);
        }

        document.addEventListener('DOMContentLoaded', function() {
            walkAndTranslate(document.body);
        });
    })();
    </script>
    @endif
</body>
</html>
