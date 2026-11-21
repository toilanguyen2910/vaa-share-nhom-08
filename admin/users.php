<?php
// ====================================================================
// HỌC VIỆN HÀNG KHÔNG VIỆT NAM - KHOA CÔNG NGHỆ THÔNG TIN
// MÔN: LẬP TRÌNH WEB - ĐỀ TÀI: VAA SHARE
// TÁC GIẢ PHỤ TRÁCH: VŨ HOÀNG KHÔI NGUYÊN (MSSV: 2531540127)
// TẬP TIN: admin/users.php - QUẢN LÝ TÀI KHOẢN SINH VIÊN
// ====================================================================

$adminTitle = 'Quản lý sinh viên';
$adminActive = 'users';
require_once __DIR__ . '/_layout_top.php';

// XỬ LÝ KHÓA / MỞ KHÓA HOẶC CẤP TÀI KHOẢN MỚI
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // 1. KHÓA HOẶC MỞ KHÓA TÀI KHOẢN
    if ($action === 'toggle_lock') {
        $userId    = (int) ($_POST['user_id'] ?? 0);
        $newStatus = $_POST['new_status'] ?? 'Hoat dong';

        // Không cho phép tự khóa tài khoản Admin hiện tại
        if ($userId === $currentUser['id']) {
            $_SESSION['flash_error'] = 'Bạn không thể tự khóa tài khoản của chính mình!';
        } else {
            $stmt = $db->prepare("UPDATE nguoi_dung SET trang_thai = ? WHERE id = ?");
            $stmt->bind_param("si", $newStatus, $userId);
            $stmt->execute();
            $stmt->close();
            $_SESSION['flash_success'] = "Đã cập nhật trạng thái tài khoản sinh viên thành '$newStatus'!";
        }
    }

    // 2. CẤP TÀI KHOẢN SINH VIÊN MỚI (CHỨC NĂNG TẠO USER)
    if ($action === 'create_user') {
        $mssv     = trim($_POST['mssv'] ?? '');
        $ho_ten   = trim($_POST['ho_ten'] ?? '');
        $sdt      = trim($_POST['so_dien_thoai'] ?? '');
        $khoa_lop = trim($_POST['khoa_lop'] ?? '25ĐHSA02');
        $pass     = trim($_POST['mat_khau'] ?? '123456');

        if (!empty($mssv) && !empty($ho_ten)) {
            // Tự động tạo email trường theo chuẩn <mssv>@vaa.edu.vn
            $email = $mssv . '@vaa.edu.vn';
            $hashPass = password_hash($pass, PASSWORD_DEFAULT);

            // Kiểm tra trùng MSSV
            $check = $db->prepare("SELECT id FROM nguoi_dung WHERE mssv = ? LIMIT 1");
            $check->bind_param("s", $mssv);
            $check->execute();
            if ($check->get_result()->fetch_assoc()) {
                $_SESSION['flash_error'] = "Mã số sinh viên $mssv đã tồn tại trên hệ thống!";
            } else {
                $stmt = $db->prepare("INSERT INTO nguoi_dung (mssv, ho_ten, email, mat_khau, so_dien_thoai, zalo, khoa_lop, vai_tro, trang_thai)
                                      VALUES (?, ?, ?, ?, ?, ?, ?, 'Sinh vien', 'Hoat dong')");
                $stmt->bind_param("sssssss", $mssv, $ho_ten, $email, $hashPass, $sdt, $sdt, $khoa_lop);
                $stmt->execute();
                $stmt->close();
                $_SESSION['flash_success'] = "Đã cấp tài khoản thành công cho sinh viên $ho_ten (MSSV: $mssv)!";
            }
            $check->close();
        } else {
            $_SESSION['flash_error'] = "Vui lòng nhập đầy đủ MSSV và Họ tên!";
        }
    }

    header('Location: ' . BASE_URL . '/admin/users.php');
    exit;
}

