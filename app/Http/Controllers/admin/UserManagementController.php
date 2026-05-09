<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\admin\UserModel;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    private $nguoidung;

    public function __construct()
    {
        $this->nguoidung = new UserModel();
    }

    public function index()
    {
        $title = 'Quản lý người dùng';
        $users = $this->nguoidung->getAllUsers();

        foreach ($users as $user) {
            // Định dạng tên nếu trống
            if (!$user->hoten) {
                $user->hoten = "Chưa cập nhật tên";
            }
            
            // Dịch trạng thái ENUM sang tiếng Việt cho giao diện
            if ($user->trangthai === 'hoat_dong') {
                $user->isActive = 'Hoạt động';
            } elseif ($user->trangthai === 'bi_khoa') {
                $user->isActive = 'Bị khóa';
            } else {
                $user->isActive = 'Đã xóa';
            }

            // Gán hiển thị vai trò
            if ($user->vaitro === 'quan_tri') {
                $user->role_text = 'Quản trị viên';
            } elseif ($user->vaitro === 'huong_dan_vien') {
                $user->role_text = 'Hướng dẫn viên';
            } else {
                $user->role_text = 'Khách hàng';
            }
        }

        return view('admin.users', compact('title', 'users'));
    }

    // Hàm này gộp chung để xử lý mọi trạng thái (Mở khóa, Khóa, Xóa, Khôi phục)
    public function changeStatus(Request $request)
    {
        $userId = $request->userId;
        $status = $request->status; // Nhận trực tiếp 'hoat_dong', 'bi_khoa', hoặc 'xoa' từ form

        $changeStatus = $this->nguoidung->changeStatus($userId, $status);

        if ($changeStatus) {
            return redirect()->back()->with('success', 'Trạng thái người dùng đã được cập nhật thành công!');
        } else {
            return redirect()->back()->with('error', 'Có lỗi xảy ra khi cập nhật trạng thái người dùng!');
        }
    }
}