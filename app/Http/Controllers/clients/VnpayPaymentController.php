<?php

namespace App\Http\Controllers\clients;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * VNPAY Payment Controller
 * Xử lý thanh toán VNPAY đơn giản
 */
class VnpayPaymentController extends Controller
{
    /**
     * Thanh toán VNPAY
     * POST /vnpay-payment
     */
    public function payment(Request $request)
    {
        if (!session('ndid')) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập');
        }

        $validated = $request->validate([
            'dtid' => 'required|integer',
        ]);

        $dtid = (int) $validated['dtid'];

        // Lấy thông tin thanh toán từ database
        $payment = DB::table('thanhtoan')
            ->join('dattour', 'thanhtoan.dtid', '=', 'dattour.dtid')
            ->where('dattour.dtid', $dtid)
            ->where('dattour.ndid', session('ndid'))
            ->where('thanhtoan.phuongthuc', 'vnpay')
            ->first();

        if (!$payment) {
            return redirect()->back()->with('error', 'Không tìm thấy đơn hàng');
        }

        if (($payment->trangthai ?? null) === 'thanh_cong') {
            return redirect()->route('tour-booked', ['id' => $payment->dtid])
                ->with('success', 'Đơn hàng đã được thanh toán');
        }

        // Xây dựng URL VNPAY
        $vnp_Url = env('VNPAY_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
        $vnp_TmnCode = env('VNPAY_TMN_CODE');
        $vnp_HashSecret = env('VNPAY_HASH_SECRET');

        if (!$vnp_TmnCode || !$vnp_HashSecret) {
            return redirect()->back()->with('error', 'Chưa cấu hình VNPAY');
        }

        $amountValue = $payment->sotien ?? $payment->giacuoi;
        $vnp_Amount = (int) round(((float) $amountValue) * 100); // Nhân 100
        $vnp_TxnRef = $payment->magiaodich ?: ('BOOK' . $dtid . time());
        $vnp_ReturnUrl = env('VNPAY_RETURN_URL', route('vnpay.callback'));

        if (! $payment->magiaodich) {
            DB::table('thanhtoan')->where('dtid', $dtid)->update([
                'magiaodich' => $vnp_TxnRef,
            ]);
        }

        // Tính thời gian hết hạn dựa trên lúc đặt tour
        $originalCreateTime = \Carbon\Carbon::parse($payment->ngaytao ?? now())->setTimezone('Asia/Ho_Chi_Minh');
        if ($originalCreateTime->copy()->addMinutes(15)->isPast()) {
            return redirect()->route('tour-booked', ['id' => $dtid])->with('error', 'Đơn hàng đã hết hạn thanh toán (quá 15 phút).');
        }

        // Thời điểm tạo request thanh toán
        $startTime = \Carbon\Carbon::now('Asia/Ho_Chi_Minh');
        $expireTime = $originalCreateTime->copy()->addMinutes(15);

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => $startTime->format('YmdHis'), // Dùng thời gian đã ép múi giờ
            "vnp_ExpireDate" => $expireTime->format('YmdHis'), // Dùng thời gian hết hạn đã ép múi giờ
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $request->ip(),
            "vnp_Locale" => "vn",
            "vnp_OrderInfo" => "Thanh toan dat tour #" . $dtid,
            "vnp_OrderType" => "billpayment",
            "vnp_ReturnUrl" => $vnp_ReturnUrl,
            "vnp_TxnRef" => $vnp_TxnRef,
        ];

        $vnp_BankCode = $request->input('bank_code');
        if ($vnp_BankCode) {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        $vnp_Url = $this->buildVnpayUrl($vnp_Url, $inputData, $vnp_HashSecret);

        return redirect()->away($vnp_Url);
    }

    /**
     * Callback từ VNPAY
     * GET /vnpay-callback
     */
    public function callback(Request $request)
    {
        $inputData = $request->all();
        $vnp_SecureHash = $inputData['vnp_SecureHash'] ?? null;
        unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);

        $vnp_HashSecret = env('VNPAY_HASH_SECRET');
        if (! $vnp_SecureHash || ! $vnp_HashSecret) {
            return redirect()->route('home')->with('error', 'Thiếu thông tin xác thực VNPAY');
        }
        $secureHash = hash_hmac('sha512', $this->buildVnpayHashData($inputData), $vnp_HashSecret);

        // Kiểm tra chữ ký
        if (! hash_equals(strtoupper($secureHash), strtoupper((string) $vnp_SecureHash))) {
            return redirect()->route('home')->with('error', 'Chữ ký không hợp lệ');
        }

        $vnp_ResponseCode = $request->get('vnp_ResponseCode');
        $vnp_TransactionStatus = $request->get('vnp_TransactionStatus');
        $vnp_TxnRef = $request->get('vnp_TxnRef');
        $vnp_Amount = (int) $request->get('vnp_Amount');
        $vnp_TmnCode = $request->get('vnp_TmnCode');
        $vnp_TransactionNo = $request->get('vnp_TransactionNo');

