<?php

namespace App\Models\admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class BookingModel extends Model
{
    use HasFactory;

    protected $table = 'dattour';

    public function getBooking()
    {
        $list_booking = DB::table($this->table . ' as dt')
            ->join('tour as t', 't.tourid', '=', 'dt.tourid')
            ->join('nguoidung as nd', 'nd.ndid', '=', 'dt.ndid')
            ->leftJoin('lichkhoihanh as lkh', 'lkh.lichid', '=', 'dt.lichid')
            ->leftJoin('thanhtoan as tt', 'tt.dtid', '=', 'dt.dtid')
            ->select(
                'dt.dtid as bookingId',
                't.tentour as title',
                'nd.hoten as fullName',
                'nd.email as email',
                'nd.sodienthoai as phoneNumber',
                'nd.diachi as address',
                'dt.ngaydat as bookingDate',
                'dt.songuoilon as numAdults',
                'dt.sotreem as numChildren',
                'dt.giacuoi as totalPrice',
                'dt.trangthai as bookingStatus',
                'tt.phuongthuc as paymentMethod',
                'tt.trangthai as paymentStatus',
                'lkh.ngayketthuc as endDate'
            )
            ->orderByDesc('dt.ngaydat')
            ->get();

        return $list_booking;
    }

    public function updateBooking($bookingId, $data)
    {
        return DB::table($this->table)
            ->where('dtid', $bookingId)
            ->update($data);
    }

    public function getInvoiceBooking($bookingId)
    {
        $invoice = DB::table($this->table . ' as dt')
            ->join('tour as t', 't.tourid', '=', 'dt.tourid')
            ->join('nguoidung as nd', 'nd.ndid', '=', 'dt.ndid')
            ->leftJoin('thanhtoan as tt', 'tt.dtid', '=', 'dt.dtid')
            ->select(
                'dt.*', 't.*', 'nd.*', 'tt.*',
                'dt.dtid as bookingId',
                'nd.hoten as fullName',
                'nd.email as email',
                'tt.magiaodich as transactionId',
                'tt.trangthai as paymentStatus'
            )
            ->where('dt.dtid', $bookingId)
            ->first();

        return $invoice;
    }

    public function updateCheckout($bookingId, $data)
    {
        return DB::table('thanhtoan')
            ->where('dtid', $bookingId)
            ->update($data);
    }
}