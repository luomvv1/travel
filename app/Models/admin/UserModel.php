<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserModel extends Model
{
    use HasFactory;

    protected $table = 'nguoidung';

    public function getAllUsers()
    {
        // Lấy danh sách người dùng, sắp xếp mới nhất lên đầu
        return DB::table($this->table)->orderBy('ndid', 'DESC')->get();
    }

    // Hàm mở khóa (kích hoạt lại)
    public function updateActive($id)
    {
        return DB::table($this->table)
            ->where('ndid', $id) 
            ->update(['trangthai' => 'hoat_dong']); 
    }

    // Hàm thay đổi trạng thái (khóa/xóa)
    public function changeStatus($id, $dbStatus)
    {
        return DB::table($this->table)
            ->where('ndid', $id) 
            ->update(['trangthai' => $dbStatus]); 
    }
}