# Hướng Dẫn Tích Hợp Thanh Toán VNPAY

## 📋 Tổng Quan

Ứng dụng Travel đã được tích hợp chức năng thanh toán VNPAY. Hệ thống cho phép khách hàng thanh toán tiền tour thông qua cổng thanh toán VNPAY.

## ⚙️ Cấu Hình

### 1. Biến Môi Trường (.env)

File `.env` đã được cập nhật với các cấu hình VNPAY:

```
# VNPAY Payment Configuration
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
VNPAY_TMN_CODE=1VYBIYQP
VNPAY_HASH_SECRET=NOH6MBGNLQL9O9OMMFMZ2AX8NIEP50W1
VNPAY_RETURN_URL=http://127.0.0.1:8000/vnpay-callback
```

### 2. Cập Nhật Cấu Hình VNPAY

Thay thế các giá trị bằng thông tin tài khoản VNPAY của bạn:

- `VNPAY_URL`: URL cổng thanh toán
  - **Sandbox**: `https://sandbox.vnpayment.vn/paymentv2/vpcpay.html` (dùng để test)
  - **Production**: `https://pay.vnpay.vn/vpcpay.html` (dùng để production)

- `VNPAY_TMN_CODE`: Mã merchant được cấp bởi VNPAY

- `VNPAY_HASH_SECRET`: Chuỗi bí mật dùng để xác minh chữ ký (SHA-512 HMAC)

- `VNPAY_RETURN_URL`: URL callback sau khi khách thanh toán (trả về từ VNPAY)

## 🔄 Quy Trình Thanh Toán

### Bước 1: Khách Hàng Đặt Tour

1. Khách hàng điền form đặt tour: thông tin cá nhân, lịch khởi hành, số hành khách
2. Chọn phương thức thanh toán: **Thanh toán bằng VNPAY**
3. Nhấn nút "Xác nhận đặt tour"

### Bước 2: Tạo Đơn Đặt Tour

- Hệ thống lưu thông tin đơn đặt tour vào database
- Trạng thái đơn: `cho_xac_nhan` (chờ xác nhận)
- Trạng thái thanh toán: `cho_xu_ly` (chờ xử lý)

### Bước 3: Hiển Thị Trang Xác Nhận

- Khách hàng được chuyển hướng tới trang `tour-booked`
- Hiển thị thông tin đơn đặt tour
- Nếu phương thức thanh toán là VNPAY, hiển thị nút **"Thanh toán VNPAY"**

### Bước 4: Thanh Toán VNPAY

1. Khách hàng nhấn nút "Thanh toán VNPAY"
2. Hệ thống xây dựng URL thanh toán với các tham số:
   - `vnp_Amount`: Số tiền (tính bằng đơn vị nhỏ nhất × 100, ví dụ: 1 triệu VNĐ = 100,000,000)
   - `vnp_TxnRef`: Mã giao dịch duy nhất
   - `vnp_OrderInfo`: Thông tin đơn hàng
   - `vnp_ReturnUrl`: URL quay lại
3. Khách hàng được chuyển hướng tới trang thanh toán VNPAY

### Bước 5: Callback từ VNPAY

1. Sau khi khách hàng thanh toán (hoặc hủy), VNPAY gửi callback tới URL: `/vnpay-callback`
2. Hệ thống kiểm tra chữ ký xác minh (SHA-512 HMAC)
3. Nếu chữ ký hợp lệ:
   - Nếu `vnp_ResponseCode = 00`: Thanh toán thành công
     - Cập nhật trạng thái: `trangthai = 'thanh_cong'`
     - Cập nhật `ngaythanhtoan` = hiện tại
     - Cập nhật `magiaodich` = `vnp_TxnRef - vnp_TransactionNo`
     - Cập nhật trạng thái đơn đặt tour: `trangthai = 'da_thanh_toan'`
   - Nếu `vnp_ResponseCode ≠ 00`: Thanh toán thất bại
     - Cập nhật trạng thái: `trangthai = 'that_bai'`
4. Khách hàng được chuyển hướng về trang `tour-booked` với thông báo kết quả

## 📁 Các File Liên Quan

