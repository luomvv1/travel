-- ============================================================================
-- Database Structure for VNPAY Payment Integration
-- ============================================================================
-- Phần này mô tả cấu trúc database sử dụng cho thanh toán VNPAY
-- ============================================================================

-- ============================================================================
-- 1. BẢNG DATTOUR (Bảng đơn đặt tour)
-- ============================================================================
-- Lưu thông tin đơn đặt tour của khách hàng

CREATE TABLE IF NOT EXISTS `dattour` (
  `dtid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID đơn đặt tour (khóa chính)',
  `ndid` int(11) NOT NULL COMMENT 'ID người dùng (khóa ngoài)',
  `tourid` int(11) NOT NULL COMMENT 'ID tour (khóa ngoài)',
  `lichid` int(11) NOT NULL COMMENT 'ID lịch khởi hành (khóa ngoài)',
  `ngaydat` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày đặt tour',
  `songuoilon` int(11) NOT NULL COMMENT 'Số người lớn',
  `sotreem` int(11) NOT NULL DEFAULT 0 COMMENT 'Số trẻ em',
  `tongtien` decimal(12,2) NOT NULL COMMENT 'Tổng tiền (chưa giảm)',
  `giamgia` decimal(12,2) NOT NULL DEFAULT 0 COMMENT 'Số tiền giảm',
  `giacuoi` decimal(12,2) NOT NULL COMMENT 'Giá cuối cùng (sau giảm)',
  `makhuyenmai` varchar(50) DEFAULT NULL COMMENT 'Mã khuyến mãi',
  `trangthai` enum('cho_xac_nhan','da_xac_nhan','da_thanh_toan','da_huy') NOT NULL DEFAULT 'cho_xac_nhan' COMMENT 'Trạng thái đơn đặt tour',
  PRIMARY KEY (`dtid`),
  KEY `ndid` (`ndid`),
  KEY `tourid` (`tourid`),
  KEY `lichid` (`lichid`),
  KEY `trangthai` (`trangthai`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='Bảng thông tin đơn đặt tour';

-- ============================================================================
-- 2. BẢNG THANHTOAN (Bảng thanh toán)
-- ============================================================================
-- Lưu thông tin giao dịch thanh toán từ VNPAY, MoMo hoặc các phương thức khác

CREATE TABLE IF NOT EXISTS `thanhtoan` (
  `ttid` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID thanh toán (khóa chính)',
  `dtid` int(11) NOT NULL COMMENT 'ID đơn đặt tour (khóa ngoài)',
  `phuongthuc` enum('tai_van_phong','chuyen_khoan','momo','vnpay') NOT NULL DEFAULT 'tai_van_phong' COMMENT 'Phương thức thanh toán',
  `sotien` decimal(12,2) NOT NULL COMMENT 'Số tiền thanh toán',
  `magiaodich` varchar(100) NOT NULL COMMENT 'Mã giao dịch từ cổng thanh toán',
  `trangthai` enum('cho_xu_ly','thanh_cong','that_bai') NOT NULL DEFAULT 'cho_xu_ly' COMMENT 'Trạng thái thanh toán',
  `ngaythanhtoan` datetime DEFAULT NULL COMMENT 'Ngày thanh toán thành công',
  `ngaytao` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Ngày tạo bản ghi',
  PRIMARY KEY (`ttid`),
  UNIQUE KEY `magiaodich` (`magiaodich`),
  KEY `dtid` (`dtid`),
  KEY `phuongthuc` (`phuongthuc`),
  KEY `trangthai` (`trangthai`),
  FOREIGN KEY (`dtid`) REFERENCES `dattour` (`dtid`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COMMENT='Bảng thông tin giao dịch thanh toán';

-- ============================================================================
-- 3. QUERY CHO PHÉP THANH TOÁN (VNPAY INTEGRATION)
-- ============================================================================

-- Xem thông tin đơn đặt tour cùng trạng thái thanh toán
SELECT 
    dt.dtid,
    dt.songuoilon,
    dt.sotreem,
    dt.tongtien,
    dt.giamgia,
    dt.giacuoi,
    dt.trangthai AS trangthai_dattour,
    tt.ttid,
    tt.phuongthuc,
    tt.sotien,
    tt.magiaodich,
    tt.trangthai AS trangthai_thanhtoan,
    tt.ngaythanhtoan
FROM dattour dt
LEFT JOIN thanhtoan tt ON dt.dtid = tt.dtid
WHERE dt.dtid = 1;

-- Xem danh sách thanh toán VNPAY đã thành công
SELECT 
    tt.ttid,
    tt.dtid,
    tt.sotien,
    tt.magiaodich,
    tt.ngaythanhtoan,
    dt.songuoilon,
    dt.sotreem,
    dt.giacuoi
FROM thanhtoan tt
JOIN dattour dt ON tt.dtid = dt.dtid
WHERE tt.phuongthuc = 'vnpay' AND tt.trangthai = 'thanh_cong'
ORDER BY tt.ngaythanhtoan DESC;

-- Xem danh sách thanh toán VNPAY đang chờ xử lý
SELECT 
    tt.ttid,
    tt.dtid,
    tt.sotien,
    tt.magiaodich,
    tt.ngaytao,
    dt.songuoilon,
    dt.sotreem,
    dt.giacuoi
FROM thanhtoan tt
JOIN dattour dt ON tt.dtid = dt.dtid
WHERE tt.phuongthuc = 'vnpay' AND tt.trangthai = 'cho_xu_ly'
ORDER BY tt.ngaytao DESC;

-- Xem danh sách thanh toán VNPAY thất bại
SELECT 
    tt.ttid,
    tt.dtid,
    tt.sotien,
    tt.magiaodich,
    tt.ngaytao,
    dt.songuoilon,
    dt.sotreem,
    dt.giacuoi
FROM thanhtoan tt
JOIN dattour dt ON tt.dtid = dt.dtid
WHERE tt.phuongthuc = 'vnpay' AND tt.trangthai = 'that_bai'
ORDER BY tt.ngaytao DESC;

-- ============================================================================
-- 4. DỮ LIỆU MẪU (DỮ LIỆU TEST)
-- ============================================================================

-- Thêm dữ liệu mẫu cho bảng dattour
INSERT INTO `dattour` (
    `ndid`,
    `tourid`,
    `lichid`,
    `ngaydat`,
    `songuoilon`,
    `sotreem`,
    `tongtien`,
    `giamgia`,
    `giacuoi`,
    `makhuyenmai`,
    `trangthai`
) VALUES (
    1,
    1,
    1,
    NOW(),
    2,
    1,
    3000000,
    300000,
    2700000,
    'SUMMER2025',
    'cho_xac_nhan'
);

-- Thêm dữ liệu mẫu cho bảng thanhtoan (VNPAY)
INSERT INTO `thanhtoan` (
    `dtid`,
    `phuongthuc`,
    `sotien`,
    `magiaodich`,
    `trangthai`,
    `ngaythanhtoan`,
    `ngaytao`
) VALUES (
    LAST_INSERT_ID(),
    'vnpay',
    2700000,
    'BOOK1126',
    'cho_xu_ly',
    NULL,
    NOW()
);

-- ============================================================================
-- 5. CALLBACKS VÍ DỤ TỪ VNPAY
-- ============================================================================

/*
Dưới đây là ví dụ về callback từ VNPAY khi thanh toán thành công:

URL: /vnpay-callback?
     vnp_Amount=270000000&
     vnp_BankCode=NCB&
     vnp_BankTranNo=VNP14869839&
     vnp_CardType=ATM&
     vnp_OrderInfo=Thanh+toan+tour+%231&
     vnp_PayDate=20250326130921&
     vnp_ResponseCode=00&
     vnp_TmnCode=1VYBIYQP&
     vnp_TransactionNo=14869839&
     vnp_TransactionStatus=00&
     vnp_TxnRef=BOOK1126&
     vnp_SecureHash=4b3533435c89e8742701a660c1be9a12e779abe72f10c6a5a8a14a05db2d5ef29f2e23e919eca87e1f6f76a13e633ca2be99bdb09b47139c7de342c03e81251f

Giải thích:
- vnp_ResponseCode = 00 (thành công)
- vnp_TxnRef = BOOK1126 (mã giao dịch merchant - ID đơn hàng)
- vnp_Amount = 270000000 (số tiền × 100 = 2,700,000 VNĐ)
- vnp_TransactionNo = 14869839 (mã giao dịch VNPAY)
- vnp_PayDate = 20250326130921 (ngày/giờ thanh toán)
- vnp_SecureHash = ... (chữ ký SHA-512 HMAC để xác minh)

Khi nhận callback này:
1. Ứng dụng xác minh chữ ký
2. Kiểm tra vnp_ResponseCode
3. Cập nhật bảng thanhtoan: trangthai = 'thanh_cong'
4. Cập nhật bảng dattour: trangthai = 'da_thanh_toan'
5. Redirect tới trang xác nhận với thông báo thành công
*/

-- ============================================================================
-- 6. THỐNG KÊ VÀ REPORTS
-- ============================================================================

-- Thống kê doanh thu theo phương thức thanh toán
SELECT 
    tt.phuongthuc,
    tt.trangthai,
    COUNT(*) AS so_giao_dich,
    SUM(tt.sotien) AS tong_sotien,
    AVG(tt.sotien) AS sotien_trungbinh
FROM thanhtoan tt
GROUP BY tt.phuongthuc, tt.trangthai
ORDER BY tt.phuongthuc, tt.trangthai;

-- Doanh thu theo ngày
SELECT 
    DATE(tt.ngaythanhtoan) AS ngay_thanhtoan,
    COUNT(*) AS so_giao_dich,
    SUM(tt.sotien) AS tong_sotien
FROM thanhtoan tt
WHERE tt.trangthai = 'thanh_cong' AND tt.phuongthuc = 'vnpay'
GROUP BY DATE(tt.ngaythanhtoan)
ORDER BY DATE(tt.ngaythanhtoan) DESC;

-- Thống kê độc lập: Phần trăm thanh toán thành công
SELECT 
    tt.phuongthuc,
    COUNT(CASE WHEN tt.trangthai = 'thanh_cong' THEN 1 END) AS thanh_cong,
    COUNT(CASE WHEN tt.trangthai = 'that_bai' THEN 1 END) AS that_bai,
    COUNT(CASE WHEN tt.trangthai = 'cho_xu_ly' THEN 1 END) AS cho_xu_ly,
    COUNT(*) AS tong_cong,
    ROUND(
        (COUNT(CASE WHEN tt.trangthai = 'thanh_cong' THEN 1 END) / COUNT(*)) * 100,
        2
    ) AS percent_thanh_cong
FROM thanhtoan tt
WHERE tt.phuongthuc = 'vnpay'
GROUP BY tt.phuongthuc;

-- ============================================================================
-- 7. MAINTENANCE QUERIES
-- ============================================================================

-- Cập nhật trạng thái thanh toán từ chờ xử lý sang thành công (khi biết đã thành công)
UPDATE thanhtoan 
SET trangthai = 'thanh_cong', ngaythanhtoan = NOW()
WHERE magiaodich = 'BOOK1126' AND trangthai = 'cho_xu_ly';

-- Cập nhật trạng thái đơn đặt tour khi thanh toán thành công
UPDATE dattour 
SET trangthai = 'da_thanh_toan'
WHERE dtid = (
    SELECT dtid FROM thanhtoan 
    WHERE magiaodich = 'BOOK1126' AND trangthai = 'thanh_cong'
);

-- Xóa bản ghi test (cẩn thận!)
-- DELETE FROM thanhtoan WHERE magiaodich LIKE 'BOOK%';
-- DELETE FROM dattour WHERE dtid = 1;

-- ============================================================================
-- 8. MIGRATION (Nếu sử dụng Laravel Migration)
-- ============================================================================
/*
Tạo file migration: php artisan make:migration add_vnpay_payment

Nội dung file migration:

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('thanhtoan', function (Blueprint $table) {
            $table->id('ttid');
            $table->unsignedBigInteger('dtid');
            $table->enum('phuongthuc', ['tai_van_phong', 'chuyen_khoan', 'momo', 'vnpay'])
                ->default('tai_van_phong');
            $table->decimal('sotien', 12, 2);
            $table->string('magiaodich', 100)->unique();
            $table->enum('trangthai', ['cho_xu_ly', 'thanh_cong', 'that_bai'])
                ->default('cho_xu_ly');
            $table->datetime('ngaythanhtoan')->nullable();
            $table->timestamps();

            $table->foreign('dtid')
                ->references('dtid')
                ->on('dattour')
                ->onDelete('cascade');

            $table->index('phuongthuc');
            $table->index('trangthai');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('thanhtoan');
    }
};
*/

-- ============================================================================
-- END OF DATABASE STRUCTURE
-- ============================================================================
