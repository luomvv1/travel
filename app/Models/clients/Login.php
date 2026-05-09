<?php

namespace App\Models\clients;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Login extends Model
{
    // Kiểm tra xem tên đăng nhập hoặc email đã tồn tại chưa
    public function checkUserExist($tendangnhap, $email)
    {
        return DB::table('nguoidung')
            ->where('tendangnhap', $tendangnhap)
            ->orWhere('email', $email)
            ->exists();
    }

    // Insert dữ liệu người dùng mới vào DB
    public function registerAcount($dataInsert)
    {
        return DB::table('nguoidung')->insert($dataInsert);
    }

    // Kiểm tra thông tin đăng nhập
    public function login($data_login)
    {
        return DB::table('nguoidung')
            ->where('tendangnhap', $data_login['tendangnhap'])
            ->where('matkhau', $data_login['matkhau'])
            ->first();
    }
}