// LẤY DANH SÁCH SINH VIÊN
$users = $db->query("SELECT * FROM nguoi_dung WHERE vai_tro = 'Sinh vien' ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);
?>

<div style="margin-bottom: 24px;">
  <h1 style="font-size:1.8rem; font-weight:800; color:var(--vaa-dark);">Quản lý Danh sách sinh viên</h1>
  <p style="color:var(--vaa-muted);">Theo dõi thành viên tham gia, kiểm soát quyền hạn và cấp tài khoản sinh viên VAA</p>
</div>

<div style="display:grid; grid-template-columns: 1fr 340px; gap:24px; align-items:start;">

  <!-- BẢNG DANH SÁCH SINH VIÊN -->
  <div class="table-responsive" style="background:#fff; border-radius:var(--vaa-radius); box-shadow:var(--vaa-shadow);">
    <table class="data-table">
      <thead>
        <tr>
          <th>MSSV</th>
          <th>Họ và tên</th>
          <th>Email & Lớp</th>
          <th>SĐT / Zalo</th>
          <th>Trạng thái</th>
          <th>Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
          <tr>
            <td><strong><?php echo e($u['mssv']); ?></strong></td>
            <td><?php echo e($u['ho_ten']); ?></td>
            <td>
              <small><?php echo e($u['email']); ?></small><br>
              <span class="badge" style="background:#f1f5f9; color:#475569; font-size:0.75rem;"><?php echo e($u['khoa_lop']); ?></span>
            </td>
            <td><?php echo e($u['so_dien_thoai']); ?></td>
            <td>
              <?php if ($u['trang_thai'] === 'Hoat dong'): ?>
                <span class="badge" style="background:#dcfce7; color:#15803d;">Hoạt động</span>
              <?php else: ?>
                <span class="badge" style="background:#fee2e2; color:#b91c1c;">Bị khóa</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($u['trang_thai'] === 'Hoat dong'): ?>
                <form action="<?php echo BASE_URL; ?>/admin/users.php" method="POST" style="display:inline;">
                  <input type="hidden" name="action" value="toggle_lock">
                  <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                  <input type="hidden" name="new_status" value="Bi khoa">
                  <button type="submit" class="btn btn-outline" style="padding:4px 8px; font-size:0.75rem; color:#ef4444;" onclick="return confirm('Chắc chắn muốn khóa tài khoản này?')">🔒 Khóa</button>
                </form>
              <?php else: ?>
                <form action="<?php echo BASE_URL; ?>/admin/users.php" method="POST" style="display:inline;">
                  <input type="hidden" name="action" value="toggle_lock">
                  <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                  <input type="hidden" name="new_status" value="Hoat dong">
                  <button type="submit" class="btn btn-success" style="padding:4px 8px; font-size:0.75rem;">🔓 Mở khóa</button>
                </form>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- FORM CẤP TÀI KHOẢN MỚI CHO SINH VIÊN -->
  <div style="background:#fff; border:1px solid var(--vaa-border); border-radius:var(--vaa-radius); padding:20px; box-shadow:var(--vaa-shadow);">
    <h3 style="font-size:1.1rem; margin-bottom:16px; border-bottom:1px solid var(--vaa-border); padding-bottom:10px;">➕ Cấp tài khoản mới</h3>

    <form action="<?php echo BASE_URL; ?>/admin/users.php" method="POST">
      <input type="hidden" name="action" value="create_user">
      <div class="form-group">
        <label>Mã số sinh viên (MSSV): <span style="color:red;">*</span></label>
        <input type="text" name="mssv" class="form-control" placeholder="Ví dụ: 2531540999" required>
      </div>

      <div class="form-group">
        <label>Họ và tên: <span style="color:red;">*</span></label>
        <input type="text" name="ho_ten" class="form-control" placeholder="Ví dụ: Nguyễn Văn An" required>
      </div>

      <div class="form-group">
        <label>Khoa / Lớp:</label>
        <input type="text" name="khoa_lop" class="form-control" value="25ĐHSA02 - Khoa CNTT">
      </div>

      <div class="form-group">
        <label>Số điện thoại / Zalo:</label>
        <input type="text" name="so_dien_thoai" class="form-control" placeholder="0901234567" required>
      </div>

      <div class="form-group">
        <label>Mật khẩu khởi tạo:</label>
        <input type="password" name="mat_khau" class="form-control" value="123456" required>
        <small style="color:var(--vaa-muted);">Mặc định: 123456</small>
      </div>

      <button type="submit" class="btn btn-primary btn-block">Cấp tài khoản</button>
    </form>
  </div>

</div>

<?php require_once __DIR__ . '/_layout_bottom.php'; ?>
