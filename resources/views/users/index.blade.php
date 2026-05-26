
@extends('layouts.app')
@section('title','ຜູ້ໃຊ້ງານ')
@section('page_title','ຈັດການຜູ້ໃຊ້ງານ')

@section('content')

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h2 class="text-xl font-bold text-gray-800">ຜູ້ໃຊ້ງານທັງໝົດ</h2>
        <p class="text-sm text-gray-400 mt-0.5">ຈັດການ ເພີ່ມ ແກ້ໄຂ ລຶບຜູ້ໃຊ້ງານ</p>
    </div>
    <button onclick="openAddModal()"
        class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition shadow-sm">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        ເພີ່ມຜູ້ໃຊ້
    </button>
</div>

{{-- Filters --}}
<form method="GET" action="{{ route('users.index') }}"
      class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 mb-5">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="col-span-2">
            <div class="relative">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="ຄົ້ນຫາ ຊື່ / username / email..."
                    class="w-full pl-9 border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"/>
            </div>
        </div>
        <div>
            <select name="role"
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- ທຸກ Role --</option>
                @foreach($roles as $key => $label)
                    <option value="{{ $key }}" {{ request('role') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <select name="status"
                class="w-full border border-gray-200 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">-- ທຸກສະຖານະ --</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>ໃຊ້ງານ</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>ປິດ</option>
            </select>
        </div>
        <div class="col-span-2 md:col-span-4 flex gap-2 justify-end">
            <button type="submit"
                class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition">
                ຄົ້ນຫາ
            </button>
            <a href="{{ route('users.index') }}"
                class="px-5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-medium transition">
                ລ້າງ
            </a>
        </div>
    </div>
</form>

{{-- Table --}}
<div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead>
            <tr class="bg-gray-50 border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wide">
                <th class="text-left px-5 py-3 font-semibold">#</th>
                <th class="text-left px-5 py-3 font-semibold">ຊື່ / Username</th>
                <th class="text-left px-5 py-3 font-semibold hidden md:table-cell">Email / ໂທ</th>
                <th class="text-left px-5 py-3 font-semibold">Role</th>
                <th class="text-left px-5 py-3 font-semibold hidden lg:table-cell">ສາຂາ</th>
                <th class="text-left px-5 py-3 font-semibold hidden xl:table-cell">ເຂົ້າຫຼ້າສຸດ</th>
                <th class="text-center px-5 py-3 font-semibold">ສະຖານະ</th>
                <th class="text-center px-5 py-3 font-semibold">ຈັດການ</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($users as $i => $u)
            <tr class="hover:bg-gray-50 transition {{ !$u->is_active ? 'opacity-60' : '' }}">
                <td class="px-5 py-3.5 text-gray-400 text-xs">{{ $users->firstItem() + $i }}</td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0
                            {{ match($u->role) {
                                'super_admin' => 'bg-purple-600',
                                'admin'       => 'bg-blue-600',
                                'manager'     => 'bg-teal-600',
                                default       => 'bg-gray-500'
                            } }}">
                            {{ mb_substr($u->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">{{ $u->name }}</p>
                            <p class="text-xs text-gray-400 font-mono">{{ $u->username }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-5 py-3.5 hidden md:table-cell">
                    <p class="text-gray-700">{{ $u->email ?? '-' }}</p>
                    <p class="text-xs text-gray-400">{{ $u->phone ?? '' }}</p>
                </td>
                <td class="px-5 py-3.5">
                    <span class="text-xs font-medium px-2.5 py-1 rounded-full
                        {{ match($u->role) {
                            'super_admin'     => 'bg-purple-100 text-purple-700',
                            'admin'           => 'bg-blue-100 text-blue-700',
                            'manager'         => 'bg-teal-100 text-teal-700',
                            'warehouse_staff' => 'bg-amber-100 text-amber-700',
                            default           => 'bg-gray-100 text-gray-600'
                        } }}">
                        {{ $roles[$u->role] ?? $u->role }}
                    </span>
                </td>
                <td class="px-5 py-3.5 text-gray-500 text-xs hidden lg:table-cell">
                    {{ $u->branch?->name ?? '-' }}
                </td>
                <td class="px-5 py-3.5 text-gray-400 text-xs hidden xl:table-cell">
                    {{ $u->last_login_at ? $u->last_login_at->diffForHumans() : 'ຍັງບໍ່ເຄີຍ' }}
                </td>
                <td class="px-5 py-3.5 text-center">
                    <form method="POST" action="{{ route('users.toggle-active', $u) }}">
                        @csrf
                        <button type="submit"
                            class="text-xs font-medium px-3 py-1 rounded-full transition
                            {{ $u->is_active
                                ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                            {{ $u->is_active ? 'ໃຊ້ງານ' : 'ປິດ' }}
                        </button>
                    </form>
                </td>
                <td class="px-5 py-3.5">
                    <div class="flex items-center justify-center gap-1">
                        <button onclick='openEditModal(@json($u))'
                            class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition" title="ແກ້ໄຂ">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </button>
                        @if($u->id !== Auth::id())
                        <form method="POST" action="{{ route('users.destroy', $u) }}"
                            onsubmit="return confirm('ລຶບຜູ້ໃຊ້ {{ addslashes($u->name) }}?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition" title="ລຶບ">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" class="text-center py-14 text-gray-400">
                    <svg class="w-12 h-12 mx-auto mb-2 text-gray-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    ຍັງບໍ່ມີຜູ້ໃຊ້ງານ
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if($users->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">{{ $users->links() }}</div>
    @endif
</div>

{{-- ===== ADD MODAL ===== --}}
<div id="addModal" onclick="if(event.target===this)closeModals()"
     style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;z-index:9999;background:rgba(0,0,0,0.5);">
    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:460px;max-width:calc(100vw - 2rem);max-height:calc(100vh - 4rem);background:#fff;border-radius:10px;box-shadow:0 8px 40px rgba(0,0,0,0.18);display:flex;flex-direction:column;overflow:hidden;">

        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid #f0f0f0;">
            <span style="font-weight:600;font-size:14px;color:#1f2937;">ເພີ່ມຜູ້ໃຊ້ໃໝ່</span>
            <button type="button" onclick="closeModals()" style="background:none;border:none;cursor:pointer;color:#9ca3af;font-size:18px;line-height:1;padding:2px 6px;">&times;</button>
        </div>

        <form method="POST" action="{{ route('users.store') }}" style="display:flex;flex-direction:column;overflow:hidden;">
            @csrf
            <input type="hidden" name="_modal" value="add">

            {{-- Validation errors --}}
            @if($errors->any() && old('_modal') === 'add')
            <div style="padding:10px 20px;background:#fef2f2;border-bottom:1px solid #fecaca;">
                @foreach($errors->all() as $err)
                <p style="color:#dc2626;font-size:12px;margin:1px 0;">• {{ $err }}</p>
                @endforeach
            </div>
            @endif

            <div style="padding:16px 20px;overflow-y:auto;display:grid;grid-template-columns:1fr 1fr;gap:12px;">

                <div style="grid-column:span 2">
                    <label style="display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px;">ຊື່ເຕັມ <span style="color:red">*</span></label>
                    <input type="text" name="name" required placeholder="ຊື່ ແລະ ນາມສະກຸນ"
                        value="{{ old('_modal') === 'add' ? old('name') : '' }}"
                        style="width:100%;border:1px solid {{ $errors->has('name') && old('_modal')==='add' ? '#f87171' : '#d1d5db' }};border-radius:6px;padding:7px 10px;font-size:13px;outline:none;box-sizing:border-box;"/>
                </div>

                <div>
                    <label style="display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px;">Username <span style="color:red">*</span></label>
                    <input type="text" name="username" required placeholder="username"
                        value="{{ old('_modal') === 'add' ? old('username') : '' }}"
                        style="width:100%;border:1px solid {{ $errors->has('username') && old('_modal')==='add' ? '#f87171' : '#d1d5db' }};border-radius:6px;padding:7px 10px;font-size:13px;font-family:monospace;outline:none;box-sizing:border-box;"/>
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px;">ເບີໂທ</label>
                    <input type="text" name="phone" placeholder="020-xxxx-xxxx"
                        value="{{ old('_modal') === 'add' ? old('phone') : '' }}"
                        style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:7px 10px;font-size:13px;outline:none;box-sizing:border-box;"/>
                </div>

                <div style="grid-column:span 2">
                    <label style="display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px;">Email</label>
                    <input type="email" name="email" placeholder="example@email.com"
                        value="{{ old('_modal') === 'add' ? old('email') : '' }}"
                        style="width:100%;border:1px solid {{ $errors->has('email') && old('_modal')==='add' ? '#f87171' : '#d1d5db' }};border-radius:6px;padding:7px 10px;font-size:13px;outline:none;box-sizing:border-box;"/>
                </div>

                <div>
                    <label style="display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px;">Role <span style="color:red">*</span></label>
                    <select name="role" required style="width:100%;border:1px solid {{ $errors->has('role') && old('_modal')==='add' ? '#f87171' : '#d1d5db' }};border-radius:6px;padding:7px 10px;font-size:13px;outline:none;box-sizing:border-box;">
                        <option value="">-- ເລືອກ --</option>
                        @foreach($roles as $key => $label)
                            <option value="{{ $key }}" {{ old('_modal')==='add' && old('role')===$key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px;">ສາຂາ</label>
                    <select name="branch_id" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:7px 10px;font-size:13px;outline:none;box-sizing:border-box;">
                        <option value="">-- ເລືອກ --</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ old('_modal')==='add' && old('branch_id')==$b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px;">ລະຫັດຜ່ານ <span style="color:red">*</span></label>
                    <input type="password" name="password" required placeholder="ຢ່າງໜ້ອຍ 6 ຕົວ"
                        style="width:100%;border:1px solid {{ $errors->has('password') && old('_modal')==='add' ? '#f87171' : '#d1d5db' }};border-radius:6px;padding:7px 10px;font-size:13px;outline:none;box-sizing:border-box;"/>
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px;">ຢືນຢັນລະຫັດຜ່ານ <span style="color:red">*</span></label>
                    <input type="password" name="password_confirmation" required placeholder="ພິມຄືນ"
                        style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:7px 10px;font-size:13px;outline:none;box-sizing:border-box;"/>
                </div>

            </div>

            <div style="display:flex;gap:8px;padding:12px 20px;border-top:1px solid #f0f0f0;background:#fafafa;">
                <button type="submit" style="flex:1;background:#2563eb;color:#fff;border:none;border-radius:6px;padding:8px;font-size:13px;font-weight:500;cursor:pointer;">ບັນທຶກ</button>
                <button type="button" onclick="closeModals()" style="flex:1;background:#fff;color:#374151;border:1px solid #d1d5db;border-radius:6px;padding:8px;font-size:13px;font-weight:500;cursor:pointer;">ຍົກເລີກ</button>
            </div>
        </form>
    </div>
</div>

{{-- ===== EDIT MODAL ===== --}}
<div id="editModal" onclick="if(event.target===this)closeModals()"
     style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;z-index:9999;background:rgba(0,0,0,0.5);">
    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:460px;max-width:calc(100vw - 2rem);max-height:calc(100vh - 4rem);background:#fff;border-radius:10px;box-shadow:0 8px 40px rgba(0,0,0,0.18);display:flex;flex-direction:column;overflow:hidden;">

        <div style="display:flex;align-items:center;justify-content:space-between;padding:14px 20px;border-bottom:1px solid #f0f0f0;">
            <span style="font-weight:600;font-size:14px;color:#1f2937;">ແກ້ໄຂຂໍ້ມູນຜູ້ໃຊ້</span>
            <button type="button" onclick="closeModals()" style="background:none;border:none;cursor:pointer;color:#9ca3af;font-size:18px;line-height:1;padding:2px 6px;">&times;</button>
        </div>

        <form method="POST" id="editForm" style="display:flex;flex-direction:column;overflow:hidden;">
            @csrf @method('PUT')
            <input type="hidden" name="_modal" value="edit">
            <div style="padding:16px 20px;overflow-y:auto;display:grid;grid-template-columns:1fr 1fr;gap:12px;">

                <div style="grid-column:span 2">
                    <label style="display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px;">ຊື່ເຕັມ <span style="color:red">*</span></label>
                    <input type="text" name="name" id="editName" required style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:7px 10px;font-size:13px;outline:none;box-sizing:border-box;"/>
                </div>

                <div>
                    <label style="display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px;">Username <span style="color:red">*</span></label>
                    <input type="text" name="username" id="editUsername" required style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:7px 10px;font-size:13px;font-family:monospace;outline:none;box-sizing:border-box;"/>
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px;">ເບີໂທ</label>
                    <input type="text" name="phone" id="editPhone" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:7px 10px;font-size:13px;outline:none;box-sizing:border-box;"/>
                </div>

                <div style="grid-column:span 2">
                    <label style="display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px;">Email</label>
                    <input type="email" name="email" id="editEmail" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:7px 10px;font-size:13px;outline:none;box-sizing:border-box;"/>
                </div>

                <div>
                    <label style="display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px;">Role <span style="color:red">*</span></label>
                    <select name="role" id="editRole" required style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:7px 10px;font-size:13px;outline:none;box-sizing:border-box;">
                        @foreach($roles as $key => $label)
                            <option value="{{ $key }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px;">ສາຂາ</label>
                    <select name="branch_id" id="editBranch" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:7px 10px;font-size:13px;outline:none;box-sizing:border-box;">
                        <option value="">-- ເລືອກ --</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px;">ລະຫັດຜ່ານໃໝ່ <span style="font-size:11px;color:#9ca3af;">(ວ່າງ = ບໍ່ປ່ຽນ)</span></label>
                    <input type="password" name="password" id="editPassword" placeholder="ໃສ່ຖ້າຕ້ອງການປ່ຽນ" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:7px 10px;font-size:13px;outline:none;box-sizing:border-box;"/>
                </div>
                <div>
                    <label style="display:block;font-size:12px;font-weight:500;color:#374151;margin-bottom:4px;">ຢືນຢັນລະຫັດຜ່ານ</label>
                    <input type="password" name="password_confirmation" placeholder="ພິມຄືນ" style="width:100%;border:1px solid #d1d5db;border-radius:6px;padding:7px 10px;font-size:13px;outline:none;box-sizing:border-box;"/>
                </div>

            </div>

            <div style="display:flex;gap:8px;padding:12px 20px;border-top:1px solid #f0f0f0;background:#fafafa;">
                <button type="submit" style="flex:1;background:#f59e0b;color:#fff;border:none;border-radius:6px;padding:8px;font-size:13px;font-weight:500;cursor:pointer;">ບັນທຶກການແກ້ໄຂ</button>
                <button type="button" onclick="closeModals()" style="flex:1;background:#fff;color:#374151;border:1px solid #d1d5db;border-radius:6px;padding:8px;font-size:13px;font-weight:500;cursor:pointer;">ຍົກເລີກ</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
const userBaseUrl = "{{ url('/users') }}";

// Auto-reopen modal on validation error
@if($errors->any() && old('_modal') === 'add')
document.addEventListener('DOMContentLoaded', () => openAddModal());
@endif

function openAddModal() {
    const el = document.getElementById('addModal');
    el.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function openEditModal(u) {
    document.getElementById('editForm').action = userBaseUrl + '/' + u.id;
    document.getElementById('editName').value     = u.name      || '';
    document.getElementById('editUsername').value = u.username  || '';
    document.getElementById('editPhone').value    = u.phone     || '';
    document.getElementById('editEmail').value    = u.email     || '';
    document.getElementById('editRole').value     = u.role      || '';
    document.getElementById('editBranch').value   = u.branch_id || '';
    document.getElementById('editPassword').value = '';
    document.querySelector('#editForm [name="password_confirmation"]').value = '';
    const el = document.getElementById('editModal');
    el.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeModals() {
    document.getElementById('addModal').style.display  = 'none';
    document.getElementById('editModal').style.display = 'none';
    document.body.style.overflow = '';
}

document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModals(); });
</script>
@endpush

@endsection
