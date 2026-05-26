<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile.index', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $data = $request->validate([
            'name'   => 'required|string|max:100',
            'phone'  => 'nullable|string|max:20',
            'email'  => 'nullable|email|max:100|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ], [
            'name.required' => 'ກະລຸນາໃສ່ຊື່',
            'email.unique'  => 'ອີເມວນີ້ຖືກໃຊ້ແລ້ວ',
            'email.email'   => 'ຮູບແບບອີເມວບໍ່ຖືກຕ້ອງ',
            'avatar.image'  => 'ຕ້ອງເປັນໄຟລ໌ຮູບ',
            'avatar.max'    => 'ຮູບຂະໜາດໃຫຍ່ເກີນ 2MB',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        AuditLog::log('profile_update', 'ແກ້ໄຂຂໍ້ມູນສ່ວນຕົວ: ' . $user->name);

        return back()->with('success', 'ອັບເດດຂໍ້ມູນສ່ວນຕົວສຳເລັດ');
    }

    public function changePassword(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $request->validate([
            'current_password'      => 'required|string',
            'password'              => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
        ], [
            'current_password.required' => 'ກະລຸນາໃສ່ລະຫັດຜ່ານປັດຈຸບັນ',
            'password.required'         => 'ກະລຸນາໃສ່ລະຫັດຜ່ານໃໝ່',
            'password.min'              => 'ລະຫັດຜ່ານຕ້ອງມີຢ່າງໜ້ອຍ 6 ຕົວອັກສອນ',
            'password.confirmed'        => 'ລະຫັດຜ່ານໃໝ່ບໍ່ກົງກັນ',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('pwd_error', 'ລະຫັດຜ່ານປັດຈຸບັນບໍ່ຖືກຕ້ອງ');
        }

        $user->update(['password' => $request->password]);

        AuditLog::log('password_change', 'User ' . $user->name . ' ປ່ຽນລະຫັດຜ່ານ');

        return back()->with('pwd_success', 'ປ່ຽນລະຫັດຜ່ານສຳເລັດ');
    }

    public function removeAvatar()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
        }

        return back()->with('success', 'ລຶບຮູບໂປຣໄຟລ໌ສຳເລັດ');
    }
}
