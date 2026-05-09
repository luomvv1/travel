// /**
//  * VNPAY Payment Handler
//  * 
//  * Xử lý thanh toán VNPAY trên view tour-booked.blade.php
//  * Cho phép khách hàng thanh toán trực tiếp từ trang xác nhận đặt tour
//  */

// // Hàm xử lý thanh toán MoMo (đã có)
// function paymentMomo(dtid) {
//     if (!dtid) {
//         toastr.error('Không tìm thấy ID đơn hàng');
//         return;
//     }

//     // Hiển thị loading
//     const button = event.target;
//     const originalText = button.innerHTML;
//     button.disabled = true;
//     button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

//     fetch('/createMomoPayment', {
//         method: 'POST',
//         headers: {
//             'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
//             'Accept': 'application/json',
//             'Content-Type': 'application/json',
//         },
//         body: JSON.stringify({ dtid: dtid })
//     })
//     .then(response => response.json())
//     .then(data => {
//         button.disabled = false;
//         button.innerHTML = originalText;

//         if (!data.success) {
//             toastr.error(data.message || 'Lỗi khi tạo link MoMo');
//             return;
//         }

//         // Redirect tới trang thanh toán MoMo
//         window.location.href = data.payUrl;
//     })
//     .catch(error => {
//         button.disabled = false;
//         button.innerHTML = originalText;
//         console.error('Error:', error);
//         toastr.error('Lỗi khi xử lý thanh toán MoMo');
//     });
// }

// // Hàm xử lý thanh toán VNPAY
// function paymentVnpay(dtid) {
//     if (!dtid) {
//         toastr.error('Không tìm thấy ID đơn hàng');
//         return;
//     }

//     // Hiển thị loading
//     const form = event.target.closest('form');
//     const button = event.target.closest('button');
    
//     if (button) {
//         const originalText = button.innerHTML;
//         button.disabled = true;
//         button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';

//         form.addEventListener('submit', function() {
//             setTimeout(() => {
//                 button.disabled = false;
//                 button.innerHTML = originalText;
//             }, 3000);
//         });
//     }

//     // Form sẽ tự submit thông qua HTML form action
// }

// /**
//  * Xác thực dữ liệu callback từ VNPAY
//  * (Được xử lý ở server-side trong BookingController::vnpayCallback)
//  */

// /**
//  * Cấu hình Toastr (nếu cần custom)
//  */
// if (typeof toastr !== 'undefined') {
//     toastr.options = {
//         "closeButton": true,
//         "debug": false,
//         "newestOnTop": false,
//         "progressBar": true,
//         "positionClass": "toast-top-right",
//         "preventDuplicates": false,
//         "onclick": null,
//         "showDuration": "300",
//         "hideDuration": "1000",
//         "timeOut": "5000",
//         "extendedTimeOut": "1000",
//         "showEasing": "swing",
//         "hideEasing": "linear",
//         "showMethod": "fadeIn",
//         "hideMethod": "fadeOut"
//     };
// }

// /**
//  * Xử lý URL parameters từ callback
//  * Hiển thị thông báo thành công/thất bại nếu có
//  */
// document.addEventListener('DOMContentLoaded', function() {
//     const params = new URLSearchParams(window.location.search);
    
//     // Kiểm tra query parameters từ URL (từ success/error message)
//     if (window.location.href.includes('vnp_ResponseCode')) {
//         // Nếu URL chứa tham số VNPAY (từ redirect), hiển thị thông báo
//         const responseCode = params.get('vnp_ResponseCode');
//         if (responseCode === '00') {
//             // Thành công - Laravel sẽ hiển thị via flash message
//         } else if (responseCode) {
//             // Thất bại - Laravel sẽ hiển thị via flash message
//         }
//     }
// });

// /**
//  * Hàm tiện ích: Định dạng tiền tệ VND
//  */
// function formatCurrency(value) {
//     return new Intl.NumberFormat('vi-VN', {
//         style: 'currency',
//         currency: 'VND'
//     }).format(value);
// }

