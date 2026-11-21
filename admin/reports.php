<?php
// ====================================================================
// HỌC VIỆN HÀNG KHÔNG VIỆT NAM - KHOA CÔNG NGHỆ THÔNG TIN
// MÔN: LẬP TRÌNH WEB - ĐỀ TÀI: VAA SHARE
// TÁC GIẢ PHỤ TRÁCH: VŨ HOÀNG KHÔI NGUYÊN (MSSV: 2531540127)
// TẬP TIN: admin/reports.php - TIẾP NHẬN & XỬ LÝ BÁO CÁO VI PHẠM
// ====================================================================

$adminTitle = 'Xử lý báo cáo vi phạm';
$adminActive = 'reports';
require_once __DIR__ . '/_layout_top.php';

// XỬ LÝ CÁC HÀNH ĐỘNG
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action   = $_POST['action'] ?? '';
    $reportId = (int) ($_POST['report_id'] ?? 0);
    $itemId   = (int) ($_POST['item_id'] ?? 0);

    if ($action === 'resolve_hide') {
        // Ẩn món đồ vi phạm và đánh dấu đã xử lý
        $db->query("UPDATE do_dung SET trang_thai = 'Da an' WHERE id = $itemId");
        $db->query("UPDATE bao_cao_vi_pham SET trang_thai = 'Da xu ly' WHERE id = $reportId");
        $_SESSION['flash_success'] = "Đã ẩn món đồ #$itemId và đóng báo cáo vi phạm!";
    } elseif ($action === 'dismiss') {
        // Bỏ qua báo cáo (báo cáo sai/spam)
        $db->query("UPDATE bao_cao_vi_pham SET trang_thai = 'Da bo qua' WHERE id = $reportId");
        $_SESSION['flash_success'] = "Đã bỏ qua báo cáo #$reportId!";
    }

    header('Location: ' . BASE_URL . '/admin/reports.php');
    exit;
}

$reports = $db->query("SELECT bc.*, dd.tieu_de, dd.hinh_anh, dd.gia, nd.ho_ten AS ten_nguoi_bao_cao, nd.mssv AS mssv_nguoi_bao_cao
                       FROM bao_cao_vi_pham bc
                       JOIN do_dung dd ON bc.do_dung_id = dd.id
                       JOIN nguoi_dung nd ON bc.nguoi_bao_cao_id = nd.id
                       ORDER BY bc.id DESC")->fetch_all(MYSQLI_ASSOC);
?>

<div style="margin-bottom: 24px;">
  <h1 style="font-size:1.8rem; font-weight:800; color:var(--vaa-dark);">Tiếp nhận & Xử lý báo cáo sai phạm</h1>
  <p style="color:var(--vaa-muted);">Bảo vệ môi trường trao đổi văn minh, an toàn, không lừa đảo giữa sinh viên Học viện</p>
</div>

<div class="table-responsive" style="background:#fff; border-radius:var(--vaa-radius); box-shadow:var(--vaa-shadow);">
  <table class="data-table">
    <thead>
      <tr>
        <th>Món đồ bị phản ánh</th>
        <th>Sinh viên báo cáo</th>
        <th>Lý do & Chi tiết</th>
        <th>Ngày gửi</th>
        <th>Trạng thái</th>
        <th>Thao tác Admin</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($reports)): ?>
        <tr><td colspan="6" style="text-align:center; padding:30px; color:#10b981;">🎉 Không có báo cáo vi phạm nào tồn đọng!</td></tr>
      <?php else: ?>
        <?php foreach ($reports as $r): ?>
          <tr>
            <td>
              <strong><a href="<?php echo BASE_URL; ?>/detail.php?id=<?php echo $r['do_dung_id']; ?>" target="_blank"><?php echo e($r['tieu_de']); ?></a></strong><br>
              <?php echo format_price($r['gia']); ?>
            </td>
            <td>
              <strong><?php echo e($r['ten_nguoi_bao_cao']); ?></strong><br>
              <small style="color:var(--vaa-muted);">MSSV: <?php echo e($r['mssv_nguoi_bao_cao']); ?></small>
            </td>
            <td>
              <strong style="color:#ef4444;"><?php echo e($r['ly_do']); ?></strong><br>
              <small style="color:#475569;"><?php echo e($r['chi_tiet']); ?></small>
            </td>
            <td><?php echo date('d/m/Y H:i', strtotime($r['ngay_bao_cao'])); ?></td>
            <td>
              <?php if ($r['trang_thai'] === 'Cho xu ly'): ?>
                <span class="badge" style="background:#fef3c7; color:#b45309;">Chờ xử lý</span>
              <?php elseif ($r['trang_thai'] === 'Da xu ly'): ?>
                <span class="badge" style="background:#dcfce7; color:#15803d;">Đã xử lý (Ẩn tin)</span>
              <?php else: ?>
                <span class="badge" style="background:#f1f5f9; color:#475569;">Đã bỏ qua</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($r['trang_thai'] === 'Cho xu ly'): ?>
                <div style="display:flex; gap:6px;">
                  <form action="<?php echo BASE_URL; ?>/admin/reports.php" method="POST">
                    <input type="hidden" name="action" value="resolve_hide">
                    <input type="hidden" name="report_id" value="<?php echo $r['id']; ?>">
                    <input type="hidden" name="item_id" value="<?php echo $r['do_dung_id']; ?>">
                    <button type="submit" class="btn btn-danger" style="padding:4px 8px; font-size:0.75rem;" onclick="return confirm('Ẩn bài đăng này khỏi hệ thống?')">Ẩn bài</button>
                  </form>

                  <form action="<?php echo BASE_URL; ?>/admin/reports.php" method="POST">
                    <input type="hidden" name="action" value="dismiss">
                    <input type="hidden" name="report_id" value="<?php echo $r['id']; ?>">
                    <button type="submit" class="btn btn-outline" style="padding:4px 8px; font-size:0.75rem;">Bỏ qua</button>
                  </form>
                </div>
              <?php else: ?>
                <span style="color:#94a3b8; font-size:0.8rem;">Đã giải quyết</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . '/_layout_bottom.php'; ?>
