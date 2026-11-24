# ✈️ VAA SHARE — NỀN TẢNG TRAO ĐỔI VÀ BÁN LẠI ĐỒ DÙNG NỘI BỘ SINH VIÊN VAA

> **HỌC PHẦN:** LẬP TRÌNH WEB (MÃ HP: 010151101410 — LỚP: 25ĐHSA06)  
> **ĐƠN VỊ:** KHOA CÔNG NGHỆ THÔNG TIN — HỌC VIỆN HÀNG KHÔNG VIỆT NAM (VAA)  
> **GIẢNG VIÊN HƯỚNG DẪN:** ThS. Huỳnh Thanh Sơn  
> **NHÓM THỰC HIỆN:** NHÓM 08  

---

## 👥 THÀNH VIÊN NHÓM 08

| STT | Họ và tên | MSSV | Lớp sinh hoạt | Vai trò | Phụ trách chính |
| :---: | :--- | :---: | :---: | :---: | :--- |
| **1** | **Vũ Hoàng Khôi Nguyên** | **2531540127** | 25ĐHSA02 | **Nhóm trưởng** | Thiết kế CSDL, Auth BCRYPT/Session, Phân hệ Admin, Tổng hợp Báo cáo & Slide |
| **2** | **Vũ Nguyễn Đức Tính** | **2531540131** | 25ĐHSA02 | Thành viên | Trang chủ, Trang danh mục & Bộ lọc đa chiều, Các hàm truy vấn `functions.php` |
| **3** | **Diệp Vĩnh Long** | **2531540054** | 25ĐHSA01 | Thành viên | Trang chi tiết món đồ, Đăng tin upload ảnh, Quản lý Đồ của tôi & Duyệt yêu cầu P2P |

---

## 🌟 GIỚI THIỆU ĐỀ TÀI

**VAA Share** là nền tảng trực tuyến phi thương mại phục vụ riêng cho cộng đồng sinh viên Học viện Hàng không Việt Nam (cả Cơ sở 1 - 104 Nguyễn Văn Trỗi và Cơ sở 2 - 18A/1 Cộng Hòa).  
Website giải quyết bài toán lãng phí giáo trình chuyên ngành hàng không, tài liệu ôn thi, đồng phục sinh viên, máy tính cầm tay và đồ dùng cá nhân; đồng thời giúp sinh viên khóa dưới tiếp cận tài liệu học tập với chi phí 0đ (trao tặng) hoặc giá pass lại sinh viên.

---

## 🛠️ CÔNG NGHỆ VÀ KIẾN TRÚC

- **Ngôn ngữ Backend:** Pure PHP (PHP 8.2) — Không dùng framework nặng, tuân thủ 100% chuẩn kỹ thuật môn học.
- **Cơ sở dữ liệu:** MariaDB / MySQL (10.4) qua XAMPP, bảng mã `utf8mb4_unicode_ci`, lưu trữ `InnoDB` có ràng buộc khóa ngoại (Foreign Keys).
- **Giao diện Frontend:** HTML5 ngữ nghĩa, CSS3 thuần (hệ màu nhận diện VAA Aero-Tech), Responsive co giãn mượt mà trên Desktop và Thiết bị di động.
- **Bảo mật hệ thống:**
  - Chống **SQL Injection** 100% bằng **Prepared Statements** (`$stmt->prepare()`, `$stmt->bind_param()`).
  - Chống lộ mật khẩu bằng mã hóa một chiều **BCRYPT** (`password_hash()`, `password_verify()`).
  - Chống tấn công **XSS** bằng hàm làm sạch dữ liệu đầu ra `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')`.
  - Phân quyền nghiêm ngặt theo **Role Session** (`Admin`, `Sinh vien`, `Giang vien`).

---

## 🚀 HƯỚNG DẪN CÀI ĐẶT & CHẠY LOCALHOST

### 1. Yêu cầu môi trường
- Máy tính đã cài đặt **XAMPP** (hỗ trợ PHP 8.x và MySQL/MariaDB).
- Trình duyệt web hiện đại (Google Chrome, Microsoft Edge, Cốc Cốc, Brave...).

### 2. Các bước triển khai
1. **Khởi động dịch vụ:** Mở *XAMPP Control Panel*, nhấn **Start** cho cả **Apache** (cổng 80) và **MySQL** (cổng 3306).
2. **Đưa mã nguồn vào XAMPP:**
   - Đặt thư mục dự án vào đường dẫn:  
     `C:\xampp\htdocs\vaa_share`  
     *(hoặc tạo Symbolic Link / Junction từ thư mục chứa code trỏ vào `C:\xampp\htdocs\vaa_share`).*
