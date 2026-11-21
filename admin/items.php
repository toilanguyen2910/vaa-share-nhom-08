<?php
// ====================================================================
// HỌC VIỆN HÀNG KHÔNG VIỆT NAM - KHOA CÔNG NGHỆ THÔNG TIN
// MÔN: LẬP TRÌNH WEB - ĐỀ TÀI: VAA SHARE
// TÁC GIẢ PHỤ TRÁCH: VŨ HOÀNG KHÔI NGUYÊN (MSSV: 2531540127)
// TẬP TIN: admin/items.php - DUYỆT BÀI ĐĂNG & QUẢN LÝ TIN
// ====================================================================

$adminTitle = 'Quản lý & Duyệt bài đăng';
$adminActive = 'items';
require_once __DIR__ . '/_layout_top.php';

// XỬ LÝ CÁC HÀNH ĐỘNG DUYỆT / ẨN / XÓA BÀI ĐĂNG
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $itemId = (int) ($_POST['item_id'] ?? 0);

    if ($itemId > 0) {
        if ($action === 'approve') {
            $stmt = $db->prepare("UPDATE do_dung SET trang_thai = 'Da duyet' WHERE id = ?");
            $stmt->bind_param("i", $itemId);
            $stmt->execute();
            $stmt->close();
            $_SESSION['flash_success'] = "Đã phê duyệt tin đăng #$itemId thành công!";
        } elseif ($action === 'hide') {
            $stmt = $db->prepare("UPDATE do_dung SET trang_thai = 'Da an' WHERE id = ?");
            $stmt->bind_param("i", $itemId);
            $stmt->execute();
            $stmt->close();
            $_SESSION['flash_success'] = "Đã ẩn tin đăng #$itemId khỏi hệ thống!";
        } elseif ($action === 'delete') {
            $stmt = $db->prepare("DELETE FROM do_dung WHERE id = ?");
            $stmt->bind_param("i", $itemId);
            $stmt->execute();
            $stmt->close();
            $_SESSION['flash_success'] = "Đã xóa vĩnh viễn tin đăng #$itemId!";
        }
    }
    header('Location: ' . BASE_URL . '/admin/items.php');
    exit;
}

$statusFilter = trim($_GET['status'] ?? '');
$sql = "SELECT dd.*, dm.ten_danh_muc, dl.ten_dia_diem, nd.ho_ten, nd.mssv
        FROM do_dung dd
        JOIN danh_muc dm ON dd.danh_muc_id = dm.id
        JOIN dia_diem dl ON dd.dia_diem_id = dl.id
        JOIN nguoi_dung nd ON dd.nguoi_dung_id = nd.id
        WHERE 1=1";

if (!empty($statusFilter)) {
    $sql .= " AND dd.trang_thai = '" . $db->real_escape_string($statusFilter) . "'";
}
$sql .= " ORDER BY dd.id DESC";

$items = $db->query($sql)->fetch_all(MYSQLI_ASSOC);
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
  <div>
    <h1 style="font-size:1.8rem; font-weight:800; color:var(--vaa-dark);">Quản lý & Kiểm duyệt tin đăng</h1>
    <p style="color:var(--vaa-muted);">Kiểm tra nội dung tin đăng từ sinh viên trước khi cho phép hiển thị ra cộng đồng</p>
  </div>
  
  <!-- BỘ LỌC TRẠNG THÁI -->
  <div style="display:flex; gap:8px;">
    <a href="<?php echo BASE_URL; ?>/admin/items.php" class="btn <?php echo empty($statusFilter) ? 'btn-primary' : 'btn-outline'; ?>" style="font-size:0.85rem; padding:6px 12px;">Tất cả</a>
    <a href="<?php echo BASE_URL; ?>/admin/items.php?status=Cho+duyet" class="btn <?php echo $statusFilter === 'Cho duyet' ? 'btn-primary' : 'btn-outline'; ?>" style="font-size:0.85rem; padding:6px 12px;">⏳ Chờ duyệt</a>
    <a href="<?php echo BASE_URL; ?>/admin/items.php?status=Da+duyet" class="btn <?php echo $statusFilter === 'Da duyet' ? 'btn-primary' : 'btn-outline'; ?>" style="font-size:0.85rem; padding:6px 12px;">✓ Đã duyệt</a>
    <a href="<?php echo BASE_URL; ?>/admin/items.php?status=Da+an" class="btn <?php echo $statusFilter === 'Da an' ? 'btn-primary' : 'btn-outline'; ?>" style="font-size:0.85rem; padding:6px 12px;">✕ Đã ẩn</a>
  </div>
