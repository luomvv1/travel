<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TourBookedController extends Controller
{
    public function cancelBooking(Request $req)
    {
        $bookingId = $req->bookingId;

        $booking = DB::table('dattour as dt')
            ->leftJoin('thanhtoan as tt', 'dt.dtid', '=', 'tt.dtid')
            ->where('dt.dtid', $bookingId)
            ->select('dt.*', 'tt.trangthai as trangthai_thanhtoan')
            ->first();

        if (! $booking) {
            session()->flash('error', 'Không tìm thấy đơn đặt tour!');
            return redirect()->back();
        }

        if ($booking->trangthai == 'da_huy') {
            session()->flash('warning', 'Đơn này đã được hủy trước đó!');
            return redirect()->back();
        }

        $paymentStatus = $booking->trangthai_thanhtoan ?? null;
        $newPaymentStatus = $paymentStatus === 'thanh_cong'
            ? 'cho_hoan_tien'
            : 'that_bai';
        $flashMessage = $paymentStatus === 'thanh_cong'
            ? 'Đã hủy đơn và chuyển yêu cầu hoàn tiền cho thanh toán.'
            : 'Đã hủy thanh toán và đặt tour. Số chỗ đã được hoàn trả!';

        DB::beginTransaction();
        try {
            $returnQuantity = (int) $booking->songuoilon + (int) $booking->sotreem;

            DB::table('lichkhoihanh')
                ->where('lichid', $booking->lichid)
                ->increment('sochocon', $returnQuantity);

            DB::table('dattour')
                ->where('dtid', $bookingId)
                ->update(['trangthai' => 'da_huy']);

            DB::table('thanhtoan')
                ->where('dtid', $bookingId)
                ->update(['trangthai' => $newPaymentStatus]);

            DB::commit();
            session()->flash('success', $flashMessage);
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Lỗi khi hủy tour: ' . $e->getMessage());
        }

        return redirect()->back();
    }
}
