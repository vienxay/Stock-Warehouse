<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required'    => 'ກະລຸນາໃສ່ຊື່ຜູ້ໃຊ້ ຫຼື ອີເມວ',
            'password.required' => 'ກະລຸນາໃສ່ລະຫັດຜ່ານ',
        ]);

        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = [$loginField => $request->login, 'password' => $request->password];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->withErrors(['login' => 'ບັນຊີຂອງທ່ານຖືກລະງັບການໃຊ້ງານ']);
            }

            $user->update(['last_login_at' => now()]);

            AuditLog::create([
                'user_id'     => $user->id,
                'action'      => 'login',
                'description' => "User {$user->name} logged in",
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ]);

            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors(['login' => 'ຊື່ຜູ້ໃຊ້ ຫຼື ລະຫັດຜ່ານບໍ່ຖືກຕ້ອງ'])->withInput($request->only('login'));
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            AuditLog::create([
                'user_id'     => Auth::id(),
                'action'      => 'logout',
                'description' => "User " . Auth::user()->name . " logged out",
                'ip_address'  => $request->ip(),
                'user_agent'  => $request->userAgent(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
