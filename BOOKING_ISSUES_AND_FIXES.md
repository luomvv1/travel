# Hướng Dẫn Sửa Trang Đặt Tour - Booking Issues

## 🔴 VẤN ĐỀ PHÁT HIỆN

### 1. **Mismatch Database Schema**
- **Model cũ** dùng table `tbl_booking` (không tồn tại)
- **Database thực tế** dùng table `dattour` với fields: `dtid`, `ndid`, `tourid`, `lichid`, `ngaydat`, `songuoilon`, `sotreem`, `tongtien`, `giamgia`, `giacuoi`, `makhuyenmai`, `trangthai`
- **Status enum** trong DB: `cho_xac_nhan`, `da_xac_nhan`, `da_thanh_toan`, `da_huy`

### 2. **Form và Route Mismatch**
- **Form gửi**: `schedule` (lichid), `adults`, `children`, `fullname`, `email`, `phone`, `address`, `promo`, `payment_method`
- **Route muốn**: `lichid` (khác tên field!)
- **Form không gửi**: `lichid`, `tourid`, `ndid` (user ID từ session), `ngaydat`

### 3. **Route Placeholder**
- `/booking/{id}` hiện là closure function, không tạo dattour record
- Chỉ check lịch tồn tại nhưng không thực sự booking
- Không validate sochocon (available slots)
- Không decrement sochocon sau booking
- Không create thanhtoan record

### 4. **Booking Form Issues**
- **Promo button** không có JavaScript handler
- **Price calculation** không xử lý discount
- Không validate form trên client-side (check sochocon)
- Không lấy user ID từ session
- Không gửi tour ID

### 5. **Payment Method**
- Form dùng: `bank-transfer`, `momo-payment`, `paypal-payment`
- Database thanhtoan.phuongthuc enum: `momo`, `zalopay`, `chuyen_khoan`, `tien_mat`, `stripe`, `paypal`
- ❌ Không match! `bank-transfer` ≠ `chuyen_khoan`, v.v.

### 6. **Missing Features**
- Không kiểm tra promo code có hợp lệ không
- Không validate số người (songuoilon + sotreem > sochocon)
- Không decrement sochocon khi booking thành công
- Không tạo thanhtoan record

## ✅ CÁC BƯỚC SỬA (Chi Tiết)

### BƯỚC 1: Sửa booking.blade.php

**Thêm hidden fields:**
```html
<!-- Hidden fields cho session/tour info -->
<input type="hidden" name="lichid" id="lichid" value="">
<input type="hidden" name="tourid" value="{{ $tour->tourid }}">
<input type="hidden" name="ndid" value="{{ session('ndid', '') }}">
```

**Fix promo code handler:**
```javascript
// Khi click "Áp dụng" nút promo
document.querySelector('.promo-code-wrap button').addEventListener('click', function() {
    const promoCode = document.getElementById('promo').value;
    if (!promoCode) return;
    
    // Gọi API để validate và lấy discount
    fetch('/check-promo', {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value},
        body: JSON.stringify({promo: promoCode})
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            // Apply discount
        }
    });
});
```

**Update schedule selection:**
```javascript
// Khi thay đổi schedule, cập nhật lichid
document.getElementById('schedule').addEventListener('change', function() {
    document.getElementById('lichid').value = this.value;
    
    // Validate sochocon
    const slots = this.options[this.selectedIndex].dataset.slots;
    const totalPeople = parseInt(document.getElementById('adults').value) + 
                       parseInt(document.getElementById('children').value);
    
    if (totalPeople > slots) {
        alert('Số người vượt quá chỗ còn trống!');
        this.value = '';
        document.getElementById('lichid').value = '';
    }
});
```

### BƯỚC 2: Sửa routes/web.php

