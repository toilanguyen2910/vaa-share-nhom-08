-- ====================================================================
-- HỌC VIỆN HÀNG KHÔNG VIỆT NAM (VAA) - KHOA CÔNG NGHỆ THÔNG TIN
-- ĐỒ ÁN MÔN HỌC: LẬP TRÌNH WEB (MÃ HP: 010151101410)
-- ĐỀ TÀI: VAA SHARE - NỀN TẢNG TRAO ĐỔI & BÁN LẠI ĐỒ DÙNG NỘI BỘ SINH VIÊN
-- NHÓM 8: Vũ Hoàng Khôi Nguyên (Trưởng nhóm), Vũ Nguyễn Đức Tính, Diệp Vĩnh Long
-- GIẢNG VIÊN HƯỚNG DẪN: ThS. Huỳnh Thanh Sơn
-- ====================================================================

CREATE DATABASE IF NOT EXISTS `vaa_share` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `vaa_share`;

-- Tắt kiểm tra khóa ngoại để xóa bảng cũ an toàn
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `bao_cao_vi_pham`;
DROP TABLE IF EXISTS `binh_luan`;
DROP TABLE IF EXISTS `yeu_cau_giao_dich`;
DROP TABLE IF EXISTS `do_dung`;
DROP TABLE IF EXISTS `dia_diem`;
DROP TABLE IF EXISTS `danh_muc`;
DROP TABLE IF EXISTS `nguoi_dung`;
SET FOREIGN_KEY_CHECKS = 1;

-- --------------------------------------------------------------------
-- 1. BẢNG nguoi_dung (Tài khoản sinh viên, giảng viên và quản trị viên)
-- --------------------------------------------------------------------
CREATE TABLE `nguoi_dung` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `mssv` VARCHAR(20) NOT NULL UNIQUE,
  `ho_ten` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `mat_khau` VARCHAR(255) NOT NULL,
  `so_dien_thoai` VARCHAR(20) NOT NULL,
  `zalo` VARCHAR(50) DEFAULT NULL,
  `khoa_lop` VARCHAR(100) DEFAULT 'K25 - Học viện Hàng không',
  `vai_tro` ENUM('Sinh vien', 'Giang vien', 'Admin') DEFAULT 'Sinh vien',
  `trang_thai` ENUM('Hoat dong', 'Bi khoa') DEFAULT 'Hoat dong',
  `ngay_tao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 2. BẢNG danh_muc (5 danh mục đồ dùng đặc thù VAA)
-- --------------------------------------------------------------------
CREATE TABLE `danh_muc` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `ten_danh_muc` VARCHAR(100) NOT NULL,
  `mo_ta` VARCHAR(255) DEFAULT NULL,
  `icon` VARCHAR(50) DEFAULT 'book',
  `thu_tu` INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 3. BẢNG dia_diem (Cơ sở bàn giao trực tiếp tại Học viện)
-- --------------------------------------------------------------------
CREATE TABLE `dia_diem` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `ten_dia_diem` VARCHAR(100) NOT NULL,
  `dia_chi` VARCHAR(255) NOT NULL,
  `ghi_chu` VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 4. BẢNG do_dung (Tin đăng trao đổi / tặng / thanh lý đồ dùng)
-- --------------------------------------------------------------------
CREATE TABLE `do_dung` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `nguoi_dung_id` INT NOT NULL,
  `danh_muc_id` INT NOT NULL,
  `dia_diem_id` INT NOT NULL,
  `tieu_de` VARCHAR(255) NOT NULL,
  `mo_ta` TEXT NOT NULL,
  `hinh_thuc` ENUM('Tang mien phi', 'Thanh ly') DEFAULT 'Tang mien phi',
  `gia` INT DEFAULT 0,
  `tinh_trang` VARCHAR(100) DEFAULT 'Còn rất tốt (trên 90%)',
  `hinh_anh` VARCHAR(255) DEFAULT 'default_item.jpg',
  `trang_thai` ENUM('Cho duyet', 'Da duyet', 'Dang giao dich', 'Hoan thanh', 'Da an') DEFAULT 'Cho duyet',
  `luot_xem` INT DEFAULT 0,
  `ngay_dang` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `ngay_cap_nhat` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT `fk_dodung_nguoidung` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_dodung_danhmuc` FOREIGN KEY (`danh_muc_id`) REFERENCES `danh_muc` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_dodung_diadiem` FOREIGN KEY (`dia_diem_id`) REFERENCES `dia_diem` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 5. BẢNG yeu_cau_giao_dich (P2P Request & Approval: Người cần bấm nhận/mua đồ)
-- --------------------------------------------------------------------
CREATE TABLE `yeu_cau_giao_dich` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `do_dung_id` INT NOT NULL,
  `nguoi_gui_id` INT NOT NULL,
  `nguoi_nhan_id` INT NOT NULL,
  `loi_nhan` TEXT DEFAULT NULL,
  `sdt_lien_he` VARCHAR(20) NOT NULL,
  `trang_thai` ENUM('Cho xac nhan', 'Dong y', 'Tu choi', 'Hoan tat') DEFAULT 'Cho xac nhan',
  `ngay_gui` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_yeucau_dodung` FOREIGN KEY (`do_dung_id`) REFERENCES `do_dung` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_yeucau_nguoigui` FOREIGN KEY (`nguoi_gui_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_yeucau_nguoinhan` FOREIGN KEY (`nguoi_nhan_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 6. BẢNG binh_luan (Hỏi đáp, trao đổi trực tiếp dưới món đồ)
