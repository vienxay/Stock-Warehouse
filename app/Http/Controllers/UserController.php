<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    const ROLES = [
        'super_admin'     => 'Super Admin',
        'admin'           => 'Admin',
        'manager'         => 'ຜູ້ຈັດການ',
        'warehouse_staff' => 'ພະນັກງານສາງ',
        'staff'           => 'ພະນັກງານ',
    ];

    public function index(Request $request)
    {
        $query = User::with('branch');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('username', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }
        if ($request->role) {
            $query->where('role', $request->role);
        }
        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }
        if ($request->status !== null && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        $users    = $query->latest()->paginate(15)->withQueryString();
        $branches = Branch::where('is_active', true)->get();
        $roles    = self::ROLES;

        return view('users.index', compact('users', 'branches', 'roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'username'  => 'required|string|max:50|unique:users',
            'email'     => 'nullable|email|unique:users',
            'phone'     => 'nullable|string|max:20',
            'role'      => ['required', Rule::in(array_keys(self::ROLES))],
            'branch_id' => 'nullable|exists:branches,id',
            'password'  => 'required|string|min:6|confirmed',
        ], [
            'password.confirmed' => 'ລະຫັດຜ່ານບໍ່ກົງກັນ',
            'password.min'       => 'ລະຫັດຜ່ານຕ້ອງຢ່າງໜ້ອຍ 6 ຕົວ',
            'username.unique'    => 'Username ນີ້ຖືກໃຊ້ແລ້ວ',
            'email.unique'       => 'Email ນີ້ຖືກໃຊ້ແລ້ວ',
        ]);

        $data['password']  = Hash::make($data['password']);
        $data['is_active'] = true;

        $newUser = User::create($data);

        AuditLog::log('user_create', 'ສ້າງຜູ້ໃຊ້ "' . $newUser->name . '"', $newUser);

        return redirect()->route('users.index')->with('success', 'ສ້າງຜູ້ໃຊ້ "' . $newUser->name . '" ສຳເລັດ');
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100',
            'username'  => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'email'     => ['nullable', 'email', Rule::unique('users')->ignore($user->id)],
            'phone'     => 'nullable|string|max:20',
            'role'      => ['required', Rule::in(array_keys(self::ROLES))],
            'branch_id' => 'nullable|exists:branches,id',
            'password'  => 'nullable|string|min:6|confirmed',
        ], [
            'password.confirmed' => 'ລະຫັດຜ່ານບໍ່ກົງກັນ',
            'password.min'       => 'ລະຫັດຜ່ານຕ້ອງຢ່າງໜ້ອຍ 6 ຕົວ',
        ]);

        // Only update password if provided
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        AuditLog::log('user_update', 'ແກ້ໄຂຜູ້ໃຊ້ "' . $user->name . '"', $user);

        return back()->with('success', 'ແກ້ໄຂຜູ້ໃຊ້ "' . $user->name . '" ສຳເລັດ');
    }

    public function toggleActive(User $user)
    {
        // Prevent disabling yourself
        if ($user->id === Auth::id()) {
            return back()->with('error', 'ບໍ່ສາມາດປິດໃຊ້ງານຕົວເອງໄດ້');
        }

        $user->update(['is_active' => !$user->is_active]);

        $action = $user->is_active ? 'user_activate' : 'user_deactivate';
        AuditLog::log($action, ($user->is_active ? 'ເປີດ' : 'ປິດ') . 'ໃຊ້ງານຜູ້ໃຊ້ "' . $user->name . '"', $user);

        $status = $user->is_active ? 'ເປີດໃຊ້ງານ' : 'ປິດໃຊ້ງານ';
        return back()->with('success', "{$status} ຜູ້ໃຊ້ \"{$user->name}\" ສຳເລັດ");
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'ບໍ່ສາມາດລຶບຕົວເອງໄດ້');
        }

        AuditLog::log('user_delete', 'ລຶບຜູ້ໃຊ້ "' . $user->name . '"', $user);
        $user->delete();
        return back()->with('success', 'ລຶບຜູ້ໃຊ້ "' . $user->name . '" ສຳເລັດ');
    }
}
