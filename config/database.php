<?php
// ====================================================================
// HỌC VIỆN HÀNG KHÔNG VIỆT NAM - KHOA CÔNG NGHỆ THÔNG TIN
// MÔN: LẬP TRÌNH WEB - ĐỀ TÀI: VAA SHARE
// TÁC GIẢ PHỤ TRÁCH: VŨ HOÀNG KHÔI NGUYÊN (MSSV: 2531540127)
// TẬP TIN: config/database.php - CẤU HÌNH KẾT NỐI CƠ SỞ DỮ LIỆU
// ====================================================================

// Khai báo thông số kết nối MySQL trên XAMPP
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'vaa_share');
define('DB_PORT', 3306);

// Đường dẫn gốc của website trên trình duyệt (dùng cho link tuyệt đối)
define('BASE_URL', '/vaa_share');

/**
 * Hàm khởi tạo và trả về đối tượng kết nối MySQLi duy nhất (Singleton Pattern)
 * Giúp tối ưu tài nguyên, không mở lại nhiều kết nối trong cùng một lượt tải trang.
 */
function get_db() {
    static $conn = null;

    if ($conn === null) {
        // Tắt báo cáo lỗi mặc định dạng exception để tự xử lý thân thiện
        mysqli_report(MYSQLI_REPORT_OFF);

        // Khởi tạo kết nối MySQLi
        $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

        // Kiểm tra nếu kết nối thất bại
        if ($conn->connect_errno) {
            die("<div style='font-family:sans-serif; padding:20px; background:#fee2e2; color:#991b1b; border:1px solid #f87171; border-radius:8px; max-width:600px; margin:40px auto; line-height:1.6;'>"
                . "<h3 style='margin-top:0;'>⚠️ Lỗi kết nối Cơ sở dữ liệu MySQL</h3>"
                . "<p>Không thể kết nối đến CSDL <strong>" . DB_NAME . "</strong> trên máy chủ <strong>" . DB_HOST . "</strong>.</p>"
                . "<p><strong>Chi tiết lỗi:</strong> " . htmlspecialchars($conn->connect_error) . "</p>"
                . "<hr style='border:0; border-top:1px solid #fca5a5; margin:15px 0;'>"
                . "<p style='margin-bottom:0;'>👉 <em>Bạn hãy kiểm tra xem đã bấm <strong>Start MySQL</strong> trong XAMPP Control Panel chưa, và đã nạp file <strong>database/vaa_share.sql</strong> vào phpMyAdmin chưa nhé!</em></p>"
                . "</div>");
        }

        // BẮT BUỘC: Thiết lập bảng mã UTF-8 đầy đủ (utf8mb4) để tiếng Việt không bị lỗi font
        // Nếu thiếu dòng này, chữ tiếng Việt có dấu trong CSDL sẽ bị biến thành ký tự lạ
        $conn->set_charset('utf8mb4');
    }

    return $conn;
}