-- --------------------------------------------------------------------
CREATE TABLE `binh_luan` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `do_dung_id` INT NOT NULL,
  `nguoi_dung_id` INT NOT NULL,
  `noi_dung` TEXT NOT NULL,
  `ngay_dang` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_binhluan_dodung` FOREIGN KEY (`do_dung_id`) REFERENCES `do_dung` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_binhluan_nguoidung` FOREIGN KEY (`nguoi_dung_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------------------
-- 7. BẢNG bao_cao_vi_pham (Sinh viên báo cáo tin đăng giả mạo/lừa đảo)
-- --------------------------------------------------------------------
CREATE TABLE `bao_cao_vi_pham` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `do_dung_id` INT NOT NULL,
  `nguoi_bao_cao_id` INT NOT NULL,
  `ly_do` VARCHAR(255) NOT NULL,
  `chi_tiet` TEXT DEFAULT NULL,
  `trang_thai` ENUM('Cho xu ly', 'Da xu ly', 'Da bo qua') DEFAULT 'Cho xu ly',
  `ngay_bao_cao` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_baocao_dodung` FOREIGN KEY (`do_dung_id`) REFERENCES `do_dung` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_baocao_nguoidung` FOREIGN KEY (`nguoi_bao_cao_id`) REFERENCES `nguoi_dung` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ====================================================================
-- DỮ LIỆU KHỞI TẠO MẪU (SEED DATA CHUẨN SINH VIÊN VAA)
-- Tất cả mật khẩu demo đều là: 123456
-- Chuỗi băm BCRYPT chuẩn: $2y$10$L3PCEsL218decUkBOuZX7uZF8UtCGMzX4fNtbpjIfSaBlg5OOb2Ea
-- ====================================================================

-- 1. NẠP TÀI KHOẢN MẪU
INSERT INTO `nguoi_dung` (`id`, `mssv`, `ho_ten`, `email`, `mat_khau`, `so_dien_thoai`, `zalo`, `khoa_lop`, `vai_tro`, `trang_thai`) VALUES
(1, 'VAA-ADM01', 'Ban Quản trị VAA Share', 'admin@vaa.edu.vn', '$2y$10$L3PCEsL218decUkBOuZX7uZF8UtCGMzX4fNtbpjIfSaBlg5OOb2Ea', '0901234567', '0901234567', 'Phòng Công tác Sinh viên', 'Admin', 'Hoat dong'),
(2, '2531540127', 'Vũ Hoàng Khôi Nguyên', '2531540127@vaa.edu.vn', '$2y$10$L3PCEsL218decUkBOuZX7uZF8UtCGMzX4fNtbpjIfSaBlg5OOb2Ea', '0912345678', '0912345678', '25ĐHSA02 - Khoa CNTT', 'Sinh vien', 'Hoat dong'),
(3, '2531540131', 'Vũ Nguyễn Đức Tính', '2531540131@vaa.edu.vn', '$2y$10$L3PCEsL218decUkBOuZX7uZF8UtCGMzX4fNtbpjIfSaBlg5OOb2Ea', '0923456789', '0923456789', '25ĐHSA02 - Khoa CNTT', 'Sinh vien', 'Hoat dong'),
(4, '2531540054', 'Diệp Vĩnh Long', '2531540054@vaa.edu.vn', '$2y$10$L3PCEsL218decUkBOuZX7uZF8UtCGMzX4fNtbpjIfSaBlg5OOb2Ea', '0934567890', '0934567890', '25ĐHSA01 - Khoa CNTT', 'Sinh vien', 'Hoat dong'),
(5, 'GV-SONHT', 'ThS. Huỳnh Thanh Sơn', 'sonht@vaa.edu.vn', '$2y$10$L3PCEsL218decUkBOuZX7uZF8UtCGMzX4fNtbpjIfSaBlg5OOb2Ea', '0987654321', '0987654321', 'Khoa Công nghệ thông tin', 'Giang vien', 'Hoat dong'),
(6, '2254050012', 'Nguyễn Thị Mai Phương', '2254050012@vaa.edu.vn', '$2y$10$L3PCEsL218decUkBOuZX7uZF8UtCGMzX4fNtbpjIfSaBlg5OOb2Ea', '0945678901', '0945678901', '22ĐHQH01 - Quản lý Hoạt động bay', 'Sinh vien', 'Hoat dong');

-- 2. NẠP 5 DANH MỤC ĐẶC THÙ VAA
INSERT INTO `danh_muc` (`id`, `ten_danh_muc`, `mo_ta`, `icon`, `thu_tu`) VALUES
(1, 'Giáo trình & Tài liệu Hàng không', 'Giáo trình dẫn đường, khí tượng, tiếng Anh chuyên ngành hàng không, đề cương ôn tập', 'book-open', 1),
(2, 'Đồng phục & Phụ kiện VAA', 'Áo đồng phục khoa, áo thể dục, caravat, cầu vai sinh viên học viện', 'shirt', 2),
(3, 'Dụng cụ học tập & Máy tính', 'Máy tính Casio fx-580VNX, thước vẽ kỹ thuật, bảng vẽ, đèn học', 'calculator', 3),
(4, 'Đồ sinh hoạt phòng trọ', 'Quạt đứng, ấm đun siêu tốc, bàn gấp sinh viên, giá phơi đồ', 'home', 4),
(5, 'Thiết bị điện tử', 'Chuột máy tính, bàn phím rời, tai nghe học ngoại ngữ, sạc dự phòng', 'laptop', 5);

-- 3. NẠP 3 ĐỊA ĐIỂM GIAO DỊCH CHÍNH
INSERT INTO `dia_diem` (`id`, `ten_dia_diem`, `dia_chi`, `ghi_chu`) VALUES
(1, 'Cơ sở 1 (CS1 - Phú Nhuận)', '104 Nguyễn Văn Trỗi, Phường 8, Quận Phú Nhuận', 'Ưu tiên hẹn tại Thư viện CS1 hoặc Căn tin sảnh A'),
(2, 'Cơ sở 2 (CS2 - Tân Bình)', '18A/1 Cộng Hòa, Phường 4, Quận Tân Bình', 'Ưu tiên hẹn tại Sảnh D2 hoặc Căn tin trung tâm'),
(3, 'Ký túc xá VAA / Khu vực quanh trường', 'Đường Đặng Văn Sâm / Thăng Long, Tân Bình', 'Khu trọ tập trung đông sinh viên VAA');

-- 4. NẠP DỮ LIỆU ĐỒ DÙNG MẪU (THỰC TẾ VAA)
INSERT INTO `do_dung` (`id`, `nguoi_dung_id`, `danh_muc_id`, `dia_diem_id`, `tieu_de`, `mo_ta`, `hinh_thuc`, `gia`, `tinh_trang`, `hinh_anh`, `trang_thai`, `luot_xem`) VALUES
(1, 6, 1, 1, 'Bộ Giáo trình Khí tượng Hàng không & Dẫn đường bay cơ bản', 'Mình học xong kỳ trước môn Khí tượng chuyên ngành, sách còn rất mới do giữ kỹ bọc bìa kiếng, có sẵn ghi chú đề cương ôn tập giữa kỳ và cuối kỳ cho bạn nào cần.', 'Tang mien phi', 0, 'Còn mới 95%, không rách', 'giao_trinh_khi_tuong.jpg', 'Da duyet', 42),
(2, 2, 2, 2, 'Áo đồng phục VAA Nam size L kèm Caravat Học viện', 'Mình tăng cân nên mặc chật áo đồng phục truyền thống VAA size L, để lại cho bạn nào tân sinh viên cần dùng học thể chất và chào cờ. Tặng kèm luôn caravat sọc chuẩn.', 'Thanh ly', 60000, 'Mặc vài lần, còn sạch đẹp', 'dong_phuc_vaa.jpg', 'Da duyet', 35),
(3, 3, 3, 2, 'Máy tính cầm tay Casio fx-580VN X chính hãng Tem Bitex', 'Máy tính bấm giải phương trình, tích phân và ma trận cực nhanh cho các môn Toán cao cấp và Vật lý đại cương. Đầy đủ nắp đậy, màn hình sáng nét không trầy.', 'Thanh ly', 320000, 'Hoạt động hoàn hảo 98%', 'casio_580.jpg', 'Da duyet', 58),
(4, 4, 4, 1, 'Ấm đun nước siêu tốc 1.8L Inox còn bảo hành', 'Ấm đun nhanh sôi trong 3 phút, tự ngắt điện an toàn khi sôi. Mình chuyển về ở chung trọ với bạn đã có sẵn đồ nên tặng lại cho bạn sinh viên nào mới lên trọ.', 'Tang mien phi', 0, 'Dùng tốt, sạch sẽ', 'am_sieu_toc.jpg', 'Da duyet', 29),
(5, 6, 1, 2, 'Giáo trình Tiếng Anh Chuyên ngành Hàng không (Aviation English Level 4)', 'Sách in màu chuẩn ICAO gồm đĩa CD nghe bài thi hội thoại buồng lái và kiểm soát viên không lưu. Rất hữu ích cho các bạn định hướng thi tuyển chuyên ngành.', 'Thanh ly', 75000, 'Sách bìa mềm, đủ bài tập', 'aviation_english.jpg', 'Da duyet', 64),
(6, 2, 5, 1, 'Chuột không dây Logitech Silent M220 học online', 'Chuột click êm không phát ra tiếng động, rất phù hợp ngồi học bài trong thư viện CS1 hoặc phòng trọ ban đêm không làm phiền bạn cùng phòng.', 'Thanh ly', 110000, 'Còn nguyên pin và đầu thu USB', 'chuot_logitech.jpg', 'Da duyet', 19),
(7, 3, 4, 3, 'Quạt đứng lỡ Senko 3 cánh gió mát êm', 'Quạt phòng trọ đang dùng tốt, có thể tăng giảm chiều cao, 3 cấp gió mát mẻ qua mùa nóng Sài Gòn. Tặng lại cho bạn nào khó khăn cần đồ dùng.', 'Tang mien phi', 0, 'Chạy êm, cánh nguyên vẹn', 'quat_senko.jpg', 'Dang giao dich', 81),
(8, 4, 2, 1, 'Bộ Cầu vai Sinh viên Học viện Hàng không Việt Nam (4 vạch)', 'Cầu vai nghi lễ trang phục trang trọng của trường, gắn cúc đồng sáng bóng. Để lại rẻ cho bạn sinh viên khóa mới tập duyệt nghi thức trường.', 'Thanh ly', 40000, 'Đầy đủ phụ kiện', 'cau_vai_vaa.jpg', 'Cho duyet', 8),
(9, 6, 3, 2, 'Bộ Thước vẽ kỹ thuật chuyên dụng & Bút chì kim Rotring 0.5', 'Dành cho các bạn sinh viên ngành Kỹ thuật Hàng không và Điện tử viễn thông học môn Vẽ kỹ thuật cơ sở. Tặng miễn phí cho bạn nào ghé CS2 nhận.', 'Tang mien phi', 0, 'Đầy đủ compa, eke, thước đo độ', 'thuoc_ve_ky_thuat.jpg', 'Cho duyet', 12);

-- 5. NẠP YÊU CẦU GIAO DỊCH MẪU
INSERT INTO `yeu_cau_giao_dich` (`id`, `do_dung_id`, `nguoi_gui_id`, `nguoi_nhan_id`, `loi_nhan`, `sdt_lien_he`, `trang_thai`) VALUES
(1, 7, 2, 3, 'Chào bạn Tính, mình ở trọ gần KTX muốn xin quạt đứng này về dùng. Chiều nay mình ghé phòng bạn lấy được không ạ?', '0912345678', 'Dong y'),
(2, 3, 4, 3, 'Bạn Tính ơi máy tính Casio còn không, mình học 25ĐHSA06 cần mua lại gấp trong tuần này để thi giữa kỳ.', '0934567890', 'Cho xac nhan');

-- 6. NẠP BÌNH LUẬN MẪU
INSERT INTO `binh_luan` (`id`, `do_dung_id`, `nguoi_dung_id`, `noi_dung`) VALUES
(1, 1, 3, 'Sách này còn không bạn ơi? Trưa mai mình có tiết học ở CS1 mình ghé thư viện nhận được không?'),
(2, 1, 6, 'Dạ sách vẫn còn nha bạn Tính ơi! Trưa mai tầm 11h30 mình ở sảnh A CS1 bạn cứ gọi số mình nhé!'),
(3, 5, 2, 'Cuốn Aviation English này có kèm file nghe audio không bạn ơi?');

-- 7. NẠP BÁO CÁO VI PHẠM MẪU (ĐỂ ADMIN DEMO)
INSERT INTO `bao_cao_vi_pham` (`id`, `do_dung_id`, `nguoi_bao_cao_id`, `ly_do`, `chi_tiet`, `trang_thai`) VALUES
(1, 8, 3, 'Nghi vấn thông tin không chính xác', 'Cầu vai chụp mờ cần người bán bổ sung thêm ảnh thật rõ nét mặt trước và cúc áo đồng.', 'Cho xu ly');
