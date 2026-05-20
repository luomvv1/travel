<?php

namespace App\Models\clients;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Booking extends Model
{
    use HasFactory;

    // Tên bảng trong Database của bạn
    protected $table = 'dattour';

    public function createBooking($data)
    {
        return DB::table($this->table)->insertGetId($data);
    }

    public function cancelBooking($dtid)
    {
        return DB::table($this->table)
            ->where('dtid', $dtid)
            ->update(['trangthai' => 'da_huy']);
    }

    public function checkBooking($tourId, $userId)
    {
        return DB::table($this->table)
            ->where('tourid', $tourId)
            ->where('ndid', $userId)
            ->whereIn('trangthai', ['da_xac_nhan', 'da_thanh_toan', 'hoan_thanh'])
            ->exists(); 
    }
}
