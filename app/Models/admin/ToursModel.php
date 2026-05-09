<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ToursModel extends Model
{
    use HasFactory;

    protected $table = 'tour';

    public function getAllTours()
    {
        // Get tours with schedule summary for the list view
        return DB::table($this->table)
            ->leftJoin('lichkhoihanh', 'tour.tourid', '=', 'lichkhoihanh.tourid')
            ->select(
                'tour.tourid',
                'tour.tentour',
                'tour.mota',
                'tour.diemkhoihanh',
                'tour.diadiemden',
                'tour.khuvuc',
                'tour.gianguoilon',
                'tour.giatreem',
                'tour.songay',
                'tour.songuoitoida',
                'tour.trangthai',
                'tour.ngaytao',
                'tour.ngaycapnhat',
                DB::raw('COUNT(DISTINCT lichkhoihanh.lichid) as solich'),
                DB::raw("GROUP_CONCAT(DISTINCT CONCAT(DATE_FORMAT(lichkhoihanh.ngaybatdau, '%d/%m/%Y'), ' - ', DATE_FORMAT(lichkhoihanh.ngayketthuc, '%d/%m/%Y'), ' (', lichkhoihanh.sochocon, ' chỗ)') ORDER BY lichkhoihanh.ngaybatdau ASC SEPARATOR '<br>') as lichtrinh_list")
            )
            ->groupBy('tour.tourid', 'tour.tentour', 'tour.mota', 'tour.diemkhoihanh', 'tour.diadiemden', 'tour.khuvuc', 'tour.gianguoilon', 'tour.giatreem', 'tour.songay', 'tour.songuoitoida', 'tour.trangthai', 'tour.ngaytao', 'tour.ngaycapnhat')
            ->orderBy('tour.tourid', 'DESC')
            ->get();
    }

    public function getTourWithDetails($tourId)
    {
        $tour = DB::table($this->table)->where('tourid', $tourId)->first();
        if (!$tour) return null;

        return (object) [
            'tour' => $tour,
            'images' => $this->getImages($tourId),
            'schedules' => $this->getSchedules($tourId),
            'itinerary' => $this->getTimeLine($tourId)
        ];
    }

    public function getSchedules($tourId)
    {
        return DB::table('lichkhoihanh')
            ->where('tourid', $tourId)
            ->orderBy('ngaybatdau', 'asc')
            ->get();
    }

    public function addSchedule($data)
    {
        return DB::table('lichkhoihanh')->insert($data);
    }

    public function deleteSchedule($lichId)
    {
        return DB::table('lichkhoihanh')->where('lichid', $lichId)->delete();
    }

    public function updateScheduleStatus($lichId, $data)
    {
        return DB::table('lichkhoihanh')->where('lichid', $lichId)->update($data);
    }

    public function deleteImage($hinhId)
    {
        return DB::table('hinhanhtour')->where('hinhid', $hinhId)->delete();
    }

    public function createTours($data)
    {
        return DB::table($this->table)->insertGetId($data);
    }

    public function uploadImages($data)
    {
        // Map field names to correct DB columns for hinhanhtour
        $mappedData = [
            'tourid' => $data['tourid'],
            'urlanh' => $data['urlanh'],
            'tenanh' => $data['tenanh'] ?? null,
            'motaanh' => $data['motaanh'] ?? null,
        ];
        return DB::table('hinhanhtour')->insert($mappedData);
    }

    public function uploadTempImages($data)
    {
        // Lưu ý: CSDL hiện tại chưa có bảng tbl_temp_images, bạn có thể cần tạo thêm 
        // bảng này (gồm cột: id, tourid, imagetempurl) để chứa ảnh nháp, hoặc bỏ qua hàm này.
        return DB::table('tbl_temp_images')->insert($data);
    }

    public function addTimeLine($data)
    {
        // Map field names to correct DB columns for lichtrinh
        $mappedData = [
            'tourid' => $data['tourid'],
            'tieude' => $data['tieude'],
            'noidung' => $data['noidung'] ?? null,
        ];
        return DB::table('lichtrinh')->insert($mappedData);
    }

    public function updateTour($tourId, $data)
    {
        return DB::table($this->table)
            ->where('tourid', $tourId)
            ->update($data);
    }

    public function deleteTour($tourId)
    {
        // Xóa tuần tự dữ liệu ở các bảng con trước
        $deleteTimeLine = DB::table('lichtrinh')->where('tourid', $tourId)->delete();
        $deleteImages = DB::table('hinhanhtour')->where('tourid', $tourId)->delete();
        $deleteLich = DB::table('lichkhoihanh')->where('tourid', $tourId)->delete();

        // Xóa tour ở bảng chính
        $deleteTour = DB::table($this->table)->where('tourid', $tourId)->delete();

        if ($deleteTour) {
            return ['success' => true, 'message' => 'Tour đã được xóa thành công.'];
        } else {
            return ['success' => false, 'message' => 'Không thể xóa tour.'];
        }
    }

    public function getTour($tourId)
    {
        return DB::table($this->table)->where('tourid', $tourId)->first();
    }

    public function getImages($tourId)
    {
        return DB::table('hinhanhtour')->where('tourid', $tourId)->get();
    }

    public function getTimeLine($tourId)
    {
        return DB::table('lichtrinh')->where('tourid', $tourId)->get();
    }

    public function deleteData($tourId, $tbl)
    {
        return DB::table($tbl)->where('tourid', $tourId)->delete();
    }
}