<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class KhuyenMaiModel extends Model
{
    protected $table = 'khuyenmai';

    public function getAll()
    {
        return DB::table($this->table)->orderByDesc('ngaytao')->get();
    }

    public function getById($id)
    {
        return DB::table($this->table)->where('macode', $id)->first();
    }

    public function createData($data)
    {
        return DB::table($this->table)->insert($data);
    }

    public function updateData($id, $data)
    {
        return DB::table($this->table)->where('macode', $id)->update($data);
    }

    public function deleteData($id)
    {
        return DB::table($this->table)->where('macode', $id)->delete();
    }
}