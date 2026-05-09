<?php

namespace App\Models\clients;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Tours extends Model
{
    use HasFactory;

    protected $table = 'tour';

    // Lấy tất cả tours (Có phân trang)
    public function getAllTours($perPage = 9)
    {
        $tours = $this->baseQuery()->paginate($perPage);

        foreach ($tours as $tour) {
            $this->enrichTour($tour);
        }

        return $tours;
    }

    // Lấy chi tiết 1 tour
    public function getTourDetail($id)
    {
        $tour = DB::table($this->table)
            ->where('tourid', $id)
            ->where('trangthai', 'hoat_dong') // Lấy tour đang hoạt động
            ->first();

        if (! $tour) {
            return null;
        }

        $this->mapTourAliases($tour);
        $tour->galleryImages = $this->getGalleryImages($tour->tourId, 5);
        $tour->images = $this->getImages($tour->tourId, 5);
        $tour->timeline = DB::table('lichtrinh')
            ->where('tourid', $tour->tourId)
            ->orderBy('ltid')
            ->get();
            
        // Chỉ lấy những lịch khởi hành từ hôm nay trở đi
        $tour->schedules = DB::table('lichkhoihanh')
            ->where('tourid', $tour->tourId)
            ->where('ngaybatdau', '>=', now()->format('Y-m-d'))
            ->orderBy('ngaybatdau')
            ->get();
        
        // Lấy thông tin ngày tháng từ lịch khởi hành gần nhất còn hạn
        $firstSchedule = $tour->schedules->whereIn('trangthai', ['con_cho', 'sap_dien_ra'])->first() ?? $tour->schedules->first();
        if ($firstSchedule) {
            $tour->startDate = $firstSchedule->ngaybatdau;
            $tour->endDate = $firstSchedule->ngayketthuc;
        }
        
        $tour->rating = $this->reviewStats($tour->tourId)->averageRating ?? 0;
        $tour->time = $this->formatDuration($tour->songay);
        $tour->domain = $tour->khuvuc; // Trả thẳng 'b', 't', 'n'

        return $tour;
    }

    // Lấy số lượng tour đếm theo miền (b/t/n)
    public function getDomain()
    {
        // Đếm số tour đang hoạt động và có lịch khởi hành tương ứng với từng khu vực
        $counts = DB::table($this->table . ' as t')
            ->join('lichkhoihanh as lkh', 't.tourid', '=', 'lkh.tourid')
            ->select('t.khuvuc', DB::raw('COUNT(DISTINCT t.tourid) as count'))
            ->where('t.trangthai', 'hoat_dong')
            ->whereIn('lkh.trangthai', ['con_cho', 'het_cho', 'sap_dien_ra'])
            ->where('lkh.ngaybatdau', '>=', now()->format('Y-m-d'))
            ->groupBy('t.khuvuc')
            ->pluck('count', 'khuvuc')
            ->toArray();

        return collect([
            (object) ['domain' => 'b', 'count' => $counts['b'] ?? 0],
            (object) ['domain' => 't', 'count' => $counts['t'] ?? 0],
            (object) ['domain' => 'n', 'count' => $counts['n'] ?? 0],
        ]);
    }

    // Bộ lọc tours (Bộ lọc giá, miền...)
    public function filterTours($filters = [], $sorting = null, $perPage = null)
    {
        $query = $this->baseQuery();

        foreach ($filters as $filter) {
            if (!is_array($filter) || count($filter) < 3) {
                continue;
            }

            [$field, $operator, $value] = $filter;

            if ($field === 'domain') {
                $this->applyDomainFilter($query, $value);
                continue;
            }

            if ($field === 'averageRating') {
                $query->havingRaw('ROUND(AVG(dg.sosao)) ' . $operator . ' ?', [$value]);
                continue;
            }

            if ($field === 'time') {
                $value = $this->normalizeDuration($value);
                if ($value === null) continue;
                $field = 'songay';
            }

            $column = $this->mapFilterField($field);
            $query->where($column, $operator, $value);
        }

        if (!empty($sorting) && isset($sorting[0], $sorting[1])) {
            $sortField = $this->mapSortField($sorting[0]);
            $query->orderBy($sortField, $sorting[1]);
        }

        $tours = $query->get();

        foreach ($tours as $tour) {
            $this->enrichTour($tour);
        }

        return $tours;
    }

