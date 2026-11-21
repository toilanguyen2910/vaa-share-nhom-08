<?php
// ====================================================================
// HỌC VIỆN HÀNG KHÔNG VIỆT NAM - KHOA CÔNG NGHỆ THÔNG TIN
// MÔN: LẬP TRÌNH WEB - ĐỀ TÀI: VAA SHARE
// TÁC GIẢ PHỤ TRÁCH: VŨ HOÀNG KHÔI NGUYÊN (MSSV: 2531540127)
// TẬP TIN: admin/categories.php - QUẢN LÝ DANH MỤC THỂ LOẠI (CRUD)
// ====================================================================

$adminTitle = 'Quản lý danh mục';
$adminActive = 'categories';
require_once __DIR__ . '/_layout_top.php';

// XỬ LÝ THÊM HOẶC XÓA DANH MỤC
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // 1. THÊM DANH MỤC MỚI
    if ($action === 'add') {
        $ten_danh_muc = trim($_POST['ten_danh_muc'] ?? '');
        $mo_ta        = trim($_POST['mo_ta'] ?? '');
        $thu_tu       = (int) ($_POST['thu_tu'] ?? 0);

        if (!empty($ten_danh_muc)) {
            $stmt = $db->prepare("INSERT INTO danh_muc (ten_danh_muc, mo_ta, thu_tu) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $ten_danh_muc, $mo_ta, $thu_tu);
            $stmt->execute();
            $stmt->close();
            $_SESSION['flash_success'] = "Đã thêm danh mục '$ten_danh_muc' thành công!";
        } else {
            $_SESSION['flash_error'] = "Tên danh mục không được để trống!";
        }
    }

    // 2. XÓA DANH MỤC
    if ($action === 'delete') {
        $catId = (int) ($_POST['cat_id'] ?? 0);
        // Kiểm tra xem danh mục có đồ dùng nào không
        $check = $db->query("SELECT COUNT(*) AS c FROM do_dung WHERE danh_muc_id = $catId")->fetch_assoc()['c'];
        if ($check > 0) {
            $_SESSION['flash_error'] = "Không thể xóa danh mục này vì đang có $check món đồ liên kết!";
        } else {
            $db->query("DELETE FROM danh_muc WHERE id = $catId");
            $_SESSION['flash_success'] = "Đã xóa danh mục thành công!";
        }
    }

    header('Location: ' . BASE_URL . '/admin/categories.php');
    exit;
}

$categories = get_categories();
?>

<div style="margin-bottom: 24px;">
  <h1 style="font-size:1.8rem; font-weight:800; color:var(--vaa-dark);">Quản lý Danh mục thể loại</h1>
  <p style="color:var(--vaa-muted);">Tạo mới và điều chỉnh các nhóm đồ dùng học tập tại Học viện Hàng không</p>
</div>

<div style="display:grid; grid-template-columns: 1fr 340px; gap:24px; align-items:start;">

  <!-- BẢNG DANH SÁCH DANH MỤC -->
  <div class="table-responsive" style="background:#fff; border-radius:var(--vaa-radius); box-shadow:var(--vaa-shadow);">
    <table class="data-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Tên danh mục</th>
          <th>Mô tả</th>
          <th>Số tin đăng</th>
          <th>Thứ tự</th>
          <th>Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($categories as $c): ?>
          <tr>
            <td>#<?php echo $c['id']; ?></td>
            <td><strong><?php echo e($c['ten_danh_muc']); ?></strong></td>
            <td style="color:var(--vaa-muted); font-size:0.85rem;"><?php echo e($c['mo_ta']); ?></td>
            <td><span class="badge" style="background:#e0f2fe; color:#0369a1;"><?php echo (int) $c['so_luong_tin']; ?> tin</span></td>
            <td><?php echo (int) $c['thu_tu']; ?></td>
            <td>
              <?php if ((int)$c['so_luong_tin'] === 0): ?>
                <form action="<?php echo BASE_URL; ?>/admin/categories.php" method="POST" style="display:inline;">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="cat_id" value="<?php echo $c['id']; ?>">
                  <button type="submit" class="btn btn-outline" style="padding:4px 8px; font-size:0.75rem; color:#ef4444;" onclick="return confirm('Xác nhận xóa danh mục này?')">Xóa</button>
                </form>
              <?php else: ?>
                <span style="font-size:0.75rem; color:#94a3b8;">Đang dùng</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- FORM THÊM DANH MỤC MỚI -->
  <div style="background:#fff; border:1px solid var(--vaa-border); border-radius:var(--vaa-radius); padding:20px; box-shadow:var(--vaa-shadow);">
    <h3 style="font-size:1.1rem; margin-bottom:16px; border-bottom:1px solid var(--vaa-border); padding-bottom:10px;">➕ Thêm danh mục mới</h3>
    
    <form action="<?php echo BASE_URL; ?>/admin/categories.php" method="POST">
      <input type="hidden" name="action" value="add">
      <div class="form-group">
        <label>Tên danh mục: <span style="color:red;">*</span></label>
        <input type="text" name="ten_danh_muc" class="form-control" placeholder="Ví dụ: Thiết bị hàng không..." required>
      </div>

      <div class="form-group">
        <label>Mô tả tóm tắt:</label>
        <textarea name="mo_ta" class="form-control" rows="3" placeholder="Mô tả nhóm đồ dùng này..."></textarea>
      </div>

      <div class="form-group">
        <label>Thứ tự sắp xếp:</label>
        <input type="number" name="thu_tu" class="form-control" value="0">
      </div>

      <button type="submit" class="btn btn-primary btn-block">Tạo danh mục</button>
    </form>
  </div>

</div>

<?php require_once __DIR__ . '/_layout_bottom.php'; ?>