// /**
//  * Hàm tiện ích: Kiểm tra trạng thái thanh toán
//  */
// function checkPaymentStatus(dtid) {
//     fetch(`/check-payment-status/${dtid}`, {
//         method: 'GET',
//         headers: {
//             'Accept': 'application/json',
//         }
//     })
//     .then(response => response.json())
//     .then(data => {
//         if (data.status === 'thanh_cong') {
//             toastr.success('Thanh toán thành công!');
//             // Có thể reload trang hoặc cập nhật UI
//             location.reload();
//         } else if (data.status === 'that_bai') {
//             toastr.error('Thanh toán thất bại!');
//         } else {
//             toastr.info('Đang xử lý thanh toán...');
//         }
//     })
//     .catch(error => {
//         console.error('Error:', error);
//         toastr.error('Lỗi khi kiểm tra trạng thái thanh toán');
//     });
// }

// /**
//  * VNPAY Payment Flow Documentation
//  * 
//  * ============================================================================
//  * 1. KHI KHÁCH HÀNG NHẤN NÚT "THANH TOÁN VNPAY"
//  * ============================================================================
//  * 
//  * HTML Form:
//  * <form action="{{ route('vnpay.payment') }}" method="POST">
//  *     @csrf
//  *     <input type="hidden" name="dtid" value="{{ $booking->dtid }}">
//  *     <button type="submit" class="btn btn-success">
//  *         <i class="fas fa-credit-card"></i> Thanh toán VNPAY
//  *     </button>
//  * </form>
//  * 
//  * ============================================================================
//  * 2. GỬI REQUEST TỚI /vnpay-payment
//  * ============================================================================
//  * 
//  * Route: POST /vnpay-payment
//  * Controller: BookingController::vnpayPayment()
//  * 
//  * Hàm này sẽ:
//  * - Nhận dtid từ request
//  * - Tìm đơn đặt tour trong database
//  * - Xây dựng URL thanh toán VNPAY
//  * - Trả về JSON với payUrl
//  * 
//  * Response:
//  * {
//  *     "success": true,
//  *     "payUrl": "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Version=2.1.0&..."
//  * }
//  * 
//  * ============================================================================
//  * 3. REDIRECT TỚI VNPAY
//  * ============================================================================
//  * 
//  * JavaScript chuyển hướng tới payUrl
//  * window.location.href = data.payUrl;
//  * 
//  * ============================================================================
//  * 4. KHÁCH HÀNG THANH TOÁN TRÊN VNPAY
//  * ============================================================================
//  * 
//  * Khách hàng:
//  * 1. Nhập thông tin thẻ
//  * 2. Xác thực (OTP, 3D Secure, etc.)
//  * 3. Hoàn thành giao dịch
//  * 
//  * ============================================================================
//  * 5. VNPAY GỌI CALLBACK
//  * ============================================================================
//  * 
//  * Route: GET /vnpay-callback
//  * URL: /vnpay-callback?vnp_Amount=...&vnp_ResponseCode=00&vnp_SecureHash=...
//  * 
//  * ============================================================================
//  * 6. XỬ LÝ CALLBACK
//  * ============================================================================
//  * 
//  * Controller: BookingController::vnpayCallback()
//  * 
//  * Hàm này sẽ:
//  * 1. Xác minh chữ ký (SHA-512 HMAC)
//  * 2. Kiểm tra vnp_ResponseCode
//  * 3. Cập nhật database (thanhtoan, dattour)
//  * 4. Redirect tới tour-booked với thông báo
//  * 
//  * ============================================================================
//  * 7. HIỂN THỊ KẾT QUẢ
//  * ============================================================================
//  * 
//  * Khách hàng được chuyển hướng tới /tour-booked/{id}
//  * 
//  * Nếu thành công:
//  * - Hiển thị: "Thanh toán VNPAY thành công!"
//  * - Cập nhật trạng thái: "Đã thanh toán"
//  * 
//  * Nếu thất bại:
//  * - Hiển thị: "Thanh toán VNPAY thất bại!"
//  * - Vẫn cho phép retry thanh toán
//  * 
//  * ============================================================================
//  */
