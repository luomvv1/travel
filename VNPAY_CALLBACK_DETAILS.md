# Xử Lý Callback VNPAY - Tài Liệu Chi Tiết

## 📌 Tổng Quan

Khi khách hàng hoàn thành thanh toán trên cổng VNPAY, VNPAY sẽ gửi yêu cầu GET/POST tới URL callback của bạn. File này giải thích cách ứng dụng xử lý callback đó.

## 🔗 URL Callback

```
GET /vnpay-callback?vnp_Amount=8086800&vnp_BankCode=NCB&...&vnp_SecureHash=4b35...
```

**Route**: `routes/web.php`
```php
Route::get('/vnpay-callback', [BookingController::class, 'vnpayCallback'])->name('vnpay.callback');
```

**Controller**: `app/Http/Controllers/clients/BookingController.php`
```php
public function vnpayCallback(Request $request)
{
    // Xử lý callback từ VNPAY
}
```

## 🔐 Quy Trình Xác Minh

### Bước 1: Lấy Dữ Liệu từ Request

```php
$vnp_SecureHash = $request->get('vnp_SecureHash');
$inputData = $request->all();
```

### Bước 2: Loại Bỏ Chữ Ký

```php
unset($inputData['vnp_SecureHash']);
```

### Bước 3: Sắp Xếp Tham Số

```php
ksort($inputData);
```

### Bước 4: Xây Dựng Chuỗi Hash

```php
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
```

**Kết quả ví dụ:**
```
vnp_Amount=8086800&vnp_BankCode=NCB&vnp_BankTranNo=VNP14869839&...
```

### Bước 5: Tính Toán Chữ Ký

```php
$vnp_HashSecret = env('VNPAY_HASH_SECRET'); // NOH6MBGNLQL9O9OMMFMZ2AX8NIEP50W1
$secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
```

### Bước 6: So Sánh Chữ Ký

```php
if ($secureHash !== $vnp_SecureHash) {
    return redirect()->route('home')->with('error', 'Chữ ký xác thực không hợp lệ!');
}
```

## 📋 Các Tham Số Callback

Khi VNPAY gọi callback, nó gửi các tham số sau:

```
vnp_Amount=8086800
vnp_BankCode=NCB
vnp_BankTranNo=VNP14869839
vnp_CardType=ATM
vnp_OrderInfo=Thanh+toán+đơn+hàng+test
vnp_PayDate=20250326130921
vnp_ResponseCode=00
vnp_TmnCode=1VYBIYQP
vnp_TransactionNo=14869839
vnp_TransactionStatus=00
vnp_TxnRef=6069
vnp_SecureHash=4b3533435c89e8742701a660c1be9a12e779abe72f10c6a5a8a14a05db2d5ef29f2e23e919eca87e1f6f76a13e633ca2be99bdb09b47139c7de342c03e81251f
```

### Giải Thích Các Tham Số

| Tham Số | Mô Tả | Ví Dụ |
|---------|-------|-------|
| `vnp_Amount` | Số tiền (×100) | 8086800 = 80,868 VNĐ |
| `vnp_BankCode` | Mã ngân hàng | NCB, BIDV, VIETCOMBANK, etc. |
| `vnp_BankTranNo` | Mã giao dịch tại ngân hàng | VNP14869839 |
| `vnp_CardType` | Loại thẻ | ATM, CREDIT, DEBIT |
| `vnp_OrderInfo` | Thông tin đơn hàng | Thanh toán đơn hàng test |
| `vnp_PayDate` | Ngày thanh toán | 20250326130921 (YmdHis) |
| `vnp_ResponseCode` | Mã kết quả thanh toán | 00=thành công, 07/09/10=lỗi |
| `vnp_TmnCode` | Mã merchant | 1VYBIYQP |
| `vnp_TransactionNo` | Mã giao dịch VNPAY | 14869839 |
| `vnp_TransactionStatus` | Trạng thái giao dịch | 00=thành công, 01=lỗi |
| `vnp_TxnRef` | Mã giao dịch của merchant (ID đơn hàng) | 6069 |
| `vnp_SecureHash` | Chữ ký SHA-512 HMAC | 4b35... |

