<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\admin\BookingModel;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class BookingManagementController extends Controller
{
    private $booking;

    public function __construct()
    {
        $this->booking = new BookingModel();
    }

    public function index()
    {
        $title = 'Quản lý đặt Tour';
        $list_booking = $this->booking->getBooking();
        $list_booking = $this->updateHideBooking($list_booking);

        return view('admin.booking', compact('title', 'list_booking'));
    }

    // Xác nhận Booking (Bỏ JS - Dùng Redirect)
    public function confirmBooking(Request $request)
    {
        $bookingId = $request->bookingId;
        $dataConfirm = ['trangthai' => 'da_xac_nhan'];

        if ($this->booking->updateBooking($bookingId, $dataConfirm)) {
            return redirect()->back()->with('success', 'Xác nhận đơn đặt tour thành công.');
        } else {
            return redirect()->back()->with('error', 'Cập nhật thất bại.');
        }
    }

    // Hoàn thành Booking (Bỏ JS - Dùng Redirect)
    public function finishBooking(Request $request)
    {
        $bookingId = $request->bookingId;
        $dataConfirm = ['trangthai' => 'hoan_thanh'];

        if ($this->booking->updateBooking($bookingId, $dataConfirm)) {
            return redirect()->back()->with('success', 'Đã chuyển trạng thái thành Hoàn thành tour.');
        } else {
            return redirect()->back()->with('error', 'Cập nhật thất bại.');
        }
    }

    // Xác nhận đã nhận tiền (Bỏ JS - Dùng Redirect)
    public function receiviedMoney(Request $request)
    {
        $bookingId = $request->bookingId;
        $dataUpdate = ['trangthai' => 'thanh_cong'];

        if ($this->booking->updateCheckout($bookingId, $dataUpdate)) {
            return redirect()->back()->with('success', 'Cập nhật trạng thái thanh toán thành công.');
        } else {
            return redirect()->back()->with('error', 'Cập nhật thất bại.');
        }
    }

    public function showDetail($bookingId)
    {
        $title = 'Chi tiết đơn đặt';
        $invoice_booking = $this->booking->getInvoiceBooking($bookingId);
        $hide = 'hide';

        if (!$invoice_booking->transactionId) {
            $invoice_booking->transactionId = 'Thanh toán tại công ty Travela';
        }
        if ($invoice_booking->paymentStatus === 'cho_xu_ly' || $invoice_booking->paymentStatus === 'that_bai') {
            $hide = '';
        }
        return view('admin.booking-detail', compact('title', 'invoice_booking', 'hide'));
    }

    // Gửi PDF (Bỏ JS - Dùng Redirect)
    public function sendPdf(Request $request)
    {
        $bookingId = $request->input('bookingId');
        $invoice_booking = $this->booking->getInvoiceBooking($bookingId);

        if (!$invoice_booking->transactionId) {
            $invoice_booking->transactionId = 'Thanh toán tại công ty Travela';
        }

        try {
            Mail::send('admin.emails.invoice', compact('invoice_booking'), function ($message) use ($invoice_booking) {
                $message->to($invoice_booking->email)
                    ->subject('Hóa đơn đặt tour của khách hàng ' . $invoice_booking->fullName);
            });

            return redirect()->back()->with('success', 'Hóa đơn đã được gửi qua email thành công.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Không thể gửi email: ' . $e->getMessage());
        }
    }

    private function updateHideBooking($list_booking)
    {
        $currentDate = date('Y-m-d');
        foreach ($list_booking as $booking) {
            if ($booking->endDate < $currentDate) {
                $booking->hide = '';
            } else {
                $booking->hide = 'hide';
            }
        }
        return $list_booking;
    }
    // Xác nhận đã hoàn tiền cho khách khi hủy tour
    public function refundMoney(Request $request)
    {
        $bookingId = $request->bookingId;
        
        // Cập nhật trạng thái thành 'da_hoan_tien' và ghi lại ngày giờ hoàn tiền
        $dataUpdate = [
            'trangthai' => 'da_hoan_tien',
            'ngaythanhtoan' => date('Y-m-d H:i:s') 
        ];

        if ($this->booking->updateCheckout($bookingId, $dataUpdate)) {
            return redirect()->back()->with('success', 'Xác nhận Đã hoàn tiền cho khách hàng thành công!');
        } else {
            return redirect()->back()->with('error', 'Cập nhật thất bại.');
        }
    }
}