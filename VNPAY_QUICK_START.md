# ⚡ VNPAY Integration - Quick Start Guide

## 🎯 Mục Tiêu

Giúp bạn nhanh chóng tích hợp thanh toán VNPAY vào ứng dụng Travel trong **5 phút**.

---

## 📋 Tóm Tắt Các Thay Đổi

| File | Thay Đổi |
|------|----------|
| `.env` | ✅ Thêm cấu hình VNPAY |
| `routes/web.php` | ✅ Thêm 2 route VNPAY |
| `BookingController.php` | ✅ Thêm 3 method VNPAY |
| `booking.blade.php` | ✅ Thêm tùy chọn VNPAY |
| `tour-booked.blade.php` | ✅ Thêm nút thanh toán VNPAY |
| `vnpay-payment.js` | ✅ Tạo file JavaScript |
| Docs | ✅ 5 file tài liệu |

---

## 🚀 Bắt Đầu Nhanh

### Bước 1: Cập Nhật .env

```bash
# Mở .env và cập nhật VNPAY config:
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
VNPAY_TMN_CODE=1VYBIYQP
VNPAY_HASH_SECRET=NOH6MBGNLQL9O9OMMFMZ2AX8NIEP50W1
VNPAY_RETURN_URL=http://127.0.0.1:8000/vnpay-callback
```

### Bước 2: Kiểm Tra Files Đã Được Tạo

```bash
# Kiểm tra routes
php artisan route:list | grep vnpay

# Kiểm tra controller
grep "vnpayPayment\|vnpayCallback" app/Http/Controllers/clients/BookingController.php
```

### Bước 3: Clear Cache

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Bước 4: Test Trên Browser

1. Truy cập: `http://127.0.0.1:8000/tours`
2. Đặt tour (chọn "Thanh toán bằng VNPAY")
3. Nhấn "Thanh toán VNPAY"
4. Kiểm tra database

---

## 🔍 Kiểm Tra Nhanh

### Kiểm Tra 1: Routes

```bash
php artisan route:list | grep vnpay
```

**Kỳ vọng:**
```
POST | /vnpay-payment
GET  | /vnpay-callback
```

### Kiểm Tra 2: Controller Methods

```bash
grep -n "function vnpay" app/Http/Controllers/clients/BookingController.php
```

**Kỳ vọng:**
```
- vnpayPayment()
- vnpayCallback()
- buildVnpayPaymentUrl()
```

### Kiểm Tra 3: Database

```sql
-- Kiểm tra từ MySQL
SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'thanhtoan' AND COLUMN_NAME = 'phuongthuc';
```

**Kỳ vọng:**
```
enum('tai_van_phong','chuyen_khoan','momo','vnpay')
```

---

## 🧪 Test Nhanh

### Test 1: Đặt Tour + Thanh Toán (5 phút)

```
1. Truy cập: http://127.0.0.1:8000/tours
2. Chọn tour → Nhấn "Đặt tour ngay"
3. Điền form → Chọn "Thanh toán bằng VNPAY"
4. Nhấn "Xác nhận đặt tour"
5. Nhấn "Thanh toán VNPAY"
6. Điền thẻ test: 9704198526191432198 (VNPAY sandbox)
7. Nhấn "Thanh toán"
8. Kiểm tra redirect + database
```

### Test 2: Kiểm Tra Database

```sql
-- Xem đơn đặt tour vừa tạo
SELECT * FROM dattour ORDER BY dtid DESC LIMIT 1;

-- Xem thanh toán VNPAY
SELECT * FROM thanhtoan ORDER BY ttid DESC LIMIT 1;
```

**Kỳ vọng:**
```
- thanhtoan.phuongthuc = 'vnpay'
- thanhtoan.trangthai = 'thanh_cong' (hoặc 'cho_xu_ly')
- dattour.trangthai = 'da_thanh_toan' (nếu callback thành công)
```

---

## ⚙️ Cấu Hình Production

### Bước 1: Cấu Hình VNPAY

Thay đổi `.env`:

```
VNPAY_URL=https://pay.vnpay.vn/vpcpay.html
VNPAY_TMN_CODE=<your_production_code>
VNPAY_HASH_SECRET=<your_production_secret>
VNPAY_RETURN_URL=https://yourdomain.com/vnpay-callback
```

### Bước 2: Cấu Hình IP Whitelist

1. Đăng nhập: https://admin.vnpay.vn
2. Tìm "Cài đặt" → "IP Whitelist"
3. Thêm IP server production

### Bước 3: Deploy

```bash
php artisan config:cache
php artisan config:clear
php artisan view:cache
```

---

## 🐛 Troubleshooting Nhanh

| Vấn Đề | Giải Pháp |
|--------|----------|
| **Lỗi 404 route** | Chạy `php artisan route:cache` |
| **Chữ ký không hợp lệ** | Kiểm tra `VNPAY_HASH_SECRET` trong .env |
| **Không nhận callback** | Kiểm tra URL callback công khai (không localhost) |
| **Số tiền sai** | Kiểm tra: `vnp_Amount = giacuoi * 100` |
| **Redirect thất bại** | Kiểm tra `VNPAY_RETURN_URL` chính xác |

---

## 📊 Quy Trình Thanh Toán (Visual)