## ✅ Xử Lý Kế Tiếp (Sau Khi Xác Minh Chữ Ký)

### 1. Lấy Mã Giao Dịch

```php
$vnp_ResponseCode = $request->get('vnp_ResponseCode');
$vnp_TxnRef = $request->get('vnp_TxnRef');
$vnp_TransactionNo = $request->get('vnp_TransactionNo');
```

### 2. Tìm Bản Ghi Thanh Toán

```php
$thanhtoan = DB::table('thanhtoan')->where('magiaodich', $vnp_TxnRef)->first();

if (!$thanhtoan) {
    return redirect()->route('home')->with('error', 'Không tìm thấy thông tin giao dịch!');
}
```

### 3. Kiểm Tra Kết Quả Thanh Toán

```php
if ($vnp_ResponseCode === '00') {
    // ✓ Thanh toán thành công
} else {
    // ✗ Thanh toán thất bại
}
```

### 4. Cập Nhật Database (Thành Công)

```php
if ($vnp_ResponseCode === '00') {
    // Cập nhật trạng thái thanh toán
    DB::table('thanhtoan')->where('magiaodich', $vnp_TxnRef)->update([
        'trangthai' => 'thanh_cong',
        'ngaythanhtoan' => now(),
        'magiaodich' => $vnp_TxnRef . '-' . $vnp_TransactionNo,
    ]);
    
    // Cập nhật trạng thái đơn đặt tour
    DB::table('dattour')->where('dtid', $thanhtoan->dtid)->update([
        'trangthai' => 'da_thanh_toan'
    ]);
    
    // Chuyển hướng tới trang xác nhận
    return redirect()->route('tour-booked', ['id' => $thanhtoan->dtid])
        ->with('success', 'Thanh toán VNPAY thành công!');
}
```

### 5. Cập Nhật Database (Thất Bại)

```php
else {
    // Cập nhật trạng thái thanh toán
    DB::table('thanhtoan')->where('magiaodich', $vnp_TxnRef)->update([
        'trangthai' => 'that_bai'
    ]);
    
    // Chuyển hướng tới trang xác nhận
    return redirect()->route('tour-booked', ['id' => $thanhtoan->dtid])
        ->with('error', 'Thanh toán VNPAY thất bại! Mã lỗi: ' . $vnp_ResponseCode);
}
```

## 🔍 Mã Lỗi VNPAY

| Mã | Mô Tả | Giải Pháp |
|----|-------|----------|
| 00 | Thành công | ✓ |
| 01 | Giao dịch bị từ chối | Liên hệ VNPAY |
| 02 | Số tiền không hợp lệ | Kiểm tra lại số tiền |
| 03 | Mã merchant không hợp lệ | Kiểm tra VNPAY_TMN_CODE |
| 04 | Giao dịch không hợp lệ | Liên hệ VNPAY |
| 05 | Giao dịch bị từ chối | Liên hệ VNPAY |
| 06 | Giao dịch bị hủy | Khách hàng hủy giao dịch |
| 07 | Trừ tiền thất bại | Kiểm tra số dư tài khoản |
| 08 | Giao dịch bị từ chối | Liên hệ ngân hàng |
| 09 | Giao dịch từ chối | Kiểm tra thông tin thẻ |
| 10 | Định dạng dữ liệu không hợp lệ | Kiểm tra URL |
| 11 | Chủ thẻ không xác thực | Kiểm tra OTP |
| 12 | Giao dịch bị hủy | Khách hàng hủy giao dịch |
| 13 | Sai mã bảo mật | Kiểm tra CVV |
| 14 | Thẻ hết hạn | Sử dụng thẻ khác |
| 15 | Thẻ không được phép | Sử dụng thẻ khác |
| ... | Xem danh sách đầy đủ tại admin.vnpay.vn | ... |

## 📊 Luồng Dữ Liệu

