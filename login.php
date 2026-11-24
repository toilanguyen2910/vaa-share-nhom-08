<?php
// ====================================================================
// HỌC VIỆN HÀNG KHÔNG VIỆT NAM - KHOA CÔNG NGHỆ THÔNG TIN
// MÔN: LẬP TRÌNH WEB - ĐỀ TÀI: VAA SHARE
// TÁC GIẢ PHỤ TRÁCH: VŨ HOÀNG KHÔI NGUYÊN (MSSV: 2531540127)
// TẬP TIN: login.php - ĐĂNG NHẬP HỆ THỐNG NỘI BỘ VAA
// ====================================================================

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

// Nếu đã đăng nhập rồi thì không cần đăng nhập lại
if (is_logged_in()) {
    if (is_admin()) {
        header('Location: ' . BASE_URL . '/admin/index.php');
    } else {
        header('Location: ' . BASE_URL . '/index.php');
    }
    exit;
}

$error = '';
$account = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $account  = trim($_POST['account'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($account) || empty($password)) {
        $error = 'Vui lòng nhập đầy đủ Email/MSSV và Mật khẩu!';
    } else {
        $result = login_user($account, $password);
        if ($result['success']) {
            $_SESSION['flash_success'] = 'Đăng nhập thành công! Chào mừng ' . $result['user']['ho_ten'] . '.';
            if ($result['user']['vai_tro'] === 'Admin') {
                header('Location: ' . BASE_URL . '/admin/index.php');
            } else {
                header('Location: ' . BASE_URL . '/index.php');
            }
            exit;
        } else {
            $error = $result['message'];
        }
    }
}

$pageTitle = 'Đăng nhập nội bộ VAA';
$activePage = 'login';
require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding: 40px 20px;">
  
  <div class="form-card" style="max-width: 480px;">
    
    <div style="text-align:center; margin-bottom:24px;">
      <span style="font-size:2.5rem;">✈️</span>
      <h2 style="font-size:1.5rem; font-weight:800; color:var(--vaa-dark); margin-top:8px;">CỔNG ĐĂNG NHẬP VAA SHARE</h2>
      <p style="color:var(--vaa-muted); font-size:0.88rem; margin-top:4px;">
        Hệ thống xác thực nội bộ sinh viên Học viện Hàng không Việt Nam
      </p>
    </div>

    <?php if (!empty($error)): ?>
      <div class="alert alert-error">
        ⚠️ <?php echo e($error); ?>
      </div>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>/login.php" method="POST">
      <div class="form-group">
        <label>Email sinh viên hoặc Mã số sinh viên (MSSV):</label>
        <input type="text" name="account" id="accountInput" value="<?php echo e($account); ?>" class="form-control" placeholder="Ví dụ: 2531540127 hoặc 2531540127@vaa.edu.vn" required autofocus>
      </div>

      <div class="form-group">
        <label>Mật khẩu:</label>
        <input type="password" name="password" id="passwordInput" class="form-control" placeholder="Nhập mật khẩu..." required>
      </div>

      <button type="submit" class="btn btn-primary btn-block" style="padding:12px; font-size:1rem; margin-top:10px;">
        Đăng nhập ngay
      </button>
    </form>

    <!-- NÚT BẤM 1-CLICK ĐIỀN NHANH DÀNH CHO THẦY CÔ CHẤM THI BẢO VỆ -->
    <div style="margin-top:24px; padding-top:20px; border-top:1px dashed var(--vaa-border);">
      <h4 style="font-size:0.85rem; text-transform:uppercase; color:var(--vaa-muted); margin-bottom:10px; text-align:center;">
        ⚡ Điền nhanh tài khoản Demo để chấm thi (Pass: 123456)
      </h4>
      
      <div style="display:flex; flex-direction:column; gap:8px;">
        <button type="button" class="btn btn-outline" style="font-size:0.82rem; padding:6px; justify-content:flex-start;" onclick="quickFill('2531540127', '123456')">
          👑 <strong>SV Khôi Nguyên</strong> (2531540127) — Nhóm trưởng
        </button>
        <button type="button" class="btn btn-outline" style="font-size:0.82rem; padding:6px; justify-content:flex-start;" onclick="quickFill('2531540131', '123456')">
          ⚙️ <strong>SV Đức Tính</strong> (2531540131) — Thành viên
        </button>
        <button type="button" class="btn btn-outline" style="font-size:0.82rem; padding:6px; justify-content:flex-start;" onclick="quickFill('2531540054', '123456')">
          ✨ <strong>SV Vĩnh Long</strong> (2531540054) — Thành viên
        </button>
        <button type="button" class="btn btn-outline" style="font-size:0.82rem; padding:6px; justify-content:flex-start; color:#0284c7; border-color:#bae6fd; background:#f0f9ff;" onclick="quickFill('admin@vaa.edu.vn', '123456')">
          🛡️ <strong>Quản trị viên (Admin)</strong> (admin@vaa.edu.vn)
        </button>
      </div>
    </div>

  </div>

</div>

<script>
function quickFill(user, pass) {
  document.getElementById('accountInput').value = user;
  document.getElementById('passwordInput').value = pass;
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
