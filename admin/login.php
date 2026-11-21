<?php
// ====================================================================
// HỌC VIỆN HÀNG KHÔNG VIỆT NAM - KHOA CÔNG NGHỆ THÔNG TIN
// MÔN: LẬP TRÌNH WEB - ĐỀ TÀI: VAA SHARE
// TÁC GIẢ PHỤ TRÁCH: VŨ HOÀNG KHÔI NGUYÊN (MSSV: 2531540127)
// TẬP TIN: admin/login.php - CỔNG ĐĂNG NHẬP RIÊNG CỦA QUẢN TRỊ VIÊN
// ====================================================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// Nếu đã là Admin thì vào thẳng Dashboard
if (is_admin()) {
    header('Location: ' . BASE_URL . '/admin/index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $result = login_user($email, $password);
    if ($result['success']) {
        if ($result['user']['vai_tro'] === 'Admin') {
            $_SESSION['flash_success'] = 'Đăng nhập trang Quản trị thành công!';
            header('Location: ' . BASE_URL . '/admin/index.php');
            exit;
        } else {
            // Đăng nhập đúng nhưng không phải quyền Admin
            logout_user();
            $error = 'Tài khoản này là quyền Sinh viên, không được phép truy cập khu vực Quản trị!';
        }
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cổng Đăng nhập Quản trị viên | VAA Share Admin</title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body style="background: #0b192c; display:flex; align-items:center; justify-content:center; min-height:100vh;">

  <div style="background:#fff; border-radius:12px; padding:36px; width:100%; max-width:440px; box-shadow:0 20px 25px -5px rgba(0,0,0,0.3);">
    <div style="text-align:center; margin-bottom:24px;">
      <span style="font-size:2.5rem;">🛡️</span>
      <h2 style="font-size:1.4rem; font-weight:800; color:#0f172a; margin-top:8px;">HỆ THỐNG QUẢN TRỊ VAA SHARE</h2>
      <p style="color:#64748b; font-size:0.85rem; margin-top:4px;">Cổng dành riêng cho Ban Quản trị VAA</p>
    </div>

    <?php if (!empty($error)): ?>
      <div class="alert alert-error">
        ⚠️ <?php echo e($error); ?>
      </div>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>/admin/login.php" method="POST">
      <div class="form-group">
        <label>Email Quản trị viên:</label>
        <input type="email" name="email" id="adminEmail" class="form-control" placeholder="admin@vaa.edu.vn" required autofocus>
      </div>

      <div class="form-group">
        <label>Mật khẩu:</label>
        <input type="password" name="password" id="adminPass" class="form-control" placeholder="Nhập mật khẩu..." required>
      </div>

      <button type="submit" class="btn btn-primary btn-block" style="padding:12px; font-size:1rem; margin-top:10px;">
        Đăng nhập Quản trị
      </button>
    </form>

    <div style="margin-top:20px; padding-top:16px; border-top:1px dashed #cbd5e1; text-align:center;">
      <button type="button" onclick="document.getElementById('adminEmail').value='admin@vaa.edu.vn'; document.getElementById('adminPass').value='123456';" class="btn btn-outline" style="font-size:0.85rem; width:100%;">
        ⚡ Điền nhanh tài khoản Admin mẫu (Pass: 123456)
      </button>
      <p style="margin-top:14px; font-size:0.85rem;">
        <a href="<?php echo BASE_URL; ?>/index.php" style="color:#64748b;">&larr; Quay về trang người dùng</a>
      </p>
    </div>
  </div>

</body>
</html>
