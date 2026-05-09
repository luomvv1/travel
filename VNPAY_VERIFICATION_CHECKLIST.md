# ✅ VNPAY Integration - Verification Checklist

## 🔍 Kiểm Tra Cấu Hình

### 1. Environment Variables (.env)
- [ ] `VNPAY_URL` = `https://sandbox.vnpayment.vn/paymentv2/vpcpay.html`
- [ ] `VNPAY_TMN_CODE` = `1VYBIYQP` (hoặc code của bạn)
- [ ] `VNPAY_HASH_SECRET` = `NOH6MBGNLQL9O9OMMFMZ2AX8NIEP50W1` (hoặc secret của bạn)
- [ ] `VNPAY_RETURN_URL` = `http://127.0.0.1:8000/vnpay-callback`

```bash
# Kiểm tra từ terminal
grep VNPAY .env
```

### 2. Routes (routes/web.php)
- [ ] Route `POST /vnpay-payment` tồn tại
- [ ] Route `GET /vnpay-callback` tồn tại
- [ ] Cả hai route được gán đúng controller method

```bash
# Kiểm tra từ terminal
php artisan route:list | grep vnpay
```

### 3. Controller (BookingController.php)
- [ ] Method `vnpayPayment()` tồn tại
- [ ] Method `vnpayCallback()` tồn tại
- [ ] Method `buildVnpayPaymentUrl()` tồn tại
- [ ] Validation có `'vnpay'` trong payment options
- [ ] Match statement có `'vnpay' => 'vnpay'`

```bash
# Kiểm tra từ terminal
grep -n "vnpay" app/Http/Controllers/clients/BookingController.php
```

### 4. Views
- [ ] booking.blade.php có tùy chọn "Thanh toán bằng VNPAY"
- [ ] tour-booked.blade.php có nút "Thanh toán VNPAY"
- [ ] Form VNPAY gửi POST tới `route('vnpay.payment')`

```bash
# Kiểm tra booking view
grep -n "vnpay" resources/views/clients/booking.blade.php

# Kiểm tra tour-booked view
grep -n "vnpay" resources/views/clients/tour-booked.blade.php
```

### 5. Database
- [ ] Bảng `thanhtoan` tồn tại
- [ ] Column `phuongthuc` chứa giá trị `enum(..., 'vnpay')`
- [ ] Column `trangthai` chứa giá trị `enum(..., 'thanh_cong', 'that_bai')`
- [ ] Có foreign key từ `thanhtoan.dtid` tới `dattour.dtid`

```bash
# Kiểm tra từ terminal - MySQL
mysql -u root -p travel -e "DESC thanhtoan;"
mysql -u root -p travel -e "SHOW CREATE TABLE thanhtoan\G"
```

### 6. JavaScript
- [ ] File `resources/js/vnpay-payment.js` tồn tại
- [ ] Có hàm `paymentVnpay()`
- [ ] Có hàm `paymentMomo()`

```bash
# Kiểm tra từ terminal
grep -n "function payment" resources/js/vnpay-payment.js
```

### 7. Documentation
- [ ] File `VNPAY_INTEGRATION_GUIDE.md` tồn tại
- [ ] File `VNPAY_CALLBACK_DETAILS.md` tồn tại
- [ ] File `VNPAY_DATABASE_STRUCTURE.sql` tồn tại
- [ ] File `VNPAY_IMPLEMENTATION_SUMMARY.md` tồn tại

---

## 🧪 Testing Checklist

### Quy Trình Test Cơ Bản

**Test Case 1: Đặt Tour Với VNPAY**

1. [ ] Truy cập trang booking: `/booking/{id}`
2. [ ] Điền form:
   - [ ] Họ tên
   - [ ] Email
   - [ ] Số điện thoại
   - [ ] Địa chỉ
   - [ ] Chọn lịch khởi hành
   - [ ] Chọn số người lớn & trẻ em
   - [ ] **Chọn "Thanh toán bằng VNPAY"**
   - [ ] Đồng ý điều khoản
