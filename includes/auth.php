<?php
// ====================================================================
// HỌC VIỆN HÀNG KHÔNG VIỆT NAM - KHOA CÔNG NGHỆ THÔNG TIN
// MÔN: LẬP TRÌNH WEB - ĐỀ TÀI: VAA SHARE
// TÁC GIẢ PHỤ TRÁCH: VŨ HOÀNG KHÔI NGUYÊN (MSSV: 2531540127)
// TẬP TIN: includes/auth.php - XÁC THỰC, PHÂN QUYỀN VÀ QUẢN LÝ PHIÊN (SESSION)
// ====================================================================

require_once __DIR__ . '/../config/database.php';

// Khởi động phiên làm việc nếu chưa được bật
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Kiểm tra người dùng hiện tại đã đăng nhập hay chưa
 */
function is_logged_in() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
}

/**
 * Lấy mảng thông tin tài khoản đang đăng nhập hiện tại
 */
function current_user() {
    return $_SESSION['user'] ?? null;
}

/**
 * Kiểm tra người dùng có quyền Quản trị viên (Admin) hay không
 */
function is_admin() {
    return is_logged_in() && ($_SESSION['user']['vai_tro'] === 'Admin');
}

/**
 * Yêu cầu người dùng phải đăng nhập mới được truy cập trang.
 * Nếu chưa đăng nhập, tự động chuyển hướng về trang login.php kèm thông báo.
 */
function require_login() {
    if (!is_logged_in()) {
        $_SESSION['flash_error'] = 'Bạn cần đăng nhập tài khoản sinh viên VAA để thực hiện thao tác này!';
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

/**
 * Yêu cầu quyền Quản trị viên (Admin).
 * Nếu không phải Admin, chặn truy cập và chuyển về trang đăng nhập của Admin.
 */
function require_admin() {
    if (!is_admin()) {
        $_SESSION['flash_error'] = 'Khu vực này chỉ dành riêng cho Quản trị viên VAA Share!';
        header('Location: ' . BASE_URL . '/admin/login.php');
        exit;
    }
}

/**
 * Xử lý đăng nhập tài khoản bằng Email trường hoặc MSSV
 * Chống SQL Injection bằng Prepared Statement và mã hóa BCRYPT chống lộ mật khẩu
 */
function login_user($email_or_mssv, $password) {
    $db = get_db();
    $email_or_mssv = trim($email_or_mssv);

    // BƯỚC 1: Tìm tài khoản trong bảng nguoi_dung khớp với Email hoặc MSSV
    $stmt = $db->prepare("SELECT * FROM nguoi_dung WHERE (email = ? OR mssv = ?) LIMIT 1");
    $stmt->bind_param("ss", $email_or_mssv, $email_or_mssv);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();

    // BƯỚC 2: Kiểm tra sự tồn tại của tài khoản
    if (!$user) {
        return ['success' => false, 'message' => 'Tài khoản không tồn tại trong hệ thống sinh viên VAA!'];
    }

    // BƯỚC 3: Kiểm tra trạng thái tài khoản (có bị Admin khóa không)
    if ($user['trang_thai'] === 'Bi khoa') {
        return ['success' => false, 'message' => 'Tài khoản này đã bị Quản trị viên tạm khóa do vi phạm quy chế!'];
    }

    // BƯỚC 4: So sánh mật khẩu bằng password_verify (so sánh với chuỗi băm BCRYPT)
    if (!password_verify($password, $user['mat_khau'])) {
        return ['success' => false, 'message' => 'Mật khẩu đăng nhập không chính xác! Vui lòng thử lại.'];
    }

    // BƯỚC 5: Đăng nhập thành công -> Lưu dữ liệu an toàn vào biến phiên $_SESSION
    $_SESSION['user'] = [
        'id'            => (int) $user['id'],
        'mssv'          => $user['mssv'],
        'ho_ten'        => $user['ho_ten'],
        'email'         => $user['email'],
        'so_dien_thoai' => $user['so_dien_thoai'],
        'zalo'          => $user['zalo'] ?: $user['so_dien_thoai'],
        'khoa_lop'      => $user['khoa_lop'],
        'vai_tro'       => $user['vai_tro']
    ];

    return ['success' => true, 'user' => $_SESSION['user']];
}

/**
 * Đăng xuất an toàn: Xóa dữ liệu phiên và hủy Session
 */
function logout_user() {
    unset($_SESSION['user']);
    session_destroy();
}
