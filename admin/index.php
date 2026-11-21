<?php
// ====================================================================
// HỌC VIỆN HÀNG KHÔNG VIỆT NAM - KHOA CÔNG NGHỆ THÔNG TIN
// MÔN: LẬP TRÌNH WEB - ĐỀ TÀI: VAA SHARE
// TÁC GIẢ PHỤ TRÁCH: VŨ HOÀNG KHÔI NGUYÊN (MSSV: 2531540127)
// TẬP TIN: admin/index.php - BẢNG ĐIỀU KHIỂN TỔNG QUAN (DASHBOARD)
// ====================================================================

$adminTitle = 'Tổng quan Dashboard';
$adminActive = 'dashboard';
require_once __DIR__ . '/_layout_top.php';

// TRUY VẤN THỐNG KÊ SỐ LIỆU TỔNG QUAN
$totalItems    = (int) $db->query("SELECT COUNT(*) AS c FROM do_dung")->fetch_assoc()['c'];
$pendingItems  = (int) $db->query("SELECT COUNT(*) AS c FROM do_dung WHERE trang_thai = 'Cho duyet'")->fetch_assoc()['c'];
$totalUsers    = (int) $db->query("SELECT COUNT(*) AS c FROM nguoi_dung WHERE vai_tro = 'Sinh vien'")->fetch_assoc()['c'];
$totalRequests = (int) $db->query("SELECT COUNT(*) AS c FROM yeu_cau_giao_dich")->fetch_assoc()['c'];

// LẤY DANH SÁCH 5 BÀI ĐĂNG CHỜ DUYỆT GẦN NHẤT
$sqlPending = "SELECT dd.*, dm.ten_danh_muc, dl.ten_dia_diem, nd.ho_ten, nd.mssv
               FROM do_dung dd
               JOIN danh_muc dm ON dd.danh_muc_id = dm.id
               JOIN dia_diem dl ON dd.dia_diem_id = dl.id
               JOIN nguoi_dung nd ON dd.nguoi_dung_id = nd.id
               WHERE dd.trang_thai = 'Cho duyet'
               ORDER BY dd.id DESC LIMIT 5";
$recentPending = $db->query($sqlPending)->fetch_all(MYSQLI_ASSOC);
?>

<div style="margin-bottom: 24px;">
  <h1 style="font-size:1.8rem; font-weight:800; color:var(--vaa-dark);">Bảng điều khiển Quản trị VAA Share</h1>
  <p style="color:var(--vaa-muted);">Tổng hợp tình hình hoạt động, số liệu bài đăng và yêu cầu giao dịch nội bộ</p>
</div>

<!-- 4 THẺ THỐNG KÊ (STAT CARDS) -->
<div class="stat-cards">
  <div class="stat-card" style="border-left: 4px solid var(--vaa-blue);">
    <div class="stat-card-title">Tổng tin đăng</div>
    <div class="stat-card-val"><?php echo $totalItems; ?></div>
  </div>

  <div class="stat-card" style="border-left: 4px solid #f59e0b;">
    <div class="stat-card-title">Tin chờ kiểm duyệt</div>
    <div class="stat-card-val" style="color: #d97706;"><?php echo $pendingItems; ?></div>
  </div>

  <div class="stat-card" style="border-left: 4px solid #10b981;">
    <div class="stat-card-title">Sinh viên tham gia</div>
    <div class="stat-card-val" style="color: #059669;"><?php echo $totalUsers; ?></div>
  </div>

  <div class="stat-card" style="border-left: 4px solid #6366f1;">
    <div class="stat-card-title">Lượt yêu cầu nhận đồ</div>
    <div class="stat-card-val" style="color: #4f46e5;"><?php echo $totalRequests; ?></div>
  </div>
</div>

<!-- DANH SÁCH TIN CHỜ DUYỆT CẦN XỬ LÝ -->
<div style="background:#fff; border:1px solid var(--vaa-border); border-radius:var(--vaa-radius); padding:24px; box-shadow:var(--vaa-shadow);">
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:18px;">
    <h3 style="font-size:1.15rem; font-weight:700;">
      ⏳ Tin đăng đang chờ kiểm duyệt (<?php echo count($recentPending); ?>)
    </h3>
    <a href="<?php echo BASE_URL; ?>/admin/items.php?status=Cho+duyet" style="font-size:0.85rem; font-weight:600;">Xem tất cả &rarr;</a>
  </div>

  <?php if (empty($recentPending)): ?>
    <p style="color:#059669; font-weight:500;">🎉 Tuyệt vời! Hiện không có bài đăng nào tồn đọng chờ duyệt.</p>
  <?php else: ?>
    <div class="table-responsive">
      <table class="data-table">
        <thead>
          <tr>
            <th>Ảnh</th>
            <th>Tên đồ dùng</th>
            <th>Người đăng</th>
            <th>Danh mục & Cơ sở</th>
            <th>Hình thức</th>
            <th>Thao tác duyệt</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recentPending as $pItem): ?>
            <tr>
              <td style="width:60px;">
                <img src="<?php echo BASE_URL; ?>/uploads/<?php echo e($pItem['hinh_anh']); ?>" style="width:50px; height:40px; object-fit:cover; border-radius:6px;">
              </td>
              <td>
                <strong><?php echo e($pItem['tieu_de']); ?></strong><br>
                <small style="color:var(--vaa-muted);">🕒 <?php echo date('d/m/Y H:i', strtotime($pItem['ngay_dang'])); ?></small>
              </td>
              <td>
                <?php echo e($pItem['ho_ten']); ?><br>
                <small style="color:var(--vaa-muted);">MSSV: <?php echo e($pItem['mssv']); ?></small>
              </td>
              <td>
                <?php echo e($pItem['ten_danh_muc']); ?><br>
                <small style="color:var(--vaa-muted);">📍 <?php echo e($pItem['ten_dia_diem']); ?></small>
              </td>
              <td><?php echo format_price($pItem['gia']); ?></td>
              <td>
                <form action="<?php echo BASE_URL; ?>/admin/items.php" method="POST" style="display:inline;">
                  <input type="hidden" name="action" value="approve">
                  <input type="hidden" name="item_id" value="<?php echo $pItem['id']; ?>">
                  <button type="submit" class="btn btn-success" style="padding:4px 10px; font-size:0.8rem;">✓ Duyệt</button>
                </form>
                <form action="<?php echo BASE_URL; ?>/admin/items.php" method="POST" style="display:inline;">
                  <input type="hidden" name="action" value="hide">
                  <input type="hidden" name="item_id" value="<?php echo $pItem['id']; ?>">
                  <button type="submit" class="btn btn-outline" style="padding:4px 10px; font-size:0.8rem; color:#ef4444;" onclick="return confirm('Từ chối bài đăng này?')">✕ Từ chối</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/_layout_bottom.php'; ?>
