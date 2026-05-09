# ⚠️ VNPAY Integration - Cấu Hình Cần Thiết

## 🔐 Mã VNPAY Cần Cung Cấp

### 1. Cấu Hình VNPAY trong `.env`

Thay thế các mã này bằng mã thực của bạn từ tài khoản VNPAY:

```bash
# VNPAY Payment Configuration
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
VNPAY_TMN_CODE=1VYBIYQP                          # ← Mã merchant của bạn
VNPAY_HASH_SECRET=NOH6MBGNLQL9O9OMMFMZ2AX8NIEP50W1  # ← Chuỗi bí mật của bạn
VNPAY_RETURN_URL=http://127.0.0.1:8000/vnpay-callback
```

### 2. Cách Lấy Mã VNPAY

#### Bước 1: Đăng ký tài khoản VNPAY
- Truy cập: https://sandbox.vnpayment.vn (Sandbox để test)
- Hoặc: https://admin.vnpay.vn (Production)

#### Bước 2: Lấy Mã Merchant (TMN_CODE)
1. Đăng nhập vào tài khoản VNPAY
2. Vào "Cài đặt" → "Chung"
3. Tìm "Mã Merchant" (Merchant Code) → Copy

#### Bước 3: Lấy Chuỗi Bí Mật (HASH_SECRET)
1. Vào "Cài đặt" → "API"
2. Tìm "Chuỗi bí mật" (Secret Key) → Copy
3. ⚠️ **Giữ bí mật chuỗi này, đừng share công khai!**

#### Bước 4: Cấu Hình IP Whitelist
1. Vào "Cài đặt" → "API" → "IP Whitelist"
2. Thêm IP server của bạn
3. Ví dụ: `192.168.1.100` hoặc `your.server.ip`

---

## 🔄 Quy Trình Thanh Toán Đơn Giản

```
1. Khách điền form → Chọn "Chuyển khoản ngân hàng (VNPAY)"
2. Nhấn "Xác nhận đặt tour"
3. Trang tour-booked hiển thị
4. Nhấn nút "Thanh toán VNPAY"
5. Redirect sang VNPAY gateway
6. Nhập thông tin thẻ → Xác thực
7. VNPAY callback → Update database
8. Redirect tour-booked + thông báo kết quả
```

---

## 💳 Thẻ Test VNPAY Sandbox

Để test thanh toán, sử dụng thông tin thẻ test từ VNPAY:

### ATM Card (Thẻ ATM)
```
Số thẻ: 9704198526191432198
Tên chủ: NGUYEN VAN A
Ngày hết hạn: 07/15
CVV: 123
OTP: 123456
```

### Credit Card
```
Số thẻ: 4111111111111111
Tên chủ: NGUYEN VAN A
Ngày hết hạn: 12/25
CVV: 123
```

---

## 🚀 Hướng Dẫn Deploy

### 1. **Local/Development**
```env
VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html
VNPAY_TMN_CODE=<sandbox_code>
VNPAY_HASH_SECRET=<sandbox_secret>
VNPAY_RETURN_URL=http://localhost:8000/vnpay-callback
```

### 2. **Production**
```env
VNPAY_URL=https://pay.vnpay.vn/vpcpay.html
VNPAY_TMN_CODE=<production_code>
VNPAY_HASH_SECRET=<production_secret>
VNPAY_RETURN_URL=https://yourdomain.com/vnpay-callback
```

---

## 🧪 Kiểm Tra Nhanh

### Bước 1: Cập nhật .env
```bash
VNPAY_TMN_CODE=1VYBIYQP
VNPAY_HASH_SECRET=NOH6MBGNLQL9O9OMMFMZ2AX8NIEP50W1
```

### Bước 2: Clear Cache
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Bước 3: Test
```
1. Truy cập: http://127.0.0.1:8000/tours
2. Đặt tour → Chọn "Chuyển khoản ngân hàng (VNPAY)"
3. Nhấn "Xác nhận đặt tour"
4. Nhấn "Thanh toán VNPAY"
5. Điền thẻ test
6. Kiểm tra database
```

---

## 📁 Files Chính

| File | Mô Tả |
|------|-------|
| [app/Http/Controllers/clients/VnpayPaymentController.php](app/Http/Controllers/clients/VnpayPaymentController.php) | Controller VNPAY (đơn giản) |
| [routes/web.php](routes/web.php) | Routes VNPAY |
| [resources/views/clients/booking.blade.php](resources/views/clients/booking.blade.php) | Form booking (chọn VNPAY) |
| [resources/views/clients/tour-booked-new.blade.php](resources/views/clients/tour-booked-new.blade.php) | Trang xác nhận & thanh toán |
| [.env](.env) | Cấu hình VNPAY |

---

## 🐛 Troubleshooting

| Vấn Đề | Giải Pháp |
|--------|----------|
| "Chữ ký không hợp lệ" | Kiểm tra `VNPAY_HASH_SECRET` chính xác |
| "Không tìm thấy giao dịch" | Kiểm tra `magiaodich` trong database |
| "Redirect VNPAY thất bại" | Kiểm tra `VNPAY_TMN_CODE` chính xác |
| "Callback không được nhận" | Kiểm tra `VNPAY_RETURN_URL` công khai |
| "Thiếu thông tin khách" | Cập nhật hồ sơ tại trang Thông tin cá nhân |

---

## 📞 Hỗ Trợ VNPAY

- **VNPAY Sandbox**: https://sandbox.vnpayment.vn
- **VNPAY Admin**: https://admin.vnpay.vn
- **VNPAY API Docs**: https://sandbox.vnpayment.vn/apis/
- **Email Support**: support@vnpay.vn

---

## ✅ Checklist Cấu Hình

- [ ] Copy `VNPAY_TMN_CODE` vào `.env`
- [ ] Copy `VNPAY_HASH_SECRET` vào `.env`
- [ ] Cập nhật hồ sơ khách hàng (email/SDT/địa chỉ)
- [ ] Chạy `php artisan config:cache`
- [ ] Test 1 quy trình hoàn chỉnh
- [ ] Kiểm tra database có dữ liệu

---

**Cập nhật lần cuối**: 2026-05-04  
**Phiên bản**: 1.0 (Simple & Easy)
