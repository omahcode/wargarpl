<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->isSiswa()) {
                return redirect()->route('student.dashboard');
            } elseif ($user->isEmployee()) {
                return redirect()->route('employee.dashboard');
            }
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ], [
            'login.required' => 'NIS, NIP, atau Email wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $user = User::where('email', $credentials['login'])
            ->orWhere('username', $credentials['login'])
            ->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            // Log login event to audit logs
            \App\Models\AuditLog::create([
                'user_id' => $user->id,
                'event' => 'Login',
                'description' => "User {$user->name} ({$user->role}) logged in.",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            if ($user->isAdmin()) {
                return redirect()->intended(route('admin.dashboard'));
            } elseif ($user->isSiswa()) {
                return redirect()->intended(route('student.dashboard'));
            } elseif ($user->isEmployee()) {
                return redirect()->intended(route('employee.dashboard'));
            }
        }

        return back()->withErrors([
            'login' => 'Kredensial yang Anda masukkan salah.',
        ])->withInput($request->only('login', 'remember'));
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            \App\Models\AuditLog::create([
                'user_id' => $user->id,
                'event' => 'Logout',
                'description' => "User {$user->name} logged out.",
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'new_password.required' => 'Password baru wajib diisi.',
            'new_password.min' => 'Password baru minimal harus 8 karakter.',
            'new_password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        // Audit Log
        \App\Models\AuditLog::create([
            'user_id' => $user->id,
            'event' => 'Change Password',
            'description' => "User {$user->name} changed their password.",
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return back()->with('success', 'Password Anda berhasil diperbarui.');
    }
}