3. **Import cơ sở dữ liệu:**
   - Mở trình duyệt vào [http://localhost/phpmyadmin](http://localhost/phpmyadmin).
   - Tạo cơ sở dữ liệu mới có tên: **`vaa_share`** (chọn bảng mã `utf8mb4_unicode_ci`).
   - Nhấn tab **Nhập (Import)** $\rightarrow$ Chọn tập tin:  
     `database/vaa_share.sql`  
     $\rightarrow$ Nhấn **Thực hiện (Go)** để nạp toàn bộ cấu trúc 7 bảng và dữ liệu mẫu VAA.
4. **Trải nghiệm ứng dụng:**
   - Mở trình duyệt truy cập: 👉 **http://localhost/vaa_share/**

---

## 🔑 TÀI KHOẢN TRẢI NGHIỆM HỆ THỐNG

| Vai trò | Tài khoản đăng nhập (MSSV / Email) | Mật khẩu mặc định | Ghi chú |
| :--- | :--- | :---: | :--- |
| **Quản trị viên (Admin)** | `admin@vaa.edu.vn` *(hoặc `VAA-ADM01`)* | `123456` | Toàn quyền Quản trị, Duyệt tin, Khóa tài khoản, Báo cáo |
| **Sinh viên (Khôi Nguyên)** | `2531540127` *(hoặc `2531540127@vaa.edu.vn`)* | `123456` | Nhóm trưởng Nhóm 8 |
| **Sinh viên (Đức Tính)** | `2531540131` *(hoặc `2531540131@vaa.edu.vn`)* | `123456` | Thành viên Nhóm 8 |
| **Sinh viên (Vĩnh Long)** | `2531540054` *(hoặc `2531540054@vaa.edu.vn`)* | `123456` | Thành viên Nhóm 8 |
| **Giảng viên mẫu** | `sonht@vaa.edu.vn` *(hoặc `GV-SONHT`)* | `123456` | Giảng viên hướng dẫn |

*(Tại trang đăng nhập [http://localhost/vaa_share/login.php](http://localhost/vaa_share/login.php) đã có sẵn các nút **Bấm 1-Click** để tự động điền nhanh tài khoản phục vụ Thầy Cô chấm thi).*

---

## 📁 CẤU TRÚC THƯ MỤC DỰ ÁN

```text
vaa_share/
├── admin/                         # Phân hệ quản trị dành riêng cho Admin
│   ├── _layout_top.php            # Bố cục sidebar & chặn quyền truy cập trái phép
│   ├── _layout_bottom.php         # Phần chân trang admin
│   ├── index.php                  # Dashboard thống kê KPIs
│   ├── items.php                  # Phê duyệt / Từ chối tin đăng mới
│   ├── users.php                  # Quản lý tài khoản (Khóa/Mở, Thêm người dùng)
│   ├── categories.php             # Thêm, sửa, xóa danh mục đồ dùng
│   ├── reports.php                # Tiếp nhận & xử lý báo cáo vi phạm
│   └── login.php                  # Đăng nhập dành cho Quản trị viên
├── assets/
│   └── css/
│       └── style.css              # Hệ thống CSS Design Tokens & Responsive toàn trang
├── config/
│   └── database.php               # Kết nối CSDL MySQLi chuẩn utf8mb4
├── database/
│   └── vaa_share.sql              # Bản xuất cơ sở dữ liệu mẫu đầy đủ 7 bảng
├── includes/
│   ├── auth.php                   # Xác thực phiên (Session), băm BCRYPT, chống XSS
│   ├── functions.php              # Bộ thư viện hàm truy vấn SQL động (Prepared Statements)
│   ├── header.php                 # Thanh điều hướng (Navbar) chung
│   └── footer.php                 # Chân trang thông tin 2 cơ sở VAA
├── uploads/                       # Thư mục lưu trữ ảnh sinh viên tải lên máy chủ
├── index.php                      # Trang chủ (Hero search, 5 danh mục, tin mới nhất)
├── listing.php                    # Danh mục đồ dùng với Bộ lọc đa chiều 4 tiêu chí
├── detail.php                     # Chi tiết món đồ, chat Zalo 1-click, gửi yêu cầu P2P
├── post.php                       # Đăng tin mới kèm tải ảnh lên máy chủ
├── my_items.php                   # Quản lý Đồ của tôi & Duyệt yêu cầu nhận đồ (3 tabs)
├── about.php                      # Giới thiệu sứ mệnh dự án & thông tin Nhóm 8
├── login.php                      # Trang đăng nhập nội bộ sinh viên VAA
└── logout.php                     # Xử lý hủy phiên đăng xuất an toàn
```

---

## 📜 BẢN QUYỀN VÀ LƯU Ý
- Dự án được thực hiện phục vụ đánh giá cuối kỳ Học phần Lập trình Web — Học viện Hàng không Việt Nam.
- Toàn bộ nội dung và mã nguồn thuộc quyền sở hữu của Nhóm 08 (Lớp 25ĐHSA02 & 25ĐHSA01).
