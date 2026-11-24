<?php
// ====================================================================
// HỌC VIỆN HÀNG KHÔNG VIỆT NAM - KHOA CÔNG NGHỆ THÔNG TIN
// MÔN: LẬP TRÌNH WEB - ĐỀ TÀI: VAA SHARE
// TẬP TIN: includes/header.php - PHẦN ĐẦU TRANG DÙNG CHUNG
// ====================================================================

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/functions.php';

// CỔNG XÁC THỰC NỘI BỘ VAA:
// Hệ thống là mạng nội bộ khép kín (Intranet Portal) nhằm bảo vệ thông tin cá nhân sinh viên.
// Khách chưa đăng nhập bắt buộc phải qua cổng login.php (ngoại trừ trang giới thiệu about.php).
if (!is_logged_in() && !in_array($activePage ?? '', ['login', 'about'])) {
    header('Location: ' . BASE_URL . '/login.php');
    exit;
}

$currentUser = current_user();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($pageTitle) ? e($pageTitle) . ' | VAA Share' : 'VAA Share — Nền tảng trao đổi đồ dùng sinh viên VAA'; ?></title>
  <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/style.css">
</head>
<body>

<!-- THANH ĐIỀU HƯỚNG CHÍNH (NAVBAR) -->
<nav class="navbar">
  <div class="container navbar-container">
    <a href="<?php echo BASE_URL; ?>/index.php" class="nav-brand">
      <span class="nav-brand-logo">✈️ VAA SHARE</span>
      <span class="nav-brand-badge">VAA</span>
    </a>

    <ul class="nav-links">
      <li><a href="<?php echo BASE_URL; ?>/index.php" class="<?php echo ($activePage ?? '') === 'home' ? 'active' : ''; ?>">Trang chủ</a></li>
      <li><a href="<?php echo BASE_URL; ?>/listing.php" class="<?php echo ($activePage ?? '') === 'listing' ? 'active' : ''; ?>">Danh mục</a></li>
      <li><a href="<?php echo BASE_URL; ?>/about.php" class="<?php echo ($activePage ?? '') === 'about' ? 'active' : ''; ?>">Giới thiệu</a></li>
      <li><a href="<?php echo BASE_URL; ?>/post.php" class="btn btn-nav-post">➕ Đăng tin</a></li>

      <?php if ($currentUser): ?>
        <li><a href="<?php echo BASE_URL; ?>/my_items.php" class="<?php echo ($activePage ?? '') === 'my_items' ? 'active' : ''; ?>">📦 Đồ của tôi</a></li>
        <?php if ($currentUser['vai_tro'] === 'Admin'): ?>
          <li><a href="<?php echo BASE_URL; ?>/admin/index.php" class="nav-admin-link">⚙️ Quản trị</a></li>
        <?php endif; ?>
        <li class="nav-user-item">
          <div class="user-chip">
            <span class="user-chip-avatar">👤</span>
            <span class="user-chip-name" title="<?php echo e($currentUser['ho_ten']); ?>"><?php echo e($currentUser['ho_ten']); ?></span>
            <a href="<?php echo BASE_URL; ?>/logout.php" class="btn-logout" title="Đăng xuất">[Thoát]</a>
          </div>
        </li>
      <?php else: ?>
        <li><a href="<?php echo BASE_URL; ?>/login.php" class="btn btn-outline" style="color:#fff; border-color:rgba(255,255,255,0.3);">Đăng nhập</a></li>
      <?php endif; ?>
    </ul>
  </div>
</nav>

<!-- HIỂN THỊ THÔNG BÁO FLASH NẾU CÓ -->
<div class="container" style="margin-top: 15px;">
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
</div>