**Replace placeholder route:**
```php
Route::post('/booking/{id}', function (Request $request, $id) {
    // 1. Get user từ session
    $ndid = session('ndid');
    if (!$ndid) {
        return redirect()->route('login')->with('error', 'Vui lòng đăng nhập trước');
    }

    // 2. Validate input
    $lichid = $request->input('lichid');
    $adults = $request->input('adults', 0);
    $children = $request->input('children', 0);
    $promoCode = $request->input('promo');
    $paymentMethod = $request->input('payment_method');
    
    if (!$lichid || $adults < 1) {
        return redirect()->back()->with('error', 'Vui lòng chọn lịch và số người lớn');
    }

    // 3. Validate lịch khởi hành
    $schedule = DB::table('lichkhoihanh')
        ->where('lichid', $lichid)
        ->where('tourid', $id)
        ->first();

    if (!$schedule) {
        return redirect()->back()->with('error', 'Lịch khởi hành không hợp lệ');
    }

    // 4. Validate slots còn trống
    $totalPeople = $adults + $children;
    if ($schedule->sochocon < $totalPeople) {
        return redirect()->back()->with('error', 'Không đủ chỗ trống cho số người này');
    }

    // 5. Lấy giá tour
    $tour = DB::table('tour')->where('tourid', $id)->first();
    $tongtien = ($adults * $tour->gianguoilon) + ($children * $tour->giatreem);

    // 6. Validate và tính discount từ promo
    $discount = 0;
    $finalPromo = null;
    if ($promoCode) {
        $promo = DB::table('khuyenmai')
            ->where('macode', $promoCode)
            ->where('danghoatdong', 'Y')
            ->where('ngaybatdau', '<=', now())
            ->where('ngayketthuc', '>=', now())
            ->first();

        if ($promo) {
            if ($promo->loaigiam === 'phan_tram') {
                $discount = $tongtien * ($promo->giatri / 100);
            } else {
                $discount = $promo->giatri;
            }
            $discount = min($discount, $tongtien); // Không discount > giá gốc
            $finalPromo = $promoCode;
        }
    }

    $giacuoi = $tongtien - $discount;

    // 7. Create booking trong dattour
    $dtid = DB::table('dattour')->insertGetId([
        'ndid' => $ndid,
        'tourid' => $id,
        'lichid' => $lichid,
        'ngaydat' => now()->toDateString(),
        'songuoilon' => $adults,
        'sotreem' => $children,
        'tongtien' => $tongtien,
        'giamgia' => $discount,
        'giacuoi' => $giacuoi,
        'makhuyenmai' => $finalPromo,
        'trangthai' => 'cho_xac_nhan'
    ]);

    // 8. Decrement sochocon
    DB::table('lichkhoihanh')
        ->where('lichid', $lichid)
        ->decrement('sochocon', $totalPeople);

    // 9. Create thanhtoan record (pending)
    $phuongthuc = match($paymentMethod) {
        'bank-transfer' => 'chuyen_khoan',
        'momo-payment' => 'momo',
        'paypal-payment' => 'paypal',
        default => 'tien_mat'
    };

    DB::table('thanhtoan')->insert([
        'dtid' => $dtid,
        'phuongthuc' => $phuongthuc,
        'sotien' => $giacuoi,
        'trangthai' => 'cho_xu_ly'
    ]);

    return redirect()->route('my-tours')
        ->with('success', 'Đặt tour thành công! Vui lòng hoàn tất thanh toán.');
})->name('booking');
```

### BƯỚC 3: Fix booking.blade.php Form

**Update form action:**
```html
<form id="booking-form" action="{{ route('booking', ['id' => $tour->tourid]) }}" method="POST">
    @csrf
    
    <!-- Hidden fields -->
    <input type="hidden" name="tourid" value="{{ $tour->tourid }}">
    <input type="hidden" name="ndid" value="{{ session('ndid', '') }}">
    <input type="hidden" name="lichid" id="lichid" value="">
    
    <!-- ... rest of form ... -->
```

**Update schedule onChange:**
```javascript
document.getElementById('schedule').addEventListener('change', function() {
    document.getElementById('lichid').value = this.value; // Update hidden field
});
```

**Update payment method mapping:**
```javascript
// Khi submit form, map payment methods đúng
document.getElementById('booking-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const methodValue = document.querySelector('input[name="payment_method"]:checked').value;
    const methodMap = {
        'bank-transfer': 'chuyen_khoan',
        'momo-payment': 'momo',
        'paypal-payment': 'paypal'
    };
    
    // Optional: có thể override nếu cần, hoặc để form gửi như cũ
    // và server sẽ map
    
    this.submit();
});
```

## 📋 DATABASE VALIDATION CHECKLIST

```sql
-- 1. Check khuyến mãi đã tồn tại
SELECT * FROM khuyenmai WHERE macode IN ('FAMILY500K', 'SUMMER2026');

-- 2. Check lịch khởi hành
SELECT * FROM lichkhoihanh WHERE trangthai = 'con_cho';

-- 3. Check booking đã tạo
SELECT * FROM dattour WHERE ndid = 1;

-- 4. Check thanh toán
SELECT * FROM thanhtoan WHERE dtid = (SELECT MAX(dtid) FROM dattour);
```

## 🔧 REQUIRED CHANGES SUMMARY

| Item | Current | Should Be | Priority |
|------|---------|-----------|----------|
| **Booking Model** | Uses `tbl_booking` | Use `dattour` | HIGH |
| **Form field** | `schedule` sent | Send `lichid` | HIGH |
| **Payment method** | `bank-transfer` | Map to `chuyen_khoan` | HIGH |
| **Promo handler** | No JavaScript | Add validation endpoint | MEDIUM |
| **Discount calc** | Not working | Implement promo calc | MEDIUM |
| **Sochocon check** | No validation | Check + decrement | HIGH |
| **User session** | Not sent | Add `ndid` hidden field | HIGH |

## 🎯 NEXT STEPS

1. ✅ Update booking.blade.php with hidden fields and JS handlers
2. ✅ Replace route placeholder with full booking logic
3. ✅ Add `/check-promo` endpoint for promo validation (optional but better UX)
4. ✅ Test booking creation in database
5. ✅ Verify sochocon decrements correctly
6. ✅ Test payment method mapping
7. ✅ Validate thanhtoan record created