### Controller
- **[app/Http/Controllers/clients/BookingController.php](app/Http/Controllers/clients/BookingController.php)**
  - `vnpayPayment()`: Xử lý yêu cầu thanh toán VNPAY
  - `vnpayCallback()`: Xử lý callback từ VNPAY
  - `buildVnpayPaymentUrl()`: Xây dựng URL thanh toán VNPAY

### Routes
- **[routes/web.php](routes/web.php)**
  - `POST /vnpay-payment`: Route xử lý yêu cầu thanh toán
  - `GET /vnpay-callback`: Route nhận callback từ VNPAY

### Views
- **[resources/views/clients/booking.blade.php](resources/views/clients/booking.blade.php)**
  - Thêm tùy chọn "Thanh toán bằng VNPAY" vào form đặt tour

- **[resources/views/clients/tour-booked.blade.php](resources/views/clients/tour-booked.blade.php)**
  - Hiển thị nút "Thanh toán VNPAY" khi cần thanh toán

### Database
- **Bảng `thanhtoan`**: Lưu thông tin thanh toán
  - `ttid`: ID thanh toán
  - `dtid`: ID đơn đặt tour
  - `phuongthuc`: Phương thức thanh toán (vnpay)
  - `sotien`: Số tiền thanh toán
  - `magiaodich`: Mã giao dịch từ VNPAY
  - `trangthai`: Trạng thái (cho_xu_ly, thanh_cong, that_bai)
  - `ngaythanhtoan`: Ngày thanh toán

- **Bảng `dattour`**: Lưu thông tin đơn đặt tour
  - `dtid`: ID đơn đặt tour
  - `trangthai`: Trạng thái (cho_xac_nhan, da_thanh_toan, etc.)

## 🔐 Xác Minh Chữ Ký (Hash Verification)

### Thuật Toán

VNPAY sử dụng **SHA-512 HMAC** để xác minh chữ ký:

```
rawData = param1=value1&param2=value2&...&paramN=valueN
signature = HMAC-SHA512(rawData, VNPAY_HASH_SECRET)
```

### Quy Trình Xác Minh

1. Lấy giá trị `vnp_SecureHash` từ callback
2. Loại bỏ `vnp_SecureHash` khỏi dữ liệu
3. Sắp xếp các tham số còn lại theo thứ tự chữ cái (ksort)
4. Xây dựng chuỗi dữ liệu: `param1=value1&param2=value2&...`
5. Tính HMAC-SHA512: `secureHash = hash_hmac('sha512', hashData, VNPAY_HASH_SECRET)`
6. So sánh `secureHash === vnp_SecureHash`

### Ví Dụ

```php
$inputData = [
    'vnp_Amount' => 8086800,
    'vnp_Command' => 'pay',
    'vnp_CreateDate' => '20250326130821',
    'vnp_TmnCode' => '1VYBIYQP',
    'vnp_OrderInfo' => 'Thanh toan tour',
    'vnp_TxnRef' => 'BOOK1',
];

ksort($inputData);

$hashData = '';
$i = 0;
foreach ($inputData as $key => $value) {
    if ($i === 0) {
        $hashData .= urlencode($key) . "=" . urlencode($value);
    } else {
        $hashData .= "&" . urlencode($key) . "=" . urlencode($value);
    }
    $i++;
}

$secureHash = hash_hmac('sha512', $hashData, 'NOH6MBGNLQL9O9OMMFMZ2AX8NIEP50W1');
```

## 📊 Các Tham Số VNPAY

### Tham Số Yêu Cầu (Request)

| Tham Số | Bắt Buộc | Mô Tả | Ví Dụ |
|---------|----------|-------|-------|
| `vnp_Version` | ✓ | Phiên bản | 2.1.0 |
| `vnp_TmnCode` | ✓ | Mã merchant | 1VYBIYQP |
| `vnp_Amount` | ✓ | Số tiền (×100) | 8086800 |
| `vnp_Command` | ✓ | Lệnh thanh toán | pay |
| `vnp_CreateDate` | ✓ | Thời gian tạo | YmdHis |
| `vnp_CurrCode` | ✓ | Mã tiền tệ | VND |
| `vnp_IpAddr` | ✓ | Địa chỉ IP | 192.168.1.1 |
| `vnp_Locale` | ✓ | Ngôn ngữ | vn, en |
| `vnp_OrderInfo` | ✓ | Thông tin đơn hàng | Thanh toan tour |
| `vnp_OrderType` | ✓ | Loại đơn hàng | billpayment |
| `vnp_ReturnUrl` | ✓ | URL callback | http://... |
| `vnp_TxnRef` | ✓ | Mã giao dịch | BOOK123 |
| `vnp_SecureHash` | ✓ | Chữ ký | [SHA-512 HMAC] |

