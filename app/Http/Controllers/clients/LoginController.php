<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use App\Models\clients\Login;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    private $login;

    public function __construct()
    {
        $this->login = new Login();
    }

    public function index()
    {
        $title = 'Đăng nhập';
        return view('clients.login', compact('title'));
    }

    // Đăng ký
    public function register(Request $request)
    {
        $request->validate([
            'username_register' => ['required', 'string', 'max:100', 'not_regex:/[<>\'\"%;()&+]/'],
            'email_register'    => ['required', 'email', 'max:255'],
            'password_register' => ['required', 'string', 'min:6', 'not_regex:/[<>\'\"%;()&+]/'],
            're_pass'           => ['required', 'same:password_register'],
        ], [
            'username_register.required'  => 'Tên tài khoản không được để trống.',
            'username_register.not_regex' => 'Tên tài khoản không được chứa ký tự đặc biệt.',
            'email_register.required'     => 'Email không được để trống.',
            'email_register.email'        => 'Email không hợp lệ.',
            'password_register.required'  => 'Mật khẩu không được để trống.',
            'password_register.min'       => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password_register.not_regex' => 'Mật khẩu không được chứa ký tự đặc biệt.',
            're_pass.required'            => 'Vui lòng nhập lại mật khẩu.',
            're_pass.same'                => 'Mật khẩu nhập lại không khớp.',
        ]);

        $tendangnhap = trim($request->username_register);
        $email = trim($request->email_register);
        $matkhau = $request->password_register;

        if ($this->login->checkUserExist($tendangnhap, $email)) {
            return back()->withInput()->with('error_register', 'Tên người dùng hoặc email đã tồn tại!');
        }

        $created = $this->login->registerAcount([
            'tendangnhap' => $tendangnhap,
            'email'       => $email,
            'matkhau'     => hash('sha256', $matkhau),
            'vaitro'      => 'khach_hang',
            'trangthai'   => 'hoat_dong',
        ]);

        if (! $created) {
            return back()->withInput()->with('error_register', 'Không thể tạo tài khoản, vui lòng thử lại.');
        }

        return back()->with('success_register', 'Đăng ký thành công! Bạn có thể đăng nhập ngay.');
    }

    // Đăng nhập
    public function login(Request $request)
    {
        $request->validate([
            'username_login' => ['required', 'string', 'max:100', 'not_regex:/[<>\'\"%;()&+]/'],
            'password_login' => ['required', 'string', 'min:6'],
        ], [
            'username_login.required'  => 'Tên tài khoản không được để trống.',
            'username_login.not_regex' => 'Tên tài khoản không được chứa ký tự đặc biệt.',
            'password_login.required'  => 'Mật khẩu không được để trống.',
            'password_login.min'       => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ]);

        $tendangnhap = trim($request->username_login);
        $matkhau = $request->password_login;

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

        $request->session()->regenerate();
        $request->session()->put('tendangnhap', $user->tendangnhap);
        $request->session()->put('ndid', $user->ndid);
        $request->session()->put('hoten', $user->hoten);
        $request->session()->put('vaitro', $user->vaitro);

        return redirect()->route('home');
    }

    // Đăng xuất
    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
