<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForgotPasswordController extends Controller
{
    public function showForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.forgot_password');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'email'    => 'required|email',
        ], [
            'username.required' => 'ກະລຸນາໃສ່ຊື່ຜູ້ໃຊ້',
            'email.required'    => 'ກະລຸນາໃສ່ອີເມວ',
            'email.email'       => 'ຮູບແບບອີເມວບໍ່ຖືກຕ້ອງ',
        ]);

        $user = User::where('username', $request->username)
                    ->where('email', $request->email)
                    ->where('is_active', true)
                    ->first();

        if (!$user) {
            return back()->with('error', 'ບໍ່ພົບຂໍ້ມູນ — ກວດສອບ username ແລະ email ຄືນໃໝ່');
        }

        $token = hash('sha256', $user->id . $user->email . now()->timestamp . random_int(1000, 9999));

        session([
            'pwd_token'   => $token,
            'pwd_uid'     => $user->id,
            'pwd_expires' => now()->addMinutes(15)->timestamp,
        ]);

        return redirect()->route('password.reset.show', ['token' => $token]);
    }

    public function showReset(Request $request, string $token)
    {
        if (
            session('pwd_token') !== $token ||
            !session('pwd_uid') ||
            now()->timestamp > (int) session('pwd_expires')
        ) {
            return redirect()->route('password.request')
                ->with('error', 'ລິ້ງໝົດອາຍຸ (15 ນາທີ) — ກະລຸນາຂໍໃໝ່');
        }

        return view('auth.reset_password', compact('token'));
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token'                 => 'required|string',
            'password'              => 'required|string|min:6|confirmed',
            'password_confirmation' => 'required',
        ], [
            'password.required'  => 'ກະລຸນາໃສ່ລະຫັດຜ່ານໃໝ່',
            'password.min'       => 'ລະຫັດຜ່ານຕ້ອງມີຢ່າງໜ້ອຍ 6 ຕົວອັກສອນ',
            'password.confirmed' => 'ລະຫັດຜ່ານບໍ່ກົງກັນ',
        ]);

        $token  = $request->token;
        $userId = session('pwd_uid');

        if (
            session('pwd_token') !== $token ||
            !$userId ||
            now()->timestamp > (int) session('pwd_expires')
        ) {
            return redirect()->route('password.request')
                ->with('error', 'ໂທເຄັນໝົດອາຍຸ — ກະລຸນາຂໍໃໝ່');
        }

        $user = User::find($userId);
        if (!$user) {
            return redirect()->route('login')->with('error', 'ບໍ່ພົບຜູ້ໃຊ້');
        }

        $user->update(['password' => $request->password]);

        session()->forget(['pwd_token', 'pwd_uid', 'pwd_expires']);

        AuditLog::create([
            'user_id'     => $user->id,
            'action'      => 'password_reset',
            'description' => "User {$user->name} ຣີເຊັດລະຫັດຜ່ານ",
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
        ]);

        return redirect()->route('login')
            ->with('success', 'ຣີເຊັດລະຫັດຜ່ານສຳເລັດ — ສາມາດ Login ໄດ້ເລີຍ');
    }
}