    // Tìm kiếm chung (Thanh tìm kiếm)
    public function searchTours($data)
    {
        $query = $this->baseQuery();

        if (!empty($data['destination'])) {
            $query->where('t.diadiemden', 'LIKE', '%' . $data['destination'] . '%');
        }

        if (!empty($data['startDate'])) {
            $query->having('startDate', '>=', $data['startDate']);
        }
        if (!empty($data['endDate'])) {
            $query->having('startDate', '<=', $data['endDate']);
        }

        if (!empty($data['keyword'])) {
            $keyword = $data['keyword'];
            $query->where(function ($inner) use ($keyword) {
                $inner->where('t.tentour', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('t.mota', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('t.diadiemden', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('t.diemkhoihanh', 'LIKE', '%' . $keyword . '%');
            });
        }

        $tours = $query->limit(12)->get();

        foreach ($tours as $tour) {
            $this->enrichTour($tour);
        }

        return $tours;
    }

    // Các Tour phổ biến (Nhiều lượt đặt)
    public function toursPopular($quantity)
    {
        $tours = DB::table($this->table . ' as t')
            ->join('lichkhoihanh as lkh', 't.tourid', '=', 'lkh.tourid')
            ->leftJoin('dattour as dt', function ($join) {
                $join->on('t.tourid', '=', 'dt.tourid')
                    ->whereIn('dt.trangthai', ['da_xac_nhan', 'da_thanh_toan']);
            })
            ->leftJoin('danhgia as dg', function ($join) {
                $join->on('t.tourid', '=', 'dg.tourid')
                    ->where('dg.trangthai', '=', 'da_duyet');
            })
            ->select(
                't.tourid as tourId',
                't.tentour as title',
                't.mota as description',
                't.diemkhoihanh as departure',
                't.diadiemden as destination',
                't.khuvuc as area',
                't.gianguoilon as priceAdult',
                't.giatreem as priceChild',
                't.songay',
                't.songuoitoida as quantity',
                't.trangthai',
                DB::raw('AVG(dg.sosao) as averageRating'),
                DB::raw('COUNT(DISTINCT dt.dtid) as totalBookings'),
                DB::raw('MIN(lkh.ngaybatdau) as startDate')
            )
            ->where('t.trangthai', 'hoat_dong')
            ->whereIn('lkh.trangthai', ['con_cho', 'het_cho', 'sap_dien_ra'])
            ->where('lkh.ngaybatdau', '>=', now()->format('Y-m-d'))
            ->groupBy(
                't.tourid', 't.tentour', 't.mota', 't.diemkhoihanh', 't.diadiemden',
                't.khuvuc', 't.gianguoilon', 't.giatreem', 't.songay', 't.songuoitoida', 't.trangthai'
            )
            ->orderByDesc('totalBookings')
            ->take($quantity)
            ->get();

        foreach ($tours as $tour) {
            $this->enrichTour($tour);
        }

        return $tours;
    }

    // -------------- CÁC HÀM CỐT LÕI (CORE QUERY) --------------

    private function baseQuery()
    {
        return DB::table($this->table . ' as t')
            ->join('lichkhoihanh as lkh', 't.tourid', '=', 'lkh.tourid')
            ->leftJoin('danhgia as dg', function ($join) {
                $join->on('t.tourid', '=', 'dg.tourid')
                    ->where('dg.trangthai', '=', 'da_duyet');
            })
            ->select(
                't.tourid as tourId',
                't.tentour as title',
                't.mota as description',
                't.diemkhoihanh as departure',
                't.diadiemden as destination',
                't.khuvuc as area',
                't.gianguoilon as priceAdult',
                't.giatreem as priceChild',
                't.songay',
                't.songuoitoida as quantity',
                't.trangthai',
                DB::raw('AVG(dg.sosao) as averageRating'),
                DB::raw('MIN(lkh.ngaybatdau) as startDate') // Lấy ngày đi gần nhất
            )
            ->where('t.trangthai', 'hoat_dong') // Phải là tour đang hoạt động
            ->whereIn('lkh.trangthai', ['con_cho', 'het_cho', 'sap_dien_ra'])
            ->where('lkh.ngaybatdau', '>=', now()->format('Y-m-d')) // Phải còn hạn
            ->groupBy(
                't.tourid', 't.tentour', 't.mota', 't.diemkhoihanh', 't.diadiemden',
                't.khuvuc', 't.gianguoilon', 't.giatreem', 't.songay', 't.songuoitoida', 't.trangthai'
            );
    }

    private function enrichTour($tour)
    {
        $this->mapTourAliases($tour);
        // Tối ưu hóa: Chỉ lấy 1 ảnh đầu tiên làm thumbnail cho danh sách
        $firstImage = DB::table('hinhanhtour')
                        ->where('tourid', $tour->tourId)
                        ->orderBy('thutuhienthi')
                        ->first();
        $tour->images = $firstImage ? [$firstImage->urlanh] : [];
        $tour->rating = isset($tour->averageRating) ? round((float) $tour->averageRating, 1) : 0;
        $tour->time = $this->formatDuration($tour->songay);
        $tour->domain = $tour->area ?? $tour->khuvuc ?? '';
    }

    private function applyDomainFilter($query, $domain)
    {
        // Vì trong DB lưu thẳng là 'b', 't', 'n' nên chỉ cần Where là xong, ko cần MAP dài dòng
        $query->where('t.khuvuc', $domain);
    }

    private function mapTourAliases($tour)
    {
        if (isset($tour->tourid)) $tour->tourId = $tour->tourid;
        if (isset($tour->tentour)) $tour->title = $tour->tentour;
        if (isset($tour->mota)) $tour->description = $tour->mota;
        if (isset($tour->diadiemden)) $tour->destination = $tour->diadiemden;
        if (isset($tour->diemkhoihanh)) $tour->departure = $tour->diemkhoihanh;
        if (isset($tour->gianguoilon)) $tour->priceAdult = $tour->gianguoilon;
        if (isset($tour->giatreem)) $tour->priceChild = $tour->giatreem;
        if (isset($tour->songuoitoida)) $tour->quantity = $tour->songuoitoida;
        if (isset($tour->khuvuc)) $tour->area = $tour->khuvuc;
    }

    private function getImages($tourId, $limit = null)
    {
        $query = DB::table('hinhanhtour')->where('tourid', $tourId)->orderBy('thutuhienthi');
        if ($limit) $query->limit($limit);
        return $query->pluck('urlanh');
    }

    private function getGalleryImages($tourId, $limit = null)
    {
        $query = DB::table('hinhanhtour')->where('tourid', $tourId)->orderBy('thutuhienthi');
        if ($limit) $query->limit($limit);
        return $query->get(['urlanh', 'motaanh', 'tenanh']);
    }

    private function formatDuration($days)
    {
        $days = (int) $days;
        if ($days <= 1) return '1 ngày';
        return $days . ' ngày ' . max(1, $days - 1) . ' đêm';
    }

    private function normalizeDuration($value)
    {
        if (is_numeric($value)) return (int) $value;
        if (preg_match('/(\d+)/', (string) $value, $matches)) return (int) $matches[1];
        return null;
    }

    private function mapFilterField($field)
    {
        return match ($field) {
            'priceAdult' => 't.gianguoilon',
            'priceChild' => 't.giatreem',
            'songay' => 't.songay',
            'tourId' => 't.tourid',
            default => $field,
        };
    }

    private function mapSortField($field)
    {
        return match ($field) {
            'tourId' => 't.tourid',
            'priceAdult' => 't.gianguoilon',
            'priceChild' => 't.giatreem',
            default => $field,
        };
    }

    // -------------- CÁC HÀM CÒN LẠI GIỮ NGUYÊN --------------

    public function updateTours($tourId, $data)
    {
        return DB::table($this->table)->where('tourid', $tourId)->update($data);
    }

    public function toursRecommendation($ids)
    {
        if (empty($ids)) return collect();
        $tours = $this->baseQuery()
            ->whereIn('t.tourid', $ids)
            ->orderByRaw("FIELD(t.tourid, " . implode(',', array_map('intval', $ids)) . ")")
            ->get();
        foreach ($tours as $tour) $this->enrichTour($tour);
        return $tours;
    }

    public function toursSearch($ids)
    {
        if (empty($ids)) return collect();
        $tours = $this->baseQuery()
            ->whereIn('t.tourid', $ids)
            ->orderByRaw("FIELD(t.tourid, " . implode(',', array_map('intval', $ids)) . ")")
            ->get();
        foreach ($tours as $tour) $this->enrichTour($tour);
        return $tours;
    }

    public function tourBooked($bookingId, $checkoutId)
    {
        return DB::table($this->table . ' as t')
            ->join('dattour as dt', 't.tourid', '=', 'dt.tourid')
            ->leftJoin('thanhtoan as tt', 'dt.dtid', '=', 'tt.dtid')
            ->where('dt.dtid', $bookingId)
            ->where('tt.ttid', $checkoutId)
            ->select('t.*', 'dt.*', 'tt.*')
            ->first();
    }

    public function createReviews($data) { return DB::table('danhgia')->insert($data); }

    public function getReviews($id)
    {
        return DB::table('danhgia as dg')
            ->join('nguoidung as nd', 'nd.ndid', '=', 'dg.ndid')
            ->where('dg.tourid', $id)
            ->orderByDesc('dg.ngaytao')
            ->take(3)
            ->get();
    }

    public function reviewStats($id)
    {
        return DB::table('danhgia')
            ->where('tourid', $id)
            ->where('trangthai', 'da_duyet')
            ->selectRaw('AVG(sosao) as averageRating, COUNT(*) as reviewCount')
            ->first();
    }

    public function checkReviewExist($tourId, $userId)
    {
        return DB::table('danhgia')
            ->where('tourid', $tourId)
            ->where('ndid', $userId)
            ->exists();
    }
}