<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use App\Models\clients\Booking;
use App\Models\clients\Tours;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TourBookedController extends Controller
{
    private $tour;
    private $booking;

    public function __construct()
    {
        $this->tour = new Tours();
        $this->booking = new Booking();
    }

    public function index(Request $req)
    {
        $title = "Tour đã đặt";

        $bookingId = $req->input('bookingId');
        $checkoutId = $req->input('checkoutId');
        $tour_booked = $this->tour->tourBooked($bookingId, $checkoutId);

        if ($tour_booked && $tour_booked->startDate) {
            $today = Carbon::now();
            $startDate = Carbon::parse($tour_booked->startDate);
            $diffInDays = $startDate->diffInDays($today);
            $hide = $diffInDays < 7 ? 'hide' : '';
        } else {
            $hide = '';
        }

        return view("clients.tour-booked", compact('title', 'tour_booked', 'hide', 'bookingId'));
    }

    // HÀM HỦY TOUR VÀ TRẢ LẠI CHỖ
    public function cancelBooking(Request $req)
    {
        $bookingId = $req->bookingId;

        // Lấy thông tin đơn hàng và thanh toán
        $booking = DB::table('dattour as dt')
            ->leftJoin('thanhtoan as tt', 'dt.dtid', '=', 'tt.dtid')
            ->where('dt.dtid', $bookingId)
            ->select('dt.*', 'tt.trangthai as trangthai_thanhtoan')
            ->first();

        if (!$booking) {
            session()->flash('error', 'Không tìm thấy đơn đặt tour!');
            return redirect()->back();
        }

        // Nếu đã hủy thì không cho hủy nữa
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

        // Bắt đầu giao dịch Database an toàn
        DB::beginTransaction();
        try {
            // 1. Tính số lượng khách để hoàn trả (Người lớn + Trẻ em)
            $return_quantity = (int) $booking->songuoilon + (int) $booking->sotreem;

            // 2. Trả lại chỗ cho lịch khởi hành tương ứng
            DB::table('lichkhoihanh')
                ->where('lichid', $booking->lichid)
                ->increment('sochocon', $return_quantity);

            // 3. Cập nhật trạng thái đơn đặt tour thành 'da_huy'
            DB::table('dattour')
                ->where('dtid', $bookingId)
                ->update(['trangthai' => 'da_huy']);

            // 4. Cập nhật bảng thanh toán theo trạng thái thực tế
            DB::table('thanhtoan')
                ->where('dtid', $bookingId)
                ->update(['trangthai' => $newPaymentStatus]);

            DB::commit();
            session()->flash('success', $flashMessage);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Lỗi khi hủy tour: ' . $e->getMessage());
        }

        // Quay trở lại trang hiện tại
        return redirect()->back();
    }
}