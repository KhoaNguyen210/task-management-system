<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    // Show login form
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Handle login request
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            if ($user) {
                $user->increment('failed_login_attempts');
                if ($user->failed_login_attempts >= 5) {
                    $user->is_locked = true;
                    $user->save();
                    return back()->withErrors(['username' => 'Tài khoản đã bị khóa do nhập sai quá 5 lần.']);
                }
                $user->save();
            }
            return back()->withErrors(['username' => 'Tên đăng nhập mật khẩu không đúng.']);
        }

        if ($user->is_locked) {
            return back()->withErrors(['username' => 'Tài khoản đã bị khóa. Vui lòng liên hệ quản trị viên.']);
        }

        $user->failed_login_attempts = 0;
        $user->last_login_time = now();
        $user->save();

        Auth::login($user);
        Log::info('User logged in: ' . $user->username);

        switch ($user->role) {
            case 'Dean':
                return redirect()->route('dashboard.dean');
            case 'Department Head':
                return redirect()->route('dashboard.department_head');
            case 'Lecturer':
                return redirect()->route('dashboard.lecturer');
            case 'Secretary':
            case 'Educational Staff':
                return redirect()->route('dashboard.secretary');
            default:
                return redirect('/login')->with('error', 'Vai trò không hợp lệ.');
        }
    }

    // Handle logout request
    public function logout(Request $request)
    {
        if ($request->has('confirm') && $request->confirm === '0') {
            return back()->with('warning', 'Bạn có dữ liệu chưa lưu. Bạn có chắc chắn muốn đăng xuất?');
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Đăng xuất thành công.');
    }
}
