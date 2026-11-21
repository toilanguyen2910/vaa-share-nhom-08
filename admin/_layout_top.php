<?php
// ====================================================================
// HỌC VIỆN HÀNG KHÔNG VIỆT NAM - KHOA CÔNG NGHỆ THÔNG TIN
// MÔN: LẬP TRÌNH WEB - ĐỀ TÀI: VAA SHARE
// TÁC GIẢ PHỤ TRÁCH: VŨ HOÀNG KHÔI NGUYÊN (MSSV: 2531540127)
// TẬP TIN: admin/_layout_top.php - BỐ CỤC TRÊN KHU VỰC QUẢN TRỊ
// ====================================================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

// BẢO VỆ NGHIÊM NGẶT: Chặn mọi truy cập nếu không có vai trò 'Admin'
require_admin();
$currentUser = current_user();
$db = get_db();

// Đếm số tin chờ duyệt để hiện huy hiệu đỏ trên menu
$resPending = $db->query("SELECT COUNT(*) AS total FROM do_dung WHERE trang_thai = 'Cho duyet'");
$pendingCount = (int) ($resPending->fetch_assoc()['total'] ?? 0);

// Đếm số báo cáo chờ xử lý
$resReports = $db->query("SELECT COUNT(*) AS total FROM bao_cao_vi_pham WHERE trang_thai = 'Cho xu ly'");
$reportsCount = (int) ($resReports->fetch_assoc()['total'] ?? 0);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($adminTitle) ? e($adminTitle) . ' | VAA Share Admin' : 'Quản trị hệ thống | VAA Share'; ?></title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
  <style>
    .admin-wrapper { display: grid; grid-template-columns: 250px 1fr; min-height: 100vh; }
    .admin-sidebar { background: #0b192c; color: #fff; padding: 24px 16px; border-right: 1px solid #1e293b; }
    .admin-menu { list-style: none; margin-top: 24px; display: flex; flex-direction: column; gap: 6px; }
    .admin-menu a { display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; border-radius: 8px; color: #cbd5e1; font-weight: 500; font-size: 0.92rem; }
    .admin-menu a:hover, .admin-menu a.active { background: #1e3e62; color: #38bdf8; }
    .admin-content { padding: 30px; background: #f8fafc; }
    .stat-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 30px; }
    .stat-card { background: #fff; padding: 20px; border-radius: 12px; border: 1px solid var(--vaa-border); box-shadow: var(--vaa-shadow); }
    .stat-card-title { font-size: 0.82rem; text-transform: uppercase; color: var(--vaa-muted); font-weight: 600; }
    .stat-card-val { font-size: 1.8rem; font-weight: 800; color: var(--vaa-dark); margin-top: 6px; }
  </style>
</head>
<body>

<div class="admin-wrapper">

  <!-- THANH ĐIỀU HƯỚNG BÊN TRÁI CỦA ADMIN -->
  <aside class="admin-sidebar">
    <div style="padding-bottom: 20px; border-bottom: 1px solid #1e293b;">
      <h3 style="font-size: 1.15rem; color:#fff; display:flex; align-items:center; gap:8px;">
        ⚙️ VAA ADMIN
      </h3>
      <p style="font-size: 0.78rem; color: #94a3b8; margin-top: 4px;">Quản trị viên: <?php echo e($currentUser['ho_ten']); ?></p>
    </div>

    <ul class="admin-menu">
      <li><a href="<?php echo BASE_URL; ?>/admin/index.php" class="<?php echo ($adminActive ?? '') === 'dashboard' ? 'active' : ''; ?>">📊 Tổng quan Dashboard</a></li>
      <li>
        <a href="<?php echo BASE_URL; ?>/admin/items.php" class="<?php echo ($adminActive ?? '') === 'items' ? 'active' : ''; ?>">
          <span>📦 Duyệt bài đăng</span>
          <?php if ($pendingCount > 0): ?>
            <span style="background:#ef4444; color:#fff; font-size:0.75rem; padding:2px 8px; border-radius:10px; font-weight:700;"><?php echo $pendingCount; ?></span>
          <?php endif; ?>
        </a>
      </li>
      <li><a href="<?php echo BASE_URL; ?>/admin/categories.php" class="<?php echo ($adminActive ?? '') === 'categories' ? 'active' : ''; ?>">📂 Quản lý Danh mục</a></li>
      <li><a href="<?php echo BASE_URL; ?>/admin/users.php" class="<?php echo ($adminActive ?? '') === 'users' ? 'active' : ''; ?>">👥 Quản lý Sinh viên</a></li>
      <li>
        <a href="<?php echo BASE_URL; ?>/admin/reports.php" class="<?php echo ($adminActive ?? '') === 'reports' ? 'active' : ''; ?>">
          <span>🚩 Xử lý Báo cáo</span>
          <?php if ($reportsCount > 0): ?>
            <span style="background:#f59e0b; color:#fff; font-size:0.75rem; padding:2px 8px; border-radius:10px; font-weight:700;"><?php echo $reportsCount; ?></span>
          <?php endif; ?>
        </a>
      </li>
      
      <li style="margin-top: 30px; border-top: 1px solid #1e293b; padding-top: 15px;">
        <a href="<?php echo BASE_URL; ?>/index.php" target="_blank" style="color: #38bdf8;">🌐 Xem trang Public &rarr;</a>
      </li>
      <li>
        <a href="<?php echo BASE_URL; ?>/logout.php" style="color: #f87171;">🚪 Đăng xuất</a>
      </li>
    </ul>
  </aside>

  <!-- KHU VỰC NỘI DUNG CHÍNH -->
  <main class="admin-content">
    <?php if (isset($_SESSION['flash_success'])): ?>
      <div class="alert alert-success">
        ✅ <?php echo e($_SESSION['flash_success']); unset($_SESSION['flash_success']); ?>
      </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['flash_error'])): ?>
      <div class="alert alert-error">
        ⚠️ <?php echo e($_SESSION['flash_error']); unset($_SESSION['flash_error']); ?>
      </div>
    <?php endif; ?>
