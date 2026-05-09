<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\clients\Login;
use Illuminate\Http\Request;

class LoginAdminController extends Controller
{

    private $login;

    public function __construct()
    {
        $this->login = new Login();
    }
    public function index()
    {
        $title = 'Đăng nhập';

        return view('admin.login', compact('title'));
    }

     public function loginadmin(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'username.required'  => 'Tên tài khoản không được để trống.',
            'password.required'  => 'Mật khẩu không được để trống.',
            'password.min'       => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ]);

        $tendangnhap = trim($request->username);
        $matkhau = $request->password;

        $user = $this->login->login([
            'tendangnhap' => $tendangnhap,
            'matkhau'     => hash('sha256', $matkhau),
        ]);

        if (! $user) {
            return back()->withInput()->with('error_login', 'Thông tin tài khoản không chính xác!');
        }

        if ($user->trangthai !== 'hoat_dong') {
            return back()->with('error_login', 'Tài khoản của bạn đã bị khóa hoặc xóa!');
        }
        if($user->vaitro !== 'quan_tri') {
            return back()->with('error_login', 'Bạn không có quyền truy cập vào trang quản trị!');
        }

        $request->session()->regenerate();
        $request->session()->put('tendangnhap', $user->tendangnhap);
        $request->session()->put('ndid', $user->ndid);
        $request->session()->put('hoten', $user->hoten);
        $request->session()->put('vaitro', $user->vaitro);

        return redirect()->route('admin.profile');
    }
    // Đăng xuất
    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