</div>

<div class="table-responsive" style="background:#fff; border-radius:var(--vaa-radius); box-shadow:var(--vaa-shadow);">
  <table class="data-table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Ảnh</th>
        <th>Tiêu đề món đồ</th>
        <th>Sinh viên đăng</th>
        <th>Danh mục & Cơ sở</th>
        <th>Giá</th>
        <th>Trạng thái</th>
        <th>Thao tác Admin</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($items)): ?>
        <tr><td colspan="8" style="text-align:center; padding:30px; color:#94a3b8;">Không có bài đăng nào thỏa mãn bộ lọc.</td></tr>
      <?php else: ?>
        <?php foreach ($items as $it): ?>
          <tr>
            <td>#<?php echo $it['id']; ?></td>
            <td style="width:60px;">
              <img src="<?php echo BASE_URL; ?>/uploads/<?php echo e($it['hinh_anh']); ?>" style="width:50px; height:40px; object-fit:cover; border-radius:6px;">
            </td>
            <td>
              <strong><a href="<?php echo BASE_URL; ?>/detail.php?id=<?php echo $it['id']; ?>" target="_blank"><?php echo e($it['tieu_de']); ?></a></strong><br>
              <small style="color:var(--vaa-muted);">🕒 <?php echo date('d/m/Y H:i', strtotime($it['ngay_dang'])); ?></small>
            </td>
            <td>
              <?php echo e($it['ho_ten']); ?><br>
              <small style="color:var(--vaa-muted);">MSSV: <?php echo e($it['mssv']); ?></small>
            </td>
            <td>
              <?php echo e($it['ten_danh_muc']); ?><br>
              <small style="color:var(--vaa-muted);">📍 <?php echo e($it['ten_dia_diem']); ?></small>
            </td>
            <td><?php echo format_price($it['gia']); ?></td>
            <td>
              <?php if ($it['trang_thai'] === 'Cho duyet'): ?>
                <span class="badge" style="background:#fef3c7; color:#b45309;">Chờ duyệt</span>
              <?php elseif ($it['trang_thai'] === 'Da duyet'): ?>
                <span class="badge" style="background:#dcfce7; color:#15803d;">Đang hiện</span>
              <?php elseif ($it['trang_thai'] === 'Dang giao dich'): ?>
                <span class="badge" style="background:#ffedd5; color:#c2410c;">Đang hẹn</span>
              <?php elseif ($it['trang_thai'] === 'Hoan thanh'): ?>
                <span class="badge" style="background:#f1f5f9; color:#475569;">Đã trao/bán</span>
              <?php else: ?>
                <span class="badge" style="background:#fee2e2; color:#b91c1c;">Đã ẩn</span>
              <?php endif; ?>
            </td>
            <td>
              <div style="display:flex; gap:6px;">
                <?php if ($it['trang_thai'] !== 'Da duyet'): ?>
                  <form action="<?php echo BASE_URL; ?>/admin/items.php" method="POST">
                    <input type="hidden" name="action" value="approve">
                    <input type="hidden" name="item_id" value="<?php echo $it['id']; ?>">
                    <button type="submit" class="btn btn-success" style="padding:4px 8px; font-size:0.75rem;" title="Phê duyệt cho hiển thị">✓ Duyệt</button>
                  </form>
                <?php endif; ?>

                <?php if ($it['trang_thai'] !== 'Da an'): ?>
                  <form action="<?php echo BASE_URL; ?>/admin/items.php" method="POST">
                    <input type="hidden" name="action" value="hide">
                    <input type="hidden" name="item_id" value="<?php echo $it['id']; ?>">
                    <button type="submit" class="btn btn-outline" style="padding:4px 8px; font-size:0.75rem; color:#f59e0b;" title="Ẩn bài vi phạm">Ẩn</button>
                  </form>
                <?php endif; ?>

                <form action="<?php echo BASE_URL; ?>/admin/items.php" method="POST">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="item_id" value="<?php echo $it['id']; ?>">
                  <button type="submit" class="btn btn-outline" style="padding:4px 8px; font-size:0.75rem; color:#ef4444;" onclick="return confirm('Chắc chắn muốn xóa hẳn tin này?')">Xóa</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/_layout_bottom.php'; ?>