```
┌──────────────────┐
│ Khách Hàng Đặt   │
│ Tour & Chọn      │
│ VNPAY            │
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│ Tạo Đơn Đặt      │
│ Tour (dtid)      │
│ Trạng thái:      │
│ cho_xac_nhan     │
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│ Trang tour-      │
│ booked (xem      │
│ nút Thanh toán   │
│ VNPAY)           │
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│ Khách Nhấn       │
│ "Thanh toán      │
│ VNPAY"           │
└────────┬─────────┘
         │
         ▼
┌──────────────────────┐
│ POST /vnpay-payment  │
│ Xây dựng URL thanh   │
│ toán VNPAY           │
└────────┬─────────────┘
         │
         ▼
┌──────────────────────┐
│ Redirect to VNPAY    │
│ Payment Gateway      │
└────────┬─────────────┘
         │
         ▼
┌──────────────────────┐
│ Khách Thanh Toán     │
│ trên VNPAY           │
└────────┬─────────────┘
         │
         ▼
┌──────────────────────┐
│ VNPAY Gọi Callback   │
│ GET /vnpay-callback  │
│ với tham số kết quả  │
└────────┬─────────────┘
         │
         ▼
┌──────────────────────┐
│ Xác Minh Chữ Ký      │
│ (SHA-512 HMAC)       │
└────┬─────────────┬───┘
     │             │
   ✗ Sai       ✓ Đúng
     │             │
     ▼             ▼
  Lỗi        ┌──────────────────┐
             │ Kiểm Tra vnp_    │
             │ ResponseCode     │
             └────┬──────────┬──┘
                  │          │
                  │ = 00     │ ≠ 00
                  │ (✓)      │ (✗)
                  │          │
                  ▼          ▼
             ┌────────┐  ┌─────────┐
             │ Cập    │  │ Cập     │
             │ Nhật   │  │ Nhật    │
             │ Trạng  │  │ Trạng   │
             │ Thái   │  │ Thái    │
             │ THÀNH  │  │ THẤT    │
             │ CÔNG   │  │ BẠI     │
             └───┬────┘  └────┬────┘
                 │            │
                 └──────┬──────┘
                        │
                        ▼
             ┌──────────────────────┐
             │ Redirect to tour-    │
             │ booked với thông     │
             │ báo kết quả          │
             └──────────────────────┘
```

## 🛡️ Bảo Mật

### 1. Luôn Xác Minh Chữ Ký

```php
if ($secureHash !== $vnp_SecureHash) {
    return redirect()->route('home')->with('error', 'Chữ ký không hợp lệ!');
}
```

### 2. Xử Lý Duplicate Callback

VNPAY có thể gọi callback nhiều lần. Cần kiểm tra trước khi cập nhật:

```php
$thanhtoan = DB::table('thanhtoan')->where('magiaodich', $vnp_TxnRef)->first();

if ($thanhtoan && $thanhtoan->trangthai === 'thanh_cong') {
    // Đã xử lý callback trước đó, bỏ qua
    return redirect()->route('tour-booked', ['id' => $thanhtoan->dtid])
        ->with('success', 'Thanh toán thành công!');
}
```

### 3. Kiểm Tra IP Whitelist

Thêm IP whitelist để chỉ nhận callback từ VNPAY:

```php
$allowedIps = [
    '103.7.28.100', // VNPAY IP (example)
    '103.7.28.101',
];

if (!in_array($request->ip(), $allowedIps)) {
    return response('Unauthorized', 401);
}
```

### 4. Log Tất Cả Callback

```php
Log::info('VNPAY Callback Received', [
    'vnp_TxnRef' => $vnp_TxnRef,
    'vnp_ResponseCode' => $vnp_ResponseCode,
    'vnp_Amount' => $vnp_Amount,
    'ip' => $request->ip(),
]);
```

## 🧪 Test Callback

### 1. Test Locally (Sandbox)

Sử dụng ngrok để expose localhost:

```bash
ngrok http 8000
```

Cập nhật VNPAY_RETURN_URL trong .env:

```
VNPAY_RETURN_URL=https://your-ngrok-url.ngrok.io/vnpay-callback
```

### 2. Test Manual

Truy cập URL callback thủ công:

