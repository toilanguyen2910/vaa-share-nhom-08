<?php
// ====================================================================
// HỌC VIỆN HÀNG KHÔNG VIỆT NAM - KHOA CÔNG NGHỆ THÔNG TIN
// MÔN: LẬP TRÌNH WEB - ĐỀ TÀI: VAA SHARE
// TÁC GIẢ PHỤ TRÁCH: VŨ HOÀNG KHÔI NGUYÊN (MSSV: 2531540127)
// TẬP TIN: logout.php - ĐĂNG XUẤT HỆ THỐNG
// ====================================================================

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

logout_user();
session_start();
$_SESSION['flash_success'] = 'Bạn đã đăng xuất an toàn khỏi hệ thống VAA Share.';
header('Location: ' . BASE_URL . '/index.php');
exit;
