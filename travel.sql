-- phpMyAdmin SQL Dump
-- Cấu trúc: Khóa chính và Khóa ngoại trùng tên tuyệt đối

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

CREATE TABLE `danhgia` (
  `dgid` bigint(20) NOT NULL COMMENT 'Mã đánh giá',
  `ndid` bigint(20) NOT NULL COMMENT 'Mã khách hàng',
  `tourid` bigint(20) NOT NULL COMMENT 'Mã tour',
  `dtid` bigint(20) NOT NULL COMMENT 'Mã đặt tour',
  `sosao` int(11) NOT NULL COMMENT 'Điểm đánh giá (1-5)',
  `tieude` varchar(255) DEFAULT NULL COMMENT 'Tiêu đề đánh giá',
  `binhluan` text DEFAULT NULL COMMENT 'Bình luận chi tiết',
  `trangthai` enum('cho_duyet','da_duyet','tu_choi') NOT NULL DEFAULT 'cho_duyet' COMMENT 'Trạng thái duyệt',
  `ngaytao` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Ngày tạo',
  `ngaycapnhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Ngày cập nhật'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

CREATE TABLE `dattour` (
  `dtid` bigint(20) NOT NULL COMMENT 'Mã đặt tour',
  `ndid` bigint(20) NOT NULL COMMENT 'Mã khách hàng',
  `tourid` bigint(20) NOT NULL COMMENT 'Mã tour',
  `lichid` bigint(20) NOT NULL COMMENT 'Mã lịch khởi hành',
  `ngaydat` date NOT NULL COMMENT 'Ngày đặt',
  `songuoilon` int(11) NOT NULL COMMENT 'Số người lớn',
  `sotreem` int(11) NOT NULL DEFAULT 0 COMMENT 'Số trẻ em',
  `tongtien` decimal(10,2) NOT NULL COMMENT 'Tổng tiền (chưa giảm)',
  `giamgia` decimal(10,2) DEFAULT 0.00 COMMENT 'Số tiền giảm',
  `giacuoi` decimal(10,2) NOT NULL COMMENT 'Giá cuối cùng',
  `makhuyenmai` varchar(50) DEFAULT NULL COMMENT 'Mã khuyến mãi áp dụng',
  `trangthai` enum('cho_xac_nhan','da_xac_nhan','da_thanh_toan','da_huy') NOT NULL DEFAULT 'cho_xac_nhan' COMMENT 'Trạng thái',
  `ngaytao` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Ngày tạo',
  `ngaycapnhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Ngày cập nhật'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DELIMITER $$
CREATE TRIGGER `trgdattourcapnhattimestamp` BEFORE UPDATE ON `dattour` FOR EACH ROW BEGIN
  SET NEW.ngaycapnhat = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

CREATE TABLE `hinhanhtour` (
  `hinhid` bigint(20) NOT NULL COMMENT 'Mã hình ảnh',
  `tourid` bigint(20) NOT NULL COMMENT 'Mã tour',
  `urlanh` varchar(500) NOT NULL COMMENT 'URL hình ảnh',
  `tenanh` varchar(255) DEFAULT NULL COMMENT 'Tên ảnh',
  `motaanh` text DEFAULT NULL COMMENT 'Mô tả ảnh',
  `thutuhienthi` int(11) DEFAULT 1 COMMENT 'Thứ tự hiển thị'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng hình ảnh tour';

-- --------------------------------------------------------

CREATE TABLE `hoadon` (
  `hdid` bigint(20) NOT NULL COMMENT 'Mã hóa đơn',
  `dtid` bigint(20) NOT NULL COMMENT 'Mã đặt tour (1-1)',
  `ngayphathanh` date NOT NULL COMMENT 'Ngày phát hành',
  `tongtien` decimal(10,2) NOT NULL COMMENT 'Tổng tiền',
  `ghichu` text DEFAULT NULL COMMENT 'Ghi chú',
  `ngaytao` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Ngày tạo',
  `ngaycapnhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Ngày cập nhật'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

CREATE TABLE `khuyenmai` (
  `macode` varchar(50) NOT NULL COMMENT 'Mã code khuyến mãi',
  `tenkhuyenmai` varchar(255) NOT NULL COMMENT 'Tên khuyến mãi',
  `loaigiam` enum('phan_tram','so_tien') NOT NULL COMMENT 'Loại giảm (% hoặc VNĐ)',
  `giatri` decimal(10,2) NOT NULL COMMENT 'Giá trị giảm',
  `ngaybatdau` date NOT NULL COMMENT 'Ngày bắt đầu',
  `ngayketthuc` date NOT NULL COMMENT 'Ngày kết thúc',
  `solansudungtoida` int(11) DEFAULT 0 COMMENT 'Số lần dùng tối đa (0 = vô hạn)',
  `solandasudung` int(11) NOT NULL DEFAULT 0 COMMENT 'Số lần đã sử dụng',
  `danghoatdong` char(1) NOT NULL DEFAULT 'Y' COMMENT 'Đang hoạt động? (Y/N)',
  `ngaytao` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Ngày tạo',
  `ngaycapnhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Ngày cập nhật'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `khuyenmai` (`macode`, `tenkhuyenmai`, `loaigiam`, `giatri`, `ngaybatdau`, `ngayketthuc`, `solansudungtoida`, `solandasudung`, `danghoatdong`, `ngaytao`, `ngaycapnhat`) VALUES
('FAMILY500K', 'Giảm 500K cho gia đình', 'so_tien', 500000.00, '2026-05-01', '2026-12-31', 0, 0, 'Y', '2026-04-16 15:54:07', '2026-04-16 15:54:07'),
('SUMMER2026', 'Khuyến mãi hè 2026', 'phan_tram', 10.00, '2026-05-01', '2026-06-30', 0, 0, 'Y', '2026-04-16 15:54:07', '2026-04-16 15:54:07');

-- --------------------------------------------------------

CREATE TABLE `lichkhoihanh` (
  `lichid` bigint(20) NOT NULL COMMENT 'Mã lịch khởi hành',
  `tourid` bigint(20) NOT NULL COMMENT 'Mã tour',
  `ngaybatdau` date NOT NULL COMMENT 'Ngày bắt đầu',
  `ngayketthuc` date NOT NULL COMMENT 'Ngày kết thúc',
  `sochocon` int(11) NOT NULL COMMENT 'Số chỗ còn trống',
  `trangthai` enum('con_cho','het_cho','huy','khong_hoat_dong') NOT NULL DEFAULT 'con_cho' COMMENT 'Trạng thái',
  `ngaytao` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Ngày tạo',
  `ngaycapnhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Ngày cập nhật'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `lichkhoihanh` (`lichid`, `tourid`, `ngaybatdau`, `ngayketthuc`, `sochocon`, `trangthai`, `ngaytao`, `ngaycapnhat`) VALUES
(1, 1, '2026-05-01', '2026-05-03', 15, 'con_cho', '2026-04-16 15:54:07', '2026-04-16 15:54:07'),
(2, 1, '2026-05-15', '2026-05-17', 18, 'con_cho', '2026-04-16 15:54:07', '2026-04-16 15:54:07'),
(3, 2, '2026-05-10', '2026-05-13', 20, 'con_cho', '2026-04-16 15:54:07', '2026-04-16 15:54:07'),
(4, 3, '2026-06-01', '2026-06-05', 25, 'con_cho', '2026-04-16 15:54:07', '2026-04-16 15:54:07');

DELIMITER $$
CREATE TRIGGER `trglichkhoihanhcapnhattimestamp` BEFORE UPDATE ON `lichkhoihanh` FOR EACH ROW BEGIN
  SET NEW.ngaycapnhat = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

CREATE TABLE `lichtrinh` (
  `ltid` bigint(20) NOT NULL COMMENT 'Mã lịch trình',
  `tourid` bigint(20) NOT NULL COMMENT 'Mã tour',
  `tieude` varchar(255) NOT NULL COMMENT 'Tiêu đề',
  `noidung` text DEFAULT NULL COMMENT 'Nội dung',
  `ngaytao` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Ngày tạo',
  `ngaycapnhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Ngày cập nhật'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

CREATE TABLE `lienhe` (
  `lhid` bigint(20) NOT NULL COMMENT 'Mã liên hệ',
  `ndid` bigint(20) DEFAULT NULL COMMENT 'Mã người dùng (tùy chọn)',
  `tenlienhe` varchar(255) DEFAULT NULL COMMENT 'Tên người liên hệ',
  `emaillienhe` varchar(255) DEFAULT NULL COMMENT 'Email',
  `sodienthoai` varchar(20) DEFAULT NULL COMMENT 'Số điện thoại',
  `chude` varchar(255) NOT NULL COMMENT 'Chủ đề',
  `noidung` text NOT NULL COMMENT 'Nội dung',
  `trangthai` enum('chua_tra_loi','da_tra_loi','dang_xu_ly') NOT NULL DEFAULT 'chua_tra_loi' COMMENT 'Trạng thái',
  `phanhoi` text DEFAULT NULL COMMENT 'Phản hồi từ admin',
  `ngaygui` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Ngày gửi',
  `ngaytraloi` timestamp NULL DEFAULT NULL COMMENT 'Ngày trả lời',
  `ngaycapnhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Ngày cập nhật'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng liên hệ / hỗ trợ khách hàng';

-- --------------------------------------------------------

CREATE TABLE `nguoidung` (
  `ndid` bigint(20) NOT NULL COMMENT 'Mã người dùng',
  `tendangnhap` varchar(100) NOT NULL COMMENT 'Tên đăng nhập',
  `email` varchar(255) NOT NULL COMMENT 'Email',
  `matkhau` varchar(255) NOT NULL COMMENT 'Mật khẩu (mã hóa SHA2-256)',
  `sodienthoai` varchar(20) DEFAULT NULL COMMENT 'Số điện thoại',
  `hoten` varchar(255) DEFAULT NULL COMMENT 'Họ tên đầy đủ',
  `vaitro` enum('khach_hang','quan_tri','huong_dan_vien') NOT NULL DEFAULT 'khach_hang' COMMENT 'Vai trò',
  `trangthai` enum('hoat_dong','bi_khoa','xoa') NOT NULL DEFAULT 'hoat_dong' COMMENT 'Trạng thái tài khoản',
  `namkinhnghiem` int(11) DEFAULT NULL COMMENT 'Năm kinh nghiệm (cho guide)',
  `ngaytao` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Ngày tạo',
  `ngaycapnhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Ngày cập nhật'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `nguoidung` (`ndid`, `tendangnhap`, `email`, `matkhau`, `sodienthoai`, `hoten`, `vaitro`, `trangthai`, `namkinhnghiem`, `ngaytao`, `ngaycapnhat`) VALUES
(1, 'user1', 'user1@example.com', 'ef92b778bafe771e89245b89ecbc08a44a4e166c06659911881f383d4473e94f', '0901234567', 'Nguyễn Văn A', 'khach_hang', 'hoat_dong', NULL, '2026-04-16 15:54:07', '2026-04-16 15:54:07'),
(2, 'guide1', 'guide1@example.com', 'd249fc479e6a6f1bb0b1be90a294f1b073a3b467c029862304b71eedccd797f0', '0912345678', 'Lê Thị B', 'huong_dan_vien', 'hoat_dong', NULL, '2026-04-16 15:54:07', '2026-04-16 15:54:07'),
(3, 'guide2', 'guide2@example.com', '2b2585026f1013c116bd0eda6e8fda8094d6a0a6cb8f05700622f2589565a476', '0923456789', 'Phạm Văn C', 'huong_dan_vien', 'hoat_dong', NULL, '2026-04-16 15:54:07', '2026-04-16 15:54:07'),
(4, 'admin', 'admin@example.com', '240be518fabd2724ddb6f04eeb1da5967448d7e831c08c8fa822809f74c720a9', '0934567890', 'Trần Thị Admin', 'quan_tri', 'hoat_dong', NULL, '2026-04-16 15:54:07', '2026-04-16 15:54:07');

-- --------------------------------------------------------

CREATE TABLE `thanhtoan` (
  `ttid` bigint(20) NOT NULL COMMENT 'Mã thanh toán',
  `dtid` bigint(20) NOT NULL COMMENT 'Mã đặt tour',
  `phuongthuc` enum('momo','zalopay','chuyen_khoan','tien_mat','stripe','paypal') NOT NULL COMMENT 'Phương thức thanh toán',
  `sotien` decimal(10,2) NOT NULL COMMENT 'Số tiền thanh toán',
  `trangthai` enum('cho_xu_ly','thanh_cong','that_bai','hoan_tien') NOT NULL DEFAULT 'cho_xu_ly' COMMENT 'Trạng thái',
  `magiaodich` varchar(255) DEFAULT NULL COMMENT 'Mã giao dịch từ cổng',
  `ngaythanhtoan` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Ngày thanh toán',
  `ngaytao` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Ngày tạo',
  `ngaycapnhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Ngày cập nhật'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

CREATE TABLE `tour` (
  `tourid` bigint(20) NOT NULL COMMENT 'Mã tour',
  `tentour` varchar(255) NOT NULL COMMENT 'Tên tour',
  `mota` text DEFAULT NULL COMMENT 'Mô tả tour',
  `diemkhoihanh` varchar(255) DEFAULT NULL COMMENT 'Điểm khởi hành',
  `diadiemden` varchar(255) NOT NULL COMMENT 'Địa điểm đến',
  `khuvuc` varchar(100) NOT NULL COMMENT 'Khu vực (tỉnh/thành phố)',
  `gianguoilon` decimal(10,2) NOT NULL COMMENT 'Giá vé người lớn (VNĐ)',
  `giatreem` decimal(10,2) NOT NULL COMMENT 'Giá vé trẻ em (VNĐ)',
  `songay` int(11) NOT NULL COMMENT 'So ngay du lich',
  `dokho` enum('de','trung_binh','kho') DEFAULT 'trung_binh' COMMENT 'Do kho',
  `songuoitoida` int(11) NOT NULL COMMENT 'Số người tối đa',
  `trangthai` enum('con_cho','het_cho','huy','khong_hoat_dong') NOT NULL DEFAULT 'con_cho' COMMENT 'Trạng thái tour',
  `ngaytao` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Ngày tạo',
  `ngaycapnhat` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Ngày cập nhật'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tour` (`tourid`, `tentour`, `mota`, `diemkhoihanh`, `diadiemden`, `khuvuc`, `gianguoilon`, `giatreem`, `songay`, `songuoitoida`, `trangthai`, `dokho`, `ngaytao`, `ngaycapnhat`) VALUES
(1, 'Tour Hạ Long 3 Ngày 2 Đêm', 'Khám phá vịnh Hạ Long tuyệt đẹp', 'Hà Nội', 'Hạ Long', 'Quảng Ninh', 8500000.00, 6000000.00, 3, 20, 'con_cho', 'trung_binh', '2026-04-16 15:54:07', '2026-04-16 15:54:07'),
(2, 'Tour Đà Lạt 4 Ngày 3 Đêm', 'Trải nghiệm thơm mơ Đà Lạt', 'Hồ Chí Minh', 'Đà Lạt', 'Lâm Đồng', 6500000.00, 4500000.00, 4, 25, 'con_cho', 'trung_binh', '2026-04-16 15:54:07', '2026-04-16 15:54:07'),
(3, 'Tour Phú Quốc 5 Ngày 4 Đêm', 'Thiên đường biển đảo Phú Quốc', 'Hồ Chí Minh', 'Phú Quốc', 'Kiên Giang', 12000000.00, 8000000.00, 5, 30, 'con_cho', 'trung_binh', '2026-04-16 15:54:07', '2026-04-16 15:54:07');

DELIMITER $$
CREATE TRIGGER `trgtourcapnhattimestamp` BEFORE UPDATE ON `tour` FOR EACH ROW BEGIN
  SET NEW.ngaycapnhat = CURRENT_TIMESTAMP;
END
$$
DELIMITER ;

-- --------------------------------------------------------

CREATE TABLE `yeuthich` (
  `ndid` bigint(20) NOT NULL COMMENT 'Mã khách hàng',
  `tourid` bigint(20) NOT NULL COMMENT 'Mã tour',
  `ngaythem` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Ngày thêm'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng yêu thích tour';

-- --------------------------------------------------------

--
-- Khởi tạo Views (Đã cập nhật JOIN ON trùng tên trường)
--

DROP VIEW IF EXISTS `vwchitietdattour`;
CREATE VIEW `vwchitietdattour` AS SELECT `dt`.`dtid` AS `dtid`, `dt`.`ndid` AS `ndid`, `nd`.`hoten` AS `tenkhachhang`, `nd`.`email` AS `email`, `nd`.`sodienthoai` AS `sodienthoai`, `t`.`tentour` AS `tentour`, `lkh`.`ngaybatdau` AS `ngaybatdau`, `lkh`.`ngayketthuc` AS `ngayketthuc`, `dt`.`songuoilon` AS `songuoilon`, `dt`.`sotreem` AS `sotreem`, `dt`.`tongtien` AS `tongtien`, `dt`.`giamgia` AS `giamgia`, `dt`.`giacuoi` AS `giacuoi`, `dt`.`trangthai` AS `trangthai`, `dt`.`ngaydat` AS `ngaydat`, `dt`.`ngaycapnhat` AS `ngaycapnhat` FROM (((`dattour` `dt` join `nguoidung` `nd` on(`dt`.`ndid` = `nd`.`ndid`)) join `tour` `t` on(`dt`.`tourid` = `t`.`tourid`)) join `lichkhoihanh` `lkh` on(`dt`.`lichid` = `lkh`.`lichid`)) ORDER BY `dt`.`ngaydat` DESC ;

DROP VIEW IF EXISTS `vwdoanhthutour`;
CREATE VIEW `vwdoanhthutour` AS SELECT `t`.`tourid` AS `tourid`, `t`.`tentour` AS `tentour`, count(distinct `dt`.`dtid`) AS `sobooking`, sum(`dt`.`giacuoi`) AS `tongdoanhthu`, avg(`dg`.`sosao`) AS `diemdanhgiatrungbinh`, count(distinct `dg`.`dgid`) AS `sodanhgia` FROM ((`tour` `t` left join `dattour` `dt` on(`t`.`tourid` = `dt`.`tourid` and `dt`.`trangthai` in ('da_xac_nhan','da_thanh_toan'))) left join `danhgia` `dg` on(`t`.`tourid` = `dg`.`tourid` and `dg`.`trangthai` = 'da_duyet')) GROUP BY `t`.`tourid` ORDER BY sum(`dt`.`giacuoi`) DESC ;

DROP VIEW IF EXISTS `vwlichconcho`;
CREATE VIEW `vwlichconcho` AS SELECT `lkh`.`lichid` AS `lichid`, `t`.`tourid` AS `tourid`, `t`.`tentour` AS `tentour`, `t`.`gianguoilon` AS `gianguoilon`, `t`.`giatreem` AS `giatreem`, `lkh`.`ngaybatdau` AS `ngaybatdau`, `lkh`.`ngayketthuc` AS `ngayketthuc`, `lkh`.`sochocon` AS `sochocon`, count(distinct `dt`.`dtid`) AS `sodat`, `lkh`.`trangthai` AS `trangthai` FROM ((`lichkhoihanh` `lkh` join `tour` `t` on(`lkh`.`tourid` = `t`.`tourid`)) left join `dattour` `dt` on(`lkh`.`lichid` = `dt`.`lichid` and `dt`.`trangthai` <> 'da_huy')) WHERE `lkh`.`trangthai` = 'con_cho' AND `lkh`.`sochocon` > 0 GROUP BY `lkh`.`lichid`, `t`.`tourid` ORDER BY `lkh`.`ngaybatdau` ASC ;

--
-- THIẾT LẬP KHÓA (INDEX & PRIMARY KEYS)
--

ALTER TABLE `danhgia`
  ADD PRIMARY KEY (`dgid`),
  ADD UNIQUE KEY `ukdattour` (`dtid`),
  ADD KEY `dtid` (`dtid`),
  ADD KEY `idxtourid` (`tourid`),
  ADD KEY `idxtrangthai` (`trangthai`),
  ADD KEY `idxsosao` (`sosao`);

ALTER TABLE `dattour`
  ADD PRIMARY KEY (`dtid`),
  ADD KEY `makhuyenmai` (`makhuyenmai`),
  ADD KEY `idxndid` (`ndid`),
  ADD KEY `idxtourid` (`tourid`),
  ADD KEY `idxlichid` (`lichid`),
  ADD KEY `idxtrangthai` (`trangthai`),
  ADD KEY `idxngaydat` (`ngaydat`),
  ADD KEY `idxngaytao` (`ngaytao`),
  ADD KEY `idxdattourndidtrangthai` (`ndid`,`trangthai`);

ALTER TABLE `hinhanhtour`
  ADD PRIMARY KEY (`hinhid`),
  ADD KEY `idxtourid` (`tourid`),
  ADD KEY `idxthutuhienthi` (`thutuhienthi`);

ALTER TABLE `hoadon`
  ADD PRIMARY KEY (`hdid`),
  ADD UNIQUE KEY `ukdtid` (`dtid`),
  ADD KEY `idxngayphathanh` (`ngayphathanh`);

ALTER TABLE `khuyenmai`
  ADD PRIMARY KEY (`macode`),
  ADD KEY `idxdanghoatdong` (`danghoatdong`),
  ADD KEY `idxngaybatdau` (`ngaybatdau`),
  ADD KEY `idxngayketthuc` (`ngayketthuc`);

ALTER TABLE `lichkhoihanh`
  ADD PRIMARY KEY (`lichid`),
  ADD UNIQUE KEY `uktourngay` (`tourid`,`ngaybatdau`),
  ADD KEY `idxtourid` (`tourid`),
  ADD KEY `idxngaybatdau` (`ngaybatdau`),
  ADD KEY `idxtrangthai` (`trangthai`),
  ADD KEY `idxlichkhoihanhngaybatdautrangthai` (`ngaybatdau`,`trangthai`);

ALTER TABLE `lichtrinh`
  ADD PRIMARY KEY (`ltid`),
  ADD KEY `idxtourid` (`tourid`);

ALTER TABLE `lienhe`
  ADD PRIMARY KEY (`lhid`),
  ADD KEY `ndid` (`ndid`),
  ADD KEY `idxtrangthai` (`trangthai`),
  ADD KEY `idxngaygui` (`ngaygui`),
  ADD KEY `idxemaillienhe` (`emaillienhe`);

ALTER TABLE `nguoidung`
  ADD PRIMARY KEY (`ndid`),
  ADD UNIQUE KEY `tendangnhap` (`tendangnhap`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idxemail` (`email`),
  ADD KEY `idxtendangnhap` (`tendangnhap`),
  ADD KEY `idxvaitro` (`vaitro`),
  ADD KEY `idxtrangthai` (`trangthai`),
  ADD KEY `idxngaytao` (`ngaytao`);

ALTER TABLE `thanhtoan`
  ADD PRIMARY KEY (`ttid`),
  ADD UNIQUE KEY `magiaodich` (`magiaodich`),
  ADD KEY `idxdtid` (`dtid`),
  ADD KEY `idxtrangthai` (`trangthai`),
  ADD KEY `idxmagiaodich` (`magiaodich`),
  ADD KEY `idxngaythanhtoan` (`ngaythanhtoan`),
  ADD KEY `idxthanhtoantrangthaingay` (`trangthai`,`ngaythanhtoan`);

ALTER TABLE `tour`
  ADD PRIMARY KEY (`tourid`),
  ADD UNIQUE KEY `tentour` (`tentour`),
  ADD KEY `idxtrangthai` (`trangthai`),
  ADD KEY `idxgianguoilon` (`gianguoilon`),
  ADD KEY `idxsongay` (`songay`),
  ADD KEY `idxtourtrangthaigia` (`trangthai`,`gianguoilon`);

ALTER TABLE `yeuthich`
  ADD PRIMARY KEY (`ndid`,`tourid`),
  ADD KEY `tourid` (`tourid`),
  ADD KEY `idxngaythem` (`ngaythem`);

--
-- THIẾT LẬP AUTO_INCREMENT
--

ALTER TABLE `danhgia` MODIFY `dgid` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Mã đánh giá';
ALTER TABLE `dattour` MODIFY `dtid` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Mã đặt tour';
ALTER TABLE `hinhanhtour` MODIFY `hinhid` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Mã hình ảnh';
ALTER TABLE `hoadon` MODIFY `hdid` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Mã hóa đơn';
ALTER TABLE `lichkhoihanh` MODIFY `lichid` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Mã lịch khởi hành';
ALTER TABLE `lichtrinh` MODIFY `ltid` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Mã lịch trình';
ALTER TABLE `lienhe` MODIFY `lhid` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Mã liên hệ';
ALTER TABLE `nguoidung` MODIFY `ndid` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Mã người dùng';
ALTER TABLE `thanhtoan` MODIFY `ttid` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Mã thanh toán';
ALTER TABLE `tour` MODIFY `tourid` bigint(20) NOT NULL AUTO_INCREMENT COMMENT 'Mã tour';

--
-- THIẾT LẬP KHÓA NGOẠI (FOREIGN KEYS)
--

ALTER TABLE `danhgia`
  ADD CONSTRAINT `danhgiaibfk1` FOREIGN KEY (`ndid`) REFERENCES `nguoidung` (`ndid`) ON DELETE CASCADE,
  ADD CONSTRAINT `danhgiaibfk2` FOREIGN KEY (`tourid`) REFERENCES `tour` (`tourid`) ON DELETE CASCADE,
  ADD CONSTRAINT `danhgiaibfk3` FOREIGN KEY (`dtid`) REFERENCES `dattour` (`dtid`) ON DELETE CASCADE;

ALTER TABLE `dattour`
  ADD CONSTRAINT `dattouribfk1` FOREIGN KEY (`ndid`) REFERENCES `nguoidung` (`ndid`),
  ADD CONSTRAINT `dattouribfk2` FOREIGN KEY (`tourid`) REFERENCES `tour` (`tourid`),
  ADD CONSTRAINT `dattouribfk3` FOREIGN KEY (`lichid`) REFERENCES `lichkhoihanh` (`lichid`),
  ADD CONSTRAINT `dattouribfk4` FOREIGN KEY (`makhuyenmai`) REFERENCES `khuyenmai` (`macode`) ON DELETE SET NULL;

ALTER TABLE `hinhanhtour`
  ADD CONSTRAINT `hinhanhtouribfk1` FOREIGN KEY (`tourid`) REFERENCES `tour` (`tourid`) ON DELETE CASCADE;

ALTER TABLE `hoadon`
  ADD CONSTRAINT `hoadonibfk1` FOREIGN KEY (`dtid`) REFERENCES `dattour` (`dtid`) ON DELETE CASCADE;

ALTER TABLE `lichkhoihanh`
  ADD CONSTRAINT `lichkhoihanhibfk1` FOREIGN KEY (`tourid`) REFERENCES `tour` (`tourid`) ON DELETE CASCADE;

ALTER TABLE `lichtrinh`
  ADD CONSTRAINT `lichtrinhibfk1` FOREIGN KEY (`tourid`) REFERENCES `tour` (`tourid`) ON DELETE CASCADE;

ALTER TABLE `lienhe`
  ADD CONSTRAINT `lienheibfk1` FOREIGN KEY (`ndid`) REFERENCES `nguoidung` (`ndid`) ON DELETE SET NULL;

ALTER TABLE `thanhtoan`
  ADD CONSTRAINT `thanhtoanibfk1` FOREIGN KEY (`dtid`) REFERENCES `dattour` (`dtid`) ON DELETE CASCADE;

ALTER TABLE `yeuthich`
  ADD CONSTRAINT `yeuthichibfk1` FOREIGN KEY (`ndid`) REFERENCES `nguoidung` (`ndid`) ON DELETE CASCADE,
  ADD CONSTRAINT `yeuthichibfk2` FOREIGN KEY (`tourid`) REFERENCES `tour` (`tourid`) ON DELETE CASCADE;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;