        // Tìm thanh toán
        $payment = DB::table('thanhtoan')
            ->where('magiaodich', $vnp_TxnRef)
            ->where('phuongthuc', 'vnpay')
            ->first();

        if (!$payment) {
            return redirect()->route('home')->with('error', 'Không tìm thấy giao dịch');
        }

        $expectedTmnCode = env('VNPAY_TMN_CODE');
        if ($expectedTmnCode && $vnp_TmnCode !== $expectedTmnCode) {
            return redirect()->route('home')->with('error', 'Sai mã website VNPAY');
        }

        $expectedAmount = (int) round(((float) $payment->sotien) * 100);
        if ($vnp_Amount !== $expectedAmount) {
            return redirect()->route('home')->with('error', 'Sai số tiền thanh toán');
        }

        if (($payment->trangthai ?? null) === 'thanh_cong') {
            return redirect()->route('tour-booked', ['id' => $payment->dtid])
                ->with('success', 'Thanh toán VNPAY đã được ghi nhận');
        }

       if ($vnp_ResponseCode === '00' && ($vnp_TransactionStatus === null || $vnp_TransactionStatus === '00')) {
            // Thành công
            DB::table('thanhtoan')->where('magiaodich', $vnp_TxnRef)->update([
                'trangthai' => 'thanh_cong',
                'ngaythanhtoan' => now(),
            ]);

            DB::table('dattour')->where('dtid', $payment->dtid)->update([
                'trangthai' => 'da_xac_nhan',
            ]);

            return redirect()->route('tour-booked', ['id' => $payment->dtid])
                ->with('success', 'Thanh toán VNPAY thành công!');
        } 
        // ==============================================================
        // TRƯỜNG HỢP 1: KHÁCH BẤM HỦY TRÊN VNPAY (MÃ 24) -> GIỮ NGUYÊN ĐƠN
        // ==============================================================
        elseif ($vnp_ResponseCode === '24') {
            DB::table('thanhtoan')->where('magiaodich', $vnp_TxnRef)->update([
                'trangthai' => 'that_bai', // Cập nhật trạng thái thất bại nhưng KHÔNG HỦY ĐƠN, giữ nguyên trạng thái đặt tour để khách có thể quay lại thanh toán nếu chưa hết 15 phút
                 // Vẫn giữ lại mã giao dịch cũ ($vnp_TxnRef) theo đúng ý bạn   
                // Vẫn giữ lại mã giao dịch cũ ($vnp_TxnRef) theo đúng ý bạn
            ]);

            return redirect()->route('tour-booked', ['id' => $payment->dtid])
                ->with('error', 'Bạn đã hủy thao tác. Bạn vẫn có thể tiếp tục thanh toán nếu đơn hàng chưa hết thời gian 15 phút.');
        } 
        // ==============================================================
        // TRƯỜNG HỢP 2: HẾT HẠN 15 PHÚT (MÃ 11) HOẶC LỖI -> HỦY & TRẢ GHẾ
        // ==============================================================
        else {
            DB::beginTransaction();
            try {
                $dattour = DB::table('dattour')->where('dtid', $payment->dtid)->first();
                
                // Tránh việc cộng dồn ghế nếu đơn đã bị hủy trước đó
                if ($dattour && $dattour->trangthai !== 'da_huy') {
                    // Trả lại số lượng khách
                    $return_quantity = (int) $dattour->songuoilon + (int) $dattour->sotreem;
                    
                    DB::table('lichkhoihanh')
                        ->where('lichid', $dattour->lichid)
                        ->increment('sochocon', $return_quantity);

                    // Cập nhật trạng thái hủy
                    DB::table('dattour')->where('dtid', $payment->dtid)->update([
                        'trangthai' => 'da_huy'
                    ]);
                }

                // Cập nhật thanh toán thất bại
                DB::table('thanhtoan')->where('magiaodich', $vnp_TxnRef)->update([
                    'trangthai' => 'that_bai',
                ]);

                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
            }

            return redirect()->route('tour-booked', ['id' => $payment->dtid])
                ->with('error', 'Giao dịch đã hết hạn 15 phút hoặc bị lỗi. Đơn hàng đã TỰ ĐỘNG HỦY và hoàn trả số lượng ghế.');
        }
    }

    private function buildVnpayUrl(string $baseUrl, array $inputData, string $hashSecret): string
    {
        ksort($inputData);

        $hashData = $this->buildVnpayHashData($inputData, false);

        $queryParts = [];
        foreach ($inputData as $key => $value) {
            $queryParts[] = urlencode($key) . "=" . urlencode($value);
        }

        $secureHash = hash_hmac('sha512', $hashData, $hashSecret);

        return $baseUrl . "?" . implode('&', $queryParts) . "&vnp_SecureHash=" . $secureHash;
    }

    private function buildVnpayHashData(array $inputData, bool $shouldSort = true): string
    {
        if ($shouldSort) {
            ksort($inputData);
        }

        $hashParts = [];
        foreach ($inputData as $key => $value) {
            $hashParts[] = urlencode($key) . "=" . urlencode($value);
        }

        return implode('&', $hashParts);
    }
}