3. [ ] Nhấn nút "Xác nhận đặt tour"
4. [ ] Kiểm tra:
   - [ ] Redirect tới `/tour-booked/{id}`
   - [ ] Hiển thị thông tin đơn đặt tour
   - [ ] Có nút "Thanh toán VNPAY" (nếu chưa thanh toán)

**Test Case 2: Thanh Toán VNPAY**

1. [ ] Trên trang `tour-booked`, nhấn nút "Thanh toán VNPAY"
2. [ ] Được redirect tới VNPAY payment gateway
3. [ ] URL chứa tham số: `vnp_Amount`, `vnp_TxnRef`, `vnp_OrderInfo`, etc.
4. [ ] Kiểm tra form VNPAY hiển thị đúng
5. [ ] Điền thông tin thẻ test (từ VNPAY sandbox)
6. [ ] Xác nhận thanh toán

**Test Case 3: Callback Từ VNPAY**

1. [ ] Sau khi thanh toán thành công, VNPAY redirect về:
   ```
   /vnpay-callback?vnp_Amount=...&vnp_ResponseCode=00&...
   ```
2. [ ] Kiểm tra redirect tới `/tour-booked/{id}` với thông báo thành công
3. [ ] Kiểm tra database:
   - [ ] Bảng `thanhtoan`: `trangthai` = `'thanh_cong'`
   - [ ] Bảng `dattour`: `trangthai` = `'da_thanh_toan'`

**Test Case 4: Thanh Toán Thất Bại**

1. [ ] Test với thanh toán thất bại (response code ≠ 00)
2. [ ] Kiểm tra redirect tới `/tour-booked/{id}` với thông báo lỗi
3. [ ] Kiểm tra database: `thanhtoan.trangthai` = `'that_bai'`
4. [ ] Có thể thử thanh toán lại

### Database Verification

```sql
-- Kiểm tra đơn đặt tour
SELECT * FROM dattour WHERE dtid = 1;

-- Kiểm tra thanh toán
SELECT * FROM thanhtoan WHERE dtid = 1;

-- Kiểm tra thanh toán VNPAY thành công
SELECT * FROM thanhtoan 
WHERE phuongthuc = 'vnpay' AND trangthai = 'thanh_cong';

-- Kiểm tra thanh toán VNPAY đang chờ
SELECT * FROM thanhtoan 
WHERE phuongthuc = 'vnpay' AND trangthai = 'cho_xu_ly';

-- Kiểm tra thanh toán VNPAY thất bại
SELECT * FROM thanhtoan 
WHERE phuongthuc = 'vnpay' AND trangthai = 'that_bai';
```

### Log Verification

```bash
# Xem log real-time
tail -f storage/logs/laravel.log

# Tìm lỗi VNPAY
grep -i "vnpay" storage/logs/laravel.log
```

---

## 🔐 Security Checklist

- [ ] VNPAY_HASH_SECRET không được public trên code
- [ ] Chữ ký được xác minh trong `vnpayCallback()`
- [ ] Không update database nếu chữ ký không hợp lệ
- [ ] Kiểm tra `vnp_ResponseCode` trước khi cập nhật
- [ ] Log tất cả callback từ VNPAY
- [ ] Xử lý duplicate callback

### Kiểm Tra Hash Verification

```php
// Trong vnpayCallback():
if ($secureHash !== $vnp_SecureHash) {
    return redirect()->route('home')->with('error', 'Chữ ký xác thực không hợp lệ!');
}
```

- [ ] Xác minh được thực hiện trước khi xử lý
- [ ] Dùng HMAC-SHA512 (không phải MD5 hay SHA1)
- [ ] Tham số được sắp xếp theo thứ tự chữ cái (ksort)

---

## 🚀 Deployment Checklist

### Trước Production

- [ ] Cập nhật cấu hình VNPAY production:
  ```
  VNPAY_URL=https://pay.vnpay.vn/vpcpay.html
  VNPAY_TMN_CODE=<production_code>
  VNPAY_HASH_SECRET=<production_secret>
  VNPAY_RETURN_URL=https://yourdomain.com/vnpay-callback
  ```

- [ ] Cấu hình IP whitelist trên admin.vnpay.vn
  - [ ] Thêm IP server production

