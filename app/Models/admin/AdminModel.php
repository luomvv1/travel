<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AdminModel extends Model
{
    use HasFactory;

    protected $table = 'nguoidung';

    public function getAdmin(){
        // Phải có where để lấy đúng tài khoản admin
        return DB::table($this->table)->where('tendangnhap', 'admin')->first();
    }

    public function updateAdmin($data){
        return DB::table($this->table)
        ->where('tendangnhap', 'admin')
        ->update($data);
    }
}