```
┌─────────────┐
│ Khách Đặt   │
│ Tour + VNPAY│
└──────┬──────┘
       │
       ▼
┌──────────────────────┐
│ POST /vnpay-payment  │
│ Xây dựng URL VNPAY   │
└──────┬───────────────┘
       │
       ▼
┌──────────────────┐
│ Redirect VNPAY   │
│ Payment Gateway  │
└──────┬───────────┘
       │
       ▼
┌──────────────────┐
│ Khách Thanh Toán │
│ trên VNPAY       │
└──────┬───────────┘
       │
       ▼
┌──────────────────────────┐
│ VNPAY Gọi Callback       │
│ GET /vnpay-callback?...  │
└──────┬───────────────────┘
       │
       ▼
┌──────────────────────┐
│ Xác Minh Chữ Ký      │
│ Cập Nhật Database    │
└──────┬───────────────┘
       │
       ▼
┌──────────────────────────┐
│ Redirect tour-booked     │
│ + Thông báo kết quả      │
└──────────────────────────┘
```

---

## 📁 Các File Liên Quan

### Code Files
```
app/Http/Controllers/clients/BookingController.php
routes/web.php
resources/views/clients/booking.blade.php
resources/views/clients/tour-booked.blade.php
resources/js/vnpay-payment.js
```

### Documentation Files
```
VNPAY_INTEGRATION_GUIDE.md           (Chi tiết đầy đủ)
VNPAY_CALLBACK_DETAILS.md            (Chi tiết callback)
VNPAY_DATABASE_STRUCTURE.sql         (Schema & queries)
VNPAY_IMPLEMENTATION_SUMMARY.md      (Tóm tắt)
VNPAY_VERIFICATION_CHECKLIST.md      (Checklist)
VNPAY_QUICK_START.md                 (File này)
```

---

## 🎓 Ví Dụ API

### Request từ Booking Form

```bash
POST /vnpay-payment
X-CSRF-TOKEN: <token>
Content-Type: application/json

{
    "dtid": 1
}
```

### Response

```json
{
    "success": true,
    "payUrl": "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?vnp_Version=2.1.0&vnp_TmnCode=1VYBIYQP&..."
}
```

### Callback từ VNPAY

```
GET /vnpay-callback?
    vnp_Amount=270000000
    vnp_BankCode=NCB
    vnp_ResponseCode=00
    vnp_TxnRef=BOOK1126
    vnp_TransactionNo=14869839
    vnp_SecureHash=...
```

---

## 💡 Mẹo Hay

1. **Log Callback**: Thêm `Log::info('VNPAY: ', $request->all());` trong `vnpayCallback()`
2. **Test Local**: Dùng ngrok: `ngrok http 8000`
3. **Debug Hash**: Thêm `dd($hashData, $secureHash);` để so sánh
4. **Monitor**: Kiểm tra `storage/logs/laravel.log` khi test

---

## ✅ Checklist Nhanh

- [ ] `.env` có cấu hình VNPAY
- [ ] Route `/vnpay-payment` & `/vnpay-callback` tồn tại
- [ ] Controller có 3 method VNPAY
- [ ] View có tùy chọn VNPAY
- [ ] Database có bảng `thanhtoan` với `phuongthuc enum(...,'vnpay')`
- [ ] Chạy `php artisan route:cache`
- [ ] Test 1 quy trình hoàn chỉnh
- [ ] Kiểm tra database sau thanh toán

---

## 🔗 Tài Liệu Chi Tiết

Để biết thêm chi tiết, xem:
- **Hướng dẫn đầy đủ**: [VNPAY_INTEGRATION_GUIDE.md](VNPAY_INTEGRATION_GUIDE.md)
- **Chi tiết callback**: [VNPAY_CALLBACK_DETAILS.md](VNPAY_CALLBACK_DETAILS.md)
- **Schema database**: [VNPAY_DATABASE_STRUCTURE.sql](VNPAY_DATABASE_STRUCTURE.sql)
- **Checklist**: [VNPAY_VERIFICATION_CHECKLIST.md](VNPAY_VERIFICATION_CHECKLIST.md)

---

## 🎬 Video Tutorial (Tưởng Tượng)

```
00:00 - Giới thiệu
01:00 - Cấu hình .env
02:00 - Kiểm tra routes
03:00 - Test đặt tour
04:00 - Thanh toán VNPAY
05:00 - Kiểm tra database
```

---

## 📞 Hỗ Trợ Nhanh

| Câu Hỏi | Trả Lời |
|--------|--------|
| Làm sao test thanh toán? | Dùng thẻ test VNPAY sandbox |
| Callback có được gọi không? | Kiểm tra log: `tail -f storage/logs/laravel.log` |
| Database không update? | Kiểm tra chữ ký xác minh |
| Số tiền sai? | Nhân `giacuoi` × 100 |
| Redirect không work? | Kiểm tra `VNPAY_URL` & `VNPAY_RETURN_URL` |

---

## 🎯 Next Steps

1. ✅ Hoàn thành integration cơ bản
2. 🧪 Test trên sandbox
3. 🔐 Thêm bảo mật (IP whitelist, etc.)
4. 📊 Thiết lập monitoring
5. 🚀 Deploy production

---

**Time to Complete**: 5-10 minutes  
**Difficulty**: ⭐⭐☆☆☆ (Easy)  
**Last Updated**: 2025-05-04
