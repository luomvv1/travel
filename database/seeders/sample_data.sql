-- Sample data for the travel database.
-- Assumes schema + base data from travel.sql are already loaded.
-- Image files referenced here exist under public/admin/assets/images/gallery-tours/

-- Sample users (password for all: 123456, SHA-256)
INSERT INTO `nguoidung`
(`ndid`, `tendangnhap`, `email`, `matkhau`, `sodienthoai`, `hoten`, `vaitro`, `trangthai`, `namkinhnghiem`, `ngaytao`, `ngaycapnhat`) VALUES
(5, 'khach01', 'khach01@example.com', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', '0901111111', 'Khach Hang 01', 'khach_hang', 'hoat_dong', NULL, '2026-04-20 08:00:00', '2026-04-20 08:00:00'),
(6, 'khach02', 'khach02@example.com', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', '0902222222', 'Khach Hang 02', 'khach_hang', 'hoat_dong', NULL, '2026-04-20 08:10:00', '2026-04-20 08:10:00'),
(7, 'khach03', 'khach03@example.com', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', '0903333333', 'Khach Hang 03', 'khach_hang', 'hoat_dong', NULL, '2026-04-20 08:20:00', '2026-04-20 08:20:00'),
(8, 'khach04', 'khach04@example.com', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', '0904444444', 'Khach Hang 04', 'khach_hang', 'hoat_dong', NULL, '2026-04-20 08:30:00', '2026-04-20 08:30:00'),
(9, 'guide03', 'guide03@example.com', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', '0905555555', 'Guide 03', 'huong_dan_vien', 'hoat_dong', 4, '2026-04-20 08:40:00', '2026-04-20 08:40:00'),
(10, 'admin02', 'admin02@example.com', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', '0906666666', 'Admin 02', 'quan_tri', 'hoat_dong', NULL, '2026-04-20 08:50:00', '2026-04-20 08:50:00');

-- Additional tours
INSERT INTO `tour`
(`tourid`, `tentour`, `mota`, `diemkhoihanh`, `diadiemden`, `khuvuc`, `gianguoilon`, `giatreem`, `songay`, `songuoitoida`, `trangthai`, `dokho`, `ngaytao`, `ngaycapnhat`) VALUES
(4, 'Tour Da Nang - Hoi An 4N3D', 'Kham pha Da Nang, Ba Na, Hoi An', 'Ha Noi', 'Da Nang - Hoi An', 'Da Nang', 7800000.00, 5200000.00, 4, 25, 'con_cho', 'trung_binh', '2026-04-20 09:00:00', '2026-04-20 09:00:00'),
(5, 'Tour Ha Giang - Cao Bang 5N4D', 'Chinh phuc deo Ma Pi Leng va thac Ban Gioc', 'Ha Noi', 'Ha Giang - Cao Bang', 'Ha Giang', 9500000.00, 6700000.00, 5, 20, 'con_cho', 'trung_binh', '2026-04-20 09:05:00', '2026-04-20 09:05:00'),
(6, 'Tour Nha Trang 3N2D', 'Bien xanh, vinpearl va tham quan dao', 'Ho Chi Minh', 'Nha Trang', 'Khanh Hoa', 6200000.00, 4200000.00, 3, 30, 'con_cho', 'trung_binh', '2026-04-20 09:10:00', '2026-04-20 09:10:00'),
(7, 'Tour Can Tho - Chau Doc 3N2D', 'Cho noi Cai Rang va lang ca be Chau Doc', 'Ho Chi Minh', 'Can Tho - Chau Doc', 'Can Tho', 5400000.00, 3600000.00, 3, 25, 'con_cho', 'de', '2026-04-20 09:15:00', '2026-04-20 09:15:00'),
(8, 'Tour Sa Pa 4N3D', 'Fansipan, nha tho da va canh doi', 'Ha Noi', 'Sa Pa', 'Lao Cai', 7300000.00, 5000000.00, 4, 22, 'con_cho', 'trung_binh', '2026-04-20 09:20:00', '2026-04-20 09:20:00');

-- Tour images (filenames in public/admin/assets/images/gallery-tours)
INSERT INTO `hinhanhtour` (`tourid`, `urlanh`, `tenanh`, `motaanh`, `thutuhienthi`) VALUES
(1, 'vinh-ha-long_1732896698.png', 'vinh-ha-long_1732896698.png', 'Ha Long 1', 1),
(1, 'vinh-ha-long-quang-ninh_1735834627.jpg', 'vinh-ha-long-quang-ninh_1735834627.jpg', 'Ha Long 2', 2),
(1, 'dong-nguom-ngao_1732897626.jpg', 'dong-nguom-ngao_1732897626.jpg', 'Ha Long 3', 3),
(1, 'chua-bai-dinh_1732896696.jpg', 'chua-bai-dinh_1732896696.jpg', 'Ha Long 4', 4),
(2, 'da-lat-view_1734022793.jpg', 'da-lat-view_1734022793.jpg', 'Da Lat 1', 1),
(2, 'lang-biang-da-lat_1734022794.jpg', 'lang-biang-da-lat_1734022794.jpg', 'Da Lat 2', 2),
(2, 'thac-datanla_1734022794.jpg', 'thac-datanla_1734022794.jpg', 'Da Lat 3', 3),
(2, 'nong-trai-puppy-farm_1734022794.jpg', 'nong-trai-puppy-farm_1734022794.jpg', 'Da Lat 4', 4),
(3, 'bai-sao-phu-quoc_1732895312.jpg', 'bai-sao-phu-quoc_1732895312.jpg', 'Phu Quoc 1', 1),
(3, 'hon-mong-tay-phu-quoc_1732895311.jpg', 'hon-mong-tay-phu-quoc_1732895311.jpg', 'Phu Quoc 2', 2),
(3, 'hon-gam-ghi-phu-quoc_1732895313.jpg', 'hon-gam-ghi-phu-quoc_1732895313.jpg', 'Phu Quoc 3', 3),
(3, 'thi-tran-hoang-hon-phu-quoc_1732895312.jpg', 'thi-tran-hoang-hon-phu-quoc_1732895312.jpg', 'Phu Quoc 4', 4),
(4, 'ba-na-hill-da-nang-1_1732896298.png', 'ba-na-hill-da-nang-1_1732896298.png', 'Da Nang 1', 1),
(4, 'ben-trong-ba-na-hills-da-nang_1732896301.png', 'ben-trong-ba-na-hills-da-nang_1732896301.png', 'Da Nang 2', 2),
(4, 'cau-vang-da-nang_1732896303.png', 'cau-vang-da-nang_1732896303.png', 'Da Nang 3', 3),
(4, 'pho-co-hoi-an_1732895857.jpg', 'pho-co-hoi-an_1732895857.jpg', 'Da Nang 4', 4),
(5, 'chinh-phuc-deo-ma-pi-leng_1732897625.jpeg', 'chinh-phuc-deo-ma-pi-leng_1732897625.jpeg', 'Ha Giang 1', 1),
(5, 'cot-co-lung-cu-ha-giang_1732897626.png', 'cot-co-lung-cu-ha-giang_1732897626.png', 'Ha Giang 2', 2),
(5, 'thac-ban-gioc-cao-bang_1732897627.png', 'thac-ban-gioc-cao-bang_1732897627.png', 'Ha Giang 3', 3),
(5, 'tour-ha-giang-cao-bang-5-ngay-4-dem-7_1732937890.jpg', 'tour-ha-giang-cao-bang-5-ngay-4-dem-7_1732937890.jpg', 'Ha Giang 4', 4),
(6, 'hon-tre-nha-trang_1734022644.jpg', 'hon-tre-nha-trang_1734022644.jpg', 'Nha Trang 1', 1),
(6, 'i-resort-nha-trang_1734022646.jpg', 'i-resort-nha-trang_1734022646.jpg', 'Nha Trang 2', 2),
(6, 'vien-hai-duong-hoc-nha-trang_1734022645.jpg', 'vien-hai-duong-hoc-nha-trang_1734022645.jpg', 'Nha Trang 3', 3),
(6, 'vinpearl-nha-trang_1734022646.jpg', 'vinpearl-nha-trang_1734022646.jpg', 'Nha Trang 4', 4),
(7, 'CAU_CAN_THO__1__1732933786.jpg', 'CAU_CAN_THO__1__1732933786.jpg', 'Can Tho 1', 1),
(7, 'CAY_THOT_NOT_-_DONG_LUA__2__1732934235.jpg', 'CAY_THOT_NOT_-_DONG_LUA__2__1732934235.jpg', 'Can Tho 2', 2),
(7, 'tfd_240103060657_209766_LANG_CA_BE_CHAU_DOC_1732934361.jpg', 'tfd_240103060657_209766_LANG_CA_BE_CHAU_DOC_1732934361.jpg', 'Can Tho 3', 3),
(7, 'tfd_241016022402_471049_CHO_NOI_CAI_RANG__1__1732934237.jpg', 'tfd_241016022402_471049_CHO_NOI_CAI_RANG__1__1732934237.jpg', 'Can Tho 4', 4),
(8, 'dinh-fansipan-sapa_1732898213.png', 'dinh-fansipan-sapa_1732898213.png', 'Sa Pa 1', 1),
(8, 'nha-tho-da-sapa_1732898214.png', 'nha-tho-da-sapa_1732898214.png', 'Sa Pa 2', 2),
(8, 'bich-van-thien-tu-sapa_1732898210.png', 'bich-van-thien-tu-sapa_1732898210.png', 'Sa Pa 3', 3),
(8, 'tour-du-lich-ha-noi-sapa-ha-long-ninh-binh-5n4d-1_1732938916.jpg', 'tour-du-lich-ha-noi-sapa-ha-long-ninh-binh-5n4d-1_1732938916.jpg', 'Sa Pa 4', 4);

-- Departure schedules
INSERT INTO `lichkhoihanh`
(`lichid`, `tourid`, `ngaybatdau`, `ngayketthuc`, `sochocon`, `trangthai`, `ngaytao`, `ngaycapnhat`) VALUES
(5, 4, '2026-05-20', '2026-05-23', 18, 'con_cho', '2026-04-20 10:00:00', '2026-04-20 10:00:00'),
(6, 4, '2026-06-10', '2026-06-13', 20, 'con_cho', '2026-04-20 10:05:00', '2026-04-20 10:05:00'),
(7, 5, '2026-06-05', '2026-06-09', 12, 'con_cho', '2026-04-20 10:10:00', '2026-04-20 10:10:00'),
(8, 5, '2026-06-20', '2026-06-24', 15, 'con_cho', '2026-04-20 10:15:00', '2026-04-20 10:15:00'),
(9, 6, '2026-05-25', '2026-05-27', 25, 'con_cho', '2026-04-20 10:20:00', '2026-04-20 10:20:00'),
(10, 6, '2026-06-15', '2026-06-17', 25, 'con_cho', '2026-04-20 10:25:00', '2026-04-20 10:25:00'),
(11, 7, '2026-05-18', '2026-05-20', 20, 'con_cho', '2026-04-20 10:30:00', '2026-04-20 10:30:00'),
(12, 8, '2026-06-08', '2026-06-11', 18, 'con_cho', '2026-04-20 10:35:00', '2026-04-20 10:35:00');

-- Itineraries
INSERT INTO `lichtrinh` (`ltid`, `tourid`, `tieude`, `noidung`, `ngaytao`, `ngaycapnhat`) VALUES
(1, 1, 'Ngay 1', 'Ha Noi - Ha Long - check in khach san', '2026-04-20 11:00:00', '2026-04-20 11:00:00'),
(2, 1, 'Ngay 2', 'Vinh Ha Long - hang dong - bai bien', '2026-04-20 11:00:00', '2026-04-20 11:00:00'),
(3, 2, 'Ngay 1', 'TP HCM - Da Lat - tham quan trung tam', '2026-04-20 11:05:00', '2026-04-20 11:05:00'),
(4, 2, 'Ngay 2', 'Lang Biang - thung lung tinh yeu', '2026-04-20 11:05:00', '2026-04-20 11:05:00'),
(5, 3, 'Ngay 1', 'TP HCM - Phu Quoc - bai Sao', '2026-04-20 11:10:00', '2026-04-20 11:10:00'),
(6, 3, 'Ngay 2', 'Hon Mong Tay - Hon Gam Ghi', '2026-04-20 11:10:00', '2026-04-20 11:10:00'),
(7, 4, 'Ngay 1', 'Da Nang - Ba Na - Cau Vang', '2026-04-20 11:15:00', '2026-04-20 11:15:00'),
(8, 4, 'Ngay 2', 'Hoi An - pho co - am thuc', '2026-04-20 11:15:00', '2026-04-20 11:15:00'),
(9, 5, 'Ngay 1', 'Ha Giang - Dong Van - pho co', '2026-04-20 11:20:00', '2026-04-20 11:20:00'),
(10, 5, 'Ngay 2', 'Ma Pi Leng - Ban Gioc', '2026-04-20 11:20:00', '2026-04-20 11:20:00'),
(11, 6, 'Ngay 1', 'Nha Trang - tam bien', '2026-04-20 11:25:00', '2026-04-20 11:25:00'),
(12, 6, 'Ngay 2', 'Vinpearl - i-resort', '2026-04-20 11:25:00', '2026-04-20 11:25:00'),
(13, 7, 'Ngay 1', 'Can Tho - cho noi Cai Rang', '2026-04-20 11:30:00', '2026-04-20 11:30:00'),
(14, 7, 'Ngay 2', 'Chau Doc - lang ca be', '2026-04-20 11:30:00', '2026-04-20 11:30:00'),
(15, 8, 'Ngay 1', 'Ha Noi - Sa Pa - nha tho da', '2026-04-20 11:35:00', '2026-04-20 11:35:00'),
(16, 8, 'Ngay 2', 'Fansipan - tham quan ban lang', '2026-04-20 11:35:00', '2026-04-20 11:35:00');

-- Bookings
INSERT INTO `dattour`
(`dtid`, `ndid`, `tourid`, `lichid`, `ngaydat`, `songuoilon`, `sotreem`, `tongtien`, `giamgia`, `giacuoi`, `makhuyenmai`, `trangthai`) VALUES
(1, 1, 1, 1, '2026-04-20', 2, 1, 23000000.00, 0.00, 23000000.00, NULL, 'da_thanh_toan'),
(2, 5, 4, 5, '2026-04-22', 2, 0, 15600000.00, 500000.00, 15100000.00, 'FAMILY500K', 'da_thanh_toan'),
(3, 6, 6, 9, '2026-04-23', 1, 1, 10400000.00, 0.00, 10400000.00, NULL, 'da_thanh_toan'),
(4, 7, 3, 4, '2026-04-25', 2, 2, 40000000.00, 4000000.00, 36000000.00, 'SUMMER2026', 'da_thanh_toan'),
(5, 8, 5, 7, '2026-04-26', 1, 0, 9500000.00, 0.00, 9500000.00, NULL, 'cho_xac_nhan'),
(6, 5, 8, 12, '2026-04-27', 2, 1, 19600000.00, 0.00, 19600000.00, NULL, 'da_xac_nhan');

-- Payments
INSERT INTO `thanhtoan`
(`ttid`, `dtid`, `phuongthuc`, `sotien`, `trangthai`, `magiaodich`) VALUES
(1, 1, 'chuyen_khoan', 23000000.00, 'thanh_cong', 'PAY20260420001'),
(2, 2, 'momo', 15100000.00, 'thanh_cong', 'PAY20260422001'),
(3, 3, 'zalopay', 10400000.00, 'thanh_cong', 'PAY20260423001'),
(4, 4, 'stripe', 36000000.00, 'thanh_cong', 'PAY20260425001');

-- Invoices
INSERT INTO `hoadon`
(`hdid`, `dtid`, `ngayphathanh`, `tongtien`, `ghichu`) VALUES
(1, 1, '2026-04-20', 23000000.00, 'Da thanh toan'),
(2, 2, '2026-04-22', 15100000.00, 'Da thanh toan'),
(3, 3, '2026-04-23', 10400000.00, 'Da thanh toan'),
(4, 4, '2026-04-25', 36000000.00, 'Da thanh toan');

-- Reviews (one per booking)
INSERT INTO `danhgia`
(`dgid`, `ndid`, `tourid`, `dtid`, `sosao`, `tieude`, `binhluan`, `trangthai`) VALUES
(1, 1, 1, 1, 5, 'Rat hai long', 'Canh dep va phuc vu tot', 'da_duyet'),
(2, 6, 6, 3, 4, 'Trai nghiem tot', 'Dich vu on, lich trinh hop ly', 'da_duyet'),
(3, 7, 3, 4, 5, 'Tuyet voi', 'Gia dinh rat thich', 'da_duyet');

-- Favorites
INSERT INTO `yeuthich` (`ndid`, `tourid`) VALUES
(1, 3),
(5, 4),
(6, 6),
(7, 1),
(8, 5);

-- Contact requests
INSERT INTO `lienhe`
(`lhid`, `ndid`, `tenlienhe`, `emaillienhe`, `sodienthoai`, `chude`, `noidung`, `trangthai`, `phanhoi`) VALUES
(1, 5, 'Khach 01', 'khach01@example.com', '0901111111', 'Hoi ve tour', 'Xin gia tour cuoi tuan', 'chua_tra_loi', NULL),
(2, NULL, 'Khach vang lai', 'guest@example.com', '0909999999', 'Tu van', 'Can tu van tour bien', 'dang_xu_ly', NULL);