### Tham Số Phản Hồi (Callback)

| Tham Số | Mô Tả | Ví Dụ |
|---------|-------|-------|
| `vnp_ResponseCode` | Mã kết quả (00=thành công) | 00, 07, 09, ... |
| `vnp_TxnRef` | Mã giao dịch | BOOK123 |
| `vnp_TransactionNo` | Mã giao dịch VNPAY | 14869839 |
| `vnp_Amount` | Số tiền | 8086800 |
| `vnp_PayDate` | Ngày thanh toán | YmdHis |
| `vnp_SecureHash` | Chữ ký | [SHA-512 HMAC] |

## 🧪 Kiểm Tra (Testing)

### 1. Test Trên Sandbox

Sử dụng môi trường sandbox để test:

```
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
```

### 2. Thông Tin Test

- **Mã merchant (Sandbox)**: 1VYBIYQP
- **Chuỗi bí mật (Sandbox)**: NOH6MBGNLQL9O9OMMFMZ2AX8NIEP50W1

### 3. Thẻ Test

Sử dụng thẻ test của VNPAY (có sẵn trên tài khoản sandbox)

### 4. Kiểm Tra Log

- Xem log thanh toán: `/storage/logs/laravel.log`
- Kiểm tra database: bảng `thanhtoan` và `dattour`

## 🐛 Xử Lý Lỗi

### Lỗi Phổ Biến

| Mã | Mô Tả | Giải Pháp |
|----|-------|----------|
| 00 | Thành công | ✓ |
| 07 | Trừ tiền thất bại | Kiểm tra số dư, liên hệ ngân hàng |
| 09 | Giao dịch từ chối | Kiểm tra thông tin thẻ, liên hệ ngân hàng |
| 10 | Sai định dạng dữ liệu | Kiểm tra URL, tham số gửi |

### Debug

1. Kiểm tra biến môi trường (.env):
   ```bash
   php artisan tinker
   > env('VNPAY_TMN_CODE')
   > env('VNPAY_HASH_SECRET')
   ```

2. Kiểm tra URL thanh toán:
   - Thêm `dd($vnp_Url)` trong hàm `buildVnpayPaymentUrl()`
   - Kiểm tra URL format

3. Kiểm tra callback:
   - Thêm log: `Log::info('VNPAY Callback', $request->all());`
   - Kiểm tra chữ ký trong database

## 📝 Ghi Chú

- **Số tiền**: VNPAY yêu cầu số tiền × 100 (ví dụ: 1 triệu VNĐ = 100,000,000)
- **Chữ ký**: Luôn xác minh chữ ký callback trước khi cập nhật database
- **URL Callback**: Phải là URL công khai (không localhost) để VNPAY gọi được
- **Timeout**: Callback có thể gọi lại nhiều lần, cần xử lý duplicate

## 📞 Hỗ Trợ

- **VNPAY Support**: https://sandbox.vnpayment.vn
- **Tài liệu API**: Xem tài liệu VNPAY API tại admin.vnpay.vn

## 🎯 Các Bước Tiếp Theo

1. **Cập nhật thông tin merchant**: Thay thế `VNPAY_TMN_CODE` và `VNPAY_HASH_SECRET` bằng thông tin thực của bạn
2. **Cấu hình IP whitelist**: Thêm IP server của bạn vào danh sách IP được phép trên trang quản lý VNPAY
3. **Cấu hình Return URL**: Đảm bảo URL callback là công khai
4. **Test toàn bộ quy trình**: Từ đặt tour đến thanh toán
5. **Deploy production**: Khi đã sẵn sàng, thay đổi `VNPAY_URL` sang production URL

---

**Ngày cập nhật**: 2025-05-04
**Phiên bản**: 1.0
