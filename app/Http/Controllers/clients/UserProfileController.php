<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use App\Models\clients\User; // Dùng đúng Model User của bạn
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    private $userModel;

    public function __construct()
    {
        // Nếu có hàm construct ở Controller cha (để check Auth/Session), bạn nhớ gọi parent
        // parent::__construct(); 
        $this->userModel = new User();
    }

    public function index()
    {
        $title = 'Thông tin cá nhân';
        
        // Lấy ID người dùng từ session đăng nhập
        $id = $this->userModel->getUserId(session('tendangnhap'));
        $user = $this->userModel->getUser($id);

        return view('clients.user-profile', compact('title', 'user'));
    }

    public function update(Request $req)
    {
        // Gắn dữ liệu để update (Đã xóa dòng 'diachi' bị lặp và sửa lại $req->gioitinh)
        $dataUpdate = [
            'hoten'       => $req->fullName,
            'diachi'      => $req->address,
            'email'       => $req->email,
            'sodienthoai' => $req->phone,
            'gioitinh'    => $req->gioitinh 
        ];

        // Lấy ID người dùng từ session
        $userId = $this->userModel->getUserId(session('tendangnhap'));

        $update = $this->userModel->updateUser($userId, $dataUpdate);
        
       if (!$update) {
            return redirect()->back()->with('error', 'Bạn chưa thay đổi thông tin nào hoặc có lỗi xảy ra!');
        }
        
        return redirect()->back()->with('success', 'Cập nhật thông tin thành công!');
    }

    public function changePassword(Request $req)
    {
        
        // Lấy ID người dùng từ session
        $userId = $this->userModel->getUserId(session('tendangnhap'));
        $user = $this->userModel->getUser($userId);

        $dbPasswordColumn = 'matkhau'; 

        // Kiểm tra mật khẩu cũ bằng SHA-256
        if (hash('sha256', $req->oldPass) === $user->{$dbPasswordColumn}) {
            
            // Cập nhật mật khẩu mới cũng bằng SHA-256
            $update = $this->userModel->updateUser($userId, [
                $dbPasswordColumn => hash('sha256', $req->newPass)
            ]);

            if (!$update) {
                return redirect()->back()->with('error', 'Mật khẩu mới trùng với mật khẩu cũ!');
            } else {
                return redirect()->back()->with('success', 'Đổi mật khẩu thành công!');
            }
            
        } else {
            return redirect()->back()->with('error', 'Mật khẩu cũ không chính xác!');
        }
    }
}

    // public function changeAvatar(Request $req)
    // {
    //     $userId = $this->getUserId();

    //     $req->validate([
    //         'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120', // Tối đa 5MB
    //     ]);

    //     $avatar = $req->file('avatar');
    //     $filename = time() . '.' . $avatar->getClientOriginalExtension();

    //     $user = $this->userModel->getUser($userId);
        
    //     // ⚠️ QUAN TRỌNG: Đổi 'anhdaidien' thành đúng tên cột lưu ảnh trong bảng `nguoidung`
    //     $dbAvatarColumn = 'anhdaidien'; 

    //     if ($user->{$dbAvatarColumn}) {
    //         $oldAvatarPath = public_path('admin/assets/images/user-profile/' . $user->{$dbAvatarColumn});
            
    //         if (file_exists($oldAvatarPath)) {
    //             unlink($oldAvatarPath);
    //         }
    //     }

    //     $avatar->move(public_path('admin/assets/images/user-profile'), $filename);
        
    //     $update = $this->userModel->updateUser($userId, [
    //         $dbAvatarColumn => $filename
    //     ]);
        
    //     $req->session()->put('avatar', $filename);
        
    //     if (!$update) {
    //         return response()->json(['error' => true, 'message' => 'Có vấn đề khi cập nhật ảnh!']);
    //     }
    //     return response()->json(['success' => true, 'message' => 'Cập nhật ảnh thành công!']);
    // }
