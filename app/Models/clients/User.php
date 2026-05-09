<?php

namespace App\Models\clients;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;

    protected $table = 'nguoidung'; // Cập nhật tên bảng
    protected $primaryKey = 'ndid'; // Khai báo khóa chính mới

    public function getUserId($tendangnhap)
    {
        return DB::table($this->table)
            ->where('tendangnhap', $tendangnhap)
            ->value('ndid'); // Dùng value() trực tiếp sẽ tự động lấy giá trị của cột này
    }

    public function getUser($id)
    {
        $users = DB::table($this->table)
            ->where('ndid', $id)
            ->first();

        return $users;
    }

    public function updateUser($id, $data)
    {
        $update = DB::table($this->table)
            ->where('ndid', $id)
            ->update($data);

        return $update;
    }

    public function getMyTours($id)
    {
        // Sử dụng leftJoin cho thanhtoan và lichkhoihanh để lấy đủ thông tin
        $myTours = DB::table('dattour as dt')
            ->join('tour as t', 'dt.tourid', '=', 't.tourid')
            ->leftJoin('lichkhoihanh as lkh', 'dt.lichid', '=', 'lkh.lichid')
            ->leftJoin('thanhtoan as tt', 'dt.dtid', '=', 'tt.dtid')
            ->where('dt.ndid', $id)
            ->orderByDesc('dt.ngaytao')
            ->orderByDesc('dt.dtid')
            ->select(
                'dt.dtid',
                'dt.tourid',
                'dt.lichid',
                'dt.ngaydat',
                'dt.songuoilon',
                'dt.sotreem',
                'dt.tongtien',
                'dt.giamgia',
                'dt.giacuoi',
                'dt.makhuyenmai',
                'dt.trangthai as booking_status',
                't.tentour',
                't.mota',
                't.diadiemden',
                't.diemkhoihanh',
                't.songay',
                't.gianguoilon',
                't.giatreem',
                'lkh.ngaybatdau',
                'lkh.ngayketthuc',
                'lkh.sochocon',
                'lkh.trangthai as schedule_status',
                'tt.phuongthuc',
                'tt.sotien',
                'tt.magiaodich',
                'tt.trangthai as payment_status'
            )
            ->paginate(5);

        foreach ($myTours as $tour) {
            // Lấy rating từ bảng danhgia cho mỗi tour
            $tour->rating = DB::table('danhgia')
                ->where('tourid', $tour->tourid)
                ->where('ndid', $id)
                ->value('sosao') ?? 0; // Cột điểm đánh giá hiện là sosao
        }
        
        foreach ($myTours as $tour) {
            // Lấy danh sách hình ảnh thuộc về tour từ bảng hinhanhtour
            $tour->images = DB::table('hinhanhtour')
                ->where('tourid', $tour->tourid)
                ->pluck('urlanh'); // Cột link ảnh hiện là urlanh
        }

        return $myTours;
    }
}
