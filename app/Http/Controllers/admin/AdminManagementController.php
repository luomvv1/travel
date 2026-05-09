<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\admin\AdminModel;
use Illuminate\Http\Request;

class AdminManagementController extends Controller
{
    private $admin;

    public function __construct()
    {
        $this->admin = new AdminModel();
    }

    public function index()
    {
        $title = 'Quản lý Admin';
        $admin = $this->admin->getAdmin();

        return view('admin.profile-admin', compact('title', 'admin'));
    }

    public function updateAdmin(Request $request)
    {
        // Lấy thông tin admin cũ
        $admin = $this->admin->getAdmin();
        $oldPass = $admin->matkhau;

        $password = $request->password;

        // Nếu mật khẩu gửi lên KHÁC với chuỗi băm trong DB -> Admin đã gõ mật khẩu mới
        if ($password != $oldPass) {
            // Sử dụng chuẩn SHA-256 giống lúc bạn tạo dữ liệu
            $password = hash('sha256', $password);
        }
        
        // Gắn dữ liệu theo đúng tên cột trong CSDL
        $dataUpdate = [
            'hoten'   => $request->fullName,
            'matkhau' => $password,
            'email'   => $request->email,
            'diachi'  => $request->address
        ];

        $update = $this->admin->updateAdmin($dataUpdate);
        $newinfo = $this->admin->getAdmin();

        if ($update) {
            return response()->json([
                'success' => true,
                'data' => $newinfo
            ]);
        } else {
            return response()->json(['success' => false, 'message' => 'Không có thông tin nào thay đổi!']);
        }
    }
}