- [ ] Cấu hình DNS & SSL
  - [ ] HTTPS sẵn sàng
  - [ ] Domain hoạt động

- [ ] Test toàn bộ quy trình trên production
  - [ ] Đặt tour
  - [ ] Thanh toán
  - [ ] Kiểm tra database

- [ ] Backup database trước khi deploy
  ```bash
  mysqldump -u root -p travel > backup_before_deploy.sql
  ```

---

## 🐛 Troubleshooting Checklist

### Nếu Gặp Lỗi "Chữ ký xác thực không hợp lệ"

- [ ] Kiểm tra `VNPAY_HASH_SECRET` trong .env
- [ ] Kiểm tra các tham số callback từ VNPAY
- [ ] Kiểm tra log xem tham số nào bị lỗi
- [ ] Thử tính toán hash thủ công để so sánh

### Nếu Gặp Lỗi "Không tìm thấy thông tin giao dịch"

- [ ] Kiểm tra `magiaodich` (vnp_TxnRef) có trong database không
- [ ] Kiểm tra SQL query: `SELECT * FROM thanhtoan WHERE magiaodich = '...'`

### Nếu Callback Không Được Nhận

- [ ] Kiểm tra URL callback có công khai (không localhost)
- [ ] Kiểm tra IP whitelist trên admin.vnpay.vn
- [ ] Kiểm tra firewall/WAF có block request từ VNPAY không
- [ ] Kiểm tra log web server (Apache/Nginx)

### Nếu Số Tiền Không Đúng

- [ ] Kiểm tra `vnp_Amount` = `giacuoi * 100`
- [ ] Ví dụ: 2,700,000 VNĐ → 270,000,000 (×100)

### Nếu Redirect Thất Bại

- [ ] Kiểm tra URL format trong `buildVnpayPaymentUrl()`
- [ ] Kiểm tra encoding của tham số
- [ ] Kiểm tra `VNPAY_URL` đúng

---

## 📊 Performance Checklist

- [ ] Database queries được optimize
- [ ] Không có N+1 query problems
- [ ] Callback được xử lý nhanh (< 1 giây)
- [ ] Không timeout trong quá trình thanh toán

### Query Performance

```bash
# Bật query logging
php artisan tinker
> DB::connection()->enableQueryLog();
> // Run queries...
> dd(DB::getQueryLog());
```

---

## 📈 Monitoring Checklist

- [ ] Set up monitoring cho route `/vnpay-callback`
- [ ] Alert nếu có lỗi 5xx
- [ ] Track số lượng callback thành công/thất bại
- [ ] Track thời gian xử lý callback

### Log Format Example

```
[2025-03-26 13:09:21] production.INFO: VNPAY Callback Received
[dtid] => 1
[vnp_ResponseCode] => 00
[vnp_Amount] => 270000000
[vnp_TxnRef] => BOOK1126
[vnp_TransactionNo] => 14869839
[status] => thanh_cong
```

---

## ✅ Final Verification

After completing all checklists:

- [ ] Tất cả file đã được tạo/chỉnh sửa
- [ ] Tất cả route được đăng ký
- [ ] Controller methods hoạt động
- [ ] Database cấu trúc đúng
- [ ] Environment variables được thiết lập
- [ ] Test cases đều pass
- [ ] Security checks được verify
- [ ] Tài liệu đầy đủ
- [ ] Sẵn sàng production deploy

---

## 📞 Support & References

- **VNPAY Sandbox**: https://sandbox.vnpayment.vn
- **VNPAY Admin**: https://admin.vnpay.vn
- **Integration Guide**: [VNPAY_INTEGRATION_GUIDE.md](VNPAY_INTEGRATION_GUIDE.md)
- **Callback Details**: [VNPAY_CALLBACK_DETAILS.md](VNPAY_CALLBACK_DETAILS.md)
- **Database Schema**: [VNPAY_DATABASE_STRUCTURE.sql](VNPAY_DATABASE_STRUCTURE.sql)

---

**Last Updated**: 2025-05-04  
**Version**: 1.0  
**Status**: ✅ Ready for Testing