```
http://127.0.0.1:8000/vnpay-callback?vnp_Amount=8086800&vnp_ResponseCode=00&vnp_TxnRef=BOOK123&...
```

### 3. Kiểm Tra Database

```sql
-- Kiểm tra bản ghi thanh toán
SELECT * FROM thanhtoan WHERE magiaodich LIKE 'BOOK%';

-- Kiểm tra trạng thái đơn đặt tour
SELECT * FROM dattour WHERE dtid = 1;
```

## 📝 Ví Dụ Callback Đầy Đủ

### Request từ VNPAY

```
GET /vnpay-callback?vnp_Amount=8086800&vnp_BankCode=NCB&vnp_BankTranNo=VNP14869839&vnp_CardType=ATM&vnp_OrderInfo=Thanh+toan+tour+%231&vnp_PayDate=20250326130921&vnp_ResponseCode=00&vnp_TmnCode=1VYBIYQP&vnp_TransactionNo=14869839&vnp_TransactionStatus=00&vnp_TxnRef=BOOK1126&vnp_SecureHash=4b3533435c89e8742701a660c1be9a12e779abe72f10c6a5a8a14a05db2d5ef29f2e23e919eca87e1f6f76a13e633ca2be99bdb09b47139c7de342c03e81251f
```

### Xử Lý trong Controller

```php
public function vnpayCallback(Request $request)
{
    // 1. Lấy chữ ký
    $vnp_SecureHash = $request->get('vnp_SecureHash');
    
    // 2. Chuẩn bị dữ liệu
    $inputData = $request->all();
    unset($inputData['vnp_SecureHash']);
    ksort($inputData);
    
    // 3. Xây dựng chuỗi hash
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
    
    // 4. Tính toán chữ ký
    $vnp_HashSecret = env('VNPAY_HASH_SECRET');
    $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
    
    // 5. Xác minh chữ ký
    if ($secureHash !== $vnp_SecureHash) {
        return redirect()->route('home')->with('error', 'Chữ ký xác thực không hợp lệ!');
    }
    
    // 6. Lấy thông tin kết quả
    $vnp_ResponseCode = $request->get('vnp_ResponseCode');
    $vnp_TxnRef = $request->get('vnp_TxnRef');
    $vnp_TransactionNo = $request->get('vnp_TransactionNo');
    
    // 7. Tìm bản ghi thanh toán
    $thanhtoan = DB::table('thanhtoan')->where('magiaodich', $vnp_TxnRef)->first();
    
    if (!$thanhtoan) {
        return redirect()->route('home')->with('error', 'Không tìm thấy thông tin giao dịch!');
    }
    
    // 8. Cập nhật trạng thái
    if ($vnp_ResponseCode === '00') {
        DB::table('thanhtoan')->where('magiaodich', $vnp_TxnRef)->update([
            'trangthai' => 'thanh_cong',
            'ngaythanhtoan' => now(),
            'magiaodich' => $vnp_TxnRef . '-' . $vnp_TransactionNo,
        ]);
        DB::table('dattour')->where('dtid', $thanhtoan->dtid)->update([
            'trangthai' => 'da_thanh_toan'
        ]);
        
        return redirect()->route('tour-booked', ['id' => $thanhtoan->dtid])
            ->with('success', 'Thanh toán VNPAY thành công!');
    } else {
        DB::table('thanhtoan')->where('magiaodich', $vnp_TxnRef)->update([
            'trangthai' => 'that_bai'
        ]);
        
        return redirect()->route('tour-booked', ['id' => $thanhtoan->dtid])
            ->with('error', 'Thanh toán VNPAY thất bại! Mã lỗi: ' . $vnp_ResponseCode);
    }
}
```

### Response Trả Về

- **Thành công (00)**: Chuyển hướng tới `/tour-booked/{id}` với thông báo thành công ✓
- **Thất bại (≠00)**: Chuyển hướng tới `/tour-booked/{id}` với thông báo lỗi ✗
- **Chữ ký sai**: Chuyển hướng tới home với thông báo lỗi

---

**Ngày cập nhật**: 2025-05-04
**Phiên bản**: 1.0
