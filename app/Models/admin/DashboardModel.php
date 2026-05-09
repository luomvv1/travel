<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DashboardModel extends Model
{
    public function getSummary()
    {
        $tourWorking = DB::table('tour')
            ->whereIn('trangthai', ['con_cho', 'het_cho'])
            ->count();

        $countBooking = DB::table('dattour')->count();

        $countUser = DB::table('nguoidung')
            ->where('vaitro', 'khach_hang')
            ->where('trangthai', 'hoat_dong')
            ->count();

        $totalAmount = DB::table('thanhtoan')
            ->where('trangthai', 'thanh_cong')
            ->sum('sotien');

        return [
            'tourWorking' => $tourWorking,
            'countBooking' => $countBooking,
            'countUser' => $countUser,
            'totalAmount' => $totalAmount,
        ];
    }

    public function getValueDomain()
    {
        $counts = ['b' => 0, 't' => 0, 'n' => 0];

        $areas = DB::table('tour')
            ->select('khuvuc')
            ->whereIn('trangthai', ['con_cho', 'het_cho'])
            ->get();

        foreach ($areas as $area) {
            $region = $this->regionFromArea($area->khuvuc ?? '');

            if ($region && isset($counts[$region])) {
                $counts[$region]++;
            }
        }

        return $counts;
    }

    public function getValuePayment()
    {
        return DB::table('thanhtoan')
            ->select('phuongthuc as paymentMethod', DB::raw('COUNT(*) as count'))
            ->groupBy('phuongthuc')
            ->get()
            ->toArray();
    }

    public function getMostTourBooked()
    {
        return DB::table('tour')
            ->leftJoin('dattour', function ($join) {
                $join->on('tour.tourid', '=', 'dattour.tourid')
                    ->where(function ($query) {
                        $query->whereNull('dattour.trangthai')
                            ->orWhere('dattour.trangthai', '!=', 'da_huy');
                    });
            })
            ->select(
                'tour.tourid as tourId',
                'tour.tentour as title',
                'tour.songuoitoida as quantity',
                DB::raw('COALESCE(SUM(dattour.songuoilon + dattour.sotreem), 0) as booked_quantity')
            )
            ->groupBy('tour.tourid', 'tour.tentour', 'tour.songuoitoida')
            ->orderByDesc(DB::raw('COALESCE(SUM(dattour.songuoilon + dattour.sotreem), 0)'))
            ->take(3)
            ->get();
    }

    public function getNewBooking()
    {
        return DB::table('dattour')
            ->join('tour', 'dattour.tourid', '=', 'tour.tourid')
            ->join('nguoidung', 'dattour.ndid', '=', 'nguoidung.ndid')
            ->orderByDesc('dattour.ngaytao')
            ->select(
                'dattour.dtid as bookingId',
                'nguoidung.hoten as fullName',
                'tour.tentour as tour_name',
                'dattour.giacuoi as totalPrice',
                'dattour.trangthai as bookingStatus',
                'dattour.ngaytao as bookingDate'
            )
            ->take(3)
            ->get();

    }

    public function getRevenuePerMonth()
    {
        $monthlyRevenue = DB::table('thanhtoan')
            ->select(DB::raw('MONTH(ngaythanhtoan) as month, SUM(sotien) as revenue'))
            ->where('trangthai', 'thanh_cong')
            ->whereNotNull('ngaythanhtoan')
            ->groupBy(DB::raw('MONTH(ngaythanhtoan)'))
            ->orderBy('month', 'asc')
            ->get();

        $revenueData = array_fill(0, 12, 0);

        foreach ($monthlyRevenue as $data) {
            if ($data->month) {
                $revenueData[$data->month - 1] = (float) $data->revenue;
            }
        }

        return $revenueData;
    }

    private function regionFromArea(string $area): ?string
    {
        $normalized = $this->normalizeArea($area);

        $north = [
            'ha noi', 'ha giang', 'cao bang', 'bac kan', 'tuyen quang', 'lao cai', 'dien bien', 'lai chau',
            'son la', 'yen bai', 'hoa binh', 'thai nguyen', 'lang son', 'quang ninh', 'bac giang', 'phu tho',
            'vinh phuc', 'bac ninh', 'hai duong', 'hai phong', 'hung yen', 'thai binh', 'ha nam', 'nam dinh',
            'ninh binh',
        ];

        $central = [
            'thanh hoa', 'nghe an', 'ha tinh', 'quang binh', 'quang tri', 'thua thien hue', 'hue',
            'da nang', 'quang nam', 'quang ngai', 'binh dinh', 'phu yen', 'khanh hoa', 'ninh thuan',
            'binh thuan', 'kon tum', 'gia lai', 'dak lak', 'dak nong', 'lam dong',
        ];

        $south = [
            'binh phuoc', 'tay ninh', 'binh duong', 'dong nai', 'ba ria - vung tau', 'ba ria vung tau',
            'ho chi minh', 'tp ho chi minh', 'sai gon', 'long an', 'tien giang', 'ben tre', 'tra vinh',
            'vinh long', 'dong thap', 'an giang', 'kien giang', 'can tho', 'hau giang', 'soc trang',
            'bac lieu', 'ca mau',
        ];

        if (in_array($normalized, $north, true)) {
            return 'b';
        }

        if (in_array($normalized, $central, true)) {
            return 't';
        }

        if (in_array($normalized, $south, true)) {
            return 'n';
        }

        return null;
    }

    private function normalizeArea(string $value): string
    {
        $value = trim($value);
        $value = str_replace(['Đ', 'đ'], ['D', 'd'], $value);
        $value = mb_strtolower($value, 'UTF-8');
        $value = preg_replace('/\s+/', ' ', $value) ?? $value;

        return $value;
    }

    public function getBookingDetails()
    {
        return DB::table('dattour as dt')
            ->join('tour as t', 'dt.tourid', '=', 't.tourid')
            ->join('lichkhoihanh as lkh', 'dt.lichid', '=', 'lkh.lichid')
            ->join('nguoidung as nd', 'dt.ndid', '=', 'nd.ndid')
            ->leftJoin('thanhtoan as tt', 'dt.dtid', '=', 'tt.dtid')
            ->orderByDesc('dt.ngaydat')
            ->select(
                'dt.dtid as booking_id',
                'nd.hoten as customer_name',
                't.tentour as tour_name',
                'lkh.ngaybatdau as tour_date',
                DB::raw('dt.songuoilon + dt.sotreem as total_people'),
                'dt.giacuoi as total_price',
                'dt.trangthai as booking_status',
                'tt.trangthai as payment_status',
                'tt.phuongthuc as payment_method'
            )
            ->take(10)
            ->get();
    }

    public function getRevenueByTour()
    {
        return DB::table('thanhtoan as tt')
            ->join('dattour as dt', 'tt.dtid', '=', 'dt.dtid')
            ->join('tour as t', 'dt.tourid', '=', 't.tourid')
            ->where('tt.trangthai', 'thanh_cong')
            ->select(
                't.tourid as tour_id',
                't.tentour as tour_name',
                DB::raw('COUNT(DISTINCT dt.dtid) as booking_count'),
                DB::raw('SUM(tt.sotien) as total_revenue')
            )
            ->groupBy('t.tourid', 't.tentour')
            ->orderByDesc(DB::raw('SUM(tt.sotien)'))
            ->take(5)
            ->get();
    }

    public function getScheduleAvailability()
    {
        return DB::table('lichkhoihanh as lkh')
            ->join('tour as t', 'lkh.tourid', '=', 't.tourid')
            ->where('lkh.trangthai', 'con_cho')
            ->select(
                'lkh.lichid as schedule_id',
                't.tentour as tour_name',
                'lkh.ngaybatdau as start_date',
                'lkh.ngayketthuc as end_date',
                'lkh.sochocon as available_slots',
                'lkh.trangthai as status'
            )
            ->orderBy('lkh.ngaybatdau', 'asc')
            ->take(8)
            ->get();
    }

    public function getVnpayTransactions()
    {
        return DB::table('thanhtoan as tt')
            ->join('dattour as dt', 'tt.dtid', '=', 'dt.dtid')
            ->where('tt.phuongthuc', 'vnpay')
            ->orderByDesc('tt.ngaythanhtoan')
            ->select(
                'tt.magiaodich as transaction_ref',
                'tt.vnpay_transaction_no as vnpay_id',
                'tt.sotien as amount',
                'tt.trangthai as status',
                'tt.vnpay_bank_code as bank_code',
                'tt.vnpay_pay_date as pay_date',
                'dt.dtid as booking_id'
            )
            ->take(8)
            ->get();
    }
}

