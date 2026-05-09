<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use App\Models\clients\Booking;
use App\Models\clients\Tours;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BookingController extends Controller
{
    private Tours $tour;
    private Booking $booking;

    public function __construct()
    {
        $this->tour = new Tours();
        $this->booking = new Booking();
    }

    public function index($id)
    {
        if (!session('ndid')) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để đặt tour');
        }

        $title = 'Đặt Tour';
        $tour = $this->tour->getTourDetail($id);

        if (!$tour) {
            return redirect()->route('tours')->with('error', 'Tour không tồn tại');
        }

        $selectedLichid = request()->query('lichid');

        $user = DB::table('nguoidung')
            ->where('ndid', session('ndid'))
            ->first();

        return view('clients.booking', compact('title', 'tour', 'selectedLichid', 'user'));
    }

    public function createBooking(Request $request, $id)
    {
        if (!session('ndid')) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để đặt tour');
        }

        $request->validate([
            'tourId' => 'required|integer',
            'lichid' => 'required|integer',
            'numAdults' => 'required|integer|min:1',
            'numChildren' => 'nullable|integer|min:0',
            'payment' => 'required|in:tai_van_phong,vnpay',
        ], [
            'lichid.required' => 'Vui lòng chọn lịch khởi hành',
            'numAdults.min' => 'Phải có ít nhất 1 người lớn',
            'payment.required' => 'Vui lòng chọn phương thức thanh toán',
        ]);

        if ((int) $request->tourId !== (int) $id) {
            return redirect()->back()->with('error', 'Tour không hợp lệ');
        }

        $tour = DB::table('tour')->where('tourid', $id)->first();
        if (!$tour) {
            return redirect()->route('tours')->with('error', 'Tour không tồn tại');
        }

        $schedule = DB::table('lichkhoihanh')
            ->where('lichid', $request->lichid)
            ->where('tourid', $id)
            ->first();

        if (!$schedule) {
            return redirect()->back()->with('error', 'Lịch khởi hành không tồn tại');
        }

        $numAdults = (int) $request->numAdults;
        $numChildren = (int) ($request->numChildren ?? 0);
        $totalGuests = $numAdults + $numChildren;

        if ((int) $schedule->sochocon < $totalGuests) {
            return redirect()->back()->with('error', 'Không đủ chỗ trống cho số lượng khách đã chọn');
        }

        $tongtien = ($numAdults * (float) $tour->gianguoilon) + ($numChildren * (float) $tour->giatreem);
        $giamgia = 0;
        $makhuyenmai = null;
        $now = Carbon::now('Asia/Ho_Chi_Minh'); // Explicitly use Vietnam timezone

        if ($request->filled('promo')) {
            $promo = DB::table('khuyenmai')
                ->where('macode', $request->promo)
                ->where('danghoatdong', 'Y')
                ->whereDate('ngaybatdau', '<=', now())
                ->whereDate('ngayketthuc', '>=', now())
                ->first();

            if (!$promo) {
                return redirect()->back()->with('error', 'Mã khuyến mãi không hợp lệ hoặc đã hết hạn');
            }

            $giamgia = $promo->loaigiam === 'phan_tram'
                ? $tongtien * ((float) $promo->giatri / 100)
                : (float) $promo->giatri;
            $giamgia = min($giamgia, $tongtien);
            $makhuyenmai = $request->promo;
        }

        $giacuoi = $tongtien - $giamgia;

        try {
            DB::beginTransaction();

            $orderId = 'BOOK' . $id . $request->lichid . time();

            $dtid = DB::table('dattour')->insertGetId([
                'ndid' => session('ndid'),
                'tourid' => $id,
                'lichid' => $request->lichid,
                'ngaydat' => $now->toDateString(), // Only store the date part
                'songuoilon' => $numAdults,
                'sotreem' => $numChildren,
                'tongtien' => $tongtien,
                'giamgia' => $giamgia,
                'giacuoi' => $giacuoi,
                'makhuyenmai' => $makhuyenmai,
                'trangthai' => 'cho_xac_nhan',
            ]);

            DB::table('thanhtoan')->insert([
                'dtid' => $dtid,
                'phuongthuc' => $request->payment,
                'sotien' => $giacuoi,
                'magiaodich' => $orderId,
                'trangthai' => 'cho_xu_ly',
                'ngaythanhtoan' => null,
            ]);

            DB::table('lichkhoihanh')
                ->where('lichid', $request->lichid)
                ->decrement('sochocon', $totalGuests);

            // Bắt buộc phải commit để lưu vào database trước khi nhảy sang VNPAY
            DB::commit();

            // =======================================================
            // XỬ LÝ VNPAY GIỐNG HỆT VIDEO HƯỚNG DẪN
            // =======================================================
            if ($request->payment === 'vnpay') {
                $vnp_Url = env('VNPAY_URL', "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html");
                $vnp_Returnurl = env('VNPAY_RETURN_URL', route('vnpay.callback'));
                $vnp_TmnCode = env('VNPAY_TMN_CODE'); // Mã website tại VNPAY 
                $vnp_HashSecret = env('VNPAY_HASH_SECRET'); // Chuỗi bí mật

                $vnp_TxnRef = $orderId; // Mã đơn hàng (giống $code_cart trong hướng dẫn)
                $vnp_OrderInfo = 'Thanh toan don hang tour #' . $dtid;
                $vnp_OrderType = 'billpayment';
                $vnp_Amount = (int) round($giacuoi * 100);
                $vnp_Locale = 'vn';
                $vnp_IpAddr = $request->ip(); // Lấy IP chuẩn của Laravel

                $inputData = array(
                    "vnp_Version" => "2.1.0",
                    "vnp_TmnCode" => $vnp_TmnCode,
                    "vnp_Amount" => $vnp_Amount,
                    "vnp_Command" => "pay",
                    "vnp_CreateDate" => date('YmdHis'),
                    "vnp_CurrCode" => "VND",
                    "vnp_IpAddr" => $vnp_IpAddr,
                    "vnp_Locale" => $vnp_Locale,
                    "vnp_OrderInfo" => $vnp_OrderInfo,
                    "vnp_OrderType" => $vnp_OrderType,
                    "vnp_ReturnUrl" => $vnp_Returnurl,
                    "vnp_TxnRef" => $vnp_TxnRef,
                );

                // Code tạo Hash giống hệt hướng dẫn
                ksort($inputData);
                $query = "";
                $i = 0;
                $hashdata = "";
                foreach ($inputData as $key => $value) {
                    if ($i == 1) {
                        $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                    } else {
                        $hashdata .= urlencode($key) . "=" . urlencode($value);
                        $i = 1;
                    }
                    $query .= urlencode($key) . "=" . urlencode($value) . '&';
                }

                $vnp_Url = $vnp_Url . "?" . $query;
                if (isset($vnp_HashSecret)) {
                    $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
                    $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
                }

                // Dùng hàm chuyển hướng của Laravel thay cho header('Location: ...'); die();
                return redirect()->away($vnp_Url);
            }

            // Dành cho thanh toán tại văn phòng
            return redirect()->route('tour-booked', ['id' => $dtid])->with('success', 'Đặt tour thành công');

        } catch (\Exception $e) {
            DB::rollBack();
            dd('Lỗi MySQL không lưu được dữ liệu:', $e->getMessage(), 'Dòng lỗi:', $e->getLine());
        }
    }

    public function showBooked($id)
    {
        if (!session('ndid')) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập');
        }

        // Tự động kiểm tra nếu là VNPAY chưa thanh toán mà quá 15 phút -> Hủy
        $bookingCheck = DB::table('dattour as dt')
            ->join('thanhtoan as tt', 'dt.dtid', '=', 'tt.dtid')
            ->where('dt.dtid', $id)
            ->where('dt.ndid', session('ndid'))
            ->where('tt.phuongthuc', 'vnpay')
            ->whereIn('dt.trangthai', ['cho_xac_nhan', 'da_xac_nhan'])
            ->where('tt.trangthai', 'cho_xu_ly')
            ->select('dt.*')
            ->first();

        if ($bookingCheck) {
            if (\Carbon\Carbon::parse($bookingCheck->ngaytao)->setTimezone('Asia/Ho_Chi_Minh')->addMinutes(15)->isPast()) {
                DB::beginTransaction();
                try {
                    $return_quantity = (int) $bookingCheck->songuoilon + (int) $bookingCheck->sotreem;
                    DB::table('lichkhoihanh')->where('lichid', $bookingCheck->lichid)->increment('sochocon', $return_quantity);
                    DB::table('dattour')->where('dtid', $id)->update(['trangthai' => 'da_huy']);
                    DB::table('thanhtoan')->where('dtid', $id)->update(['trangthai' => 'that_bai']);
                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                }
            }
        }

        $booking = DB::table('dattour as dt')
            ->join('tour as t', 'dt.tourid', '=', 't.tourid')
            ->leftJoin('lichkhoihanh as lkh', 'dt.lichid', '=', 'lkh.lichid')
            ->leftJoin('thanhtoan as tt', 'dt.dtid', '=', 'tt.dtid')
            ->leftJoin('nguoidung as nd', 'dt.ndid', '=', 'nd.ndid')
            ->where('dt.dtid', $id)
            ->where('dt.ndid', session('ndid'))
            ->select('dt.*', 't.tentour', 't.diadiemden', 't.diemkhoihanh', 'lkh.ngaybatdau', 'lkh.ngayketthuc', 'tt.ttid', 'tt.phuongthuc', 'tt.sotien as sotien_thanhtoan', 'tt.trangthai as trangthai_thanhtoan', 'tt.ngaythanhtoan', 'nd.hoten as fullName', 'nd.email as email', 'nd.sodienthoai as tel', 'nd.diachi as address')
            ->first();

        // Tính toán hết hạn: nếu VNPAY chưa thanh toán, tính từ ngaytao
        $hetHan = null;
        $conHan = false;
        if ($booking && $booking->phuongthuc === 'vnpay' && in_array($booking->trangthai_thanhtoan, ['cho_xu_ly', null])) {
            $hetHan = \Carbon\Carbon::parse($booking->ngaytao)->setTimezone('Asia/Ho_Chi_Minh')->addMinutes(15);
            $conHan = \Carbon\Carbon::now('Asia/Ho_Chi_Minh')->lessThan($hetHan);
        }

        if (!$booking) {
            return redirect()->route('my-tours')->with('error', 'Không tìm thấy đơn đặt tour');
        }

        $title = 'Xác nhận đặt tour';

        return view('clients.tour-booked-new', compact('title', 'booking', 'hetHan', 'conHan'));
    }

    public function checkBooking(Request $req)
    {
        $tourId = $req->tourId;
        $userId = session('ndid');
        
        if (!$userId) return response()->json(['success' => false]);

        $check = $this->booking->checkBooking($tourId, $userId);
        return response()->json(['success' => $check]);
    }

    public function checkPromo(Request $request)
    {
        $promoCode = trim((string) $request->input('promo'));

        if ($promoCode === '') {
            return response()->json(['success' => false, 'message' => 'Vui lòng nhập mã khuyến mãi']);
        }

        $promo = DB::table('khuyenmai')
            ->where('macode', $promoCode)
            ->where('danghoatdong', 'Y')
            ->whereDate('ngaybatdau', '<=', now())
            ->whereDate('ngayketthuc', '>=', now())
            ->first();

        if (!$promo) {
            return response()->json(['success' => false, 'message' => 'Mã khuyến mãi không hợp lệ hoặc đã hết hạn']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Mã khuyến mãi hợp lệ',
            'loaigiam' => $promo->loaigiam,
            'giatri' => $promo->giatri,
        ]);
    }
}